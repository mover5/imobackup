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
		'key'	=> 'bbpress',
		'name'	=> 'bbPress 2.x (WordPress version)',
		'login'	=> true,
	);
	
	class admin_convert_board_bbpress extends ipsCommand
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
			// a note on this.. a fresh install of 2.3 bbpress will use wp_posts (where post_type='forum') 
			// but an upgrade seems to use wp_forum_forums etc..
			// So I think I will make two versions of the 'wordpress' version, one for new install and one for upgrade. We will see.
			switch ($action)
			{
				case 'members':
					//return $this->lib->countRows('wp_users');
					return $this->lib->countRows( 'users' );
					break;
					
				case 'forums':
					return $this->lib->countRows('forum_forums');
					break;
					
				case 'topics':
					return $this->lib->countRows('forum_threads');
					break;
				
				case 'posts':
					return $this->lib->countRows('forum_posts');
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
			return false;
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
			
			$main = array(	'select' 	=> '*',
							'from' 		=> 'forum_forums',
							'order'		=> 'id ASC',
						);
									
			$loop = $this->lib->load('forums', $main);
									
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save = array(
					'name'			=> $row['name'],
					'description'	=> $row['description'],
					//'position'		=> $row['views'],
					//'posts'			=> $row['posts'],
					//'topics'		=> $row['topics'],
					'parent_id'		=> ($row['parent_id']) ? $row['parent_id'] : -1,
					);
				
				$this->lib->convertForum($row['id'], $save, array());				
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
							'from' 		=> 'forum_threads',
							'order'		=> 'id ASC',
						);
			
			$loop = $this->lib->load('topics', $main);
						
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$author = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'display_name', 'from' => 'users', 'where' => "ID='{$row['starter']}'" ) );
				
				$save = array(
					'title'		 		=> $row['subject'],
					'starter_name'	 	=> $author['display_name'],	
					'starter_id'		=> $row['starter'],
					'forum_id'		 	=> $row['parent_id'],
					'state'				=> ($row['status'] == 'open') ? 'open' : ($row['closed'] == '1') ? 'closed' : 'open',
					'approved'			=> 1,
					'pinned'			=> ($row['status'] == 'sticky') ? '1' : 0,                   
					);
				
				$this->lib->convertTopic($row['id'], $save);
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
				
			$this->lib->useKey( 'id' );

			//---------------------------
			// Set up
			//---------------------------
			
			$main = array(	'select' 	=> '*',
							'from' 		=> 'forum_posts',
							'order'		=> 'id ASC',
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
					'author_id'			=> $row['author_id'],
					'topic_id'			=> $row['parent_id'],
					'post'				=> $this->fixPostData( $row['text'] ),
					'post_date'			=> strtotime( $row['date'] ),
					);
				
				$this->lib->convertPost($row['id'], $save);
				$this->lib->setLastKeyValue( $row['id'] );
			}

			$this->lib->next();
						
		}				
	}
	
