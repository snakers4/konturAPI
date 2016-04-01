<?php
//echo KonturCheckPhpFunction();
checkKonturDataMethods () ;

function KonturCheckPhpFunction() {
	echo '<pre>';
	if (is_file('../konturAPI/KonturApiClass.php')) {
		require'../konturAPI/KonturApiClass.php' ;
	} 
	else {
		exit('../konturAPI/KonturApiClass.php not connected');
	}

	if (is_file("../konturAPI/konturIncludeList.php")){
		require "../konturAPI/konturIncludeList.php";
	} 
	else {
		exit('../konturAPI/konturIncludeList.php not connected');
	}

	$methodParams =
	[
	'key' => $api_key,
	'ogrn' => '',
	'inn' =>  772072420033,
	];
	var_dump($methodParams);
	
	$konturApiCall = new KonturApiReq;
	$konturApiCall -> apiCall($methodParams);
	$result = ($konturApiCall -> apiCallTransform());
	var_dump($result);
	$konturApiCall -> apiLog(['company_id'=>301]);
	unset ($konturApiCall);
	return json_encode($result);
	unset ($konturCompanies);
}	


function checkKonturDataMethods() {
	if (is_file('../konturAPI/KonturApiClass.php')) {
		require'../konturAPI/KonturApiClass.php' ;
	} 
	else {
		exit('../konturAPI/KonturApiClass.php not connected');
	}

	if (is_file("../konturAPI/konturIncludeList.php")){
		require "../konturAPI/konturIncludeList.php";
	} 
	else {
		exit('../konturAPI/konturIncludeList.php not connected');
	}


	$kontur = new companiesGetKontur;
	var_dump (json_decode(($kontur -> getRelevantUncheckedCompanies()), true));
}


?>