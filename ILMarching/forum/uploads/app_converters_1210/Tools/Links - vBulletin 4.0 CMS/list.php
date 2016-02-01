<?php
/**
 * IPS Converters
 * vBulletin CMS Category Links
 * list.php
 * Last Update: $Date:$
 * Last Updated By: $Author:$
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision:$
 */

//-----------------------------------------
// Load our libraries
//-----------------------------------------

require_once 'config.php';

if ( !file_exists( IPB_PATH.'/initdata.php' ) )
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

$qs = explode( '-', str_replace( '/list.php?category/', '', $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_SERVER['REQUEST_URI'] ) );
preg_match( "#\d+#", $qs, $m );
ipsRegistry::$request['c'] = intval( $m[0] );

//-----------------------------------------
// Do we have a valid app?
//-----------------------------------------

$app = $DB->buildAndFetch( array( 'select' => 'app_id, app_merge', 'from' => 'conv_apps', 'where' => "name='".CONV_CCS_ID."'" ) );

if ( !$app['app_id'] )
{
	echo 'Invalid Conversion ID';
	exit;
}

if ( isset( ipsRegistry::$request['c'] ) )
{
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	
	if ( !is_numeric( ipsRegistry::$request['c'] ) )
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
		exit;
	}

	// ccs_databases ???
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id=".intval(ipsRegistry::$request['c'])." AND type='ccs_database_categories' AND app='".$app['app_id']."'" ) );
	$page = $DB->buildAndFetch( array( 'select' => '*', 'from' => 'ccs_pages', 'where' => "page_content LIKE '%{parse articles}%'" ) );
	
	if ( ! $page['page_id'] )
	{
		$registry->output->silentRedirect( IPB_URL, '', TRUE );
	}
	
	if ( $row )
	{
		// so we need to get the CCS category.. which is a part of pages.. so we need to get the page ID and category ID somehow..
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?app=ccs&module=pages&section=pages&id=' . $page['page_id'] . '&category='.$row['ipb_id'], '', TRUE);
	}
	else
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
		exit;
	}
}