<?php
/**
 * IPS Converters
 * Application Files
 * Library functions
 * Last Update: $Date: 2014-01-02 18:21:30 -0500 (Thu, 02 Jan 2014) $
 * Last Updated By: $Author: rashbrook $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 951 $
 *
 * @todo		Remove Tracker and Subscriptions references / code.
 * @todo		Move hard coded text into language strings
 */

abstract class lib_master extends _interface
{
	private $linkCache = array();
	public $usingKeys = FALSE;
	protected $extendedInserts = array();
	public $usingExtendedInserts = FALSE;
	protected $parent = array();

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	object		ipsRegistry
	 * @return	void
	*/
	public function __construct(ipsRegistry $registry, $html='', $module='', $link=true, $extra='')
	{
		$this->registry 	= $registry;
		$this->DB	    	= $this->registry->DB();
		$this->settings =& $this->registry->fetchSettings();
		$this->request  =& $this->registry->fetchRequest();
		$this->member   	= $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
		$this->cache		= $this->registry->cache();
		$this->caches   =& $this->registry->cache()->fetchCaches();
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_convert' );
		$this->app = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'conv_apps', 'where' => "name='{$this->settings['conv_current']}'" ) );
		$this->useLocalLink = $link;
		$this->module = $module;
		$this->extra = $extra;
		
		// Parent conversion? (Thanks Mert!)
		if ( $this->app['parent'] )
		{
			$this->parent = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'conv_apps', 'where' => "app_id={$this->app['parent']}" ) );
		}
		
		// Extended Inserts
		$this->usingExtendedInserts = ! $this->app['app_merge'];

		if ( file_exists( DOC_IPS_ROOT_PATH . 'cache/converter_lock.php' ) )
		{
			parent::sendError( 'The converters have been locked. To unlock, delete the cache/converter_lock.php file.' );
		}

	}
	
	public function getExtendedInserts($k='')
	{
		if ( $k )
		{
			return count( $this->extendedInserts[$k] ) ? $this->extendedInserts[$k] : array();
		}
		
		return $this->extendedInserts;
	}

	/**
	 * Test connect to external database
	 *
	 * @access 	public
	 * @param 	array 			Database details
	 * @return 	Error, or true on success
	 **/
	public function test_connect($app)
	{
		if (!file_exists(IPS_KERNEL_PATH . 'classDb' . ucwords($app['db_driver']) . '.php'))
		{
			return 'Invalid driver';
		}

		require_once( IPS_KERNEL_PATH . 'classDb' . ucwords($app['db_driver']) . '.php' );

		$classname = "db_driver_".$app['db_driver'];

		$DB = new $classname;

		$DB->obj['sql_database']   = $app['db_db'];
		$DB->obj['sql_user']	   = $app['db_user'];
		$DB->obj['sql_pass']	   = $app['db_pass'];
		$DB->obj['sql_host']	   = $app['db_host'];
		$DB->obj['sql_charset']	   = $app['db_charset'];

		define( 'SQL_DRIVER'              , $app['db_driver'] );
		define( 'IPS_MAIN_DB_CLASS_LOADED', TRUE );

		/* Required vars? */
		if ( is_array( $DB->connect_vars ) and count( $DB->connect_vars ) )
		{
			foreach( $DB->connect_vars as $k => $v )
			{
				$DB->connect_vars[ $k ] = ( isset( $app[ $k ] ) ) ? $app[ $k ] : ipsRegistry::$settings[ $k ];
			}
		}

		$DB->return_die = true;

		if ( ! $DB->connect() )
		{
			return $DB->error;
		}
		else
		{
			return true;
		}

	}

	/**
	 * Connect to external database
	 *
	 * @access 	public
	 * @return 	void
	 **/
	public function connect()
	{
		//-----------------------------------------
		// Turn the board (or whatever) offline
		//-----------------------------------------

		$doNothing		= false;
		$offlineSetting = 'board_offline';
		$offlineSetTo	= true;
		$offlineMessage = 'offline_msg';

		switch ($this->app['sw'])
		{
			case 'blog':
				$offlineSetting = 'blog_online';
				$offlineSetTo 	= false;
				$offlineMessage = 'blog_offline_text';
				break;

			case 'calendar':
				$doNothing = true;
				break;

			case 'ccs':
				$offlineSetting = 'ccs_online';
				$offlineSetTo 	= false;
				$offlineMessage = 'ccs_offline_message';
				break;

			case 'downloads':
				$offlineSetting = 'idm_online';
				$offlineSetTo 	= false;
				$offlineMessage = 'idm_offline_msg';
				break;

			case 'gallery':
				$offlineSetting = 'gallery_offline';
				$offlineMessage = 'gallery_offline_text';
				break;

			case 'subscriptions':
				$doNothing = true;
				break;

			case 'tracker':
				$offlineSetting = 'tracker_is_online';
				$offlineSetTo 	= false;
				$offlineMessage = 'tracker_offline_message';
				break;
		}
		
		if ( defined( 'CONV_NO_BOARD_OFFLINE' ) AND CONV_NO_BOARD_OFFLINE )
		{
			$doNothing = true;
		}

		if (!$doNothing)
		{
			if ( ($offlineSetTo and !$this->settings[$offlineSetting]) or (!$offlineSetTo and $this->settings[$offlineSetting]) )
			{
				IPSLib::updateSettings(array($offlineSetting => $offlineSetTo, $offlineMessage => 'Conversion in process'));
			}
		}

		//-----------------------------------------
		// And connect
		//-----------------------------------------

		return $this->registry->dbFunctions()->setDB($this->app['db_driver'], 'hb',
			array(
				'sql_database' => $this->app['db_db'],
				'sql_user' => $this->app['db_user'],
				'sql_pass' => $this->app['db_pass'],
				'sql_host' => $this->app['db_host'],
				'sql_tbl_prefix' => $this->app['db_prefix'],
				'sql_charset' => $this->app['db_charset'],
			)
		);
	}

	/**
	 * Test if an action has been done
	 *
	 * @access 	public
	 * @param	string		action (e.g. 'members', 'forums', etc.)
	 * @param 	boolean		If true, will check parent app's history instead of own
	 * @return 	string		'Converted', 'Converted with errors' or '-'
	 **/
	public function getStatus($action, $parent=false)
	{
		$get = unserialize($this->settings['conv_completed']);

		if ($parent && $this->app['parent'] != -1)
		{
			$appparent = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'conv_apps', 'where' => 'app_id='.$this->app['parent'] ) );
			$us = $get[$appparent['name']];
		}
		else
		{
			$us = $get[$this->app['name']];
		}

		if (is_array($us) and isset($us[$action]))
		{
			if ($us[$action] === true)
			{
				return 'Converted';
			}
			else
			{
				return 'Converted with errors';
			}
		}
		else
		{
			if ( $parent && $this->app['parent'] == -1 )
			{
				if ( ! isset( $this->module->actions[$action] ) )
				{
					return 'Converted';
				}
			}
			
			return '-';
		}
	}

	/**
	 * Count Rows
	 *
	 * @access 	public
	 * @param	string		table name
	 * @param	string		where (optional)
	 * @return	integer		row count
	 **/
	public function countRows($table, $where='', $group='')
	{
		ipsRegistry::DB('hb')->return_die = true;
		if ($where)
		{
			if ( $group )
			{
				$count = @ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => $table, 'where' => $where, 'group' => $group ) );
			}
			else
			{
				$count = @ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => $table, 'where' => $where ) );
			}
		}
		else
		{
			if ( $group )
			{
				$count = @ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => $table, 'group' => $group ) );
			}
			else
			{
				$count = @ipsRegistry::DB('hb')->buildAndFetch( array( 'select' => 'COUNT(*) as count', 'from' => $table ) );
			}
		}
		if (!$count)
		{
			ipsRegistry::DB('hb')->query('SHOW TABLES');
			$tables = array();
			while($row = ipsRegistry::DB('hb')->fetch())
			{
				$tables[] = $row;
			}
			$tl = '<ul>';
			foreach ($tables as $a)
			{
				$name = $a['Tables_in_'.$this->app['db_db']];
				// try to figure out prefix automagically - only works with underscores.
				//preg_match('/^[a-z]+_[a-z]+$/i', $name, $matches);
				$prefix = explode('_', $name);
				$tl .= "<li>{$name}</li>";
			}
			$tl .= '</ul>';
			parent::sendError("
				<script type='text/javascript'>
					jQ(document).ready(function() {
						jQ('#tableList').hide();
		    			jQ('#toggleTables').click(function() {
						  jQ('#tableList').toggle('slow');
						});
					});
				</script><br />
				It looks like your SQL Prefix may be incorrect. Please click the 'Go Back' link below and check that the SQL prefix is correct.
				<br /><br />
				The table prefix you entered was: {$this->app['db_prefix']}<br />
				We have detected that the table prefix might be: {$prefix['0']}_ <em>(note that this is an imprecise estimate)</em>
				<br /><br />
				<a href='javascript:void(0)' id='toggleTables'>Click here to view a more detailed error</a>
				<br /><br />
				<div id='tableList'>
					Error: Could not find the <em><u>{$table}</u></em> table.<br /><br />
					Below are a list of the tables in your database in order to help you ascertain the table prefix:<br /><br />
					{$tl}
				</div>
				"
			);
		}
		return $count['count'];
	}

	/**
	 * Loads what's needed and returns the relevant rows
	 *
	 * @access 	public
	 * @param	string		action (e.g. 'members', 'forums', etc.)
	 * @param	array 		Query parameters for DB::build()
	 * @param	array 		Other values to send to self::prepare() (optional)
	 * @param 	string 		Action to do next (optional)
	 * @param	bool		Whether or not to load all rows now (necessary for mapping configuaration steps)
	 * @return 	array 		Data from foreign database
	 **/
	public function load($action, $mainBuild, $extra=array(), $next=array(), $loadAll=FALSE)
	{
		ipsRegistry::getClass( 'class_localization' )->loadLanguageFile( array( 'public_register' ), 'core' );

		if ( ! isset($this->request['st']) )
		{
			$info = $this->menuRow($action);
		}
		
		// Rebuild file type cache in case we've added extra types - remove when above recache method is completed.
		if ( $action == 'attachments' )
		{
			$this->cache->rebuildCache( 'attachtypes', 'forums' );
		}
		
		// need to rebuild profile fields after we've added them apparently.
		if ( $action == 'pfields' )
		{
			$this->cache->rebuildCache( 'profilefields', 'core' );
		}
		
		if ( $this->usingKeys !== TRUE )
		{
			$this->start = intval($this->request['st']);
			$this->end = $this->start + intval($this->request['cycle']);
		} else {
			$this->start = isset($this->request['lastKey']) ? urldecode($this->request['lastKey']) : 0;
			$this->end = intval($this->request['count']);
		}
		
		// only update our static vars on POST. GET is our 'finish' screen which actually logs the error.
		if ( $this->request['request_method'] == 'post' )
		{
			if ( $this->request['empty'] == '1' )
			{
				$empty = '1';
			}
			
			if ( $this->request['rc'] == '1' )
			{
				$reconvert = '1';
			}
		}
		
		//if ( defined( IN_DEV ) )
		//{
		// we need to rebuild caches ideally after each convert step.
		// dev only for now.
		
			if ( $this->start != 0 ) // dont log on start or getMoreInfo or saveMoreInfo
			{
				$message = 'step run at '. time() . ' ( ' . date('Y-m-d H:i:s') . ' )';
				
				if ( $empty == '1' )
				{
					$message .= ' (LOCAL DATA EMPTIED) ';
				}
				
				if ( $reconvert == '1' )
				{
					$message .= ' ( LIKELY RECONVERTED ) ';
				}
				
				// request_method is passed as a get arg when reconverting.
				$this->recacheAndLog( $action, $message );
			}
		//}
		
		
		if ( $this->start == 0 )
		{
			// Truncate
			$this->prepare( $action );

			if ( is_array( $extra ) )
			{
				foreach ( $extra as $e )
				{
					$this->prepare( $e );
				}
			}

			// Save that it's been started and get rid of old errors
			$get = unserialize($this->settings['conv_completed']);
			$us = $get[$this->app['name']];
			$us = is_array($us) ? $us : array();
			$us = array_merge($us, array($action => false));
			$get[$this->app['name']] = $us;
			IPSLib::updateSettings(array('conv_completed' => serialize($get), 'conv_error' => serialize(array())));

			// Database changes?
			$dbChanges = $this->databaseChanges($action);
			if (is_array($dbChanges))
			{
				foreach ($dbChanges as $key => $value)
				{
					switch ($key)
					{
						case 'addfield':
							if (!$this->DB->checkForField($value[1], $value[0]))
							{
								$this->DB->addField( $value[0], $value[1], $value[2] );
							}
							break;
					}
				}
			}
		}

		$this->errors = unserialize($this->settings['conv_error']);

		if ( $mainBuild === FALSE )
		{
			return;
		}

		if ( $this->usingKeys === TRUE )
		{
			if ( $this->start != 0 )
			{
				$mainBuild['where'] = (isset($mainBuild['where']) ? $mainBuild['where'] . ' AND ' : '') . $this->key . ">'{$this->start}'";
			}
			$mainBuild['limit'] = array(0, $this->request['cycle']);
		} else {
			$mainBuild['limit'] = array($this->start, $this->request['cycle']);
		}

		ipsRegistry::DB('hb')->build($mainBuild);
		$this->queryRes = ipsRegistry::DB('hb')->execute();

		if (!ipsRegistry::DB('hb')->getTotalRows($this->queryRes))
		{
			if (!empty($next))
			{
				parent::goToNext($next);
			}
			else
			{
				// Save that it's been completed
				$get = unserialize($this->settings['conv_completed']);
				$us = $get[$this->app['name']];
				$us = is_array($us) ? $us : array();
				if (empty($this->errors))
				{
					$us = array_merge($us, array($action => true));
				}
				else
				{
					$us = array_merge($us, array($action => 'e'));
				}
				$get[$this->app['name']] = $us;
				IPSLib::updateSettings(array('conv_completed' => serialize($get)));

				// Clear Caches
				switch($info['name'])
				{
					case 'Members':
						IPSContentCache::truncate( 'sig' );
						break;
					
					case 'Posts':
						IPSContentCache::truncate( 'post' );
						break;
				}
				
				// Display
				parent::displayFinishScreen($info);
			}
		}

		if ( $loadAll )
		{
			$return = array();
			while ( $row = ipsRegistry::DB('hb')->fetch($this->queryRes) )
			{
				$return[] = $row;
			}
			return $return;
		}

	}

	/**
	 * Log an error so they can be displayed at the end
	 *
	 * @access 	public
	 * @param 	integer 	ID number
	 * @param 	string		Error
	 * @return 	void
	 **/
	public function logError($id, $error)
	{
		$this->errors[] = "{$id}: {$error}";

		// log to file
		if ( $FH = @fopen( DOC_IPS_ROOT_PATH . 'cache/converter_error_log_'.date('m_d_y').'.cgi', 'a' ) )
		{
			@fwrite( $FH, "{$id}: {$error}\n" );
			@fclose( $FH );
		}
	}
	
	/**
	 * Log a generic message to file for debug purposes.
	 *
	 * @access 	public
	 * @param 	integer 	Action Performed
	 * @param 	string		Message
	 * @return 	void
	 **/
	public function logMessage( $action, $message=false )
	{
		// log to file
		if ( $FH = @fopen( DOC_IPS_ROOT_PATH . 'cache/converter_log_' . $this->request['section'] . '_' . date('m_d_y') .'.cgi', 'a' ) )
		{
			if ( empty ( $message ) )
			{
				$message = 'step run at '. time() . ' ('. date('Y-m-d H:i:s') . ')';
			}
			@fwrite( $FH, "{$action}: {$message}\n" );
			@fclose( $FH );
		}
	}

	/**
	 * Save More Info
	 *
	 * @access	public
	 * @param	string			action (e.g. 'members', 'forums', etc.)
	 * @param 	array / string	Fields in conv_extra to empty if we're reconfiguring OR the word 'map' if this is being mapped
	 * @return 	void
	 **/
	public function saveMoreInfo($action, $empty)
	{
		$get = unserialize($this->settings['conv_extra']);
		$us = $get[$this->app['name']];
		$us = is_array($us) ? $us : array();
		$extra = is_array($us[$action]) ? $us : array_merge($us, array($action => array()));
		$get[$this->app['name']] = $extra;

		if ($this->request['info'])
		{
			if (!is_array($empty) and $empty == 'map')
			{
				if (is_array($this->request[$action]))
				{
					$get[$this->app['name']][$action] = $this->request[$action];
					$save = serialize($get);
					IPSLib::updateSettings(array('conv_extra' => $save));
				}
				else
				{
					$get[$this->app['name']][$action] = array();
					IPSLib::updateSettings(array('conv_extra' => serialize($get)));
				}
			}
			else
			{
				if (is_array($this->request['input']))
				{
					foreach ($this->request['input'] as $key => $value)
					{
						$get[$this->app['name']][$key] = $value;
					}
					$save = serialize($get);
					IPSLib::updateSettings(array('conv_extra' => $save));
				}
				else
				{
					foreach ($empty as $key)
					{
						$get[$this->app['name']][$key] = '';
					}
					IPSLib::updateSettings(array('conv_extra' => serialize($get)));
				}
			}
			$this->reload();
		}
	}

	/**
	 * If reconverting, delete entries from last conversion
	 *
	 * @access	public
	 * @param	string		action (e.g. 'members', 'forums', etc.)
	 * @return 	void
	 * @todo	[Future] Update the function to remove batches of data and not all at once
	 * @todo	See report: http://community.invisionpower.com/tracker/issue-25348-big-boards-memory-issue-with-re-conversion/
	 **/
	public function prepare($action)
	{
		// New table switching device - makes things LOTS faster.
		switch( $action )
		{
			case 'posts':
				$table = 'conv_link_posts';
				break;
			
			case 'topics':
				$table = 'conv_link_topics';
				break;
				
			case 'pms':
			case 'pm_posts':
			case 'pm_maps':
				$table = 'conv_link_pms';
				break;
				
			default:
				$table = 'conv_link';
				break;
		}
		
		/**
		 * Optimization for big boards
		 * If we are going to truncate the tables there is no need to load the IDs!
		 */
		if ( !isset($this->request['empty']) || !$this->request['empty'] )
		{
			$this->DB->build(array('select' => 'ipb_id as id', 'from' => $table, 'where' => "type = '{$action}' AND duplicate = '0' AND app={$this->app['app_id']}"));
			$this->DB->execute();
			$ids = array();
			while ($row = $this->DB->fetch())
			{
				$ids[] = $row['id'];
			}
			$id_string = implode(",", $ids);
		}

		$del = $this->truncate( $action );
		if ( !empty ( $del['tables'] ) )
		{
			foreach ( $del['tables'] as $del_table => $del_id )
			{
				if ( $this->request['empty'] )
				{
					if ( $del['where'][$del_table] )
					{
						$this->DB->delete( $del_table, $del['where'][$del_table] );
					}
					else
					{
						$this->DB->delete( $del_table );
					}
				}
				elseif( $del_table != "" && count($ids) )
				{
					if ( $del['where'][$del_table] )
					{
						$this->DB->delete( $del_table, "{$del_id} IN ({$id_string}) AND {$del['where'][$del_table]}" );
					}
					else
					{
						$this->DB->delete( $del_table, "{$del_id} IN ({$id_string})" );
					}
				}
			}
		}
		
		$this->DB->delete( $table, "type = '{$action}' AND app={$this->app['app_id']}" );
	}

	public function prepareDeletionLog($type)
	{
		if ($this->start != 0)
		{
			return;
		}

		$this->DB->build(array('select' => 'ipb_id as id', 'from' => 'conv_link', 'where' => "type = 'core_soft_delete_log' AND app={$this->app['app_id']} AND foreign_id LIKE '{$type}_%'" ) );
		$delRes = $this->DB->execute();
		$ids = array();

		while ( $row = $this->DB->fetch($delRes) )
		{
			$ids[] = $row['id'];

			if ( count($ids) >= 1500 )
			{
				$idString = implode(", ",$ids );
				$this->DB->delete('core_soft_delete_log', "sdl_id IN ({$idString})");
				$ids = array();
			}
		}

		if ( count($ids) )
		{
			$idString = implode(", ",$ids );
			$this->DB->delete('core_soft_delete_log', "sdl_id IN ({$idString})");
		}

		$this->DB->delete('conv_link', "type='core_soft_delete_log' AND app={$this->app['app_id']} AND foreign_id LIKE '{$type}_%'" );
	}

	/**
	 * If reconverting, delete entries from last conversion for reports
	 *
	 * @access	public
	 * @param	string		type of reports
	 * @return 	void
	 **/
	public function prepareReports($type)
	{
		if ($this->start == 0)
		{
			$this->DB->build(array('select' => 'ipb_id as id', 'from' => 'conv_link', 'where' => "type = 'rc_reports_{$type}' AND app={$this->app['app_id']}"));
			$this->DB->execute();
			$rids = array();
			while ($row = $this->DB->fetch())
			{
				$rids[] = $row['id'];
			}
			$rid_string = implode(",", $rids);

			$this->DB->build(array('select' => 'ipb_id as id', 'from' => 'conv_link', 'where' => "type = 'rc_reports_index_{$type}' AND app={$this->app['app_id']}"));
			$this->DB->execute();
			$iids = array();
			while ($row = $this->DB->fetch())
			{
				$iids[] = $row['id'];
			}
			$iid_string = implode(",", $iids);

			if ($rid_string)
			{
				$this->DB->delete('rc_reports', "id IN ({$rid_string})");
			}
			if ($iid_string)
			{
				$this->DB->delete('rc_reports_index', "id IN ({$iid_string})");
			}

			$this->DB->delete('conv_link', "type='rc_reports_{$type}' AND app={$this->app['app_id']}");
			$this->DB->delete('conv_link', "type='rc_reports_index_{$type}' AND app={$this->app['app_id']}");
		}
	}

	/**
	 * If reconverting, delete entries from last conversion for permissions index
	 *
	 * @access	public
	 * @param	string		type of permissions
	 * @return 	void
	 **/
	public function preparePermissions($type)
	{
		if ($this->start == 0)
		{
			$this->DB->build(array('select' => 'ipb_id as id', 'from' => 'conv_link', 'where' => "type = 'forum_perms_{$type}' AND app={$this->app['app_id']}"));
			$this->DB->execute();
			$rids = array();
			while ($row = $this->DB->fetch())
			{
				$rids[] = $row['id'];
			}
			$rid_string = implode(",", $rids);

			if ($rid_string)
			{
				$this->DB->delete('permission_index', "perm_type_id IN ({$rid_string})");
			}

			$this->DB->delete('conv_link', "type='forum_perms_{$type}' AND app={$this->app['app_id']}");
		}
	}

	/**
	 * Add link
	 *
	 * @access	public
	 * @param	integer		IPB's ID
	 * @param	integer		Foreign ID
	 * @param	string		Type
	 * @param	boolean		Duplicate?
	 * @return	void
	 **/
	public function addLink($ipb_id, $foreign_id, $type, $dupe='0')
	{
		// New table switching device - makes things LOTS faster.
		switch( $type )
		{
			case 'posts':
				$table = 'conv_link_posts';
				break;
			
			case 'topics':
				$table = 'conv_link_topics';
				break;
				
			case 'pms':
			case 'pm_posts':
			case 'pm_maps':
				$table = 'conv_link_pms';
				break;
				
			default:
				$table = 'conv_link';
				break;
		}
		
		// Setup the insert array with link values
		$insert_array = array( 'ipb_id'		=> $ipb_id,
							   'foreign_id' => $foreign_id,
							   'type'		=> $type,
							   'duplicate'	=> $dupe,
							   'app'		=> $this->app['app_id'] );

		// Insert the link into the database
		$this->DB->insert( $table, $insert_array );

		// Cache the link
		$this->linkCache[$type][$foreign_id] = $ipb_id;
	}

	/**
	 * Get Link
	 *
	 * @access	public
	 * @param	integer		Foreign ID
	 * @param	string		Type
	 * @param	boolean		If true, will return false on error, otherwise will display error
	 * @param 	boolean		If true, will check parent app's history instead of own
	 * @return 	integer		IPB's ID
	 **/
	public function getLink($foreign_id, $type, $ret=false, $parent=false)
	{
		if ( ( $parent && $this->app['parent'] == -1 ) OR $this->usingExtendedInserts OR ( $parent && $this->parent['app_id'] && ! $this->parent['app_merge'] ) )
		{
			// We need to bring this back as it broke things
			switch( $type )
			{
				case 'members':
				case 'posts':
				case 'topics':
				case 'forums':
				case 'pms':
				case 'pm_posts':
				case 'pm_maps':
					return $foreign_id;
					break;
			}
		}
				
		if (!$foreign_id or !$type)
		{
			if ($ret)
			{
				return false;
			}
			parent::sendError("There was a problem with the converter - could not get valid link: {$type}:{$foreign_id}");
		}

		if ( isset($this->linkCache[$type][$foreign_id]) )
		{
			return $this->linkCache[$type][$foreign_id];
		}
		else
		{
			// New table switching device - makes things LOTS faster.
			switch( $type )
			{
				case 'posts':
					$table = 'conv_link_posts';
					break;
				
				case 'topics':
					$table = 'conv_link_topics';
					break;
				
				case 'pms':
				case 'pm_posts':
				case 'pm_maps':
					$table = 'conv_link_pms';
					break;
					
				default:
					$table = 'conv_link';
					break;
			}
			
			$appid = ($parent) ? $this->app['parent'] : $this->app['app_id'];
			$row = $this->DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => $table, 'where' => "foreign_id='{$foreign_id}' AND type='{$type}' AND app={$appid}" ) );

			if(!$row)
			{
				if ( $ret )
				{
					return false;
				}
			}

			$this->linkCache[$type][$foreign_id] = $row['ipb_id'];
			return $row['ipb_id'];
		}
	}

	/**
	 * Add entry to global permissions index
	 *
	 * @access	public
	 * @param	string		Type
	 * @param	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @param	integer		Local ID number
	 * @return 	void
	 **/
	public function addToPermIndex($type, $id, $perms, $local_id)
	{
		
		// Process authorized users
		if ($perms['authorized_users'])
		{
			foreach (explode($perms['authorized_users']) as $user)
			{
				$save[] = $this->getLink($user, 'members');
			}
			$perms['authorized_users'] = implode(',', $save);
		}
		
		// Work out app
		switch ($type)
		{
			case 'forum':
				$app	= 'forums';
				$fperms = array(
					'view'	=> $perms['view'],
					2		=> $perms['read'],
					3		=> $perms['reply'],
					4		=> $perms['start'],
					5		=> $perms['upload'],
					6		=> $perms['download'],
					'oo'	=> 0,
					'fo'	=> 0,
					'auth'	=> '',
				 );
				break;

			case 'calendar':
				$app	= 'calendar';
				$fperms = array(
					'view'	=> $perms['view'],
					2		=> $perms['create'],
					3		=> $perms['bypassmod'],
					4		=> '',
					5		=> '',
					6		=> '',
					'oo'	=> 0,
					'fo'	=> 0,
					'auth'	=> '',
				 );
				break;

			case 'blog':
				$app	= 'blog';
				$fperms = array(
					'view'	=> $perms['view'],
					2		=> '',
					3		=> '',
					4		=> '',
					5		=> '',
					6		=> '',
					'oo'	=> $perms['owner_only'],
					'fo'	=> 0,
					'auth'	=> $perms['authorized_users'],
				 );
				break;

			case 'cat':
				$app	= 'gallery';
				$fperms = array(
					'view'	=> $perms['view_thumbnails'],
					2		=> $perms['view_images'],
					3		=> $perms['post_images'],
					4		=> $perms['comment'],
					5		=> $perms['moderate'],
					6		=> '',
					'oo'	=> 0,
					'fo'	=> 0,
					'auth'	=> '',
				 );
				break;

			case 'dcat':
				$app	= 'downloads';
				$type	= 'cat';
				$fperms = array(
					'view'	=> $perms['view_files'],
					2		=> $perms['show_files'],
					3		=> $perms['add_files'],
					4		=> $perms['download'],
					5		=> $perms['comment'],
					6		=> $perms['rate'],
					7		=> $perms['bypass_mod'],
					'oo'	=> 0,
					'fo'	=> 0,
					'auth'	=> '',
				 );
				break;
		}

		// Insert
		$insert_array = array(
			'app'				=> $app,
			'perm_type'			=> $type,
			'perm_type_id'		=> $id,
			'perm_view'			=> $fperms['view'],
			'perm_2'			=> $fperms[2],
			'perm_3'			=> $fperms[3],
			'perm_4'			=> $fperms[4],
			'perm_5'			=> $fperms[5],
			'perm_6'			=> $fperms[6],
			'perm_7'			=> $fperms[7],
			'owner_only'		=> $fperms['oo'],
			'friend_only'		=> $fperms['fo'],
			'authorized_users'	=> $fperms['auth'],
		 );

		// authorized_users can't just be blank... oh no, it has to be null
		if (!$insert_array['authorized_users'])
		{
			unset($insert_array['authorized_users']);
		}

		// Check for and remove duplicate entries
        $this->DB->delete('permission_index', "app ='" . $app . "' AND perm_type = '" . $type . "' AND perm_type_id = " . $id);

        // Insert
		$this->DB->insert('permission_index', $insert_array);

		// Link
		$inserted_id = $this->DB->getInsertId();
		$this->addLink($inserted_id, $local_id, 'forum_perms_'.$type);
	}

	/**
	 * Move files
	 *
	 * @access	public
	 * @param	array 		File names 			array('file1.gif', 'folder/file2.txt')
	 * @param 	string		Source path
	 * @param 	string 		Destination path
	 * @return 	true or error message
	 **/
	public function moveFiles($files=array(), $source, $destination)
	{
		foreach ($files as $fileloc)
		{
			// Check the file actually exists
			if (!file_exists($source.'/'.$fileloc))
			{
				$this->logError($id, 'Could not locate file '.$source.'/'.$fileloc);
				return false;
			}

			// Check that we're trying to move it into a valid directory
			if (!is_dir($destination))
			{
				if (!mkdir($destination, 0777, true))
				{
					$this->logError($id, 'Could not create directroy for file - create directory and then reconvert: '.$destination);
					return false;
				}
			}
			if (preg_match('#/#',$fileloc))
			{
				$dir = $destination . '/' . preg_replace('#(.+)/(.+)\.(.+)#', '$1' , $fileloc);
				if (!@is_dir($dir))
				{
					if (!@mkdir($dir, 0777, true))
					{
						$this->logError($id, 'Could not create directroy for file - create directory and then reconvert: '.$dir);
						return false;
					}
				}
			}

			// Now move it!
			if(!@copy( $source.'/'.$fileloc, $destination.'/'.$fileloc))
			{
				$e = error_get_last();
				$this->logError($id, 'Could not move file - attempted to move '.$source.'/'.$fileloc.' to '.$destination.'/'.$fileloc.'<br />'.$e['message'].'<br /><br />');
				return false;
			}
		}
	}

	/**
	 * Create file from data
	 *
	 * @access	public
	 * @param	string 		File name to use
	 * @param	string 		File data
	 * @param	integer		Filesize
	 * @param 	string 		Destination path
	 * @return 	true or error message
	 **/
	public function createFile($filename, $filedata, $filesize, $destination)
	{
		// Check that we're trying to move it into a valid directory
		if (!is_dir($destination))
		{
			if (!mkdir($destination))
			{
				$this->logError($id, 'Could not create directory for file - create directory and then reconvert: '.$destination);
				return false;
			}
		}

		// Now create it!
		$FH = @fopen( $destination . '/' . $filename, 'wb' );
		if ( !$FH )
		{
			$e = error_get_last();
			$this->logError($id, 'Could create file - attempted to create '.$destination . '/' . $filename.'<br />'.$e['message'].'<br /><br />');
			return false;
		}
		else
		{
			@chmod( $destination . '/' . $filename, 0777 );
			if ( !@fwrite( $FH, $filedata, $filesize ) )
			{
				$e = error_get_last();
				$this->logError($id, 'Could not write data to file '.$destination . '/' . $filename.'<br />'.$e['message'].'<br /><br />');
				return false;
			}
			@fclose( $FH );
		}
	}

	/**
	 * Load data for mapping
	 *
	 * @access 	public
	 * @param 	string 		action (groups or forum_perms)
	 * @return 	array 		('id' => 'name', ...)
	 **/
	public function loadLocalInfo($table)
	{
		$return = array();
		$this->DB->build(array('select' => '*', 'from' => $table));
		$this->DB->execute();
		while ($row = $this->DB->fetch())
		{
			switch ($table)
			{
				case 'groups':
					$return[$row['g_id']] = $row['g_title'];
					break;

				case 'forum_perms':
					$return[$row['perm_id']] = $row['perm_name'];
					break;

			}
		}
		return $return;
	}

	/**
	 * Convert tags
	 *
	 * @access	public
	 * @param 	array		Tags
	 * @param 	array 		Data to insert to tags
	 * @return 	boolean		Success or fail
	 **/
	public function convertTags( $tags, $info, $app='forums', $area='topics' )
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------
		
		if (!$info['tag_meta_id'])
		{
			$this->logError($id, 'No ID number provided');
			return false;
		}
		
		if (!$info['tag_meta_parent_id'])
		{
			$this->logError($id, 'No parent ID number provided');
			return false;
		}
		
		// Clean tags
		foreach( $tags as $key => $tag )
		{
			$tags[ $key ] = trim( IPSText::htmlspecialchars( $tag ) );
		}
				
		// Load tagging library
		if ( ! $this->registry->isClassLoaded('tags') )
		{
			require_once( IPS_ROOT_PATH . 'sources/classes/tags/bootstrap.php' );/*noLibHook*/
		}
		
		$this->registry->setClass( 'tags', classes_tags_bootstrap::run( $app, $area ) );
		$this->registry->tags = ipsRegistry::getClass('tags');

		switch ( $area )
		{
			case 'topics':
					$linkType = 'topics';
					$linkParentType = 'forums';
				break;
		}

		// Get links
		$info['tag_meta_id'] 		= $this->getLink( $info['tag_meta_id'], $linkType );
		$info['tag_meta_parent_id']	= $this->getLink( $info['tag_meta_parent_id'], $linkParentType );
		$info['tag_member_id']		= $info['tag_member_id'] ? $this->getLink( $info['tag_member_id'], 'members' ) : 0;

		if (!$info['tag_meta_id'])
		{
			$this->logError($id, 'No ID number available');
			return false;
		}
		
		if (!$info['tag_meta_parent_id'])
		{
			$this->logError($id, 'No parent ID number available');
			return false;
		}
		
		
		$this->registry->tags->add( $tags, $info, 'add' );
		
		return true;
	}

	/**
	 * Convert a member
	 *
	 * @access	public
	 * @param 	array		Basic data (id number, username, email, group, joined date, password)
	 * @param 	array 		Data to insert to members table
	 * @param 	array 		Data to insert to profile table
	 * @param 	array 		Data to insert to custom profile fields table
	 * @param 	string 		Path to avatars folder
	 * @param 	string 		Path to profile pictures folder
	 * @return 	boolean		Success or fail
	 **/
	public function convertMember($info, $members, $profile, $custom, $pic_path='', $groupLink=TRUE)
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------

		if (!$info['id'])
		{
			$this->logError($info['id'], 'No ID number provided');
			return false;
		}
		
		if (!$info['username'])
		{
			$this->logError($info['id'], 'No username provided');
			return false;
		}
		
		if (!$info['email'])
		{
			// See Tracker Report #28874 for reasons why this got changed.
			$info['email'] = $info['id'] . '@' . time ( ) . '.com';
			//$info['email'] = rand(1, 100).'@'.time().'.com';
			$this->logError($info['id'], 'No email address provided - member converted with '.$info['email']);
		}
		
		// Check profile photo
		if ( ! is_writeable( $this->settings['upload_dir'] . '/profile' ) )
		{
			$this->error( $this->settings['upload_dir'] . '/profile is not writeable, cannot continue');
			return false;
		}
		
		//-----------------------------------------
		// Set some needed variables
		//-----------------------------------------
		$now = time();
		$joined = $info['joined'] ? $info['joined'] : $now;

		if ($info['md5pass'])
		{
			$salt = IPSMember::generatePasswordSalt(5);
			$hash = IPSMember::generateCompiledPasshash( $salt, $info['md5pass'] );
		}
		elseif ($info['plainpass'])
		{
			$salt = IPSMember::generatePasswordSalt(5);
			$hash = IPSMember::generateCompiledPasshash( $salt, md5($info['plainpass']) );
		}
		elseif ($info['pass_hash'])
		{
			$salt = $info['pass_salt'];
			$hash = $info['pass_hash'];
		}
		elseif($info['password'] !== NULL)
		{
			$members['conv_password'] = $info['password'];
		}
		else
		{
			// give em a random pass, let's stop those posts by these users being lost and assigned to guests.
			$randPass	=	IPSMember::makePassword();
			$salt		=	IPSMember::generatePasswordSalt(5);
			$hash		=	IPSMember::generateCompiledPasshash( $salt, $randPass );
			$this->logError($info['id'], 'No password provided. Member has still been converted with password: '. $randPass);
			
			//return false;
		}

		$duplicateMember = IPSMember::load( $info['email'], 'all' );
		if ( $duplicateMember['member_id'] )
		{
			$this->logError($info['id'], "Duplicate member found. {$info['username']} has been merged with the account email {$duplicateMember['username']}");
			
			$this->addLink($duplicateMember['member_id'], $info['id'], 'members');
			$this->DB->update('conv_link', array('duplicate' => '1'), "type = 'members' AND app={$this->app['app_id']} AND foreign_id='{$info['id']}'");

			if ( $info['posts'] > 0 )
			{
				$this->DB->update('members', array('posts' => "posts+'{$info['posts']}'" ), "member_id='{$duplicateMember['member_id']}'");
			}
			
			// we have a customavatar and the one we have on file does not match what we've been given, time to update it.
			if ( $profile['photo_type'] == 'custom' AND $duplicateMember['pp_main_photo'] != $profile['pp_main_photo'] )
			{
				if ( $profile['photo_data'] )
				{
					// open file for writing
					if ( !$handle = fopen( $this->settings['upload_dir'] . '/profile/photo-'.$profile['pp_member_id'].'.png', 'w' ) )
					{
						$this->logError($info['id'], 'Could not write to file.');
					}
						
					// Write image to our opened file.
					if (fwrite($handle, $profile['photo_data']) === FALSE)
					{
						$this->logError($info['id'], 'Could not write to file.');
					}
					// log it all into DB
					$profile['pp_main_photo']	=	'profile/photo-'.$profile['pp_member_id'].'.png';
				}
			}
			
			return TRUE;
		}
		
		//-----------------------------------------
		// Handle Names
		//-----------------------------------------
		
		// Apostrophe is an allowed character but needs converting
		$info['username'] = str_replace( "'", '&#39;', $info['username']);
		$info['username'] = str_replace( "!", '&#33;', $info['username'] ); // as is an excalamation point.
		
		$nameCheck = IPSMember::getFunction()->cleanAndCheckName( $info['username'], array(), 'name' );

		// Check for illegal characters
		if ( $nameCheck['errors']['username'] == ipsRegistry::getClass( 'class_localization' )->words['reg_error_chars'] )
		{
			// Illegal characters exist, clean them out with dashes
			$nameCheckMap['disallowed']		=	array("'", "\"", "&#34;", "<", ">", "\\", "&#92;", "\$", "&#036;", "]", "[", ",", "|");
			$nameCheckMap['replace']		=	array('&#39;', '&#quot;', '&#quot;', '&#lt;', '&#gt;', '-', '-', '-', '-', '-', '-', '-', '-');
			
			$nameCheck['members_display_name'] = str_replace( $nameCheckMap['disallowed'] , $nameCheckMap['replace'], $nameCheck['username'] );

			$this->logError($info['id'], "{$nameCheck['errors']['username']} with name {$info['username']}. Member has still been created but with username as {$nameCheck['username']}");
			
			// Now check for duplicate username.
			try
			{
				if ( IPSMember::getFunction()->checkNameExists( $nameCheck['username'], array(), 'name', true, true ) )
				{
					$t = time();
					$this->logError($info['id'], ipsRegistry::getClass( 'class_localization' )->words['reg_error_username_taken'] . " with name {$nameCheck['username']}. Member has still been created but with username as {$nameCheck['username']}{$t}");
					$nameCheck['username'] = $nameCheck['username'] . $t;
				}
			}
			catch( Exception $e )
			{
				//-----------------------------------------
				// Name exists, let's return appropriately
				//-----------------------------------------
				switch( $e->getMessage() )
				{
					default:
						$this->logError($info['id'], "Unexpected error with name: {$info['username']}. Member was skipped.");
					return false;
				}
			}


		}
		elseif ( $nameCheck['errors']['username'] == 'reg_error_username_taken' )
		{
			$nameCheck['username'] = $nameCheck['username'] . time();
			$this->logError($info['id'], "{$nameCheck['errors']['username']} with name: {$info['username']}. Member has still been created but with username as {$nameCheck['username']}");
		}
		$username = $displayname = $nameCheck['username'];

		// Begin check and clean for display name if provided.
		if ( isset($info['displayname']) )
		{
			// Apostrophe is an allowed character but needs converting
			$info['displayname'] = str_replace( "'", '&#39;', $info['displayname']);
			
			$displayname = NULL;
			$nameCheck = IPSMember::getFunction()->cleanAndCheckName( $info['displayname'], array(), 'members_display_name' );

			if ( $nameCheck['errors']['dname'] == str_replace( '{chars}', ipsRegistry::$settings['username_characters'], ipsRegistry::$settings['username_errormsg'] ) )
			{
				$nameCheckMap['disallowed']		=	array("'", "\"", "&#34;", "<", ">", "\\", "&#92;", "\$", "&#036;", "]", "[", ",", "|");
				$nameCheckMap['replace']		=	array('&#39;', '&#quot;', '&#quot;', '&#lt;', '&#gt;', '-', '-', '-', '-', '-', '-', '-', '-');
				
				$nameCheck['members_display_name'] = str_replace( $nameCheckMap['disallowed'] , $nameCheckMap['replace'], $nameCheck['members_display_name'] );

				$this->logError($info['id'], "{$nameCheck['errors']['dname']} with name: {$info['displayname']}. Member has still been created but with display name as {$nameCheck['members_display_name']}");
				// Now check for duplicate display name.
				try
				{
					if ( IPSMember::getFunction()->checkNameExists( $nameCheck['members_display_name'], array(), 'members_display_name', true, true ) )
					{
						$t = time();
						$this->logError($info['id'], ipsRegistry::getClass( 'class_localization' )->words['reg_error_username_taken'] . " with name {$nameCheck['members_display_name']}. Member has still been created but with display name as {$nameCheck['members_display_name']}{$t}");
						$nameCheck['members_display_name'] = $nameCheck['members_display_name'] . $t;
					}
				}
				catch( Exception $e )
				{
					//-----------------------------------------
					// Name exists, let's return appropriately
					//-----------------------------------------
					switch( $e->getMessage() )
					{
						default:
							$this->logError($info['id'], "Unexpected error with display name: {$info['displayname']}. Member was skipped.");
						return false;
					}
				}


			}
			elseif ( $nameCheck['errors']['dname'] == 'reg_error_username_taken' )
			{
				$nameCheck['members_display_name'] = $nameCheck['members_display_name'] . time();
				$this->logError($info['id'], "{$nameCheck['errors']['dname']} with name: {$info['displayname']}. Member has still been created but with display name as {$nameCheck['members_display_name']}");
			}

			$displayname = $nameCheck['members_display_name'];
		}

		// Check we have a path
		if (!$this->settings['upload_dir'])
		{
			$this->logError($info['id'], 'Your IP.Board uploads path has not been configured');
			return false;
		}

		//-----------------------------------------
		// Insert
		//-----------------------------------------
		$members['title'] 					= str_replace( "'" , '&#39;', $members['title'] );
		//$members['member_id']				= $info['id'];
		$members['name']			   		= $username;
		$members['last_post']				= intval( $members['last_post'] );
		
		if ( empty ( $info['member_group_id'] ) )
		{
			$members['member_group_id']	   		= $info['group'] ? ( $groupLink === TRUE ? $this->getLink( $info['group'], 'groups' ) : $info['group'] ) : $this->settings['member_group'];
		}
		else
		{
			$members['member_group_id']	=	$info['member_group_id'];
		}
		$members['email']			   		= $info['email'];
		$members['joined']			   		= $joined;
		$members['member_login_key']   		= IPSMember::generateAutoLoginKey();
		$members['member_login_key_expire']	= ( ipsRegistry::$settings['login_key_expire'] ) ? ( time() + ( intval( ipsRegistry::$settings['login_key_expire'] ) * 86400 ) ) : 0;
		$members['members_display_name']	= $displayname;
		$members['members_seo_name']		= IPSText::makeSeoTitle( $displayname );
		$members['members_l_display_name']	= IPSText::mbstrtolower($displayname);
		$members['members_l_username']		= IPSText::mbstrtolower($username);
		$members['members_pass_hash']		= $hash;
		$members['members_pass_salt']		= $salt;
		$members['posts']					= ( $members['posts'] ) ? $members['posts'] : 0;

		$members['warn_level'] = (int) $members['warn_level'];

		// Sort out secondary groups
		if( !empty( $info['secondary_groups'] ) )
		{
			// explode so we can loop through for the getLink
			$secondary_groups = explode(",", $info['secondary_groups']);
			
			$_secondary = array();
			
			if( !empty( $secondary_groups ) )
			{
				foreach( $secondary_groups as $group )
				{
					if ( !empty( $group ) )
					{
						$newGroup = $this->getLink( $group, 'groups', true );
						if ( $newGroup )
						{
							$_secondary[] =  $newGroup;
						}
					}
					/**else
					{
						$this->logError($info['id'] .' - '. $group, 'empty secondary group id');
					}**/
				}
			}
			
			$members['mgroup_others'] = implode( ",", $_secondary );
		}

		// Sneaky hack with the comments and friends
		if (!in_array('pp_setting_count_comments', $profile))
		{
			$profile['pp_setting_count_comments'] = 1;
		}
		
		if (!in_array('pp_setting_count_friends', $profile))
		{
			$profile['pp_setting_count_friends'] = 1;
		}

		// We better turn on allow_admin_mails if it isn't set
		$members['allow_admin_mails'] = isset($members['allow_admin_mails']) ? $members['allow_admin_mails'] : 1;

		// Fix up the birthday since STRICT complains..
		$members['bday_day']   = intval($members['bday_day']);
		$members['bday_month'] = intval($members['bday_month']);
		$members['bday_year']  = intval($members['bday_year']);
		
		// No idea why birthdays are messing up.., so I'll just hack this bit. - Alex
		// #020372 tracker
		if ( $members['bday_year'] < 1900 )
		{
			// Don't think we can really be this old ya know.
			$members['bday_day']	= 0;
			$members['bday_month']	= 0;
			$members['bday_year']	= 0;
		}

		// 3.1.3 dropped columns
		unset($members['email_pm']);
		
		// 3.2.0 Dropped columns
		unset ( $members['hide_email'] );
		unset ( $members['view_avs'] );
		
		// 3.3.0 Dropped columns
		unset ( $members['members_editor_choice'] );
		
		// First member?
		if ( $info['id'] != $this->memberData['member_id'] )
		{
			if ( $this->usingExtendedInserts )
			{
				// Add it to the extended insert array which runs on next()
				$this->extendedInserts['members'][] = $this->DB->compileInsertString( $members );
				//$memberId = $members['member_id'];
				$memberId = $info['id'];
			}
			else
			{
				//unset( $members['member_id'] );
				$this->DB->insert( 'members', $members );
				$memberId = $this->DB->getInsertId();
				
				// Add a link
				$this->addLink( $memberId, $info['id'], 'members' );
			}
		}
		else if ( $this->usingExtendedInserts )
		{
			// Unset important information to stop locking us out
			$unset = array( 'member_id', 'members_pass_salt', 'members_pass_hash', 'name', 'members_l_username', 'members_display_name', 'members_l_display_name', 'members_seo_name', 'member_banned', 'conv_password', 'email', 'member_group_id' );
			
			foreach ( $unset as $k )
			{
				unset( $members[$k] );
			}
			
			$this->DB->update('members', $members, "member_id=" . $this->memberData['member_id'] );
			$memberId = $this->memberData['member_id'];
			
			$this->logError($memberId, "<strong><span style='size: 1.15em;'>{$username} has been merged with {$this->memberData['members_display_name']}. This is because you are logged in as {$this->memberData['members_display_name']} and due to both members sharing the same ID. You are not running the conversion in 'merge' mode and therefore you WILL have to manually update this members name, email address, AND member group.</span></strong><br /><ul><li>Username: {$username}</li><li>Email Address: {$info['email']}</li></ul>");
		}
		else
		{
			//unset( $members['member_id'] );
			$this->DB->insert( 'members', $members );
			$memberId = $this->DB->getInsertId();
				
			// Add a link
			$this->addLink( $memberId, $info['id'], 'members' );
		}
		
		// If user group is the auth group, add them to validating table.
		if ( $members['member_group_id']  == $this->settings['auth_group'] && ( $this->settings['reg_auth_type'] == 'user' || $this->settings['reg_auth_type'] == 'admin' || $this->settings['reg_auth_type'] == 'admin_user' ) )
		{
			//-----------------------------------------
			// We want to validate all reg's via email,
			// after email verificiation has taken place,
			// we restore their previous group and remove the validate_key
			//-----------------------------------------
			$validating = array(
								'vid'         => md5( uniqid() ),
								'member_id'   => $memberId,
								'real_group'  => $this->settings['member_group'],
								'temp_group'  => $this->settings['auth_group'],
								'entry_date'  => time(),
								'coppa_user'  => 0,
								'new_reg'     => 1,
								'ip_address'  => $members['ip_address'],
								'spam_flag'	=> 0
							);
			
			if ( $this->usingExtendedInserts )
			{
				$this->extendedInserts['validating'][] = $this->DB->compileInsertString( $validating );
			}
			else
			{
				$this->DB->insert( 'validating', $validating );
			}
		}
	

		$profile['pp_member_id'] 	= $memberId;
		
		// Defaults if not specified (prevents "Column Count Mismatches" in MySQL)
		$profile['pp_thumb_photo']	= $profile['pp_thumb_photo']	? 	$profile['pp_thumb_photo']	: '';
		$profile['pp_main_photo']	= $profile['pp_main_photo']		?	$profile['pp_main_photo']	: '';
		$profile['pp_main_width']	= $profile['pp_main_width']		? 	$profile['pp_main_width']	: 0;
		$profile['pp_main_height']	= $profile['pp_main_height']	? 	$profile['pp_main_height']	: 0;
		$profile['pp_thumb_width']	= $profile['pp_thumb_width']	? 	$profile['pp_thumb_width']	: 0;
		$profile['pp_thumb_height']	= $profile['pp_thumb_height']	? 	$profile['pp_thumb_height']	: 0;
		
		//-----------------------------------------
		// Sort out uploaded avatars / photos
		//-----------------------------------------

		// we send profile_type of custom if we have a photo at all
		if ( $profile['photo_type'] == 'custom' )
		{
			if ( $profile['photo_data'] )
			{
				// open file for writing
				if ( !$handle = fopen( $this->settings['upload_dir'] . '/profile/photo-'.$profile['pp_member_id'].'.png', 'w' ) )
				{
					$this->logError($info['id'], 'Could not write to file.');
				}
					
				// Write image to our opened file.
				if (fwrite($handle, $profile['photo_data']) === FALSE)
				{
					$this->logError($info['id'], 'Could not write to file.');
				}
				// log it all into DB
				$profile['pp_main_photo']	=	'profile/photo-'.$profile['pp_member_id'].'.png';
			}
		}
		
		if ( $profile['photo_type'] == 'url' )
		{
			// Make an attempt at fetching the remote pic. If not, log an error.
			
			$profile['pp_main_photo']	= '';
			
			if ( $remote = @file_get_contents ( $profile['photo_location'] ) )
			{
				$image_dims = @getimagesize( $profile['photo_location'] );
				
				if( $image_dims[0] )
				{
					$profile['photo_data'] 		= $remote;
					$profile['photo_type'] 		= 'custom';
					$profile['pp_main_photo']	= $profile['photo_location'];
					
					if ( !isset ( $profile['photo_filesize'] ) )
					{
						$profile['photo_filesize'] = strlen( $remote );
					}
				}
				else
				{
					$this->logError ( $info['id'], 'Remote picture file does not appear to be an image.' );
				}
			}
			else
			{
				$this->logError ( $info['id'], 'Could not fetch remote picture file.' );
			}
		}
		
		// Oops... I screwed up... workaround for now... will fix properly soon.
		if ( $profile['photo_type'] != 'url' AND $profile['photo_location'] AND !$profile['pp_main_photo'] )
		{
			$profile['pp_main_photo'] = $profile['photo_location'];
		}

		if (!is_dir($pic_path) and $profile['pp_main_photo'] and !$profile['photo_data'])
		{
			$this->logError($info['id'], 'Incorrect profile pictures path');
			//return false;
		}

		// Move em or create em
		if ($profile['pp_main_photo'])
		{
			//-----------------------------------------
			// Already a dir?
			//-----------------------------------------
			$upload_path = $this->settings['upload_dir'];
			$upload_dir;
			if ( ! file_exists( $upload_path . "/profile" ) )
			{
				if ( @mkdir( $upload_path . "/profile", 0777 ) )
				{
					@file_put_contents( $upload_path . '/profile/index.html', '' );
					@chmod( $upload_path . "/profile", 0777 );

					# Set path and dir correct
					$upload_path .= "/profile";
					$upload_dir   = "profile/";
				}
				else
				{
					# Set path and dir correct
					$upload_dir   = "";
				}
			}
			else
			{
				# Set path and dir correct
				$upload_path .= "/profile";
				$upload_dir   = "profile/";
			}
			
			// What's the extension?
			$e = explode('.', $profile['pp_main_photo']);
			$extension = array_pop( $e );
			
			// There's an issue with profile photo thumbnail rebuilds. Waiting on the deal with that issue before adjusting this.
			// For now, we'll just set the thumbnail the same as the main photo.
			$profile['pp_thumb_photo'] = "{$upload_dir}photo-{$memberId}.{$extension}";

			if ($profile['photo_data'])
			{
				//$this->createFile($profile['pp_main_photo'], $profile['photo_data'], $profile['photo_filesize'], $this->settings['upload_dir']);
				$this->createFile("photo-{$memberId}.{$extension}", $profile['photo_data'], $profile['photo_filesize'], $upload_path);
				$profile['pp_main_photo']	= "{$upload_dir}photo-{$memberId}.{$extension}";
			}
			else
			{
				//$this->moveFiles(array($profile['pp_main_photo']), $profile_path, $this->settings['upload_dir']);
				$this->moveFiles(array($profile['pp_main_photo']), $pic_path, $upload_path);
				if ( $upload_dir != '' && @rename($upload_path."/{$profile['pp_main_photo']}", $upload_path."/photo-{$memberId}.{$extension}") )
				{
					$profile['pp_main_photo'] = "{$upload_dir}photo-{$memberId}.{$extension}";
				}
			}
							
			// Try and get width and height.
			$dimensions = @getimagesize( $upload_dir . 'photo-' . $memberId . '.' . $extension );
			
			// Add some triple checks.
			$profile['pp_main_width']	= $dimensions[0] ? $dimensions[0] : 1;
			$profile['pp_main_height']	= $dimensions[1] ? $dimensions[1] : 1;
			$profile['pp_thumb_width']	= $dimensions[0] ? $dimensions[0] : 1;
			$profile['pp_thumb_height']	= $dimensions[1] ? $dimensions[1] : 1;
		}

		$profile['pp_photo_type'] = $profile['photo_type'];
		
		unset( $profile['avatar_data'] );
		unset( $profile['photo_data'] );
		unset( $profile['photo_filesize'] );
		unset( $profile['avatar_filesize'] );
		unset( $profile['photo_type'] );
		unset( $profile['photo_location'] );
		unset( $profile['notes'] );
		
		// need to stop fields which have been added by hooks getting through. See ticket 854980 as to why this is needed.
		if ( is_array($profile) )
		{
			// set the fields we're allowed (I can't think of a better way of populating this array unfortunately)
			$allowedFields = array('pp_member_id','pp_last_visitors','pp_rating_hits','pp_rating_value','pp_rating_real','pp_main_photo','pp_main_width','pp_main_height','pp_thumb_photo','pp_thumb_width','pp_thumb_height','pp_setting_moderate_comments','pp_setting_moderate_friends','pp_setting_count_friends','pp_setting_count_comments','pp_setting_count_visitors','pp_about_me','pp_reputation_points','pp_gravatar','pp_photo_type','signature','avatar_location','avatar_size','avatar_type','pconversation_filters','fb_photo','fb_photo_thumb','fb_bwoptions','tc_last_sid_import','tc_photo','tc_bwoptions','pp_customization','pp_profile_update');
			
			foreach ( $profile as $k => $v )
			{
				// not in allowed array? unset. (this stops fields from hooks making it through)
				if ( !in_array( $k, $allowedFields ) )
				{
					unset($profile[$k]);
				}
			}
		}
		
		// check if we passed in custom..
		$profileFields = array();
		
		$profileFields['member_id'] = $memberId;
		
		if ( ! empty( $custom ) )
		{
			$profileFields = array_merge( $profileFields, $custom );
		}

		// First member?
		if ( $info['id'] != $this->memberData['member_id'] )
		{
			if ( $this->usingExtendedInserts )
			{
				// Add it to the extended insert array which runs on next()
				$this->extendedInserts['profile_portal'][] = $this->DB->compileInsertString( $profile );

				// Custom profile fields
				$this->extendedInserts['pfields_content'][] = $this->DB->compileInsertString( $profileFields );
			}
			else
			{
				$this->DB->insert( 'profile_portal', $profile );
				$this->DB->insert( 'pfields_content', $profileFields );
			}
		}
		else if ( $this->usingExtendedInserts )
		{
			unset( $profileFields['member_id'] );
			unset( $profile['pp_member_id'] );
			$this->DB->update('profile_portal', $profile, "pp_member_id=" . $this->memberData['member_id'] );
			$this->DB->update('pfields_content', $profileFields, "member_id=" . $this->memberData['member_id'] );
		}
		else
		{
			$this->DB->insert( 'profile_portal', $profile );
			$this->DB->insert( 'pfields_content', $profileFields );
		}
		
		// Conversion cycle complete
		return true;
	}

	/**
	 * Convert profile ratings
	 *
	 * @access	public
	 * @param	string		Type
	 * @param 	array 		Data to insert to rc_reports_index table
	 * @param 	array 		Data to insert to rc_reports table
	 * @param 	boolean		If we're linking rc_status table
	 * @param 	array 		Data to insert to rc_comments table
	 * @return 	boolean		Success or fail
	 **/
	public function convertReport($type, $report, $reports, $status=true, $comments=array())
	{

		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------

		if (!$report['id'])
		{
			$this->logError($id, '(REPORT) No id provided');
			return false;
		}

		//-----------------------------------------
		// Link
		//-----------------------------------------

		$report['updated_by'] = ($report['updated_by']) ? $this->getLink($report['updated_by'], 'members', false, $this->useLocalLink) : 0;
		$report = $this->processReportLinks($type, $report);

		/* No go? Skip */
		if ( $report === FALSE )
		{
			$this->logError($report['id'], 'Report was referring to something deleted');
			return false;
		}

		if ($status)
		{
			$report['status'] = $this->getLink($report['status'], 'rc_status');
		}

		//-----------------------------------------
		// Insert
		//-----------------------------------------

		$rid = $report['id'];
		
		if ( $this->usingExtendedInserts )
		{
			$this->extendedInserts['rc_reports_index'][] = $this->DB->compileInsertString( $report );
			$inserted_id = $rid;
		}
		else
		{
			unset($report['id']);
			$this->DB->insert('rc_reports_index', $report);
			$inserted_id = $this->DB->getInsertId();
		}

		$this->addLink($inserted_id, $rid, 'rc_reports_index_'.$type);
		
		//-----------------------------------------
		// Process rc_reports
		//-----------------------------------------

		foreach ($reports as $r)
		{
			$r['rid'] = $inserted_id;
			$r['report_by'] = ($r['report_by']) ? $this->getLink($r['report_by'], 'members', false, $this->useLocalLink) : 0;
			
			if ( $this->usingExtendedInserts )
			{
				$this->extendedInserts['rc_reports'][] = $this->DB->compileInsertString( $r );
				$_inserted_id = $r['id'];
			}
			else
			{
				unset($r['id']);
				$this->DB->insert('rc_reports', $r);
				$_inserted_id = $this->DB->getInsertId();
			}
				
			$this->addLink($_inserted_id, $r['id'], 'rc_reports_'.$type);
		}

		//-----------------------------------------
		// Process rc_comments
		//-----------------------------------------

		foreach ($comments as $c)
		{
			$c['rid'] = $inserted_id;
			$c['comment_by'] = $this->getLink($c['comment_by'], 'members', false, $this->useLocalLink);
			unset($c['id']);
			$this->DB->insert( 'rc_comments', $c );
			$__inserted_id = $this->DB->getInsertId();
			$this->addLink($__inserted_id, $c['id'], 'rc_comments_'.$type);
		}

		return true;
	}

	public function convertDeletionLog( $type, $id, $memberId, $logDate=NULL, $reason=NULL, $locked=FALSE )
	{
		$localMemberId = $this->getLink($memberId, 'members', TRUE);
		$localTypeId = $this->getLink($id, $type, TRUE);

		if ( $localMemberId == FALSE || $localTypeId == FALSE )
		{
			if ( $localMemberId == FALSE )
			{
				$this->logError($id, "Invalid member id: {$memberId}");
			}

			if ( $localTypeId == FALSE )
			{
				$this->logError($id, "Invalid type {{$type}} with id: {$id}");
			}

			return FALSE;
		}


		ipsRegistry::DB()->replace( 'core_soft_delete_log', array( 'sdl_obj_id'        => $localTypeId,
																   'sdl_obj_key'       => $type,
																   'sdl_obj_reason'    => $reason,
																   'sdl_obj_member_id' => $localMemberId,
																   'sdl_obj_date'      => $logDate == NULL ? time() : $logDate,
																   'sdl_locked'		   => (int)$locked ), array( 'sdl_obj_id', 'sdl_obj_key' ) );
		$lastRow = ipsRegistry::DB()->buildAndFetch( array( 'select' => 'sdl_id', 'from' => 'core_soft_delete_log', 'where' => "sdl_obj_key='{$type}' AND sdl_obj_id='{$localTypeId}'" ) );
		//-----------------------------------------
		// Add link
		//-----------------------------------------
		$this->addLink($lastRow['sdl_id'], $type.'_'.$id, 'core_soft_delete_log');

		return TRUE;
	}

	/**
	 * Convert an attachment
	 *
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @param 	string 		Path to avatars folder
	 * @param	boolean		If true, loads file data from database, rather than move file
	 * @param	boolean		If true, loads member data from parent application
	 * @return 	boolean		Success or fail
	 **/
	public function convertAttachment($id, $info, $path, $db=false, $useparent=false)
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------
		
		// Check GD
		if ( !function_exists( gd_info ) )
		{
			$this->error('GD is not installed. Please install GD before converting attachments');
		}
		
		if (!$id)
		{
			$this->logError($id, 'No ID number provided');
			return false;
		}
		if (!$info['attach_rel_id'])
		{
			$this->logError($id, 'No relative ID provided');
			return false;
		}
		if (!$db and !$path)
		{
			$this->logError($id, 'No path provided');
			return false;
		}
		if (!$info['attach_file'])
		{
			$this->logError($id, 'No filename provided');
			return false;
		}
		if (!$db and !$info['attach_location'])
		{
			$this->logError($id, 'No location provided');
			return false;
		}
		if ($db and !$info['data'])
		{
			$this->logError($id, 'No file data provided');
			return false;
		}

		//-----------------------------------------
		// Check upload dir
		//-----------------------------------------
		$pathCheck = $this->_checkUploadDirectory( ( $info['attach_date'] ? $info['attach_date'] : 0 ) );
		$upload_dir = $upload_path = NULL;
		if ( $pathCheck['upload_path'] == NULL )
		{
			$this->logError($id, 'Your IP.Board uploads path has not been configured. Message: ' . $pathCheck['error']);
			return false;
		}
		else
		{
			$upload_dir = $pathCheck['upload_dir'];
			$upload_path = $pathCheck['upload_path'];
		}

		//-----------------------------------------
		// Got attachment types?
		//-----------------------------------------
		if ( ! ( $this->registry->cache()->getCache('attachtypes') ) OR ! is_array( $this->registry->cache()->getCache('attachtypes') ) )
		{
			$attachtypes = array();

			$this->DB->build( array( 'select' => '*',
									 'from'   => 'attachments_type',
									 'where'  => "atype_post=1" ) );
			$this->DB->execute();
			while ( $r = $this->DB->fetch() )
			{
				$attachtypes[ $r['atype_extension'] ] = $r;
			}

			$this->registry->cache()->updateCacheWithoutSaving( 'attachtypes', $attachtypes );
		}

		//-----------------------------------------
		// Set up array
		//-----------------------------------------
		$attach_data = array( 'attach_ext'            => "",
							  'attach_file'           => $info['attach_file'],
							  'attach_location'       => "",
							  'attach_thumb_location' => "",
							  'attach_hits'           => $info['attach_hits'],
							  'attach_date'           => $info['attach_date'],
							  'attach_temp'           => 0,
							  'attach_member_id'      => $this->memberData['member_id'],
							  'attach_rel_id'         => 0,
							  'attach_rel_module'     => $info['attach_rel_module'],
							  'attach_filesize'       => 0,
							  'attach_parent_id'	  => $info['attach_parent_id'],
							  'attach_img_width'		=> 0,
							  'attach_img_height'		=> 0,
							  'attach_thumb_width'		=> 0,
							  'attach_thumb_height'		=> 0,
							  'attach_post_key'			=> ( ! $info['attach_post_key'] ? md5( $info['attach_rel_id'] . $info['attach_rel_module'] ) : $info['attach_post_key'] )
							 );
		
		//-----------------------------------------
		// Populate allowed extensions
		//-----------------------------------------
		$allowed_file_ext = array();
		if ( is_array( $this->registry->cache()->getCache('attachtypes') ) and count( $this->registry->cache()->getCache('attachtypes') ) )
		{
			/* SKINNOTE: I had to add [attachtypes] to this cache to make it work, may need fixing? */
			//$tmp = $this->registry->cache()->getCache('attachtypes');
			foreach( $this->registry->cache()->getCache('attachtypes') as $idx => $data )
			{
				if ( $data['atype_post'] )
				{
					$allowed_file_ext[] = $data['atype_extension'];
				}
			}
		}

		//-------------------------------------------------
		// Do we have allowed file_extensions?
		//-------------------------------------------------
		$attach_data['attach_ext'] = $this->_getFileExtension($info['attach_file']);
		if ( !in_array( $attach_data['attach_ext'], $allowed_file_ext ) )
		{
			$this->logError($id, "invalid_mime_type for file name: {$info['attach_file']}" );
			return false;
		}


		$attach_file = str_replace( '.' . $attach_data['attach_ext'], "", $info['attach_file'] );
		$isImage = in_array( $attach_data['attach_ext'], array( 'gif', 'jpeg', 'jpg', 'jpe', 'png' ) );
		$attach_data['attach_member_id'] = ($info['attach_member_id']) ? $this->getLink($info['attach_member_id'], 'members', true, $useparent) : 0;

		$attach_location = $info['attach_rel_module'] . '-' . $info['attach_member_id'] . '-' .str_replace( '.', '', microtime(TRUE) ) . '.' . ($isImage ? $attach_data['attach_ext'] : 'ipb');

		// Create the file from the db if that's the case
		if ($db)
		{
			$this->createFile($attach_location, $info['data'], $info['attach_filesize'], $pathCheck['upload_path']);
		}
		else
		{
			// Check the file actually exists
			if (!file_exists($path.'/'.$info['attach_location']))
			{
				$this->logError($id, 'Could not locate file '.$path.'/'.$info['attach_location']);
				return false;
			}

			// Now move it!
			if(!@copy( $path.'/'.$info['attach_location'], $pathCheck['upload_path'].'/'.$attach_location))
			{
				$e = error_get_last();
				$this->logError($id, 'Could not move file - attempted to move '.$path.'/'.$info['attach_location'].' to '.$pathCheck['upload_path'].'/'.$attach_location.'<br />'.$e['message'].'<br /><br />');
				return false;
			}
			@chmod( $pathCheck['upload_path'].'/'.$attach_location, 0777 );
		}

		$attach_data['attach_filesize'] = @filesize( $pathCheck['upload_path'].'/'.$attach_location );

		if ( $isImage )
		{
			require_once( IPS_KERNEL_PATH."classImage.php" );
			require_once( IPS_KERNEL_PATH."classImageGd.php" );
			$image = new classImageGd();

			$image->force_resize = true;

			$image->init( array( 'image_path' => $pathCheck['upload_path'],
								 'image_file' => $attach_location ) );

			if ( $this->settings['siu_thumb'] && $this->settings['siu_width'] && $this->settings['siu_height'] )
			{
				$_thumbName = preg_replace( "#^(.*)\.(\w+?)$#", "\\1_thumb.\\2", $attach_location );

				if( $thumb_data = $image->resizeImage( $this->settings['siu_width'], $this->settings['siu_height'] ) )
				{
					$image->writeImage( $pathCheck['upload_path'] . '/' . $_thumbName );

					if ( is_array( $thumb_data ) )
					{
						$thumb_data['thumb_location'] = $pathCheck['upload_dir'] . $_thumbName;
					}
				}
			}

			if ( $thumb_data['thumb_location'] )
			{
				$attach_data['attach_thumb_width']    = $thumb_data['newWidth'];
				$attach_data['attach_thumb_height']   = $thumb_data['newHeight'];
				$attach_data['attach_thumb_location'] = $thumb_data['thumb_location'];
			}
			$dimensions = getimagesize( $pathCheck['upload_path'] . '/' . $attach_location );
			$attach_data['attach_img_width'] = $dimensions[0];
			$attach_data['attach_img_height'] = $dimensions[1];
		}

		//-----------------------------------------
		// Insert
		//-----------------------------------------
		// Handle links
		switch ($info['attach_rel_module'])
		{
			case 'post':
				$attach_data['attach_rel_id'] = $this->getLink($info['attach_rel_id'], 'posts', true);
				$attach_data['attach_parent_id'] = $this->getLink($info['attach_parent_id'], 'topics', true);
				
				$post = $this->DB->buildAndFetch( array( 'select' => 'post_key', 'from' => 'posts', 'where' => "pid='{$attach_data['attach_rel_id']}'" ) );
				$attach_data['attach_post_key']	= $post['post_key'];
				break;

			case 'msg':
				$attach_data['attach_rel_id'] = $this->getLink($info['attach_rel_id'], 'pm_posts', true);
				$attach_data['attach_parent_id'] = $this->getLink($info['attach_parent_id'], 'pms', true);
				break;

			case 'blogentry':
				$attach_data['attach_rel_id'] = $this->getLink($info['attach_rel_id'], 'blog_entries', true);
				break;

			case 'tracker':
				$attach_data['attach_rel_id'] = $this->getLink($info['attach_rel_id'], 'tracker_posts', true);
				break;

			case 'ccs':
				$attach_data['attach_rel_id'] = $this->getLink($info['attach_rel_id'], 'ccs_articles', true);
				break;

			default:
				$this->logError($id, 'Invalid Module');
				return false;
				break;
		}

		if ( $attach_data['attach_rel_id'] == 0)
		{
			$this->logError($id, $info['attach_rel_module'] . ' resource not found.');
			return false;
		}
		
		// STRICT likes to complain about everything...
		$attach_data['attach_is_image'] = intval($isImage);
		$attach_data['attach_location'] = $pathCheck['upload_dir'] . $attach_location;

		// Dropped fields
		unset($attach_data['attach_temp']);
		unset($attach_data['attach_approved']);
		
		if ( $this->usingExtendedInserts )
		{
			$attach_data['attach_id'] = $id;
			$this->extendedInserts['attachments'][] = $this->DB->compileInsertString( $attach_data );
			$inserted_id = $id;
		}
		else
		{
			$this->DB->insert('attachments', $attach_data);
			$inserted_id = $this->DB->getInsertId();
		}
		
		// Update module attachment count cache
		switch ($info['attach_rel_module'])
		{
			case 'post':
				$this->DB->update( 'topics', array( 'topic_hasattach' => 1 ), "tid='{$attach_data['attach_parent_id']}'" );
				break;

			case 'msg':
				$this->DB->update( 'message_topics', array( 'mt_hasattach' => 1 ), "mt_id='{$attach_data['attach_parent_id']}'" );
				break;
		}

		$this->addLink( $inserted_id, $id, 'attachments' );
		return true;
	}

	public function processAttachment( $fileName, $fullPath, $processFileSize, $appType='post' )
	{
		//-----------------------------------------
		// Should do a full attachment initialization and then
		// move to convertAttachment which saves the DB record.
		//-----------------------------------------

		//-----------------------------------------
		// Load attachment types, if they are not loaded
		//-----------------------------------------
		if ( !$this->registry->cache()->getCache('attachtypes') || !is_array($this->registry->cache()->getCache('attachtypes')) )
		{
			$attachtypes = array();

			$this->DB->build( array( 'select' => 'atype_extension,atype_mimetype,atype_post,atype_photo,atype_img',
									 'from'   => 'attachments_type',
									 'where'  => "atype_photo=1 OR atype_post=1" ) );
			$attachRes = $this->DB->execute();

			while ( $r = $this->DB->fetch($attachRes) )
			{
				$attachtypes[ $r['atype_extension'] ] = $r;
			}

			$this->registry->cache()->updateCacheWithoutSaving( 'attachtypes', $attachtypes );
		}


	}

	/**
	 * Convert a group
	 *
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @return 	boolean		Success or fail
	 **/
	public function convertGroup($id, $info)
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------

		if (!$id)
		{
			$this->logError($id, 'No ID number provided');
			return false;
		}

		//-----------------------------------------
		// Just merging?
		//-----------------------------------------

		$us = unserialize($this->settings['conv_extra']);
		$extra = $us[$this->app['name']];
		if ($extra['groups'][$id] != 'x')
		{
			$this->addLink($extra['groups'][$id], $id, 'groups', 1);
		}

		//-----------------------------------------
		// Or creating one?
		//-----------------------------------------

		else
		{
			if (!$info['g_title'])
			{
				$this->logError($id, 'No group name provided');
				return false;
			}

			$info['g_title'] = str_replace( "'" , '&#39;', $info['g_title'] );

			//-----------------------------------------
			// Handle Duplicates
			//-----------------------------------------

			while($this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'groups', 'where' => "g_title='{$info['g_title']}'" ) ))
			{
				$info['g_title'] .= '_converted';
			}

			//-----------------------------------------
			// Insert
			//-----------------------------------------

			// Sort out permission sets
			foreach (explode(',', $info['g_perm_id']) as $perm)
			{
				$perms[] = $this->getLink($perm, 'forum_perms');
			}
			$info['g_perm_id'] = implode(',', $perms);

			// Fix stuff up
			$info['g_title']		= substr($info['g_title'], 0, 32);
			$info['g_max_mass_pm']	= intval( $info['g_max_mass_pm'] );
			$info['g_edit_profile']	= intval( $info['g_edit_profile'] );

			// And go!
			unset($info['g_id']);
			unset($info['g_invite_friend']);
			
			// 3.2.1 dropped columns (Tracker: 31613)
			unset ( $info['g_email_friend'] );
			unset ( $info['g_email_limit'] );
			unset ( $info['g_avatar_upload'] );
			
			$this->DB->insert( 'groups', $info );
			$inserted_id = $this->DB->getInsertId();

			//-----------------------------------------
			// Add link
			//-----------------------------------------

			$this->addLink($inserted_id, $id, 'groups');
		}

		return true;

	}

	/**
	 * Convert a permission set
	 *
	 * @access	public
	 * @param 	integer		Foreign ID number
	 * @param 	array 		Data to insert to table
	 * @return 	boolean		Success or fail
	 **/
	public function convertPermSet($id, $name)
	{
		//-----------------------------------------
		// Make sure we have everything we need
		//-----------------------------------------

		if (!$id)
		{
			$this->logError($id, 'No ID number provided');
			return false;
		}
		if (!$name)
		{
			$this->logError($id, 'No name provided');
			return false;
		}

		//-----------------------------------------
		// Just merging?
		//-----------------------------------------

		$us = unserialize($this->settings['conv_extra']);
		$extra = $us[$this->app['name']];

		if (!is_array($extra['forum_perms']) or !$extra['forum_perms'][$id])
		{
			$this->logError($id, 'Could not locate matching local set.');
			return false;
		}

		if ($extra['forum_perms'][$id] != 'x')
		{
			$this->addLink($extra['forum_perms'][$id], $id, 'forum_perms', 1);
		}

		//-----------------------------------------
		// Or creating one?
		//-----------------------------------------

		else
		{
			$this->DB->insert( 'forum_perms', array('perm_name' => str_replace( "'" , '&#39;', $name )) );
			$inserted_id = $this->DB->getInsertId();
			$this->addLink($inserted_id, $id, 'forum_perms');
		}

		return true;
	}
	
	/**
	 * Convert Follow
	 *
	 * @access	private
	 * @return	void
	 */
	public function convertFollow ( $info )
	{
		if ( !$info['like_app'] )
		{
			$this->logError ( $info['like_id'], 'No Application Provided.' );
			return false;
		}
		
		if ( !$info['like_area'] )
		{
			$this->logError ( $info['like_id'], 'No Area provided.' );
			return false;
		}
		
		if ( !$info['like_rel_id'] )
		{
			$this->logError ( $info['like_id'], 'No Relative ID provided.' );
			return false;
		}
		
		if ( !$info['like_member_id'] )
		{
			// Seems to always error, so commented it out for now as its scaring people.
			//$this->logError( $info['like_id'] . ': No Member ID provided, possibly deleted.' );
			return false;
		}
		
		// Set up like class.
		require_once( IPS_ROOT_PATH . '/sources/classes/like/composite.php' );
		$_like = classes_like::bootstrap( $info['like_app'], $info['like_area'] );
		
		$_like->add( $info['like_rel_id'], $info['like_member_id'], array(
			'like_notify_do'	=> 1,
			'like_notify_meta'	=> $info['like_notify_meta'],
			'like_notify_freq'	=> $info['like_notify_freq']
		) );
				
		return true;
	}

	/**
	 * Display Menu
	 *
	 * @access	private
	 * @return	void
	*/
	public function menu($special=array())
	{
		IPSLib::updateSettings(array('conv_error' => ''));
		parent::menu($special);
	}
	
	public function recacheAndLog( $action, $message=null )
	{
		if ( $action )
		{
			// log our message
			$this->logMessage( $action, $message );
			// recache our stuff.
			switch ( $action )
			{
				case 'attachments':
					$this->cache->rebuildCache( 'attachtypes', 'forums' );
					break;
				
				case 'attachments_type':
					break;
					
				case 'announcements':
					break;
					
				case 'custom_bbcode':
					break;
					
				case 'emoticons':
					break;
					
				case 'forums':
					break;
					
				case 'forum_perms':
					break;
					
				case 'groups':
					break;
					
				case 'ignored_users':
					break;
					
				case 'members':
					break;
					
				case 'moderators':
					break;
					
				case 'posts':
					IPSContentCache::truncate( $type );
					break;
					
				case 'polls':
					break;
							
				case 'pms':
					break;
					
				case 'profile_friends':
					break;
				
				case 'profile_comments':
					break;
				
				case 'pfields':
					break;
					
				case 'tags':
					break;
								
				case 'topics':
					IPSContentCache::truncate( $type );
					break;

				case 'topic_ratings':
					break;
					
				case 'reputation_index':
					break;

				case 'ranks':
					break;
					
				case 'warn_logs':
					break;
					
				case 'finalize':
					// recache all
					break;
					
				default:
					break;
			}
		}
	}


	/**
	 * Information box to display on convert screen
	 *
	 * @abstract
	 * @access	public
	 * @return	string 		html to display
	 */
	public abstract function getInfo();

	/**
	 * Return the information needed for a specific action
	 *
	 * @abstract
	 * @access	public
	 * @param 	string		action (e.g. 'members', 'forums', etc.)
	 * @return 	array 		info needed for html->convertMenuRow
	 **/
	public abstract function menuRow($action='', $return=false);

	/**
	 * Return the tables that need to be truncated for a given action
	 *
	 * @abstract
	 * @access	public
	 * @param 	string		action (e.g. 'members', 'forums', etc.)
	 * @return 	array 		array('table' => 'id_field', ...)
	 **/
	public abstract function truncate($action);

	/**
	 * Database changes
	 *
	 * @abstract
	 * @access	public
	 * @param 	string		action (e.g. 'members', 'forums', etc.)
	 * @return 	array 		Details of change - array('type' => array(info))
	 **/
	public abstract function databaseChanges($action);

	/**
	 * Process report links
	 *
	 * @access	protected
	 * @param 	string		type (e.g. 'post', 'pm')
	 * @param 	array 		Data for reports_index table with foreign IDs
	 * @return 	array 		Processed data for reports_index table
	 **/
	protected abstract function processReportLinks($type, $report);

	/**
	 * Show Error Message
	 *
	 * @access	private
	 * @param	string		Error message
	 * @return	void
	*/
	public function error($message)
	{
		parent::sendError( $message );
	}

	/**
	 * Checks the upload dir. See above. It's not rocket science
	 *
	 * @access	public
	 * @return	bool
	 */
	private function _checkUploadDirectory( $date=0 )
	{
		$uploadPath = $this->settings['upload_dir'];
		$uploadDir = NULL;
		$error = NULL;

		/* Check dir exists... */
		if( ! file_exists( $uploadPath ) )
		{
			if( @mkdir( $uploadPath, 0777 ) )
			{
				@file_put_contents( $uploadPath . '/index.html', '' );
				@chmod( $uploadPath, 0777 );
			}
			else
			{
				return array( 'upload_path' => NULL, 'upload_dir' => NULL, 'error' => 'no_upload_dir' );
			}
		}
		else if( ! is_writeable( $uploadPath ) )
		{
			return array( 'upload_path' => NULL, 'upload_dir' => NULL, 'error' => 'no_upload_dir_perms' );
		}

		/* Try and create a new monthly dir */
		$this_month = "monthly_" . gmstrftime( "%m_%Y", ( $date != 0 ? $date : time() ) );

		/* Already a dir? */
		if( (@ini_get("safe_mode") ? 0 : ( $this->settings['safe_mode_skins'] ? 0 : 1) ) )
		{
			$path = $uploadPath . '/' . $this_month;

			if( ! file_exists( $path ) )
			{
				if( @mkdir( $path, 0777 ) )
				{
					@file_put_contents( $path . '/index.html', '' );
					@chmod( $path, 0777 );

					# Set path and dir correct
					$uploadPath .= '/' . $this_month;
					$uploadDir   = $this_month . '/';
				}

				/* Was it really made or was it lying? */
				if( ! file_exists( $path ) )
				{
					$uploadPath = $this->_upload_path;
					$uploadDir  = '/';
				}
			}
			else
			{
				/* Set path and dir correct */
				$uploadPath .= '/' . $this_month;
				$uploadDir   = $this_month . '/';
			}
		}

		return array( 'upload_path' => $uploadPath, 'upload_dir' => $uploadDir );
	}

	/**
	* Returns the file extension of the current filename
	*
	* @access	public
	* @param	string		Filename
	* @return	string		File extension
	*/
	private function _getFileExtension($file)
	{
		return IPSText::mbstrtolower( str_replace( ".", "", substr( $file, strrpos( $file, '.' ) ) ) );
	}

	static public function myStrToTime($value) { return is_object($value) ? strtotime(DATE_FORMAT($value, DATE_ATOM)) : intval(strtotime((string)($value)));}

	public function useKey( $key )
	{
		$this->usingKeys = TRUE;
		$this->key = $key;
	}

	public function setLastKeyValue( $keyValue )
	{
		$this->end += 1;
		$this->lastKey = $keyValue;
	}
	
	public function runQueries()
	{
		$doneColumns = array();
		
		foreach( $this->getExtendedInserts() as $table => $data )
		{
			$insert	 = array();

			foreach( $data as $k => $v )
			{
				$columns = $v['FIELD_NAMES'];
				
				if ( ! $doneColumns[$table] )
				{
					$sql = "INSERT INTO " . $this->DB->obj['sql_tbl_prefix'] . $table . " (" . $columns .") VALUES ";
					$doneColumns[$table] = true;
				}
			
				$insert[] = "(" . $v['FIELD_VALUES'] . ")";
			}
			
			if ( is_array( $insert ) && count( $insert ) )
			{
				$sql .= implode( ",\n", $insert );
				$sql .= ";";
			
				$this->DB->query($sql);
				$sql = "";
			}
		}
	}

	public function next()
	{
		$this->runQueries();
		
		if ( $this->usingKeys !== TRUE )
		{
			parent::next();
		}

		$total = $this->request['total'];
		$pc = round((100 / $total) * $this->end);
		$message = ($pc > 100) ? 'Finishing...' : "{$pc}% complete";
		
		// super hacky but I don't want to just ditch db logging altogether so let's just stop it for strict peoples.
		$isStrict = $this->DB->query( 'SELECT @@sql_mode' );

		if ( empty( $isStrict ) )
		{
			IPSLib::updateSettings( array('conv_error' => serialize( $this->errors ) ) );
		}

		$end = ($this->end > $total) ? $total : $this->end;
		//print "{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$this->request['do']}&lastKey=" . urlencode($this->lastKey) . "&count={$this->end}&cycle={$this->request['cycle']}&total={$total}<br />{$end} of {$total} converted<br />{$message}";exit;
		$this->registry->output->html .= $this->registry->output->global_template->temporaryRedirect("{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$this->request['do']}&lastKey=" . urlencode($this->lastKey) . "&count={$this->end}&cycle={$this->request['cycle']}&total={$total}&empty={$this->request['empty']}", "<strong>{$end} of {$total} converted</strong><br />{$message}<br /><br /><strong><a href='{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$this->request['do']}&st={$this->end}&cycle={$this->request['cycle']}&total={$total}'>Click here if you are not redirected.</a></strong>");
		$this->sendOutput ( );
	}
	
	// because Mikey says so.
	public function xmpDebug( $data=array(), $exit=true )
	{
		echo '<xmp>';
		print_r($data);
		echo '</xmp>';
		if ( $exit == true )
		{
			exit;
		}
	}
	
}
