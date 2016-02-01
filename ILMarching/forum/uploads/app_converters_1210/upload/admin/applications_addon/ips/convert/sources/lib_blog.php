<?php
/**
 * IPS Converters
 * Application Files
 * Library functions for IP.Blog 2.0 conversions
 * Last Update: $Date: 2010-07-22 11:29:06 +0200(gio, 22 lug 2010) $
 * Last Updated By: $Author: terabyte $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 447 $
 *
 * @todo		Move all hard coded templates into skin_cp/ templates. ( I cannot see why this isn't possible )
 * @todo		Make hard coded text into language strings
 * @todo		Remove Blog Headers code, I don't believe that we use those. (verify)
 */

	class lib_blog extends lib_master
	{
		/**
	     * Information box to display on convert screen
	     *
	     * @access	public
	     * @return	string 		html to display
	     */
		public function getInfo()
		{
			return "<strong>Rebuild Content</strong><br />
				<a href='{$this->settings['base_url']}&app=blog&module=tools&section=rebuild&do=overview' target='_blank'>Click here</a> and run the following tools:
				<ul>
					<li>Resynchronize Entries</li>
					<li>Resynchronize Blogs</li>
					<li>Rebuild Blog Statistics</li>
				</ul><br />
				<br /><br />
				<strong>Turn the application back online</strong><br />
				Visit your IP.Blog settings and turn the application back online.";
		}
				
		/**
		 * Return the information needed for a specific action
		 *
	     * @access	public
		 * @param 	string		action (e.g. 'members', 'forums', etc.)
		 * @return 	array 		info needed for html->convertMenuRow
		 **/
		public function menuRow($action='', $return=false)
		{
			switch ($action)
			{
				case 'members':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'members' ) );
					$return = array(
						'name'	=> 'Members',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'blog_bookmarks':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_bookmarks' ) );
					$return = array(
						'name'	=> 'Bookmarks',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;
				
				case 'blog_themes':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_themes' ) );
					$return = array(
						'name'	=> 'Themes',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;
					
				case 'blog_headers':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_headers' ) );
					$return = array(
						'name'	=> 'Headers',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
						'conf'	=> false,
					);
					break;
					
				case 'blog_blogs':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_blogs' ) );
					$return = array(
						'name'	=> 'Blogs',
						'rows'	=> $count['count'],
						'cycle'	=> 75,
					);
					break;
					
				case 'blog_entries':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_entries' ) );
					$return = array(
						'name'	=> 'Entries',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
						'conf'	=> false,
					);
					break;
					
				case 'blog_polls':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_polls' ) );
					$return = array(
						'name'	=> 'Polls',
						'rows'	=> $count['count'],
						'cycle'	=> 1000,
					);
					break;
					
				case 'blog_comments':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_comments' ) );
					$return = array(
						'name'	=> 'Comments',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;
					
				case 'blog_moderators':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_moderators' ) );
					$return = array(
						'name'	=> 'Moderators',
						'rows'	=> $count['count'],
						'cycle'	=> 1000,
					);
					break;
					
				case 'blog_pingservices':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_pingservices' ) );
					$return = array(
						'name'	=> 'Ping Services',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;
					
				case 'blog_trackback':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_trackback' ) );
					$return = array(
						'name'	=> 'Trackbacks',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;
					
				case 'blog_ratings':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'blog_ratings' ) );
					$return = array(
							'name'	=> 'Ratings',
							'rows'	=> $count['count'],
							'cycle'	=> 2000,
					);
					break;
					
				case 'blog_attachments':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'attachments', 'where' => "attach_rel_module='blogentry'" ) );
					$return = array(
						'name'	=> 'Attachments',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;
								
				default:
					if ($return)
					{
						return false;
					}
					$this->error("There is a problem with the converter: called invalid action {$action}");
					break;
			}
			
			$basic = array('section' => $this->app['app_key'], 'key' => $action, 'app' => 'blog');
			return array_merge($basic, $return);
		}
				
		/**
		 * Return the tables that need to be truncated for a given action
		 *
	     * @access	public
		 * @param 	string		action (e.g. 'members', 'forums', etc.)
		 * @return 	array 		array('table' => 'id_field', ...)
		 **/
		public function truncate($action)
		{
			switch ($action)
			{
				case 'members':
					// We should never remove data for the member we are currently using. Ever.
					return array(
						'tables'	=> array( 'members' => 'member_id', 'pfields_content' => 'member_id', 'profile_portal' => 'pp_member_id', 'rc_modpref' => 'mem_id' ),
						'where'		=> array( 'members' => "member_id <> {$this->memberData['member_id']}", 'pfields_content' => "member_id <> {$this->memberData['member_id']}", 'profile_portal' => "pp_member_id <> {$this->memberData['member_id']}", 'rc_modpref' => "mem_id <> {$this->memberData['member_id']}" )
					);
					break;

				case 'blog_bookmarks':
					return array(
						'tables'	=> array( 'blog_bookmarks' => 'bookmark_id' ),
					);
					break;
					
				case 'blog_themes':
					return array(
						'tables'	=> array( 'blog_themes' => 'theme_id' ),
					);
					break;
					
				case 'blog_headers':
					return array(
						'tables'	=> array( 'blog_headers' => 'header_id' )
					);
					break;
					
				case 'blog_blogs':
					return array(
						'tables'	=> array( 'blog_blogs' => 'blog_id' )
					);
					break;
					
				case 'blog_entries':
					return array(
						'tables'	=> array( 'blog_entries' => 'entry_id' )
					);
					break;
					
				case 'blog_polls':
					return array(
						'tables'	=> array( 'blog_polls' => 'poll_id' )
					);
					break;

				case 'blog_voters':
					return array(
						'tables'	=> array( 'blog_voters' => 'vote_id' )
					);
					break;
					
				case 'blog_comments':
					return array(
						'tables'	=> array( 'blog_comments' => 'comment_id' )
					);
					break;
					
				case 'blog_cblocks':
					return array(
						'tables'	=> array( 'blog_cblocks' => 'cblock_id' )
					);
					break;
						
				case 'blog_custom_cblocks':
					return array(
						'tables'	=> array( 'blog_custom_cblocks' => 'cbcus_id' )
					);
					break;
					
				case 'blog_ratings':
					return array(
						'tables'	=> array( 'blog_ratings' => 'rating_id' )
					);
					break;
					
				case 'blog_moderators':
					return array(
						'tables'	=> array( 'blog_moderators' => 'moderate_id' )
					);
					break;
					
				case 'blog_pingservices':
					return array(
						'tables'	=> array( 'blog_pingservices' => 'blog_service_id' )
					);
					break;
					
				case 'blog_trackback':
					return array(
						'tables'	=> array( 'blog_trackback' => 'trackback_id' )
					);
					break;
					
				case 'blog_tracker':
					return array();
				break;
					
				case 'blog_attachments':
					return array(
						'tables'	=> array( 'attachments' => "attach_id" ),
						'where'		=> array( 'attachments' => "attach_rel_module = 'blogentry'" )
					);
					break;
				
				default:
					$this->error('There is a problem with the converter: bad truncate command');
					break;
			}
		}
		
		/**
		 * Database changes
		 *
		 * @access	public
		 * @param 	string		action (e.g. 'members', 'forums', etc.)
		 * @return 	array 		Details of change - array('type' => array(info))
		 **/
		public function databaseChanges($action)
		{
			switch ($action)
			{
				case 'members':
					return array('addfield' => array('members', 'conv_password', 'varchar(128)'));
					break;

				default:
			return null;
					break;
			}
		}
		
		/**
		 * Process report links
		 *
		 * @access	protected
		 * @param 	string		type (e.g. 'post', 'pm')
		 * @param 	array 		Data for reports_index table with foreign IDs
		 * @return 	array 		Processed data for reports_index table
		 **/
		protected function processReportLinks($type, $report)
		{
			$report['exdat1'] = $this->getLink($report['exdat1'], 'blog_blogs');
			$report['exdat2'] = $this->getLink($report['exdat2'], 'blog_entries');
			$report['exdat3'] = $this->getLink($report['exdat3'], 'blog_comments');
			$report['url'] = "/index.php?app=blog&amp;blogid={$report['exdat1']}&amp;showentry={$report['exdat2']}}&amp;st=0}&amp;#comment{$report['exdat3']}";
			$report['seotemplate'] = '';

			return $report;
		}
		
		/**
		 * Convert theme
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertTheme($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['theme_css'] and !$theme['images'])
			{
				$this->logError($id, 'No CSS or images provided');
				return false;
			}
			if (!$info['theme_name'])
			{
				$this->logError($id, 'No name provided');
				return false;
			}
									
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['theme_id']);
			$this->DB->insert( 'blog_themes', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_themes');
			
			return true;
		}
		
		/**
		 * Convert blog
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	array 		Permissions index data
		 * @return 	boolean		Success or fail
		 **/
		public function convertBlog($id, $info, $perms)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['member_id'])
			{
				$this->logError($id, 'No member ID provided');
				return false;
			}
			if (!$info['blog_name'])
			{
				$this->logError($id, 'No name provided');
				return false;
			}
			if (!$info['blog_type'])
			{
				$info['blog_type'] = 'local';
			}
			if ($info['blog_type'] == 'external' and !$info['blog_exturl'])
			{
				$this->logError($id, 'No external URL provided');
				return false;
			}
			
			//-----------------------------------------
			// Link
			//-----------------------------------------
			
			$info['member_id'] = $this->getLink($info['member_id'], 'members', false, true);
			$info['blog_theme_id'] = $info['blog_theme_id'] ? $this->getLink($info['blog_theme_id'], 'blog_themes', true) : 0;
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['blog_id']);
			unset($info['blog_skin_id']);
			
			// Make sure we don't have any fields we shouldn't have
			foreach (array('perm_id', 'app', 'perm_type', 'perm_type_id', 'perm_view', 'perm_2', 'perm_3', 'perm_4', 'perm_5', 'perm_6', 'perm_7', 'owner_only', 'friend_only', 'authorized_users') as $unset)
			{
				unset($info[$unset]);
			}
			
			// Process authorized users
			if ($info['blog_authorized_users'])
			{
				foreach ( explode( ",", $info['blog_authorized_users']) as $user )
				{
					$save[] = $this->getLink( $user, 'members', true, true );
				}
			
				$info['blog_authorized_users'] = implode(',', $save);
			}
			
			// Process group blog groups
			if ($info['blog_groupblog_ids'])
			{
				foreach ( explode( ",", $info['blog_groupblog_ids']) as $group )
				{
					$save[] = $this->getLink( $group, 'groups', true, true );
				}
					
				$info['blog_groupblog_ids'] = implode(',', $save);
			}

			$info['blog_editors'] = ( $info['blog_editors'] ) ? $info['blog_editors'] : 0;
			
			// Process editors
			if ($info['blog_editors'])
			{
				foreach ( explode( ",", $info['blog_editors']) as $editor )
				{
					$save[] = $this->getLink( $editor, 'members', true, true );
				}
					
				$info['blog_editors'] = implode(',', $save);
			}
			
			$this->DB->insert( 'blog_blogs', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add permissions entry
			//-----------------------------------------
			
// 			foreach ($perms as $key => $value)
// 			{
// 				if ($value != '*')
// 				{
// 					$save = array();
// 					foreach (explode(',', $value) as $pset)
// 					{
// 						if ($pset)
// 						{
// 							$save[] = $this->getLink($pset, 'forum_perms', false, true);
// 						}
// 					}
// 					$perms[$key] = implode(',', $save);
// 				}
// 			}
			
// 			$this->addToPermIndex('blog', $inserted_id, $perms, $id);
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_blogs');
			
			return true;
		}
		
		/**
		 * Convert entry
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	boolean		If true, will not get link for blog id
		 * @return 	boolean		Success or fail
		 **/
		public function convertEntry($id, $info, $skip_blog_link=FALSE)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['blog_id'])
			{
				$this->logError($id, 'No blog ID provided');
				return false;
			}
			if (!$info['entry_author_id'])
			{
				$this->logError($id, 'No author ID provided');
				return false;
			}
			if (!$info['entry_name'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['entry'])
			{
				$this->logError($id, 'No entry provided');
				return false;
			}
			if (!$info['entry_status'])
			{
				$info['entry_status'] = 'published';
			}
			
			if ( ! $info['entry_post_key'] )
			{
				$info['entry_post_key'] = md5( $id . 'blogentry' );
			}
			
			//-----------------------------------------
			// Link
			//-----------------------------------------
			
			unset($info['entry_last_comment']);
			
			$info['blog_id_'] = ($skip_blog_link) ? $info['blog_id'] : $this->getLink($info['blog_id'], 'blog_blogs');

			//-----------------------------------------
			// What if getLink fails?
			//-----------------------------------------

			if (empty($info['blog_id_']))
			{
				$this->logError($id, 'Bad Blog ID');
			}
			else
			{
				$info['blog_id'] = $info['blog_id_'];
			}

			unset($info['blog_id_']);
			$info['entry_author_id'] = $this->getLink($info['entry_author_id'], 'members', false, true);
			$info['entry_last_comment_mid'] = ($info['entry_last_comment_mid']) ? $this->getLink($info['entry_last_comment_mid'], 'members', false, true) : 0;
			
			unset($info['entry_gallery_album']);
											
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			$info['entry_last_comment_date'] = ( $info['entry_last_comment_date'] ) ? $info['entry_last_comment_date'] : 0;
			$info['entry_edit_time'] = ( $info['entry_edit_time'] ) ? $info['entry_edit_time'] : 0;
			
			unset($info['entry_id']);
			$this->DB->insert( 'blog_entries', $info );
			$inserted_id = $this->DB->getInsertId();
						
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_entries');
			
			return true;
		}
		
		/**
		 * Convert a poll
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertPoll($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['entry_id'])
			{
				$this->logError($id, 'No entry ID number provided');
				return false;
			}
			if (!$info['starter_id'])
			{
				$this->logError($id, 'No author ID number provided');
				return false;
			}
			if (!$info['poll_question'])
			{
				$this->logError($id, 'No poll title provided');
				return false;
			}
			if (!$info['choices'])
			{
				$this->logError($id, 'No questions provided');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			$info['entry_id'] = $this->getLink($info['entry_id'], 'blog_entries');
			$info['starter_id'] = $this->getLink($info['starter_id'], 'members', false, true);
			
			unset($info['poll_id']);
			$this->DB->insert( 'blog_polls', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_polls');
			
			return true;
		}
		
		/**
		 * Convert a poll voter
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertPollVoter($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, '(VOTE) No ID number provided');
				return false;
			}
			if (!$info['entry_id'])
			{
				$this->logError($id, '(VOTE) No entry ID number provided');
				return false;
			}
			if (!$info['member_id'])
			{
				$this->logError($id, '(VOTE) No voter ID number provided');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			$info['entry_id'] = $this->getLink($info['entry_id'], 'blog_entries');
			$info['member_id'] = $this->getLink($info['member_id'], 'members', false, true);
			
			unset($info['vote_id']);
			$this->DB->insert( 'blog_voters', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_voters');
			
			return true;
		}
		
		/**
		 * Convert a comment
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertComment($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['entry_id'])
			{
				$this->logError($id, 'No entry ID number provided');
				return false;
			}
			/*if (!$info['member_id'])
			{
				$this->logError($id, 'No member ID number provided');
				return false;
			}*/
			if (!$info['comment_text'])
			{
				$this->logError($id, 'No comment provided');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			$info['entry_id'] = $this->getLink($info['entry_id'], 'blog_entries');
			$info['member_id'] = $info['member_id'] > 0 ? $this->getLink($info['member_id'], 'members', false, true) : 0;
			
			// Unset 3.2 removed fields
			unset($info['comment_id']);
			unset($info['comment_use_emo']);
			unset($info['comment_edit_name']);
			
			$this->DB->insert( 'blog_comments', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_comments');
			
			return true;
		}
		
		/**
		 * Convert a bookmark
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertBookmark($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['bookmark_title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['bookmark_url'])
			{
				$this->logError($id, 'No URL provided');
				return false;
			}
			
			//-----------------------------------------
			// Check for duplicates
			//-----------------------------------------

			if ($this->DB->buildAndFetch( array( 'select' => 'bookmark_id', 'from' => 'blog_bookmarks', 'where' => "bookmark_title='{$info['bookmark_title']}'" ) ))
			{
				return false;
			}
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['bookmark_id']);
			$this->DB->insert( 'blog_bookmarks', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_bookmarks');
			
			return true;
		}
		
		/**
		 * Convert a content block
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertCBlock($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, '(CONTENT BLOCK) No ID number provided');
				return false;
			}
			if (!$info['blog_id'])
			{
				$this->logError($id, '(CONTENT BLOCK) No blog ID provided');
				return false;
			}
			if (!$info['member_id'])
			{
				$this->logError($id, '(CONTENT BLOCK) No member ID provided');
				return false;
			}
			
			if ( ! $info['cbcus_post_key'] )
			{
				$info['cbcus_post_key'] = md5( $id . 'blogcblock' );
			}
			
			//-----------------------------------------
			// Link
			//-----------------------------------------
			
			$info['blog_id'] = $this->getLink($info['blog_id'], 'blog_blogs');
			$info['member_id'] = $this->getLink($info['member_id'], 'members', false, true);
			
			if ($info['cblock_type'] == 'custom')
			{
				$info['cblock_ref_id'] = $this->getLink($info['cblock_ref_id'], 'blog_custom_cblocks');
			}
			else
			{
				$info['cblock_ref_id'] = $this->getLink($info['cblock_ref_id'], 'blog_default_cblocks');
			}
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['cblock_id']);
			$this->DB->insert( 'blog_cblocks', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_cblocks');
			
			return true;
		}
		
		/**
		 * Convert a custom block
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertCustomCBlock($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, '(CUSTOM CONTENT BLOCK) No ID number provided');
				return false;
			}
			if (!$info['cbcus_name'])
			{
				$this->logError($id, '(CUSTOM CONTENT BLOCK) No name provided');
				return false;
			}
			if (!$info['cbcus'])
			{
				$this->logError($id, '(CUSTOM CONTENT BLOCK) No content provided');
				return false;
			}
						
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['cbcus_id']);
			$this->DB->insert( 'blog_custom_cblocks', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_custom_cblocks');
			
			return true;
		}
		
		/**
		 * Convert a default block
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertDefaultCBlock($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, '(DEFAULT CONTENT BLOCK) No ID number provided');
				return false;
			}
			if (!$info['cbdef_name'])
			{
				$this->logError($id, '(DEFAULT CONTENT BLOCK) No name provided');
				return false;
			}
			if (!$info['cbdef_function'])
			{
				$this->logError($id, '(DEFAULT CONTENT BLOCK) No function provided');
				return false;
			}
			
			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------
			
			$dupe = $this->DB->buildAndFetch(array('select' => 'cbdef_id', 'from' => 'blog_default_cblocks', 'where' => "cbdef_function='{$info['cbdef_function']}'"));
			if ($dupe)
			{
				$this->addLink($dupe['cbdef_id'], $id, 'blog_default_cblocks', 1);
				return false;
			}
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			$this->DB->insert('blog_default_cblocks', $info);
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_default_cblocks');
			
			return true;
		}
		
		/**
		 * Convert ratings
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertRating($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['member_id'])
			{
				$this->logError($id, 'No member ID provided');
				return false;
			}
			if (!$info['blog_id'])
			{
				$this->logError($id, 'No blog ID provided');
				return false;
			}
			if (!$info['rating'])
			{
				$this->logError($id, 'No rating provided');
				return false;
			}
									
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			$info['member_id'] = $this->getLink($info['member_id'], 'members', false, true);
			$info['blog_id'] = $this->getLink($info['blog_id'], 'blog_blogs');
			$info['entry_id'] = $this->getLink($info['entry_id'], 'blog_entries');
			
			unset($info['rating_id']);
			$this->DB->insert( 'blog_ratings', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_ratings');
			
			return true;
		}
		
		/**
		 * Convert a moderator
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertModerator($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['moderate_type'])
			{
				$this->logError($id, 'No type provided');
				return false;
			}
			if (!$info['moderate_mg_id'])
			{
				$this->logError($id, 'No link ID provided');
				return false;
			}
			
			//-----------------------------------------
			// Link
			//-----------------------------------------
			
			if ($info['moderate_type'] == 'group')
			{
				$info['moderate_mg_id'] = $this->getLink($info['moderate_mg_id'], 'groups', false, true);
			}
			else
			{
				$info['moderate_mg_id'] = $this->getLink($info['moderate_mg_id'], 'members', false, true);
			}
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['moderate_id']);
			$this->DB->insert( 'blog_moderators', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_moderators');
			
			return true;
		}
		
		/**
		 * Convert a ping service
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertPingService($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['blog_service_key'])
			{
				$this->logError($id, 'No key provided');
				return false;
			}
			if (!$info['blog_service_name'])
			{
				$this->logError($id, 'No name provided');
				return false;
			}
			
			//-----------------------------------------
			// Check for duplicates
			//-----------------------------------------

			if ($this->DB->buildAndFetch( array( 'select' => 'blog_service_id', 'from' => 'blog_pingservices', 'where' => "blog_service_key='{$info['blog_service_key']}'" ) ))
			{
				return false;
			}
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['blog_service_id']);
			$this->DB->insert( 'blog_pingservices', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_pingservices');
			
			return true;
		}
		
		/**
		 * Convert a trackback
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertTrackback($id, $info, $skip_blog_link=FALSE)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['blog_id'])
			{
				$this->logError($id, 'No blog ID provided');
				return false;
			}
			if (!$info['entry_id'])
			{
				$this->logError($id, 'No entry ID provided');
				return false;
			}
			if (!$info['trackback_url'])
			{
				$this->logError($id, 'No URL provided');
				return false;
			}
			
			//-----------------------------------------
			// Link
			//-----------------------------------------

			$info['blog_id'] = ($skip_blog_link) ? $info['blog_id'] : $this->getLink($info['blog_id'], 'blog_blogs');
			$info['entry_id'] = $this->getLink($info['entry_id'], 'blog_entries');
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['trackback_id']);
			$this->DB->insert( 'blog_trackback', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_trackback');
			
			return true;
		}
		
		/**
		 * Convert a subscription
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 *
		 * @deprecated
		 **/
		public function convertTracker($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			
			$this->convertFollow ( array (
				'like_app'			=> 'blog',
				'like_area'			=> 'blog',
				'like_rel_id'		=> $info['blog_id'],
				'like_member_id'	=> $info['member_id'],
			) );
			
			/*if (!$id)
			{
				$this->logError($id, '(SUBSCRIPTION) No ID number provided');
				return false;
			}
			if (!$info['blog_id'])
			{
				$this->logError($id, '(SUBSCRIPTION) No blog ID provided');
				return false;
			}
			if (!$info['member_id'])
			{
				$this->logError($id, '(SUBSCRIPTION) No member ID provided');
				return false;
			}
			
			//-----------------------------------------
			// Link
			//-----------------------------------------

			$info['blog_id'] = $this->getLink($info['blog_id'], 'blog_blogs');
			$info['member_id'] = $this->getLink($info['member_id'], 'members', false, true);
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			unset($info['tracker_id']);
			$this->DB->insert( 'blog_tracker', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'blog_tracker');*/
			
			return true;
		}
		
	}
	
