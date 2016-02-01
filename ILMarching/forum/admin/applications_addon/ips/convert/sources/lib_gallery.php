<?php
/**
 * IPS Converters
 * Application Files
 * Library functions for IP.Gallery 3.0 conversions
 * Last Update: $Date: 2013-12-09 18:35:21 -0500 (Mon, 09 Dec 2013) $
 * Last Updated By: $Author: rashbrook $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 947 $
 *
 * @todo		Move all hard coded templates into skin_cp/ templates. ( I cannot see why this isn't possible )
 * @todo		Move hard coded text into language strings
 */
class lib_gallery extends lib_master
{
	
	/**
	 * Image remap array for converting gallery 4 to 5
	 *
	 * @param	array
	 */
	protected $_imageRemap = array(	'img_album_id'			=> 'image_album_id',
			'member_id'			=> 'image_member_id',
			'caption'			=> 'image_caption',
			'description'		=> 'image_description',
			'directory'			=> 'image_directory',
			'file_name'			=> 'image_file_name',
			'file_size'			=> 'image_file_size',
			'file_type'			=> 'image_file_type',
			'approved'			=> 'image_approved',
			'views'				=> 'image_views',
			'comments'			=> 'image_comments',
			'idate'				=> 'image_date',
			'ratings_total'		=> 'image_ratings_total',
			'ratings_count'		=> 'image_ratings_count',
			'rating'			=> 'image_rating',
			'masked_file_name'	=> 'image_masked_file_name',
			'media'				=> 'image_media',
			'copyright'			=> 'image_copyright',
			'caption_seo'		=> 'image_caption_seo',
			'pinned'			=> 'image_pinned',
			'credit_info'		=> 'image_credit_info',
			'thumbnail'			=> 'image_thumbnail',
			'data'				=> 'image_data',
			'meta_data'			=> 'image_metadata' );
	
	/**
	 * Information box to display on convert screen
	 *
	 * @access	public
	 * @return	string 		html to display
	 */
	public function getInfo()
	{
		return "<a href='{$this->settings['base_url']}&app=gallery&module=categories&section=manage&do=overview' target='_blank'>Click here</a> and confirm each categories permissions and settings are correct, then run the following tool from the same page.<br /><br />
		
		<ol>
			<li>Recount & Resync Categories</li>
		</ol><br />
		
		Next, <a href='{$this->settings['base_url']}&app=gallery&module=albums&section=manage&do=overview' target='_blank'>Click here</a> and confirm each albums permissions and settings are correct, then run the following tools in the order specified.<br /><br />
		
		<ol>
			<li>Rebuild Images</li>
			<li>Recount & Resync Albums</li>
		</ol><br />
		
		After that, <a href='{$this->settings['base_url']}&app=gallery&module=overview&section=settings' target='_blank'>click here</a> and turn the application back on.";
	}

	/**
	 * Return the information needed for a specific action
	 *
	 * @access	public
	 * @param 	string		action (e.g. 'members', 'forums', etc.)
	 * @return 	array 		info needed for html->convertMenuRow
	 **/
	public function menuRow( $action='', $return=false )
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

