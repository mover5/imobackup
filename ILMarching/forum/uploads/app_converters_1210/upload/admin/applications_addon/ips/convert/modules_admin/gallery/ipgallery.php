<?php
/**
 * IPS Converters
 * IP.Gallery 4.0 Converters
 * IP.Gallery Merge Tool
 * Last Update: $Date: 2013-07-24 10:00:46 -0400 (Wed, 24 Jul 2013) $
 * Last Updated By: $Author: AndyMillne $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 894 $
 */


	$info = array(
		'key'	=> 'ipgallery',
		'name'	=> 'IP.Gallery 5.0',
		'login'	=> false,
	);

	$parent = array('required' => true, 'choices' => array(
		array('app' => 'board', 'key' => 'ipboard', 'newdb' => false),
		));

	class admin_convert_gallery_ipgallery extends ipsCommand
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

			$this->actions = array(
				'gallery_categories'	=> array('members'),
				'gallery_albums'		=> array('gallery_categories'),
				'gallery_images'		=> array('members','gallery_albums'),
				'gallery_comments'		=> array('members', 'gallery_images'),
				'gallery_ratings'		=> array('members', 'gallery_albums', 'gallery_images'),
				);

			//-----------------------------------------
	        // Load our libraries
	        //-----------------------------------------

			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_master.php' );
			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_gallery.php' );
			$this->lib =  new lib_gallery( $registry, $html, $this );

	        $this->html = $this->lib->loadInterface();
			$this->lib->sendHeader( 'IP.Gallery Merge Tool' );

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
				case 'gallery_albums':
					return $this->lib->countRows ( 'gallery_albums' );
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
				case 'gallery_images':
				case 'gallery_albums':
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
		 * Convert Albums
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_gallery_categories()
		{
			//---------------------------
			// Set up
			//---------------------------
		
			$main = array(	'select' 	=> '*',
					'from' 		=> 'gallery_categories',
					'order'		=> 'category_id ASC',
			);
		
			$loop = $this->lib->load('gallery_categories', $main);
				
			//---------------------------
			// Loop
			//---------------------------
		
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				// Ensure we are only passing the default fields.
				// May seem redundant at first, because the fields don't change
				// but avoids the "admin has apps installed" issue.
				
		
				// ALso, only do the important stuff. Anything else is done during Album Resync.
				$save = array (
						'category_id'						=> $row['category_id'],
						'category_parent_id'				=> $row['category_parent_id'],
						'category_name'						=> $row['category_name'],
						'category_name_seo'					=> $row['category_name_seo'],
						'category_description'				=> $row['category_description'],
						'category_count_imgs'				=> $row['category_count_imgs'],
						'category_count_comments'			=> $row['category_count_comments'],
						'category_count_imgs_hidden'		=> $row['category_count_imgs_hidden'],
						'category_count_comments_hidden'	=> $row['category_count_comments_hidden'],
						'category_type'						=> $row['category_allow_comments'],
						'category_sort_options'				=> $row['category_sort_options'],
						'category_allow_comments'			=> $row['category_allow_comments'],
						'category_allow_rating'				=> $row['category_allow_rating'],
						'category_approve_img'				=> $row['category_approve_img'],
						'category_approve_com'				=> $row['category_approve_com'],
						'category_rules'					=> $row['category_rules'],
						'category_rating_aggregate'			=> $row['category_rating_aggregate'],
						'category_rating_count'				=> $row['category_rating_count'],
						'category_rating_total'				=> $row['category_rating_total'],
						'category_can_tag'					=> $row['category_can_tag'],
						'category_preset_tags'				=> $row['category_preset_tags'],
						
				);
		
				$this->lib->convertCategory($row['category_id'], $save, array ( ), TRUE);
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
			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'gallery_albums',
							'order'		=> 'album_id ASC',
						);

			$loop = $this->lib->load('gallery_albums', $main);
			
			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				// Ensure we are only passing the default fields.
				// May seem redundant at first, because the fields don't change
				// but avoids the "admin has apps installed" issue.
				
				// ALso, only do the important stuff. Anything else is done during Album Resync.
				$save = array (
					'album_category_id'				=> $row['album_category_id'],
					'album_owner_id'				=> $row['album_owner_id'],
					'album_name'					=> $row['album_name'],
					'album_name_seo'				=> $row['album_name_seo'],
					'album_description'				=> $row['album_description'],
					'album_type'					=> $row['album_type'],
					'album_count_imgs'				=> $row['album_count_imgs'],
					'album_count_comments'			=> $row['album_count_comments'],
					'album_count_imgs_hidden'		=> $row['album_count_imgs_hidden'],
					'album_count_comments_hidden'	=> $row['album_count_comments_hidden'],
					'album_allow_comments'			=> $row['album_allow_comments'],
					'album_sort_options'			=> $row['album_sort_options'],
					'album_allow_rating'			=> $row['album_allow_rating'],
					'album_rating_aggregate'		=> $row['album_rating_aggregate'],
					'album_rating_count'			=> $row['album_rating_total'],
				);
				
				$this->lib->convertAlbum( $row['album_id'], $save );
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
							'from' 		=> 'gallery_images',
							'order'		=> 'image_id ASC',
						);

			$loop = $this->lib->load('gallery_images', $main);

			//-----------------------------------------
			// We need to know the path
			//-----------------------------------------

			$this->lib->getMoreInfo('gallery_images', $loop, array('gallery_path' => array('type' => 'text', 'label' => 'The path to the folder where images are saved (no trailing slash - usually path_to_ipgallery/uploads):')), 'path');

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

			//-----------------------------------------
			// Prepare for reports conversion
			//-----------------------------------------

			$this->lib->prepareReports('gallery_images');

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				//-----------------------------------------
				// Do the image
				//-----------------------------------------


				$this->lib->convertImage($row['image_id'], $row, $path);

				//-----------------------------------------
				// Report Center
				//-----------------------------------------

				$rc = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'com_id', 'from' => 'rc_classes', 'where' => "my_class='gallery'" ) );
				$rs = array(	'select' 	=> '*',
								'from' 		=> 'rc_reports_index',
								'order'		=> 'id ASC',
								'where'		=> 'exdat1='.$row['image_id']." AND exdat2=0 AND rc_class='{$rc['com_id']}'"
							);

				ipsRegistry::DB('hb')->build($rs);
				ipsRegistry::DB('hb')->execute();
				while ($report = ipsRegistry::DB('hb')->fetch())
				{
					$rs = array(	'select' 	=> '*',
									'from' 		=> 'rc_reports',
									'order'		=> 'id ASC',
									'where'		=> 'rid='.$report['id']
								);

					ipsRegistry::DB('hb')->build($rs);
					ipsRegistry::DB('hb')->execute();
					$reports = array();
					while ($r = ipsRegistry::DB('hb')->fetch())
					{
						$reports[] = $r;
					}
					$this->lib->convertReport('gallery_images', $report, $reports);
				}

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
							'from' 		=> 'gallery_comments',
							'order'		=> 'comment_id ASC',
						);

			$loop = $this->lib->load('gallery_comments', $main);

			//-----------------------------------------
			// Prepare for reports conversion
			//-----------------------------------------

			$this->lib->prepareReports('gallery_comments');

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$row['comment_text'] = $this->fixPostData($row['comment_text']);
				$this->lib->convertComment($row['comment_id'], $row);

				//-----------------------------------------
				// Report Center
				//-----------------------------------------

				$rc = ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'com_id', 'from' => 'rc_classes', 'where' => "my_class='gallery'" ) );
				$rs = array(	'select' 	=> '*',
								'from' 		=> 'rc_reports_index',
								'order'		=> 'id ASC',
								'where'		=> 'exdat2='.$row['comment_id']." AND rc_class='{$rc['com_id']}'"
							);

				ipsRegistry::DB('hb')->build($rs);
				ipsRegistry::DB('hb')->execute();
				while ($report = ipsRegistry::DB('hb')->fetch())
				{
					$rs = array(	'select' 	=> '*',
									'from' 		=> 'rc_reports',
									'order'		=> 'id ASC',
									'where'		=> 'rid='.$report['id']
								);

					ipsRegistry::DB('hb')->build($rs);
					ipsRegistry::DB('hb')->execute();
					$reports = array();
					while ($r = ipsRegistry::DB('hb')->fetch())
					{
						$reports[] = $r;
					}
					$this->lib->convertReport('gallery_comments', $report, $reports);
				}

			}

			$this->lib->next();

		}
		
		/**
		 * Convert Ratings
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_gallery_ratings()
		{
			
			//-----------------------------------------
			// Set up
			//-----------------------------------------
			
			$main = array(	'select' 	=> '*',
					'from' 		=> 'gallery_ratings',
					'order'		=> 'rate_id ASC',
			);
			
		
			$loop = $this->lib->load('gallery_ratings', $main);
		
			//---------------------------
			// Loop
			//---------------------------
		
			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$this->lib->convertRating($row['rate_id'], $row);
			}
		
			$this->lib->next();
		
		}

		/**
		 * Convert media types
		 *
		 * @deprecated As of Gallery 4.x
		 * @access	private
		 * @return void
		 **/
		/*private function convert_gallery_media_types()
		{

			//-----------------------------------------
			// Were we given more info?
			//-----------------------------------------

			$this->lib->saveMoreInfo('gallery_media_types', array('media_opt'));

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'gallery_media_types',
							'order'		=> 'id ASC',
						);

			$loop = $this->lib->load('gallery_media_types', $main);

			//-----------------------------------------
			// We need to know what do do with duplicates
			//-----------------------------------------

			$this->lib->getMoreInfo('gallery_media_types', $loop, array('media_opt'  => array('type' => 'dupes', 'label' => 'How do you want to handle duplicate media types?')));

			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->lib->app['name']];

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$this->lib->convertMediaType($row['id'], $row, $us['media_opt']);
			}

			$this->lib->next();

		}*/

		/**
		 * Convert Form Fields
		 *
		 * @deprecated As of Gallery 4.x
		 * @access	private
		 * @return void
		 **/
		/*private function convert_gallery_form_fields()
		{

			//---------------------------
			// Set up
			//---------------------------

			$main = array(	'select' 	=> '*',
							'from' 		=> 'gallery_form_fields',
							'order'		=> 'id ASC',
						);

			$loop = $this->lib->load('gallery_form_fields', $main);

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch($this->lib->queryRes) )
			{
				$this->lib->convertFormField($row['id'], $row);
			}

			$this->lib->next();

		}*/

	}

