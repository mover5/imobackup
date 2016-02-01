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
//RewriteRule [^/]+/([0-9]+)-[^/]+\.html http://www.site.com/forum/showthread.php?t=$1 [L,R=301]
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

	if ( !is_numeric( ipsRegistry::$request['t'] ) )
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
		exit;
	}

	if ( isset( ipsRegistry::$request['t'] ) )
	{
		// Boink to topic
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.ipsRegistry::$request['t'], '', TRUE);
		exit;
	}

	if ( isset ( ipsRegistry::$request['p'] ) )
	{
		// Boink to post
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.ipsRegistry::$request['t'].'&view=findpost&p='.ipsRegistry::$request['p'], '', TRUE);
		exit;
	}

}

//-----------------------------------------
// Do we have a valid ID number?
//-----------------------------------------
if ( isset(ipsRegistry::$request['t']) )
{
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	if (!is_numeric(ipsRegistry::$request['t']))
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
		exit;
	}

	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_topics', 'where' => "foreign_id=".intval(ipsRegistry::$request['t'])." AND type='topics' AND app='".$app['app_id']."'" ) );
	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$row['ipb_id'], '', TRUE);
}
else
{
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------

	if (!is_numeric(ipsRegistry::$request['p']))
	{
	    // Boink to index
	    $registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
	    exit;
	}
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_posts', 'where' => "foreign_id=".intval(ipsRegistry::$request['p'])." AND type='posts' AND app='".$app['app_id']."'" ) );
	$topic = $DB->buildAndFetch( array( 'select' => 'topic_id', 'from' => 'posts', 'where' => "pid='".$row['ipb_id']."'" ) );

	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$topic['topic_id'].'&view=findpost&p='.$row['ipb_id'], '', TRUE);
}