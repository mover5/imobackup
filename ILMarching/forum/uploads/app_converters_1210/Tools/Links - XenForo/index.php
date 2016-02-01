<?php
/**
 * IPS Converters
 * XenForo Links
 * Last Update: $Date: 2013-10-09 19:21:55 -0400 (Wed, 09 Oct 2013) $
 * Last Updated By: $Author: rashbrook $
 *
 * @package		IPS Converters
 * @author 		Ryan Ashbrook
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 931 $
 */

/**
 * Configuration
 */

// Full URL to IPB (no trailing slash or index.php)
define ( 'IPB_URL', 'http://localhost/ipb_mysql' );

// Full Path to IPB (no trailing slash or index.php)
define ( 'IPB_PATH', 'C:/Zend/Apache2/htdocs/ipb_mysql' );

// Convert ID (what you used when deciding which software to convert from)
define ( 'CONV_ID', 'xf_test_1' );

/** DONE EDITING */

define( 'CCS_GATEWAY_CALLED', true );

if ( !file_exists ( IPB_PATH . '/initdata.php' ) )
{
	echo ( 'Wrong IPB Path' );
	exit;
}

$do_redirect	= FALSE;
$convType		= '';
$url			= '';
$table			= '';
$id				= 0;

$_qs = ( $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_SERVER['PATH_INFO'] );
$_qs = ( $_qs ? $_qs : $_SERVER['REQUEST_URI'] );
$_qs = str_replace( rtrim( $_SERVER['PHP_SELF'], 'index.php' ), '', $_qs );

preg_match ( '#([forums|threads|members]+)\/(.*)\.([0-9]+)\/#i', $_qs, $matches );

$old_id = ( int )$matches[3];

switch ( $matches[1] )
{
	case 'forums':
		$do_redirect	= 1;
		$convType		= 'forums';
		$url			= 'showforum';
	break;
	
	case 'threads':
		$do_redirect	= 1;
		$convType		= 'topics';
		$url			= 'showtopic';
		$table			= '_topics';
	break;
	
	case 'members':
		$do_redirect	= 1;
		$convType		= 'members';
		$url			= 'showuser';
	break;
}

if ( !$do_redirect )
{
	define ( 'IPB_THIS_SCRIPT', 'public' );
	require_once ( IPB_PATH . '/initdata.php' );
	require_once ( IPS_ROOT_PATH . 'sources/base/ipsRegistry.php' );
	require_once ( IPS_ROOT_PATH . 'sources/base/ipsController.php' );
	
	ipsController::run ( );
	
	exit;
}

require_once ( IPB_PATH . '/initdata.php' );
require_once ( IPB_PATH . '/' . CP_DIRECTORY . '/sources/base/ipsRegistry.php' );

$registry = ipsRegistry::instance ( );
$registry->init ( );

$DB = $registry->DB ( );

if ( !is_numeric ( $old_id ) )
{
	$registry->getClass ( 'output' )->silentRedirect ( IPB_URL, '', TRUE );
}

$app = $DB->buildAndFetch ( array (
	'select'	=> 'app_id',
	'from'		=> 'conv_apps',
	'where'		=> "name = '" . CONV_ID . "'"
) );

if ( !$app['app_id'] )
{
	echo ( 'Invalid Conversion ID' );
	exit;
}

$row = $DB->buildAndFetch ( array (
	'select'	=> 'ipb_id',
	'from'		=> 'conv_link'.$table,
	'where'		=> "foreign_id = " . intval ( $old_id ) . " AND type = '" . $convType . "' AND app = " . $app['app_id']
) );

if ( $row['ipb_id'] )
{
	$registry->getClass ( 'output' )->silentRedirect ( IPB_URL . '?' . $url . '=' . $row['ipb_id'], '', TRUE );
}
else
{
	$registry->getClass ( 'output' )->silentRedirect ( IPB_URL, '', TRUE );
}