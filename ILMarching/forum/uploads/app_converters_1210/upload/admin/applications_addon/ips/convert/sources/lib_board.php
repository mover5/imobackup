<?php
/**
 * IPS Converters
 * Application Files
 * Library functions for IP.Board 3.0 conversions
 * Last Update: $Date: 2014-01-06 17:07:13 -0500 (Mon, 06 Jan 2014) $
 * Last Updated By: $Author: rashbrook $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 953 $
 *
 * @todo		Linkify the Recache areas - often see in tickets that people don't run them.
 * @todo		Move all hard coded templates into skin_cp/ templates. ( I cannot see why this isn't possible )
 * @todo		Make hard coded text into language strings
 */

	class lib_board extends lib_master
	{
		/**
		 * Array of default BB Codes to prevent them from being removing during Empty Local Data
		 *
		 */
		private $_defaultBbCodes = array( 'snapback', 'right', 'left', 'center', 'topic', 'post', 'spoiler', 'acronym', 'b', 'i', 'u', 'hr', 'code', 'php', 'html', 'sql', 'xml', 'url', 'img', 'quote', 'indent', 'list', 'strike', 'sub', 'sup', 'email', 'background', 'color', 'size', 'font', 'member', 'media', 'twitter', 'sharedmedia', 'entry', 'blog', 'extract', 'page' );
		/**
	     * Information box to display on convert screen
	     *
	     * @access	public
	     * @return	string 		html to display
	     */
		public function getInfo()
		{
			return "<strong>Permissions</strong><br />
				Please note that permissions can not be converted between software. Please make sure you reset your <a href='{$this->settings['base_url']}&app=members&module=groups&section=permissions'>Permission Sets</a>. Until you do, please note that <strong>no members will be able to view any forums on your community after the conversion.</strong><br /><br />
				<strong>Rebuild Content</strong><br />
				<a href='{$this->settings['base_url']}&app=core&module=tools&section=rebuild&do=rebuild_overview' target='_blank'>Click here</a> and run the following tools in the order given:
				<ul>
					<li>Recount Statistics</li>
					<li>Resynchronize Topics</li>
					<li>Resynchronize Forums</li>
					<li>Rebuild Attachment Thumbnails</li>
					<li>Rebuild Profile Photo Thumbnails</li>
				</ul><br />
				<strong>Rebuild Caches</strong><br />
				<a href='{$this->settings['base_url']}&app=core&&module=tools&section=cache' target='_blank'>Click here</a> and recache all.";
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
				case 'groups':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'groups' ) );
					$return = array(
						'name'	=> 'Member Groups',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'members':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'members' ) );
					$return = array(
						'name'	=> 'Members',
						'rows'	=> $count['count'],
						'cycle'	=> 250,
					);
					break;

				case 'dnames_change':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'dnames_change' ) );
					$return = array(
						'name'	=> 'Display Name History',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;
					
				case 'tags':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'core_tags' ) );
					$return = array(
						'name'	=> 'Tags',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'forum_perms':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'forum_perms' ) );
					$return = array(
						'name'	=> 'Permission Sets',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'forums':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'forums' ) );
					$return = array(
						'name'	=> 'Categories &amp; Forums',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
						'finish'=> "You will now need to <a href='{$this->settings['base_url']}&amp;app=members&amp;module=groups&amp;section=permissions' target='_blank'>configure permissions</a>."
					);
					break;

				case 'moderators':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'moderators' ) );
					$return = array(
						'name'	=> 'Forum Moderators',
						'rows'	=> $count['count'],
						'cycle'	=> 500,
					);
					break;

				case 'custom_bbcode':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'custom_bbcode' ) );
					$return = array(
						'name'	=> 'Custom BBCode',
						'rows'	=> $count['count'],
						'cycle'	=> 50,
					);
					break;

				case 'topics':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'topics' ) );
					$return = array(
						'name'	=> 'Topics',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'posts':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'posts' ) );
					$return = array(
						'name'	=> 'Posts',
						'rows'	=> $count['count'],
						'cycle'	=> 1500,
					);
					break;

				case 'polls':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'polls' ) );
					$return = array(
						'name'	=> 'Polls',
						'rows'	=> $count['count'],
						'cycle'	=> 1000,
					);
					break;

				case 'pms':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'message_topics' ) );
					$return = array(
						'name'	=> 'Personal Conversations',
						'rows'	=> $count['count'],
						'cycle'	=> 1000,
					);
					break;

				case 'ranks':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'titles' ) );
					$return = array(
						'name'	=> 'Ranks',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'attachments_type':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'attachments_type' ) );
					$return = array(
						'name'	=> 'File Types',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'attachments':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'attachments', 'where' => "attach_rel_module='post' OR attach_rel_module='msg'" ) );
					$return = array(
						'name'	=> 'Attachments',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'emoticons':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'emoticons' ) );
					$return = array(
						'name'	=> 'Emoticons',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'announcements':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'announcements' ) );
					$return = array(
						'name'	=> 'Announcements',
						'rows'	=> $count['count'],
						'cycle'	=> 1500,
					);
					break;

				case 'badwords':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'badwords' ) );
					$return = array(
						'name'	=> 'Bad Word Filters',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'banfilters':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'banfilters' ) );
					$return = array(
						'name'	=> 'Ban Filters',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'ignored_users':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'ignored_users' ) );
					$return = array(
						'name'	=> 'Ignored Users',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'pfields':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'pfields_data' ) );
					$return = array(
						'name'	=> 'Custom Profile Fields',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'profile_comments':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'member_status_updates' ) );
					$return = array(
						'name'	=> 'Profile Comments and Status Updates',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;
					
				case 'profile_comment_replies':
					$count = $this->DB->buildAndFetch ( array ( 'select' => 'COUNT(*) as count', 'from' => 'member_status_replies' ) );
					$return = array (
						'name'	=> 'Profile Comment/Status Replies',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
				break;

				case 'profile_friends':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'profile_friends' ) );
					$return = array(
						'name'	=> 'Friendships',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'profile_ratings':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'profile_ratings' ) );
					$return = array(
						'name'	=> 'Profile Ratings',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'rc_status':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'rc_status' ) );
					$return = array(
						'name'	=> 'Report Center: Statuses',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'rc_status_sev':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'rc_status_sev' ) );
					$return = array(
						'name'	=> 'Report Center: Severities',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'reputation_index':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'reputation_index' ) );
					$return = array(
						'name'	=> 'Likes / Reputations',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
						'conf'	=> false,
					);
					break;

				case 'rss_import':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'rss_import' ) );
					$return = array(
						'name'	=> 'RSS Imports',
						'rows'	=> $count['count'],
						'cycle'	=> 1,
					);
					break;
					
				case 'rss_export':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'rss_export' ) );
					$return = array(
							'name'	=> 'RSS Exports',
							'rows'	=> $count['count'],
							'cycle'	=> 50,
					);
					break;

				case 'topic_mmod':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'topic_mmod' ) );
					$return = array(
						'name'	=> 'Multi-Moderation',
						'rows'	=> $count['count'],
						'cycle'	=> 100,
					);
					break;

				case 'topic_ratings':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'topic_ratings' ) );
					$return = array(
						'name'	=> 'Topic Ratings',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;
				
				case 'warn_reasons':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) AS count', 'from' => 'members_warn_reasons' ) );
					$return = array(
						'name'	=> 'Warning Reasons',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
					);
					break;

				case 'warn_logs':
					$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'members_warn_logs' ) );
					$return = array(
						'name'	=> 'Warning Logs',
						'rows'	=> $count['count'],
						'cycle'	=> 2000,
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

			$basic = array('section' => $this->app['app_key'], 'key' => $action, 'app' => 'board');

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
				case 'tags':
					return array();
					break;
				
				case 'members':
					return array(
						'tables'	=> array( 'members' => 'member_id', 'pfields_content' => 'member_id', 'profile_portal' => 'pp_member_id', 'rc_modpref' => 'mem_id' ),
						'where'		=> array( 'members' => "member_id <> {$this->memberData['member_id']}", 'pfields_content' => "member_id <> {$this->memberData['member_id']}", 'profile_portal' => "pp_member_id <> {$this->memberData['member_id']}", 'rc_modpref' => "mem_id <> {$this->memberData['member_id']}" )
					);
					break;

				case 'dnames_change':
					return array(
						'tables'	=> array( 'dnames_change' => 'dname_id' )
					);
					break;

				case 'groups':
					return array(
						'tables'	=> array( 'groups' => 'g_id' ),
						'where'		=> array( 'groups' => "g_id NOT IN({$this->settings['admin_group']},{$this->settings['guest_group']},{$this->settings['banned_group']},{$this->settings['member_group']},{$this->settings['auth_group']})" ),
					);
					break;

				case 'forum_perms':
					return array(
						'tables'	=> array( 'forum_perms' => 'perm_id' )
					);
					break;

				case 'forums':
					return array(
						'tables'	=> array( 'forums' => 'id', 'permission_index' => 'perm_id' )
					);
					break;

				case 'moderators':
					return array(
						'tables'	=> array( 'moderators' => 'mid' )
					);
					break;

				case 'custom_bbcode':
					return array(
						'tables'	=> array( 'custom_bbcode' => 'bbcode_id' ),
						'where'		=> array( 'custom_bbcode' => "bbcode_tag NOT IN('" . implode( "','", $this->_defaultBbCodes ) . "')" ),
					);
					break;

				case 'bbcode_media':
					return array(
						'tables'	=> array( 'bbcode_mediatag' => 'mediatag_id' )
					);
					break;

				case 'topic_icons':
					return array();
					break;

				case 'topics':
					return array(
						'tables'	=> array( 'topics' => 'tid', 'core_like' => 'like_rel_id', 'core_like_cache' => 'like_cache_rel_id' )
					);
					break;

				case 'posts':
					return array(
						'tables'	=> array( 'posts' => 'pid', 'reputation_cache' => 'id', 'reputation_index' => 'id', 'content_cache_posts' => 'cache_content_id' ),
						'where'		=> array( 'reputation_cache' => "app = 'forums' AND type = 'pid'", 'reputation_index' => "app = 'forums' AND type = 'pid'" )
					);
					break;

				case 'reputation_cache':
					return array(
						'tables'	=> array( 'reputation_cache' => 'id' )
					);
					break;

				case 'polls':
					return array(
						'tables'	=> array( 'polls' => 'pid' )
					);
					break;

				case 'voters':
					return array(
						'tables'	=> array( 'voters' => 'vid' )
					);
					break;

				case 'pms':
					return array(
						'tables'	=> array( 'message_topics' => 'mt_id', 'message_posts' => 'msg_id', 'message_topic_user_map' => 'map_id' ),
					);
					break;

				case 'pm_posts':
					return array(
						'tables'	=> array( 'message_posts' => 'msg_id' )
					);
					break;

				case 'pm_maps':
					return array(
						'tables'	=> array( 'message_topic_user_map' => 'map_id' )
					);
					break;

				case 'ranks':
					return array(
						'tables'	=> array( 'titles' => 'id' )
					);
					break;

				case 'attachments_type':
					return array(
						'tables'	=> array( 'attachments_type' => 'atype_id' )
					);
					break;

				case 'attachments':
					return array(
						'tables'	=> array( 'attachments' => 'attach_id' ),
						'where'		=> array( 'attachments'	=> "attach_rel_module = 'post'" )
					);
					break;

				case 'emoticons':
					return array(
						'tables'	=> array( 'emoticons' => 'id' )
					);
					break;

				case 'announcements':
					return array(
						'tables'	=> array( 'announcements' => 'announce_id' )
					);
					break;

				case 'ranks':
					return array(
						'tables'	=> array( 'titles' => 'id' )
					);
					break;

				case 'badwords':
					return array(
						'tables'	=> array( 'badwords' => 'wid' )
					);
					break;

				case 'banfilters':
					return array(
						'tables'	=> array( 'banfilters' => 'ban_id' )
					);
					break;

				case 'ignored_users':
					return array(
						'tables'	=> array( 'ignored_users' => 'ignore_id' )
					);
					break;

				case 'pfields':
					return array(
						'tables'	=> array( 'pfields_data' => 'pf_id' )
					);
					break;

				case 'pfields_groups':
					return array(
						'tables'	=> array( 'pfields_groups' => 'pf_group_id' )
					);
					break;

				case 'profile_comments':
					return array(
						'tables'	=> array( 'member_status_updates' => 'status_id' )
					);
					break;
				
				case 'profile_comment_replies':
					return array(
						'tables'	=> array( 'member_status_replies' => 'reply_id' )
					);
				break;

				case 'profile_friends':
					return array(
						'tables'	=> array( 'profile_friends' => 'friends_id' )
					);
					break;

				case 'profile_ratings':
					return array(
						'tables'	=> array( 'profile_ratings' => 'rating_id' )
					);
					break;

				case 'rc_status':
					return array(
						'tables'	=> array( 'rc_status' => 'status' )
					);
					break;

				case 'rc_status_sev':
					return array(
						'tables'	=> array( 'rc_status_sev' => 'id' )
					);
					break;

				case 'reputation_index':
					return array(
						'tables'	=> array( 'reputation_index' => 'id', 'reputation_cache' => 'id' )
					);
					break;

				case 'rss_export':
					return array(
						'tables'	=> array( 'rss_export' => 'rss_export_id' )
					);
					break;

				case 'rss_import':
					return array(
						'tables'	=> array( 'rss_import' => 'rss_import_id' )
					);
					break;

				case 'topic_mmod':
					return array(
						'tables'	=> array( 'topic_mmod' => 'mm_id' )
					);
					break;

				case 'topic_ratings':
					return array(
						'tables'	=> array( 'topic_ratings' => 'rating_id' )
					);
					break;
				
				case 'warn_reasons':
					return array(
						'tables'	=> array( 'members_warn_reasons' => 'wr_id' )
					);
					break;

				case 'warn_logs':
					return array(
						'tables'	=> array( 'members_warn_logs' => 'wl_id' )
					);
					break;
				
				case 'forum_tracker':
				case 'tracker':
					return array();
				break;

				default:
					$this->error('There is a problem with the converter: bad truncate command ('.$action.')');
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
				case 'forums':
					return array('addfield' => array('forums', 'conv_parent', 'varchar(45)'));
					break;

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
			# Added the "return false" to avoid errors while converting reports from deleted forums/topics/posts/pms/members

			switch ($type)
			{
				case 'post':
					if ( $this->getLink($report['exdat1'], 'forums', true) || $this->getLink($report['exdat2'], 'topics', true) || $this->getLink($report['exdat3'], 'posts', true) )
					{
						return false;
					}
					$report['exdat1'] = $this->getLink($report['exdat1'], 'forums');
					$report['exdat2'] = $this->getLink($report['exdat2'], 'topics');
					$report['exdat3'] = $this->getLink($report['exdat3'], 'posts');
					$report['url'] = "/index.php?showtopic={$report['exdat2']}&amp;view=findpost&amp;p={$report['exdat3']}";
					$report['seotemplate'] = 'showtopic';
					break;

				case 'pm':
					if ( $this->getLink($report['exdat1'], 'pms', true) || $this->getLink($report['exdat2'], 'pm_posts', true) )
					{
						return false;
					}
					$report['exdat1'] = $this->getLink($report['exdat1'], 'pms');
					$report['exdat2'] = $this->getLink($report['exdat2'], 'pm_posts');
					$report['url'] = "/index.php?app=members&amp;module=messaging&amp;section=view&amp;do=showConversation&amp;topicID={$report['exdat1']}";
					break;

				case 'member':
					if ( $this->getLink($report['exdat1'], 'members', true) )
					{
						return false;
					}
					$report['exdat1'] = $this->getLink($report['exdat1'], 'members');
					$report['url'] = "/index.php?showuser={$report['exdat1']}";
					break;
			}
			return $report;
		}

		/**
		 * Convert a forum
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	array 		Permissions index data
		 * @return 	boolean		Skip getLink call? true or false (NB: if this is set to true you MUST pass parent_id to the $info array)
		 **/
		public function convertForum($id, $info, $perms = array(), $skip_global_link=false)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No Forum ID number provided');
				return false;
			}
			if (!$info['name'])
			{
				$this->logError($id, 'No name provided');
				return false;
			}
			
			if (!$info['parent_id'])
			{
				// Changed 05/29/2013 by Ryan - if not parent ID is passed, just default to saving as a category (Parent ID -1)
				// $this->logError($id, 'No parent ID number provided');
				// return false;
				$info['parent_id'] = -1;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			// These things will be fixed on rebuild - probably best to have them blank now to prevent confusion
			unset($info['last_poster_id']);
			unset($info['last_id']);
			
			// We need to sort out the parent id
			if (($info['parent_id'] != -1) && ($skip_global_link === false))
			{
				$parent = $this->getLink($info['parent_id'], 'forums');
				if ($parent)
				{
					$info['parent_id'] = $parent;
				}
				else
				{
					$info['conv_parent'] = $info['parent_id'];
					unset($info['parent_id']);
				}
			}

			$info['permission_showtopic'] = 0;
			
			if ( $info['parent_id'] == -1 ) $info['permission_showtopic'] = 1;

			// Make sure we don't have any fields we shouldn't have
			foreach (array('perm_id', 'app', 'perm_type', 'perm_type_id', 'perm_view', 'perm_2', 'perm_3', 'perm_4', 'perm_5', 'perm_6', 'perm_7', 'owner_only', 'friend_only', 'authorized_users') as $unset)
			{
				unset($info[$unset]);
			}

			// Make sure we have a cutoff date
			$info['prune'] = isset($info['prune']) ? $info['prune'] : 100;

			// Make sure we use BBCode
			$info['use_ibc'] = isset($info['use_ibc']) ? intval($info['use_ibc']) : 1;

			// MSSQL makes me want to cry...
			$info['min_posts_view'] = $info['min_posts_view'] ? intval($info['min_posts_view']) : 0;
			$info['min_posts_post'] = $info['min_posts_post'] ? intval($info['min_posts_post']) : 0;
			
			// Post count increment
			$info['inc_postcount']	= isset($info['inc_postcount']) ? intval($info['inc_postcount']) : 1;
			
			// Strict concerns le sigh.
			$info['topics'] = intval( $info['topics'] );
			$info['posts']  = intval( $info['posts'] );
			$info['preview_posts'] = intval( $info['preview_posts'] );
			$info['position'] = intval( $info['position'] );
			
			// Legacy columns
			unset ( $info['status'] );
			unset( $info['quick_reply'] );
			
			if ( $this->usingExtendedInserts )
			{
				// Primary IDs are no longer 'links'.
				$info['id'] = $id;

				// And do it!
				$this->extendedInserts['forums'][] = $this->DB->compileInsertString( $info );
				$inserted_id = $id;
			}
			else
			{
				unset( $info['id'] );
				
				$this->DB->insert('forums', $info);
				$inserted_id = $this->DB->getInsertId();
				
				$this->addLink( $inserted_id, $id, 'forums' );
			}
			
			//-----------------------------------------
			// Add permissions entry
			//-----------------------------------------

			foreach ($perms as $key => $value)
			{
				if ($value != '*')
				{
					$save = array();
					foreach (explode(',', $value) as $pset)
					{
						if ($pset)
						{
							$save[] = $this->getLink($pset, 'forum_perms');
						}
					}
					$perms[$key] = implode(',', $save);
				}
			}

			$this->addToPermIndex('forum', $inserted_id, $perms, $id);

			//-----------------------------------------
			// Sort out children
			//-----------------------------------------

			$this->DB->update('forums', array('parent_id' => $inserted_id, 'conv_parent' => 0), "conv_parent='$id'");

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

			if (!$info['forum_id'])
			{
				$this->logError($id, 'No forum ID number provided');
				return false;
			}
			if (!$info['member_name'] and !$info['group_name'])
			{
				$this->logError($id, 'No member or group name provided');
				return false;
			}
			if (!$info['member_id'] and !$info['group_id'])
			{
				$this->logError($id, 'No member or group ID number provided');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			// Convert those forum ids
			$exploded = explode(',', $info['forum_id']);
			foreach ($exploded as $forum_id)
			{
				if (!$forum_id)
				{
					continue;
				}
				$linked[] = $this->getLink($forum_id, 'forums');
			}
			if (empty($linked))
			{
				$this->logError($id, 'No valid forum ID numbers found');
				return false;
			}
			$info['forum_id'] = implode(',', $linked);

			// Is this a member or a group?
			if ($info['member_id'] and $info['member_id'] != -1)
			{
				$info['member_id'] = $this->getLink($info['member_id'], 'members');
				unset($info['group_id']);
				unset($info['group_name']);
			}
			else
			{
				$info['group_id'] = $this->getLink($info['group_id'], 'groups');
				$info['member_id'] = -1;
				$info['member_name'] = -1;
			}

			unset($info['mid']);
			unset($info['edit_user']);
			
			$this->DB->insert( 'moderators', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'moderators');

			return true;
		}


		/**
		 * Convert a custom bbcode
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	string 		How to handle duplicates ('local' or 'remote')
		 * @return 	boolean		Success or fail
		 **/
		public function convertBBCode($id, $info, $dupes)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['bbcode_title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['bbcode_tag'])
			{
				$this->logError($id, 'No tag provided');
				return false;
			}
			if (!$info['bbcode_replace'] and !$info['bbcode_php_plugin'])
			{
				$this->logError($id, 'No replacement provided');
				return false;
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$this->DB->build( array( 'select' => 'bbcode_id, bbcode_tag, bbcode_aliases', 'from' => 'custom_bbcode' ) );
			$codeRes = $this->DB->execute();
			
			while ($row = $this->DB->fetch( $codeRes ))
			{

				$aliases = array();
				$aliases = explode(',', $row['bbcode_aliases']);
				
				if ($row['bbcode_tag'] == $info['bbcode_tag'] || in_array($info['bbcode_tag'], $aliases)) {
					
					if ($dupes == 'local')
					{
						return false;
					}
					else
					{
						$this->DB->delete('custom_bbcode', "bbcode_id={$row['bbcode_id']}");
					}
					
				}
				
			}
			
			// Strip spaces from tag
			$info['bbcode_tag'] = str_replace(' ', '', $info['bbcode_tag']);

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			$info['bbcode_image'] = isset( $info['bbcode_image'] ) ? $info['bbcode_image'] : '';

			unset($info['bbcode_id']);
			
			// dropped columns
			unset($info['bbcode_parse']);
			
			$this->DB->insert( 'custom_bbcode', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'custom_bbcode');

			return true;
		}

		/**
		 * Convert a [media] option
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	string 		How to handle duplicates ('local' or 'remote')
		 * @return 	boolean		Success or fail
		 **/
		public function convertMediaTag($id, $info, $dupes)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, '(MEDIA) No ID number provided');
				return false;
			}
			if (!$info['mediatag_name'])
			{
				$this->logError($id, '(MEDIA) No title provided');
				return false;
			}
			if (!$info['mediatag_match'])
			{
				$this->logError($id, '(MEDIA) No match provided');
				return false;
			}
			if (!$info['mediatag_replace'])
			{
				$this->logError($id, '(MEDIA) No replacement provided');
				return false;
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$dupe = $this->DB->buildAndFetch( array( 'select' => 'mediatag_id', 'from' => 'bbcode_mediatag', 'where' => "mediatag_match = '{$info['mediatag_match']}'" ) );
			if ($dupe)
			{
				if ($dupes == 'local')
				{
					return false;
				}
				else
				{
					$this->DB->delete('bbcode_mediatag', "mediatag_id={$dupe['mediatag_id']}");
				}
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			unset($info['mediatag_id']);
			$this->DB->insert( 'bbcode_mediatag', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'bbcode_mediatag');

			return true;
		}


		/**
		 * Convert a topic
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param	boolean		Load member IDs from parent app
		 * @return 	boolean		Success or fail
		 **/
		public function convertTopic($id, $info, $parent=false)
		{
			//-----------------------------------------
			// We don't bother with shadow topics
			//-----------------------------------------

			if ($info['topic_status'] == 'link')
			{
				continue;
			}

			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No Topic ID number provided');
				return false;
			}
			if (!$info['title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['forum_id'])
			{
				$this->logError($id, 'No forum ID number provided');
				return false;
			}

			//-----------------------------------------
			// Build SEO title
			//-----------------------------------------
			
			if (!$info['title_seo'])
			{
				$info['title_seo'] = IPSText::makeSeoTitle($info['title']);
			}
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------

			$info['approved']	= isset($info['approved']) ? $info['approved'] : 1;
			$info['pinned']		= isset($info['pinned']) ? $info['pinned'] : 0;


			//-----------------------------------------
			// Link
			//-----------------------------------------

			$info['starter_id'] = ($info['starter_id']) ? intval($this->getLink($info['starter_id'], 'members')) : 0;
			$info['last_poster_id'] = ($info['last_poster_id']) ? intval($this->getLink($info['last_poster_id'], 'members')) : 0;
			$info['forum_id'] = $this->getLink($info['forum_id'], 'forums');
			
			$info['posts'] = intval( $info['posts'] );
			$info['views'] = intval( $info['views'] );
			
			if (!$info['forum_id'])
			{
				$this->logError($id, 'Forum link not found');
				return false;
			}
			
			
			if ( $this->usingExtendedInserts )
			{
				// Primary ID
				$info['tid']			= $id;

				// Add to our inserts
				$this->extendedInserts['topics'][] = $this->DB->compileInsertString( $info );
			}
			else
			{
				$this->DB->insert('topics', $info);
				$this->addLink( $this->DB->getInsertId(), $id, 'topics' );
			}
			
			return true;
		}
		
		/**
		 * Convert an archived post
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param	boolean		Load member IDs from parent app
		 * @return 	boolean		Success or fail
		 **/
		public function convertArchivedPost( $id, $info, $parent = false )
		{
			if ( ! $id )
			{
				$this->logError( $id, 'No Post ID number provided' );
				return false;
			}
			
			if ( ! $info['archive_content'] )
			{
				$this->logError( $id, 'No post provided' );
				return false;
			}
			
			if ( ! $info['archive_topic_id'] )
			{
				$this->logError( $id, 'No topic ID provided (Archived Post)' );
				return false;
			}
			
			// Convert to entities
			$info['archive_content'] = str_replace( "\\", "&#092;", $info['archive_content'] );
			
			// Set up our links.
			$info['archive_author_id']		= ( $info['archive_author_id'] ? $this->getLink( $info['archive_author_id'], 'members', true ) : 0);
			$info['archive_topic_id']		= $this->getLink( $info['archive_topic_id'], 'topics', true );
			$info['archive_forum_id']		= $this->getLink( $info['archive_forum_id'], 'forums', true );
			
			// Strict mode stuff. Basically just being pre-emptive and intval all int() columns.
			$info['archive_author_id']		= intval( $info['archive_author_id'] );
			$info['archive_content_date']	= intval( $info['archive_content_date'] );
			$info['archive_queued']			= intval( $info['archive_queued'] );
			$info['archive_topic_id']		= intval( $info['archive_topic_id'] );
			$info['archive_is_first']		= intval( $info['archive_is_first'] );
			$info['archive_bwoptions']		= intval( $info['archive_bwoptions'] );
			$info['archive_html_mode']		= intval( $info['archive_html_mode'] );
			$info['archive_show_signature']	= intval( $info['archive_show_signature'] );
			$info['archive_show_emoticons']	= intval( $info['archive_show_emoticons'] );
			$info['archive_show_edited_by'] = intval( $info['archive_show_edited_by'] );
			$info['archive_edit_time']		= intval( $info['archive_edit_time'] );
			$info['archive_added']			= intval( $info['archive_added'] );
			$info['archive_restored']		= intval( $info['archive_restored'] );
			$info['archive_forum_id']		= intval( $info['archive_forum_id'] );
			
			// Do we have a topic?
			if ( ! $info['archive_topic_id'] )
			{
				$this->logError( $id, 'Topic not found (Archived Posts)' );
				return false;
			}
			
			// Re-assign rep_points so the system doesn't try to insert it into the table.
			$rep_points = $info['rep_points'];
			unset( $info['rep_points'] );
			
			// Cue bug generating code... err, I mean extended inserts.
			if ( $this->usingExtendedInserts )
			{
				$info['archive_id'] = $id;
				
				$this->extendedInserts['forums_archive_posts'][] = $this->DB->compileInsertString( $info );
				$inserted_id = $id;
			}
			else
			{
				// @todo - Right now we only support converting into the local database. We need to expand to allow for remote databases as well.
				$this->DB->insert( 'forums_archive_posts', $info );
				$inserted_id = $this->DB->getInsertId();
				
				$this->addLink( $inserted_id, $id, 'posts' );
			}
			
			// "We don't have reputation."
			if ( $rep_points )
			{
				$rep_cache = array(
					'app'			=> 'forums',
					'type'			=> 'pid',
					'type_id'		=> $inserted_id,
					'rep_points'	=> $rep_points,
				);
				
				if ( $this->usingExtendedInserts )
				{
					// Add to our inserts
					$this->extendedInserts['reputation_cache'][] = $this->DB->compileInsertString( $rep_cache );
				}
				else
				{
					$this->DB->insert('reputation_cache', $rep_cache);
				}
			}
			
			return true;
		}

		/**
		 * Convert a post
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param	boolean		Load member IDs from parent app
		 * @return 	boolean		Success or fail
		 **/
		public function convertPost($id, $info, $parent=false, $skipLink = false)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No Post ID number provided');
				return false;
			}
			if (!$info['post'])
			{
				$this->logError($id, 'No post provided');
				return false;
			}
			if (!$info['topic_id'])
			{
				$this->logError($id, 'No topic ID provided (Post)');
				return false;
			}
			
			$info['post_key'] = md5( microtime() );
			
			// Convert to entities
			$info['post'] = str_replace("\\", "&#092;", $info['post']);

			$info['author_id'] = ($info['author_id']) ? $this->getLink($info['author_id'], 'members', true) : 0;
			$info['topic_id'] = $this->getLink($info['topic_id'], 'topics', true);

			// Fix integers since STRICT likes to complain...
			$info['author_id'] = intval($info['author_id']);
			$info['edit_time']	= intval( $info['edit_time'] );
			$info['post_date']	= intval( $info['post_date'] );
			
			if ( ! $info['post_key'] )
			{
				$info['post_key'] = md5( $id . 'post' );
			}

			unset($info['icon_id']);
			unset($info['post_title']);

			if (!$info['topic_id'])
			{
				$this->logError($id, 'Topic not found.');
				return FALSE;
			}
			

			$rep_points = $info['rep_points'];
			unset($info['rep_points']);
			
			if ( $this->usingExtendedInserts )
			{
				// Post ID
				$info['pid'] = $id;
			
				// Add to our inserts
				$this->extendedInserts['posts'][] = $this->DB->compileInsertString( $info );
				$inserted_id = $id;
			}
			else
			{
				$this->DB->insert('posts', $info);
				$inserted_id = $this->DB->getInsertId();
				
				if ( !$skipLink )
				{
					$this->addLink( $inserted_id, $id, 'posts' );
				}
			}
			
			//-----------------------------------------
			// We've got a reputation to think about here!
			//-----------------------------------------

			if ($rep_points)
			{
				$rep_cache = array(
					'app' => 'forums',
					'type' => 'pid',
					'type_id' => $inserted_id,
					'rep_points' => $rep_points,
					);
				
				if ( $this->usingExtendedInserts )
				{
					// Add to our inserts
					$this->extendedInserts['reputation_cache'][] = $this->DB->compileInsertString( $rep_cache );
				}
				else
				{
					$this->DB->insert('reputation_cache', $rep_cache);
				}
			}

			return true;
		}

		/**
		 * Convert a poll
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param	boolean		Load member IDs from parent app
		 * @return 	boolean		Success or fail
		 **/
		public function convertPoll($id, $info, $parent=false)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['tid'])
			{
				$this->logError($id, 'No topic ID number provided');
				return false;
			}
			// if (!$info['starter_id'])
			// {
			// 	$this->logError($id, 'No author ID number provided');
			// 	return false;
			// }

			// The title '0' triggered the old check, do not revert to !
			if ( $info['poll_question'] == '' )
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

			$info['tid'] = $this->getLink($info['tid'], 'topics');
			$info['starter_id'] = ($info['starter_id']) ? $this->getLink($info['starter_id'], 'members', false, $parent) : 0;
			$info['forum_id'] = $this->getLink($info['forum_id'], 'forums');

			unset($info['pid']);
			$this->DB->insert( 'polls', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'polls');

			return true;
		}

		/**
		 * Convert a poll voter
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param	boolean		Load member IDs from parent app
		 * @return 	boolean		Success or fail
		 **/
		public function convertPollVoter($id, $info, $parent=false)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, '(VOTE) No ID number provided');
				return false;
			}
			if (!$info['tid'])
			{
				$this->logError($id, '(VOTE) No topic ID number provided');
				return false;
			}
			if (!$info['member_id'])
			{
				$this->logError($id, '(VOTE) No voter ID number provided');
				return false;
			}
			if (!$info['member_choices'])
			{
				$this->logError($id, '(VOTE) No answers provided');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			$info['tid'] = $this->getLink($info['tid'], 'topics');
			$info['member_id'] = $this->getLink($info['member_id'], 'members', false, $parent);
			$info['forum_id'] = $this->getLink($info['forum_id'], 'forums');

			if (!$info['tid'])
			{
				$this->logError($id, '(VOTE) Topic not found.');
				return false;
			}

			if (!$info['member_id'])
			{
				$this->logError($id, '(VOTE) Member not found.');
				return false;
			}

			if (!$info['forum_id'])
			{
				$this->logError($id, '(VOTE) Forum not found.');
				return false;
			}

			unset($info['vid']);
			$this->DB->insert( 'voters', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'voters');

			return true;
		}

		/**
		 * Convert a personal conversation
		 *
		 * @access	public
		 * @param 	array		Data to insert to topics table
		 * @param 	array 		Data to insert to posts table
		 * @param 	array 		Data to insert to maps table
		 * @return 	boolean		Success or fail
		 **/
		public function convertPM($topic, $posts, $maps)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$topic['mt_id'])
			{
				$this->logError($topic['mt_id'], 'No ID number provided');
				return false;
			}
			if (!$topic['mt_title'])
			{
				$this->logError($topic['mt_id'], 'No title provided');
				return false;
			}
			// if (!$topic['mt_starter_id'])
			// {
			// 	$this->logError($topic['mt_id'], 'No starter ID provided');
			// 	return false;
			// }
			// if (!$topic['mt_to_member_id'])
			// {
			// 	$this->logError($topic['mt_id'], 'No recipient ID provided');
			// 	return false;
			// }
			

			//-----------------------------------------
			// Links
			//-----------------------------------------
			$oldMemberID			= $topic['mt_to_member_id'] ? $topic['mt_to_member_id'] : 'NULL';
			$topic['mt_starter_id'] = ($topic['mt_starter_id']) ? $this->getLink($topic['mt_starter_id'], 'members', true) : 0;
			$topic['mt_to_member_id'] = ($topic['mt_to_member_id']) ? $this->getLink($topic['mt_to_member_id'], 'members', true) : 0;
			
			$converted = array();
			
			if( $topic['mt_invited_members'] )
			{
				$invitees = unserialize( $topic['mt_invited_members'] );
				
				if( !empty( $invitees ) )
				{
					foreach( $invitees as $invitee )
					{
						$converted[] =  $this->getLink( $invitee, 'members', true );
					}
				}
			}
			
			$topic['mt_invited_members'] = serialize( $converted );

			if (!$topic['mt_starter_id'])
			{
				$this->logError($topic['mt_id'], 'Starter not found.');
				return false;
			}

			if (!$topic['mt_to_member_id'])
			{
				$this->logError($topic['mt_id'], 'Recipient (' . $oldMemberID . ') not found.');
				return false;
			}
			
			//-----------------------------------------
			// Insert topic
			//-----------------------------------------

			$tid = $topic['mt_id'];
			
			if ( ! $this->usingExtendedInserts )
			{
				unset($topic['mt_id']);
				$this->DB->insert( 'message_topics', $topic );
				$topic_id = $this->DB->getInsertId();

				$this->addLink($topic_id, $tid, 'pms');
			}
			else
			{
				$topic_id = $tid;
			}
			
			//-----------------------------------------
			// Loop through the posts
			//-----------------------------------------

			foreach ($posts as $post)
			{
				//-----------------------------------------
				// Make sure we have everything we need
				//-----------------------------------------

				if (!$post['msg_id'])
				{
					$this->logError($post['msg_id'], '(PM POST) No ID number provided');
					continue;
				}
				if (!$post['msg_post'])
				{
					$this->logError($post['msg_id'], '(PM POST) No post provided');
					continue;
				}
				// if (!$post['msg_author_id'])
				// {
				// 	$this->logError($post['msg_id'], '(PM POST) No author ID number provided');
				// 	continue;
				// }
				
				if ( ! $post['msg_post_key'] )
				{
					$post['msg_post_key'] = md5( $post['msg_id'] . 'msg' );
				}

				//-----------------------------------------
				// Insert
				//-----------------------------------------
				$post['msg_topic_id'] = $topic_id;
				$post['msg_author_id'] = ($post['msg_author_id']) ? $this->getLink($post['msg_author_id'], 'members', true) : 0;

				if (!$post['msg_author_id'])
				{
					$this->logError($post['msg_id'], 'Author not found.');
					return false;
				}

				$pid = $post['msg_id'];
				
				if ( $this->usingExtendedInserts )
				{
					$this->extendedInserts['message_posts'][] = $this->DB->compileInsertString( $post );
					$inserted_id = $pid;
				}
				else
				{
					unset($post['msg_id']);
					$this->DB->insert( 'message_posts', $post );
					$inserted_id = $this->DB->getInsertId();

					$this->addLink($inserted_id, $pid, 'pm_posts');
				}
				
				//-----------------------------------------
				// Get first / last count
				//-----------------------------------------
				if ($post['msg_is_first_post'])
				{
					$first = $inserted_id;
				}
				$last = $inserted_id;
			}

			//-----------------------------------------
			// We need maps
			//-----------------------------------------
			foreach ($maps as $map)
			{
				//-----------------------------------------
				// Make sure we have everything we need
				//-----------------------------------------
				if (!$map['map_user_id'])
				{
					$this->logError($map['map_id'], '(PM MAP) No user ID number provided');
					continue;
				}
				if (!$map['map_topic_id'])
				{
					$this->logError($map['map_id'], '(PM MAP) No topic ID number provided');
					continue;
				}
				
				if (!$map['map_last_topic_reply'])
				{
					$this->logError($map['map_id'], '(PM MAP) Last topic reply not provided');
					continue;
				}
				
				if (!$map['map_folder_id'])
				{
					$map['map_folder_id'] = 'myconvo';
				}

				//-----------------------------------------
				// Insert
				//-----------------------------------------

				$map['map_user_id'] = $this->getLink($map['map_user_id'], 'members', true);
				$map['map_topic_id'] = $this->getLink($map['map_topic_id'], 'pms', true);
				

				if (!$map['map_user_id'])
				{
					$this->logError($map['map_id'], '(PM MAP) No user ID link could be found');
					continue;
				}
				if (!$map['map_topic_id'])
				{
					$this->logError($map['map_id'], '(PM MAP) No topic ID link could be found');
					continue;
				}
				
				// Check if map already exists.
				$existingMap = $this->DB->buildAndFetch ( array (
					'select'	=> '*',
					'from'		=> 'message_topic_user_map',
					'where'		=> 'map_user_id = ' . $map['map_user_id'] . ' AND  map_topic_id = ' . $map['map_topic_id']
				) );
				
				if ( $existingMap )
				{
					$this->logError ( $existingMap['map_id'], '(PM MAP) PM Map Already Exists.' );
					continue;
				}
				
				if ( $this->usingExtendedInserts )
				{
					$this->extendedInserts['message_topic_user_map'][] = $this->DB->compileInsertString( $map );
					$inserted_id = $map['map_id'];
				}
				else
				{
					// Store in memory for the addLink call later.
					$map_id = $map['map_id'];
					unset($map['map_id']);
					$this->DB->insert( 'message_topic_user_map', $map );
					$inserted_id = $this->DB->getInsertId();

					$this->addLink($inserted_id, $map_id, 'pm_maps');
				}
			}

			if ( ! $this->usingExtendedInserts )
			{
				//-----------------------------------------
				// Update topic
				//-----------------------------------------
				$this->DB->update( 'message_topics', array('mt_last_msg_id' => intval($last), 'mt_first_msg_id' => intval($first)), "mt_id={$topic_id}" );
			}
			else
			{
				$topic['mt_last_msg_id'] = intval($last);
				$this->extendedInserts['message_topics'][] = $this->DB->compileInsertString( $topic );
				$topic_id = $tid;
			}
			
			return true;
		}

		/**
		 * Convert a rank
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	string 		How to handle duplicates ('local' or 'remote')
		 * @return 	boolean		Success or fail
		 **/
		public function convertRank($id, $info, $dupes)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$dupe = $this->DB->buildAndFetch( array( 'select' => 'id', 'from' => 'titles', 'where' => "posts = '{$info['posts']}'" ) );
			if ($dupe)
			{
				if ($dupes == 'local')
				{
					return false;
				}
				else
				{
					$this->DB->delete('titles', "id={$dupe['id']}");
				}
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			unset($info['id']);
			$this->DB->insert( 'titles', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'ranks');

			return true;
		}

		/**
		 * Convert a mimetype
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertAttachType($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['atype_extension'])
			{
				$this->logError($id, 'No extension provided');
				return false;
			}
			if (!$info['atype_mimetype'])
			{
				$this->logError($id, 'No mime type provided');
				return false;
			}
			if (!$info['atype_img'] or !file_exists(DOC_IPS_ROOT_PATH.$info['atype_img']))
			{
				$info['atype_img'] = 'style_extra/mime_types/unknown.gif';
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			if ($this->DB->buildAndFetch( array( 'select' => 'atype_id', 'from' => 'attachments_type', 'where' => "atype_extension = '{$info['atype_extension']}'" ) ))
			{
				// $this->logError($id, 'Type already exists');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			unset($info['atype_id']);
			$this->DB->insert( 'attachments_type', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'attachments_type');

			return true;
		}

		/**
		 * Convert an emoticon
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	string 		How to handle duplicates ('local' or 'remote')
		 * @param 	string 		Path to emoticons folder
		 * @return 	boolean		Success or fail
		 **/
		public function convertEmoticon($id, $info, $dupes, $path)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['typed'])
			{
				$this->logError($id, 'No code provided');
				return false;
			}
			if (!$info['image'])
			{
				$this->logError($id, 'No code provided');
				return false;
			}
			if (!$info['emo_set'])
			{
				$info['emo_set'] = 'default';
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$dupe = $this->DB->buildAndFetch( array( 'select' => 'id', 'from' => 'emoticons', 'where' => "typed = '".addslashes($info['typed'])."' AND emo_set='{$info['emo_set']}'" ) );
			if ($dupe)
			{
				if ($dupes == 'local')
				{
					return false;
				}
				else
				{
					$this->DB->delete('emoticons', "id={$dupe['id']}");
				}
			}

			//-----------------------------------------
			// Move the file
			//-----------------------------------------

			$emo_dir = DOC_IPS_ROOT_PATH.'public/style_emoticons/'.$info['emo_set'];

			// Check we have a path
			if (!is_dir($emo_dir) and !mkdir($emo_dir))
			{
				$this->logError($id, 'Bad directory:'.$emo_dir);
				return false;
			}

			$this->moveFiles(array($info['image']), $path, $emo_dir);

			// Convert special chars
			$info['typed'] = IPSText::htmlspecialchars($info['typed']);
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------

			unset($info['id']);
			$this->DB->insert( 'emoticons', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'emoticons');

			return true;
		}

		/**
		 * Convert an announcement
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertAnnouncement($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['announce_title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['announce_post'])
			{
				$this->logError($id, 'No content provided');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			// Linky-loo
			if ($info['announce_forum'] != '*')
			{
				foreach (explode(',', $info['announce_forum']) as $fid)
				{
					$forums[] = $this->getLink($fid, 'forums');
				}
				$info['announce_forum'] = implode(',', $forums);
			}
			$info['announce_member_id'] = $this->getLink($info['announce_member_id'], 'members');

			// Go go go
			unset($info['announce_id']);
			$this->DB->insert( 'announcements', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'announcements');

			return true;
		}

		/**
		 * Convert bad words
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertBadword($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['type'])
			{
				$this->logError($id, 'No word provided');
				return false;
			}
			if (!$info['swop'])
			{
				$this->logError($id, 'No replacement provided');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			unset($info['wid']);
			$this->DB->insert( 'badwords', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'badwords');

			return true;
		}

		/**
		 * Convert ban filters
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertBan($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['ban_type'])
			{
				$this->logError($id, 'No type provided');
				return false;
			}
			if (!$info['ban_content'])
			{
				$this->logError($id, 'No content provided');
				return false;
			}
			if ( !$info['ban_date'] )
			{
				$info['ban_date'] = time();
			}
			
			unset($info['ban_nocache']);
			unset($info['ban_id']);
			$this->DB->insert( 'banfilters', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'banfilters');

			return true;
		}

		/**
		 * Convert display name history
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertDname($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['dname_member_id'])
			{
				$this->logError($id, 'No member ID provided');
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			$info['dname_member_id'] = $this->getLink($info['dname_member_id'], 'members');

			unset($info['dname_id']);
			$this->DB->insert( 'dnames_change', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'dnames_change');

			return true;
		}

		/**
		 * Convert forum subscription
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertForumSubscription($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------
			$info['member_id'] = $this->getLink($info['member_id'], 'members', true);
			$info['forum_id']  = $this->getLink($info['forum_id'], 'forums', true);
			
			$this->convertFollow ( array (
				'like_app'			=> 'forums',
				'like_area'			=> 'forums',
				'like_rel_id'		=> $info['forum_id'],
				'like_member_id'	=> $info['member_id'],
				'like_notify_freq'	=> $info['forum_track_type'],
				'like_is_anon'		=> ( isset( $info['like_is_anon'] ) ) ? $info['like_is_anon'] : 0,
			) );

			return true;
		}

		/**
		 * Convert topic subscription
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertTopicSubscription($id, $info)
		{
			
			//-----------------------------------------
			// Link
			//-----------------------------------------
			
			$info['member_id'] = $this->getLink($info['member_id'], 'members', true);
			$info['topic_id']  = $this->getLink($info['topic_id'], 'topics', true);
			
			/* @alias */
			$this->convertFollow( array (
				'like_app'			=> 'forums',
				'like_area'			=> 'topics',
				'like_rel_id'		=> $info['topic_id'],
				'like_member_id'	=> $info['member_id'],
				'like_notify_freq'	=> $info['topic_track_type'],
				'like_is_anon'		=> ( isset( $info['like_is_anon'] ) ) ? $info['like_is_anon'] : 0,
			) );
			
			return true;
		}

		/**
		 * Convert ignore
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertIgnore($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			
			$info['ignore_owner_id'] = $this->getLink($info['ignore_owner_id'], 'members');
			$info['ignore_ignore_id'] = $this->getLink($info['ignore_ignore_id'], 'members');
			
			if (!$info['ignore_owner_id'])
			{
				$this->logError($id, 'No owner ID provided');
				return false;
			}
			if (!$info['ignore_ignore_id'])
			{
				$this->logError($id, 'No ignoring ID provided');
				return false;
			}
			
			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------
			
			$dupe = $this->DB->buildAndFetch( array( 'select' => 'ignore_id', 'from' => 'ignored_users', 'where' => "ignore_owner_id = '{$info['ignore_owner_id']}' AND ignore_ignore_id = '{$info['ignore_ignore_id']}'" ) );
				
			if ($dupe)
			{
				$this->addLink($dupe['ignore_id'], $id, 'ignore', 1);
				$inserted_id = $dupe['ignore_id'];
			}
			else
			{
				//-----------------------------------------
				// Insert
				//-----------------------------------------
				$this->DB->insert( 'ignored_users', $info );
				$inserted_id = $this->DB->getInsertId();
				
				//-----------------------------------------
				// Add link
				//-----------------------------------------
				
				$this->addLink($inserted_id, $id, 'ignore');
			}

			return true;
		}

		/**
		 * Convert custom profile field
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertPField($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['pf_title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['pf_key'])
			{
				$this->logError($id, 'No key provided');
				return false;
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$dupe = $this->DB->buildAndFetch( array( 'select' => 'pf_id', 'from' => 'pfields_data', 'where' => "pf_key = '{$info['pf_key']}'" ) );
			
			if ($dupe)
			{
				$this->addLink($dupe['pf_id'], $id, 'pfields', 1);
				$inserted_id = $dupe['pf_id'];
			}
			else
			{
				//-----------------------------------------
				// Insert
				//-----------------------------------------
				$this->DB->insert( 'pfields_data', $info );
				$inserted_id = $this->DB->getInsertId();
				$this->addLink($inserted_id, $id, 'pfields');
			}
			
			//-----------------------------------------
			// Links
			//-----------------------------------------
			
			if( $info['pf_group_id'] )
			{
				$info['pf_group_id'] = $this->getLink($info['pf_group_id'], 'pfields_groups');
			}
			
			//-----------------------------------------
			// We need a column in pfields_content (Only if it doesnt exist)
			//-----------------------------------------
			if ( ! $this->DB->checkForField( "field_$inserted_id", 'pfields_content' ) )
			{
				$this->DB->addField( 'pfields_content', "field_$inserted_id", 'text' );
				$this->DB->optimize( 'pfields_content' );
			}
			
			if ( $dupe ) return false;
			return true;
		}

		/**
		 * Convert custom profile field group
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	boolean 	If true, will return error instead of logging
		 * @return 	boolean		Success or fail
		 **/
		public function convertPFieldGroup($id, $info, $return=false)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$error = '(GROUP) No ID number provided';
				if ($return)
				{
					$this->error($error);
				}
				$this->logError($id, $error);
				return false;
			}
			if (!$info['pf_group_name'])
			{
				$error = '(GROUP) No name provided';
				if ($return)
				{
					$this->error($error);
				}
				$this->logError($id, $error);
				return false;
			}
			if (!$info['pf_group_key'])
			{
				$error = '(GROUP) No key provided';
				if ($return)
				{
					$this->error($error);
				}
				$this->logError($id, $error);
				return false;
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$dupe = $this->DB->buildAndFetch( array( 'select' => 'pf_group_id', 'from' => 'pfields_groups', 'where' => "pf_group_key = '{$info['pf_group_key']}'" ) );
			if ($dupe)
			{
				$this->addLink($dupe['pf_group_id'], $id, 'pfields_groups');
				if ($return)
				{
					return $dupe['pf_group_id'];
				}
				return false;
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			unset($info['pf_group_id']);
			$this->DB->insert( 'pfields_groups', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'pfields_groups');

			if ($return)
			{
				return $inserted_id;
			}
			else
			{
				return true;
			}

		}

		/**
		 * Convert profile comment
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertProfileComment($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if ( !$id )
			{
				$this->logError( $id, 'No ID number provided' );
				return false;
			}
			if ( !$info['status_member_id'] )
			{
				$this->logError( $id, 'No member ID provided' );
				return false;
			}
			if ( !$info['status_author_id'] )
			{
				$this->logError( $id, 'No author ID provided' );
				return false;
			}
			if ( !$info['status_content'] )
			{
				$this->logError( $id, 'No comment provided' );
				return false;
			}
			
			if ( ! $this->usingExtendedInserts )
			{
				unset( $info['status_id'] );
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------
			$info['status_member_id'] = $this->getLink( $info['status_member_id'], 'members' );
			$info['status_author_id'] = $this->getLink( $info['status_author_id'], 'members' );

			$this->DB->insert( 'member_status_updates', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink( $inserted_id, $id, 'profile_comments' );

			return true;
		}
		
		/**
		 * Convert profile comment replies
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertProfileCommentReply($id, $info)
		{
			if ( !$id )
			{
				$this->logError( $id, 'No Reply ID provided.' );
				return false;
			}
			
			if ( !$info['reply_status_id'] )
			{
				$this->logError( $id, 'No Comment/Status ID Provided.' );
				return false;
			}
			
			if ( !$info['reply_member_id'] )
			{
				$this->logError( $id, 'No Reply Member ID provided.' );
				return false;
			}
			
			if ( !$info['reply_content'] )
			{
				$this->logError( $id, 'No Reply Content provided.' );
				return false;
			}
			
			if ( ! $this->usingExtendedInserts )
			{
				unset( $info['reply_id'] );
			}
			
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			$info['reply_status_id']  = $this->getLink( $info['reply_status_id'], 'profile_comments' );
			$info['reply_member_id']  = $this->getLink( $info['reply_member_id'], 'members' );
			
			$this->DB->insert( 'member_status_replies', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			$this->addLink( $inserted_id, $id, 'profile_comment_replies' );
			
			// And we're done!
			return true;
		}


		/**
		 * Convert friends
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertFriend($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, '(FRIENDS) No ID number provided');
				return false;
			}
			if (!$info['friends_member_id'])
			{
				$this->logError($id, '(FRIENDS) No member ID provided');
				return false;
			}
			if (!$info['friends_friend_id'])
			{
				$this->logError($id, '(FRIENDS) No friend ID provided');
				return false;
			}
			
			$info['friends_member_id'] = $this->getLink($info['friends_member_id'], 'members');
			$info['friends_friend_id'] = $this->getLink($info['friends_friend_id'], 'members');

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			if (!$info['friends_member_id'])
			{
				$this->logError($id, '(FRIENDS) No member ID found');
				return false;
			}
			if (!$info['friends_friend_id'])
			{
				$this->logError($id, '(FRIENDS) No friend ID found');
				return false;
			}
			
			$this->DB->insert( 'profile_friends', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'friends');
			
			//-----------------------------------------
			// Recache
			//-----------------------------------------
			
			$classToLoad            = IPSLib::loadLibrary( IPS_ROOT_PATH . '/applications/members/sources/friends.php', 'profileFriendsLib' );
			$profileFriendsLib      = new $classToLoad( ipsRegistry::instance() );

			$profileFriendsLib->recacheFriends( array( 'member_id' => $info['friends_member_id'] ) );
			$profileFriendsLib->recacheFriends( array( 'member_id' => $info['friends_friend_id'] ) );

			return true;
		}

		/**
		 * Convert profile ratings
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertProfileRating($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['rating_for_member_id'])
			{
				$this->logError($id, 'No member ID provided');
				return false;
			}
			if (!$info['rating_by_member_id'])
			{
				$this->logError($id, 'No rater ID provided');
				return false;
			}
			if (!$info['rating_value'])
			{
				$this->logError($id, 'No rating provided');
				return false;
			}
			
			$info['rating_for_member_id'] = $this->getLink( $info['rating_for_member_id'], 'members' );
			$info['rating_by_member_id'] = $this->getLink( $info['rating_by_member_id'], 'members' );
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			$this->DB->insert( 'profile_ratings', $info );
			$inserted_id = $this->DB->getInsertId();
				
			//-----------------------------------------
			// Add link
			//-----------------------------------------
				
			$this->addLink($inserted_id, $id, 'profile_rating');

			return true;
		}

		/**
		 * Convert a report center status
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	string 		How to handle duplicates ('local' or 'remote')
		 * @return 	boolean		Success or fail
		 **/
		public function convertRCStatus($id, $info, $dupes)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$dupe = $this->DB->buildAndFetch( array( 'select' => 'status', 'from' => 'rc_status', 'where' => "title = '{$info['title']}'" ) );
			if ($dupe)
			{
				if ($dupes == 'local')
				{
					$this->addLink($dupe['status'], $id, 'rc_status', 1);
					return false;
				}
				else
				{
					$this->DB->delete('rc_status', "status={$dupe['status']}");
				}
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			unset($info['status']);
			$this->DB->insert( 'rc_status', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'rc_status');

			return true;
		}

		/**
		 * Convert a report center severity
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	string 		How to handle duplicates ('local' or 'remote')
		 * @return 	boolean		Success or fail
		 **/
		public function convertRCSeverity($id, $info, $dupes)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['status'])
			{
				$this->logError($id, 'No status provided');
				return false;
			}
			if (!$info['img'])
			{
				$info['img'] = 'style_extra/report_icons/flag_pink.png';
				$info['is_png'] = '1';
				$info['width'] = '16';
				$info['height'] = '16';
			}

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$dupe = $this->DB->buildAndFetch( array( 'select' => 'id', 'from' => 'rc_status_sev', 'where' => "status = '{$info['status']}' AND points={$info['points']}" ) );
			if ($dupe)
			{
				if ($dupes == 'local')
				{
					$this->addLink($dupe['id'], $id, 'rc_status_sev', 1);
					return false;
				}
				else
				{
					$this->DB->delete('rc_status_sev', "id={$dupe['id']}");
				}
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			unset($info['id']);
			$this->DB->insert( 'rc_status_sev', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'rc_status_sev');

			return true;
		}

		/**
		 * Convert a reputation vote
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertRep($id, $info)
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
			if (!$info['type_id'])
			{
				$this->logError($id, 'No post ID provided');
				return false;
			}
			if (!$info['rep_rating'])
			{
				$this->logError($id, 'No rep provided');
				return false;
			}
			if (!$info['app'] or !$info['type'])
			{
				$info['app'] = 'forums';
				$info['type'] = 'pid';
			}
			
			switch ( $info['type'] )
			{
				case 'pid':
					$info['type_id'] = $this->getLink( $info['type_id'], 'posts' );
					break;
			}
			
			if ( ! $info['type_id'] )
			{
				return false;
			}
			
			// Links yessirree!
			$info['member_id'] 			= $this->getLink( $info['member_id'], 'members' );
			$info['content_member_id'] 	= $info['content_member_id'] ? $this->getLink( $info['content_member_id'], 'members' ) : false;
			
			if ( $info['content_member_id'] )
			{
				// Update the members reputation
				$this->DB->update( 'profile_portal', "pp_reputation_points=pp_reputation_points+" . intval($info['rep_rating']), 'pp_member_id=' . $info['content_member_id'], false, true );
			}
			
			unset( $info['content_member_id'] );
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			$this->DB->insert( 'reputation_index', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'rep');

			return true;
		}

		/**
		 * Convert an RSS export
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertRSSExport($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['rss_export_title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['rss_export_forums'])
			{
				$this->logError($id, 'No forum IDs provided');
				return false;
			}
			if (!$info['rss_export_cache_time'])
			{
				$info['rss_export_cache_time'] = 30;
			}
			if (!$info['rss_export_sort'])
			{
				$info['rss_export_sort'] = 'DESC';
			}
			if (!$info['rss_export_order'])
			{
				$info['rss_export_order'] = 'start_date';
			}
			
			//-----------------------------------------
			// Links
			//-----------------------------------------
			
			$forumIds = array();
			foreach ( explode(',', $info['rss_export_forums']) as $forum)
			{
				$forumIds[] = $this->getLink( $forum, 'forums' );
			}

			$info['rss_export_forums'] = implode( ",", $forumIds);
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			
			$this->DB->insert( 'rss_export', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'rss_export');
			
			return true;
		}

		/**
		 * Convert an RSS import
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @param 	string 		How to handle duplicates ('local' or 'remote')
		 * @return 	boolean		Success or fail
		 **/
		public function convertRSSImport($id, $info, $dupes)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['rss_import_title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['rss_import_url'])
			{
				$this->logError($id, 'No URL provided');
				return false;
			}
			if (!$info['rss_import_forum_id'])
			{
				$this->logError($id, 'No forum ID provided');
				return false;
			}
			if (!$info['rss_import_mid'])
			{
				$this->logError($id, 'No member ID provided');
				return false;
			}
			if (!$info['rss_import_pergo'])
			{
				$info['rss_import_pergo'] = 10;
			}
			if (!$info['rss_import_time'])
			{
				$info['rss_import_time'] = 200;
			}
			
			//-----------------------------------------
			// Links
			//-----------------------------------------
			
			$info['rss_import_mid'] = $this->getLink($info['rss_import_mid'], 'members');
			$info['rss_import_forum_id'] = $this->getLink($info['rss_import_forum_id'], 'forums');

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$dupe = $this->DB->buildAndFetch( array( 'select' => 'rss_import_id', 'from' => 'rss_import', 'where' => "rss_import_url = '{$info['rss_import_url']}'" ) );
			if ($dupe)
			{
				if ($dupes == 'local')
				{
					return false;
				}
				else
				{
					$this->DB->delete('rss_import', "id={$dupe['rss_import_id']}");
					$this->DB->delete('rss_imported', "rss_imported_impid={$dupe['rss_import_id']}");
				}
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------
			$this->DB->insert( 'rss_import', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'rss_import');
			
			return true;
		}

		/**
		 * Convert an RSS import
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertRSSImportLog($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, '(LOG) No ID number provided');
				return false;
			}
			if (!$info['rss_imported_guid'])
			{
				$this->logError($id, '(LOG) No GUID provided');
				return false;
			}
			if (!$info['rss_imported_tid'])
			{
				$this->logError($id, 'No topic ID provided');
				return false;
			}
			if (!$info['rss_imported_impid'])
			{
				$info['rss_imported_impid'] = $id;
			}
			
			//-----------------------------------------
			// Links
			//-----------------------------------------
				
			$info['rss_imported_tid'] = $this->getLink($info['rss_imported_tid'], 'topics');
			$info['rss_imported_impid'] = $this->getLink($info['rss_imported_impid'], 'rss_import');

			//-----------------------------------------
			// Handle duplicates
			//-----------------------------------------

			$this->DB->delete('rss_imported', "rss_imported_guid='{$info['rss_imported_guid']}'");

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			$this->DB->insert( 'rss_imported', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'rss_import_log');

			return true;
		}

		/**
		 * Convert multimods
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertMultiMod($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['mm_title'])
			{
				$this->logError($id, 'No title provided');
				return false;
			}
			if (!$info['topic_state'])
			{
				$info['topic_state'] = 'leave';
			}
			if (!$info['topic_pin'])
			{
				$info['topic_pin'] = 'leave';
			}
			if (!$info['mm_forums'])
			{
				$info['mm_forums'] = '*';
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------
			$this->DB->insert( 'topic_mmod', $info );
			$inserted_id = $this->DB->getInsertId();
			
			//-----------------------------------------
			// Add link
			//-----------------------------------------
			
			$this->addLink($inserted_id, $id, 'multimod');

			return true;
		}

		/**
		 * Convert topic ratings
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertTopicRating($id, $info)
		{
			//-----------------------------------------
			// Make sure we have everything we need
			//-----------------------------------------

			if (!$id)
			{
				$this->logError($id, 'No ID number provided');
				return false;
			}
			if (!$info['rating_tid'])
			{
				$this->logError($id, 'No topic ID provided');
				return false;
			}
			if (!$info['rating_member_id'])
			{
				$this->logError($id, 'No rater ID provided');
				return false;
			}
			if (!$info['rating_value'])
			{
				$this->logError($id, 'No rating provided');
				return false;
			}
			
			$info['rating_tid'] = $this->getLink( $info['rating_tid'], 'topics' );
			$info['rating_member_id'] = $this->getLink( $info['rating_member_id'], 'members' );
			
			//-----------------------------------------
			// Insert
			//-----------------------------------------
			$this->DB->insert( 'topic_ratings', $info );
			$inserted_id = $this->DB->getInsertId();
				
			//-----------------------------------------
			// Add link
			//-----------------------------------------
				
			$this->addLink($inserted_id, $id, 'topic_rating');

			return true;
		}
		
		/**
		 * Convert warning reason
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertWarnReason( $id, $info )
		{
			if ( ! $id )
			{
				$this->logError( $id, 'No ID number provided.' );
				return false;
			}
			
			if ( ! $info['wr_name'] )
			{
				$this->logError( $id, 'No Warn Reason Name provided.' );
				return false;
			}
			
			// MySQL Strict
			$info['wr_points']			= floatval( $info['wr_points'] );
			$info['wr_points_override']	= intval( $info['wr_points_override'] );
			$info['wr_remove']			= intval( $info['wr_remove'] );
			$info['wr_remove_override']	= intval( $info['wr_remove_override'] );
			$info['wr_order']			= intval( $info['wr_order'] );
			
			$this->DB->insert( 'members_warn_reasons', $info );
			$inserted_id = $this->DB->getInsertId();
			
			$this->addLink( $inserted_id, $id, 'warn_reasons' );
			
			return true;
		}
		
		/**
		 * Convert warning
		 *
		 * @access	public
		 * @param 	integer		Foreign ID number
		 * @param 	array 		Data to insert to table
		 * @return 	boolean		Success or fail
		 **/
		public function convertWarn( $id, $info )
		{
			// Required items.
			if ( ! $id )
			{
				$this->logError( $id, 'No ID provided.' );
				return false;
			}
			
			if ( ! $info['wl_member'] )
			{
				$this->logError( $id, 'No Member ID provided.' );
				return false;
			}
			
			// Set up links.
			$info['wl_member']		= $this->getLink( $info['wl_member'], 'members', true );
			$info['wl_moderator']	= $this->getLink( $info['wl_moderator'], 'members', true );
			$info['wl_reason']		= $this->getLink( $info['wl_reason'], 'warn_reasons', true );
			$info['wl_ban_group']	= $this->getLink( $info['wl_ban_group'], 'groups', true );
			$info['wl_content_id1']	= $this->getLink( $info['wl_content_id1'], 'posts', true ); //@todo Adjust for other content types.
			//$info['wl_content_id2'] = $this->getLink( $info['wl_content_id2'], 'topics', true ); //@todo Make work.
			
			// Make sure MySQL Strict doesn't complain.
			$info['wl_member']		= intval( $info['wl_member'] );
			$info['wl_moderator']	= intval( $info['wl_moderator'] );
			$info['wl_date']		= intval( $info['wl_date'] );
			$info['wl_reason']		= intval( $info['wl_reason'] );
			$info['wl_points']		= intval( $info['wl_points'] );
			$info['wl_mq']			= intval( $info['wl_mq'] );
			$info['wl_rpa']			= intval( $info['wl_rpa'] );
			$info['wl_suspend']		= intval( $info['wl_suspend'] );
			$info['wl_ban_group']	= intval( $info['wl_ban_group'] );
			$info['wl_expire']		= intval( $info['wl_expire'] );
			$info['wl_acknowledged']= intval( $info['wl_acknowledged'] );
			$info['wl_expire_date']	= intval( $info['wl_expire_date'] );
			
			// Make sure we actually have a member still... we also need to update warn levels in members table.
			$member = $this->DB->buildAndFetch( array( 'select' => 'member_id, warn_level', 'from' => 'members', 'where' => "member_id = {$info['wl_member']}" ) );
			
			if ( ! $member['member_id'] )
			{
				$this->logError( $id, 'No Member Found.' );
				return false;
			}
			
			$this->DB->insert( 'members_warn_logs', $info );
			$inserted_id = $this->DB->getInsertId();
			
			$this->addLink( $inserted_id, $id, 'warn_logs' );
			
			// Update member warn level.
			$this->DB->update( 'members', array( 'warn_level' => $member['warn_level'] + 1 ), "member_id = {$member['member_id']}" );
			
			return true;
		}
	}

?>