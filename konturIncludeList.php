<?php
	/**
	 * Connecting other files
	 */
	if (is_file("../common/kontur_test_credentials.php")) 
	{
		require_once "../common/kontur_test_credentials.php";
	} 
	else {exit('../common/kontur_test_credentials.php not connected');}
	
?>