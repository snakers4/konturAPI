<?php
/*
if (is_file("konturIncludeList.php")) 
{
	require_once "konturIncludeList.php";
} 
else {exit('konturIncludeList.php not connected');}

//Usage example 

echo '<pre>';
$getAllCompanies = new companiesGetKontur;
$companies = $getAllCompanies -> getRelevantCompanies();

echo $companies;

$companies = json_decode ($companies, true);



$params =
[
'key' => $api_key,
'ogrn' => '',
'inn' =>  '7705967569', //$companies[0]['inn'],
];

$konturApiCall = new KonturApiReq;
($konturApiCall -> apiCall($params));
($konturApiCall -> apiCallTransform());
($konturApiCall -> apiLog(['company_id'=>$companies[0]['company_id']]));
*/ 
/*
class classname
{
	function __construct()
	{
		echo __METHOD__,"\n";
	}
}
*/
function pgSingleQuotes ($params) {
	if ($params) {
		if ($params ===  'NULL') {
			return 'NULL';
		} else {
			return "'" . $params . "'";
		}
	} else {
		return 'NULL';
	}
}
class KonturApiReq{
	/**
	https://focus-api.kontur.ru/api3/req/userform?key=10aeb24bed75638637585788355bfed02d3d6b10&q=&region=&page=&ogrn=&inn=6732059797&
	*/
	private $_apiUrl = 'https://focus-api.kontur.ru/api3/req';
	private $_apiParams = 
		[
			'key' => '?key=',
			'ogrn' => '&ogrn=',
			'inn' => '&inn=',		
		];
	private $_apiReply;
	private $_apiReplyDecoded;
	private $_apiCallUrl;
	
	function apiCallTransform () {
		if(isset($this->_apiReply[0]['ogrn'])) {
			$this -> _apiReplyDecoded ['ogrn(ip)'] = $this->_apiReply[0]['ogrn'];
		}  else {
			$this -> _apiReplyDecoded ['ogrn(ip)'] = 'NULL';	
		}
		if(isset($this->_apiReply[0]['UL']['kpp'])) {
			$this -> _apiReplyDecoded ['kpp'] = $this->_apiReply[0]['UL']['kpp'];
		}  else {
			$this -> _apiReplyDecoded ['kpp'] = 'NULL';	
		}	
		if(isset($this->_apiReply[0]['UL']['legalName']['short'])) {
			$this -> _apiReplyDecoded ['ul_legalname_short'] = $this->_apiReply[0]['UL']['legalName']['short'];
		}  else {
			if(isset($this->_apiReply[0]['IP']['fio'])) {
				$this -> _apiReplyDecoded ['ul_legalname_short'] = $this->_apiReply[0]['IP']['fio'];
			} else {
				$this -> _apiReplyDecoded ['ul_legalname_short'] = 'NULL';	
			}		
		}
		if(isset($this->_apiReply[0]['UL']['legalName']['full'])) {
			$this -> _apiReplyDecoded ['ul_legalname_long'] = $this->_apiReply[0]['UL']['legalName']['full'];
		}  else {
			if(isset($this->_apiReply[0]['IP']['fio'])) {
				$this -> _apiReplyDecoded ['ul_legalname_long'] = $this->_apiReply[0]['IP']['fio'];
			} else {
				$this -> _apiReplyDecoded ['ul_legalname_long'] = 'NULL';	
			}
		}
		if(isset($this->_apiReply[0]['UL']['status']['statusString'])) {
			$this -> _apiReplyDecoded ['ul_status_statusstring'] = $this->_apiReply[0]['UL']['status']['statusString'];
		}  else {
			if(isset($this->_apiReply[0]['IP']['status']['statusString'])) {
				$this -> _apiReplyDecoded ['ul_status_statusstring'] = $this->_apiReply[0]['IP']['status']['statusString'];
			} else {
				$this -> _apiReplyDecoded ['ul_status_statusstring'] = 'NULL';	
			}	
		}
		if(isset($this->_apiReply[0]['briefReport']['summary']['greenStatements'])) {
			$this -> _apiReplyDecoded ['green_statements'] = $this->_apiReply[0]['briefReport']['summary']['greenStatements'];
		}  else {
			$this -> _apiReplyDecoded ['green_statements'] = 'NULL';	
		}
		if(isset($this->_apiReply[0]['briefReport']['summary']['yellowStatements'])) {
			$this -> _apiReplyDecoded ['yellow_statements'] = $this->_apiReply[0]['briefReport']['summary']['yellowStatements'];
		}  else {
			$this -> _apiReplyDecoded ['yellow_statements'] = 'NULL';	
		}
		if(isset($this->_apiReply[0]['briefReport']['summary']['redStatements'])) {
			$this -> _apiReplyDecoded ['red_statements'] = $this->_apiReply[0]['briefReport']['summary']['redStatements'];
		}  else {
			$this -> _apiReplyDecoded ['red_statements'] = 'NULL';	
		}
		$this -> _apiReplyDecoded ['ul_legaladdress'] = '';
		if(isset($this->_apiReply[0]['UL']['legalAddress']['parsedAddressRF'])) {
			$addressArray = ($this->_apiReply[0]['UL']['legalAddress']['parsedAddressRF']);
			foreach ($addressArray as $key => $value){
				if (is_array($value)) {
					if(isset($value['topoFullName'])){$a = $value['topoFullName'] . ' ';}else{$a = ' ';}
					$this -> _apiReplyDecoded ['ul_legaladdress'] = $this -> _apiReplyDecoded ['ul_legaladdress'] . $a . $value['topoValue'] . ' ';
				}
			}
		}  else {
			$this -> _apiReplyDecoded ['red_statements'] = 'NULL';	
		}
	return $this -> _apiReplyDecoded;
	}	
	
