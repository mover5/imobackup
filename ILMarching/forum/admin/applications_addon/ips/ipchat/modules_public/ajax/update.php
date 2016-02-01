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

class public_ipchat_ajax_update extends ipsAjaxCommand
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
		// Two hours of not doing anything...
		//-----------------------------------------
		
		if ( $this->memberData['last_activity'] < ( time() - 7200 ) )
		{
			/* @link http://community.invisionpower.com/tracker/issue-36045-user-active-in-chat-not-correct */
			//$this->returnString( "no" );
		}
		
		$tmp_cache = $this->cache->getCache('chatting');
		$new_cache = array();
		
		//-----------------------------------------
		// Goforit
		//-----------------------------------------

		if ( is_array( $tmp_cache ) and count( $tmp_cache ) )
		{
			foreach( $tmp_cache as $data )
			{
				//-----------------------------------------
				// This us?
				//-----------------------------------------
				
				if ( $data['member_id'] == $this->memberData['member_id'] )
				{
					$data['updated']	= time();
				}
				
				//-----------------------------------------
				// Not hit in 2 mins?
				//-----------------------------------------
				
				if ( $data['updated'] < ( time() - 120 ) )
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

		//-----------------------------------------
		// Update cache
		//-----------------------------------------
														  
		$this->cache->setCache( 'chatting', $new_cache, array( 'donow' => 1, 'array' => 1 ) );
		
		//-----------------------------------------
		// Something to return
		//-----------------------------------------
		
		$this->returnString( "ok" );
	}
}