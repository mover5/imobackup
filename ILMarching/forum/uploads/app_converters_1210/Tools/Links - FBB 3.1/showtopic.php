<?php
/**
 * IPS Converters
 * FBB Links
 * forumdisplay.php
 * Last Update: $Date: 2010-09-07 10:33:59 -0400 (Tue, 07 Sep 2010) $
 * Last Updated By: $Author: jason $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 459 $
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
require_once( IPB_PATH.'/admin/sources/base/ipsRegistry.php' );
require_once( IPB_PATH.'/admin/sources/base/ipsController.php' );
$registry = ipsRegistry::instance();
$registry->init();
$DB       = $registry->DB();

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
// Do we have a valid ID number?
//-----------------------------------------
if ( isset($fbbglobals['tid']) )
{
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	if (!is_numeric($fbbglobals['tid']))
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
		exit;
	}

	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_topics', 'where' => "foreign_id=".intval($fbbglobals['tid'])." AND type='topics' AND app='".$app['app_id']."'" ) );
	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$row['ipb_id'], '', TRUE);
}
else
{
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------

	if (!is_numeric($fbbglobals['pid']))
	{
	    // Boink to index
	    $registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
	    exit;
	}
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_posts', 'where' => "foreign_id=".intval($fbbglobals['pid'])." AND type='posts' AND app='".$app['app_id']."'" ) );
	$topic = $DB->buildAndFetch( array( 'select' => 'topic_id', 'from' => 'posts', 'where' => "pid='".$row['ipb_id']."'" ) );

	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showtopic='.$topic['topic_id'].'&view=findpost&p='.$row['ipb_id'], '', TRUE);
}