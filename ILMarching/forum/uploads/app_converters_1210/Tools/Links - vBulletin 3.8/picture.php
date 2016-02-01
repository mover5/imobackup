<?php
/**
 * IPS Converters
 * vBulletin Links
 * forumdisplay.php
 * Last Update: $Date: 2013-05-30 16:09:39 -0400 (Thu, 30 May 2013) $
 * Last Updated By: $Author: rashbrook $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 871 $
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
	$settings =& $registry->fetchSettings();
	
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	
	if (!is_numeric(ipsRegistry::$request['pictureid']) )
	{
		exit;
	}
	
	//-----------------------------------------
	// Do we have a valid app?
	//-----------------------------------------
	
	$app = $DB->buildAndFetch( array( 'select' => 'app_id, parent', 'from' => 'conv_apps', 'where' => "name='".CONV_GALLERY_ID."'" ) );
	
	if (!$app['app_id'])
	{
		echo 'Invalid Conversion ID';
		exit;
	}
	
	//-----------------------------------------
	// Get our link and boink
	//-----------------------------------------
	
	$link = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id='".ipsRegistry::$request['pictureid']."' AND type='gallery_images' AND app='".$app['app_id']."'" ) );
		
	if (!$link)
	{
		die('a');
		exit;
	}
	
	$row = $DB->buildAndFetch( array( 'select' => '*', 'from' => 'gallery_images', 'where' => "image_id='".$link['ipb_id']."'" ) );
	if(!$row)
	{
		die('c');
		exit;
	}
	
	$path = ($row['directory']) ? $settings['gallery_images_path'].'/'.$row['directory'].'/'.$row['masked_file_name'] : $settings['gallery_images_path'].'/'.$row['masked_file_name'];
	
	if (!file_exists($path))
	{
		die('b');
		exit;
	}
	
	print file_get_contents($path);
	