<?php
/**
 * IPS Converters
 * IP.Board 3.4 Converters
 * vBulletin 5 Connect
 * Last Update: $Date$
 * Last Updated By: $Author$
 *
 * @package		IPS Converters
 * @author 		Ryan Ashbrook / Michael Burton / Andrew Millne
 * @copyright	(c) 2013 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision$
 *
 * @todo rename 'key' to vbulletin and name file as vbulletin.php (let's be consistent)
 * @todo we may need to abstract content types
 */

$info = array(
	'key'	=> 'vb5connect',
	'name'	=> 'vBulletin 5 Connect',
	'login'	=> true,
);

class admin_convert_board_vb5connect extends ipsCommand
{
	public $name = 'vBulletin 5 Connect';
	
	/**
	 * Bitwise settings - Forum Options
	 *
	 * @access	private
	 * @var 	array
	 **/
	private $FORUMOPTIONS = array(
			'active'            =>	1,
			'allowposting'      =>	2,
			'cancontainthreads' =>	4,
			'moderatenewpost'   =>	8,
			'moderatenewthread' =>	16,
			'moderateattach'    =>	32,
			'allowbbcode'       =>	64,
			'allowimages'       =>	128,
			'allowhtml'         =>	256,
			'allowsmilies'      =>	512,
			'allowicons'        =>	1024,
			'allowratings'      =>	2048,
			'countposts'        =>	4096,
			'canhavepassword'   =>	8192,
			'indexposts'        =>	16384,
			'styleoverride'     =>	32768,
			'showonforumjump'   =>	65536,
			'warnall'           =>	131072
	);
	
	/**
	 * Bitwise settings - User forum permissions
	 *
	 * @access	private
	 * @var 	array
	 **/
	private $USER_FORUM = array(
			'canview'           =>	1,
			'canviewothers'     =>	2,
			'cansearch'         =>	4,
			'canemail'          =>	8,
			'canpostnew'        =>	16,
			'canreplyown'       =>	32,
			'canreplyothers'    =>	64,
			'caneditpost'       =>	128,
			'candeletepost'     =>	256,
			'candeletethread'   =>	512,
			'canopenclose'      =>	1024,
			'canmove'           =>	2048,
			'cangetattachment'  =>	4096,
			'canpostattachment' =>	8192,
			'canpostpoll'       =>	16384,
			'canvote'           =>	32768,
			'canthreadrate'     =>	65536,
			'isalwaysmoderated' =>	131072,
			'canseedelnotice'   =>	262144
	);
	
	/**
	 * Bitwise settings - User groups
	 *
	 * @access	private
	 * @var 	array
	**/
	private $USER_PERM = array(
			'ismoderator'         => 	1,
			'cancontrolpanel'     => 	2,
			'canadminsettings'    => 	4,
			'canadminstyles'      => 	8,
			'canadminlanguages'   => 	16,
			'canadminforums'      => 	32,
			'canadminthreads'     => 	64,
			'canadmincalendars'   => 	128,
			'canadminusers'       => 	256,
			'canadminpermissions' => 	512,
			'canadminfaq'         => 	1024,
			'canadminimages'      => 	2048,
			'canadminbbcodes'     => 	4096,
			'canadmincron'        => 	8192,
			'canadminmaintain'    => 	16384,
			'canadminupgrade'     =>	32768
	);
	
