<?php
/**
 * IPS Converters
 * punBB Links
 * viewtopic.php
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
	// Load our libraries
	//-----------------------------------------
	
	require_once 'config_punbb_conv.php';
	
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
	
	if ( !is_numeric(ipsRegistry::$request['id']) )
	{
		if ( !is_numeric(ipsRegistry::$request['pid']) )
		{
			// Boink to index
			$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
		}
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
	
	if ( is_numeric(ipsRegistry::$request['pid']) && ipsRegistry::$request['pid'] > 0 )
	{
		/* Got a PID, check for post */
		$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_posts', 'where' => "foreign_id=".intval(ipsRegistry::$request['pid'])." AND type='posts' AND app='".$app['app_id']."'" ) );
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?findpost='.$row['ipb_id'], '', TRUE);
	}
	elseif ( is_numeric(ipsRegistry::$request['id']) && ipsRegistry::$request['id'] > 0 )
	{
		$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_topics', 'where' => "foreign_id=".intval(ipsRegistry::$request['id'])." AND type='topics' AND app='".$app['app_id']."'" ) );
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$row['ipb_id'], '', TRUE);
	}
	else
	{
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
	}
	