<?php
/**
 * IPS Converters
 * IP.Board 3.0 Converters
 * IP.Board Merge Tool
 * Last Update: $Date: 2014-01-02 20:16:59 -0500 (Thu, 02 Jan 2014) $
 * Last Updated By: $Author: rashbrook $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 952 $
 */
$info = array( 'key'	=> 'ipboard',
			   'name'	=> 'IP.Board 3.2+',
			   'login'	=> false );

class admin_convert_board_ipboard extends ipsCommand
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
			'custom_bbcode' 			=> array(),
			'pfields'					=> array(),
			'rc_status'					=> array(),
			'rc_status_sev'				=> array('rc_status'),
			'forum_perms'				=> array(),
			'groups' 					=> array('forum_perms'),
			'members'					=> array('groups', 'custom_bbcode', 'pfields', 'rc_status_sev'),
			'dnames_change' 			=> array('members'),
			'profile_comments' 			=> array('members'),
			'profile_comment_replies'	=> array ( 'members', 'profile_comments' ),
			'profile_friends' 			=> array('members'),
			'profile_ratings' 			=> array('members'),
			'ignored_users'				=> array('members'),
			'forums'					=> array('forum_perms', 'members'),
			'moderators'				=> array('groups', 'members', 'forums'),
			'topics'					=> array('members', 'forums'),
			'topic_ratings' 			=> array('topics', 'members'),
			'posts'						=> array('members', 'topics', 'custom_bbcode', 'rc_status_sev'),
			'reputation_index' 			=> array('members', 'posts'),
			'polls'						=> array('topics', 'members', 'forums'),
			'announcements'				=> array('forums', 'members', 'custom_bbcode'),
			'pms'						=> array('members', 'custom_bbcode', 'rc_status_sev'),
			'ranks'						=> array(),
			'attachments_type'			=> array(),
			'attachments'				=> array('attachments_type', 'posts', 'pms'),
			'emoticons'					=> array(),
			'badwords'					=> array(),
			'banfilters'				=> array(),
			'rss_import'				=> array('forums', 'members', 'topics'),
			'rss_export'				=> array('forums', 'members', 'topics'),
			'topic_mmod'				=> array('forums'),
			'warn_reasons'				=> array(),
			'warn_logs'					=> array('members', 'warn_reasons'),
			);

		//-----------------------------------------
	    // Load our libraries
	    //-----------------------------------------

		require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_master.php' );
		require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_board.php' );
		$this->lib =  new lib_board( $registry, $html, $this );

	    $this->html = $this->lib->loadInterface();
		$this->lib->sendHeader( 'IP.Board Merge Tool' );

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
			case 'tags':
				return $this->lib->countRows('core_tags');
				break;
				
			case 'pms':
				return $this->lib->countRows('message_topics');
				break;

			case 'ranks':
				return $this->lib->countRows('titles');
				break;

			case 'pfields':
				return $this->lib->countRows('pfields_data');
				break;

			case 'attachments':
				return $this->lib->countRows('attachments', "attach_rel_module='post' OR attach_rel_module='msg'");
				break;
			
			case 'profile_comments':
				return $this->lib->countRows( 'member_status_updates' );
			break;
			
			case 'profile_comment_replies':
				return $this->lib->countRows( 'member_status_replies' );
			break;
			
			case 'warn_reasons':
				return $this->lib->countRows( 'members_warn_reasons' );
			break;
			
			case 'warn_logs':
				return $this->lib->countRows( 'members_warn_logs' );
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
			case 'ranks':
			case 'attachments':
			case 'emoticons':
			case 'custom_bbcode':
			case 'rc_status':
			case 'rc_status_sev':
			case 'rss_import':
				return true;
				break;

			default:
				return false;
				break;
		}
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
		$this->lib->saveMoreInfo('members', array('pp_path'));

		//---------------------------
		// Set up
		//---------------------------
		$main = array(
						'select'	=> '*',
						'from'	=> 'members',
					  	'order'  => 'member_id ASC' );

		$loop = $this->lib->load('members', $main);

		//-----------------------------------------
		// Prepare for reports conversion
		//-----------------------------------------
		$this->lib->prepareReports('member');

		//-----------------------------------------
		// We need to know how to the uploaded avatar / profile path
		//-----------------------------------------
		$this->lib->getMoreInfo('members', $loop, array('pp_path' => array('type' => 'text', 'label' => 'Path to uploads folder (no trailing slash): ')), 'path');

		$get = unserialize($this->settings['conv_extra']);
		$us = $get[$this->lib->app['name']];

		//---------------------------
		// Loop
		//---------------------------
		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$profilePortal = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'profile_portal', 'where' => "pp_member_id='{$row['member_id']}'" ) );
			
			/* Unset reputation points as we update this as we convert actual reputation later */
			unset( $profilePortal['pp_reputation_points'] );
			
			$custom = array();
			$pfieldsContent = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'pfields_content', 'where' => "member_id='{$row['member_id']}'" ) );
			
			unset( $pfieldsContent['member_id'] );

			if( !empty( $pfieldsContent ) )
			{
				foreach( $pfieldsContent as $key => $value )
				{
					$converted_id = $this->lib->getLink( str_replace( "field_", "", $key ), 'pfields');
					
					if( $converted_id )
					{
						$custom[ 'field_' . $converted_id ] = $value;
					}
				}
			}

			// Set basic info
			$info = array( 'id'               => $row['member_id'],
						   'username'     	  => $row['name'],
						   'displayname'	  => $row['members_display_name'],
						   'email'			  => $row['email'],
						   'group'			  => $row['member_group_id'],
						   'secondary_groups' => $row['mgroup_others'],
						   'joined'		      => $row['joined'],
						   'pass_salt'		  => $row['members_pass_salt'],
						   'pass_hash'		  => $row['members_pass_hash'] );

			// Filter data
			foreach (array_keys($row) as $key)
			{
				if ( !in_array($key, array('name', 'member_group_id', 'email', 'joined', 'ip_address', 'posts', 'title', 'allow_admin_mails', 'time_offset', 'hide_email', 'email_pm', 'last_post', 'view_sigs', 'view_avs', 'bday_day', 'bday_month', 'bday_year', 'msg_count_new', 'msg_count_total', 'msg_count_reset', 'msg_show_notification', 'misc', 'last_visit', 'last_activity', 'dst_in_use', 'auto_track', 'members_editor_choice', 'members_auto_dst', 'members_display_name', 'members_seo_name', 'members_created_remote', 'members_disable_pm', 'members_l_display_name', 'members_l_username', 'members_profile_views')) )
				{
					unset($row[$key]);
				}
			}
			
			if ( $profilePortal['pp_main_photo'] AND ! $profilePortal['pp_photo_type'] )
			{
				$profilePortal['pp_photo_type'] = 'custom';
			}

			// And send it to the lib
			$this->lib->convertMember($info, $row, $profilePortal, $custom, $us['pp_path'], $us['pp_path']);

			//-----------------------------------------
			// Report Center
			//-----------------------------------------

			// Is this user a report center mod?
			/** if ($rcModpref['mem_id'])
			{
				$rcModpref['mem_id'] = $this->lib->getLink($rcModpref['mem_id'], 'members');
				$save = $rcModpref;
				unset($save['mem_id']);
				$this->DB->insert( 'rc_modpref', $save );
			} **/

			// Or is he a naughty boy?
			$rc = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'com_id', 'from' => 'rc_classes', 'where' => "my_class='profiles'" ) );
			$rs = array( 'select' 	=> '*',
						 'from' 		=> 'rc_reports_index',
						 'order'		=> 'id ASC',
						 'where'		=> "exdat1='{$row['member_id']}' AND rc_class='{$rc['com_id']}'" );

			ipsRegistry::DB('hb')->build($rs);
			$rsRes = ipsRegistry::DB('hb')->execute();

			while ($report = ipsRegistry::DB('hb')->fetch($rsRes))
			{
				$rs = array( 'select' 	=> '*',
							 'from' 		=> 'rc_reports',
							 'order'		=> 'id ASC',
							 'where'		=> 'rid='.$report['id'] );

				ipsRegistry::DB('hb')->build($rs);
				$rsInnerRes = ipsRegistry::DB('hb')->execute();
				$reports = array();
				while ($r = ipsRegistry::DB('hb')->fetch($rsInnerRes))
				{
					$reports[] = $r;
				}
				$this->lib->convertReport('member', $report, $reports);
			}
		}

		$this->lib->next();
	}


	/**
	 * Convert Groups
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
						'from' 		=> 'groups',
						'order'		=> 'g_id ASC',
					);

		$loop = $this->lib->load( 'groups', $main, array(), array(), TRUE );

		//-----------------------------------------
		// We need to know how to map these
		//-----------------------------------------

		$this->lib->getMoreInfo('groups', $loop, array('new' => '--Create new group--', 'ot'	=> 'Old group',	'nt'	=> 'New group'), '', array('idf' => 'g_id', 'nf' => 'g_title'));

		//---------------------------
		// Loop
		//---------------------------

		foreach( $loop as $row )
		{
			$save = array(
					'g_title'				=> $row['g_title'],
					'g_max_messages'		=> $row['g_max_messages'],
					'g_max_mass_pm'			=> $row['g_max_mass_pm'],
					'prefix'				=> $row['prefix'],
					'suffix'				=> $row['suffix'],
					'g_view_board'			=> $row['g_view_board'],
					'g_mem_info'			=> $row['g_mem_info'],
					'g_other_topics'		=> $row['g_other_topics'],
					'g_use_search'			=> $row['g_use_search'],
					'g_email_friend'		=> $row['g_email_friend'],
					'g_invite_friend'		=> $row['g_invite_friend'],
					'g_edit_profile'		=> $row['g_edit_profile'],
					'g_post_new_topics'		=> $row['g_post_new_topics'],
					'g_reply_own_topics'	=> $row['g_reply_own_topics'],
					'g_reply_other_topics'	=> $row['g_reply_other_topics'],
					'g_edit_posts'			=> $row['g_edit_posts'],
					'g_delete_own_posts'	=> $row['g_delete_own_posts'],
					'g_open_close_posts'	=> $row['g_open_close_posts'],
					'g_delete_own_topics'	=> $row['g_delete_own_topics'],
					'g_post_polls'		 	=> $row['g_post_polls'],
					'g_vote_polls'		 	=> $row['g_vote_polls'],
					'g_use_pm'			 	=> $row['g_use_pm'],
					'g_is_supmod'		 	=> $row['g_is_supmod'],
					'g_access_cp'		 	=> $row['g_access_cp'],
					'g_access_offline'	 	=> $row['g_access_offline'],
					'g_avoid_q'			 	=> $row['g_avoid_q'],
					'g_avoid_flood'		 	=> $row['g_avoid_flood'],
					'g_perm_id'				=> $row['g_perm_id'],
					'g_edit_profile'		=> $row['g_edit_profile'],
					'g_append_edit'			=> $row['g_append_edit'],
					'g_icon'				=> $row['g_icon'],
					'g_attach_max'			=> $row['g_attach_max'],
					'g_search_flood'		=> $row['g_search_flood'],
					'g_edit_cutoff'			=> $row['g_edit_cutoff'],
					'g_promotion'			=> $row['g_promotion'],
					'g_hide_from_list'		=> $row['g_hide_from_list'],
					'g_post_closed'			=> $row['g_post_closed'],
					'g_photo_max_vars'		=> $row['g_photo_max_vars'],
					'g_dohtml'				=> $row['g_dohtml'],
					'g_edit_topic'			=> $row['g_edit_topic'],
					'g_bypass_badwords'		=> $row['g_bypass_badwords'],
					'g_can_msg_attach'		=> $row['g_can_msg_attach'],
					'g_attach_per_post'		=> $row['g_attach_per_post'],
					'g_topic_rate_setting'	=> $row['g_topic_rate_setting'],
					'g_dname_changes'		=> $row['g_dname_changes'],
					'g_dname_date'			=> $row['g_dname_date'],
					'g_mod_preview'			=> $row['g_mod_preview'],
					'g_rep_max_positive'	=> $row['g_rep_max_positive'],
					'g_rep_max_negative'	=> $row['g_rep_max_negative'],
					'g_signature_limits'	=> $row['g_signature_limits'],
					'g_can_add_friends'		=> $row['g_can_add_friends'],
					'g_hide_online_list'	=> $row['g_hide_online_list'],
					'g_bitoptions'			=> $row['g_bitoptions'],
					'g_pm_perday'			=> $row['g_pm_perday'],
					'g_mod_post_unit'		=> $row['g_mod_post_unit'],
					'g_ppd_limit'			=> $row['g_ppd_limit'],
					'g_ppd_unit'			=> $row['g_ppd_unit'],
					'g_displayname_unit'	=> $row['g_displayname_unit'],
					'g_sig_unit'			=> $row['g_sig_unit'],
					'g_pm_flood_mins'		=> $row['g_pm_flood_mins'],
					'g_max_notifications'	=> $row['g_max_notifications'],
					'g_max_bgimg_upload'	=> $row['g_max_bgimg_upload'],
			);
					
			$this->lib->convertGroup($row['g_id'], $save);
		}

		$this->lib->next();

	}

	/**
	 * Convert Permission Masks
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
						'from' 		=> 'forum_perms',
						'order'		=> 'perm_id ASC',
					);

		$loop = $this->lib->load( 'forum_perms', $main, array(), array(), TRUE );

		//-----------------------------------------
		// We need to know how to map these
		//-----------------------------------------

		$this->lib->getMoreInfo('forum_perms', $loop, array('new' => '--Create new set--', 'ot' => 'Old permission set', 'nt' => 'New permission set'), '', array('idf' => 'perm_id', 'nf' => 'perm_name'));

		//---------------------------
		// Loop
		//---------------------------

		foreach( $loop as $row )
		{
			$this->lib->convertPermSet($row['perm_id'], $row['perm_name']);
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

		$main = array(	'select' 	=> 'f.*',
						'from' 		=> array('forums' => 'f'),
						'order'		=> 'id ASC',
						'add_join'	=> array(
										array( 	'select' => 'p.*',
												'from'   =>	array( 'permission_index' => 'p' ),
												'where'  => "p.perm_type='forum' AND p.perm_type_id=f.id",
												'type'   => 'left'
											),
										)
					);

		$loop = $this->lib->load('forums', $main, array('rss_export'));

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			//-----------------------------------------
			// Handle permissions
			//-----------------------------------------

			$perms = array();
			$perms['view']		= $row['perm_view'];
			$perms['read']		= $row['perm_2'];
			$perms['reply']		= $row['perm_3'];
			$perms['start']		= $row['perm_4'];
			$perms['upload']	= $row['perm_5'];
			$perms['download']	= $row['perm_6'];

			//-----------------------------------------
			// And go
			//-----------------------------------------
			$save = array('topics'			=> $row['topics'],
					'posts'			  	=> $row['posts'],
					'last_post'		  	=> $row['last_post'],
					'last_poster_name'	=> $row['last_poster_name'],
					'parent_id'		  	=> $row['parent_id'],
					'name'			  	=> $row['name'],
					'description'	  	=> $row['description'],
					'position'		  	=> $row['position'],
					'use_ibc'		  	=> $row['use_ibc'],
					'use_html'		  	=> $row['use_html'],
					'status'			=> $row['status'],
					'inc_postcount'	  	=> $row['inc_postcount'],
					'password'		  	=> $row['password'],
					'sub_can_post'		=> $row['sub_can_post'],
					'redirect_on'		=> $row['redirect_on'],
					'redirect_url'		=> $row['redirect_url'],
					'preview_posts'		=> $row['preview_posts'],
					'forum_allow_rating'=> $row['forum_allow_rating'],
					);

			$this->lib->convertForum($row['id'], $save, $perms);
			
			//-----------------------------------------
			// Handle subscriptions
			//-----------------------------------------

			ipsRegistry::DB('hb')->build(
										array(
												'select'	=> '*',
												'from'		=> 'core_like',
												'where'		=> "like_rel_id='{$row['id']}' AND like_app='forums' AND like_area='forums'"
											)
										);
			
			$subRes = ipsRegistry::DB('hb')->execute();
			
			while ( $tracker = ipsRegistry::DB('hb')->fetch($subRes) )
			{
				$savetracker = array(
						'member_id'			=> $tracker['like_member_id'],
						'forum_id'			=> $tracker['like_rel_id'],
						'forum_track_type' 	=> $tracker['like_notify_freq'],
						'like_is_anon'		=> $tracker['like_is_anon'],
				);
				
				$this->lib->convertForumSubscription( $tracker['like_id'], $savetracker );
			}
		}

		$this->lib->next();

	}

	/**
	 * Convert Moderators
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_moderators()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'moderators',
						'order'		=> 'mid ASC',
					);

		$loop = $this->lib->load('moderators', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			// we need a $save to stop duplicate keys
			$save	=	array(
							//'mid'						=>	$row['mid'],
							'forum_id'					=>	$row['forum_id'],
							'member_name'				=>	$row['member_name'],
							'member_id'					=>	$row['member_id'],
							'edit_post'					=>	$row['edit_post'],
							'edit_topic'				=>	$row['edit_topic'],
							'delete_post'				=>	$row['delete_post'],
							'delete_topic'				=>	$row['delete_topic'],
							'view_ip'					=>	$row['view_ip'],
							'open_topic'				=>	$row['open_topic'],
							'close_topic'				=>	$row['close_topic'],
							'mass_move'					=>	$row['mass_move'],
							'mass_prune'				=>	$row['mass_prune'],
							'move_topic'				=>	$row['move_topic'],
							'pin_topic'					=>	$row['pin_topic'],
							'unpin_topic'				=>	$row['unpin_topic'],
							'post_q'					=>	$row['post_q'],
							'topic_q'					=>	$row['topic_q'],
							'allow_warn'				=>	$row['allow_warn'],
							'is_group'					=>	$row['is_group'],
							'group_id'					=>	$row['group_id'],
							'group_name'				=>	$row['group_name'],
							'split_merge'				=>	$row['split_merge'],
							'can_mm'					=>	$row['can_mm'],
							'mod_can_set_open_time'		=>	$row['mod_can_set_open_time'],
							'mod_can_set_close_time'	=>	$row['mod_can_set_close_time'],
							'mod_bitoptions'			=>	$row['mod_bitoptions'],
						);
			
			$this->lib->convertModerator($row['mid'], $save);
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
						'from' 		=> 'topics',
						'order'		=> 'tid ASC',
					);

		$loop = $this->lib->load('topics', $main, array());

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	= 	array(
								'title'					=> $row['title'],
							   	'state'					=> $row['state'],
							   	'posts'					=> $row['posts'],
							   	'starter_id'			=> $row['starter_id'],
							   	'starter_name'			=> $row['starter_name'],
							   	'start_date'			=> $row['start_date'],
							   	'last_post'				=> $row['last_post'],
							   	'last_poster_name'		=> $row['last_poster_name'],
							   	'poll_state'			=> $row['poll_state'],
							   	'views'					=> $row['views'],
							   	'forum_id'				=> $row['forum_id'],
							   	'approved'				=> $row['approved'],
							   	'pinned'				=> $row['pinned'],
							   	'topic_hasattach'		=> $row['topic_hasattach'],
								'topic_rating_total'	=> $row['topic_rating_total'],
								'topic_rating_hits'		=> $row['topic_rating_hits'],
								'topic_open_time'		=> $row['topic_open_time'],
								'topic_close_time'		=> $row['topic_close_time'],
						);
							  	 
			$this->lib->convertTopic($row['tid'], $save);
			
			//-----------------------------------------
			// Handle subscriptions
			//-----------------------------------------
				
			ipsRegistry::DB('hb')->build(
										array(
												'select'	=> '*',
												'from'		=> 'core_like',
												'where'		=> "like_rel_id='{$row['tid']}' AND like_app='forums' AND like_area='topics'"
											)
										);
						$subRes = ipsRegistry::DB('hb')->execute();
			
			while ( $tracker = ipsRegistry::DB('hb')->fetch($subRes) )
			{
				$savetracker = array(
						'member_id'			=> $tracker['like_member_id'],
						'topic_id'			=> $tracker['like_rel_id'],
						'topic_track_type' 	=> $tracker['like_notify_freq'],
						'like_is_anon'		=> $tracker['like_is_anon'],
				);
			
				$this->lib->convertTopicSubscription( $tracker['like_id'], $savetracker);
			}
			
			//-----------------------------------------
			// Tags
			//-----------------------------------------
			
			$tags = array();
			
			ipsRegistry::DB('hb')->build(
				array(
				'select'	=> '*',
				'from'		=> 'core_tags',
				'where'		=> "tag_meta_id='{$row['tid']}' AND tag_meta_app='forums' AND tag_meta_area='topics'"
				)
			);
			
			$tagRes = ipsRegistry::DB('hb')->execute();
				
			while ( $tag = ipsRegistry::DB('hb')->fetch( $tagRes ) )
			{
				$tags[] = $tag['tag_text'];
			}
			
			if( !empty( $tags ) )
			{
				$save	=	array(
						'tag_meta_id'			=>	$row['tid'],
						'tag_meta_parent_id'	=>	$row['forum_id'],
						'tag_member_id'			=>	$row['starter_id'],
				);

				$this->lib->convertTags( $tags, $save, 'forums', 'topics' );
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
		//---------------------------
		// Primary Key
		//---------------------------
			
		$this->lib->useKey( 'p.pid' );

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> 'p.*',
						'from' 		=> array('posts' => 'p'),
						'order'		=> 'p.pid ASC',
						'add_join'	=> array(
										array( 	'select' => 'r.rep_points',
												'from'   =>	array( 'reputation_cache' => 'r' ),
												'where'  => "r.app='forums' AND r.type='pid' AND r.type_id=p.pid",
												'type'   => 'left'
											),
										),
					);

		$loop = $this->lib->load('posts', $main, array('reputation_cache'));

		//-----------------------------------------
		// Prepare for reports conversion
		//-----------------------------------------

		$this->lib->prepareReports('posts');

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save = $row;
			// we need to remove pid from our save array
			unset($save['pid']);
			
			$this->lib->convertPost($row['pid'], $save);

			$this->lib->setLastKeyValue( $row['pid'] );

			//-----------------------------------------
			// Report Center
			//-----------------------------------------

			$rc = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'com_id', 'from' => 'rc_classes', 'where' => "my_class='post'" ) );
			$rs = array(	'select' 	=> '*',
							'from' 		=> 'rc_reports_index',
							'order'		=> 'id ASC',
							'where'		=> 'exdat3='.$row['pid']." AND rc_class='{$rc['com_id']}'"
						);

			ipsRegistry::DB('hb')->build($rs);
			$res = ipsRegistry::DB('hb')->execute();
			while ($report = ipsRegistry::DB('hb')->fetch( $res ))
			{
				$rs = array(	'select' 	=> '*',
								'from' 		=> 'rc_reports',
								'order'		=> 'id ASC',
								'where'		=> 'rid='.$report['id']
							);

				ipsRegistry::DB('hb')->build($rs);
				$repRes = ipsRegistry::DB('hb')->execute();
				$reports = array();
				while ($r = ipsRegistry::DB('hb')->fetch( $repRes ))
				{
					$save	=	array(
									//'id'				=>	$r['id'],
									'rid'				=>	$r['rid'],
									'report'			=>	$r['report'],
									'report_by'			=>	$r['report_by'],
									'date_reported'		=>	$r['date_reported'],

								);
					$reports[]	=	$save;
				}
				$this->lib->convertReport('post', $report, $reports);
			}

		}

		$this->lib->next();

	}

	/**
	 * Convert Polls
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_polls()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'polls',
						'order'		=> 'pid ASC',
					);

		$loop = $this->lib->load('polls', $main, array('voters'));

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			//-----------------------------------------
			// We need to do voters...
			//-----------------------------------------

			ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'voters', 'where' => "tid={$row['tid']}"));
			ipsRegistry::DB('hb')->execute();
			while ($voter = ipsRegistry::DB('hb')->fetch())
			{
				$save	=	array(
								//'vid'				=>	$voter['vid'],
								'ip_address'		=>	$voter['ip_address'],
								'vote_date'			=>	$voter['vote_date'],
								'tid'				=>	$voter['tid'],
								'member_id'			=>	$voter['member_id'],
								'forum_id'			=>	$voter['forum_id'],
								'member_choices'	=>	$voter['member_choices'],
						
							);
				$this->lib->convertPollVoter($voter['vid'], $save);
			}

			//-----------------------------------------
			// Then we can do the actual poll
			//-----------------------------------------

			$this->lib->convertPoll($row['pid'], $row);
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
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'message_topics',
						'order'		=> 'mt_id ASC',
					);

		$loop = $this->lib->load('pms', $main, array('pm_posts', 'pm_maps'));

		//-----------------------------------------
		// Prepare for reports conversion
		//-----------------------------------------

		$this->lib->prepareReports('pm');

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{

			//-----------------------------------------
			// Load the posts
			//-----------------------------------------

			$posts = array();
			ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'message_posts', 'where' => "msg_topic_id={$row['mt_id']}", 'order' => 'msg_date ASC'));
			ipsRegistry::DB('hb')->execute();
			while ($post = ipsRegistry::DB('hb')->fetch())
			{
				$post['msg_post'] = $this->fixPostData($post['msg_post']);
				$posts[] = $post;
			}

			//-----------------------------------------
			// And the maps
			//-----------------------------------------

			$maps = array();
			ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'message_topic_user_map', 'where' => "map_topic_id={$row['mt_id']}"));
			ipsRegistry::DB('hb')->execute();
			while ($map = ipsRegistry::DB('hb')->fetch())
			{
				$save	=	array(
								'map_id'					=>	$map['map_id'],
								'map_user_id'				=>	$map['map_user_id'],
								'map_topic_id'				=>	$map['map_topic_id'],
								'map_folder_id'				=>	$map['map_folder_id'],
								'map_read_time'				=>	$map['map_read_time'],
								'map_user_active'			=>	$map['map_user_active'],
								'map_user_banned'			=>	$map['map_user_banned'],
								'map_has_unread'			=>	$map['map_has_unread'],
								'map_is_system'				=>	$map['map_is_system'],
								'map_is_starter'			=>	$map['map_is_starter'],
								'map_left_time'				=>	$map['map_left_time'],
								'map_ignore_notification'	=>	$map['map_ignore_notification'],
								'map_last_topic_reply'		=>	$map['map_last_topic_reply'],
							);
				
				$maps[]	=	$save;
			}

			//-----------------------------------------
			// And send it through
			//-----------------------------------------

			$this->lib->convertPM($row, $posts, $maps);


			//-----------------------------------------
			// Report Center
			//-----------------------------------------

			foreach ($posts as $post)
			{
				$rc = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'com_id', 'from' => 'rc_classes', 'where' => "my_class='messages'" ) );
				$rs = array(	'select' 	=> '*',
								'from' 		=> 'rc_reports_index',
								'order'		=> 'id ASC',
								'where'		=> 'exdat2='.$post['msg_id']." AND rc_class='{$rc['com_id']}'"
							);

				ipsRegistry::DB('hb')->build($rs);
				$res = ipsRegistry::DB('hb')->execute();
				while ($report = ipsRegistry::DB('hb')->fetch( $res ))
				{
					$rs = array(	'select' 	=> '*',
									'from' 		=> 'rc_reports',
									'order'		=> 'id ASC',
									'where'		=> 'rid='.$report['id']
								);
	
					ipsRegistry::DB('hb')->build($rs);
					$repRes = ipsRegistry::DB('hb')->execute();
					$reports = array();
					while ($r = ipsRegistry::DB('hb')->fetch( $repRes ))
					{
						$save	=	array(
								//'id'				=>	$r['id'],
								'rid'				=>	$r['rid'],
								'report'			=>	$r['report'],
								'report_by'			=>	$r['report_by'],
								'date_reported'		=>	$r['date_reported'],
						
						);
						$reports[] = $save;
					}
					$this->lib->convertReport('pm', $report, $reports);
				}
			}

		}

		$this->lib->next();

	}

	/**
	 * Convert Ranks
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_ranks()
	{

		//-----------------------------------------
		// Were we given more info?
		//-----------------------------------------

		$this->lib->saveMoreInfo('ranks', array('rank_opt'));

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'titles',
						'order'		=> 'id ASC',
					);

		$loop = $this->lib->load('ranks', $main);

		//-----------------------------------------
		// We need to know what do do with duplicates
		//-----------------------------------------

		$this->lib->getMoreInfo('ranks', $loop, array('rank_opt'  => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate ranks?')));

		$get = unserialize($this->settings['conv_extra']);
		$us = $get[$this->lib->app['name']];

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'id'		=>	$row['id'],
							'posts'		=>	$row['posts'],
							'title'		=>	$row['title'],
							'pips'		=>	$row['pips'],
						);
			$this->lib->convertRank($row['id'], $save, $us['rank_opt']);
		}

		$this->lib->next();

	}

	/**
	 * Convert Mime Types
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_attachments_type()
	{

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'attachments_type',
						'order'		=> 'atype_id ASC',
					);

		$loop = $this->lib->load('attachments_type', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'atype_id'			=>	$row['atype_id'],
							'atype_extension'	=>	$row['atype_extension'],
							'atype_mimetype'	=>	$row['atype_mimetype'],
							'atype_post'		=>	$row['atype_post'],
							'atype_img'			=>	$row['atype_img'],
						);
			$this->lib->convertAttachType($row['atype_id'], $save);
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

		$main = array(	'select' 	=> '*',
						'from' 		=> 'attachments',
						'where'		=> "attach_rel_module='post' OR attach_rel_module='msg'",
						'order'		=> 'attach_id ASC',
					);

		$loop = $this->lib->load('attachments', $main);

		//-----------------------------------------
		// We need to know the path
		//-----------------------------------------

		$this->lib->getMoreInfo('attachments', $loop, array('attach_path' => array('type' => 'text', 'label' => 'The path to the folder where attachments are saved (no trailing slash - usually path_to_ipboard/uploads):')), 'path');

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
		if (!is_readable($path))
		{
			$this->lib->error('Your remote upload path is not readable.');
		}

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			// Send em on
			$done = $this->lib->convertAttachment($row['attach_id'], $row, $path);

			// Fix inline attachments
			if ($done === true)
			{
				$aid = $this->lib->getLink($row['attach_id'], 'attachments');

				switch ($row['attach_rel_module'])
				{
					case 'post':
						$field = 'post';
						$table = 'posts';
						$pid = $this->lib->getLink($row['attach_rel_id'], 'posts');
						$where = "pid={$pid}";
						break;

					case 'msg':
						$field = 'msg_post';
						$table = 'message_posts';
						$pid = $this->lib->getLink($row['attach_rel_id'], 'pm_posts');
						$where = "msg_id={$pid}";
						break;

					default:
						continue;
						break;
				}

				if ( $pid )
				{
					$attachrow = $this->DB->buildAndFetch( array( 'select' => $field, 'from' => $table, 'where' => $where ) );
					$save = preg_replace("#(\[attachment=)({$row['attach_id']}+?)\:([^\]]+?)\]#ie", "'$1'. $aid .':$3]'", $attachrow[$field]);
					$this->DB->update($table, array($field => $save), $where);
				}
			}

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
						'from' 		=> 'emoticons',
						'order'		=> 'id ASC',
					);

		$loop = $this->lib->load('emoticons', $main);

		//-----------------------------------------
		// We need to know the path and how to handle duplicates
		//-----------------------------------------

		$this->lib->getMoreInfo('emoticons', $loop, array('emo_path' => array('type' => 'text', 'label' => 'The path to the folder where emoticons are saved (no trailing slash - usually path_to_ipboard/public/style_emoticons):'), 'emo_opt'  => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate emoticons?') ), 'path' );

		$get = unserialize($this->settings['conv_extra']);
		$us = $get[$this->lib->app['name']];
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
			$save	=	array(
							//'id'			=>	$row['id'],
							'typed'			=>	$row['typed'],
							'image'			=>	$row['image'],
							'clickable'		=>	$row['clickable'],
							'emo_set'		=>	$row['emo_set'],
							'emo_position'	=>	$row['emo_position'],
						);
			$done = $this->lib->convertEmoticon($row['id'], $save, $us['emo_opt'], $path.'/'.$row['emo_set']);
		}

		$this->lib->next();

	}

	/**
	 * Convert Announcements
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_announcements()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'announcements',
						'order'		=> 'announce_id ASC',
					);

		$loop = $this->lib->load('announcements', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'announce_id'			=>	$row['announce_id'],
							'announce_title'		=>	$row['announce_title'],
							'announce_post'			=>	$this->fixPostData($row['announce_post']),
							'announce_forum'		=>	$row['announce_forum'],
							'announce_member_id'	=>	$row['announce_member_id'],
							'announce_html_enabled'	=>	$row['announce_html_enabled'],
							'announce_nlbr_enabled'	=>	$row['announce_nlbr_enabled'],
							'announce_views'		=>	$row['announce_views'],
							'announce_start'		=>	$row['announce_start'],
							'announce_end'			=>	$row['announce_end'],
							'announce_active'		=>	$row['announce_active'],
							'announce_seo_title'	=>	$row['announce_seo_title'],
						);

			$this->lib->convertAnnouncement($row['announce_id'], $save);
		}

		$this->lib->next();

	}

	/**
	 * Convert Bad Words
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_badwords()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'badwords',
						'order'		=> 'wid ASC',
					);

		$loop = $this->lib->load('badwords', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'wid'		=>	$row['wid'],
							'type'		=>	$row['type'],
							'swop'		=>	$row['swop'],
							'm_exact'	=>	$row['m_exact'],
						);
			$this->lib->convertBadword($row['wid'], $save);
		}

		$this->lib->next();

	}

	/**
	 * Convert Ban Filters
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_banfilters()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'banfilters',
						'order'		=> 'ban_id ASC',
					);

		$loop = $this->lib->load('banfilters', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'ban_id'		=>	$row['ban_id'],
							'ban_type'		=>	$row['ban_type'],
							'ban_content'	=>	$row['ban_content'],
							'ban_date'		=>	$row['ban_date'],
							'ban_reason'	=>	$row['ban_reason'],
						);
			$this->lib->convertBan($row['ban_id'], $save);
		}

		$this->lib->next();

	}

	/**
	 * Convert Custom BBCode
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_custom_bbcode()
	{

		//-----------------------------------------
		// Were we given more info?
		//-----------------------------------------

		$this->lib->saveMoreInfo('custom_bbcode', array('custom_bbcode_opt'));

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'custom_bbcode',
						'order'		=> 'bbcode_id ASC',
					);

		$loop = $this->lib->load('custom_bbcode', $main, array('bbcode_media'));

		//-----------------------------------------
		// We need to know what do do with duplicates
		//-----------------------------------------

		$this->lib->getMoreInfo('custom_bbcode', $loop, array('custom_bbcode_opt' => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate BBCodes?')));

		$get = unserialize($this->settings['conv_extra']);
		$us = $get[$this->lib->app['name']];

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							'bbcode_title'				=>	$row['bbcode_title'],
							'bbcode_desc'				=>	$row['bbcode_desc'],
							'bbcode_tag'				=>	$row['bbcode_tag'],
							'bbcode_replace'			=>	$row['bbcode_replace'],
							'bbcode_useoption'			=>	$row['bbcode_useoption'],
							'bbcode_example'			=>	$row['bbcode_example'],
							'bbcode_switch_option'		=> $row['bbcode_switch_option'],
							'bbcode_menu_option_text'	=> $row['bbcode_menu_option_text'],
							'bbcode_menu_content_text'	=> $row['bbcode_menu_content_text'],
							'bbcode_single_tag'			=> $row['bbcode_single_tag'],
							'bbcode_php_plugin'			=> $row['bbcode_php_plugin'],
							'bbcode_no_parsing'			=> $row['bbcode_no_parsing'],
							'bbcode_protected'			=> $row['bbcode_protected'],
							'bbcode_aliases'			=> $row['bbcode_aliases'],
							'bbcode_optional_option'	=> $row['bbcode_optional_option'],
							'bbcode_app'				=> $row['bbcode_app'],
							'bbcode_custom_regex'		=> $row['bbcode_custom_regex'],
						);
			$this->lib->convertBBCode($row['bbcode_id'], $save, $us['custom_bbcode_opt']);

			// We need to do special stuff for [media]
			if ($row['bbcode_tag'] == 'media')
			{
				ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'bbcode_mediatag'));
				ipsRegistry::DB('hb')->execute();
				while ($media = ipsRegistry::DB('hb')->fetch())
				{
					$save	=	array(
									//'mediatag_id'		=>	$media['mediatag_id'],
									'mediatag_name'		=>	$media['mediatag_name'],
									'mediatag_match'	=>	$media['mediatag_match'],
									'mediatag_replace'	=>	$media['mediatag_replace'],
									'mediatag_position'	=>	$media['mediatag_position'],
								);
					$this->lib->convertMediaTag($media['mediatag_id'], $save, $us['custom_bbcode_opt']);
				}
			}
		}

		$this->lib->next();

	}

	/**
	 * Convert Display Name history
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_dnames_change()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'dnames_change',
						'order'		=> 'dname_id ASC',
					);

		$loop = $this->lib->load('dnames_change', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'dname_id'			=>	$row['dname_id'],
							'dname_member_id'	=>	$row['dname_member_id'],
							'dname_date'		=>	$row['dname_date'],
							'dname_ip_address'	=>	$row['dname_ip_address'],
							'dname_previous'	=>	$row['dname_previous'],
							'dname_current'		=>	$row['dname_current'],
							'dname_discount'	=>	$row['dname_discount'],
						);
			$this->lib->convertDname($row['dname_id'], $save);
		}

		$this->lib->next();

	}

	/**
	 * Convert Ignored Users
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_ignored_users()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'ignored_users',
						'order'		=> 'ignore_id ASC',
					);

		$loop = $this->lib->load('ignored_users', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'ignore_id'			=>	$row['ignore_id'],
							'ignore_owner_id'	=>	$row['ignore_owner_id'],
							'ignore_ignore_id'	=>	$row['ignore_ignore_id'],
							'ignore_messages'	=>	$row['ignore_messages'],
							'ignore_topics'		=>	$row['ignore_topics'],
							'ignore_signatures'	=>	$row['ignore_signatures'],
							'ignore_chats'		=>	$row['ignore_chats'],
						);
			$this->lib->convertIgnore($row['ignore_id'], $save);
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

		$main = array(	'select' 	=> '*',
						'from' 		=> 'pfields_data',
						'order'		=> 'pf_id ASC',
					);

		$loop = $this->lib->load('pfields', $main, array('pfields_groups'));

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			// Do we have a group?
			if ( $row['pf_group_id'] && !$this->lib->getLink( $row['pf_group_id'], 'pfields_groups' ) )
			{
				$group = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'pfields_groups', 'where' => "pf_group_id = '{$row['pf_group_id']}'" ) );
 				$this->lib->convertPFieldGroup($group['pf_group_id'], $group);
			}

			$save	=	array(
							'pf_title'			=>	$row['pf_title'],
							'pf_desc'			=>	$row['pf_desc'],
							'pf_content'		=>	$row['pf_content'],
							'pf_type'			=>	$row['pf_type'],
							'pf_not_null'		=>	$row['pf_not_null'],
							'pf_member_hide'	=>	$row['pf_member_hide'],
							'pf_max_input'		=>	$row['pf_max_input'],
							'pf_member_edit'	=>	$row['pf_member_edit'],
							'pf_position'		=>	$row['pf_position'],
							'pf_show_on_reg'	=>	$row['pf_show_on_reg'],
							'pf_input_format'	=>	$row['pf_input_format'],
							'pf_admin_only'		=>	$row['pf_admin_only'],
							'pf_topic_format'	=>	$row['pf_topic_format'],
							'pf_group_id'		=>	$row['pf_group_id'],
							'pf_icon'			=>	$row['pf_icon'],
							'pf_key'			=>	$row['pf_key'],
							'pf_search_type'	=>	$row['pf_search_type'],
							'pf_filtering'		=>	$row['pf_filtering'],
						);
			// Carry on...
			$this->lib->convertPField($row['pf_id'], $save);
		}

		$this->lib->next();

	}

	/**
	 * Convert profile comments
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_profile_comments()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'member_status_updates',
						'order'		=> 'status_id ASC',
					);

		$loop = $this->lib->load('profile_comments', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'status_id'			=>	$row['status_id'],
							'status_member_id'	=>	$row['status_member_id'],
							'status_date'		=>	$row['status_date'],
							'status_content'	=>	$row['status_content'],
							'status_replies'	=>	$row['status_replies'],
							'status_last_ids'	=>	$row['status_last_ids'],
							'status_is_latest'	=>	$row['status_is_latest'],
							'status_is_locked'	=>	$row['status_is_locked'],
							'status_hash'		=>	$row['status_hash'],
							'status_imported'	=>	$row['status_imported'],
							'status_creator'	=>	$row['status_creator'],
							'status_author_id'	=>	$row['status_author_id'],
							'status_author_ip'	=>	$row['status_author_ip'],
							'status_approved'	=>	$row['status_approved'],
						);
			$this->lib->convertProfileComment($row['status_id'], $save);
		}

		$this->lib->next();

	}

	/** Convert profile comment replies
	 *
	 * @access private
	 * @return void
	 */
	private function convert_profile_comment_replies ( )
	{
		$main = array (
			'select'	=> '*',
			'from'		=> 'member_status_replies',
			'order'		=> 'reply_id ASC',
		);
		
		$loop = $this->lib->load ( 'profile_comment_replies', $main );
		
		while ( $row = ipsRegistry::DB ( 'hb' )->fetch ( $this->lib->queryRes ) )
		{
			$save	=	array(
							//'reply_id'			=>	$row['reply_id'],
							'reply_status_id'	=>	$row['reply_status_id'],
							'reply_member_id'	=>	$row['reply_member_id'],
							'reply_date'		=>	$row['reply_date'],
							'reply_content'		=>	$row['reply_content'],
						);
			$this->lib->convertProfileCommentReply ( $row['reply_id'], $save );
		}
		
		$this->lib->next ( );
	}

	/**
	 * Convert friends
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_profile_friends()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'profile_friends',
						'order'		=> 'friends_id ASC',
					);

		$loop = $this->lib->load('profile_friends', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'friends_id'		=>	$row['friends_id'],
							'friends_member_id'	=>	$row['friends_member_id'],
							'friends_friend_id'	=>	$row['friends_friend_id'],
							'friends_approved'	=>	$row['friends_approved'],
							'friends_added'		=>	$row['friends_added'],
						);
			$this->lib->convertFriend( $row['friends_id'], $save );
		}

		$this->lib->next();

	}

	/**
	 * Convert profile ratings
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_profile_ratings()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'profile_ratings',
						'order'		=> 'rating_id ASC',
					);

		$loop = $this->lib->load('profile_ratings', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'rating_id'				=>	$row['rating_id'],
							'rating_for_member_id'	=>	$row['rating_for_member_id'],
							'rating_by_member_id'	=>	$row['rating_by_member_id'],
							'rating_ip_address'		=>	$row['rating_ip_address'],
							'rating_value'			=>	$row['rating_value'],
						);
			$this->lib->convertProfileRating( $row['rating_id'], $save );
		}

		$this->lib->next();

	}

	/**
	 * Convert Report Center Statuses
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_rc_status()
	{

		//-----------------------------------------
		// Were we given more info?
		//-----------------------------------------

		$this->lib->saveMoreInfo('rc_status', array('rc_status_opt'));

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'rc_status',
						'order'		=> 'status ASC',
					);

		$loop = $this->lib->load('rc_status', $main);

		//-----------------------------------------
		// We need to know what do do with duplicates
		//-----------------------------------------

		$this->lib->getMoreInfo('rc_status', $loop, array('rc_status_opt'  => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate statuses?')));

		$get = unserialize($this->settings['conv_extra']);
		$us = $get[$this->lib->app['name']];

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'status'				=>	$row['status'],
							'title'					=>	$row['title'],
							'points_per_report'		=>	$row['points_per_report'],
							'minutes_to_apoint'		=>	$row['minutes_to_apoint'],
							'is_new'				=>	$row['is_new'],
							'is_complete'			=>	$row['is_complete'],
							'is_active'				=>	$row['is_active'],
							'rorder'				=>	$row['rorder'],
						);
			$this->lib->convertRCStatus($row['status'], $save, $us['rc_status_opt']);
		}

		$this->lib->next();

	}

	/**
	 * Convert Report Center Severities
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_rc_status_sev()
	{

		//-----------------------------------------
		// Were we given more info?
		//-----------------------------------------

		$this->lib->saveMoreInfo('rc_status_sev', array('rc_status_sev_opt'));

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'rc_status_sev',
						'order'		=> 'status ASC',
					);

		$loop = $this->lib->load('rc_status_sev', $main);

		//-----------------------------------------
		// We need to know what do do with duplicates
		//-----------------------------------------

		$this->lib->getMoreInfo('rc_status_sev', $loop, array('rc_status_sev_opt'  => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate severities?')));

		$get = unserialize($this->settings['conv_extra']);
		$us = $get[$this->lib->app['name']];

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'id'		=>	$row['id'],
							'status'	=>	$row['status'],
							'points'	=>	$row['points'],
							'img'		=>	$row['img'],
							'is_png'	=>	$row['is_png'],
							'width'		=>	$row['width'],
							'height'	=>	$row['height'],
						);
			$this->lib->convertRCSeverity($row['id'], $save, $us['rc_status_sev_opt']);
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

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'reputation_index',
						'order'		=> 'id ASC',
					);

		$loop = $this->lib->load('reputation_index', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'id'			=>	$row['id'],
							'member_id'		=>	$row['member_id'],
							'app'			=>	$row['app'],
							'type'			=>	$row['type'],
							'type_id'		=>	$row['type_id'],
							'rep_date'		=>	$row['rep_date'],
							'rep_msg'		=>	$row['rep_msg'],
							'rep_rating'	=>	$row['rep_rating'],
						);
			$this->lib->convertRep($row['id'], $save);
		}

		$this->lib->next();

	}

	/**
	 * Convert RSS Imports
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_rss_import()
	{

		//-----------------------------------------
		// Were we given more info?
		//-----------------------------------------

		$this->lib->saveMoreInfo('rss_import', array('rss_import_opt'));

		//-----------------------------------------
		// We need to get rid of unwanted import logs
		// I know, this is really hacky but if they don't use keys in the tables...
		//-----------------------------------------

		if (!$this->request['st'])
		{
			$this->DB->build(array('select' => 'ipb_id as id', 'from' => 'conv_link', 'where' => "type = 'rss_import' AND app = " . $this->lib->app['app_id'] ));
			$this->DB->execute();
			$ids = array();
			while ($row = $this->DB->fetch())
			{
				$ids[] = $row['id'];
			}
			if ( !empty( $ids ) )
			{
				$id_string = implode(",", $ids);
				$this->DB->delete('rss_imported', "rss_imported_impid IN({$id_string})");
			}
		}

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'rss_import',
						'order'		=> 'rss_import_id ASC',
					);

		$loop = $this->lib->load('rss_import', $main);

		//-----------------------------------------
		// We need to know what do do with duplicates
		//-----------------------------------------

		$this->lib->getMoreInfo('rss_import', $loop, array('rss_import_opt'  => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate imports?')));

		$get = unserialize($this->settings['conv_extra']);
		$us = $get[$this->lib->app['name']];

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			if ($this->lib->convertRSSImport($row['rss_import_id'], $row, $us['rss_import_opt']))
			{
				ipsRegistry::DB('hb')->build(array('select' => '*', 'from' => 'rss_imported', 'where' => "rss_imported_impid='{$row['rss_import_id']}'"));
				ipsRegistry::DB('hb')->execute();
				while ($log = ipsRegistry::DB('hb')->fetch())
				{
					unset($log['rss_import_feed']);
					$this->lib->convertRSSImportLog($row['rss_import_id'], $log);
				}
			}
		}

		$this->lib->next();

	}
	
	/**
	 * Convert RSS exports
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_rss_export()
	{
		//---------------------------
		// Set up
		//---------------------------
	
		$main =	array(
			'select'	=> '*',
			'from'		=> 'rss_export'
		);
	
		$loop = $this->lib->load('rss_export', $main);
	
		//---------------------------
		// Loop
		//---------------------------
	
		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			//-----------------------------------------
			// Pass it on
			//-----------------------------------------
	
			$this->lib->convertRSSExport( $row['rss_export_id'], $row );
		}
	
		$this->lib->next();
	}

	/**
	 * Convert Multi-Moderation
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_topic_mmod()
	{

		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'topic_mmod',
						'order'		=> 'mm_id ASC',
					);

		$loop = $this->lib->load('topic_mmod', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'mm_id'					=>	$row['mm_id'],
							'mm_title'				=>	$row['mm_title'],
							'mm_enabled'			=>	$row['mm_enabled'],
							'topic_state'			=>	$row['topic_state'],
							'topic_pin'				=>	$row['topic_pin'],
							'topic_move'			=>	$row['topic_move'],
							'topic_move_link'		=>	$row['topic_move_link'],
							'topic_title_st'		=>	$row['topic_title_st'],
							'topic_title_end'		=>	$row['topic_title_end'],
							'topic_reply'			=>	$row['topic_reply'],
							'topic_reply_content'	=>	($row['topic_reply_content']) ? $this->fixPostData($row['topic_reply_content']) : '',
							'topic_reply_postcount'	=>	$row['topic_reply_postcount'],
							'mm_forums'				=>	$row['mm_forums'],
							'topic_approve'			=>	$row['topic_approve'],
						);

			$this->lib->convertMultiMod($row['mm_id'], $save);
		}

		$this->lib->next();

	}

	/**
	 * Convert topic ratings
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_topic_ratings()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'topic_ratings',
						'order'		=> 'rating_id ASC',
					);

		$loop = $this->lib->load('topic_ratings', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			$save	=	array(
							//'rating_id'			=>	$row['rating_id'],
							'rating_tid'		=>	$row['rating_tid'],
							'rating_member_id'	=>	$row['rating_member_id'],
							'rating_value'		=>	$row['rating_value'],
							'rating_ip_address'	=>	$row['rating_ip_address'],
						);
			$this->lib->convertTopicRating($row['rating_id'], $save);
		}

		$this->lib->next();

	}
	
	/**
	 * Convert Warn Reasons
	 *
	 * @access	private
	 * @return void
	 */
	private function convert_warn_reasons()
	{
		$main = array(
			'select'	=> '*',
			'from'		=> 'members_warn_reasons',
			'order'		=> 'wr_id ASC',
		);
		
		$loop = $this->lib->load( 'warn_reasons', $main );
		
		while( $row = ipsRegistry::DB('hb')->fetch( $this->lib->queryRes ) )
		{
			$save = array(
				'wr_name'				=> $row['wr_name'],
				'wr_points'				=> $row['wr_points'],
				'wr_points_override'	=> $row['wr_points_override'],
				'wr_remove'				=> $row['wr_remove'],
				'wr_remove_unit'		=> $row['wr_remove_unit'],
				'wr_remove_override'	=> $row['wr_remove_override'],
				'wr_order'				=> $row['wr_order'],
			);
			
			$this->lib->convertWarnReason( $row['wr_id'], $save );
		}
		
		$this->lib->next();
	}

	/**
	 * Convert warn logs
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_warn_logs()
	{
		//---------------------------
		// Set up
		//---------------------------

		$main = array(	'select' 	=> '*',
						'from' 		=> 'members_warn_logs',
						'order'		=> 'wl_id ASC',
					);

		$loop = $this->lib->load('warn_logs', $main);

		//---------------------------
		// Loop
		//---------------------------

		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			//-----------------------------------------
			// Process info
			//-----------------------------------------
			
			$save	=	array(
							'wl_mmember'				=> $row['wl_member'],
							'wl_moderator'				=> $row['wl_moderator'],
							'wl_date'					=> $row['wl_date'],
							'wl_reason'					=> $row['wl_reason'],
							'wl_points'					=> $row['wl_points'],
							'wl_note_members'			=> $this->fixPostData( $row['wl_note_member'] ),
							'wl_note_mods'				=> $this->fixPostData( $row['wl_note_mods'] ),
							'wl_mq'						=> $row['wl_mq'],
							'wl_mq_unit'				=> $row['wl_mq_unit'],
							'wl_rpa'					=> $row['wl_rpa'],
							'wl_rpa_unit'				=> $row['wl_rpa_unit'],
							'wl_suspend'				=> $row['wl_suspend'],
							'wl_suspend_unit'			=> $row['wl_suspend_unit'],
							'wl_ban_group'				=> $row['wl_ban_group'],
							'wl_expire'					=> $row['wl_expire'],
							'wl_expire_unit'			=> $row['wl_expire_unit'],
							'wl_acknowledged'			=> $row['wl_acknowledged'],
							'wl_content_app'			=> $row['wl_content_app'],
							'wl_content_id1'			=> $row['wl_content_id1'],
							'wl_content_id2'			=> $row['wl_content_id2'],
							'wl_expire_date'			=> $row['wl_expire_date'],
						);
			
			//-----------------------------------------
			// Pass it on
			//-----------------------------------------

			$this->lib->convertWarn($row['wl_id'], $save);
		}

		$this->lib->next();

	}
	
	/**
	 * Convert warn logs
	 *
	 * @access	private
	 * @return void
	 **/
	private function convert_tags()
	{
		//---------------------------
		// Set up
		//---------------------------
	
		$main = array(	'select' 	=> '*',
				'from' 		=> 'core_tags',
				'order'		=> 'tag_id ASC',
		);
	
		$loop = $this->lib->load('core_tags', $main);
	
		//---------------------------
		// Loop
		//---------------------------
	
		while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
		{
			//-----------------------------------------
			// Process info
			//-----------------------------------------
					
			$save	=	array(
					'tag_prefix'			=>	$row['tag_prefix'],
					'tag_meta_id'			=>	$row['tag_meta_id'],
					'tag_meta_parent_id'	=>	$row['tag_meta_parent_id'],
					'tag_text'				=>	$row['tag_text'],
					'tag_member_id'			=>	$row['tag_member_id'],
					'tag_added'				=>	$row['tag_added'],
			);
				
			//-----------------------------------------
			// Pass it on
			//-----------------------------------------
	
			$this->lib->convertTags($row['tag_id'], $save);
		}
	
		$this->lib->next();
	
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

}

