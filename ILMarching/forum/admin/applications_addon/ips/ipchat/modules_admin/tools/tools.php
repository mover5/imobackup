<?php

/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.0.4
 * Chat default section
 * Last Updated: $LastChangedDate: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Chat
 * @link		http://www.invisionpower.com
 * @since		17 February 2003
 * @version		$Revision: 10721 $
 */

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}


class admin_ipchat_tools_tools extends ipsCommand
{
	/**
	 * How many members per cycle
	 *
	 * @var		int
	 */
	public $perPage			= 500;

	/**
	 * Main class entry point
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void		[Outputs to screen]
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		//-----------------------------------------
		// Load HTML
		//-----------------------------------------
		
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_chat' );
		
		$this->registry->class_localization->loadLanguageFile( array( 'admin_chat' ) );
		
		//-----------------------------------------
		// Set up stuff
		//-----------------------------------------
		
		$this->form_code	= $this->html->form_code	= 'module=tools&amp;section=tools';
		$this->form_code_js	= $this->html->form_code_js	= 'module=tools&section=tools';
		
		switch($this->request['do'])
		{
			case 'ignored':
				$this->convertIgnored();
			break;
			
			default:
				$this->overview();
			break;
		}
		
		//-----------------------------------------
		// Pass to CP output hander
		//-----------------------------------------
		
		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}
	
	/**
	 * Convert ignored chat users
	 *
	 * @return	@e void
	 */
	public function convertIgnored()
	{
		/* Init */
		$st		= intval($this->request['st']);
		$did	= 0;

		/* Find chat ignored users */
		$this->DB->build( array( 'select' => 'member_id, members_cache', 'from' => 'members', 'order' => 'member_id ASC', 'limit' => array( $st, $this->perPage ) ) );
		$outer	= $this->DB->execute();
		
		while( $r = $this->DB->fetch($outer) )
		{
			$did++;
			
			/* Unpack cache */
			$_cache	= unserialize( $r['members_cache'] );
			
			/* Now look for ignored users in chat */
			if( is_array($_cache['ignore_chat']) AND count($_cache['ignore_chat']) )
			{
				foreach( $_cache['ignore_chat'] as $_mid )
				{
					/* Are we already 'ignoring' this user for other reasons? */
					$_check	= $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'ignored_users', 'where' => "ignore_owner_id=" . $r['member_id'] . ' AND ignore_ignore_id=' . $_mid ) );
					
					/* If yes, then update record to also ignore chat */
					if( $_check['ignore_id'] )
					{
						$this->DB->update( 'ignored_users', array( 'ignore_chats' => 1 ), 'ignore_id=' . $_check['ignore_id'] );
					}
					/* If no, then insert new ignore record for this user */
					else
					{
						$this->DB->insert( 'ignored_users', array( 'ignore_chats' => 1, 'ignore_owner_id' => $r['member_id'], 'ignore_ignore_id' => $_mid ) );
					}
				}

				/* Rebuild cache */
				IPSMember::rebuildIgnoredUsersCache( $r );
				
				/* Clean up members_cache */
				unset( $_cache['ignore_chat'] );
				$_cache	= serialize( $_cache );
				
				$this->DB->update( 'members', array( 'members_cache' => $_cache ), 'member_id=' . $r['member_id'] );
			}
		}
		
		if( $did > 0 )
		{
			$next	= $st + $this->perPage;
			
			$this->registry->output->html	.= $this->registry->output->global_template->temporaryRedirect( $this->settings['base_url'] . $this->form_code . '&do=ignored&st=' . $next, sprintf( $this->lang->words['upto_converted_sofar'], $next ) );
		}
		else
		{
			$this->registry->output->redirect( $this->settings['base_url'] . $this->form_code, $this->lang->words['all_converted_sofar'], 1 );
		}
	}
	
	/**
	 * Overview
	 *
	 * @return	@e void
	 */
	protected function overview()
	{
		$this->registry->output->html .= $this->html->toolsStart();
	}
}