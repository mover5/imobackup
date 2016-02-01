<?php
/**
 * IPS Converters
 * SMF Links
 * Last Update: $Date: 2010-02-15 10:44:06 +0100(lun, 15 feb 2010) $
 * Last Updated By: $Author: terabyte $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 423 $
 */
	
	//-----------------------------------------
	// Configuration
	//-----------------------------------------
	
	// THE URL TO YOUR IPB FORUMS (no trailing slash or index.php)
	define('IPB_URL', 'http://localhost/ipb3');
	
	// THE PATH TO YOUR IPB FORUMS (no trailing slash)
	define('IPB_PATH', '/home/Users/Mark/Sites/ipb3');
	
	// THE CONVERSION ID
	// This would have been asked for when setting up the converters
	define('CONV_ID', 'old_gallery');
	
	//-----------------------------------------
	// Load our libraries
	//-----------------------------------------
	if (!file_exists(IPB_PATH.'/initdata.php'))
	{
		echo 'Invalid IPB path';
		exit;
	}
	
	//-----------------------------------------
	// Who am I and where are you!?
	//-----------------------------------------
	$ident = '';
	$convType = '';
	$url = '';
	
	if ($_REQUEST['n'])
	{
		$ident = 'n';
		$convType = 'gallery_images';
		$url = 'app=gallery&module=images&section=viewimage&img';
	}
	elseif ($_REQUEST['c'])
	{
		$ident = 'c';
		$convType = 'gallery_categories';
		$url = 'app=gallery&module=cats&do=sc&cat';
	}
	elseif ($_REQUEST['u'])
	{
		$ident = 'u';
		$convType = 'members';
		$url = 'app=gallery&module=user&user';
	}
	else
	{
		define( 'IPB_THIS_SCRIPT', 'public' );
		require_once( IPB_PATH.'/initdata.php' );

		require_once( IPS_ROOT_PATH . 'sources/base/ipsRegistry.php' );
		require_once( IPS_ROOT_PATH . 'sources/base/ipsController.php' );

		ipsController::run();

		exit();
	}
	define('IPS_ENFORCE_ACCESS', true);
	
	require_once( IPB_PATH.'/initdata.php' );
	require_once( IPB_PATH.'/'.CP_DIRECTORY.'/sources/base/ipsRegistry.php' );
	require_once( IPB_PATH.'/'.CP_DIRECTORY.'/sources/base/ipsController.php' );
	$registry = ipsRegistry::instance();
	$registry->init();
	$DB       = $registry->DB();
	
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
	
	$app = $DB->buildAndFetch( array( 'select' => 'app_id, parent', 'from' => 'conv_apps', 'where' => "name='".CONV_ID."'" ) );
	
	if (!$app['app_id'])
	{
		echo 'Invalid Conversion ID';
		exit;
	}
	
	if ( $convType == 'members' && $app['parent'] > 0 )
	{
		$app['app_id'] = $app['parent'];	
	}
		
	//-----------------------------------------
	// Get our link and boink
	//-----------------------------------------
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id='".intval(ipsRegistry::$request[$ident])."' AND type='".$convType."' AND app='".$app['app_id']."'" ) );
	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?'.$url.'='.$row['ipb_id'], '', TRUE);
