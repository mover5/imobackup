<?php
/**
 * IPS Converters
 * vBulletin Links
 * forumdisplay.php
 * Last Update: $Date: 2013-01-25 20:50:52 -0500 (Fri, 25 Jan 2013) $
 * Last Updated By: $Author: MikeyB $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 770 $
 */
	
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
	$DB       = $registry->DB();
	
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	
	if (!is_numeric(ipsRegistry::$request['u']))
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
	}
	
	//-----------------------------------------
	// Do we have a valid app?
	//-----------------------------------------
	
	$app = $DB->buildAndFetch( array( 'select' => 'app_id, app_merge', 'from' => 'conv_apps', 'where' => "name='".CONV_ID."'" ) );
	
	if (!$app['app_id'])
	{
		echo 'Invalid Conversion ID';
		exit;
	}

	if ( $app['app_merge'] == '0' )
	{
	
		if ( isset( ipsRegistry::$request['u'] ) )
		{
			// Boink to user
			$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showuser='.ipsRegistry::$request['u'], '', TRUE);
			exit;
		}
	
	}	
	
	//-----------------------------------------
	// Get our link and boink
	//-----------------------------------------
	
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id='".ipsRegistry::$request['u']."' AND type='members' AND app='".$app['app_id']."'" ) );
	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showuser='.$row['ipb_id'], '', TRUE);