	function apiLog ($params) {
		/**
		$params =
		[
		 'company_id' => 666,
		];
		*/
		if (is_file('/home/aveysov/htdocs/common/db_credentials_sandbox_rw.php')){
			include '/home/aveysov/htdocs/common/db_credentials_sandbox_rw.php';
		} 
		else {
			exit("No db_credentials_sandbox_rw.php credentials available");
		}

		if(isset($params['inn'])) {
			$inn = $params['inn'];
		}  else {
			$inn = 'NULL';	
		}		
		
		$query = 
		"
		INSERT 
		INTO aveysov.kontur_api_log
		(
			\"query_string\",
			\"company_id\",
			\"ul_legalname_short\",
			\"ul_legalname_long\",
			\"ul_legaladdress\",
			\"kpp\",
			\"ul_status_statusstring\",
			\"green_statements\",
			\"yellow_statements\",
			\"red_statements\",
			\"query_date_time\",
			\"ogrn(ip)\",
			\"inn\"		
		)
		SELECT
			".pgSingleQuotes($this -> _apiCallUrl).",
			".pgSingleQuotes($params['company_id']).",
			".pgSingleQuotes($this -> _apiReplyDecoded['ul_legalname_short']).",
			".pgSingleQuotes($this -> _apiReplyDecoded['ul_legalname_long']).",			
			".pgSingleQuotes($this -> _apiReplyDecoded['ul_legaladdress']).",					
			".pgSingleQuotes($this -> _apiReplyDecoded['kpp']).",			
			".pgSingleQuotes($this -> _apiReplyDecoded['ul_status_statusstring']).",	
			".pgSingleQuotes($this -> _apiReplyDecoded['green_statements']).",	
			".pgSingleQuotes($this -> _apiReplyDecoded['yellow_statements']).",				
			".pgSingleQuotes($this -> _apiReplyDecoded['red_statements']).",
			now(),
			".pgSingleQuotes(($this -> _apiReplyDecoded['ogrn(ip)'])).",
			".pgSingleQuotes($inn)."
		RETURNING
			aveysov.kontur_api_log.*
		";
		
		$dbconn = pg_connect("host=$host port=5432 dbname=$db user=$user password=$pass") or die("Could not connect"."</br>");	
		$result = pg_query($dbconn, $query);
		if (!$result){
		  echo "A DB insert error occurred.\n";
		  pg_close($dbconn);
		  exit;
		}
		$resultData = '';
		while ($row = pg_fetch_row($result)){
		  $resultData = $resultData . $row[0];
		}
		if (($this -> _apiReplyDecoded['yellow_statements'])=== true) {
			$alarmQuery =
			"
			INSERT 
			INTO aveysov.kontur_alarm
			(
				\"alarm_type\",
				\"alarm_datetime\",
				\"company_id\"
			)
			SELECT
				3,
				now(),
				".pgSingleQuotes($params['company_id'])."
			RETURNING
				aveysov.kontur_alarm.*			
			";
			
			$result = pg_query($dbconn, $alarmQuery);
			if (!$result) 
			{
			  echo "A DB insert error occurred.\n";
			  pg_close($dbconn);
			  exit;
			}
		}
		
		if (($this -> _apiReplyDecoded['red_statements'])=== true) {
			$alarmQuery =
			"
			INSERT 
			INTO aveysov.kontur_alarm
			(
				\"alarm_type\",
				\"alarm_datetime\",
				\"company_id\"
			)
			SELECT
				2,
				now(),
				".pgSingleQuotes($params['company_id'])."
			RETURNING
				aveysov.kontur_alarm.*			
			";
			
			$result = pg_query($dbconn, $alarmQuery);
			if (!$result) 
			{
			  echo "A DB insert error occurred.\n";
			  pg_close($dbconn);
			  exit;
			}
		}
		
		if (($this -> _apiReplyDecoded['ul_status_statusstring'])!='Действующее') {
			$alarmQuery =
			"
			INSERT 
			INTO aveysov.kontur_alarm
			(
				\"alarm_type\",
				\"alarm_datetime\",
				\"company_id\"
			)
			SELECT
				1,
				now(),
				".pgSingleQuotes($params['company_id'])."
			RETURNING
				aveysov.kontur_alarm.*			
			";
			
			$result = pg_query($dbconn, $alarmQuery);
			if (!$result) 
			{
			  echo "A DB insert error occurred.\n";
			  pg_close($dbconn);
			  exit;
			}
		}

		pg_close($dbconn);
		if ($resultData) {
			return $resultData;		
		}	

	}
	
