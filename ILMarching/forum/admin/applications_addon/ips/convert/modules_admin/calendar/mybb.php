<?php
/**
 * IPS Converters
 * IP.Calendar 3.0 Converters
 * 
 * Last Update: $Date$
 * Last Updated By: $Author$
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision$
 */


	$info	= array(
					'key'	=> 'mybb',
					'name'	=> 'MyBB 1.6',
					'login'	=> false
				);

	$parent	= array(
					'required'	=> true,
					'choices'	=> array(
										array(
												'app'	=> 'board', 
												'key'	=> 'mybb', 
												'newdb'	=> false
											),
										)
					);

	class admin_convert_calendar_mybb extends ipsCommand
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

			$this->actions	= array(
				'cal_calendars' 		=> array('forum_perms' ),
				'cal_events'			=> array('cal_calendars', 'members' ),
			);

			//-----------------------------------------
	        // Load our libraries
	        //-----------------------------------------

			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_master.php' );
			require_once( IPS_ROOT_PATH . 'applications_addon/ips/convert/sources/lib_calendar.php' );
			$this->lib	=  new lib_calendar( $registry, $html, $this );

	        $this->html	= $this->lib->loadInterface();
			$this->lib->sendHeader( 'MyBB Calendar &rarr; IP.Calendar' );

			//-----------------------------------------
			// Are we connected?
			// (in the great circle of life...)
			//-----------------------------------------

			$this->HB	= $this->lib->connect();

			//-----------------------------------------
			// What are we doing?
			//-----------------------------------------

			if ( array_key_exists( $this->request['do'], $this->actions ) )
			{
				call_user_func( array( $this, 'convert_'.$this->request['do'] ) );
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
			$this->registry->output->html		.= $this->html->convertFooter();
			$this->registry->output->html_main	.= $this->registry->output->global_template->global_frame_wrapper();
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
		public function countRows( $action )
		{
			switch ( $action )
			{
				case 'cal_calendars':
					return $this->lib->countRows('calendars');
					break;
					
				case 'cal_events':
					return $this->lib->countRows('events');
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
		public function checkConf( $action )
		{
			return false;
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
			return $post;
		}

		/**
		 * Convert Calendars
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_cal_calendars()
		{

			//---------------------------
			// Set up
			//---------------------------

			$main	= array(
							'select'	=> 'c.*, c.cid as CalId',
							'from'		=> array('calendars' => 'c'),
							'order'		=> 'c.cid ASC',
							'add_join'	=> array(
												array(
													'select'	=> 'p.*, p.cid as calID',
													'from'		=> array('calendarpermissions'	=>	'p'),
													'where'		=> 'c.cid=p.cid',
													'type'		=> 'left',
												),
											),
						);

			$loop = $this->lib->load('cal_calendars', $main);

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch( $this->lib->queryRes ) )
			{
				//-----------------------------------------
				// Handle permissions
				//-----------------------------------------

				$perms = array();
				$perms['view']		= $row['canviewcalendar'];
				$perms['create']	= $row['canaddevents'];
				$perms['bypassmod']	= $row['canbypasseventmod'];
				
				$save	= array(
								//'cal_id'				=> $row['cid'],
								'cal_title'				=> $row['name'],
								'cal_moderate'			=> $row['moderation'],
								'cal_position'			=> $row['disporder'],
								'cal_event_limit'		=> $row['eventlimit'],
								'cal_bday_limit'		=> $row['showbirthdays'],
								'cal_title_seo'			=> IPSText::makeSeoTitle( $row['name'] ),
								'cal_comment_moderate'	=> $row['moderation'],
							);
				//-----------------------------------------
				// Send
				//-----------------------------------------

				$this->lib->convertCalendar($row['CalId'], $save, $perms);
			}

			$this->lib->next();

		}

		/**
		 * Convert Events
		 *
		 * @access	private
		 * @return void
		 **/
		private function convert_cal_events()
		{
			//---------------------------
			// Set up
			//---------------------------

			$main = array(
							'select'	=> '*',
							'from'		=> 'events',
							'order'		=> 'eid ASC',
						);

			$loop = $this->lib->load('cal_events', $main);

			//---------------------------
			// Loop
			//---------------------------

			while ( $row = ipsRegistry::DB('hb')->fetch( $this->lib->queryRes ) )
			{
				$repeats	= unserialize( $row['repeats'] );
				$save	= array(
								'event_calendar_id'			=> $row['cid'],
								'event_member_id'			=> $row['uid'],
								'event_content'				=> $this->fixPostData( $row['description'] ),
								'event_title'				=> IPSText::htmlspecialchars( $row['name'] ),
								'event_perms'				=> '*',
								'event_private'				=> $row['private'],
								'event_approved'			=> $row['visible'],
								'event_saved'				=> $row['dateline'],
								'event_lastupdated'			=> $row['dateline'],
								'event_recurring'			=> is_array( $repeats ) ? '1' : '0',
								'event_start_date'			=> date( 'Y-m-d H:i:s', $row['starttime'] ),
								'event_end_date'			=> date( 'Y-m-d H:i:s', $row['endtime'] ),
								'event_title_seo'			=> IPSText::makeSeoTitle( $row['name'] ),
							);				
				
				$this->lib->convertEvent( $row['eid'], $save );
			}

			$this->lib->next();

		}

	}