<?php
/**
 * IPS Converters
 * IP.Gallery 3.0 Converters
 * Photopost vBGallery
 * Last Update: $Date: 2013-01-03 21:04:17 -0500 (Thu, 03 Jan 2013) $
 * Last Updated By: $Author: MikeyB $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 732 $
 * 
 */


	$info = array(
		'key'	=> 'photopostvbgallery',
		'name'	=> 'Photopost vBGallery 3.x',
		'login'	=> false,
	);

	$parent = array('required' => false, 'self' => true, 'choices' => array(
		array('app' => 'board', 'key' => 'vbulletin_legacy', 'newdb' => true),
		array('app' => 'board', 'key' => 'vbulletin', 'newdb' => true),
		array('app' => 'board', 'key' => 'phpbb', 'newdb' => true),
		array('app' => 'board', 'key' => 'ipboard', 'newdb' => true),
		array('app' => 'board', 'key' => 'smf', 'newdb' => true),
		array('app' => 'board', 'key' => 'smf_legacy', 'newdb' => true),
		array('app' => 'board', 'key' => 'mybb', 'newdb' => true),
		));

	class admin_convert_gallery_photopostvbgallery extends ipsCommand
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
			$this->registry = $registry;
			//-----------------------------------------
			// What can this thing do?
			//-----------------------------------------

			$app = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'conv_apps', 'where' => "name='{$this->settings['conv_current']}'" ) );

			$forSome = array();
			$useLocal = true;

			if(!$app['parent'])
			{
				$useLocal = false;
				$forSome = array(
					'forum_perms'			=> array(),
					'groups' 				=> array('forum_perms'),
					'members'				=> array('groups'),
				);
			}

			
			$forAll = array(
				'gallery_categories'	=> is_array($forSome['forum_perms']) ? array('members') : '',
				'gallery_albums'		=> is_array($forSome['forum_perms']) ? array('members', 'gallery_categories') : array('gallery_categories'),
				'gallery_images'		=> is_array($forSome['forum_perms']) ? array('members') : '',
				'gallery_comments'		=> is_array($forSome['forum_perms']) ? array('members', 'gallery_images') : array('gallery_images'),
				);

			$this->actions = array_merge($forSome, $forAll);

			//-----------------------------------------
	        // Load our libraries
	        //-----------------------------------------

			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_master.php' );
			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_gallery.php' );
			$this->lib =  new lib_gallery( $this->registry, $html, $this, $useLocal );

	        $this->html = $this->lib->loadInterface();
			$this->lib->sendHeader( 'Photopost vBGallery &rarr; IP.Gallery Converter' );
			$this->HB = $this->lib->connect();

			//-----------------------------------------
			// What are we doing?
			//-----------------------------------------

			if (array_key_exists($this->request['do'], $this->actions) || $this->request['do'] == 'albums2' )
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
		
		public function countRows($action)
		{
			switch ($action)
			{
				case 'forum_perms':
				case 'groups':
					return $this->lib->countRows('ppgal_usergroups');
					break;

				case 'members':
					return $this->lib->countRows('ppgal_users');
					break;

				case 'gallery_categories':
					return $this->lib->countRows('ppgal_categories', "parent='0'");
					break;
					
				case 'gallery_albums':
					return $this->lib->countRows('ppgal_categories', "parent='1'");
					break;
				case 'gallery_images':
					return $this->lib->countRows('ppgal_images');
					break;

				case 'gallery_comments':
				 	return $this->lib->countRows('ppgal_posts');
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
				case 'gallery_images':
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
							'from' 		=> 'usergroups',
							'order'		=> 'groupid ASC',
						);

			$loop = $this->lib->load( 'forum_perms', $main, array(), array(), TRUE );

			//-----------------------------------------
			// We need to know how to map these
			//-----------------------------------------

			$this->lib->getMoreInfo('forum_perms', $loop, array('new' => '--Create new set--', 'ot' => 'Old permission set', 'nt' => 'New permission set'), '', array('idf' => 'groupid', 'nf' => 'groupname'));

			//---------------------------
			// Loop
			//---------------------------

			foreach( $loop as $row )
			{
				$this->lib->convertPermSet($row['groupid'], $row['groupname']);
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
							'from' 		=> 'usergroups',
							'order'		=> 'groupid ASC',
						);

			$loop = $this->lib->load( 'groups', $main, array(), array(), TRUE );

			//-----------------------------------------
			// We need to know how to map these
			//-----------------------------------------

			$this->lib->getMoreInfo('groups', $loop, array('new' => '--Create new group--', 'ot' => 'Old group', 'nt' => 'New group'), '', array('idf' => 'groupid', 'nf' => 'groupname'));

			//---------------------------
			// Loop
			//---------------------------

			foreach( $loop as $row )
			{
				$save = array(
					'g_title'			=> $row['groupname'],
					'g_access_cp'		=> $row['cpaccess'],
					'g_is_supmod'		=> $row['modaccess'],
					'g_max_diskspace'	=> $row['diskspace'],
					'g_max_upload'		=> $row['uploadsize'],
					'g_edit_own'		=> $row['editpho'],
					'g_create_albums'	=> $row['useralbums'],
					'g_perm_id'			=> $row['groupid'],
					);
				$this->lib->convertGroup($row['groupid'], $save);
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
			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$pcpf = array(
				'icq'			=> 'ICQ Number',
				'aim'			=> 'AIM ID',
				'yahoo'			=> 'Yahoo ID',
				'homepage'		=> 'Website',
				'location'		=> 'Location',
				'interests'		=> 'Interests',
				'occupation'	=> 'Occupation',
				);

			$this->lib->saveMoreInfo('members', array_keys($pcpf));

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'users',
							'order'		=> 'userid ASC',
						);

			$loop = $this->lib->load('members', $main);

			//-----------------------------------------
			// Tell me what you know!
			//-----------------------------------------

			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			$ask = array();

			// We need to know the avatars path
			$ask['avvy_path'] = array('type' => 'text', 'label' => 'The path to the folder where avatars are saved (no trailing slash - usually /path_to_photopost/data/avatars):');

			// And those custom profile fields
			$options = array('x' => '-Skip-');
			$this->DB->build(array('select' => '*', 'from' => 'pfields_data'));
			$this->DB->execute();
			while ($row = $this->DB->fetch())
			{
				$options[$row['pf_id']] = $row['pf_title'];
			}
			foreach ($pcpf as $id => $name)
			{
				$ask[$id] = array('type' => 'dropdown', 'label' => 'Custom profile field to store '.$name.': ', 'options' => $options, 'extra' => $extra );
			}


			$this->lib->getMoreInfo('members', $loop, $ask, 'path');

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{

				//-----------------------------------------
				// Set info
				//-----------------------------------------

				$info = array(
					'id'				=> $row['userid'],
					'group'				=> $row['usergroupid'],
					'joined'			=> $row['joindate'],
					'username'			=> $row['username'],
					'email'				=> $row['email'],
					'md5pass'			=> $row['password'],
					);

				$birthday = ($row['birthday']) ? explode('-', $row['birthday']) : null;
				$members = array(
					'bday_day'			=> ($row['birthday']) ? $birthday[2] : '',
					'bday_month'		=> ($row['birthday']) ? $birthday[1] : '',
					'bday_year'			=> ($row['birthday']) ? $birthday[0] : '',
					'ip_address'		=> $row['ipaddress'],
					'time_offset'		=> $row['offset'],
					'title'				=> $row['title'],
					'last_visit'		=> $row['laston'],
					);

				$profile = array(
					'pp_about_me'		=> $this->fixPostData($row['bio']),
					'signature'			=> $row['signature'],
					);

				//-----------------------------------------
				// Avatars
				//-----------------------------------------

				$path = '';

				if ($row['avatar'])
				{
					$profile['photo_type'] = 'custom';
					$profile['pp_main_photo'] = $row['avatar'];
					$path = $us['avvy_path'];
				}

				//-----------------------------------------
				// Custom Profile fields
				//-----------------------------------------

				$custom = array();
				foreach ($pcpf as $id => $name)
				{
					if ($us[$id] != 'x')
					{
						$custom['field_'.$us[$id]] = $row[$id];
					}
				}

				//-----------------------------------------
				// And go!
				//-----------------------------------------

				$this->lib->convertMember($info, $members, $profile, $custom, $path);

			}

			$this->lib->next();

		}

		/**
		 * Work out the permissions column
		 *
		 * @param 	array		row from lib_master::load()
		 * @param 	groups		remote usergroups data
		 * @param 	string		action (e.g. 'view', 'upload', etc.)
		 * @return 	null
		 **/
		private function _populatePerms($row, $groups, $type)
		{
			$refuse = array();
			switch ($type)
			{
				case 'view':
					$refuse = explode(',', $row['ugnoview']);
					break;

				case 'upload':
					$refuse = explode(',', $row['ugnoupload']);
					break;

				case 'comment':
					$refuse = explode(',', $row['ugnopost']);
					break;
			}

			$allow = array();
			foreach($groups as $g)
			{
				if ( ! in_array($g, $refuse) )
				{
					$allow[] = $g;
				}
			}

			return implode(',', $allow);
		}

		/**
		 * Convert Categories
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_gallery_categories()
		{			
			//---------------------------
			// Set up
			//---------------------------
			
			$main = array(	
					'select' 	=> 	'*',
					'from' 		=> 	'ppgal_categories',
					'where'		=>	"parent='0'",
					'order'		=> 	'catid ASC',
			);
			
			$loop = $this->lib->load('gallery_categories', $main);
			
			// loop through and fetch our values
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes))
			{
				
				$save	=	array(
							//	'category_id'						=>	'',
							//	'category_parent_id'				=>	$row['parent'],
								'category_name'						=>	$row['title'],
								'category_name_seo'					=>	IPSText::makeSeoTitle($row['title_clean']),
								'category_description'				=>	IPSText::htmlspecialchars($row['description']),
								'category_count_imgs'				=>	intval($row['imagecount']),
								'category_count_comments'			=>	intval($row['postcount']),
								'category_last_img_id'				=>	intval($row['lastimageid']),
								'category_type'						=>	$row['hasimages'] == '1' ? 1 : 2,
								'category_position'					=>	$row['displayorder'],
							);
				
				// save category
				$this->lib->convertCategory( $row['catid'], $save );
			}
			
			$this->lib->next();
		}
		
		/**
		 * Convert Albums
		 *
		 * @access	private
		 * @return void
		 **/
		
		private function convert_gallery_albums()
		{

			// get usergroups for permissions
			$groups = array();
			
			ipsRegistry::DB('hb')->build(
										array(
											'select' => '*',
											'from' => 'usergroups'
										)
									);
				
			$gp = ipsRegistry::DB('hb')->execute();
				
			while ( $row = ipsRegistry::DB('hb')->fetch($gp) )
			{
				$groups[] = $row['groupid'];
			}
				
			// fetch all albums
			$main = array(	
					'select' 	=> 	'*',
					'from' 		=> 	'ppgal_categories',
					'where'		=>	"parent =! '0'",
					'order'		=> 	'catid ASC',
			);
				
			$loop = $this->lib->load('gallery_albums', $main);
			
			// loop through and fetch our values
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save	=	array(
						//	'album_id'						=>	'',
						'album_owner_id'				=>	0, // photopost doesn't assign the category to... anybody.
						'album_name'					=>	$row['title'],
						'album_name_seo'				=>	IPSText::makeSeoTitle($row['title_clean']),
						'album_description'				=>	IPSText::htmlspecialchars($row['description']),
						'album_count_imgs'				=>	intval($row['imagecount']),
						'album_count_comments'			=>	intval($row['postcount']),
						//'album_cover_img_id'			=>	intval($row['thumbnail']),
						'album_last_img_id'				=>	intval($row['lastimageid']),
						//'album_watermark'				=>	$row['watermark'] ? $row['watermark'] : '',
						'album_position'				=>	$row['displayorder'],
						'album_category_id'				=>	$row['parent'],
						'album_type'					=>	empty($row['password']) OR $row['password'] === 'NULL' ? 1 : 2,
				);
			
				$perms = array();
				//$perms['album_g_perms_view']		= $this->_populatePerms($row, $groups, 'view');
				//$perms['album_g_perms_images']		= $this->_populatePerms($row, $groups, 'upload');
				//$perms['album_g_perms_comments']	= $this->_populatePerms($row, $groups, 'comment');
			
				$save = array_merge( $save, $perms );
			
				$this->lib->convertAlbum( $row['catid'], $save );
			}
			
			$this->lib->next();			
		}

		/**
		 * Convert Images
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_gallery_images()
		{

			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$this->lib->saveMoreInfo('gallery_images', array('gallery_path'));

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'ppgal_images',
							'order'		=> 'imageid ASC',
						);

			$loop = $this->lib->load('gallery_images', $main);
			
			$cats = array();
			$this->DB->build(array('select' => '*', 'from' => 'gallery_categories', 'category_type=2')); // fetch all Images Only Categories
			$this->DB->execute();
			while ($r = $this->DB->fetch())
			{
				$cats[$r['category_id']] = $r['category_name'];
			}

			//-----------------------------------------
			// We need to know the path
			//-----------------------------------------
			$featuredOpts['1'] = 'Yes';
			$featuredOpts['0'] = 'No';
			$this->lib->getMoreInfo('gallery_images', $loop, array('gallery_path' => array('type' => 'text', 'label' => 'The path to the folder where images are saved (no trailing slash - usually path_to_photopost/data):'), 'orphans' => array('type' => 'dropdown', 'label' => 'Where do you want to put orphaned images?', 'options' => $cats), 'feature_all' => array('type' => 'dropdown', 'label' => 'Feature All Images?', 'options' => $featuredOpts)), 'path');

			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];
			$path = $us['gallery_path'];

			//-----------------------------------------
			// Check all is well
			//-----------------------------------------

			if (!is_writable($this->settings['gallery_images_path']))
			{
				$this->lib->error('Your IP.Gallery upload path is not writeable. '.$this->settings['gallery_images_path']);
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
				//-----------------------------------------
				// Do the image
				//-----------------------------------------

				$row['extension'] = ($row['extension'] == 'jpg') ? 'jpeg' : $row['extension'];
				$mime = "image/{$row['extension']}";

				// Basic info
				$save = array(
					//'image_id'					=>	0,
					'image_member_id'			=>	$row['userid'],
					'image_category_id'			=>	$row['catid'] ? $row['catid'] : $us['orphans'],
					'image_album_id'			=>	$row['catid'] ? $row['catid'] : $us['orphans'],
					'image_directory'			=>	$row['catid'],
					'image_caption'				=>	$row['title'],
					'image_description'			=>	$row['description'],
					'image_file_name'			=>	$row['filename'],
					'image_file_size'			=>	$row['filesize'],
					'image_file_type'			=>	$mime,
					'image_approved'			=>	$row['open'],
					'image_views'				=>	$row['views'],
					'image_comments'			=>	$row['posts'],
					'image_date'				=>	$row['dateline'],
					//'image_metadata'			=>	$row['exifinfo'] // @todo we could fetch this from pp_exif
					'image_caption_seo'			=>	IPSText::makeSeoTitle($row['title']),
					'image_feature_flag'		=>	$us['feature_all'],
				);
				
				$tmpPath = '/' . implode('/', preg_split('//', $row['userid'],  -1, PREG_SPLIT_NO_EMPTY));
				
				// Go!
				$this->lib->convertImage($row['imageid'], $save, $path . $tmpPath);
			}

			$this->lib->next();

		}

		/**
		 * Convert Comments
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_gallery_comments()
		{

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'ppgal_posts',
							'order'		=> 'postid ASC',
						);

			$loop = $this->lib->load('gallery_comments', $main);


			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$save = array(
						//'comment_id'			=>	'',
						//'comment_edit_time'	=>	'',
						'comment_author_id'		=>	$row['userid'],
						'comment_author_name'	=>	$row['username'],
						'comment_ip_address'	=>	$row['ipaddress'],
						'comment_post_date'		=>	$row['dateline'],
						'comment_text'			=>	$this->fixPostData($row['pagetext']),
						'comment_approved'		=>	$row['visible'],
						'comment_img_id'		=>	$row['imageid'],
					);

				$this->lib->convertComment($row['imageid'], $save);
			}

			$this->lib->next();

		}

	}
