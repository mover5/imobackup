<?php
	
	//-----------------------------------------
	// Load our libraries
	//-----------------------------------------
	
	require_once 'config.php';
	
	if (!file_exists(IPB_PATH.'/initdata.php'))
	{
		echo 'Invalid IPB path';
		exit;
	}
	
	require_once( IPB_PATH.'/initdata.php' );
	require_once( IPB_PATH.'/'.CP_DIRECTORY.'/sources/base/ipsRegistry.php' );
	require_once( IPB_PATH.'/'.CP_DIRECTORY.'/sources/base/ipsController.php' );
	$registry = ipsRegistry::instance();
	$registry->init();
	
	$qstring = '';
	foreach ( $_POST as $k => $v )
	{
		$v = urlencode( $v );
		$qstring .= "&{$k}={$v}";
	}

	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?app=nexus&module=payments&section=receive&do=validate&validate=paypal' . $qstring, '', TRUE);