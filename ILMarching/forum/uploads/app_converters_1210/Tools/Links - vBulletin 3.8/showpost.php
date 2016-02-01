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
	$DB			= $registry->DB();
	$settings	= &$registry->fetchSettings();
	
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	
	if (!is_numeric(ipsRegistry::$request['p']))
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
	
		if ( isset( ipsRegistry::$request['p'] ) )
		{
			// Boink to post
			$topic = $DB->buildAndFetch( array( 'select' => 'topic_id', 'from' => 'posts', 'where' => "pid='".ipsRegistry::$request['p']."'" ) );
			$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$topic['topic_id'].'&view=findpost&p='.ipsRegistry::$request['p'], '', TRUE);
			exit;
		}
	
	}
	
	//-----------------------------------------
	// Get our link and boink
	//-----------------------------------------
	
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_posts', 'where' => "foreign_id=".intval(ipsRegistry::$request['p'])." AND type='posts' AND app='".$app['app_id']."'" ) );
	$topic = $DB->buildAndFetch( array( 'select' => 'topic_id', 'from' => 'posts', 'where' => "pid='".$row['ipb_id']."'" ) );
	
	if ( ! $topic )
	{
		// Are we archived?
		if ( ! empty( $settings['archive_remote_sql_database'] ) && ! empty( $settings['archive_remote_sql_user'] ) )
		{
			// Are we... remote? *shudder*
			$registry->dbFunctions()->setDB( 'mysql', 'remoteArchive', array(
				'sql_database'	        => $settings['archive_remote_sql_database'],
				'sql_user'		        => $settings['archive_remote_sql_user'],
				'sql_pass'		        => $settings['archive_remote_sql_pass'],
				'sql_host'		        => $settings['archive_remote_sql_host'],
				'sql_charset'	        => $settings['archive_remote_sql_charset'],
				'sql_tbl_prefix'		=> $settings['sql_tbl_prefix'],
			) );
			$RDB	= $registry->dbFunctions()->getDB( 'remoteArchive' );
			$topic	= $RDB->buildAndFetch( array ( 'select' => 'archive_topic_id AS topic_id', 'from' => 'forums_archive_posts', 'where' => "pid='".$row['ipb_id']."'" ) );
		}
		else
		{
			// Oh good, we're local.
			$topic = $DB->buildAndFetch( array( 'select' => 'archive_topic_id AS topic_id', 'from' => 'forums_archive_posts', 'where' => "pid='".$row['ipb_id']."'" ) );
		}
	}
	
	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$topic['topic_id'].'&view=findpost&p='.$row['ipb_id'], '', TRUE);