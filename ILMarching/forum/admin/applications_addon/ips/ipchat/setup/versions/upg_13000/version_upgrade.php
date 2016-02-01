<?php
/*
+--------------------------------------------------------------------------
|   IP.Board v3.4.5
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class version_upgrade
{
	/**
	 * Custom HTML to show
	 *
	 * @var		string
	 */
	protected $_output = '';
	
	/**
	 * fetchs output
	 * 
	 * @return	string
	 */
	public function fetchOutput()
	{
		return $this->_output;
	}
	
	/**
	 * Execute selected method
	 *
	 * @param	object		Registry object
	 * @return	@e void
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		/* Make object */
		$this->registry =  $registry;
		$this->DB       =  $this->registry->DB();
		$this->settings =& $this->registry->fetchSettings();
		$this->request  =& $this->registry->fetchRequest();
		$this->cache    =  $this->registry->cache();
		$this->caches   =& $this->registry->cache()->fetchCaches();
		
		//--------------------------------
		// What are we doing?
		//--------------------------------

		switch( $this->request['workact'] )
		{
			default:
			case 'ignored':
				$this->convertIgnored();
				break;
		}
		
		/* Workact is set in the function, so if it has not been set, then we're done. The last function should unset it. */
		if ( $this->request['workact'] )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Convert ignored users
	 * 
	 * @param	int
	 * @return	@e void
	 */
	public function convertIgnored()
	{
		/* Are we skipping this step? */
		$options	= IPSSetUp::getSavedData('custom_options');
		$_skip		= $options['ipchat'][13000]['skipIgnoredUsers'];
		
		if( $_skip )
		{
			$this->registry->output->addMessage( "Skipping chat ignored users conversion..." );
			
			$this->request['st']		= 0;
			$this->request['workact']	= '';
			return;
		}
		
		/* Init */
		$st		= intval($this->request['st']);
		$did	= 0;
		$each	= 500;

		/* Find chat ignored users */
		$this->DB->build( array( 'select' => 'member_id, members_cache', 'from' => 'members', 'order' => 'member_id ASC', 'limit' => array( $st, $each ) ) );
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

		/* Show message and redirect */
		if( $did > 0 )
		{
			$this->request['st']		= $st + $did;
			$this->request['workact']	= 'ignored';
			
			$this->registry->output->addMessage( "Up to {$this->request['st']} members checked for ignored chat users..." );
		}
		else
		{
			$this->request['st']		= 0;
			$this->request['workact']	= '';
			
			$this->registry->output->addMessage( "All ignored chat users converted..." );
		}

		/* Next Page */
		return;
	}
}