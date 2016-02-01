<?php
/**
 * IPS Converters
 * Application Files
 * Finalizes a conversion
 * Last Update: $Date$
 * Last Updated By: $Author$
 *
 * @package		IPS Converters
 * @author 		Michael Burton
 * @copyright	© 2013 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision$
 *
 * @todo needs serious cleanup
 */


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class admin_convert_setup_finalize extends ipsCommand
{
	/**
	 * Main class entry point
	 * Sends use to the correct conversion page
	 *
	 * @access	public
	 * @param	object		ipsRegistry
	 * @return	void
	 */
	public function doExecute( ipsRegistry $registry )
	{
		// get Registry
		$this->registry	=	$registry;
		// get HTML
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_convert' );
		// load forums lib
		$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'applications/forums/sources/classes/forums/class_forums.php', 'class_forums', 'forums' );
		$this->registry->setClass( 'class_forums', new $classToLoad( $registry ) );
		$this->registry->class_forums->forumsInit();
		
		require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_master.php' );
		require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_board.php' );
		$this->lib =  new lib_board( $registry, $html, $this );
		
		// get Software (necessary in case we need to do stuff per software.)
		if ( ( $this->request['sw'] ) || ( $this->request['swtype'] ) )
		{
			if  ( ( function_exists( 'finalize_' . $this->request['sw'] ) ) || ( function_exists( 'finalize_' . $this->request['swtype'] ) ) )
			{
				switch ( $this->request )
				{
					case 'sw':
						// finalize_vbulletin()
						call_user_func( array( $this, 'finalize_'.$this->request['sw'] ) );
						break;
					
					case 'swtype':
						// finalize_gallery()
						call_user_func( array( $this, 'finalize_'.$this->request['swtype'] ) );
						break;
				}
			}
			else
			{
				//call_user_func( array( $this, 'finalize_general' ) );
				$this->finalize_general();
			}
		}
		else if ( $this->request['do'] )
		{
			switch ( $this->request['do'] )
			{
				case 'recountstats':
					$this->_recountStatistics();
					break;
					
				case 'resynchtopics':
					$this->_resynchronizeTopics();
					break;
				
				case 'resynchforums':
					$this->_resynchronizeForums();
					break;
					
				case 'rebuildAttachThumbs':
					$this->_rebuildAttachmentThumbs();
					break;
					
				case 'rebuildPhotoThumbs':
					$this->_rebuildProfilePhotoThumbs();
					break;
				
				case 'rebuildStatusUpdates':
					$this->_rebuildStatusUpdates();
					break;
					
				case 'recache_all':
					$this->_recacheAll();
					break;
					
				case 'finish':
					$this->boink('success');
					break;
					
				default:
					$this->finalize_general();
					break;
			}
		}
		else
		{
			$this->registry->output->html .= $this->html->convertError('<strong>An unexpected error occurred, could not locate conversion. Please try again.</strong><br /><a href="'.$this->settings['base_url'] . 'app=convert&module=setup">Go Back</a>');
			$this->registry->output->sendOutput();
			exit;
		}
		
		$this->registry->output->html .= $this->html->convertFooter();
		$this->registry->output->html_main .= $this->registry->output->global_template->global_frame_wrapper();
		$this->registry->output->sendOutput();
		exit;
	}
	
	/**
	 * Finalizes the conversion
	 *
	 * @access	private
	 * @return	mixed	success or fail
	 */
	private function finalize_general()
	{
		// Recount Statistics
		$this->registry->output->multipleRedirectInit( $this->settings['base_url'] . '&module=setup&section=finalize&do=recountstats' );
		
		$this->_recountStatistics();
		// Resync Topics
		$this->_resynchronizeTopics();
		// Resync Forums
		$this->_resynchronizeForums();
		// Rebuild Attachment Thumbnails
		$this->_rebuildAttachmentThumbs();
		// Rebuild Profile Photo Thumbnails
		$this->_rebuildProfilePhotoThumbs();
		// Recache All
		$this->registry->output->multipleRedirectInit( $this->settings['base_url'] . '&app=core&module=tools&section=cache&do=cache_update_all_process&id=0' );
		return false;
		
	}
	
	/**
	 * Redirects User
	 *
	 * @access	public
	 * @return	void
	 */
	public function boink( $code )
	{
		if ( $code )
		{
			$earliestFid = $this->DB->buildAndFetch(
												array(
													'select'	=>	'MIN(id) as id',
													'from'		=>	'forums',
													'limit'		=>	array(0,1),
												)
											);
			switch ( $code )
			{
				case 'success':
					$this->lib->logMessage('finalize');
					
					// lock
					file_put_contents( DOC_IPS_ROOT_PATH . 'cache/converter_lock.php', 'Auto locked on '. date('Y-m-d H:i:s') );
					
					// boink
					$this->registry->output->multipleRedirectFinish( "Conversion Complete. Please be sure to review your forum permissions from ACP > Forums > Manage Forums");
					break;
					
				case 'fail':
					// error
					break;
					
				default:
					// error
					break;
			}
		}
	}
	
	private function finalize_gallery() {}
	private function finalize_board() {}
	private function finalize_blog() {}
	private function finalize_nexus() {}
	private function finalize_downloads() {}
	private function finalize_content() {}
	private function finalize_calendar() {}
	private function finalize_chat() {}
	
	protected function _recountStatistics()
	{
		$img		= '<img src="' . $this->settings['skin_acp_url'] . '/images/loading_anim.gif" alt="-" /> ';
		
		switch ( $this->request['step'] )
		{
				default:
			$stats	= $this->cache->getCache('stats');
			$topics	= $this->DB->buildAndFetch(
											array(
													'select'	=> 'COUNT(*) as tcount',
													'from'		=> 'topics',
													'where'		=> $this->registry->getClass('class_forums')->fetchTopicHiddenQuery( array( 'visible' ), '' )
												)
										);
			
			$posts	= $this->DB->buildAndFetch(
											array(
													'select'	=> 'SUM(posts) as replies',
													'from'		=> 'topics',
													'where'		=> $this->registry->getClass('class_forums')->fetchTopicHiddenQuery( array( 'visible' ), '' )
											)
										);
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=recountstats&step=posts', $img . ' Total Topics & Posts Recounted' );
			break;
			
				case 'posts':
			$stats['total_topics']  = $topics['tcount'];
			$stats['total_replies'] = $posts['replies'];
			
			$r		= $this->DB->buildAndFetch(
											array(
													'select' => 'count(member_id) as members',
													'from' => 'members',
													'where' => "member_group_id <> '{$this->settings['auth_group']}'"
											)
										);
	
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=recountstats&step=members', $img .' Total Members Recounted' );
			break;
			
				case 'members':
					
			$stats['mem_count'] = intval($r['members']);
			$stats = array_merge( $stats, IPSMember::resetLastRegisteredMember( true ) );
			$stats['most_date']  = time();
			$stats['most_count'] = 1;
			
			$this->cache->setCache( 'stats', $stats, array( 'array' => 1 ) );
			
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=resynchtopics', $img . ' Most Online & Last Registered Member Recounted' );
			break;
				case 'done':
			break;
		}
	}
	
	protected function _resynchronizeTopics()
	{
		$img		= '<img src="' . $this->settings['skin_acp_url'] . '/images/loading_anim.gif" alt="-" /> ';
		
		$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'forums' ) . '/sources/classes/moderate.php', 'moderatorLibrary', 'forums' );
		$modfunc = new $classToLoad( $this->registry );
		
		$this->registry->class_localization->loadLanguageFile( array( 'public_global' ) );
		
		//-----------------------------------------
		// Set up
		//-----------------------------------------
		
		$done   = 0;
		$start  = intval( $this->request['st'] ) >= 0 ? intval( $this->request['st']    ) : 0;
		$end    = intval( $this->request['pergo'] )   ? intval( $this->request['pergo'] ) : 500;
		$last   = $start + 1;
		$output = array();
		
		//-----------------------------------------
		// Got any more?
		//-----------------------------------------
		
		$tmp = $this->DB->buildAndFetch( array( 'select' => 'count(*) as count', 'from' => 'topics', 'where' => "state != 'link' AND tid>{$start} AND topic_archive_status NOT IN (" . $this->registry->class_forums->fetchTopicArchiveFlag( 'working' ) . "," . $this->registry->class_forums->fetchTopicArchiveFlag( 'restore' ) . ")", 'limit' => array( 0, 1 ) ) );
		$max = intval( $tmp['count'] );
		
		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------
		
		$this->DB->build( array( 'select' => '*', 'from' => 'topics', 'where' => "state != 'link' AND tid>{$start} AND topic_archive_status NOT IN (" . $this->registry->class_forums->fetchTopicArchiveFlag( 'working' ) . "," . $this->registry->class_forums->fetchTopicArchiveFlag( 'restore' ) . ")", 'order' => 'tid ASC', 'limit' => array( 0, $end ) ) );
		$outer = $this->DB->execute();
		
		//-----------------------------------------
		// Process...
		//-----------------------------------------
		
		while( $r = $this->DB->fetch( $outer ) )
		{
			$modfunc->rebuildTopic($r['tid'], 0);
		
			$done++;
			$last = $r['tid'];
		}
		
		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
			//-----------------------------------------
			// Done..
			//-----------------------------------------

			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=resynchforums' . "&pergo={$this->request['pergo']}", $img . 'Resynchronized Topics' );
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------
				
			$thisgoeshere = sprintf( "Resynchronising Topics - Up to %s processed so far, continuing...", $last );
			$text = $thisgoeshere . '<br />' . implode( "<br />", $output );
			
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=resynchtopics' . "&pergo={$this->request['pergo']}&st={$last}", $img . $text );
		}
		
		//-----------------------------------------
		// Bye....
		//-----------------------------------------
		
		
	}
	
	protected function _resynchronizeForums()
	{
		$img		= '<img src="' . $this->settings['skin_acp_url'] . '/images/loading_anim.gif" alt="-" /> ';
		
		$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'forums' ) . '/sources/classes/moderate.php', 'moderatorLibrary', 'forums' );
		$modfunc = new $classToLoad( $this->registry );
		
		//-----------------------------------------
		// Set up
		//-----------------------------------------
		
		$done   = 0;
		$start  = intval( $this->request['st'] ) >= 0 ? intval( $this->request['st'] )    : 0;
		$end    = intval( $this->request['pergo'] )   ? intval( $this->request['pergo'] ) : 50;
		$dis    = $end + $start;
		$output = array();
		
		//-----------------------------------------
		// Got any more?
		//-----------------------------------------
		
		$tmp = $this->DB->buildAndFetch( array( 'select' => 'count(*) as count', 'from' => 'forums', 'limit' => array( $dis, 1 )  ) );
		$max = intval( $tmp['count'] );
		
		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------
		
		$this->DB->build( array( 'select' => '*', 'from' => 'forums', 'order' => 'id ASC', 'limit' => array( $start, $end ) ) );
		$outer = $this->DB->execute();
		
		//-----------------------------------------
		// Process...
		//-----------------------------------------
		
		while( $r = $this->DB->fetch( $outer ) )
		{
			$modfunc->forumRecount( $r['id'] );
			$done++;
		}
		
		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------
		
		if ( ! $done and ! $max )
		{
			//-----------------------------------------
			// Done..
			//-----------------------------------------
			
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=rebuildAttachThumbs', $img . ' Resynchronized Forums' );
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------
			$thisgoeshere = sprintf( "Resynchronising Forums - Up to %s processed so far, continuing...", $dis );
			$text = $thisgoeshere . '<br />' . implode( "<br />", $output );
			
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . "&module=setup&section=finalize&do=resynchforums&pergo={$this->request['pergo']}&st={$dis}", $img . $text );
		}
		
	}
	
	protected function _rebuildAttachmentThumbs()
	{
		$img		= '<img src="' . $this->settings['skin_acp_url'] . '/images/loading_anim.gif" alt="-" /> ';
		
		require_once( IPS_KERNEL_PATH . 'classImage.php' );/*noLibHook*/
		require_once( IPS_KERNEL_PATH . 'classImageGd.php' );/*noLibHook*/

		//-----------------------------------------
		// Set up
		//-----------------------------------------

		$done   = 0;
		$start  = intval($this->request['st']) >=0 ? intval($this->request['st']) : 0;
		$end    = intval( $this->request['pergo'] ) ? intval( $this->request['pergo'] ) : 100;
		$dis    = $end + $start;
		$output = array();

		//-----------------------------------------
		// Got any more?
		//-----------------------------------------

		$tmp = $this->DB->buildAndFetch( array( 'select' => 'attach_id', 'from' => 'attachments', 'where' => "attach_rel_module IN('post','msg')", 'limit' => array($dis,1)  ) );
		$max = intval( $tmp['attach_id'] );

		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------

		$this->DB->build( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_rel_module IN('post','msg')", 'order' => 'attach_id ASC', 'limit' => array($start,$end) ) );
		$outer = $this->DB->execute();

		//-----------------------------------------
		// Process...
		//-----------------------------------------

		while( $r = $this->DB->fetch( $outer ) )
		{
			if ( $r['attach_is_image'] )
			{
				if ( $r['attach_thumb_location'] and ( $r['attach_thumb_location'] != $r['attach_location'] ) )
				{
					if ( is_file( $this->settings['upload_dir'] . '/' . $r['attach_thumb_location'] ) )
					{
						if ( ! @unlink( $this->settings['upload_dir'] . '/' . $r['attach_thumb_location'] ) )
						{
							continue;
						}
					}
				}

				$attach_data	= array();
				$thumbnail		= preg_replace( "/^(.*)\.(.+?)$/", "\\1_thumb.\\2", $r['attach_location'] );

				$image = new classImageGd();

				$image->init( array(
				                         'image_path'     => $this->settings['upload_dir'],
				                         'image_file'     => $r['attach_location'],
				               )          );

				$image->force_resize	= false;

				$return = $image->resizeImage( $this->settings['siu_width'], $this->settings['siu_height'] );
				
				if( !$return['noResize'] )
				{
					$image->writeImage( $this->settings['upload_dir'] . '/' . $thumbnail );

					$attach_data['attach_thumb_location'] = $thumbnail;
				}
				else
				{
					$attach_data['attach_thumb_location'] = $r['attach_location'];
				}

				$attach_data['attach_thumb_width']    = intval($return['newWidth'] ? $return['newWidth'] : $image->cur_dimensions['width']);
				$attach_data['attach_thumb_height']   = intval($return['newHeight'] ? $return['newHeight'] : $image->cur_dimensions['height']);

				if ( count( $attach_data ) )
				{
					$this->DB->update( 'attachments', $attach_data, 'attach_id='.$r['attach_id'] );

				}

				unset($image);
			}

			$done++;
		}

		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------

		if ( ! $done and ! $max )
		{
		 	//-----------------------------------------
			// Done..
			//-----------------------------------------

			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=rebuildStatusUpdates', $img . ' Rebuilt Attachment Thumbnails' );
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------
			$thisgoeshere = sprintf( "Rebuilding Attachments - Up to %s processed so far, continuing...", $dis );
			$text = $thisgoeshere . '<br />' . implode( "<br />", $output );

			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . "&module=setup&section=finalize&do=rebuildAttachThumbs&pergo={$this->request['pergo']}&st={$dis}", $img . $text );
		}
		
	}
	
	protected function _rebuildStatusUpdates()
	{
		$img		= '<img src="' . $this->settings['skin_acp_url'] . '/images/loading_anim.gif" alt="-" /> ';
		
		$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/member/status.php', 'memberStatus' );
		$statusClass = new $classToLoad( $this->registry );
		
		$done	= 0;
		$start	= intval( $this->request['st'] ) >= 0 ? intval( $this->request['st'] ) : 0;
		$end	= intval( $this->request['pergo'] ) ? intval( $this->request['pergo'] ) : 500;
		$dis	= $end + $start;
		$output	= array();
		
		$tmp	= $this->DB->buildAndFetch( array( 'select' => 'status_id', 'from' => 'member_status_updates', 'order' => 'status_id ASC', 'limit' => array( $dis, 1 ) ) );
		$max	= intval( $tmp['status_id'] );
		
		$this->DB->build( array( 'select' => '*', 'from' => 'member_status_updates', 'order' => 'status_id ASC', 'limit' => array( $start, $end ) ) );
		$outer	= $this->DB->execute();
		
		while( $r = $this->DB->fetch( $outer ) )
		{
			$statusClass->rebuildStatus( $r['status_id'] );
			
			$done++;
		}
		
		if ( ! $done AND ! $max )
		{
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=rebuildPhotoThumbs&id=0', $img . ' Rebuilt Status Updates' );
		}
		else
		{
			$thisgoeshere = sprintf( "Rebuilding Status Updates - Up to %s processed so far, continuing...", $dis );
			$text = $thisgoeshere . '<br />' . implode( "<br />", $output );
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . "&module=setup&section=finalize&do=rebuildStatusUpdates&pergo={$this->request['pergo']}&st={$dis}", $img . $text );
		}
	}
	
	protected function _rebuildProfilePhotoThumbs()
	{
		$img		= '<img src="' . $this->settings['skin_acp_url'] . '/images/loading_anim.gif" alt="-" /> ';
		
		/* Image Class */
		$classToLoad  = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/member/photo.php', 'classes_member_photo' );
		$photoClass = new $classToLoad( $this->registry );
		
		//-----------------------------------------
		// Set up
		//-----------------------------------------
		
		$done   = 0;
		$start  = intval($this->request['st']) >=0 ? intval($this->request['st']) : 0;
		$end    = intval( $this->request['pergo'] ) ? intval( $this->request['pergo'] ) : 100;
		$dis    = $end + $start;
		$output = array();
		
		//-----------------------------------------
		// Got any more?
		//-----------------------------------------
		
		$tmp = $this->DB->buildAndFetch( array(  'select' => 'member_id', 'from' => 'members', 'order' => 'member_id ASC', 'limit' => array($dis,1)  ) );
		$max = intval( $tmp['pp_member_id'] );
		
		//-----------------------------------------
		// Avoid limit...
		//-----------------------------------------
		
		$this->DB->build( array(
			'select'	=> 'm.*',
			'from'		=> array( 'members' => 'm' ),
			'add_join'	=> array(
				array(
					'select'	=> 'pp.*',
					'from'		=> array( 'profile_portal' => 'pp' ),
					'where'		=> 'pp.pp_member_id = m.member_id',
					'type'		=> 'left',
				),
			),
			'order'	=> 'm.member_id ASC',
			'limit' => array($start,$end)
		) );
		$outer = $this->DB->execute();
		
		//-----------------------------------------
		// Process...
		//-----------------------------------------
		
		while( $r = $this->DB->fetch( $outer ) )
		{
			// Update Last Post Information
			$post = $this->DB->buildAndFetch( array( 'select' => 'post_date', 'from' => 'posts', 'where' => "author_id = {$r['member_id']}", 'order' => 'post_date DESC' ) );
			
			$this->DB->update( 'members', array( 'last_post' => $post['post_date'] ), 'member_id = ' . $r['member_id'] );
			
			if ( $r['pp_main_photo'] != '' )
			{
				/* Preserve FB, Twitter and Gravatar photos */
				if( $r['pp_photo_type'] != 'custom' )
				{
					continue;
				}
		
				$update = array();
		
				$info	= $photoClass->buildSizedPhotos( str_replace( 'upload:', '', $r['pp_main_photo'] ), $r['pp_member_id'], true );
		
				if( $info['status'] == 'missing_image' )
				{
					$update['pp_main_photo']	= '';
					$update['pp_main_width']	= 0;
					$update['pp_main_height']	= 0;
					$update['pp_thumb_photo']	= '';
					$update['pp_thumb_width']	= 0;
					$update['pp_thumb_height']	= 0;
				}
				else
				{
					$update['pp_main_width']	= intval( $info['final_width'] );
					$update['pp_main_height']	= intval( $info['final_height'] );
					$update['pp_thumb_photo']	= $info['t_final_location'] ? $info['t_final_location'] : $info['final_location'];
					$update['pp_thumb_width']	= intval( $info['t_final_width'] );
					$update['pp_thumb_height']	= intval( $info['t_final_height'] );
				}
		
				$this->DB->update( 'profile_portal', $update, 'pp_member_id=' . $r['pp_member_id'] );
			}
				
			$done++;
		}
		
		//-----------------------------------------
		// Finish - or more?...
		//-----------------------------------------
		
		if ( ! $done and ! $max )
		{
			//-----------------------------------------
			// Done..
			//-----------------------------------------
		
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=recache_all&id=0', $img . ' Member Data Resynchronized' );
		}
		else
		{
			//-----------------------------------------
			// More..
			//-----------------------------------------
			$thisgoeshere = sprintf( "Resynchronising Member Data and Rebuilding Profile Photos - Up to %s processed so far, continuing...", $dis );
			$text = $thisgoeshere . '<br />' . implode( "<br />", $output );
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . "&module=setup&section=finalize&do=rebuildPhotoThumbs&pergo={$this->request['pergo']}&st={$dis}", $img . $text );
		}
		
	}
	
	protected function _recacheAll()
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$id			= intval( $this->request['id'] );
		$cache_name	= '';
		$cache_data	= '';
		$count		= 0;
		$img		= '<img src="' . $this->settings['skin_acp_url'] . '/images/loading_anim.gif" alt="-" /> ';
		$_caches	= array();
		
		//-----------------------------------------
		// Get core cache list
		//-----------------------------------------
		
		$_caches = array_merge( $_caches, $this->registry->_fetchCoreVariables( 'cache' ) );
		
		//-----------------------------------------
		// Get all application's cache lists
		//-----------------------------------------
		
		foreach( ipsRegistry::$applications as $app_dir => $app_data )
		{
		$_file = IPSLib::getAppDir( $app_dir ) . '/extensions/coreVariables.php';
		
			if ( is_file( $_file ) )
					{
					$CACHE = array();
					require( $_file );/*maybeLibHook*/
		
		foreach( $CACHE as $k => $v )
		{
		$CACHE[ $k ]['cache_app']	= $app_dir;
		}
			
		$_caches = array_merge( $_caches, $CACHE );
		}
		}
		
		//-----------------------------------------
		// Get cache data
		//-----------------------------------------
		
		foreach( $_caches as $_cache_name => $_cache_data )
		{
			if ( $count == $id )
			{
				$cache_name = $_cache_name;
				$cache_data = $_cache_data;
					break;
			}
					
			$count++;
		}
		
		
		$id++;
		
		if ( $cache_name )
		{
			$this->cache->rebuildCache( $cache_name, $cache_data['cache_app'] ? $cache_data['cache_app'] : 'global' );
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=recache_all&id=' . $id, $img . 'Rebuilding Cache: ' . $_cache_name );
		}
		else
		{
			$this->registry->output->multipleRedirectHit( $this->settings['base_url'] . '&module=setup&section=finalize&do=finish', $img . ' Completed.' );
		}
	}
	
}