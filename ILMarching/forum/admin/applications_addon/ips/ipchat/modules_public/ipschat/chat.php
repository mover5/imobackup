<?php

/**
 * Invision Power Services
 * IP.Board v3.0.4
 * Chat services
 * Last Updated: $Date: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Chat
 * @link		http://www.invisionpower.com
 * @since		Fir 12th Aug 2005
 * @version		$Revision: 10721 $
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class public_ipchat_ipschat_chat extends ipsCommand
{
	/**
	 * Master chat server URL
	 *
	 * @var	string
	 */
	const MASTERSERVER	= "http://chatservice.ipsdns.com/";
	
	/**
	 * Main class entry point
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void		[Outputs to screen]
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		//-----------------------------------------
		// Load lang file
		//-----------------------------------------
		
		ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'public_chat' ) );
		ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'public_editors' ), 'core' );

		//-----------------------------------------
		// IPB 3.1
		//-----------------------------------------
		
		if( $this->settings['ipb_reg_number'] )
		{
			$this->settings['ipschat_account_key']	= $this->settings['ipb_reg_number'];
		}
		
		//-----------------------------------------
		// Check that we have the key
		//-----------------------------------------
		
		if ( ! $this->settings['ipschat_account_key'] )
		{
			$this->registry->output->showError( 'no_chat_account_number', 'CHAT-01' );
		}
		
		$this->settings['ipschat_account_key'] = trim( $this->settings['ipschat_account_key'] );

		//-----------------------------------------
		// Can we access?
		//-----------------------------------------

		$access_groups = explode( ",", $this->settings['ipschat_group_access'] );
		
		$my_groups = array( $this->memberData['member_group_id'] );
		
		if( $this->memberData['mgroup_others'] )
		{
			$my_groups = array_merge( $my_groups, explode( ",", IPSText::cleanPermString( $this->memberData['mgroup_others'] ) ) );
		}
		
		$access_allowed = false;
		
		foreach( $my_groups as $group_id )
		{
			if( in_array( $group_id, $access_groups ) )
			{
				$access_allowed = 1;
				break;
			}
		}
		
		if( !$access_allowed )
		{
			$this->registry->output->showError( 'no_chat_access', 'CHAT-02' );
		}
		
		//-----------------------------------------
		// Is this a guest we banned?
		//-----------------------------------------
		
		if( !$this->memberData['member_id'] AND IPSCookie::get('chat_blocked') )
		{
			$this->registry->output->showError( 'no_chat_access', 'CHAT-021' );
		}

		//-----------------------------------------
		// Is this a spider?
		//-----------------------------------------
		
		if( $this->member->is_not_human )
		{
			$this->registry->output->showError( 'no_chat_access', 'CHAT-022' );
		}
		
		//-----------------------------------------
		// Is this a banned member?
		//-----------------------------------------
		
		if( $this->memberData['chat_banned'] )
		{
			$this->registry->output->showError( 'no_chat_access', 'CHAT-03' );
		}
    	
		//-----------------------------------------
		// Is it offline?
		//-----------------------------------------
		
		if( !$this->settings['ipschat_online'] )
		{
			$offline_groups = explode( ",", $this->settings['ipschat_offline_groups'] );
			
			$access_allowed = false;
			
			foreach( $my_groups as $group_id )
			{
				if( in_array( $group_id, $offline_groups ) )
				{
					$access_allowed = 1;
					break;
				}
			}
			
			if( !$access_allowed )
			{
				$this->_isOffline();
			}
    	}
    	
    	//-----------------------------------------
    	// Chat only online during certain hours of the day
    	//-----------------------------------------
    	
    	if( $this->settings['ipschat_online_start'] AND $this->settings['ipschat_online_end'] )
    	{
    		$_currentHour	= gmstrftime('%H') + ( $this->settings['time_offset'] - ( $this->memberData['dst_in_use'] ? -1 : 0 ) );

			//-----------------------------------------
			// Rollback if we cross midnight
			//-----------------------------------------
			
			if( $_currentHour < 0 )
			{
				$_currentHour	= 24 + $_currentHour;
			}

			//-----------------------------------------
			// Open 12:00 - 15:00
			//-----------------------------------------
			
			if( $this->settings['ipschat_online_end'] > $this->settings['ipschat_online_start'] )
			{
	    		if( !($_currentHour >= $this->settings['ipschat_online_start']) OR !($_currentHour < $this->settings['ipschat_online_end']) )
	    		{
	    			$this->_isOffline();
	    		}
    		}
    		
    		//-----------------------------------------
    		// Open 22:00 - 02:00
    		//-----------------------------------------
    		
    		else
    		{
    			$_open	= true;
    			
    			//-----------------------------------------
    			// Only check if we are not past the start time yet
    			// i.e. if at 23:00 we know it's open
    			//-----------------------------------------
    			
    			if( !($_currentHour >= $this->settings['ipschat_online_start']) )
    			{
    				//-----------------------------------------
    				// Now, if we're past end time, it's closed
    				// since we already know it's not past start time
    				//-----------------------------------------
    				
    				if( $_currentHour >= $this->settings['ipschat_online_end'] )
    				{
    					$_open	= false;
    				}
    				
    			}
    			
	    		if( !$_open )
	    		{
	    			$this->_isOffline();
	    		}
    		}
    	}
    	
    	//-----------------------------------------
    	// Did we request to leave chat?
    	//-----------------------------------------
    	
    	if( $this->request['do'] == 'leave' )
    	{
    		$this->_leaveChat();
    	}

    	//-----------------------------------------
    	// Post restriction or unacknowledged warnings?
    	//-----------------------------------------

    	if( $this->memberData['member_id'] )
    	{
			$message = '';

			if ( $this->memberData['restrict_post'] )
			{
				$data = IPSMember::processBanEntry( $this->memberData['restrict_post'] );

				if ( $data['date_end'] )
				{
					if ( time() >= $data['date_end'] )
					{
						IPSMember::save( $this->memberData['member_id'], array( 'core' => array( 'restrict_post' => 0 ) ) );
					}
					else
					{
						$message = sprintf( $this->lang->words['warnings_restrict_post_temp'], $this->lang->getDate( $data['date_end'], 'JOINED' ) );
					}
				}
				else
				{
					$message = $this->lang->words['warnings_restrict_post_perm'];
				}
				
				if ( $this->memberData['unacknowledged_warnings'] )
				{
					$warn = ipsRegistry::DB()->buildAndFetch( array( 'select' => '*', 'from' => 'members_warn_logs', 'where' => "wl_member={$this->memberData['member_id']} AND wl_rpa<>0", 'order' => 'wl_date DESC', 'limit' => 1 ) );

					if ( $warn['wl_id'] )
					{
						$moredetails = "<a href='javascript:void(0);' onclick='warningPopup( this, {$warn['wl_id']} )'>{$this->lang->words['warnings_moreinfo']}</a>";
					}
				}
				
				$this->registry->getClass('output')->showError( "{$message} {$moredetails}", '10CHAT126', null, null, 403 );
			}

			if ( empty($message) )
			{
				if ( $this->memberData['unacknowledged_warnings'] )
				{
					$unAcknowledgedWarns = ipsRegistry::DB()->buildAndFetch( array( 'select' => '*', 'from' => 'members_warn_logs', 'where' => "wl_member={$this->memberData['member_id']} AND wl_acknowledged=0", 'order' => 'wl_date DESC', 'limit' => 1 ) );

					if ( $unAcknowledgedWarns['wl_id'] )
					{
						$this->registry->getClass('output')->silentRedirect( $this->registry->getClass('output')->buildUrl( "app=members&amp;module=profile&amp;section=warnings&amp;do=acknowledge&amp;id={$unAcknowledgedWarns['wl_id']}" ) );
					}
				}
			}
    	}
    	
		//-----------------------------------------
		// Moderator permissions
		//-----------------------------------------
		
		$permissions	= 0;
		$private		= 0;
		
		if( $this->settings['ipschat_mods'] )
		{
			$mod_groups = explode( ",", $this->settings['ipschat_mods'] );

			foreach( $my_groups as $group_id )
			{
				if( in_array( $group_id, $mod_groups ) )
				{
					$permissions = 1;
					break;
				}
			}
    	}
    	
		if( $this->settings['ipschat_private'] )
		{
			$mod_groups = explode( ",", $this->settings['ipschat_private'] );

			foreach( $my_groups as $group_id )
			{
				if( in_array( $group_id, $mod_groups ) )
				{
					$private = 1;
					break;
				}
			}
    	}
    	
    	//-----------------------------------------
    	// Agree to rules?
    	//-----------------------------------------
    	
		if( $this->settings['ipschat_enable_rules'] )
		{
			if( !$_POST['agree'] )
			{
				$this->agreeToTerms();
				return;
			}
		}

		//-----------------------------------------
		// Get going!
		//-----------------------------------------
		
		$userId			= 0;
		$userName		= $this->cleanUsername( $this->memberData['member_id'] ? $this->memberData['members_display_name'] : $this->lang->words['global_guestname'] );
		$roomId			= 0;
		$accessKey		= '';

		$result		= $this->_callServer( self::MASTERSERVER . "gateway31.php?api_key={$this->settings['ipschat_account_key']}&user={$userName}&level={$permissions}" );
		$results	= explode( ',', $result );
		
		if( $results[0] == 0 )
		{
			$this->registry->output->showError( $this->lang->words['connect_gw_error_' . $results[1] ] ? $this->lang->words['connect_gw_error_' . $results[1] ] : $this->lang->words['connect_error'], 'CSTART-' . intval($results[1]) );
		}

		$roomId		= intval($results[3]);
		$serverHost	= $results[1];
		$serverPath	= $results[2];
	
    	//-----------------------------------------
    	// This a guest with a chat user id?
    	//-----------------------------------------
    	
    	$_extra	= '';
    	
    	if( !$this->memberData['member_id'] )
    	{
    		$_extra	= "&userid=" . intval( IPSCookie::get( 'chat_user_id' ) );
    	}

    	//-----------------------------------------
    	// Join chat room
    	//-----------------------------------------
    	
		$result		= $this->_callServer( "http://{$serverHost}{$serverPath}/join31.php?api_key={$this->settings['ipschat_account_key']}&user={$userName}&level={$permissions}&room={$roomId}&forumid={$this->memberData['member_id']}&forumgroup={$this->memberData['member_group_id']}{$_extra}" );
		$results	= explode( ',', $result );

		if( $results[0] == 0 )
		{
			$this->registry->output->showError( $this->lang->words['connect_error_' . $results[1] ] ? $this->lang->words['connect_error_' . $results[1] ] : $this->lang->words['connect_error'], 'CJOIN-' . intval($results[1]) );
		}
		
		$userId		= $results[1];
		$accessKey	= $results[2];

    	//-----------------------------------------
    	// Set the options...
    	//-----------------------------------------
    	
    	$options	= array(
    						'roomId'			=> $roomId,
    						'userId'			=> $userId,
    						'accessKey'			=> $accessKey,
    						'serverHost'		=> $serverHost,
    						'serverPath'		=> $serverPath,
    						'ourUrl'			=> urlencode($this->settings['board_url']),
    						'moderator'			=> $permissions,
    						'private'			=> $private,
    						);

		$this->cache->setCache( 'chatserver', array( 'host' => $serverHost, 'path' => $serverPath ), array( 'donow' => 1, 'array' => 1 ) );

    	//-----------------------------------------
    	// Remember guest so they don't flood the room
    	//-----------------------------------------
    	
    	if( !$this->memberData['member_id'] )
    	{
    		IPSCookie::set( 'chat_user_id', $userId );
    	}

		//-----------------------------------------
		// Get online list
		//-----------------------------------------
		
		$online		= array();
		$memberIds	= array();
		$result		= $this->_callServer( "http://{$serverHost}{$serverPath}/list.php?room={$roomId}&user={$userId}&access_key={$accessKey}" );

		if( $result )
		{
			$results	= explode( "~~||~~", $result );
			
			if( $results[0] == 1 )
			{
				foreach( $results as $k => $v )
				{
					if( $k == 0 )
					{
						continue;
					}
					
					$_thisRecord	= explode( ',', $v );
					
					if( !$_thisRecord[0] )
					{
						continue;
					}

					$online[]		= array(
											'user_id'	=> $_thisRecord[0],
											'user_name'	=> str_replace( '~~#~~', ',', $_thisRecord[1] ),
											'forum_id'	=> $_thisRecord[2]
											);

					if( $_thisRecord[0] == $userId )
					{
						$userName	= str_replace( '~~#~~', ',', $_thisRecord[1] );
					}

					$memberIds[ $_thisRecord[2] ]	= intval($_thisRecord[2]);
				}
			}
		}
		
		$members	= IPSMember::load( $memberIds );
		$chatters	= array();
		
		foreach( $online as $_online )
		{
			$_online['member']	= IPSMember::buildDisplayData( $members[ $_online['forum_id'] ] );
			
			$_online['member']['prefix']	= str_replace( '"', '__DBQ__', $_online['member']['prefix'] );
			$_online['member']['suffix']	= str_replace( '"', '__DBQ__', $_online['member']['suffix'] );
			
			$_online['member']['members_display_name']	= $_online['member']['member_id'] ? $_online['member']['members_display_name'] : $this->lang->words['global_guestname'] . "_" . $_online['user_id'];
			$_online['member']['_canBeIgnored']			= $_online['member']['member_id'] ? $_online['member']['_canBeIgnored'] : 1;
			$_online['member']['g_id']					= $_online['member']['member_id'] ? $_online['member']['g_id'] : $this->settings['guest_group'];
			
			$chatters[ $_online['forum_id'] ? strtolower($members[ $_online['forum_id'] ]['members_display_name']) : $_online['user_name'] ]	= $_online;
		}

		ksort($chatters);
		
		//-----------------------------------------
		// Add ignored guests to list
		//-----------------------------------------
		
		$_ignored	= IPSCookie::get('chat_ignored_guests');
		
		if( $_ignored )
		{
			$_userIds	= explode( ',', IPSText::cleanPermString( $_ignored ) );
			
			foreach( $_userIds as $_userId )
			{
				$this->memberData['_ignoredUsers'][ 'g_' . $_userId ] = array( 'ignore_chats' => 1 );
			}
		}

		//-----------------------------------------
		// Output
		//-----------------------------------------
		
		$this->output .= $this->registry->getClass('output')->getTemplate('ipchat')->chatRoom( $options, $chatters );
		
		//-----------------------------------------
		// Put us in "chatting"
		//-----------------------------------------
		
		$tmp_cache = $this->cache->getCache('chatting');
		$new_cache = array();

		if ( is_array( $tmp_cache ) and count( $tmp_cache ) )
		{
			foreach( $tmp_cache as $id => $data )
			{
				//-----------------------------------------
				// Not hit in 2 mins?
				//-----------------------------------------
				
				if ( $data['updated'] < ( time() - 120 ) )
				{
					continue;
				}
				
				//-----------------------------------------
				// This us?
				//-----------------------------------------
				
				if ( $data['member_id'] == $this->memberData['member_id'] )
				{
					$data['updated']	= time();
				}
				
				//-----------------------------------------
				// No user id?
				//-----------------------------------------
				
				if( !$data['userid'] )
				{
					continue;
				}
				
				//-----------------------------------------
				// Not online according to server?
				//-----------------------------------------
				
				$_isOnlineServer	= false;
				
				foreach( $online as $_serverOnline )
				{
					if( $_serverOnline['user_id'] == $data['userid'] )
					{
						$_isOnlineServer	= true;
					}
				}
				
				if( !$_isOnlineServer )
				{
					continue;
				}
				
				$new_cache[ $data['userid'] ] = $data;
			}
		}
		
		if( !$new_cache[ $userId ] )
		{
			$new_cache[ $userId ]	= array( 'updated' => time(), 'userid' => $userId, 'username' => $this->memberData['member_id'] ? $this->memberData['members_display_name'] : $userName, 'member_id' => $this->memberData['member_id'] );
		}
		
		//-----------------------------------------
		// Add any from server that are missing
		//-----------------------------------------
		
		foreach( $online as $_serverOnline )
		{
			if( !isset( $new_cache[ $_serverOnline['user_id'] ] ) )
			{
				$new_cache[ $_serverOnline['user_id'] ]	= array(
																	'updated'	=> time(),
																	'userid'	=> $_serverOnline['user_id'],
																	'username'	=> $_serverOnline['user_name'],
																	'member_id'	=> $_serverOnline['forum_id'],
																	);
			}
		}

		//-----------------------------------------
		// Update cache
		//-----------------------------------------
														  
		$this->cache->setCache( 'chatting', $new_cache, array( 'donow' => 1, 'array' => 1 ) );
		
		//-----------------------------------------
		// Add our JS files
		//-----------------------------------------

		$this->registry->output->addToDocumentHead( 'javascript', $this->settings['public_dir'] . 'sounds/soundmanager2-nodebug-jsmin.js' );
		$this->registry->output->addToDocumentHead( 'raw', "<script type='text/javascript'>document.observe('dom:loaded', function() { soundManager.url = '{$this->settings['public_dir']}sounds/';soundManager.debugMode=false; });</script>" );
		//$this->registry->output->addToDocumentHead( 'javascript', $this->settings['public_dir'] . 'js/ips.chat.js' );
		
		$_ie	= <<<EOF
<!--[if lte IE 7]>
	<link rel="stylesheet" type="text/css" title='ChatIE7' media="screen" href="{$this->settings['public_dir']}style_css/ipchat_ie.css" />
<![endif]-->
EOF;
		$this->registry->output->addToDocumentHead( 'raw', $_ie );
		
		//-----------------------------------------
		// Show chat..
		//-----------------------------------------

		$this->registry->output->addNavigation( IPSLib::getAppTitle('ipchat'), '' );
		$this->registry->output->setTitle( IPSLib::getAppTitle('ipchat') . ' - ' . $this->settings['board_name'] );
		
		if( $this->request['_popup'] )
		{
			$this->registry->output->popUpWindow( $this->output );
		}
		else
		{
			$this->registry->output->addContent( $this->output );
			$this->registry->output->sendOutput();
		}
	}
	
	/**
	 * Clean username for chat
	 *
	 * @param	string		Username
	 * @return	string		HTML
	 */
	protected function cleanUsername( $username )
	{
		$username		= str_replace( "\r", '', $username );
		$username		= str_replace( "\n", '__N__', $username );
		$username		= str_replace( ",", '__C__', $username );
		$username		= str_replace( "=", '__E__', $username );
		$username		= str_replace( "+", '__PS__', $username );
		$username		= str_replace( "&", '__A__', $username );
		$username		= str_replace( "%", '__P__', $username );
		$username		= urlencode($username);
		
		return $username;
	}

	/**
	 * Show a screen requiring users to agree to terms before continuing
	 *
	 * @return	@e void
	 */
	protected function agreeToTerms()
	{
		IPSText::getTextClass('bbcode')->parse_bbcode		= 1;
		IPSText::getTextClass('bbcode')->parse_html			= 0;
		IPSText::getTextClass('bbcode')->parse_nl2br		= 0;
		IPSText::getTextClass('bbcode')->parse_emoticons	= 1;
		IPSText::getTextClass('bbcode')->parsing_section	= 'global';
		
		$this->settings['ipschat_rules']	= IPSText::getTextClass('bbcode')->preDbParse( $this->settings['ipschat_rules'] );
		$this->settings['ipschat_rules']	= IPSText::getTextClass('bbcode')->preDisplayParse( $this->settings['ipschat_rules'] );

		$this->registry->output->addNavigation( IPSLib::getAppTitle('ipchat'), '' );
		$this->registry->output->setTitle( IPSLib::getAppTitle('ipchat') . ' - ' . $this->settings['board_name'] );
		
		if( $this->request['_popup'] )
		{
			$this->registry->output->popUpWindow( $this->registry->getClass('output')->getTemplate('ipchat')->chatRules( $this->settings['ipschat_rules'] ) );
		}
		else
		{
			$this->registry->output->addContent( $this->registry->getClass('output')->getTemplate('ipchat')->chatRules( $this->settings['ipschat_rules'] ) );
			$this->registry->output->sendOutput();
		}
	}
		
	/**
	 * Show an offline message
	 *
	 * @return	@e void
	 */
	protected function _isOffline()
	{
		IPSText::getTextClass('bbcode')->parse_bbcode		= 1;
		IPSText::getTextClass('bbcode')->parse_html			= 1;
		IPSText::getTextClass('bbcode')->parse_nl2br		= 1;
		IPSText::getTextClass('bbcode')->parse_emoticons	= 1;
		IPSText::getTextClass('bbcode')->parsing_section	= 'global';
		
		$this->settings['ipschat_offline_msg']	= IPSText::getTextClass('bbcode')->preDbParse( $this->settings['ipschat_offline_msg'] );
		$this->settings['ipschat_offline_msg']	= IPSText::getTextClass('bbcode')->preDisplayParse( $this->settings['ipschat_offline_msg'] );

		$this->registry->output->showError( $this->settings['ipschat_offline_msg'], 'CHAT-04' );
	}
	
	/**
	 * Leave chat
	 *
	 * @return	@e void
	 */
	protected function _leaveChat()
	{	
		//-----------------------------------------
		// Get server info from cache
		//-----------------------------------------

		$this->request['secure_key'] = $this->request['secure_key'] ? $this->request['secure_key'] : $this->request['md5check'];

		if( $this->request['secure_key'] != $this->member->form_hash )
		{
			$this->registry->output->showError( 'usercp_forums_bad_key', '10CHAT99' );
		}

		$cache	= $this->cache->getCache('chatserver');
		
		if( $cache['host'] )
		{
			//-----------------------------------------
			// Tell server we've left
			//-----------------------------------------
		
			$result		= $this->_callServer( "http://{$cache['host']}{$cache['path']}/leave.php?room={$this->request['room']}&user={$this->request['user']}&access_key={$this->request['access_key']}" );
		}
				
		//-----------------------------------------
		// Remove us from "chatting"
		//-----------------------------------------
		
		$tmp_cache = $this->cache->getCache('chatting');
		$new_cache = array();

		if ( is_array( $tmp_cache ) and count( $tmp_cache ) )
		{
			foreach( $tmp_cache as $data )
			{
				//-----------------------------------------
				// Not hit in 2 mins?
				//-----------------------------------------
				
				if ( $data['updated'] < ( time() - 120 ) )
				{
					continue;
				}
				
				//-----------------------------------------
				// This us?
				//-----------------------------------------
				
				if ( $data['userid'] == $this->request['user'] OR ( $this->memberData['member_id'] AND $data['member_id'] == $this->memberData['member_id'] ) )
				{
					continue;
				}
				
				//-----------------------------------------
				// No user id?
				//-----------------------------------------
				
				if( !$data['userid'] )
				{
					continue;
				}
				
				$new_cache[ $data['userid'] ] = $data;
			}
		}
											  
		$this->cache->setCache( 'chatting', $new_cache, array( 'donow' => 1, 'array' => 1 ) );
				
		//-----------------------------------------
		// And redirect
		//-----------------------------------------
		
		if ( $this->request['popup'] )
		{
			echo "<script type='text/javascript'>window.close()</script>";
			$this->registry->output->redirectScreen( $this->lang->words['you_left_room'] , $this->settings['base_url'] );
		}
		else
		{
			$this->registry->output->redirectScreen( $this->lang->words['you_left_room'] , $this->settings['base_url'] );
		}
	}
	
	/**
	 * Get response from chat servers.  This is mostly copied from
	 * classFileManagement.php, however I needed HTTP 1.1 support, so
	 * I had to copy it and update.
	 *
	 * @param	string		URL to call
	 * @return	string		Response from server
	 */
	protected function _callServer( $url )
	{
		//-----------------------------------------
		// Try CURL first
		//-----------------------------------------
		
		if ( function_exists( 'curl_init' ) AND function_exists("curl_exec") )
		{
			$ch = curl_init( $url );
			
			curl_setopt( $ch, CURLOPT_HEADER		, 0);
			curl_setopt( $ch, CURLOPT_TIMEOUT		, 10 );
			curl_setopt( $ch, CURLOPT_POST			, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
			curl_setopt( $ch, CURLOPT_FAILONERROR	, 1 ); 
			curl_setopt( $ch, CURLOPT_MAXREDIRS		, 2 );
			curl_setopt( $ch, CURLOPT_HTTP_VERSION	, CURL_HTTP_VERSION_1_1 );
			
			/**
			 * Cannot set this when safe_mode or open_basedir is enabled
			 * @link http://forums.invisionpower.com/index.php?autocom=tracker&showissue=11334
			 */
			if( !ini_get('open_basedir') AND !ini_get('safe_mode') )
			{
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 ); 
			}

			$data = curl_exec($ch);
			curl_close($ch);
			
			return trim($data);
		}

		//-----------------------------------------
		// Fall back to sockets
		//-----------------------------------------

		$data				= null;
		$fsocket_timeout	= 20;
		
		//-----------------------------------------
		// Get details
		//-----------------------------------------

		$url_parts = @parse_url($url);
		
		if ( ! $url_parts['host'] )
		{
			return '0';
		}

		$host = $url_parts['host'];
	 	$port = ( isset($url_parts['port']) ) ? $url_parts['port'] : ( $url_parts['scheme'] == 'https' ? 443 : 80 );

	 	if ( !empty( $url_parts["path"] ) )
		{
			$path = $url_parts["path"];
		}
		else
		{
			$path = "/";
		}
 
		if ( !empty( $url_parts["query"] ) )
		{
			$path .= "?" . $url_parts["query"];
		}
	 	
	 	//-----------------------------------------
	 	// Establish connection
	 	//-----------------------------------------

	 	if ( ! $fp = @fsockopen( $host, $port, $errno, $errstr, $fsocket_timeout ) )
	 	{
			return '0';
		}
		else
		{
			if ( ! fputs( $fp, "GET {$path} HTTP/1.1\r\nHost: {$host}\r\nConnection: Keep-Alive\r\n\r\n" ) )
			{
				return '0';
			}
		}

		@stream_set_timeout( $fp, $fsocket_timeout );
		
		$status = @stream_get_meta_data($fp);
		
		while( ! feof($fp) && ! $status['timed_out'] )		
		{
		  $data .= fgets( $fp, 8192 );
		  $status = stream_get_meta_data($fp);
		}
		
		fclose ($fp);

		//-----------------------------------------
		// Clean the result and return
		//-----------------------------------------

		// HTTP/1.1 ### ABCD
		$this->http_status_code = substr( $data, 9, 3 );
		$this->http_status_text = substr( $data, 13, ( strpos( $data, "\r\n" ) - 13 ) );

		$_chunked	= false;
		
		if( preg_match( "/Transfer\-Encoding:\s*chunked/i", $data ) )
		{
			$_chunked	= true;
		}
		
		$tmp	= split( "\r\n\r\n", $data, 2 );
		$data	= trim($tmp[1]);

		if( $_chunked )
		{
			$lines	= explode( "\n", $data );
			array_pop($lines);
			array_shift($lines);
			$data	= implode( "\n", $lines );
		}

 		return trim($data);
	}
}