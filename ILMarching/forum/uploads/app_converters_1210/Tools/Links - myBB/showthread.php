<?php
/**
 * IPS Converters
 * myBB Links
 * showthread.php
 * Last Update: $Date: 2013-07-22 09:25:01 -0400 (Mon, 22 Jul 2013) $
 * Last Updated By: $Author: AndyMillne $
 *
 * @package		IPS Converters
 * @author 		Terabyte
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 891 $
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
	
	if (!is_numeric(ipsRegistry::$request['tid']))
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
		
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_topics', 'where' => "foreign_id=".intval(ipsRegistry::$request['tid'])." AND type='topics' AND app='".$app['app_id']."'" ) );
	
	if (intval(ipsRegistry::$request['page']))
	{
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$row['ipb_id'].'&page='.intval(ipsRegistry::$request['page']), '', TRUE);
	}
	else
	{
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$row['ipb_id'], '', TRUE);
	}