			case 'groups':
				$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'groups' ) );
				$return = array(
					'name'	=> 'Member Groups',
					'rows'	=> $count['count'],
					'cycle'	=> 100,
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
			
			case 'gallery_categories':
				$count = $this->DB->buildAndFetch ( array ( 'select' => 'COUNT(*) as count', 'from' => 'gallery_categories' ) );
				$return = array (
					'name'	=> 'Categories',
					'rows'	=> $count['count'],
					'cycle'	=> 1000,
				);
				break;

			case 'gallery_albums':
				$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'gallery_albums' ) );
				$return = array(
					'name'	=> 'Albums',
					'rows'	=> $count['count'],
					'cycle'	=> 1000,
				);
				break;

			case 'gallery_images':
				$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'gallery_images' ) );
				$return = array(
					'name'	=> 'Images',
					'rows'	=> $count['count'],
					'cycle'	=> 100,
				);
				break;

			case 'gallery_comments':
				$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'gallery_comments' ) );
				$return = array(
					'name'	=> 'Comments',
					'rows'	=> $count['count'],
					'cycle'	=> 2000,
				);
				break;

			case 'gallery_ratings':
				$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'gallery_ratings' ) );
				$return = array(
					'name'	=> 'Ratings',
					'rows'	=> $count['count'],
					'cycle'	=> 2000,
				);
				break;

			/*case 'gallery_media_types':
				$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'gallery_media_types' ) );
				$return = array(
					'name'	=> 'Media Types',
					'rows'	=> $count['count'],
					'cycle'	=> 100,
				);
				break;

			case 'gallery_form_fields':
				$count = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => 'gallery_form_fields' ) );
				$return = array(
					'name'	=> 'Form Fields',
					'rows'	=> $count['count'],
					'cycle'	=> 100,
				);
				break;*/

			default:
				if ($return)
				{
					return false;
				}
				$this->error("There is a problem with the converter: called invalid action {$action}");
				break;
		}

		$basic = array('section' => $this->app['app_key'], 'key' => $action, 'app' => 'gallery');
		return array_merge($basic, $return);
	}

	/**
	 * Return the tables that need to be truncated for a given action
	 *
	 * @access	public
	 * @param 	string		action (e.g. 'members', 'forums', etc.)
	 * @return 	array 		array('table' => 'id_field', ...)
	 **/
	public function truncate( $action )
	{
		switch ($action)
		{
			case 'members':
				return array(
					'tables'	=> array( 'members' => 'member_id', 'pfields_content' => 'member_id', 'profile_portal' => 'pp_member_id', 'rc_modpref' => 'mem_id' ),
					'where'		=> array( 'members' => "member_id <> {$this->memberData['member_id']}", 'pfields_content' => "member_id <> {$this->memberData['member_id']}", 'profile_portal' => "pp_member_id <> {$this->memberData['member_id']}", 'rc_modpref' => "mem_id <> {$this->memberData['member_id']}" )
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

			case 'gallery_albums':
				return array(
					'tables'	=> array( 'gallery_albums' => 'album_id' )
				);
				break;
			case 'gallery_categories':
				return array(
					'tables'	=> array( 'gallery_categories' => 'category_id' ),
					'where'		=> array( 'gallery_categories' => "category_id <> {$this->settings['gallery_members_album']}" )
				);
				break;

			case 'gallery_images':
				return array(
					'tables'	=> array( 'gallery_images' => 'image_id' )
				);
				break;

			case 'gallery_comments':
				return array(
					'tables'	=> array( 'gallery_comments' => 'comment_id' )
				);
				break;

			case 'gallery_ratings':
				return array(
					'tables'	=> array( 'gallery_ratings' => 'id' )
				);
				break;

			default:
				$this->error('There is a problem with the converter: bad truncate command ' . $action );
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
	public function databaseChanges ( $action )
	{
		switch ($action)
		{
// 			case 'gallery_albums':
// 				return array ( 'addfield' => array('gallery_albums_main', 'conv_album_category_id', 'mediumint(5)' ) );
// 				break;

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
	protected function processReportLinks( $type, $report )
	{
		switch ($type)
		{
			case 'gallery_images':
				$report['exdat1'] = $this->getLink($report['exdat1'], 'gallery_images');
				$report['exdat2'] = 0;
				$report['exdat3'] = 0;
				$report['url'] = "/index.php?app=gallery&amp;module=images&amp;section=viewimage&amp;img={$report['exdat1']}";
				$report['seotemplate'] = '';
				break;

			case 'gallery_comments':
				$report['exdat1'] = $this->getLink($report['exdat1'], 'gallery_images');
				$report['exdat2'] = $this->getLink($report['exdat2'], 'gallery_comments');
				$report['exdat3'] = $report['exdat3'];
				$report['url'] = "/index.php?app=gallery&amp;module=images&amp;section=viewimage&amp;img={$report['exdat1']}&amp;st={$report['exdat3']}#{$report['exdat2']}";
				$report['seotemplate'] = '';
				break;
		}
		return $report;
	}

	/**
	 * Convert a category
	 *
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @return 	boolean		Success or fail
	 **/
	public function convertCategory( $id, $info )
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------
		unset($info['category_id']);
		if ( ! $id )
		{
			$this->logError( $id, 'No ID number provided' );
			return false;
		}
		
		
		if ( ! $info['category_name'] )
		{
			$this->logError( $id, 'No name provided' );
			return false;
		}
		
		// Get parent ID link for category
		$info['category_parent_id'] 	= $info['category_parent_id'] ? $this->getLink( $info['category_parent_id'], 'gallery_categories' ) : 0;
		$info['category_name_seo']		= IPSText::makeSeoTitle( $info['category_name'] );
		$info['category_rules']			= is_array( $info['category_rules'] ) ? serialize( $info['category_rules'] ) : serialize( array( 'title' => '', 'text' => '' ) );
		
		//-----------------------------------------
		// Insert
		//-----------------------------------------

		unset($info['id']);
		$this->DB->insert( 'gallery_categories', $info );
		$inserted_id = $this->DB->getInsertId();

		//-----------------------------------------
		// Add link
		//-----------------------------------------

		$this->addLink($inserted_id, $id, 'gallery_categories');

		return true;
	}

	/**
	 * Convert an album
	 *
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @param 	boolean		If true, will not get link for category id
	 * @return 	boolean		Success or fail
	 *
	 * @todo - see if we use $us at all anywhere in converters, if not, remove from lib as it appears to be redundant.
	 **/
	public function convertAlbum( $id, $info, $us=array(), $skip_global_link=false )
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------

		if ( ! $id )
		{
			$this->logError( $id, 'No ID number provided' );
			return false;
		}
		
		//-----------------------------------------
		// Link
		//-----------------------------------------
		
		if ( ! isset( $info['album_category_id'] ) )
		{
			$skip_global_link = true;
			$info['album_category_id'] = ipsRegistry::$settings['gallery_members_album'];
		}
		
		if ( ! $skip_global_link )
		{
			$info['album_category_id'] 		= $this->getLink( $info['album_category_id'], 'gallery_categories' );
		}
		
		
		$info['album_name']				= ( ! empty( $info['album_name'] ) ? $info['album_name'] : 'Untitled Album' );
		$info['album_owner_id']			= $this->getLink( $info['album_owner_id'], 'members', true, $this->useLocalLink );
		$info['album_name_seo']			= IPSText::makeSeoTitle( $info['album_name'] );
		$info['album_allow_comments']	= intval( $info['album_allow_comments'] );

		//-----------------------------------------
		// Insert
		//-----------------------------------------

		unset($info['id']);
		$this->DB->insert( 'gallery_albums', $info );
		$inserted_id = $this->DB->getInsertId();

		//-----------------------------------------
		// Add link
		//-----------------------------------------

		$this->addLink($inserted_id, $id, 'gallery_albums');

		return true;
	}

	/**
	 * Convert an image
	 *
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @param 	string 		Path to where images are stores
	 * @param	boolean		If true, loads file data from database, rather than move file
	 * @return 	boolean		Success or fail
	 **/
	public function convertImage( $id, $info, $path, $db=false, $parent=false )
	{
		// First remap for gallery 5
		foreach( $info as $k => $v )
		{
			if ( isset( $this->_imageRemap[ $k ] ) )
			{
				$info[ $this->_imageRemap[ $k ] ] = $v;
				unset($info[ $k ]);
			}
			else
			{
				$info[ $k ] = $v;
			}
		}
		unset($info['image_id']);
				
		// Check we have a path
		//if (!$this->settings['gallery_images_path'])
		//{
		//	$this->logError($id, 'Your IP.Gallery uploads path has not been configured');
		//	return false;
		//}
		
		if ( !file_exists ( $this->settings['gallery_images_path'] . '/gallery' ) )
		{
			if ( !mkdir( $this->settings['gallery_images_path'].'/gallery', 0777 ) )
			{
				$this->error ( '"gallery" folder does not exist in the uploads directory.' );
				return false;
			}
		}
		
		if ( !is_writable ( $this->settings['gallery_images_path'] ) )
		{
			$this->error ( '"gallery" folder is not writable.' );
			return false;
		}

		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------
		if (!$id)
		{
			$this->logError($id, 'No ID number provided');
			return false;
		}
		// Need image path if was not stored in database
		if (!$path and !$db)
		{
			$this->logError($id, 'No path provided');
			return false;
		}

		// Be sure to have member id
		if (!$info['image_member_id'])
		{
			$this->logError($id, 'No member ID provided');
			return false;
		}

		// Need to store in either category or album
		if (!$info['image_album_id'])
		{
			$this->logError($id, 'No album ID provided');
			return false;
		}

		// Check if a masked name was provided. If not, just use the filename.
		$info['image_masked_file_name'] = ($info['image_masked_file_name']) ? $info['image_masked_file_name'] : $info['image_file_name'];
		if (!$db and !$info['image_masked_file_name'])
		{
			$this->logError($id, 'No filename provided');
			return false;
		}

		// Make sure image data was provided if stored in database.
		if ($db && !$info['image_data'])
		{
			$this->logError($id, 'No file data provided');
			return false;
		}

		if ( isset($info['image_directory']) && $info['image_directory'] != '' )
		{
			$oldPath	= $path;
			$path		= $path . '/' . trim($info['image_directory'], '/');
		}
		
		// Check the file actually exists
		if (!$db && !file_exists($path.'/'.$info['image_masked_file_name']))
		{
			if ( ! file_exists( $oldPath . '/' . $info['image_masked_file_name'] ) )
			{
				$this->logError($id, 'Could not locate file '.$path.'/'.$info['image_masked_file_name']);
				return false;
			}
			
			$path = $oldPath;
		}
		
		$albumID = $this->getLink( $info['image_album_id'], 'gallery_albums', true );
		
		if ( $albumID )
		{
			if ( isset( $info['image_category_id'] ) )
			{
				$categoryID = $this->getLink( $info['image_category_id'], 'gallery_categories', true );
				$info['image_category_id'] = $categoryID;
			}
			else
			{
				$info['image_category_id'] = ipsRegistry::$settings['gallery_members_album'];
			}
			$info['image_album_id'] = $albumID;
		}
		else
		{
			$info['image_category_id'] = $this->getLink( $info['image_album_id'], 'gallery_categories' );
			$info['image_album_id'] = 0;
		}
		
		//-----------------------------------------
		// Set up array
		//-----------------------------------------
		$imageArray = array(
						'image_member_id'    	=> $this->getLink( $info['image_member_id'], 'members', false, $this->useLocalLink ),
						'image_album_id'	 	=> $info['image_album_id'],
						'image_category_id'		=> $info['image_category_id'],
						'image_caption'			=> $info['image_caption'] ? $info['image_caption'] : 'No caption',
						'image_description'		=> $info['image_description'],
						'image_directory'		=> '',
						'image_file_name'   	=> $info['image_file_name'],
						'image_approved'		=> $info['image_approved'],
						'image_thumbnail'		=> 0,
						'image_views'			=> intval($info['image_views']),
						'image_comments'		=> intval($info['image_comments']),
						'image_date'			=> intval($info['image_date']),
						'image_ratings_total'	=> intval($info['image_ratings_total']),
						'image_ratings_count'	=> intval($info['image_ratings_count']),
						'image_caption_seo'		=> IPSText::makeSeoTitle( $info['image_caption'] ),
						'image_notes'	 		=> $info['image_notes'],
						'image_rating'			=> intval($info['image_ratings_total']) > 0 ? intval($info['image_ratings_total']) / intval($info['image_ratings_count']) : 0,
						'image_privacy'			=> $info['image_privacy'],
					);
		
		if ( !isset ( $info['image_file_size'] ) )
		{
			$imageArray['image_file_size'] = @filesize ( $path . '/' . $info['image_masked_file_name'] );
		}
		else
		{
			$imageArray['image_file_size'] = $info['image_file_size'];
		}
			 
		// Fields still required = array( 'file_name', 'file_type', 'masked_file_name', 'medium_file_name');
		// Fields optional = array( 'file_size', 'pinned', 'media', 'credit_info', 'metadata', 'media_thumb');

		$_file = IPSLib::getAppDir(  'gallery' ) . '/app_class_gallery.php';
		$_name = 'app_class_gallery';

		$galleryLibObject = null;
		if ( file_exists( $_file ) )
		{
			$classToLoad = IPSLib::loadLibrary( $_file, $_name );

			 $galleryLibObject = new $classToLoad( $this->registry );
		}

		require_once IPS_KERNEL_PATH . 'classUpload.php';
		$upload = new classUpload();
		
		$dir = $this->registry->gallery->helper( 'upload' )->createDirectoryName ( $imageArray['image_album_id'], $imageArray['image_category_id'] );

		if ( !is_dir( $this->settings['gallery_images_path'] . DIRECTORY_SEPARATOR . $dir ) )
		{
			$this->error('Could not create directory to store images, please check <b>permissions (0777)</b> and <b>ownership</b> on "'.$this->settings['gallery_images_path'].'/gallery/"');
		}

		
		$ext = $upload->_getFileExtension( $info['image_file_name'] );
		
		$container = $imageArray['image_category_id'];
		
		if ( $imageArray['image_album_id'] )
		{
			$container = $imageArray['image_album_id'];
		}

		$new_name = "gallery_{$info['image_member_id']}_" . $container . "_" . time() . '_' . $id . '.' . $ext;
		$imageArray['image_masked_file_name'] = $new_name;
		$new_file = $this->settings['gallery_images_path'] . '/' . $dir . '/' . $new_name;

		// stop image_directory being category_ and album_
		if (( $imageArray['image_album_id'] != 0  || isset($imageArray['image_album_id']) || !empty($imageArray['image_album_id']) ) && ( $imageArray['image_category_id'] != 0 || isset($imageArray['image_category_id']) || !empty($imageArray['image_category_id']) ) )
		{
			// Set directory
			$imageArray['image_directory'] = $imageArray['image_album_id'] ? 'gallery/album_' . $imageArray['image_album_id'] : 'gallery/category_' . $imageArray['image_category_id'];
		}
		else
		{
			$imageArray['image_directory'] = '';
		}
		
		if ( $imageArray['image_directory'] == 'gallery/category_' || $imageArray['image_directory'] == 'gallery/album_' )
		{
			$imageArray['image_directory'] = '';
		}

		// Create the file from the db if that's the case
		if ($db)
		{
			$this->createFile($new_name, $info['image_data'], $info['image_file_size'], $this->settings['gallery_images_path'] . '/' . substr($dir,0,-1));
		}
		else
		{
			// Copy the file to its end IP.Gallery location
			if(!@copy( $path.'/'.$info['image_masked_file_name'], $new_file))
			{
				$e = error_get_last();
				$this->logError($id, 'Could not move file - attempted to move '.$path.'/'.$info['image_masked_file_name'].' to '.$new_file.'<br />'.$e['message'].'<br /><br />');
				return false;
			}
		}

		@chmod( $new_file, 0777 );

		if( method_exists( $upload, 'check_xss_infile' ) )
		{
			$upload->saved_upload_name = $new_file;
			$upload->check_xss_infile();

			if( $upload->error_no == 5 )
			{
				$this->logError($id, 'Invalid XSS file: '.$info['image_file_name'].'<br /><br />');
				return false;
			}
		}

		//-------------------------------------------------------------
		// Exif/IPTC support?
		//-------------------------------------------------------------
		$meta_data = array();

		if ( $this->settings['gallery_exif'] )
		{
			$meta_data = array_merge( $meta_data, $this->registry->gallery->helper ( 'image' )->extractExif( $new_file ) );
		}

		if ( $this->settings['gallery_iptc'] )
		{
			$meta_data = array_merge( $meta_data, $this->registry->gallery->helper ( 'image' )->extractIptc( $new_file ) );
		}
		$imageArray['image_metadata'] = serialize($meta_data);

		//-------------------------------------------------------------
		// Pass to library
		//-------------------------------------------------------------
		$media 	= 0;
		$imageArray['image_media'] = $this->_isImage ( $ext ) ? 0 : 1;


		$imageArray['image_medium_file_name'] = 'med_' . $new_name;
		$imageArray['image_file_type'] = $this->registry->gallery->helper ( 'image' )->getImageType( $new_file );

		// Go
		$this->DB->insert( 'gallery_images', $imageArray );
		$inserted_id = $this->DB->getInsertId();
		
		// Permissions
		$prefix = ipsRegistry::dbFunctions()->getPrefix();
		$this->DB->query("UPDATE {$prefix}gallery_images i, {$prefix}permission_index p SET i.image_parent_permission=p.perm_view WHERE p.app='gallery' AND p.perm_type='categories' AND p.perm_type_id=i.image_category_id");

		//-----------------------------------------
		// Add link
		//-----------------------------------------
		$this->addLink($inserted_id, $id, 'gallery_images');

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
	public function convertComment( $id, $info )
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------

		if ( !$id )
		{
			$this->logError( $id, 'No ID number provided' );
			return false;
		}
		if ( !$info['comment_author_id'] )
		{
			$this->logError( $id, 'No member ID provided' );
			return false;
		}
		if ( !$info['comment_img_id'] )
		{
			$this->logError( $id, 'No image ID provided' );
			return false;
		}
		if ( !$info['comment_text'] )
		{
			$this->logError( $id, 'No comment provided' );
			return false;
		}

		//-----------------------------------------
		// Link
		//-----------------------------------------

		$info['comment_author_id'] = $this->getLink( $info['comment_author_id'], 'members', false, $this->useLocalLink );
		$info['comment_img_id'] = $this->getLink( $info['comment_img_id'], 'gallery_images' );

		//-----------------------------------------
		// Insert
		//-----------------------------------------

		unset($info['comment_id']);
		$this->DB->insert( 'gallery_comments', $info );
		$inserted_id = $this->DB->getInsertId();

		//-----------------------------------------
		// Add link
		//-----------------------------------------

		$this->addLink($inserted_id, $id, 'gallery_comments');

		return true;
	}

	/**
	 * Convert a rating
	 *
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @return 	boolean		Success or fail
	 **/
	public function convertRating( $id, $info )
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------

		if (!$id)
		{
			$this->logError($id, '(RATING) No ID number provided');
			return false;
		}
		if (!$info['rate_member_id'])
		{
			$this->logError($id, '(RATING) No member ID provided');
			return false;
		}
		if (!$info['rate_type_id'])
		{
			$this->logError($id, '(RATING) No album/image ID provided');
			return false;
		}
		if (!$info['rate'])
		{
			$this->logError($id, '(RATING) No rating provided');
			return false;
		}

		//-----------------------------------------
		// Link
		//-----------------------------------------

		$info['rate_member_id'] = $this->getLink($info['rate_member_id'], 'members', false, $this->useLocalLink);
		
		switch( $info['rate_type'] )
		{
			case 'image':
					$info['rating_type_id'] = $this->getLink($info['rating_type_id'], 'gallery_images');
				break;
			case 'album':
					$info['rating_type_id'] = $this->getLink($info['rating_type_id'], 'gallery_albums');
				break;
		}

		//-----------------------------------------
		// Insert
		//-----------------------------------------

		unset($info['id']);
		$this->DB->insert( 'gallery_ratings', $info );
		$inserted_id = $this->DB->getInsertId();

		//-----------------------------------------
		// Add link
		//-----------------------------------------

		$this->addLink($inserted_id, $id, 'gallery_ratings');

		return true;
	}

	/**
	 * Convert a media type
	 *
	 * @deprecated As of Gallery 4.x
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @param 	string 		How to handle duplicates ('local' or 'remote')
	 * @return 	boolean		Success or fail
	 **/
	/*public function convertMediaType($id, $info, $dupes)
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
		if (!$info['mime_type'])
		{
			$this->logError($id, 'No mime type provided');
			return false;
		}
		if (!$info['extension'])
		{
			$this->logError($id, 'No extension provided');
			return false;
		}
		if (!$info['display_code'])
		{
			$this->logError($id, 'No display code provided');
			return false;
		}

		//-----------------------------------------
		// Handle duplicates
		//-----------------------------------------

		$dupe = $this->DB->buildAndFetch( array( 'select' => 'id', 'from' => 'gallery_media_types', 'where' => "extension = '{$info['extension']}'" ) );
		if ($dupe)
		{
			if ($dupes == 'local')
			{
				return false;
			}
			else
			{
				$this->DB->delete('gallery_media_types', "id={$dupe['id']}");
			}
		}

		//-----------------------------------------
		// Insert
		//-----------------------------------------

		unset($info['id']);
		$this->DB->insert( 'gallery_media_types', $info );
		$inserted_id = $this->DB->getInsertId();

		//-----------------------------------------
		// Add link
		//-----------------------------------------

		$this->addLink($inserted_id, $id, 'gallery_media_types');

		return true;
	}*/

	/**
	 * Convert a form field
	 *
	 * @deprecated As of Gallery 4.x
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @return 	boolean		Success or fail
	 **/
	/*public function convertFormField($id, $info)
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------

		if (!$id)
		{
			$this->logError($id, 'No ID number provided');
			return false;
		}
		if (!$info['name'])
		{
			$this->logError($id, 'No name provided');
			return false;
		}
		if (!$info['type'])
		{
			$this->logError($id, 'No type provided');
			return false;
		}

		//-----------------------------------------
		// Handle duplicates
		//-----------------------------------------

		$dupe = $this->DB->buildAndFetch( array( 'select' => 'id', 'from' => 'gallery_form_fields', 'where' => "name = '{$info['name']}'" ) );
		if ($dupe)
		{
			$this->addLink($dupe['id'], $id, 'gallery_form_fields');
			return false;
		}

		//-----------------------------------------
		// Insert
		//-----------------------------------------

		unset($info['id']);
		$this->DB->insert( 'gallery_form_fields', $info );
		$inserted_id = $this->DB->getInsertId();

		//-----------------------------------------
		// Add a column
		//-----------------------------------------

		$this->DB->addField( 'gallery_images', "field_$inserted_id", 'text' );
		$this->DB->optimize( 'gallery_images' );

		//-----------------------------------------
		// Add link
		//-----------------------------------------

		$this->addLink($inserted_id, $id, 'gallery_form_fields');

		return true;
	}*/

	/**
	 * Check for Media files.
	 *
	 * @access	private
	 * @param	File Extension
	 * @return	boolean		True if Image, False if Media.
	 */
	private function _isImage ( $extension )
	{
		$valid_image_ext = array ( 'jpeg', 'jpg', 'jpe', 'png', 'gif', 'bmp' );
		
		if ( in_array ( $extension, $valid_image_ext ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
