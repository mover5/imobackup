<?php
/**
 * IPS Converters
 * UBB Links
 * Last Update: $Date: 2011-11-18 13:13:29 -0500 (Fri, 18 Nov 2011) $
 * Last Updated By: $Author: AlexHobbs $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 602 $
 */
	
	//-----------------------------------------
	// Configuration
	//-----------------------------------------
	
	// THE URL TO YOUR IPB FORUMS (no trailing slash or index.php)
	define('IPB_URL', 'http://localhost/ipb3');
	
	// THE PATH TO YOUR IPB FORUMS (no trailing slash)
	define('IPB_PATH', '/Users/Mark/Sites/ipb3');
	
	// THE CONVERSION ID
	// This would have been asked for when setting up the converters
	define('CONV_ID', 'ubb');
	
	// THE SUBFOLDER THAT UBBTHREADS IS INSTALLED IN, LEAVE BLANK IF IN ROOT
	define('SUB_FOLDER', '');
	
	/* NO MORE CONFIGURATION BELOW THIS LINE */
	
	//-----------------------------------------
	// Load our libraries
	//-----------------------------------------
	
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
	$DB       = $registry->DB();
	
	// Query URL
	$qs = explode('/', str_replace( SUB_FOLDER . '/ubbthreads.php/', '', $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_SERVER['REQUEST_URI'] ) );
	
	switch( $qs[0] )
	{
		case 'topics':
			$_REQUEST['ubb'] = 'showthreaded';
			ipsRegistry::$request['Number'] = intval( $qs[1] );
			break;
	}
	
	//-----------------------------------------
	// Who am I and where are you!?
	//-----------------------------------------

	$ident = '';
	$convType = '';
	$url = '';
	$table = '';
	
	if ($_REQUEST['ubb'] == 'postlist')
	{
		$ident = 'Board';
		$convType = 'forums';
		$url = 'showforum';
	}
	elseif ($_REQUEST['ubb'] = 'showflat' or $_REQUEST['ubb'] = 'showthreaded')
	{
		$ident = 'Number';
		$convType = 'posts';
		$url = 'findpost';
		$table = '_posts';
	}
	elseif ($_REQUEST['ubb'] = 'showprofile')
	{
		$ident = 'User';
		$convType = 'members';
		$url = 'showuser';
	}
	else
	{
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
		exit;
	}
	
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	
	if (!is_numeric(ipsRegistry::$request[$ident]))
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
	}
	
	//-----------------------------------------
	// Do we have a valid app?
	//-----------------------------------------
	
	$app = $DB->buildAndFetch( array( 'select' => 'app_id', 'from' => 'conv_apps', 'where' => "name='".CONV_ID."'" ) );

	if (!$app['app_id'])
	{
		echo 'Invalid Conversion ID';
		exit;
	}
		
	//-----------------------------------------
	// Get our link and boink
	//-----------------------------------------
		
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link'.$table, 'where' => "foreign_id='".intval(ipsRegistry::$request[$ident])."' AND type='".$convType."' AND app='".$app['app_id']."'" ) );
	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?'.$url.'='.$row['ipb_id'], '', TRUE);
