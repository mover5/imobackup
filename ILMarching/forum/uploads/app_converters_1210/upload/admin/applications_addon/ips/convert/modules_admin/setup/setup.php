<?php
/**
 * IPS Converters
 * Application Files
 * Sets up a conversion
 * Last Update: $Date: 2013-07-29 09:55:09 -0400 (Mon, 29 Jul 2013) $
 * Last Updated By: $Author: AndyMillne $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	© 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 899 $
 */


if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class admin_convert_setup_setup extends ipsCommand
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
		if ( file_exists( DOC_IPS_ROOT_PATH . 'cache/converter_lock.php' ) )
		{
			ipsRegistry::getClass('output')->showError( 'The converters have been locked. To unlock, delete the cache/converter_lock.php file.' );
		}
		
		if ( ! $this->DB->checkForField( 'app_merge', 'conv_apps' ) )
		{
			$this->DB->addField( 'conv_apps', 'app_merge', 'TINYINT(1)', 1 );
		}

		$this->html = $this->registry->output->loadTemplate( 'cp_skin_convert' );

		switch ($this->request['do'])
		{
			case 'save':
				$this->select_old();
				break;

			case 'info':
				$this->get_more_info();
				break;

			case 'convert':
				$this->finish();
				break;

			default:
				$this->show_apps();
				break;
		}

		$this->registry->output->html .= $this->html->convertFooter();
		$this->registry->output->html_main .= $this->registry->output->global_template->global_frame_wrapper();
		$this->registry->output->sendOutput();
		exit;
	}

	/**
    * Display the list of apps (board, gallery, etc)
    *
    * @access	private
    * @return	void
    */
	private function show_apps()
	{
		foreach( ipsRegistry::$applications as $app_dir => $application )
		{
			if ( $app_dir == 'core' )
			{
				$app_dir = 'board';
			}
						
			if ( isset( _interface::$software[ $app_dir ] ) )
			{
				_interface::$software[$app_dir]['enabled'] = true;
				_interface::$software[$app_dir]['software'] = array();
				
				// Get options
				foreach ( glob( IPSLib::getAppDir('convert') . '/modules_admin/' . $app_dir . '/*.php' ) as $file )
				{
					require_once $file;
					_interface::$software[$app_dir]['software'][$info['key']] = $info['name'];
				}
			}
			
			// Else is it third party?
			// do something to facilitate /extensions/convert/ here.
			else if ( ( $application['app_location'] == 'other' && is_dir( IPSLib::getAppDir('convert') . '/modules_admin/' . $application['app_directory'] ) ) OR ( $application['app_location'] == 'other' && is_dir( IPSLib::getAppDir( IPSText::mbstrtolower( $application['app_title'] ) ) . '/extensions/convert/' . $application['app_directory'] ) ) )
			{
				_interface::$software[$app_dir] = array( 'title' => $application['app_title'], 'enabled' => true, 'third_party' => true );
			}
		}

		$this->registry->output->html .= $this->html->convertShowSoftware(_interface::$software);
	}
	

	/**
    * Display the choices for converting from
    *
    * @access	private
    * @return	void
    */
	private function select_old()
	{
		switch ($this->request['sw'])
		{
			case 'board':
				$name = 'IP.Board';
				break;

			case 'calendar':
				$name = 'IP.Calendar';
				break;

			/**
			 *  case 'subscriptions':
				$name = 'IP.Subscriptions';
				break;
				**/
			case 'nexus':
				$name = 'IP.Nexus';
				break;

			case 'blog':
				$name = 'IP.Blog';
				break;

			case 'gallery':
				$name = 'IP.Gallery';
				break;

			case 'downloads':
				$name = 'IP.Downloads';
				break;

			/**
			 *  case 'tracker':
				$name = 'IP.Tracker';
				break;
			**/
			case 'ccs':
				$name = 'IP.Content';
				break;

			default:
				$this->registry->output->html .= $this->html->convertError('Invalid application. Reset system.');
				$this->sendOutput();
				exit;
		}
		$this->registry->output->html .= $this->html->convertHeader($name.' Conversion Set Up');

		$options = array();
		foreach (glob(IPS_ROOT_PATH.'applications_addon/ips/convert/modules_admin/'.$this->request['sw'].'/*.php') as $file)
		{
			require_once $file;
			$options[] = $this->html->convertAddOption($info);
		}

		$this->registry->output->html .= $this->html->convertShowOptions1(implode('', $options));
	}

	/**
    * Ask for more information
    *
    * @access	private
    * @return	void
    */
	private function get_more_info()
	{
		require_once IPS_ROOT_PATH.'applications_addon/ips/convert/modules_admin/'.$this->request['sw'].'/'.$this->request['choice'].'.php';
		
		// Defaults for form
		$this->request['hb_sql_host'] = $this->request['hb_sql_host'] ? $this->request['hb_sql_host'] : $this->settings['sql_host'];
		$this->request['hb_sql_user'] = $this->request['hb_sql_user'] ? $this->request['hb_sql_user'] : $this->settings['sql_user'];
		$this->request['hb_sql_pass'] = $this->request['hb_sql_pass'] ? $this->request['hb_sql_pass'] : $this->settings['sql_pass'];
		$this->request['hb_sql_charset'] = $this->request['hb_sql_charset'] ? $this->request['hb_sql_charset'] : 'UTF8';
		
		// Child app?
		if (!$this->request['parent'] and isset($parent))
		{
			// What choices do we have?
			$where = '';
			$hidden = '';
			foreach ($parent['choices'] as $choice)
			{
				$where[] = "(sw='{$choice['app']}' and app_key='{$choice['key']}')";
				$hidden .= "<input type='hidden' name='newdb_{$choice['key']}' value='{$choice['newdb']}' />";
			}

			$this->DB->build(array('select' => 'app_id, name', 'from' => 'conv_apps', 'where' => implode(' OR ', $where)));
			$this->DB->execute();
			$parentoptions = array();
			if($parent['required'] === false)
			{
				$parentoptions[] = $this->html->convertAddOption(array('key' => 'x', 'name' => 'NO PARENT'));
			}
			if($parent['self'] === true)
			{
				$parentoptions[] = $this->html->convertAddOption(array('key' => -1, 'name' => 'This installation') );
			}
			while ($r = $this->DB->fetch())
			{
				$parentoptions[] = $this->html->convertAddOption(array('key' => $r['app_id'], 'name' => $r['name']));
			}
			$this->registry->output->html .= $this->html->convertAskForParent(implode('', $parentoptions), $hidden);
			return;
		}

		elseif ($this->request['parent'])
		{
			// If parent is "NO PARENT" we need to find information
			if ( $this->request['parent'] == "x" )
			{
				$this->registry->output->html .= $this->html->convertShowOptions2( _interface::$software, $info['name'] );
				return;
			}
			
			if ( $this->request['parent'] == -1 )
			{
				$this->request['hb_sql_driver'] = $this->settings['sql_driver'];
				$this->request['hb_sql_host'] = $this->settings['sql_host'];
				$this->request['hb_sql_user'] = $this->settings['sql_user'];
				$this->request['hb_sql_pass'] = $this->settings['sql_pass'];
				$this->request['hb_sql_database'] = $this->settings['sql_database'];
				$this->request['hb_sql_tbl_prefix'] = $this->settings['sql_tbl_prefix'];
				$this->request['hb_sql_charset'] = $this->settings['sql_charset'];
				$this->finish($info);
				return;
			}
			
			// Do we need new db info?
			$chosenparent = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'conv_apps', 'where' => "app_id='{$this->request['parent']}'" ) );
			if (!$this->request['newdb_'.$chosenparent['app_key']])
			{
				$this->request['hb_sql_driver'] = $chosenparent['db_driver'];
				$this->request['hb_sql_host'] = $chosenparent['db_host'];
				$this->request['hb_sql_user'] = $chosenparent['db_user'];
				$this->request['hb_sql_pass'] = $chosenparent['db_pass'];
				$this->request['hb_sql_database'] = $chosenparent['db_db'];
				$this->request['hb_sql_tbl_prefix'] = $chosenparent['db_prefix'];
				$this->request['hb_sql_charset'] = $chosenparent['db_charset'];
				$this->finish($info);
				return;
			}
		}

		// File system?
		elseif($info['nodb'])
		{
			$this->registry->output->html .= $this->html->convertShowOptionsCustom($custom);
			return;
		}

		$this->registry->output->html .= $this->html->convertShowOptions2( _interface::$software, $info['name'] );
	}

	/**
    * Save and boink to conversion page
    *
    * @access	private
    * @return	void
    */
	private function finish($info=array())
	{
		if (empty($info))
		{
			// should probably error?
			require_once IPS_ROOT_PATH.'applications_addon/ips/convert/modules_admin/'.$this->request['sw'].'/'.$this->request['choice'].'.php';
		}
		
		$app_name	=	$this->request['app_name'] ? $this->request['app_name'] : 'convert_' . $this->request['choice'];

		// Check (please)
		if ($this->DB->buildAndFetch( array( 'select' => 'app_id', 'from' => 'conv_apps', 'where' => "name='{$app_name}'" ) ))
		{
			$app_name	=	$app_name . '_' . date( 'Ymdis' );
		}

		// Insert
		$app = array(
					'sw'			=> $this->request['sw'],
					'app_key'		=> $info['key'],
					'name'			=> $app_name,
					'login'			=> (int)$info['login'],
					'parent'		=> ($this->request['parent'] != 'x' ) ? intval($this->request['parent']) : '',
					'db_driver'		=> $this->request['hb_sql_driver'],
					'db_host'		=> $this->request['hb_sql_host'],
					'db_user'		=> $this->request['hb_sql_user'],
					'db_pass'		=> $_REQUEST['hb_sql_pass'],
					'db_db'			=> $this->request['hb_sql_database'],
					'db_prefix'		=> $this->request['hb_sql_tbl_prefix'],
					'db_charset'	=> $this->request['hb_sql_charset'],
					'app_merge'		=> ( isset($info['merge']) && $info['merge'] === TRUE ) ? intval($this->request['app_merge']) : 1
				);
		
		// Check it doesn't exist
		$check = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'conv_apps', 'where' => "name='{$app_name}'" ) );
		
		if ( $check['app_id'] )
		{
			$this->DB->update( 'conv_apps', $app, "name='" . $app_name . "'" );
			IPSLib::updateSettings( array( 'conv_current' => $app_name ) );
			$this->registry->output->silentRedirect( $this->settings['base_url'] . 'app=convert&module=setup&section=switch' );
		}
		
		$this->DB->insert( 'conv_apps', $app );

		// Enable login?
		if ( $info['login'] )
		{
			$this->enableLogin();
		}

		// Custom info?
		if ( $this->request['custom'] )
		{
			$get	= unserialize( $this->settings['conv_extra'] );
			$us		= $get[ $this->request['app_name'] ];
			$us		= is_array($us) ? $us : array();
			$extra	= is_array($us['core']) ? $us : array_merge( $us, array( 'core' => array( ) ) );
			$get[ $this->request['app_name'] ] = $extra;

			foreach ( $custom as $k => $v )
			{
				$get[ $this->request['app_name'] ]['core'][$k] = $_REQUEST[$k];
			}

			IPSLib::updateSettings(array('conv_extra' => serialize($get)));
		}

		// Update which one we're on now
		IPSLib::updateSettings(array('conv_current' => $app_name));

		// And boink
		$this->registry->output->silentRedirect( $this->settings['base_url'] . 'app=convert&module=setup&section=switch' );
	}

	/**
    * Enable the converter's login method
    *
    * @access	private
    * @return	void
    */
	private function enableLogin()
	{
		//--------------------------------------------
		// INIT
		//--------------------------------------------

		require_once( IPS_KERNEL_PATH . 'class_xml.php' );
		$xml			= new class_xml();
		$xml->doc_type	= IPS_DOC_CHAR_SET;

		$login_id	= basename('convert');

		//-----------------------------------------
		// Now get the XML data
		//-----------------------------------------

		$dh = opendir( IPS_PATH_CUSTOM_LOGIN );

		if ( $dh !== false )
		{
			while ( false !== ($file = readdir($dh) ) )
			{
				if( is_dir( IPS_PATH_CUSTOM_LOGIN . '/' . $file ) AND $file == $login_id )
				{
					if( file_exists( IPS_PATH_CUSTOM_LOGIN . '/' . $file . '/loginauth_install.xml' ) )
					{
						$file_content = file_get_contents( IPS_PATH_CUSTOM_LOGIN . '/' . $file . '/loginauth_install.xml' );

						$xml->xml_parse_document( $file_content );

						if( is_array($xml->xml_array['export']['group']['row']) )
						{
							foreach( $xml->xml_array['export']['group']['row'] as $f => $entry )
							{
								if( is_array($entry) )
								{
									foreach( $entry as $k => $v )
									{
										if ( $f == 'VALUE' or $f == 'login_id' )
										{
											continue;
										}

										$data[ $f ] = $v;
									}
								}
							}
						}
					}
					else
					{
						ipsRegistry::getClass('output')->showError( 'Could not locate login method.' );
					}

					$dir_methods[ $file ] = $data;

					break;
				}
			}

			closedir( $dh );
		}

		if( !is_array($dir_methods) OR !count($dir_methods) )
		{
			ipsRegistry::getClass('output')->showError( 'An error occured while trying to enable the converter login method.' );
		}

		//-----------------------------------------
		// Now verify it isn't installed
		//-----------------------------------------

		$login		= $this->DB->buildAndFetch( array( 'select' => 'login_id', 'from' => 'login_methods', 'where' => "login_folder_name='" . $login_id . "'" ) );

		if( ! $login['login_id'] )
		{
			$max = $this->DB->buildAndFetch( array( 'select' => 'MAX(login_order) as highest_order', 'from' => 'login_methods' ) );

			$dir_methods[ $login_id ]['login_order'] = $max['highest_order'] + 1;

			$dir_methods[$login_id]['login_enabled'] = 1;

			$this->DB->insert( 'login_methods', $dir_methods[ $login_id ] );
		}
		else
		{
			$this->DB->update( 'login_methods', array( 'login_enabled' => 1 ), 'login_id=' . $login['login_id'] );
		}

		//-----------------------------------------
		// Recache
		//-----------------------------------------

		$cache	= array();

		$this->DB->build( array( 'select' => '*', 'from' => 'login_methods', 'where' => 'login_enabled=1' ) );
		$this->DB->execute();

		while ( $r = $this->DB->fetch() )
		{
			$cache[ $r['login_id'] ] = $r;
		}

		ipsRegistry::cache()->setCache( 'login_methods', $cache, array( 'array' => 1, 'deletefirst' => 1 ) );

		//-----------------------------------------
		// Switch
		//-----------------------------------------

		IPSLib::updateSettings(array('conv_login' => 1));
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
}

?>
