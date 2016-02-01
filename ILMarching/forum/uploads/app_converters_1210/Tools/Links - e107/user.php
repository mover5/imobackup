<?php
/**
 * <pre>
 * IPS Converters
 * e107 Redirections - Members
 * Last Updated: $Date: 2011-04-06 15:41:49 -0400 (Wed, 06 Apr 2011) $ BY $Author: rashbrook $
 * </pre>
 *
 * @author 		Ryan Ashbrook
 * @copyright	(c) 2011 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/community/board/license.html
 * @package		IP.Board
 * @link		http://www.invisionpower.com
 * @version		$Revision: 519 $
 *
 */

preg_match ( '/id\.([0-9]+)/si', $_SERVER['QUERY_STRING'], $matches );
$oldid = intval ( $matches[1] );

require_once ( './config.php' );
if ( !file_exists ( IPB_PATH . '/initdata.php' ) )
{
	die ( 'Invalid IPB Path.' );
}

require_once ( IPB_PATH . '/initdata.php' );
require_once ( IPB_PATH . '/' . CP_DIRECTORY . '/sources/base/ipsRegistry.php' );
require_once ( IPB_PATH . '/' . CP_DIRECTORY . '/sources/base/ipsController.php' );

$registry = ipsRegistry::instance ( );
$registry->init ( );
$db = $registry->DB ( );

if ( !is_numeric ( $oldid ) )
{
	$registry->getClass ( 'output' )->silentRedirect ( IPB_URL, '', TRUE );
}

$app = $db->buildAndFetch ( array (
	'select'	=> 'app_id',
	'from'		=> 'conv_apps',
	'where'		=> "name = '" . CONV_ID ."'"
) );

$row = $db->buildAndFetch ( array (
	'select'	=> 'ipb_id',
	'from'		=> 'conv_link',
	'where'		=> "foreign_id = '" . $oldid . "' AND app = '" . $app['app_id'] . "' AND type = 'members'"
) );

$registry->getClass ( 'output' )->silentRedirect ( IPB_URL . '/index.php?showuser=' . $row['ipb_id'], '', TRUE );

?>