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

class public_ipchat_ajax_block extends ipsAjaxCommand
{
	/**
	 * Main class entry point
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void		[Outputs to screen]
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		//-----------------------------------------
		// Got sess ID and mem ID?
		//-----------------------------------------
		
		if ( ! $this->member->getProperty('member_id') )
		{
			$this->returnString( "no" );
		}
		
		//-----------------------------------------
		// Get data
		//-----------------------------------------
		
		$block	= $this->request['block'] ? true : false;
		$id		= intval($this->request['id']);
		
		if( !$id )
		{
			$this->returnString( "no" );
		}
		
		//-----------------------------------------
		// Get member record and verify we can ignore
		//-----------------------------------------
		
		$member = IPSMember::load( $id, 'core' );
		
		if ( $member['_canBeIgnored'] !== TRUE )
		{
			$this->returnString( "no" );
	 	}
		
		//-----------------------------------------
		// Get ignore record
		//-----------------------------------------
		
		$_ignore	= $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'ignored_users', 'where' => "ignore_owner_id=" . $this->memberData['member_id'] . ' AND ignore_ignore_id=' . $id ) );
		
		if( $_ignore['ignore_id'] )
		{
			$this->DB->update( 'ignored_users', array( 'ignore_chats' => $block ? 1 : 0 ), 'ignore_id=' . $_ignore['ignore_id'] );
		}
		else
		{
			$_ignore	= array(
								'ignore_owner_id'	=> $this->memberData['member_id'],
								'ignore_ignore_id'	=> $id,
								'ignore_chats'		=> 1,
								);

			$this->DB->insert( 'ignored_users', $_ignore );
		}

		//-----------------------------------------
		// Update cache
		//-----------------------------------------
		
		IPSMember::rebuildIgnoredUsersCache( $this->memberData );
		
		//-----------------------------------------
		// Something to return
		//-----------------------------------------
		
		$this->returnString( "ok" );
	}
}