	/**
	 * Bitwise settings - Mod permissions
	 *
	 * @access	private
	 * @var 	array
	 **/
	private $MOD_PERM = array(
			'caneditposts'           => 	1,
			'candeleteposts'         => 	2,
			'canopenclose'           => 	4,
			'caneditthreads'         => 	8,
			'canmanagethreads'       => 	16,
			'canannounce'            => 	32,
			'canmoderateposts'       => 	64,
			'canmoderateattachments' => 	128,
			'canmassmove'            => 	256,
			'canmassprune'           => 	512,
			'canviewips'             => 	1024,
			'canviewprofile'         => 	2048,
			'canbanusers'            => 	4096,
			'canunbanusers'          => 	8192,
			'newthreademail'         => 	16384,
			'newpostemail'           => 	32768,
			'cansetpassword'         => 	65536,
			'canremoveposts'         => 	131072,
			'caneditsigs'            => 	262144,
			'caneditavatar'          => 	524288,
			'caneditpoll'            => 	1048576,
			'caneditprofilepic'      => 	2097152
	);
	
	
	public function doExecute( ipsRegistry $registry )
	{
		$this->actions = array(
			'custom_bbcode'		=>	array(),
			'emoticons'			=> 	array(),
			'pfields'			=> 	array(),
			'forum_perms'		=> 	array(),
			'groups'			=> 	array('forum_perms'),
			'members'			=> 	array('groups'),
			'profile_friends'	=> 	array('members'),
			'ignored_users'		=> 	array('members'),
 			'forums'			=> 	array(),
			'moderators'		=> 	array('groups', 'members', 'forums'),
 			'topics'			=> 	array('forums', 'members'),
 			'posts'				=> 	array('topics'),
			'reputation_index' 	=> 	array('members', 'posts'),
			'polls'				=> array( 'members', 'forums', 'topics' ),
			'ranks'				=> 	array(),
			'pms'				=> array( 'members' ),
			'attachments_type'	=> 	array(),
			'attachments'		=> 	array('attachments_type', 'posts'),
			'warn_logs'			=> 	array('members'),
		);
		
		require_once( IPSLib::getAppDir( 'convert' ) . '/sources/lib_master.php' );
		$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'convert' ) . '/sources/lib_board.php', 'lib_board', 'convert' );
		$this->lib = new $classToLoad( $registry, $html, $this );
		
		$this->html = $this->lib->loadInterface();
		$this->lib->sendHeader( 'vBulletin 5 Connect &rarr; IP.Board Converter' );
		
		$this->HB = $this->lib->connect();
		
		if ( array_key_exists( $this->request['do'], $this->actions ) )
		{
			call_user_func( array( $this, 'convert_' . $this->request['do'] ) );
		}
		else
		{
			$this->lib->menu();
		}
		
		$this->sendOutput();
	}
	
	public function sendOutput()
	{
		$this->registry->output->html .= $this->html->convertFooter();
		$this->registry->output->html_main .= $this->registry->output->global_template->global_frame_wrapper();
		$this->registry->output->sendOutput();
		exit;
	}
	
	public function countRows( $action )
	{
		
		if( in_array( $action, array( 'forums', 'topics', 'posts' ) ) )
		{
			$forums = $this->fetch_forums();
		}
		
		switch( $action )
		{
			case 'members':
				return $this->lib->countRows('user');
				break;

			case 'groups':
			case 'forum_perms':
				return $this->lib->countRows('usergroup');
				break;

			case 'forums':
				return count($forums);
				break;

			case 'topics':
				return $this->lib->countRows('node', "(contenttypeid = {$this->fetch_type( 'Text' )} OR contenttypeid = {$this->fetch_type( 'Poll' )}) AND parentid IN(" . implode( ",", array_keys( $forums ) ) . ")");
				break;
					
			case 'posts':
				return $this->lib->countRows('node', "contenttypeid IN( {$this->fetch_type( 'Text' )}, {$this->fetch_type( 'Gallery' )}, {$this->fetch_type( 'Video' )}, {$this->fetch_type( 'Link' )} )");
				break;
				
			case 'attachments':
				return $this->lib->countRows('node', "contenttypeid = {$this->fetch_type( 'Attach' )}" );
				break;
			
			case 'attachments_type':
				return $this->lib->countRows('attachmenttype');
				break;
			
			case 'custom_bbcode':
				return $this->lib->countRows('bbcode');
				break;
				
			case 'pfields':
				return $this->lib->countRows('profilefield');
				break;
			
			case 'emoticons':
				return $this->lib->countRows('smilie');
				break;
				
			case 'moderators':
				return $this->lib->countRows('moderator', "nodeid != '-1'");
				break;
				
			case 'profile_friends':
				return $this->lib->countRows('userlist', "type='buddy'");
				break;
			
			case 'ignored_users':
				return $this->lib->countRows('userlist', "type='ignore'");
				break;

			case 'reputation_index':
				return $this->lib->countRows('reputation');
				break;

			case 'ranks':
				return $this->lib->countRows('usertitle');
				break;

			case 'warn_logs':
				return $this->lib->countRows('infraction');
				break;
			
			case 'polls':
				return $this->lib->countRows('poll');
				break;
			
			case 'pms':
				$rootNodeId = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'nodeid', 'from' => 'channel', 'where' => "guid = 'vbulletin-4ecbdf567f3da8.31769341'" ) );
				$count = ipsRegistry::DB('hb')->buildAndFetch( array(
					'select'	=> 'count(*) AS count',
					'from'		=> array( 'privatemessage' => 'pm' ),
					'add_join'	=> array(
						array(
							'select'	=> 'n.*',
							'from'		=> array( 'node' => 'n' ),
							'where'		=> 'pm.nodeid = n.nodeid',
							'type'		=> 'left',
						),
					),
					'where'		=> "pm.msgtype = 'message' AND n.parentid = {$rootNodeId['nodeid']}",
				) );
				return $count['count'];
				break;

			default:
				return $this->lib->countRows($action);
				break;
		}
	}
	
	public function checkConf( $action )
	{
		switch( $action )
		{
			case 'members':
			case 'groups':
			case 'forum_perms':
			case 'attachments':
				return true;
				break;

			default:
				return false;
				break;
		}
	}
	
	/**
	 * I'm making this public, rather than private, for two reasons.
	 * 1. Save me from having to make repeat code for the Gallery and Blog versions.
	 * 2. Due to the way vB5 is built (everything contained in a node table) this particular converter will
	 *		be loaded alongside the main libraries in the Blog and Gallery converters as it has a general
	 *		API method to load data from the node table. May as well use this in the same way.
	 *
	 * What we'll probably want to do here.. vBulletin still uses BB Code, so what we COULD do is maybe
	 * load up the post parsing libraries and rather then store the post as BB Code, convert it and parse it
	 * on the fly. I'm not sure how viable that will be, but it will save us some headache later on if we can
	 * get them to IPB 3.4 parsed format, rather then stored as BB Code.
	 */
	public function fixPostData( $post )
	{
		return $post;
	}
	
	
	/**
	 * Convert Forum Permission Sets
	 */
	private function convert_forum_perms()
	{
		/* Do we have more info? */
		$this->lib->saveMoreInfo( 'forum_perms', 'map' );
		
		/* Set up */
		$main = array(
			'select'	=> '*',
			'from'		=> 'usergroup',
			'order'		=> 'usergroupid ASC',
		);
		
		$loop = $this->lib->load( 'forum_perms', $main, array(), array(), TRUE );
		
		/* Get more info if necessary */
		$this->lib->getMoreInfo( 'forum_perms', $loop, array( 'new' => '-- Create new set --', 'ot' => 'Old permission set', 'nt' => 'New permission set' ), '', array( 'idf' => 'usergroupid', 'nf' => 'title' ) );
		
		/* Loopy-loo */
		foreach( $loop AS $row )
		{
			$this->lib->convertPermSet( $row['usergroupid'], $row['title'] );
		}
		
		$this->lib->next();
	}
	
	/**
	 * Convert Member Groups
	 */
	private function convert_groups()
	{
		$this->lib->saveMoreInfo( 'groups', 'map' );
		
		$main = array(
			'select'	=> '*',
			'from'		=> 'usergroup',
			'order'		=> 'usergroupid ASC',
		);
		
		$loop = $this->lib->load( 'groups', $main, array(), array(), TRUE );
		
		$this->lib->getMoreInfo( 'groups', $loop, array( 'new' => '-- Create new set --', 'ot' => 'Old permission set', 'nt' => 'New permission set' ), '', array( 'idf' => 'usergroupid', 'nf' => 'title' ) );
		
		foreach( $loop AS $row )
		{
			// Silly bitwise permissions
			foreach( $this->USER_FORUM as $name => $bit )
			{
				$row[ $name ] = ( $row['forumpermissions'] & $bit ) ? 1 : 0;
			}
			
			foreach( $this->USER_PERM as $name => $bit )
			{
				$row[ $name ] = ( $row['adminpermissions'] & $bit ) ? 1 : 0;
			}

			$save = array(
				'g_title'				=> $row['title'],
				'g_max_messages'		=> $row['pmquota'],
				'g_max_mass_pm'			=> $row['pmsendmax'],
				'prefix'				=> $row['opentag'],
				'suffix'				=> $row['closetag'],
				'g_view_board'			=> $row['canview'],
				'g_mem_info'			=> 1,
				'g_other_topics'		=> $row['canviewothers'],
				'g_use_search'			=> $row['cansearch'],
				'g_email_friend'		=> $row['canemail'],
				'g_invite_friend'		=> 1,
				'g_edit_profile'		=> $row['canmodifyprofile'],
				'g_post_new_topics'		=> $row['canpostnew'],
				'g_reply_own_topics'	=> 1,
				'g_reply_other_topics'	=> isset($row['canreplyothers']) && $row['canreplyothers'] == 0 ? 0 : 1,
				'g_edit_posts'			=> $row['caneditpost'],
				'g_delete_own_posts'	=> $row['candeletepost'],
				'g_open_close_posts'	=> $row['canopenclose'],
				'g_delete_own_topics'	=> $row['candeletethread'],
				'g_post_polls'		 	=> $row['canpostpoll'],
				'g_vote_polls'		 	=> $row['canvote'],
				'g_use_pm'			 	=> $row['pmpermissions'] != 0 ? 1 : 0,
				'g_is_supmod'		 	=> $row['ismoderator'],
				'g_access_cp'		 	=> $row['cancontrolpanel'],
				'g_access_offline'	 	=> $row['cancontrolpanel'],
				'g_avoid_q'			 	=> $row['ismoderator'],
				'g_avoid_flood'		 	=> $row['ismoderator'],
				'g_perm_id'				=> $row['usergroupid'],
			);
			
			$this->lib->convertGroup( $row['usergroupid'], $save );
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
			// Members will now do WHERE id>x instead of LIMIT 500000, 250;
			
			$this->lib->useKey('u.userid');
			
			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$pcpf = array(
				'icq'			=> 'ICQ Number',
				'aim'			=> 'AIM ID',
				'yahoo'			=> 'Yahoo ID',
				'msn'			=> 'MSN ID',
				'skype'			=> 'Skype ID',
				'homepage'		=> 'Website',
				);

			$this->lib->saveMoreInfo( 'members', array_merge( array_keys($pcpf), array( /*'avvy_path',*/ 'pp_path', 'pp_type' ) ) );

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> 'u.*',
							'from' 		=> array('user' => 'u'),
							'order'		=> 'u.userid ASC',
							'add_join'	=> array(
											array( 	'select' => 't.*',
													'from'   =>	array( 'usertextfield' => 't' ),
													'where'  => "u.userid = t.userid",
													'type'   => 'left'
												),
											array( 	'select' => 'c.*',
													'from'   =>	array( 'userfield' => 'c' ),
													'where'  => "u.userid = c.userid",
													'type'   => 'left'
												),
											),
						);


			$loop = $this->lib->load('members', $main, 'profile_portal');

			//-----------------------------------------
			// Tell me what you know!
			//-----------------------------------------

			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			$ask = array();

			// We need to know the avatars path
			$ask['pp_path'] = array('type' => 'text', 'label' => 'The path to the folder where your custom profile pictures/avatars are saved (no trailing slash - usually /path_to_vb/customprofilepics or /path_to_vb/customavatars). If profile pictures are stored in the database enter \'.\':');
			$ask['pp_type'] = array ( 'type' => 'dropdown', 'label' => 'The Member Photo type to convert?', 'options' => array ( 'avatar' => 'Avatars', 'profile' => 'Profile Pictures' ) );

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

			//-----------------------------------------
			// Get our custom profile fields
			//-----------------------------------------
			
			$this->DB->build(array('select' => '*', 'from' => 'conv_link', 'where' => "type='pfields' AND app={$this->lib->app['app_id']}"));
			$fieldRes = $this->DB->execute();
			
			$pfields = array();
			while ( $row = $this->DB->fetch( $fieldRes ) )
			{
				$pfields[] = $row;
			}

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				//-----------------------------------------
				// Set info
				//-----------------------------------------

				// Basic info
				$info = array(
								'id'             	=> $row['userid'],
								'username'     	 	=> $row['username'],
								'email'			 	=> $row['email'],
								'group'			 	=> $row['usergroupid'],
								'secondary_groups'	=> $row['membergroupids'],
								'joined'			=> $row['joindate'],
								'password'		 	=> $row['password'],
								);

				// Member info
				$birthday = ($row['birthday']) ? explode('-', $row['birthday']) : null;

				$members = array(
					'title'				=> strip_tags($row['usertitle']),
					'last_visit'		=> $row['lastvisit'],
					'last_activity'		=> $row['lastactivity'],
					'last_post'			=> $row['lastpost'],
					'posts'				=> ( $row['posts'] < 9999999 ) ? $row['posts'] : 0,
					'time_offset'		=> $row['timezoneoffset'],
					'bday_day'			=> ($row['birthday']) ? $birthday[1] : '',
					'bday_month'		=> ($row['birthday']) ? $birthday[0] : '',
					'bday_year'			=> ($row['birthday']) ? $birthday[2] : '',
					'ip_address'		=> $row['ipaddress'],
					'misc'				=> $row['salt'],
					'warn_level'		=> $row['warnings'],
					'member_banned'		=> 0,
					'temp_ban'			=> '',
					);

				// Profile
				$profile = array(
					'pp_reputation_points'		=> $row['reputation'],
					//'pp_friend_count'			=> $row['friendcount'],
					'signature'					=> $this->fixPostData($row['signature']),
					'pp_setting_count_friends' 	=> 1,
					'pp_setting_count_comments' => 1,
					//'pp_main_photo'				=> '',
					'pp_main_width'				=> 0,
					'pp_main_height'			=> 0,
					);

				//-----------------------------------------
				// Has he been a naughty boy?
				//-----------------------------------------

				$ban = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'userban', 'where' => "userid='{$row['userid']}'" ) );
				if ($ban)
				{
					// Permanant?
					if ($ban['liftdate'] == 0)
					{
						$members['member_banned'] = 1;
					}
					// Or just temporary?
					else
					{
						//-----------------------------------------
						// Work out the length... this could be fun..
						//-----------------------------------------

						$inseconds = $ban['liftdate'] - $ban['bandate'];

						if (($inseconds / 86400) >= 1)
						{
							// It's at least a day...
							$indays = round($inseconds / 86400);
							$length = "{$indays}:d";
						}
						else
						{
							// It's less than a day...
							$inhours = round($inseconds / 3600);
							$length = "{$inhours}:h";
						}

						//-----------------------------------------
						// Save
						//-----------------------------------------

						$members['temp_ban'] = "{$ban['bandate']}:{$ban['liftdate']}:{$length}";
					}
				}

				//-----------------------------------------
				// Custom Profile fields
				//-----------------------------------------

				// We have to make sure ALL are present due to extended inserts
				foreach( $options as $id => $title )
				{
					if ( $id != 'x' )
					{
						$custom['field_' . $id] = $row[$id] ? $row[$id] : '';
					}
				}
				
				// Pseudo
				foreach ($pcpf as $id => $name)
				{
					if ($us[$id] != 'x')
					{
						$custom['field_'.$us[$id]] = $row[$id];
					}
				}

				// Actual
				foreach ($pfields as $field)
				{
					$custom['field_'.$field['ipb_id']] = $row['field'.$field['foreign_id']];

					// Multi-selects
					if ( $row['field'.$field['foreign_id']] and @array_key_exists( $field['foreign_id'], $us['multi_fields'] ) )
					{
						$options = unserialize( $us['multi_fields'][ $field['foreign_id'] ] );
						$f_options = array();
						$i = 1;
						for ( $j = 0; $j < count( $options ); $j++ )
						{
							$f_options[ $j ] = $i;
							$i <<= 1;
						}

						$final = array();
						foreach( $f_options as $ipbid => $bit )
						{
							if ( $row['field'.$field['foreign_id']] & $bit )
							{
								$final[] = $ipbid;
							}
						}

						$custom['field_'.$field['ipb_id']] = '|' . implode( '|', $final ) . '|';

					}
				}

				//-----------------------------------------
				// Avatars and profile pictures
				//-----------------------------------------
				$profile['photo_type'] = 'custom';
				if ( $us['pp_type'] == 'avatar' )
				{
					$customavatar = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'customavatar', 'where' => "userid='{$row['userid']}'" ) );
					if ($customavatar)
					{
						if ($customavatar['filedata'])
						{
							$profile['photo_data'] = $customavatar['filedata'];
						}
						else
						{
							$profile['pp_main_photo'] = "avatar{$customavatar['userid']}_{$row['avatarrevision']}.gif";
						}
						
						$profile['pp_main_width'] = $customavatar['width'];
						$profile['pp_main_height'] = $customavatar['height'];
						$profile['photo_filesize'] = $customavatar['filesize'];
					}
				}
				else
				{
					$customprofilepic = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'customprofilepic', 'where' => "userid='{$row['userid']}'" ) );
					if ($customprofilepic)
					{
						if ($customprofilepic['filedata'])
						{
							$profile['photo_data'] = $customprofilepic['filedata'];
						}
						else
						{
							$profile['pp_main_photo'] = "profilepic{$customprofilepic['userid']}_{$row['profilepicrevision']}.gif";
						}
	
						$profile['pp_main_width'] = $customprofilepic['width'];
						$profile['pp_main_height'] = $customprofilepic['height'];
						$profile['photo_filesize'] = $customprofilepic['filesize'];
					}
				}

				//-----------------------------------------
				// Go
				//-----------------------------------------

				$this->lib->convertMember($info, $members, $profile, $custom, $us['pp_path']);
				$this->lib->setLastKeyValue($info['id']);
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
			
			//-----------------------------------------
			// Work stuff out
			//-----------------------------------------
			
			// Permissions will need to be reconfigured
			$perms = array();
			
			// Silly bitwise permissions
			foreach( $this->FORUMOPTIONS as $name => $bit ) {
				$row[ $name ] = ( $row['nodeoptions'] & $bit ) ? 1 : 0;
			}
			
			// Is this forum on mod queue?
			$moderation = 0;
			if ($row['moderatenewpost'] and $row['moderatenewthread'])
			{
				$moderation = 1;
			}
			elseif ($row['moderatenewthread'])
			{
				$moderation = 2;
			}
			elseif ($row['moderatenewpost'])
			{
				$moderation = 1;
			}
			
			//---------------------------
			// Set up
			//---------------------------

			$main	= array(
				'select'	=> 'n.*',
				'from'		=> array('node'	=> 'n'),
				'add_join'	=> array(
									array(
									'select'	=> 'cl.*',
									'from'		=> array('closure'	=> 'cl'),
									'where'		=> 'cl.child = n.nodeid'
											),
								),
				'where'		=> "n.nodeid <> 2 AND cl.parent=2 AND n.contenttypeid = {$this->fetch_type( 'Channel' )}",
				'order'		=> 'n.displayorder ASC, n.title ASC'
			);

			$loop = $this->lib->load('forums', $main, array('forum_tracker'));

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{

				$perms = array();
				//-----------------------------------------
				// Save
				//-----------------------------------------

				$save = array(

					'last_post'		  	=> $row['lastcontent'],
					'last_poster_name'	=> $row['lastcontentauthor'],
					'parent_id'		  	=> $row['parentid'],
					'name'			  	=> IPSText::htmlspecialchars($row['title']),
					'description'	  	=> $row['description'],
					'position'		  	=> $row['displayorder'],
					'use_ibc'		  	=> 1,
					'use_html'		  	=> 0,
					'inc_postcount'	  	=> 1,
					'preview_posts'		=> $moderation,
					'forum_allow_rating'=> 1,
					'topics'			=> $row['textcount'],
					'posts'				=> $row['totalcount'],
					);

				$this->lib->convertForum($row['nodeid'], $save, $perms);

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
			$this->lib->useKey('n.nodeid');
				
			//---------------------------
			// Set up
			//---------------------------
			
			$forums = $this->fetch_forums();
		
			$main = array(	'select' 	=> 'n.*, t.rawtext',
					'from' 		=> array( 'node' => 'n' ),
					'where'		=> "n.parentid IN (" . implode( ",", array_keys( $forums ) ) . ") AND (n.contenttypeid = {$this->fetch_type( 'Text' )} OR n.contenttypeid = {$this->fetch_type( 'Poll' )})",
					'add_join'	=> array(
							array(
									'from'		=> array('text'	=> 't'),
									'where'		=> 't.nodeid = n.nodeid'
							)),
					'order'		=> 'n.nodeid ASC',
			);
		
			$loop = $this->lib->load('topics', $main, array('tracker'));
		
			$this->lib->prepareDeletionLog('topics');
		
			//---------------------------
			// Loop
			//---------------------------
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{

				$save = array( 'title'			  => $row['title'],
						'state'			  => ($row['open'] == 1) ? 'open' : 'closed',
						'posts'			  => $row['totalcount'],
						'starter_id'		  => $row['userid'],
						'starter_name'	  => $row['authorname'],
						'start_date'		  => $row['publishdate'],
						'last_post'		  => $row['lastcontent'],
						'last_poster_name' => $row['lastcontentauthor'],
						'last_poster_id'  => $row['lastauthorid'],
						'forum_id'		  => $row['parentid'],
						'approved'		  => $row['approved'],
						'pinned'			  => $row['sticky'],
				);
		
				$this->lib->convertTopic($row['nodeid'], $save);
				
				
				$this->lib->setLastKeyValue($row['nodeid']);
		
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
			$this->lib->useKey('n.nodeid');
			
			//---------------------------
			// Set up
			//---------------------------
			$main = array( 'select' => 'n.*, t.rawtext',
						   'from'   => array( 'node' => 'n' ),
						   'where'	=> "n.contenttypeid IN( {$this->fetch_type( 'Text' )}, {$this->fetch_type( 'Gallery' )}, {$this->fetch_type( 'Video' )}, {$this->fetch_type( 'Link' )}, {$this->fetch_type( 'Poll' )} ) ",
							'add_join'	=> array(
									array(
											'from'		=> array('text'	=> 't'),
											'where'		=> 't.nodeid = n.nodeid'
									),
							),
						   'order'  => 'n.nodeid ASC' );

			$loop = $this->lib->load('posts', $main);
			$this->lib->prepareDeletionLog('posts');
			
			$forums = $this->fetch_forums();

			//---------------------------
			// Loop
			//---------------------------
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save = array( 'author_id'		=> $row['userid'],
							   'author_name' 	=> $row['authorname'],
							 //  'use_sig'     	=> $row['showsignature'],
							   'use_emo'     	=> 1,
							   'ip_address' 	=> $row['ipaddress'],
							   'post_date'   	=> $row['created'],
							   'post'		 	=> $this->fixPostData( $row['rawtext'] ),
							   'queued'      	=> $row['showpublished'] == 0 ? 2 : 0,
							   'pdelete_time'	=> intval($row['unpublishdate']),
						 );
				
				if ( in_array( $row['parentid'], array_keys( $forums ) ) )
				{
					$save['topic_id'] = $row['nodeid'];
				}
				else if ( $this->lib->getLink( $row['parentid'] , 'topics', true) )
				{
					$save['topic_id'] = $row['parentid'];
				}
				else if ( $link = $this->lib->getLink( $row['parentid'] , 'posts', true) )
				{
					// Get the parent post
					$post = ipsRegistry::DB('hb')->buildAndFetch( array(
							'select' => '*',
							'from' => array( 'node' => 'n' ),
							'add_join'	=> array(
									array(
											'from'		=> array('text'	=> 't'),
											'where'		=> 't.nodeid = n.nodeid'
									)),
							'where' => "n.nodeid='{$row['parentid']}'"
					) );
					
					// Is the parent post hidden? If so hide this too.
					$save['queued'] = ( $post['showpublished'] == 0 || $row['showpublished'] == 0 ) ? 2 : 0;
					
					$save['topic_id'] = $post['parentid'];
						
					$save['post'] = "[quote name='" . $post['authorname'] ."' timestamp='" . $post['created'] . "']" . $this->fixPostData( $post['rawtext'] ) . "[/quote]" . $save['post'];
				}

				$this->lib->convertPost($row['nodeid'], $save);
				$this->lib->setLastKeyValue($row['nodeid']);

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
					'from' 		=> 'attachmenttype',
			);
		
			$loop = $this->lib->load('attachments_type', $main);
		
			//---------------------------
			// Loop
			//---------------------------
		
			$count = $this->request['st'];
		
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$count++;
		
				$rm = unserialize($row['mimetype']);
				$mime = str_replace('Content-type: ', '', $rm[0]);
		
				$save = array(
						'atype_extension'	=> $row['extension'],
						'atype_mimetype'	=> $mime,
				);
		
				$this->lib->convertAttachType($count, $save);
			}
		
			$this->lib->next();
		
		}
		
		/**
		 * Convert Attachments
		 *
		 * From what I can tell so far vb5 stores all attachments in the DB
		 * We may need to alter this if that turns out not to be the case.
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_attachments()
		{
			$this->lib->useKey('attachment.nodeid');
				
			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------
		
			$this->lib->saveMoreInfo('attachments', array('attach_path'));
			
		
			//---------------------------
			// Set up
			//---------------------------
			
			$main = array(
				'select'	=> 'attachment.*',
				'from'		=> array( 'node' => 'attachment' ),
				'add_join'	=> array(
					array(
						'select'	=> 'post.nodeid AS post_id, post.parentid AS topic_id',
						'from'		=> array( 'node' => 'post' ),
						'where'		=> 'attachment.parentid = post.nodeid',
						'type'		=> 'left',
					),
					array(
						'select'	=> 'attach.filename AS attach_filename',
						'from'		=> array( 'attach' => 'attach' ),
						'where'		=> 'attachment.nodeid = attach.nodeid',
						'type'		=> 'left',
					),
					array(
						'select'	=> 'fd.*',
						'from'		=> array( 'filedata' => 'fd' ),
						'where'		=> 'fd.filedataid = attach.filedataid',
						'type'		=> 'left',
					)
				),
				'where'		=> "attachment.contenttypeid = {$this->fetch_type( 'Attach' )}",
				'order'		=> 'attachment.nodeid ASC',
			);
		
			$loop = $this->lib->load('attachments', $main);
		
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
				$this->lib->setLastKeyValue( $row['nodeid'] );
		
				//-----------------------------------------
				// Init
				//-----------------------------------------
		
				// What's the mimetype?
				$type = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'attachments_type', 'where' => "atype_extension='{$row['extension']}'" ) );
				
				$image = 0;
				if ( preg_match( '/image/', $type['atype_mimetype'] ) )
				{
					$image = 1;
				}
		
				if ( ! $row['topic_id'] )
				{
					$this->lib->logError ( $row['attachmentid'], 'Orphaned Attachment - Post Missing' );
					continue;
				}
		
				$save = array(
						'attach_ext'			=> $row['extension'],
						'attach_file'			=> "conv_" . $row['filedataid'] . "." . $row['extension'],
						'attach_is_image'		=> $image,
						'attach_date'			=> $row['dateline'],
						'attach_member_id'		=> $row['userid'],
						'attach_filesize'		=> $row['filesize'],
						'attach_rel_id'			=> $row['post_id'],
						'attach_rel_module'		=> 'post',
						'attach_parent_id'		=> $row['topic_id'],
						'attach_location'		=> '',
						'attach_hits'			=> 0,
				);

				//-----------------------------------------
				// Database
				//-----------------------------------------
				if ($row['filedata'])
				{
					$save['attach_location'] = $row['filename'];
					$save['data'] = $row['filedata'];
		
					$done = $this->lib->convertAttachment($row['filedataid'], $save, '', true);
				}
		
				//-----------------------------------------
				// File storage
				//-----------------------------------------
		
				else
				{
					if ($path == '.')
					{
						// Race issue... seen it a couple times, the file data is lost but the row still exists.
						$this->lib->logError( $row['filedataid'], 'No File Data' );
						continue;
					}
					
					$tmpPath = '/' . implode('/', preg_split('//', $row['userid'],  -1, PREG_SPLIT_NO_EMPTY));
					$save['attach_location'] = "{$row['filedataid']}.attach";

					$done = $this->lib->convertAttachment($row['filedataid'], $save, $path . $tmpPath);
				}
		
				//-----------------------------------------
				// Fix inline attachments
				//-----------------------------------------
		
				if ($done === true)
				{
					$aid = $this->lib->getLink($row['filedataid'], 'attachments', true);
					$pid = $this->lib->getLink($save['attach_rel_id'], 'posts', true);
		
					if ( $pid )
					{
						$attachrow = $this->DB->buildAndFetch( array( 'select' => 'post', 'from' => 'posts', 'where' => "pid={$pid}" ) );
		
						$rawaid = $row['filedataid'];
						$update = preg_replace("/\[ATTACH(.+?)\]n".$rawaid."\[\/ATTACH\]/i", "[attachment={$aid}:{$save['attach_location']}]", $attachrow['post']);
		
						$this->DB->update('posts', array('post' => $update), "pid={$pid}");
					}
				}
		
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
					'from' 		=> 'bbcode',
					'order'		=> 'bbcodeid ASC',
			);
		
			$loop = $this->lib->load('custom_bbcode', $main);
		
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
				$replacement = str_replace('%1$s', '{content}', $row['bbcodereplacement']);
		
				$save = array(
						'bbcode_title'				=> $row['title'],
						'bbcode_desc'				=> $row['bbcodeexplanation'],
						'bbcode_tag'				=> $row['bbcodetag'],
						'bbcode_replace'			=> $replacement,
						'bbcode_useoption'			=> $row['twoparams'],
						'bbcode_example'			=> $row['bbcodeexample'],
						'bbcode_menu_option_text'	=> 'option',
						'bbcode_menu_content_text'	=> 'content',
						'bbcode_groups'				=> 'all',
						'bbcode_sections'			=> 'all',
						'bbcode_parse'				=> 2,
						'bbcode_app'				=> 'core',
				);
		
				$this->lib->convertBBCode($row['bbcodeid'], $save, $us['custom_bbcode_opt']);
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
					'from' 		=> 'profilefield',
					'order'		=> 'profilefieldid ASC',
			);
		
			$loop = $this->lib->load('pfields', $main, array('pfields_groups'));
		
			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			$us['multi_fields'] = array();
		
			//-----------------------------------------
			// Create an unfiled group
			//-----------------------------------------

			if (!$us['pfield_group'])
			{
				$group = $this->lib->convertPFieldGroup(99, array('pf_group_name' => 'Converted', 'pf_group_key' => 'vbulletin'), true);
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
				//-----------------------------------------
				// Sort out groups
				//-----------------------------------------

				$usegroup = false;
				if ($row['profilefieldcategoryid'])
				{
					$usegroup = true;
					if (!$this->lib->getLink($row['profilefieldcategoryid'], 'pfields_groups', true))
					{
						
						$group = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'profilefieldcategory', 'where' => "profilefieldcategoryid = '{$row['profilefieldcategoryid']}'" ) );
						$glang = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'text', 'from' => 'phrase', 'where' => "varname = 'category{$group['profilefieldcategoryid']}_title'" ) );
						$savegroup = array(
								'pf_group_name'	=> $glang['text'],
								'pf_group_key'	=> 'vbcat'.$group['profilefieldcategoryid'],
						);
						$this->lib->convertPFieldGroup($group['profilefieldcategoryid'], $savegroup);
					}
				}
		
				//-----------------------------------------
				// Now the data
				//-----------------------------------------
		
				// This phrase table is the most ridiculous idea yet...
				$lang_title = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'text', 'from' => 'phrase', 'where' => "varname = 'field{$row['profilefieldid']}_title'" ) );
				$lang_desc  = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'text', 'from' => 'phrase', 'where' => "varname = 'field{$row['profilefieldid']}_desc'" ) );
		
				// Implode data
				$data = array();
				if ($row['data'])
				{
					$tmpData = unserialize($row['data']);
					if ( is_array($tempData) )
					{
						foreach ( $tempData as $key => $value)
						{
							$data[] = "{$key}={$value}";
						}
					}
				}
				// Type?
				switch ($row['type'])
				{
					case 'textarea':
						$type = 'textarea';
						break;
		
					case 'radio':
						$type = 'radio';
						break;
		
					case 'select':
						$type = 'drop';
						break;
		
					case 'select_multiple':
					case 'checkbox';
					$type = 'cbox';
					$us['multi_fields'][ $row['profilefieldid'] ] = $row['data'];
					break;
		
					default:
						$type = 'input';
						break;
				}
		
				// Required?
				$not_null = 0;
				$reg = 0;
				if ($row['required'] == 1 or $row['required'] == 3)
				{
					$not_null = 1;
					$reg = 1;
				}
				if ($row['required'] == 2)
				{
					$reg = 1;
				}
		
				// Editable?
				$editable = 0;
				if ($row['editable'] == 0 or $row['editable'] == 2)
				{
					$editable = 1;
				}
		
				// Finalise
				$save = array(
						'pf_title'		=> $lang_title['text'],
						'pf_desc'		=> $lang_desc['text'],
						'pf_content'	=> implode('|', $data),
						'pf_type'		=> $type,
						'pf_not_null'	=> $not_null,
						'pf_member_hide'=> $row['hidden'],
						'pf_max_input'	=> $row['maxlength'],
						'pf_member_edit'=> $editable,
						'pf_position'	=> $row['displayorder'],
						'pf_show_on_reg'=> $reg,
						'pf_group_id'	=> ($usegroup) ? $row['profilefieldcategoryid'] : 99,
						'pf_key'		=> 'vb'.$row['profilefieldid']
				);

				// And save
				$this->lib->convertPField($row['profilefieldid'], $save);
			}
		
			// Save pfield_data
			$get[$this->lib->app['name']] = $us;
			IPSLib::updateSettings(array('conv_extra' => serialize($get)));
		
			// Next, please!
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
					'order'		=> 'smilieid ASC',
			);
		
			$loop = $this->lib->load('emoticons', $main);
		
			//-----------------------------------------
			// We need to know the path and how to handle duplicates
			//-----------------------------------------
		
			$this->lib->getMoreInfo('emoticons', $loop, array('emo_path' => array('type' => 'text', 'label' => 'The path to the folder where emoticons are saved (no trailing slash - usually path_to_vb/images/smilies):'), 'emo_opt'  => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate emoticons?') ), 'path' );
		
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
				//-----------------------------------------
				// But wait! What about those sneaky URL emoticons?
				//-----------------------------------------
				if ( preg_match( "#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i", $row['smiliepath'] ) )
				{
					$this->lib->logError ( $row['smiliepath'], 'Emoticon is a URL, and has not been converted. You need to download the emoticon and add it <a href="'.$this->settings['base_url'].'app=core&&module=posts&section=emoticons&do=overview" target="_blank">here</a>.' );
					continue;
				}
		
				$save = array(
						'typed'			=> $row['smilietext'],
						'image'			=> preg_replace('#^(.+)?images/smilies/(.+?)$#', '$2', $row['smiliepath']),
						'clickable'		=> 0,
						'emo_set'		=> 'default',
						'emo_position'	=> $row['displayorder'],
				);
				$done = $this->lib->convertEmoticon($row['smilieid'], $save, $us['emo_opt'], $path);
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
		
			$main = array(	'select' 	=> 'm.*',
					'from'		=> array( 'moderator' => 'm' ),
					'add_join'	=> array(
							array(	'select' => 'mem.username',
									'from'   => array( 'user' => 'mem' ),
									'where'  => 'm.userid = mem.userid',
									'type'   => 'inner'
							),
					),
					'order'		=> 'moderatorid ASC',
			);
		
			$loop = $this->lib->load('moderators', $main);
		
			//---------------------------
			// Loop
			//---------------------------
		
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				// We handle supermods slightly differently
				if ($row['nodeid'] == -1)
				{
					continue;
				}
		
				foreach( $this->MOD_PERM as $name => $bit ) {
					$row[ $name ] = ( $row['permissions'] & $bit ) ? 1 : 0;
				}
		
				$save = array(
						'forum_id'	  => $row['nodeid'],
						'member_name'  => $row['username'],
						'member_id'	  => $row['userid'],
						'edit_post'	  => $row['caneditposts'],
						'edit_topic'	  => $row['caneditthreads'],
						'delete_post'  => $row['candeleteposts'],
						'delete_topic' => $row['canmanagethreads'],
						'view_ip'	  => $row['canviewips'],
						'open_topic'	  => $row['canopenclose'],
						'close_topic'  => $row['canopenclose'],
						'mass_move'	  => $row['canmassmove'],
						'mass_prune'	  => $row['canmassprune'],
						'move_topic'	  => $row['canmanagethreads'],
						'pin_topic'	  => $row['canmanagethreads'],
						'unpin_topic'  => $row['canmanagethreads'],
						'post_q'		  => $row['canmoderateposts'],
						'topic_q'	  => $row['canmoderateposts'],
						'allow_warn'	  => 0,
						'is_group'	  => 0,
						'split_merge'  => $row['canmanagethreads'] );
		
		
				$this->lib->convertModerator($row['mid'], $save);
			}
		
			$this->lib->next();
		
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
					'from' 		=> 'userlist',
					'where'		=> "type='buddy'",
					'order'		=> 'userid ASC',
			);
		
			$loop = $this->lib->load('profile_friends', $main);
		
			//---------------------------
			// Loop
			//---------------------------
		
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save = array(
						'friends_member_id'	=> $row['userid'],
						'friends_friend_id'	=> $row['relationid'],
						'friends_approved'	=> 1,
				);
				$this->lib->convertFriend($row['userid'].'-'.$row['relationid'], $save);
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
					'from' 		=> 'userlist',
					'where'		=> "type='ignore'",
					'order'		=> 'userid ASC',
			);
		
			$loop = $this->lib->load('ignored_users', $main);
		
			//---------------------------
			// Loop
			//---------------------------
		
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save = array(
						'ignore_owner_id'	=> $row['userid'],
						'ignore_ignore_id'	=> $row['relationid'],
						'ignore_messages'	=> '1',
						'ignore_topics'		=> '1',
				);
				$this->lib->convertIgnore($row['userid'].'-'.$row['relationid'], $save);
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
					'from' 		=> 'reputation',
					'order'		=> 'reputationid ASC',
			);
		
			$loop = $this->lib->load('reputation_index', $main);
		
			//---------------------------
			// Loop
			//---------------------------
		
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save = array(
						'member_id'	=> $row['whoadded'],
						'app'		=> 'forums',
						'type'		=> 'pid',
						'type_id'	=> $row['nodeid'],
						'rep_date'	=> $row['dateline'],
						'rep_msg'	=> $row['reason'],
						'rep_rating'=> ($row['reputation'] > 0) ? 1 : -1,
				);
				
				$this->lib->convertRep($row['reputationid'], $save);
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
					'from' 		=> 'usertitle',
					'order'		=> 'usertitleid ASC',
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
				$save = array(
						'posts'	=> $row['minposts'],
						'title'	=> $row['title'],
				);
				$this->lib->convertRank($row['usertitleid'], $save, $us['rank_opt']);
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

			$main = array(	'select' 	=> 'i.*',
							'from' 		=> array( 'infraction' => 'i' ),
							'add_join'	=> array(
								array(
									'select'	=> 'n.*',
									'from'		=> array( 'node' => 'n' ),
									'where'		=> 'i.nodeid = n.nodeid',
									'type'		=> 'left',
								),
							),
							'order'		=> 'i.nodeid ASC',
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

				$log = unserialize($row['log_data']);
				$level = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'infractionlevel', 'where' => "infractionlevelid = {$row['infractionlevelid']}" ) );
				
				$save = array(
					'wl_member'			=> $row['infracteduserid'],
					'wl_note_member'	=> $this->fixPostData($row['description']),
					'wl_date'			=> $row['publishdate'],
					'wl_moderator'		=> $row['userid'],
					'wl_points'			=> $level['points'],
				);

				//-----------------------------------------
				// Pass it on
				//-----------------------------------------

				$this->lib->convertWarn($row['nodeid'], $save);
			}

			$this->lib->next();

		}
		
		private function convert_polls()
		{
			$main = array(
				'select'	=> 'p.*',
				'from'		=> array( 'poll' => 'p' ),
				'add_join'	=> array(
					array(
						'select'	=> 'n.*',
						'from'		=> array( 'node' => 'n' ),
						'where'		=> "p.nodeid = n.nodeid",
						'type'		=> 'left',
					),
				),
				'order'		=> 'p.nodeid ASC',
			);
			
			$loop = $this->lib->load( 'polls', $main, array( 'voters' ) );
			
			while( $row = ipsRegistry::DB('hb')->fetch( $this->lib->queryRes ) )
			{
				// Votes
				$votes = array();
				
				foreach( ipsRegistry::DB('hb')->buildAndFetchAll( array( 'select' => '*', 'from' => 'pollvote', 'where' => "nodeid = {$row['nodeid']}" ) ) AS $key => $vote )
				{
					$choice = array();
					
					if ( ! $vote['userid'] OR in_array( $vote['userid'], $votes ) )
					{
						continue;
					}
					
					foreach( ipsRegistry::DB('hb')->buildAndFetchAll( array( 'select' => '*', 'from' => 'pollvote', 'where' => "nodeid = {$row['nodeid']} AND userid = {$vote['userid']}" ) ) AS $k => $v )
					{
						$option = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => '*', 'from' => 'polloption', 'where' => "polloptionid = {$v['polloptionid']}" ) );
						$choice[$v['voteoption']] = str_replace( "'", "&#39;", $option['title'] );
					}
					
					$vsave = array(
						'vote_date'			=> $v['votedate'],
						'tid'				=> $v['nodeid'],
						'forum_id'			=> $row['parentid'],
						'member_id'			=> $v['userid'],
						'member_choices'	=> serialize( array( 1 => $choice ) ),
					);
					
					$this->lib->convertPollVoter( $v['pollvoteid'], $vsave );
				}
				
				$vb5choices	= unserialize( $row['options'] );
				$ipschoices	= array();
				$ipsvotes	= array();
				
				foreach( $vb5choices AS $k => $option )
				{
					$ipschoices[]	= $option['title'];
					$ipsvotes[]		= $option['votes'];
				}
				
				$poll_array = array(
					1 => array(
						'question'	=> $row['description'],
						'multi'		=> $row['multiple'],
						'choice'	=> $ipschoices,
						'votes'		=> $ipsvotes,
					),
				);
				
				$save = array(
					'tid'				=> $row['nodeid'],
					'start_date'		=> $row['publishdate'],
					'choices'			=> serialize( $poll_array ),
					'starter_id'		=> $row['userid'],
					'votes'				=> $row['votes'],
					'forum_id'			=> $row['parentid'],
					'poll_question'		=> $row['title'],
					'poll_view_voters'	=> $row['public'],
				);
				
				$this->lib->convertPoll( $row['nodeid'], $save );
			}
			
			$this->lib->next();
		}
		
		private function convert_pms()
		{
			$this->lib->useKey( 'pm.nodeid' );
			
			// Schtuuuuupid. I really hope the GUID here isn't dynamically generated at download time.
			$rootNodeId = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'nodeid', 'from' => 'channel', 'where' => "guid = 'vbulletin-4ecbdf567f3da8.31769341'" ) );
			$main = array(
				'select'	=> 'pm.*',
				'from'		=> array( 'privatemessage' => 'pm' ),
				'add_join'	=> array(
					array(
						'select'	=> 'n.*',
						'from'		=> array( 'node' => 'n' ),
						'where'		=> "pm.nodeid = n.nodeid",
						'type'		=> 'left',
					),
				),
				'where'		=> "pm.msgtype = 'message' AND n.parentid = {$rootNodeId['nodeid']}",
				'order'		=> "pm.nodeid ASC"
			);
			
			$loop = $this->lib->load( 'pms', $main );
			
			while( $row = ipsRegistry::DB('hb')->fetch( $this->lib->queryRes ) )
			{
				// Who is in this conversation?
				$members	= array();
				$_recipient	= array();
				
				foreach( ipsRegistry::DB('hb')->buildAndFetchAll( array( 'select' => '*', 'from' => 'sentto', 'where' => "nodeid = {$row['nodeid']}" ) ) AS $k => $member )
				{
					if ( $member['userid'] != $row['userid'] )
					{
						$members[$member['userid']] = $member['userid'];
						$_recipient[] = $member['userid'];
					}
				}
				
				$topic = array(
					'mt_id'					=> $row['nodeid'],
					'mt_date'				=> $row['publishdate'],
					'mt_title'				=> $row['title'],
					'mt_starter_id'			=> $row['userid'],
					'mt_start_time'			=> $row['publishdate'],
					'mt_last_post_time'		=> $row['lastcontent'],
					'mt_invited_members'	=> serialize( array_keys( $members ) ),
					'mt_to_count'			=> count( $members ) + 1,
					'mt_to_member_id'		=> array_shift( $_recipient ),
					'mt_replies'			=> $row['totalcount'],
					'mt_is_draft'			=> 0,
					'mt_is_deleted'			=> 0,
					'mt_is_system'			=> 0,
				);
				
				$posts	= array();
				$maps	= array();
				
				// Add in our first post, and the convo starter map.
				$posts[] = array(
					'msg_id'			=> $row['nodeid'],
					'msg_topic_id'		=> $row['nodeid'],
					'msg_date'			=> $row['publishdate'],
					'msg_post'			=> $this->fixPostData( $row['description'] ),
					'msg_post_key'		=> md5( microtime() ),
					'msg_author_id'		=> $row['userid'],
					'msg_is_first_post'	=> 1,
				);
				
				$maps[] = array(
					'map_user_id'			=> $row['userid'],
					'map_topic_id'			=> $row['nodeid'],
					'map_folder_id'			=> 'myconvo',
					'map_read_time'			=> 0,
					'map_last_topic_reply'	=> $row['publishdate'],
					'map_user_active'		=> 1,
					'map_user_banned'		=> 0,
					'map_has_unread'		=> 0,
					'map_is_system'			=> 0,
					'map_is_starter'		=> 1,
				);
				
				foreach( ipsRegistry::DB('hb')->buildAndFetchAll( array( 'select' => '*', 'from' => 'node', 'where' => "parentid = {$row['nodeid']}" ) ) AS $k => $post )
				{
					$posts[] = array(
						'msg_id'			=> $post['nodeid'],
						'msg_topic_id'		=> $post['parentid'],
						'msg_date'			=> $post['publishdate'],
						'msg_post'			=> $this->fixPostData( $post['description'] ),
						'msg_post_key'		=> md5( microtime() ),
						'msg_author_id'		=> $post['userid'],
						'msg_is_first_post'	=> 0,
					);
				}
				
				//var_dump( $members );
				
				foreach( $members AS $k => $v )
				{
					$maps[] = array(
						'map_user_id'			=> $v,
						'map_topic_id'			=> $row['nodeid'],
						'map_folder_id'			=> 'myconvo',
						'map_read_time'			=> 0,
						'map_last_topic_reply'	=> 0,
						'map_user_active'		=> 1,
						'map_user_banned'		=> 0,
						'map_has_unread'		=> 0,
						'map_is_system'			=> 0,
						'map_is_starter'		=> 0,
					);
				}
				
				$this->lib->convertPM( $topic, $posts, $maps );
				$this->lib->setLastKeyValue( $row['nodeid'] );
			}
			
			$this->lib->next();
		}
		
		/**
		 * Helper method to retrieve forums from nodes
		 *
		 * @access	private
		 * @return void
		 **/
		private function fetch_forums()
		{
			$forums = ipsRegistry::DB('hb')->buildAndFetchAll( array(
					'select'	=> '*',
					'from'		=> array('node'	=> 'n'),
					'add_join'	=> array(
							array(
									'select'	=> 'cl.*',
									'from'		=> array('closure'	=> 'cl'),
									'where'		=> 'cl.child = n.nodeid'
							),
					),
					'where'		=> "n.nodeid <> 2 AND cl.parent={$this->fetch_type( 'Thread' )} AND n.contenttypeid = {$this->fetch_type( 'Channel' )}",
			), 'nodeid');
			
			return $forums;
		}
		
		/**
		 * Helper method to retrieve content type ids
		 *
		 * @access	private
		 * @return void
		 **/
		private function fetch_type( $type )
		{
			
			if ( !empty( $this->typesCache ) )
			{
				return $this->typesCache[$type];
			}
			else
			{
				$types = ipsRegistry::DB('hb')->buildAndFetchAll( array(
						'select'	=> '*',
						'from'		=> array('contenttype'	=> 't'),
				), 'class');
				
				foreach( $types as $key => $values )
				{
					$this->typesCache[$key] = $values['contenttypeid'];
				}

				return $this->typesCache[$type];
			}
		}
}