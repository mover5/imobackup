<?php

/**
 * Invision Power Services
 * IP.Board v3.0.4
 * Member property updater
 * Last Updated: $Date: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Chat
 * @link		http://www.invisionpower.com
 * @version		$Revision: 10721 $
 *
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class admin_member_form__ipchat implements admin_member_form
{	
	/**
	 * Tab name
	 *
	 * @var		string		Tab name
	 */
	public $tab_name = "";

	
	/**
	 * Returns sidebar links for this tab
	 *
	 * @param	array	$member		Member data
	 * @return	@e array Array of links
	 */
	public function getSidebarLinks( $member=array() )
	{
		return array();
	}
	
	/**
	 * Returns HTML tabs content for the page
	 *
	 * @param	array		$member		Member data
	 * @return	@e array Array of 'tabs' (HTML for the tabs), 'content' (HTML for the content)
	 */
	public function getDisplayContent( $member=array() )
	{
		//-----------------------------------------
		// Load skin
		//-----------------------------------------
		
		$html = ipsRegistry::getClass('output')->loadTemplate( 'cp_skin_chat', 'ipchat' );

		//-----------------------------------------
		// Load lang
		//-----------------------------------------
				
		ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'admin_chat' ), 'ipchat' );
		
		//-----------------------------------------
		// Get member data
		//-----------------------------------------
		
		$member = IPSMember::load( $member['member_id'], 'extendedProfile' );

		//-----------------------------------------
		// Show...
		//-----------------------------------------
		
		return array( 'tabs' => $html->acp_member_form_tabs( $member ), 'content' => $html->acp_member_form_main( $member ) );
	}
	
	/**
	 * Process the entries for saving and return
	 *
	 * @return	@e array Multi-dimensional array (core, extendedProfile) for saving
	 */
	public function getForSave()
	{
		$return = array( 'core' => array(), 'extendedProfile' => array() );
		
		$return['core']['chat_banned'] = intval(ipsRegistry::$request['chat_banned']);

		return $return;
	}
}