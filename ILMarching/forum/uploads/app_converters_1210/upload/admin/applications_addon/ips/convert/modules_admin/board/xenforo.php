<?php
/**
 * IPS Converters
 * IP.Board 3.0 Converters
 * XenForo
 * Last Updated By: $Author: AndyMillne $
 *
 * @package		IPS Converters
 * @author 		Andrew Millne
 * @copyright	(c) 2011 Invision Power Services, Inc.
 *
 * @todo forum permissions
 */


	$info = array(
		'key'	=> 'xenforo',
		'name'	=> 'XenForo',
		'login'	=> true,
		'merge' => true
	);

	class admin_convert_board_xenforo extends ipsCommand
	{
		
		/**
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
				'emoticons'					=> array(),
				'pfields'					=> array(),
				'forum_perms'				=> array(),
				'groups'					=> array('forum_perms'),
				'members'					=> array('groups', 'pfields'),
				'profile_comments'			=> array ( 'members' ),
				'profile_comment_replies'	=> array ( 'members', 'profile_comments' ),
				'forums'					=> array(),
				'topics'					=> array('forums'),
				//'tags'					=> array('topics', 'members'),
				'posts'						=> array('topics', 'emoticons'),
				'reputation_index'			=> array('posts'),
				'pms'						=> array('members', 'emoticons'),
				'attachments'				=> array('posts'),
				);

			//-----------------------------------------
	        // Load our libraries
	        //-----------------------------------------

			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_master.php' );
			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_board.php' );
			$this->lib =  new lib_board( $registry, $html, $this );

	        $this->html = $this->lib->loadInterface();
			$this->lib->sendHeader( 'XenForo &rarr; IP.Board Converter' );

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
					return $this->lib->countRows('user');
					break;

				case 'groups':
				case 'forum_perms':
					return $this->lib->countRows('user_group');
					break;

				case 'forums':
					return $this->lib->countRows('node', "node_type_id = 'Category' OR node_type_id = 'Forum'");
					break;

				case 'topics':
					return $this->lib->countRows('thread');
					break;

				case 'posts':
					return $this->lib->countRows('post');
					break;

				case 'attachments':
					return $this->lib->countRows('attachment');
					break;

				case 'emoticons':
					return $this->lib->countRows('smilie');
					break;

				case 'profile_comments':
					return $this->lib->countRows('profile_post');
					break;
				
				case 'profile_comment_replies':
					return $this->lib->countRows('profile_post_comment');
					break;
				
				case 'reputation_index':
					return $this->lib->countRows('liked_content', "content_type='post'" );
					break;

				case 'pms':
					return $this->lib->countRows('conversation_master');
					break;

				case 'pfields':
					return  $this->lib->countRows('user_field');
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
			switch ($action)
			{
				case 'members':
				case 'groups':
				case 'forum_perms':
				case 'emoticons':
				case 'attachments':
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
		private function fixPostData( $post )
		{
			// run everything through htmlspecialchars to prevent XSS ( @see http://community.invisionpower.com/resources/bugs.html/_/ips-extras/converters/possible-xf-converter-xss-vector-r38108 )
			$post = IPSText::htmlspecialchars($post);
			// fix newlines
			$post = nl2br($post);
			// find YouTube ID's and replace.
			$post = preg_replace('#\[media=youtube\](.+?)\[/media\]#i', '[media]http://www.youtube.com/watch?v=$1[/media]', $post);
			// finally, give us the post back.
			return $post;
		}

		/**
		 * Convert forum permissions
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_forum_perms()
		{
			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$this->lib->saveMoreInfo('forum_perms', 'map');

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'user_group',
							'order'		=> 'user_group_id ASC',
						);

			$loop = $this->lib->load( 'forum_perms', $main, array(), array(), TRUE );

			//-----------------------------------------
			// We need to know how to map these
			//-----------------------------------------

			$this->lib->getMoreInfo('forum_perms', $loop, array('new' => '--Create new set--', 'ot' => 'Old permission set', 'nt' => 'New permission set'), '', array('idf' => 'user_group_id', 'nf' => 'title'));

			//---------------------------
			// Loop
			//---------------------------

			foreach( $loop as $row )
			{
				$this->lib->convertPermSet($row['user_group_id'], $row['title']);
			}

			$this->lib->next();

		}

		/**
		 * Convert groups
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_groups()
		{
			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$this->lib->saveMoreInfo('groups', 'map');

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'user_group',
							'order'		=> 'user_group_id ASC',
						);

			$loop = $this->lib->load( 'groups', $main, array(), array(), TRUE );

			//-----------------------------------------
			// We need to know how to map these
			//-----------------------------------------

			$this->lib->getMoreInfo('groups', $loop, array('new' => '--Create new group--', 'ot' => 'Old group', 'nt' => 'New group'), '', array('idf' => 'user_group_id', 'nf' => 'title'));

			//---------------------------
			// Loop
			//---------------------------

			foreach( $loop as $row )
			{

				$save = array(
					'g_title'				=> $row['title'],
					'g_mem_info'			=> 1,
					'g_invite_friend'		=> 1,
					'g_perm_id'				=> $row['user_group_id'],
					);
					
				//-----------------------------------------
				// Handle group settings
				//-----------------------------------------
					
				ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'permission_entry', 'where' => "user_group_id={$row['user_group_id']} AND permission_group_id IN ('general', 'forum', 'conversation')"));
				$subRes = ipsRegistry::DB('hb')->execute();
				while ($settings = ipsRegistry::DB('hb')->fetch($subRes))
				{
					switch($settings['permission_id']) {
						
						case 'view':
							$save['g_view_board'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							break;
		
						case 'deleteOwnPost' :
							$save['g_delete_own_posts'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							break;
		
						case 'editOwnPost':
							$save['g_edit_posts'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							break;
							
						case 'postThread':
							$save['g_post_new_topics'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							$save['g_post_polls'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							break;

						case 'postReply':
							$save['g_reply_other_topics'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							$save['g_reply_own_topics'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							break;

						case 'votePoll':
							$save['g_vote_polls'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							break;

						case 'deleteOwnThread':
							$save['g_delete_own_topics'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							break;

						case 'maxRecipients':
							$save['g_max_mass_pm'] = $settings['permission_value_int'];
							break;

						case 'bypassFloodCheck':
							$save['g_avoid_flood'] = ($settings['permission_value'] == 'allow') ? 1 : 0;
							break;
					}
					
				}
					
				$this->lib->convertGroup($row['user_group_id'], $save);
			}

			$this->lib->next();

		}

		/**
		 * Convert Members
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_members()
		{
			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$pcpf = array(
				'homepage'		=> 'Website',
				'skype'			=> 'Skype',
				'icq'			=> 'ICQ Number',
				'aim'			=> 'AIM ID',
				'yahoo'			=> 'Yahoo ID',
				'msn'			=> 'MSN ID',
				);

			$this->lib->saveMoreInfo( 'members', array_merge( array_keys($pcpf), array( 'pp_path' ) ) );

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> 'u.*',
							'from' 		=> array('user' => 'u'),
							'order'		=> 'u.user_id ASC',
							'add_join'	=> array(
											array( 	'select' => 'p.*',
													'from'   =>	array( 'user_profile' => 'p' ),
													'where'  => "u.user_id = p.user_id",
													'type'   => 'left'
												),
											array( 	'select' => 'a.*',
													'from'   =>	array( 'user_authenticate' => 'a' ),
													'where'  => "u.user_id = a.user_id",
													'type'   => 'left'
												),
											/**array(
													'select' => 'pf.*',
													'from'   => array('user_field_value' => 'pf'),
													'where'  => 'u.user_id = pf.user_id',
													'type'   => 'left',
												),**/
											),
						);


			$loop = $this->lib->load('members', $main);

			//-----------------------------------------
			// Tell me what you know!
			//-----------------------------------------

			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			$ask = array();

			// We need to know the avatars path
			$ask['pp_path'] = array('type' => 'text', 'label' => 'The path to the folder where custom avatars are saved (no trailing slash - usually /path_to_xf/data/avatars):');
			
			// And those custom profile fields
			$options = array('x' => '-Skip-');
			$this->DB->build(array('select' => '*', 'from' => 'pfields_data'));
			$fieldRes = $this->DB->execute();
			
			while ($row = $this->DB->fetch($fieldRes))
			{
				$options[$row['pf_id']] = $row['pf_title'];
			}
							
			
			foreach ($pcpf as $id => $name)
			{
				$ask[$id] = array('type' => 'dropdown', 'label' => 'Custom profile field to store '.$name.': ', 'options' => $options, 'extra' => $extra );
			}


			$this->lib->getMoreInfo('members', $loop, $ask, 'path');
			
			// Get our Custom Profile Fields (plz)

			if (isset($us['pfield_group']))
			{
				$this->DB->build(array('select' => '*', 'from' => 'pfields_data', 'where' => 'pf_group_id='.$us['pfield_group']));
				$this->DB->execute();
				$pfields = array();
				while ($row = $this->DB->fetch())
				{
					$pfields[] = $row;
				}
			}
			else
			{
				$pfields = array();
			}
						
			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				//-----------------------------------------
				// Set info
				//-----------------------------------------
				
				// Identities
				if ($row['identities']) {
					$row = array_merge(unserialize($row['identities']), $row);
				}

				// Password
				$password = unserialize($row['data']);
			
				// Basic info
				$info = array(
								'id'             	=> $row['user_id'],
								'username'     	 	=> IPSText::htmlspecialchars($row['username']),
								'email'			 	=> $row['email'],
								'group'			 	=> $row['user_group_id'],
								'secondary_groups'	=> $row['secondary_group_ids'],
								'joined'			=> $row['register_date'],
								'password'			=> $password['hash'],
								);

				$members = array(
								'title'				=> strip_tags($row['custom_title']),
								'last_visit'		=> $row['last_activity'],
								'last_activity'		=> $row['last_activity'],
								'posts'				=> $row['message_count'],
								'bday_day'			=> $row['dob_day'],
								'bday_month'		=> $row['dob_month'],
								'bday_year'			=> $row['dob_year'],
								'misc'				=> $password['salt'],
								'member_banned'		=> $row['is_banned'],
								'temp_ban'			=> '',
								);

				// Profile
				$profile = array(
								'signature'					=>	$this->fixPostData($row['signature']),
								'pp_setting_count_friends'	=>	1,
								'pp_setting_count_comments'	=>	1,
								'pp_main_photo'				=>	'',
								'pp_main_height'			=>	'',
								'pp_main_width'				=>	'',
								'pp_about_me'				=>	$row['about'],
								);



				//-----------------------------------------
				// Custom Profile fields
				//-----------------------------------------

				// Pseudo
				foreach ($pcpf as $id => $name)
				{
					if ($us[$id] != 'x')
					{
						$custom['field_'.$us[$id]] = $row[$id];
					}
				}
				
				// Array
				$row['custom_fields_array'] = unserialize($row['custom_fields']);
				foreach ($pfields as $field)
				{
					$custom['field_'.$field['pf_id']] = $row['custom_fields_array'][$field['pf_key']];
				}
				
				//-----------------------------------------
				// Avatars
				//-----------------------------------------
				
				$group = floor($row['user_id'] / 1000);

				if ( file_exists( $us['pp_path'] . "/l/{$group}/{$row['user_id']}.jpg" ) ) {
					$profile['photo_type'] = 'custom';

					$profile['pp_main_photo'] = "l/{$group}/{$row['user_id']}.jpg";
					$profile['pp_main_width'] = $row['avatar_width'];
					$profile['pp_main_height'] = $row['avatar_height'];
					$profile['photo_filesize'] = filesize( $us['pp_path'] . "/l/{$group}/{$row['user_id']}.jpg" );
				}

				//-----------------------------------------
				// Go
				//-----------------------------------------
				
				$this->lib->convertMember($info, $members, $profile, $custom, $us['pp_path']);

			}

			$this->lib->next();

		}

		/**
		 * Convert Profile Comments
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_profile_comments ( )
		{
			$main = array (
				'select'	=> '*',
				'from'		=> 'profile_post',
				'order'		=> 'profile_post_id ASC',
			);
			
			$loop = $this->lib->load ( 'profile_comments', $main );
			
			while ( $row = ipsRegistry::DB ( 'hb' )->fetch ( $this->lib->queryRes ) )
			{
				$save = array (
					'status_member_id'	=> $row['profile_user_id'],
					'status_author_id'	=> $row['user_id'],
					'status_date'		=> $row['post_date'],
					'status_content'	=> $this->fixPostData ( $row['message'] ),
					'status_approved'	=> ( $row['message_state'] == 'visible' ? 1 : 0 ),
				);
				
				$this->lib->convertProfileComment ( $row['profile_post_id'], $save );
			}
			
			$this->lib->next ( );
		}
		
		/**
		 * Convert Profile Comment Replies
		 *
		 * @access private
		 * @return void
		 */
		private function convert_profile_comment_replies ( )
		{
			$main = array (
				'select'	=> '*',
				'from'		=> 'profile_post_comment',
				'order'		=> 'profile_post_comment_id ASC',
			);
			
			$loop = $this->lib->load ( 'profile_comment_replies', $main );
			
			while ( $row = ipsRegistry::DB ( 'hb' )->fetch ( $this->lib->queryRes ) )
			{
				$save = array (
					'reply_status_id'	=> $row['profile_post_id'],
					'reply_member_id'	=> $row['user_id'],
					'reply_date'		=> $row['comment_date'],
					'reply_content'		=> $row['message'],
				);
				
				$this->lib->convertProfileCommentReply ( $row['profile_post_comment_id'], $save );
			}
			
			$this->lib->next ( );
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

			$main = array(	'select' 	=> 'n.node_id AS node, n.title, n.description, n.parent_node_id',
							'from' 		=> array('node' => 'n'),
							'order'		=> 'n.node_id ASC',
							'where'		=> "node_type_id = 'Category' OR node_type_id = 'Forum'",
							'add_join'	=> array(
											array( 	'select' => 'f.*',
													'from'   =>	array( 'forum' => 'f' ),
													'where'  => "n.node_id = f.node_id",
													'type'   => 'left'
												),
											
											),
						);

			$loop = $this->lib->load('forums', $main);

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				//-----------------------------------------
				// Work stuff out
				//-----------------------------------------

				// Permissions will need to be reconfigured
				$perms = array();

				//-----------------------------------------
				// Save
				//-----------------------------------------

				$save = array(
					'topics'			=> $row['discussion_count'],
					'posts'			  	=> $row['message_count'],
					'last_post'		  	=> $row['last_post_date'],
					'last_poster_name'	=> $row['last_post_user_id'],
					'parent_id'		  	=> ($row['parent_node_id']) ? $row['parent_node_id'] : -1,
					'name'			  	=> IPSText::htmlspecialchars($row['title']),
					'description'	  	=> $row['description'],
					'position'		  	=> $row['display_order'],
					'inc_postcount'	  	=> 1,
					'preview_posts'		=> $row['moderate_messages'],
					);

				$this->lib->convertForum($row['node'], $save, $perms);

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
			$this->lib->useKey('thread_id');
			
			//---------------------------
			// Set up
			//---------------------------
			$main = array( 'select' => '*',
						   'from'   => 'thread',
						   'order'  => 'thread_id ASC' );

			$loop = $this->lib->load('topics', $main);

			$this->lib->prepareDeletionLog('topics');

			//---------------------------
			// Loop
			//---------------------------
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				
				$save = array( 'title'			  => $row['title'],
							   'state'			  => ($row['discussion_open'] == 1) ? 'open' : 'closed',
							   'posts'			  => $row['reply_count'],
							   'starter_id'		  => $row['user_id'],
							   'starter_name'	  => $row['username'],
							   'start_date'		  => $row['post_date'],
							   'last_post'		  => $row['last_post_date'],
							   'last_poster_name' => $row['last_post_username'],
							   'views'			  => $row['view_count'],
							   'forum_id'		  => $row['node_id'],
							   'approved'		  => ( $row['discussion_state'] == 'visible' ) ? 1 : 0,
							   'pinned'			  => $row['sticky'],
							   'topic_hasattach'  => 0,
							  );

				$this->lib->convertTopic($row['thread_id'], $save);
				$this->lib->setLastKeyValue($row['thread_id']);

				//-----------------------------------------
				// Handle subscriptions
				//-----------------------------------------

				ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'liked_content', 'where' => "content_id={$row['first_post_id']} AND content_type='post'"));
				$subRes = ipsRegistry::DB('hb')->execute();
				while ($tracker = ipsRegistry::DB('hb')->fetch($subRes))
				{
					$savetracker = array(
						'member_id'	=> $tracker['like_user_id'],
						'topic_id'	=> $row['thread_id'],
						'topic_track_type' => 'immediate',
						);
					$this->lib->convertTopicSubscription($tracker['like_id'], $savetracker);
				}

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
			$this->lib->useKey('p.post_id');
			
			//---------------------------
			// Set up
			//---------------------------
			$main = array(	'select' 	=> 'p.*',
							'from' 		=> array('post' => 'p'),
							'order'		=> 'p.post_id ASC',
							'add_join'	=> array(
											array( 	'select' => 'i.ip as ip_address, i.ip_id',
													'from'   =>	array( 'ip' => 'i' ),
													'where'  => "p.ip_id = i.ip_id",
													'type'   => 'left'
												),
											
											),
						);
			
			$loop = $this->lib->load('posts', $main);
		

			//---------------------------
			// Loop
			//---------------------------
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{

				//-----------------------------------------
				// Save
				//-----------------------------------------

				$save = array(
							   'author_id'		=> $row['user_id'],
							   'author_name' 	=> $row['username'],
							   'use_sig'     	=> 1,
							   'use_emo'     	=> 1,
							   'ip_address' 	=> long2ip($row['ip_address']),
							   'post_date'   	=> $row['post_date'],
							   'post'		 	=> $this->fixPostData($row['message']),
							   'queued'      	=> ($row['message_state']) == 'visible' ? 0 : 2,
							   'topic_id'    	=> $row['thread_id'],
							   );

				$this->lib->convertPost($row['post_id'], $save);
				$this->lib->setLastKeyValue($row['post_id']);
			}
			$this->lib->next();
		}
		
		/**
		 * Convert Post Reputations
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_reputation_index()
		{
			$this->lib->useKey('like_id');
			
			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'liked_content',
							'order'		=> 'like_id ASC',
							'where'		=> "content_type='post'"
						);

			$loop = $this->lib->load('reputation_index', $main);

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save = array(
					'member_id'	=> $row['like_user_id'],
					'app'		=> 'forums',
					'type'		=> 'pid',
					'type_id'	=> $row['content_id'],
					'rep_date'	=> $row['like_date'],
					'rep_msg'	=> '',
					'rep_rating'=> 1,
					'content_member_id' => $row['content_user_id'],
					);
					
				$this->lib->convertRep($row['like_id'], $save);
				$this->lib->setLastKeyValue($row['like_id']);
			}

			$this->lib->next();

		}


		/**
		 * Convert Emoticons
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_emoticons()
		{

			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$this->lib->saveMoreInfo('emoticons', array('emo_path', 'emo_opt'));

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'smilie',
							'order'		=> 'smilie_id ASC',
						);

			$loop = $this->lib->load('emoticons', $main);

			//-----------------------------------------
			// We need to know the path and how to handle duplicates
			//-----------------------------------------

			$this->lib->getMoreInfo('emoticons', $loop, array('emo_path' => array('type' => 'text', 'label' => 'The path to your XenForo installation (no trailing slash):'), 'emo_opt'  => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate emoticons?') ), 'path' );

			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			IPSLib::updateSettings(array('conv_extra' => serialize($get)));
			$path = $us['emo_path'];
	
			//-----------------------------------------
			// Check all is well
			//-----------------------------------------

			if (!is_writable(DOC_IPS_ROOT_PATH.'public/style_emoticons/'))
			{
				$this->lib->error('Your IP.Board emoticons path is not writeable. '.DOC_IPS_ROOT_PATH.'public/style_emoticons/');
			}
			if (!is_readable($path))
			{
				$this->lib->error('Your remote emoticons path is not readable.');
			}

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				// isolate first smiley only
				// @todo we could offer a choice of which code they want to use - this only uses the first.
				$smilies	= 	preg_split('#\r?\n#', $row['smilie_text'], 0);
				
				$save = array(
					'typed'		=>	$smilies[0],
					//'image'		=> 	preg_replace('#^(.+)?xenforo/smilies/(.+?)$#', '$2', $row['image_url']),
					'image'		=>	$row['image_url'], # @todo - fix so above works - changing to just "path to xf" for now.
					'clickable'	=>	0,
					'emo_set'	=>	'default',
					);

				$done = $this->lib->convertEmoticon($row['smilie_id'], $save, $us['emo_opt'], $path);
			}

			$this->lib->next();

		}

		/**
		 * Convert Attachments
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_attachments()
		{
			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$this->lib->saveMoreInfo('attachments', array('attach_path'));

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> 'a.*',
							'from' 		=> array('attachment' => 'a'),
							'order'		=> 'a.attachment_id ASC',
							'add_join'	=> array(
											array( 	'select' => 'd.*',
													'from'   =>	array( 'attachment_data' => 'd' ),
													'where'  => "a.data_id = d.data_id",
													'type'   => 'left'
												),
											
											),
							'where'		=> "content_type = 'post'",
						);

			$loop = $this->lib->load('attachments', $main);

			//-----------------------------------------
			// We need to know the path
			//-----------------------------------------

			$this->lib->getMoreInfo('attachments', $loop, array('attach_path' => array('type' => 'text', 'label' => 'The path to the folder where attachments are saved (no trailing slash, usually path_to_xf/internal_data/attachments):')), 'path');

			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			$path = $us['attach_path'];

			//-----------------------------------------
			// Check all is well
			//-----------------------------------------

			if (!is_writable($this->settings['upload_dir']))
			{
				$this->lib->error('Your IP.Board upload path is not writeable. '.$this->settings['upload_dir']);
			}

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				//-----------------------------------------
				// Init
				//-----------------------------------------

				// What's the extension?
				$e = explode('.', $row['filename']);
				$extension = array_pop( $e );
				
				// What's the mimetype?
				$type = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'attachments_type', 'where' => "atype_extension='{$extension}'" ) );

				// Is this an image?
				$image = false;
				if (preg_match('/image/', $type['atype_mimetype']))
				{
					$image = true;
				}
				
				// Need to grab the topic ID
				$topicid	= ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'thread_id', 'from' => 'post', 'where' => "post_id='" . intval( $row['content_id'] ) . "'" ) );

				$save = array(
					'attach_ext'			=> $extension,
					'attach_file'			=> $row['filename'],
					'attach_is_image'		=> $image,
					'attach_hits'			=> $row['view_count'],
					'attach_date'			=> $row['attach_date'],
					'attach_member_id'		=> $row['user_id'],
					'attach_filesize'		=> $filedata['file_size'],
					'attach_rel_id'			=> $row['content_id'],
					'attach_rel_module'		=> 'post',
					'attach_parent_id'		=>  $topicid['thread_id'],
					);


					$tmpPath = "/" . floor($row['data_id'] / 1000);
					$save['attach_location'] = "{$row['data_id']}-{$row['file_hash']}.data";

					$done = $this->lib->convertAttachment($row['attachment_id'], $save, $path . $tmpPath);


				//-----------------------------------------
				// Fix inline attachments
				//-----------------------------------------

				if ($done === true)
				{
					$aid = $this->lib->getLink($row['attachment_id'], 'attachments');
					$pid = $this->lib->getLink($save['attach_rel_id'], 'posts');

					if ( $pid )
					{
						$attachrow = $this->DB->buildAndFetch( array( 'select' => 'post', 'from' => 'posts', 'where' => "pid={$pid}" ) );

						$rawaid = $row['attachment_id'];
						$update = preg_replace("/\[ATTACH(.+?)\]".$rawaid."\[\/ATTACH\]/i", "[attachment={$aid}:{$save['attach_file']}]", $attachrow['post']);

						$this->DB->update('posts', array('post' => $update), "pid={$pid}");
					}
				}

			}

			$this->lib->next();

		}

		/**
		 * Convert PMs
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_pms()
		{
			// XenForo not ready to use these *just* yet
			$this->lib->useKey('conversation_id');
			
			//---------------------------
			// Set up
			//---------------------------
			$main = array( 'select' => '*',
							'from'  => 'conversation_master',
							'order' => 'conversation_id ASC' );

			$loop = $this->lib->load('pms', $main, array('pm_posts', 'pm_maps'));

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				// XenForo will convert to proper conversations.
				$posts = array();
				
				ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'conversation_message', 'where' => "conversation_id={$row['conversation_id']}"));
				$postRes = ipsRegistry::DB('hb')->execute();
				
				while ($msg = ipsRegistry::DB('hb')->fetch($postRes))
				{
					//-----------------------------------------
					// Post Data
					//-----------------------------------------
	
					$posts[ $msg['message_id'] ] = array(
						'msg_id'			=> $msg['message_id'],
						'msg_topic_id'      => $row['conversation_id'],
						'msg_date'          => $msg['message_date'],
						'msg_post'          => $this->fixPostData($msg['message']),
						'msg_post_key'      => md5(microtime()),
						'msg_author_id'     => $msg['user_id'],
						'msg_is_first_post' => ( $msg['message_id'] == $row['first_message_id'] ) ? 1 : 0
						);
				}

				//-----------------------------------------
				// Map Data
				//-----------------------------------------

				$maps = array();
				$_invited   = array();
				$recipient = array();

				ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'conversation_user', 'where' => "conversation_id={$row['conversation_id']}"));
				$pmRes = ipsRegistry::DB('hb')->execute();
				while ($to = ipsRegistry::DB('hb')->fetch($pmRes))
				{
					if (!$to['owner_user_id'])
					{
						break;
					}

					foreach ($maps as $map)
					{
						if ($map['map_user_id'] == $to['owner_user_id'] and $map['map_topic_id'] == $to['conversation_id'])
						{
							break 2;
						}
					}
					
					// Get our recipient information
					$recip = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'conversation_recipient', 'where' => 'conversation_id=' . $row['conversation_id'] . ' AND user_id=' . $to['owner_user_id'] ) );

					$maps[] = array(
						'map_user_id'     => $to['owner_user_id'],
						'map_topic_id'    => $row['conversation_id'],
						'map_folder_id'   => 'myconvo',
						'map_read_time'   => 0,
						'map_last_topic_reply' => $to['last_message_date'],
						'map_user_active' => ( $recip['recipient_state'] == 'active' ) ? 1 : 0,
						'map_user_banned' => 0,
						'map_has_unread'  => ( $recip['last_read_date'] < $to['last_message_date'] ) ? 1 : 0,
						'map_is_system'   => 0,
						'map_is_starter'  => ( $to['owner_user_id'] == $row['user_id'] ) ? 1 : 0,
						);

					if ( $to['owner_user_id'] != $row['user_id'] )
					{
						$_invited[ $to['owner_user_id'] ] = $to['owner_user_id'];
						$recipient[] = $to['owner_user_id'];
					}
				}

				//-----------------------------------------
				// Topic Data
				//-----------------------------------------


				$topic = array(
					'mt_id'			     => $row['conversation_id'],
					'mt_date'		     => $row['start_date'],
					'mt_title'		     => $row['title'],
					'mt_starter_id'	     => $row['user_id'],
					'mt_start_time'      => $row['start_date'],
					'mt_last_post_time'  => $row['last_message_date'],
					'mt_invited_members' => serialize( array_keys( $_invited ) ),
					'mt_to_count'		 => $row['recipient_count']-1,
					'mt_to_member_id'	 => array_shift($recipient),
					'mt_replies'		 => $row['reply_count'],
					'mt_is_draft'		 => 0,
					'mt_is_deleted'		 => ! $row['conversation_open'],
					'mt_is_system'		 => 0
					);

				//-----------------------------------------
				// Go
				//-----------------------------------------

				$this->lib->convertPM($topic, $posts, $maps);
				$this->lib->setLastKeyValue( $row['conversation_id'] );
			}

			$this->lib->next();

		}
		
		
		/**
		 * Convert custom profile fields
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_pfields()
		{
		
			//---------------------------
			// Set up
			//---------------------------
		
			$main = array(
							'select' 	=> '*',
							'from' 		=> 'user_field',
							'order'		=> 'field_id ASC',
						);
		
			$loop = $this->lib->load('pfields', $main, array('pfields_groups'));
		
			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			if (!$this->request['st'])
			{
				$us['pfield_group'] = null;
				IPSLib::updateSettings(array('conv_extra' => serialize($us)));
			}
		
			//-----------------------------------------
			// Do we have a group
			//-----------------------------------------
		
			if (!$us['pfield_group'])
			{
				$group = $this->lib->convertPFieldGroup(1, array('pf_group_name' => 'Converted', 'pf_group_key' => 'xenforo'), true);
				if (!$group)
				{
					$this->lib->error('There was a problem creating the profile field group');
				}
				$us['pfield_group'] = $group;
				$get[$this->lib->app['name']] = $us;
				IPSLib::updateSettings(array('conv_extra' => serialize($get)));
			}
		
			//---------------------------
			// Loop
			//---------------------------
		
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				// get data
				$data = array();
				
				if ($row['field_choices'])
				{
					$tmpData = unserialize($row['field_choices']);
					if ( is_array($tmpData) )
					{
						foreach ( $tmpData as $key => $value)
						{
							$data[] = "{$key}={$value}";
								
						}
					}
				}
				
				// What kind of field is this?
				$e = explode( "\n", $row['field_type'] );
		
				switch( $e[0] )
				{
					case 'textbox':
						$type = 'textarea';
						break;
					case 'select':
						$type = 'drop';
						break;
					case 'radio':
						$type = 'radio';
						break;
					default:
						$type = 'input';
						break;
				}
				
				// make our name make sense
				$name = str_replace('_', ' ', $row['field_id']);
				$name = ucwords($name);
				
				// Insert
				$save = array(
						'pf_title'			=> $name,
						//'pf_desc'			=> $row['description'],
						'pf_content'		=> implode('|', $data),
						'pf_type'			=> $type,
						'pf_not_null'		=> $row['required'],
						'pf_member_hide'	=> $row['viewable_profile'] == '1' ? 0 : 1,
						'pf_max_input'		=> $row['max_length'],
						'pf_member_edit'	=> $row['user_editable'],
						'pf_position'		=> $row['display_order'],
						'pf_show_on_reg'	=> $row['show_registration'],
						'pf_group_id'		=> $us['pfield_group'],
						'pf_key'			=> $row['field_id'],
				);
		
				$this->lib->convertPField($row['field_id'], $save);
			}
		
			// Save pfield_data
			$get[$this->lib->app['name']] = $us;
			IPSLib::updateSettings(array('conv_extra' => serialize($get)));
		
			// Next, please!
			$this->lib->next();
		
		}
	}