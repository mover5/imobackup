<?php
/**
 * IPS Converters
 * FBB Links
 * forumdisplay.php
 * Last Update: $Date: 2010-08-06 20:07:15 -0400 (Fri, 06 Aug 2010) $
 * Last Updated By: $Author: jason $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 456 $
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
if (!is_numeric($fbbglobals['fid']))
{
	// Boink to index
	$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
}



//-----------------------------------------
// Get our link and boink
//-----------------------------------------

$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id='".$fbbglobals['fid']."' AND type='forums' AND app='".$app['app_id']."'" ) );
$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?showforum='.$row['ipb_id'], '', TRUE);