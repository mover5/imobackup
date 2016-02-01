<?php
/**
 * IPS Converters
 * SMF Links
 * Last Update: $Date: 2011-11-18 13:13:29 -0500 (Fri, 18 Nov 2011) $
 * Last Updated By: $Author: AlexHobbs $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 602 $
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
	
	//-----------------------------------------
	// Load our libraries
	//-----------------------------------------
	
	if (!file_exists(IPB_PATH.'/initdata.php'))
	{
		echo 'Invalid IPB path';
		exit;
	}
	
	// Friendly URLs
	$qs = explode(',', str_replace( '/index.php/', '', $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_SERVER['REQUEST_URI'] ) );
	
	if ( $qs[0] == 'topic' )
	{
		$_REQUEST['topic']	= intval( $qs[1] );
		$_GET['topic']		= $_REQUEST['topic'];
	}
	
	//-----------------------------------------
	// Who am I and where are you!?
	//-----------------------------------------
	
	$ident    = '';
	$convType = '';
	$url      = '';
	$table	  = '';
	$urlPid   = 0;
	
	if ($_REQUEST['board'])
	{
		$ident = 'board';
		$convType = 'forums';
		$url = 'showforum';
	}
	elseif ($_REQUEST['topic'])
	{
		$ident = 'topic';
		$convType = 'topics';
		$url = 'showtopic';
		$table = '_topics';
		
		/* Search for PID */
		$_pid = strpos($_REQUEST['topic'], '.msg');
		
		if ( $_pid !== FALSE )
		{
			$urlPid = intval( substr( $_REQUEST['topic'], $_pid+4 ) );
			$_REQUEST['topic'] = substr( $_REQUEST['topic'], 0, $_pid );
			
			/* Reset $_GET as well */
			$_GET['topic'] = $_REQUEST['topic'];
		}
	}
	elseif (preg_match('/profile/', $_REQUEST['action']))
	{
		$ident = 'u';
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
	
	require_once( IPB_PATH.'/initdata.php' );
	require_once( IPB_PATH.'/'.CP_DIRECTORY.'/sources/base/ipsRegistry.php' );
	require_once( IPB_PATH.'/'.CP_DIRECTORY.'/sources/base/ipsController.php' );
	$registry = ipsRegistry::instance();
	$registry->init();
	$DB       = $registry->DB();
	
	if ($ident == 'u')
	{
		$explode = explode('=', $_REQUEST['action']);
		ipsRegistry::$request[$ident] = array_pop($explode);
	}
	
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
	
	if ( $row['ipb_id'] )
	{
		if ( $urlPid )
		{
			$pidData = $DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_posts', 'where' => "foreign_id=".$urlPid." AND type='posts' AND app='".$app['app_id']."'" ) );
			
			if ( $pidData['ipb_id'] )
			{
				$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?'.$url.'='.$row['ipb_id'].'&amp;view=findpost&amp;p='.$pidData['ipb_id'], '', TRUE);
			}
		}
		
		/* Fallback if no PID */
		$registry->getClass('output')->silentRedirect(IPB_URL.'/index.php?'.$url.'='.$row['ipb_id'], '', TRUE);
	}
	else
	{
		// Failed to retrieve ID... Boink to index
		$registry->getClass('output')->silentRedirect(IPB_URL, '', TRUE);
	}
	