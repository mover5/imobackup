<?php
/**
 * IPS Converters
 * IP.Board 3.0 Converters
 * bbPress
 * Last Update: $Date: 2010-07-22 11:29:06 +0200(gio, 22 lug 2010) $
 * Last Updated By: $Author: terabyte $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 447 $
 */

	$info = array(
		'key'	=> 'bbpress23',
		'name'	=> 'bbPress 2.3 (WordPress version)',
		'login'	=> true,
	);
	
	class admin_convert_board_bbpress23 extends ipsCommand
	{
		/*
	    * Main class entry point
	    *
	    * @access	public
	    * @param	object		ipsRegistry
	    * @return	void
	    */
	    public function doExecute( ipsRegistry $registry )
		{
			//-----------------------------------------
			// What can this thing do?
			//-----------------------------------------
			
			// array('action' => array('action that must be completed first'))
			$this->actions = array(
				'members'		=> array(),
				'forums'		=> array(),
				'topics'		=> array('forums'),
				'posts'			=> array('topics')
				);
					
			//-----------------------------------------
	        // Load our libraries
	        //-----------------------------------------
			
			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_master.php' );
			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_board.php' );
			$this->lib =  new lib_board( $registry, $html, $this );
	
	        $this->html = $this->lib->loadInterface();
			$this->lib->sendHeader( 'bbPress &rarr; IP.Board Converter' );
	
			//-----------------------------------------
			// Are we connected?
			// (in the great circle of life...)
			//-----------------------------------------
			
			$this->HB = $this->lib->connect();

			//-----------------------------------------
			// What are we doing?
			//-----------------------------------------
			
			if (array_key_exists($this->request['do'], $this->actions))
			{
				call_user_func(array($this, 'convert_'.$this->request['do']));
			}
			else
			{
				$this->lib->menu();
			}

			//-----------------------------------------
	        // Pass to CP output hander
	        //-----------------------------------------

			$this->sendOutput();

		}
		
		/**
	    * Output to screen and exit
	    *
	    * @access	private
	    * @return	void
	    */
		private function sendOutput()
		{
			$this->registry->output->html .= $this->html->convertFooter();
			$this->registry->output->html_main .= $this->registry->output->global_template->global_frame_wrapper();
			$this->registry->output->sendOutput();
			exit;
		}
				
		/**
		 * Count rows
		 *
		 * @access	private
		 * @param 	string		action (e.g. 'members', 'forums', etc.)
		 * @return 	integer 	number of entries
		 **/
		public function countRows($action)
		{

			switch ($action)
			{
				case 'members':
					return $this->lib->countRows( 'users' );
					break;
					
				case 'forums':
					return $this->lib->countRows('posts', "post_type = 'forum'");
					break;
					
				case 'topics':
					return $this->lib->countRows('posts', "post_type = 'topic'");
					break;
				
				case 'posts':
					return $this->lib->countRows('posts', "post_type IN('reply', 'topic')");
					break;
				default:
					return $this->lib->countRows($action);
					break;
			}
		}
		
		/**
		 * Check if section has configuration options
		 *
		 * @access	private
		 * @param 	string		action (e.g. 'members', 'forums', etc.)
		 * @return 	boolean
		 **/
		public function checkConf($action)
		{
			switch( $action )
			{
				case 'forums':
					return true;
				break;
				
				default:
					return false;
				break;
			}
		}
		
		/**
		 * Fix post data
		 *
		 * @access	private
		 * @param 	string		raw post data
		 * @return 	string		parsed post data
		 **/
		private function fixPostData($post)
		{
			return $post;
		}
		
		/**
		 * Convert members
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_members()
		{

			//---------------------------
			// Set up
			//---------------------------
			
			$main = array(	'select' 	=> '*',
							'from' 		=> 'users',
							'order'		=> 'ID ASC',
						);
						
			$loop = $this->lib->load('members', $main);
			
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$info = array(
					'id'				=> $row['ID'],
					'group'				=> $INFO['member_group'],
					'joined'			=> strtotime( $row['user_registered'] ),
					'username'			=> $row['user_login'],
					'displayname'		=> $row['display_name'],
					'email'				=> $row['user_email'],
					'password'			=> $row['user_pass'],
					);
					
				$this->lib->convertMember($info, array(), array(), array(), '');
			}
			
			$this->lib->next();

		}
		
		/**
		 * Convert Forums
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_forums()
		{
			//---------------------------
			// Set up
			//---------------------------
			$this->lib->saveMoreInfo( 'forums', array( 'no_parent_cat' ) );
			
			$main = array(	'select' 	=> '*',
							'from' 		=> 'posts',
							'where'		=> "post_type='forum'",
							'order'		=> 'ID ASC',
						);
									
			$loop = $this->lib->load('forums', $main);
			
			// Get our current categories
			$cats = array();
			foreach( $this->DB->buildAndFetchAll( array( 'select' => '*', 'from' => 'forums', 'where' => "parent_id = '-1'" ), 'id' ) AS $k => $v )
			{
				$cats[$k] = $v['name'];
			}
			
			$get = unserialize( $this->settings['conv_extra'] );
			$us = $get[$this->lib->app['name']];
			
			$this->lib->getMoreInfo( 'forums', $loop, array( 'no_parent_cat' => array(
				'type'		=> 'dropdown',
				'label'		=> 'Where should forums that have no parent categories be stored? (Note: You MUST have at least one category configured)',
				'options'	=> $cats,
			) ) );
									
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				// Do we have any children?
				$skip_global_link = false;
				if ( ! $row['post_parent'] )
				{
					$children = ipsRegistry::DB('hb')->buildAndFetchAll( array( 'select' => '*', 'from' => 'posts', 'where' => "post_parent = {$row['ID']} AND post_type = 'forum'" ) );
					if ( count( $children ) )
					{
						// We have children.
						$skip_global_link = true;
						$parent = -1;
					}
					else
					{
						// No - assign to chosen category.
						$skip_global_link = true;
						$parent = $us['no_parent_cat'];
					}
				}
				else
				{
					$parent = $row['post_parent'];
				}
				$save = array(
					'name'			=> $row['post_title'],
					'description'	=> $row['post_content'],
					'parent_id'		=> $parent,
					);
				
				$this->lib->convertForum($row['ID'], $save, array(), $skip_global_link);
			}

			$this->lib->next();
						
		}
		
		/**
		 * Convert Topics
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_topics()
		{
			//---------------------------
			// Set up
			//---------------------------
			
			$main = array(	'select' 	=> '*',
							'from' 		=> 'posts',
							'where'		=> "post_type='topic'",
							'order'		=> 'ID ASC',
						);
			
			$loop = $this->lib->load('topics', $main);
						
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$author = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'display_name', 'from' => 'users', 'where' => "ID='{$row['starter']}'" ) );
				
				$save = array(
					'title'		 		=> $row['post_title'],
					'starter_id'		=> $row['post_author'],
					'forum_id'		 	=> $row['post_parent'],
					'state'				=> ($row['post_status'] == 'publish') ? 'open' : 'closed',
					'approved'			=> 1,
					);
				
				$this->lib->convertTopic($row['ID'], $save);
			}
			
			$this->lib->next();
						
		}
		
		/**
		 * Convert Posts
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_posts()
		{
			//---------------------------
			// Primary Key
			//---------------------------
				
			$this->lib->useKey( 'ID' );

			//---------------------------
			// Set up
			//---------------------------
			
			$main = array(	'select' 	=> '*',
							'from' 		=> 'posts',
							'where'		=> "post_type IN('reply', 'topic')",
							'order'		=> 'ID ASC',
						);
			
			$loop = $this->lib->load('posts', $main);
						
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				// We need to get some info
				$author = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'display_name', 'from' => 'users', 'where' => "ID='{$row['author_id']}'" ) );

				$save = array(
					'author_name'	 	=> $author['display_name'],
					'author_id'			=> $row['post_author'],
					'topic_id'			=> ( $row['post_type'] == 'topic' ) ? $row['ID'] : $row['post_parent'],
					'post'				=> $this->fixPostData( $row['post_content'] ),
					'post_date'			=> strtotime( $row['post_date'] ),
					);
				
				$this->lib->convertPost($row['ID'], $save);
				$this->lib->setLastKeyValue( $row['ID'] );
			}

			$this->lib->next();
						
		}
	}
	
