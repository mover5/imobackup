<?php
/**
 * IPS Converters
 * IP.Board 3.0 Converters
 * vBulletin
 * Last Update: $Date: $
 * Last Updated By: $Author: $
 *
 * @package		IPS Converters
 * @author 		Michael Burton
 * @copyright	(c) 2012 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: $
 */


	$info = array(
		'key'	=> 'vanilla',
		'name'	=> 'Vanilla 2',
		'login'	=> true,
	);
	
	class admin_convert_board_vanilla extends ipsCommand
	{
		public $name = 'Vanilla 2';
		
		/**
		 * Main class entry point
		 *
		 * @access	public
		 * @param	object		ipsRegistry
		 * @return	void
		 */
		
		public function doExecute( ipsRegistry $registry )
		{
			$this->registry	=	$registry;
			
			//-----------------------------------------
			// Vanilla does not support a lot of things by default, but does support them as Plugins.
			// An example of this is Emoticons. This converter does not support those by default, but we may look into it in future.
			//-----------------------------------------
			
			$this->actions	=	array(
									'forum_perms'	=>	array(),
									'groups'		=>	array('forum_perms'),
									'members'		=>	array('groups'),
									'forums'		=>	array('members'),
									'topics'		=>	array('members'),
									'posts'			=>	array('members', 'topics'),
									'pms'			=>	array('members'),
								);
			
			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_master.php' );
			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_board.php' );
			$this->lib	=	new lib_board( $registry, $html, $this );
			
			$this->html	=	$this->lib->loadInterface();
			$this->lib->sendHeader( 'Vanilla Forums &rarr; IP.Board Converter' );
			$this->HB	=	$this->lib->connect();

			require_once( IPS_ROOT_PATH . 'sources/handlers/han_parse_bbcode.php' );
			$this->parser	=	new parseBbcode( $registry );
			$this->parser->parse_smilies	=	1;
			$this->parser->parse_bbcode		=	1;
			$this->parser->parsing_section	=	'convert';
			
			if ( array_key_exists( $this->request['do'], $this->actions ) or $this->request['do'] == 'disallow' )
			{
				call_user_func(array($this, 'convert_'.$this->request['do']));
			}
			else
			{
				$this->lib->menu();
			}
			
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
			$this->registry->output->html		.=	$this->html->convertFooter();
			$this->registry->output->html_main	.=	$this->registry->output->global_template->global_frame_wrapper();
			$this->registry->output->sendOutput();
			exit;
		}
		

		/**
		 * Count rows
		 *
		 * @access	private
		 * @param 	string		action (e.g. 'members', 'forums', etc.)
		 * @return 	integer 	number of entries
		 */
		
		public function countRows($action)
		{
			switch ($action)
			{
				case 'forums':
					return $this->lib->countRows('Category', 'CategoryID!="-1"');
					break;
				
				case 'forum_perms':
					//return  $this->lib->countRows('GDN_Permission', "JunctionTable='Category'");
					return  $this->lib->countRows('Permission', 'JunctionTable IS NULL'); // forum_perms is misleading - it's usergroup perms. I know, right.
					break;
		
				case 'groups':
					return  $this->lib->countRows('Role');
					break;
		
				case 'members':
					return  $this->lib->countRows('User');
					break;
		
				case 'topics':
					return  $this->lib->countRows('Discussion');
					break;
					
				case 'posts':
					return  $this->lib->countRows('Comment'); // GDN_Message
					break;
		
				case 'pms':
					return  $this->lib->countRows('Conversation'); // GDN_ConversationMessage
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
				case 'forum_perms':
				case 'groups':
				case 'members':
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
			// Sort out newlines
			$post = nl2br($post);
			
			// HTMLPurify
			require_once( IPS_KERNEL_PATH . 'HTMLPurifier/HTMLPurifier.auto.php' );
			
			// run through purifier
			$config = HTMLPurifier_Config::createDefault();
			$config->set( 'AutoFormat.Linkify', TRUE );
			$config->set( 'Core.Encoding', IPS_DOC_CHAR_SET );
			$config->set( 'HTML.TargetBlank', TRUE );
			$config->set( 'HTML.SafeIframe', TRUE );
			// YouTube and Vimeo
			$config->set( 'URI.SafeIframeRegexp', '%^http://((www.)?youtube.com/embed/|player.vimeo.com/video/)%' );
			
			$purifier = new HTMLPurifier( $config );
			$post = $purifier->purify( $post );
			
			// return the purified post
			return $post;
		}
		
		/**
		 * Convert Forum Permissions
		 *
		 * @access	private
		 * @return void
		 **/
		
		private function convert_forum_perms()
		{
			$this->lib->saveMoreInfo('forum_perms', 'map');

			$main = array(
							'select' 	=>	'P.RoleID',
							'from' 		=>	array('Permission'	=>	'P'),
							'where'		=>	'P.JunctionTable IS NULL AND R.RoleID !=0',
							'order'		=>	'P.RoleID ASC',
							'add_join'	=>	array(
												array(
														'select'	=>	'R.Name',
														'from'		=>	array('Role'	=>	'R'),
														'where'		=>	'P.RoleID=R.RoleID',
														'type'		=>	'left',
												),
											)
						);
			
			$loop = $this->lib->load( 'forum_perms', $main, array(), array(), TRUE );

			$this->lib->getMoreInfo( 'forum_perms', $loop, array( 'new' => '--Create new set--', 'ot' => 'Old permission set', 'nt' => 'New permission set' ), '', array( 'idf' => 'RoleID', 'nf' => 'Name' ) );
			
			foreach( $loop as $row )
			{
				$this->lib->convertPermSet( $row['RoleID'], $row['Name'] );
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
			$this->lib->saveMoreInfo('groups', 'map');
			
			$main = array(
							'select' 	=>	'P.*',
							'from' 		=>	array('Permission'	=>	'P'),
							'where'		=>	'P.JunctionTable IS NULL AND R.RoleID !=0',
							'order'		=>	'P.RoleID ASC',
							'add_join'	=>	array(
												array(
														'select'	=>	'R.*',
														'from'		=>	array('Role'	=>	'R'),
														'where'		=>	'P.RoleID=R.RoleID',
														'type'		=>	'left',
												),
											)
						);
			
			$loop = $this->lib->load( 'groups', $main, array(), array(), TRUE );
			
			$this->lib->getMoreInfo( 'groups', $loop, array( 'new' => '--Create new group--', 'ot' => 'Old group', 'nt' => 'New group' ), '', array( 'idf' => 'RoleID', 'nf' => 'Name' ) );
			
			
			foreach( $loop as $row )
			{
				$save = array(
						'g_view_board'			=>	($row['Garden.SignIn.Allow'] == '1' && $row['Garden.Profiles.View'] == '1' && $row['Garden.Activity.View'] == '1' && $row['Vanilla.Discussions.View'] == '1') ? 1 : 0,
						'g_mem_info'			=>	$row['Garden.Profiles.View'],
						'g_other_topics'		=>	$row['Vanilla.Discussions.View'],
						'g_use_search'			=>	1,
						'g_email_friend'		=>	1,
						'g_edit_profile'		=>	$row['Garden.Profiles.Edit'],
						'g_post_new_topics'		=>	$row['Vanilla.Discussions.Add'],
						'g_reply_own_topics'	=>	$row['Vanilla.Comments.Add'],
						'g_reply_other_topics'	=>	$row['Vanilla.Comments.Add'],
						'g_edit_posts'			=>	$row['Vanilla.Comments.Edit'],
						'g_delete_own_posts'	=>	$row['Vanilla.Comments.Delete'],
						'g_post_polls'			=> 	$row['Vanilla.Discussions.Add'],
						'g_vote_polls'			=> 	$row['Vanilla.Comments.Add'],
						'g_use_pm'				=> 	($row['Vanilla.Discussions.Add'] == '1' && $row['Vanilla.Comments.Add'] == '1') ? 1 : 0,
						'g_is_supmod'			=> 	($row['Garden.Settings.Manage'] == '1') ? 1 : 0,
						'g_title'				=> 	$row['Name'],
						'g_access_offline'		=> 	($row['Garden.Settings.Manage'] == '1') ? 1 : 0,
						'g_perm_id'				=> 	$row['RoleID'],
						'g_bypass_badwords'		=> 	($row['Garden.Settings.Manage'] == '1') ? 1 : 0,
				);
				$this->lib->convertGroup($row['RoleID'], $save);
			}
			
			$this->lib->next();
		}
		
		/**
		 * Convert members
		 *
		 * @access	private
		 * @return void
		 **/
		
		private function convert_members()
		{			
			$this->lib->saveMoreInfo('members', 'path');
				
			$main = array(	
							'select' 	=>	'U.*',
							'from' 		=>	array('User' => 'U'),
							'add_join'	=>	array(
												array(
														'select'	=>	'R.*',
														'from'		=>	array('UserRole'	=>	'R'),
														'where'		=>	'U.UserID=R.UserID',
													),
												/*array(
														'select'	=>	'X.*',
														'from'		=>	array('GDN_Role'	=>	'X'),
														'where'		=>	'X.RoleID=R.RoleID',	
													)*/
											),
							'order'		=>	'U.UserID ASC',
						);
			
			$loop = $this->lib->load('members', $main);
			
			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			$ask = array();
			
			// We need to know how to the avatar paths
			$ask['avvy_path'] 	= array('type' => 'text', 'label' => 'Path to avatars folder (no trailing slash, default /path_to_vanilla/uploads/userpics): ');
			
			$this->lib->getMoreInfo('members', $loop, $ask, 'path');
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$info = array(
						'id'				=>	$row['UserID'],
						'group'				=>	$row['RoleID'],
						'joined'			=>	strtotime($row['DateInserted']),
						'username'			=>	$row['Name'],
						'email'				=>	$row['Email'],
						'password'			=>	$row['Password'],
				);
			
				// Member info
				// strip out time 00:00:00
				$row['DateOfBirth'] = substr($row['DateOfBirth'], 0, 10);
				$birthday = ($row['DateOfBirth']) ? explode('-', $row['DateOfBirth']) : null;
				
				$members = array(
						'ip_address'		=>	empty($row['InsertIPAddress']) ? $row['UpdateIPAddress'] : $row['InsertIPAddress'],
						'posts'				=>	$row['CountDiscussions'] + $row['CountComments'],
						'time_offset'		=>	$row['HourOffset'],
						'hide_email'		=>	$row['ShowEmail'],
						'bday_day'			=>	($row['DateOfBirth']) ? $birthday[0] : '',
						'bday_month'		=>	($row['DateOfBirth']) ? $birthday[1] : '',
						'bday_year'			=>	($row['DateOfBirth']) ? $birthday[2] : '',
						'last_visit'		=>	strtotime($row['DateLastActive']),
				);
			
				// Profile
				$profile = array(
						'pp_about_me'		=>	$this->fixPostData($row['About']),
				);
			
				//-----------------------------------------
				// Avatar
				//-----------------------------------------
			
				$path = '';
				if ( $row['Photo'] )
				{
					$profile['photo_type']		=	'custom';
					$profile['photo_location']	=	$row['Photo'];
					$path = $us['avvy_path'];
				}
			
				//-----------------------------------------
				// And go!
				//-----------------------------------------

				$this->lib->convertMember($info, $members, $profile, array(), $path);
			}
			
			$this->lib->next();			
		}
		
		/**
		 * Convert forums
		 *
		 * @access	private
		 * @return void
		 **/
		
		private function convert_forums()
		{
			$main = array(	
							'select' 	=>	'*',
							'from' 		=>	'Category',
							'where'		=>	'CategoryID!="-1"',
							'order'		=>	'CategoryID ASC',
					);
			
			$loop = $this->lib->load('forums', $main);
			
			//-----------------------------------------
			// Get groups
			//-----------------------------------------
			
			$groups = array();
			ipsRegistry::DB('hb')->build(
										array(
												'select'	=>	'*', 
												'from'		=>	'Role'
											)
									);
			
			ipsRegistry::DB('hb')->execute();
			while ($g = ipsRegistry::DB('hb')->fetch())
			{
				$groups[] = $g;
			}
			
			//---------------------------	
			// create a new container category
			//---------------------------
			
			$forum	=	array(
							'name'				=>	'Vanilla',
							'name_seo'			=>	IPSText::makeSeoTitle('Vanilla'),
							'description'		=>	'This is an automatically generated category for converted Vanilla forums.',
							'posts'				=> 0,
							'last_post'			=> 0,
						);
			
			$this->lib->convertForum('c_0', $forum, array());

			
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				//-----------------------------------------
				// Handle permissions
				//-----------------------------------------
			
				$canview = array();
				$canread = array();
				$canreply = array();
				$canstart = array();
				$canupload = array();
				$candownload = array();

				foreach ($groups as $group)
				{
					$perms = array();
			
					if ($perms['canview'])
					{
						$canview[]		=	$group['RoleID'];
					}
					if ($perms['canviewthreads'])
					{
						$canread[]		=	$group['RoleID'];
					}
					if ($perms['canpostreplys'])
					{
						$canreply[]		=	$group['RoleID'];
					}
					if ($perms['canpostthreads'])
					{
						$canstart[]		=	$group['RoleID'];
					}
					if ($perms['canpostattachments'])
					{
						$canupload[]	=	$group['RoleID'];
					}
					if ($perms['candlattachments'])
					{
						$candownload[]	=	$group['RoleID'];
					}
				}
			
				$perms = array();
				$perms['view']		= implode(',', $canview);
				$perms['read']		= implode(',', $canread);
				$perms['reply']		= implode(',', $canreply);
				$perms['start']		= implode(',', $canstart);
				$perms['upload']	= implode(',', $canupload);
				$perms['download']	= implode(',', $candownload);
				
				if (!$row['active'])
				{
					$perms = array();
				}

				$parent = $row['ParentCategoryID'];

				$save = array(
							'topics'			=>	$row['CountDiscussions'],
							'posts'				=>	$row['CountComments'],
							'last_post'			=>	$row['LastCommentID'] ? $row['LastCommentID'] : 0,
							'name'				=>	$row['Name'],
							'name_seo'			=>	IPSText::makeSeoTitle($row['Name']),
							'description'		=>	$this->fixPostData($row['Description']),
							'use_html'			=>	0, // vanilla uses Raw HTML and HTMLPurifies... unsure what to do here.
							'status'			=>	$row['Archived'] == '0' ? 1 : 0,
							'conv_parent'		=>	$parent,
							'parent_id'			=>	$parent == '-1' ? 'c_0' : $parent,							
							'sub_can_post'		=>	$row['AllowDiscussions'],
						);
			
				$this->lib->convertForum($row['CategoryID'], $save, $perms, true);
			}
				$this->lib->next();			
		}
		
		/**
		 * Convert topics
		 *
		 * @access	private
		 * @return void
		 **/
		
		private function convert_topics()
		{
			$main = array(	
							'select' 	=> 'D.*, D.Name as TopicName',
							'from' 		=> array('Discussion'	=>	'D'),
							'add_join'	=>	array(
												array(
														'select'	=>	'U.*, U.Name as UserName',
														'from'		=>	array('User'	=>	'U'),
														'where'		=>	'D.InsertUserID=U.UserID',
													),
											),
							'order'		=> 'D.DiscussionID ASC',
						);
			
			$loop = $this->lib->load('topics', $main);
			
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{		

				if ($row['LastCommentUserID'])
				{
						$lastPoster = ipsRegistry::DB('hb')->buildAndFetch(
														array(
																'select'	=>	'Uz.Name',
																'from'		=>	array('User' => 'Uz'),
																'where'		=>	'Uz.UserID='.$row['LastCommentUserID'],	
															)
													);
				}

				$save = array(
						'forum_id'			=> $row['CategoryID'],
						'title'				=> $row['TopicName'],
						'poll_state'		=> 0, // Vanilla does not have Polls
						'starter_id'		=> $row['InsertUserID'],
						'starter_name'		=> $row['UserName'],
						'start_date'		=> $row['DateInserted'] ? strtotime( $row['DateInserted'] ) : 0,
						'last_post'			=> $row['LastCommentID'] ? $row['LastCommentID'] : 0,
						'last_poster_name'	=> $lastPoster['Name'] ? $lastPoster['Name'] : '',
						'last_poster_id'	=> $row['LastCommentUserID'],
						'views'				=> $row['CountViews'],
						'posts'				=> $row['CountComments'] ? $row['CountComments'] : 0,
						'state'		   	 	=> $row['Closed'] == 0 ? 'open' : 'closed',
						'pinned'			=> $row['Announce'],
				);

			
				$this->lib->convertTopic($row['DiscussionID'], $save);
				
				$psave = array(
						'topic_id'		=> $row['DiscussionID'],
						'author_id'		=> $row['InsertUserID'],
						'author_name'	=> $row['Name'],
						'post_date'		=> strtotime($row['DateInserted']),
						'post'			=> $this->fixPostData($row['Body']),
						'ip_address'	=> $row['InsertIPAddress'],
						'edit_name'		=> $row['Name'],
						'edit_time'		=> strtotime($row['DateUpdated']),
				);
					
				$this->lib->convertPost( 'fp-' . $row['DiscussionID'], $psave, false, true );
			}

			$this->lib->next();			
		}
		
		/**
		 * Convert posts
		 *
		 * @access	private
		 * @return void
		 **/
		
		private function convert_posts()
		{
			//---------------------------
			// Set up
			//---------------------------
			
			$main = array(	
							'select' 	=> 'C.*',
							'from' 		=>	array('Comment'	=>	'C'),
							'add_join'	=>	array(
												array(
														'select'	=>	'U.Name',
														'from'		=>	array('User'	=>	'U'),
														'where'		=>	'C.InsertUserID=U.UserID'	
													)
											),
							'order'		=> 'CommentID ASC',
						);
			
			$loop = $this->lib->load('posts', $main);

			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save = array(
						'topic_id'		=> $row['DiscussionID'],
						'author_id'		=> $row['InsertUserID'],
						'author_name'	=> $row['Name'],
						'post_date'		=> strtotime($row['DateInserted']),
						'post'			=> $this->fixPostData($row['Body']),
						'ip_address'	=> $row['InsertIPAddress'],
						'edit_name'		=> $row['Name'],
						'edit_time'		=> strtotime($row['DateUpdated']),
				);
			
				$this->lib->convertPost($row['CommentID'], $save);
			}
			
			$this->lib->next();
				
		}
		
		/**
		 * Convert PM's
		 *
		 * @access	private
		 * @return void
		 **/
		
		private function convert_pms()
		{
			$main = array(
							'select' 	=> 'C.*',
							'from' 		=>	array('Conversation'	=>	'C'),
							'add_join'	=>	array(
												array(
														'select'	=>	'CM.*',
														'from'		=>	array('ConversationMessage'	=>	'CM'),
														'where'		=>	'C.ConversationID=CM.ConversationID',
													)
											),
							'order'		=> 'C.ConversationID ASC',
					);
			
			$loop = $this->lib->load('pms', $main, array('pm_posts', 'pm_maps'));
			
			//---------------------------
			// Loop
			//---------------------------
			
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
			
				//-----------------------------------------
				// Post Data
				//-----------------------------------------
			
				$post = array(
						'msg_id'			=> $row['MessageID'],
						'msg_topic_id'      => $row['ConversationID'],
						'msg_date'          => strtotime($row['DateInserted']),
						'msg_post'          => $this->fixPostData($row['Body']),
						'msg_post_key'      => md5(microtime()),
						'msg_author_id'     => $row['InsertUserID'],
						'msg_is_first_post' => 1
				);
			
				//-----------------------------------------
				// Map Data
				//-----------------------------------------
			
				$maps = array(
						array(
								'map_user_id'			=> $row['InsertUserID'],
								'map_topic_id'			=> $row['ConversationID'],
								'map_folder_id'			=> 'myconvo',
								'map_read_time'   		=> 0,
								'map_last_topic_reply'	=> strtotime($row['DateInserted']),
								'map_user_active'		=> 1,
								'map_user_banned'		=> 0,
								//'map_has_unread'		=> ($row['receipt'] == 0) ? 1 : 0,
								'map_is_system'			=> 0,
								//'map_is_starter'		=> ( $row['InsertUserID'] == $to['InsertUserID'] ) ? 1 : 0
						)
				);
			
				//-----------------------------------------
				// Map Data
				//-----------------------------------------
			
				$topic = array(
						'mt_id'			     => $row['ConversationID'],
						'mt_date'		     => strtotime($row['DateInserted']),
						'mt_title'		     => empty($row['Subject']) ? substr($row['Body'], 0, 48) : $row['Subject'],
						'mt_starter_id'	     => $row['InsertUserID'],
						'mt_start_time'      => strtotime($row['DateInserted']),
						'mt_last_post_time'  => strtotime($row['DateUpdated']),
						'mt_invited_members' => $row['Contributors'],
						'mt_to_count'		 => 1,
						'mt_to_member_id'	 => $row['InsertUserID'],
						'mt_replies'		 => 0,
						'mt_is_draft'		 => 0,
						'mt_is_deleted'		 => 0,
						'mt_is_system'		 => 0
				);
			
				//-----------------------------------------
				// Go
				//-----------------------------------------
			
				$this->lib->convertPM($topic, array($post), $maps);
			
			}
			
			$this->lib->next();			
		}
		
	}