	function apiCall ($params) {
		$apiCallString = $this->_apiUrl . $this->_apiParams['key'] . $params['key'] . $this->_apiParams['ogrn'] . $params['ogrn'] . $this->	_apiParams['inn']  . $params['inn'];
		$this -> _apiCallUrl = $apiCallString;
		$curl = curl_init($apiCallString);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curl_response = curl_exec($curl);
		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occurred during curl exec. Additional info: ' . var_export($info));
		}
		curl_close($curl);
		try {
			$this -> _apiReply = json_decode($curl_response, true);
			return $this -> _apiReply;
		} catch (Exception $e) {
			echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
		}
	}	
}

class companiesGetKontur{
	function getAllCompanies () {
		if (is_file('/home/aveysov/htdocs/common/db_credentials_ro.php')){
			include '/home/aveysov/htdocs/common/db_credentials_ro.php';
		} 
		else {
			exit("No database credentials available");
		}

		$query =
		"
		SELECT 
		to_json(\"array_agg\"(a))
		FROM
		(		
			SELECT DISTINCT
				com.\"id\" as company_id,
				com.inn as inn,
				com.official_name,
				com.report_name
			FROM
				cache_gateway_event cge
				JOIN hamster h ON cge.hamster_id = h.id
				JOIN companies com ON h.company_id = com.id
			WHERE
				com.inn NOTNULL AND
				char_length(com.inn) > 8
		) a
		";
		$dbconn = pg_connect("host=$host port=5432 dbname=$db user=$user password=$pass") or die("Could not connect"."</br>");
		$result = pg_query($dbconn, $query);
		$resultData = '';

		if (!$result) 
		{
		  echo "An error occurred.\n";
		  pg_close($dbconn);
		  exit;
		}
		while ($row = pg_fetch_row($result)) 
		{
		  $resultData = $resultData . $row[0];
		}
		pg_close($dbconn);
		return $resultData;
	}
	
	function getRelevantCompanies () {
		
		/*
		$params = 
		[
			'monthsBack' => 12,
		]

		*/

		if (is_file('/home/aveysov/htdocs/common/db_credentials_ro.php')){
			include '/home/aveysov/htdocs/common/db_credentials_ro.php';
		} 
		else {
			exit("No database credentials available");
		}
		
		$query =
		"
		SELECT 
		to_json(\"array_agg\"(a))
		FROM
		(			
			SELECT DISTINCT
				com.\"id\" as company_id,
				com.inn as inn,
				com.official_name as company_name,
				com.report_name as company_short_name
			FROM
				cache_gateway_event cge
				JOIN hamster h ON cge.hamster_id = h.id
				JOIN companies com ON h.company_id = com.id
			WHERE
				cge.creation_date > now() - interval '12 months' AND
				com.inn NOTNULL AND
				char_length(com.inn) > 8
				
			UNION

			SELECT DISTINCT
				com.\"id\" company_id,
				com.inn as companyinn,
				com.official_name as company_name,
				com.report_name as company_short_name
			FROM
				cache_gateway_event cge
				JOIN hamster h ON cge.hamster_id = h.id
				JOIN companies com ON h.company_id = com.id
			WHERE
				cge.event_date > now() AND
				com.inn NOTNULL AND
				char_length(com.inn) > 8				
	
			ORDER BY
				company_id DESC
		) a
		";
		$dbconn = pg_connect("host=$host port=5432 dbname=$db user=$user password=$pass") or die("Could not connect"."</br>");
		$result = pg_query($dbconn, $query);
		$resultData = '';

		if (!$result){
		  echo "An error occurred.\n";
		  pg_close($dbconn);
		  exit;
		}
		while ($row = pg_fetch_row($result)){
		  $resultData = $resultData . $row[0];
		}
		pg_close($dbconn);
		return $resultData;
	}	
	
	function getRelevantUncheckedCompanies () {
		if (is_file('/home/aveysov/htdocs/common/db_credentials_sandbox_rw.php')){
			include '/home/aveysov/htdocs/common/db_credentials_sandbox_rw.php';
		} 
		else {
			exit("No db_credentials_sandbox_rw.php credentials available");
		}
		if (is_file('/home/aveysov/htdocs/common/reports_db_link_ro.php')){
			include '/home/aveysov/htdocs/common/reports_db_link_ro.php';
		} 
		else {
			exit("No reports_db_link_ro.php credentials available");
		}
		$query =
		"
		SELECT 
		to_json(\"array_agg\"(a))
		FROM
		(			
			SELECT
				raw_data.company_id,
				raw_data.inn
			FROM
			(
				SELECT DISTINCT
					coms.company_id,
					coms.inn,
					klog.company_id as klog_company_id,
					MAX(klog.query_date_time) as max_time
				FROM
					dblink(
					'".$dblink."',
					'
						SELECT DISTINCT
							com.\"id\" as company_id,
							com.inn as inn
						FROM
							cache_gateway_event cge
							JOIN hamster h ON cge.hamster_id = h.id
							JOIN companies com ON h.company_id = com.id
						WHERE
							cge.creation_date > now() - interval ''12 months'' AND com.company_type = 0 AND
							com.inn NOTNULL AND
							char_length(com.inn) > 8
						UNION
						SELECT DISTINCT
							com.\"id\" company_id,
							com.inn as companyinn
						FROM
							cache_gateway_event cge
							JOIN hamster h ON cge.hamster_id = h.id
							JOIN companies com ON h.company_id = com.id
						WHERE
							cge.event_date > now() AND com.company_type = 0 AND 
							com.inn NOTNULL AND
							char_length(com.inn) > 8				
						ORDER BY
							company_id DESC
					'
					) AS coms (company_id INT, inn VARCHAR)
				LEFT JOIN aveysov.kontur_api_log klog ON klog.company_id = coms.company_id
				GROUP BY
					coms.company_id,
					coms.inn,
					klog.company_id
			) raw_data
			WHERE
				raw_data.klog_company_id ISNULL OR raw_data.max_time < now() - interval '1 month'
			LIMIT 50
		) a
		";
		$dbconn = pg_connect("host=$host port=5432 dbname=$db user=$user password=$pass") or die("Could not connect"."</br>");
		$result = pg_query($dbconn, $query);
		$resultData = '';

		if (!$result){
		  echo "An error occurred.\n";
		  pg_close($dbconn);
		  exit;
		}
		while ($row = pg_fetch_row($result)){
		  $resultData = $resultData . $row[0];
		}
		pg_close($dbconn);
		return $resultData;
	}		
	
	function getBalanceUncheckedCompanies () {
		/*
		Метод неактуальный и не рефакторился. Для работы нужно дописать dblink.
		*/
		if (is_file('../common/db_credentials_ro.php')){
			require ('../common/db_credentials_ro.php');
		} 
		else {
			exit("No ../common/db_credentials_ro.php credentials available");
		}

		/*
			тут нужен дблинк
		*/

		$query =
		"
		SELECT 
		to_json(\"array_agg\"(a))
		FROM
		(			
			SELECT
				kba.inn as inn
			FROM
				aveysov.kontur_balance_check kba
				LEFT JOIN
					(
						SELECT
							klog.\"id\",
							COALESCE (com.inn, klog.inn) as inn
						FROM
							aveysov.kontur_api_log klog 
							LEFT JOIN companies com ON klog.company_id = com.\"id\"
					) sub ON sub.inn = kba.inn
			WHERE 1=1
				AND sub.\"id\" ISNULL
			GROUP BY
				kba.inn,
				kba.balance
			ORDER BY
				kba.balance DESC
			LIMIT 20
		) a
		";
		$dbconn = pg_connect("host=$host port=5432 dbname=$db user=$user password=$pass") or die("Could not connect"."</br>");
		$result = pg_query($dbconn, $query);
		$resultData = '';

		if (!$result) 
		{
		  echo "An error occurred.\n";
		  pg_close($dbconn);
		  exit;
		}
		while ($row = pg_fetch_row($result)) 
		{
		  $resultData = $resultData . $row[0];
		}
		pg_close($dbconn);
		return $resultData;
	}

	
}

?>
