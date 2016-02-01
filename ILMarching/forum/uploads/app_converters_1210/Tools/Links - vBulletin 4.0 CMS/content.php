<?php
/**
 * IPS Converters
 * vBulletin CMS Links
 * content.php
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

$qs = explode('-', str_replace( '/content.php/', '', $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_SERVER['REQUEST_URI'] ) );
$articleId = intval( $qs[0] );

//-----------------------------------------
// Do we have a valid app?
//-----------------------------------------

$app = $DB->buildAndFetch( array( 'select' => 'app_id, app_merge', 'from' => 'conv_apps', 'where' => "name='".CONV_CCS_ID."'" ) );

if (!$app['app_id'])
{
	echo 'Invalid Conversion ID';
	exit;
}

// Obtain our page.
$page = $DB->buildAndFetch( array( 'select' => '*', 'from' => 'ccs_pages', 'where' => "page_content LIKE '%{parse articles}%'" ) );

if ( ! $page['page_id'] )
{
	$registry->output->silentRedirect( IPB_URL, '', TRUE );
}

// And get our article link.
$link = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id = {$articleId} AND type = 'ccs_articles' AND app = {$app['app_id']}" ) );

if ( ! $link['ipb_id'] )
{
	$registry->output->silentRedirect( IPB_URL, '', TRUE );
}

// Still here?
$registry->output->silentRedirect( IPB_URL . "/index.php?app=ccs&module=pages&section=pages&id={$page['page_id']}&record={$link['ipb_id']}", '', TRUE );