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
	
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	
	if (!is_numeric(ipsRegistry::$request['albumid']) and !is_numeric(ipsRegistry::$request['u']) )
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
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
	
	if(ipsRegistry::$request['albumid'])
	{	
		if(is_numeric(ipsRegistry::$request['pictureid']))
		{
			$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id='".ipsRegistry::$request['pictureid']."' AND type='gallery_images' AND app='".$app['app_id']."'" ) );
			$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php??app=gallery&module=images&section=viewimage&img='.$row['ipb_id'], '', TRUE);
		}
		else
		{
			$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id='".ipsRegistry::$request['albumid']."' AND type='gallery_albums' AND app='".$app['app_id']."'" ) );
			$album = $DB->buildAndFetch( array( 'select' => 'album_owner_id', 'from' => 'gallery_albums', 'where' => 'album_id='.$row['ipb_id'] ) );
		
			$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?app=gallery&module=user&user='.$album['member_id'].'&op=view_album&album='.$row['ipb_id'], '', TRUE);
		}
	}
	else
	{
		$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id='".ipsRegistry::$request['u']."' AND type='members' AND app='".$app['parent']."'" ) );
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?app=gallery&module=user&user='.$row['ipb_id'], '', TRUE);
	}
	