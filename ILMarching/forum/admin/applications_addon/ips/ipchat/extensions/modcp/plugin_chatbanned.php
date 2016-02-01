<?php
/**
 * @file		plugin_chatbanned.php 	Moderator control panel plugin: show users banned from chat
 *~TERABYTE_DOC_READY~
 * $Copyright: (c) 2001 - 2011 Invision Power Services, Inc.$
 * $License: http://www.invisionpower.com/company/standards.php#license$
 * $Author: bfarber $
 * @since		2/23/2011
 * $LastChangedDate: 2011-11-07 13:10:35 -0500 (Mon, 07 Nov 2011) $
 * @version		v3.4.5
 * $Revision: 9774 $
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

/**
 *
 * @class		plugin_ipchat_chatbanned
 * @brief		Moderator control panel plugin: show users banned from chat
 */
class plugin_ipchat_chatbanned
{
	/**
	 * Registry Object Shortcuts
	 *
	 * @var		$registry
	 * @var		$DB
	 * @var		$settings
	 * @var		$request
	 * @var		$lang
	 * @var		$member
	 * @var		$memberData
	 * @var		$cache
	 * @var		$caches
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

	/**
	 * Constructor
	 *
	 * @param	object		$registry		Registry object
	 * @return	@e void
	 */
	public function __construct( ipsRegistry $registry ) 
	{
		//-----------------------------------------
		// Make shortcuts
		//-----------------------------------------

		$this->registry		= $registry;
		$this->DB			= $this->registry->DB();
		$this->settings		=& $this->registry->fetchSettings();
		$this->request		=& $this->registry->fetchRequest();
		$this->member		= $this->registry->member();
		$this->memberData	=& $this->registry->member()->fetchMemberData();
		$this->cache		= $this->registry->cache();
		$this->caches		=& $this->registry->cache()->fetchCaches();
		$this->lang			= $this->registry->class_localization;
		
		/* Load language */
		$this->lang->loadLanguageFile( array( 'public_chat' ), 'ipchat' );
	}
	
	/**
	 * Returns the primary tab key for the navigation bar
	 * 
	 * @return	@e string
	 */
	public function getPrimaryTab()
	{
		return 'manage_members';
	}
	
	/**
	 * Returns the secondary tab key for the navigation bar
	 * 
	 * @return	@e string
	 */
	public function getSecondaryTab()
	{
		return 'chatbanned';
	}

	/**
	 * Determine if we can view tab
	 *
	 * @param	array 	$permissions	Moderator permissions
	 * @return	@e bool
	 */
	public function canView( $permissions )
	{
		if( $this->memberData['g_is_supmod'] )
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Execute plugin
	 *
	 * @param	array 	$permissions	Moderator permissions
	 * @return	@e string
	 */
	public function executePlugin( $permissions )
	{
		//-----------------------------------------
		// Check permissions
		//-----------------------------------------

		if( !$this->canView( $permissions ) )
		{
			return '';
		}
		
		//-----------------------------------------
		// Unban?
		//-----------------------------------------
		
		if( $this->request['mid'] AND $this->request['auth_key'] == $this->member->form_hash )
		{
			IPSMember::save( intval($this->request['mid']), array( 'core' => array( 'chat_banned' => 0 ) ) );
			
			$this->registry->output->redirectScreen( $this->lang->words['chat_unban_success'], $this->settings['base_url'] . "app=core&amp;module=modcp&amp;fromapp=ipchat&amp;tab=chatbanned" );
		}
		
		//-----------------------------------------
		// Initial setup
		//-----------------------------------------
		
		$this->lang->loadLanguageFile( array( 'public_list' ), 'members' );
		$this->registry->output->addToDocumentHead( 'importcss', "{$this->settings['css_base_url']}style_css/{$this->registry->output->skin['_csscacheid']}/ipb_mlist.css" );
		
		//-----------------------------------------
		// Get 10 banned members
		//-----------------------------------------
		
		$members	= array();

		$st			= intval($this->request['st']);
		$total		= $this->DB->buildAndFetch( array( 'select' => 'count(*) as members', 'from' => 'members', 'where' => "chat_banned=1" ) );

		$this->DB->build( array(
								'select'	=> 'm.*',
								'from'		=> array( 'members' => 'm' ),
								'order'		=> 'm.joined DESC',
								'limit'		=> array( $st, 10 ),
								'where'		=> "m.chat_banned=1",
								'add_join'	=> array(
													array(
														'select'	=> 'pp.*',
														'from'		=> array( 'profile_portal' => 'pp' ),
														'where'		=> 'm.member_id=pp.pp_member_id',
														'type'		=> 'left',
														),
													),
						)		);
		$outer	= $this->DB->execute();
		
		while( $r = $this->DB->fetch($outer) )
		{
			$r['_language']	= $this->lang->words['modcp_modq_indef'];

			$members[] = IPSMember::buildDisplayData( $r );
		}

		//-----------------------------------------
		// Page links
		//-----------------------------------------
		
		$pages	= $this->registry->output->generatePagination( array(	'totalItems'		=> $total['members'],
																		'itemsPerPage'		=> 10,
																		'currentStartValue'	=> $st,
																		'baseUrl'			=> "app=core&amp;module=modcp&amp;fromapp=ipchat&amp;tab=chatbanned",
															)		);

		return $this->registry->output->getTemplate('modcp')->membersList( 'chatbanned', $members, $pages );
	}
}