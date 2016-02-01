<?php
/**
 * Invision Power Services
 * IP.Board v3.0.4
 * API file for hooks to call to
 * Last Updated: $Date: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Chat
 * @link		http://www.invisionpower.com
 * @since		20th February 2002
 * @version		$Revision: 10721 $
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class hooksApi
{
	/**#@+
	 * Registry object
	 *
	 * @var		object
	 */	
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $lang;
	protected $member;
	protected $memberData;
	protected $cache;
	protected $caches;
	/**#@-*/

	/**
	 * Constructor
	 *
	 * @param	object		$registry		Registry object
	 * @return	@e void
	 */
	public function __construct( ipsRegistry $registry )
	{
		/* Make object */
		$this->registry = $registry;
		$this->DB       = $this->registry->DB();
		$this->settings =& $this->registry->fetchSettings();
		$this->request  =& $this->registry->fetchRequest();
		$this->lang     = $this->registry->getClass('class_localization');
		$this->member   = $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
		$this->cache    = $this->registry->cache();
		$this->caches   =& $this->registry->cache()->fetchCaches();
	}
	
	/**
	 * Show the who's chatting block on the board index
	 *
	 * @return	string		HTML output
	 */
	public function whosChatting()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$member_ids         = array();
		$to_load            = array();
		
		//-----------------------------------------
		// Check module/app
		//-----------------------------------------

		if( !IPSLib::appIsInstalled('ipchat') OR !$this->settings['ipschat_online'] )
		{
			return '';
		}

		//-----------------------------------------
		// Check
		//-----------------------------------------
		
		if ( ! $this->settings['ipschat_whos_chatting'] )
		{
			return '';
		}
		
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
			return '';
		}
		
		//-----------------------------------------
		// Sort and show :D
		//-----------------------------------------
		
		$_guests	= array();
		
		if ( is_array( $this->cache->getCache('chatting') ) AND count( $this->cache->getCache('chatting') ) )
		{
			foreach( $this->cache->getCache('chatting') as $data )
			{
				if ( $data['updated'] < ( time() - 120 ) )
				{
					continue;
				}
				
				if( $data['member_id'] )
				{
					$to_load[ $data['member_id'] ] = $data['member_id'];
				}
				else
				{
					$_guests[ $data['username'] ]	= $data['username'];
				}
			}
		}

		//-----------------------------------------
		// Got owt?
		//-----------------------------------------

		if ( count($to_load) )
		{
			$_seenIds	= array();
			
			$this->DB->build( array( 'select' => 'm.member_id, m.members_display_name, m.member_group_id, m.members_seo_name',
												     'from'   => array( 'members' => 'm' ),
												     'where'  => "m.member_id IN(" . implode( ",", $to_load ) . ")",
	 												 'add_join' => array( 0 => array( 'select' => 's.id, s.login_type, s.current_appcomponent',
																					  'from'   => array( 'sessions' => 's' ),
																					  'where'  => 's.member_id=m.member_id',
																					  'type'   => 'left' ) ),
							)		);
			$this->DB->execute();
			
			while ( $m = $this->DB->fetch() )
			{
				if( in_array( $m['member_id'], $_seenIds ) )
				{
					continue;
				}
				
				//-----------------------------------------
				// @link	http://community.invisionpower.com/tracker/issue-20547-logout-from-the-board-still-shown-the-user-active/
				// @see 	ipchatMemberSync
				//-----------------------------------------
				
				if( !$m['id'] )
				{
					continue;
				}
				
				$_seenIds[]	= $m['member_id'];

				$_key						= $m['members_display_name'];
				$m['members_display_name']	= IPSMember::makeNameFormatted( $m['members_display_name'], $m['member_group_id'] );
				$member_ids[ $_key ]		= "<a href='" . $this->registry->getClass('output')->buildSEOUrl( "showuser={$m['member_id']}", 'public', $m['members_seo_name'], 'showuser' ) . "'>{$m['members_display_name']}</a>";
			}
		}
		
		$member_ids	= array_merge( $_guests, $member_ids );
		
		ksort($member_ids);
		
		//-----------------------------------------
		// Got owt?
		//-----------------------------------------
		
		$html	= '';
		
		$this->lang->loadLanguageFile( array( 'public_chat' ), 'ipchat' );
		
		if ( count( $member_ids ) )
		{
			$html = $this->registry->getClass('output')->getTemplate('ipchat')->whoschatting_show( intval(count($member_ids)), $member_ids );
		}
		else
		{
			if ( ! $this->settings['ipschat_hide_chatting'] )
			{
				$html = $this->registry->getClass('output')->getTemplate('ipchat')->whoschatting_empty();
			}
		}
		
		return $html;
	}

	/**
	 * Show a count of the number of chatters on the 'Chat' tab
	 *
	 * @return	string		HTML output
	 */
	public function getChatTabCount()
	{
		if( !IPSLib::appIsInstalled('ipchat') OR !$this->settings['ipschat_online'] )
		{
			return '';
		}
		
		$cache	= $this->cache->getCache('chatting');
		$count	= 0;
		
		//-----------------------------------------
		// Sort and show :D
		//-----------------------------------------
		
		if ( is_array( $this->caches['chatting'] ) AND count( $this->caches['chatting'] ) )
		{
			foreach( $this->caches['chatting'] as $id => $data )
			{
				if ( $data['updated'] < ( time() - 120 ) )
				{
					continue;
				}
				
				$count++;
			}
		}
		
		return $this->registry->getClass('output')->getTemplate('ipchat')->tabCount( $count );
	}
	
	/**
	 * Make chat tab open in a new window
	 *
	 * @return	string		HTML output
	 */
	public function chatNewWindow()
	{
		if( !IPSLib::appIsInstalled('ipchat') OR !$this->settings['ipschat_online'] )
		{
			return '';
		}
		
		return $this->settings['ipchat_new_window'] ? $this->registry->getClass('output')->getTemplate('ipchat')->newWindow() : '';
	}

	/**
	 * Show link for chat on mobile skin
	 *
	 * @return	string		HTML output
	 */
	public function chatMobileLink()
	{
		if( !IPSLib::appIsInstalled('ipchat') OR !$this->settings['ipschat_online'] )
		{
			return '';
		}
		
		return $this->registry->getClass('output')->getTemplate('ipchat')->chatMobileLink();
	}

	/**
	 * Show unban link in modcp
	 *
	 * @return	string		HTML output
	 */
	public function chatUnbanModcp( $member )
	{
		if( !IPSLib::appIsInstalled('ipchat') OR !$this->settings['ipschat_online'] )
		{
			return '';
		}
		
		return $this->registry->getClass('output')->getTemplate('ipchat')->chatUnbanModcp( $member );
	}
}