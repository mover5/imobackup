<?php
/**
 * IPS Converters
 * SMF Links
 * Last Update: $Date: 2010-02-15 10:44:06 +0100(lun, 15 feb 2010) $
 * Last Updated By: $Author: terabyte $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 423 $
 */
	
	//-----------------------------------------
	// Configuration
	//-----------------------------------------
	
	// THE URL TO YOUR IPB FORUMS (no trailing slash or index.php)
	define('IPB_URL', 'http://localhost/ipb3');
	
	// THE PATH TO YOUR IPB FORUMS (no trailing slash)
	define('IPB_PATH', '/home/Users/Mark/Sites/ipb3');
	
	// THE CONVERSION ID
	// This would have been asked for when setting up the converters
	define('CONV_ID', 'old_forums');
	
	/*
# WCF-SEO-START

Options -MultiViews
RewriteEngine On
RewriteBase /forum/

RewriteRule ^user/([0-9]+)-([^/\.]*)/?$ index.php?page=User&userID=$1&username=$2 [L,QSA]
RewriteRule ^([0-9]+)-([^/\.]*)/last-post\.html$ index.php?page=Thread&threadID=$1&action=lastPost [L,QSA]

RewriteRule ^([0-9]+)-([^/\.]*)-last-post/\.html$ index.php?page=Thread&threadID=$1&action=lastPost [L,QSA]

RewriteRule ^([0-9]+)-([^/\.]*)/first-new-post\.html$ index.php?page=Thread&threadID=$1&action=firstNew [L,QSA]
RewriteRule ^([0-9]+)-([^/\.]*)\.html$ index.php?page=Thread&threadID=$1 [L,QSA]
RewriteRule ^p([0-9]+)-([^/\.]*)(/?)\.html$ index.php?page=Thread&postID=$1 [L,QSA]
RewriteRule ^([^/\.]+)/([0-9]+)-([^/\.]*)/index([0-9]+)\.html$ index.php?page=Thread&threadID=$2&pageNo=$4 [L,QSA]
#RewriteRule ^([^/\.]+)/([0-9]+)-([^/\.]*)/?$ index.php?page=Thread&threadID=$2 [L,QSA]
RewriteRule ^(board[0-9]+-[^/\.]+/)*board([0-9]+)-([^/\.]+)/index([0-9]+)\.html$ index.php?page=Board&boardID=$2&pageNo=$4 [L,QSA]
RewriteRule ^(board[0-9]+-[^/\.]+/)*board([0-9]+)-([^/\.]+)/?$ index.php?page=Board&boardID=$2 [L,QSA]



RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /forum/index.php [L]
# WCF-SEO-END 
*/
	
	//-----------------------------------------
	// Load our libraries
	//-----------------------------------------
	
	if (!file_exists(IPB_PATH.'/initdata.php'))
	{
		echo 'Invalid IPB path';
		exit;
	}
	
	//-----------------------------------------
	// Who am I and where are you!?
	//-----------------------------------------
	
	$ident = '';
	$convType = '';
	$url = '';
	$table = '';
	
	if ($_REQUEST['page'] == 'Board' && $_REQUEST['boardID'])
	{
		$ident = 'boardID';
		$convType = 'forums';
		$url = 'showforum';
	}
	elseif ($_REQUEST['page'] == 'Thread' && $_REQUEST['threadID'])
	{
		$ident = 'threadID';
		$convType = 'topics';
		$url = 'showtopic';
		$table = '_topics';
	}
	elseif ($_REQUEST['page'] == 'Thread' && $_REQUEST['postID'])
	{
		$ident = 'postID';
		$convType = 'posts';
		$url = 'app=forums&module=forums&section=findpost&pid';
		$table = '_posts';
	}
	elseif ($_REQUEST['page'] == 'User' && $_REQUEST['userID'])
	{
		$ident = 'userID';
		$convType = 'members';
		$url = 'showuser';
	}
	else
	{
		define( 'IPB_THIS_SCRIPT', 'public' );
		require_once( IPB_PATH.'/initdata.php' );

		require_once( IPS_ROOT_PATH . 'sources/base/ipsRegistry.php' );
		require_once( IPS_ROOT_PATH . 'sources/base/ipsController.php' );

		ipsController::run();

		exit();
	}
	define('IPS_ENFORCE_ACCESS', true);
	
	require_once( IPB_PATH.'/initdata.php' );
	require_once( IPB_PATH.'/'.CP_DIRECTORY.'/sources/base/ipsRegistry.php' );
	require_once( IPB_PATH.'/'.CP_DIRECTORY.'/sources/base/ipsController.php' );
	$registry = ipsRegistry::instance();
	$registry->init();
	$DB       = $registry->DB();
	
	//-----------------------------------------
	// Do we have a valid ID number?
	//-----------------------------------------
	
	if (!is_numeric(ipsRegistry::$request[$ident]))
	{
		// Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
	}
	
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
	// Get our link and boink
	//-----------------------------------------
	
	$row = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link'.$table, 'where' => "foreign_id='".intval(ipsRegistry::$request[$ident])."' AND type='".$convType."' AND app='".$app['app_id']."'" ) );
	$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?'.$url.'='.$row['ipb_id'], '', TRUE);
