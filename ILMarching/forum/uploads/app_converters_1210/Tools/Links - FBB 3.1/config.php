<?php

	// THE URL TO YOUR IPB FORUMS (no trailing slash or index.php)
	define('IPB_URL', 'http://localhost:8888/ipb3');

	// THE PATH TO YOUR IPB FORUMS (no trailing slash)
	define('IPB_PATH', '/Users/Mark/Sites/ipb3');

	// THE CONVERSION ID
	// This would have been asked for when setting up the converters
	define('CONV_ID', 'fbb');
	
	// GATEWAY - DO NOT EDIT
	// This stops a 'same domain' check which stops redirect scripts working in certain circumstances.
	define( 'CCS_GATEWAY_CALLED', true );

$fbbglobals = array();

if(empty($_SERVER['QUERY_STRING'])){
	$_SERVER['QUERY_STRING'] = '';
}

if(empty($_SERVER['QUERY_STRING'])){
	$path = '';
	$path_info = $_SERVER['PATH_INFO'];
	$path_info = explode('.php',$path_info);
	if(is_array($path_info)){
		if(sizeof($path_info) > 1){
			$path = $path_info[1];
		}
		else{
			$path = $path_info[0];
		}
		if($path{0} == '/'){
			$path = substr($path,1);
		}
		$_SERVER['QUERY_STRING'] = $path;
	}
}

$qstring = explode("/",$_SERVER['QUERY_STRING']);

// +----------------------------------------------------------------
// Bump up array size by one just in case a key doesn't have a value
$qstring[] = "";
$sizeofqs = sizeof($qstring);
for($i=0;$i<$sizeofqs;$i++) {
	$fbbglobals[$qstring[$i]] = @$qstring[$i+1];
	$i++;
}