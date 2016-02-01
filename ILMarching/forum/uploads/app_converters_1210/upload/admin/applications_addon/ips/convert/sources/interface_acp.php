<?php
/**
 * IPS Converters
 * Application Files
 * ACP Interface
 * Last Update: $Date: 2013-04-08 08:59:47 -0400 (Mon, 08 Apr 2013) $
 * Last Updated By: $Author: AndyMillne $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 826 $
 */

	abstract class _interface
	{

		public static $software = array(
									'board'		=>	array( 
														'title'		=>	'IP.Board', 
														'enabled'	=>	true 
													),
									'blog'		=>	array( 
														'title'		=>	'IP.Blog', 
														'enabled'	=>	false 
													),
									'calendar'	=> 	array( 
														'title'		=>	'IP.Calendar', 
														'enabled'	=>	false 
													),
									'ccs'		=> 	array( 
														'title'		=>	'IP.Content', 
														'enabled'	=>	false 
													),
									'downloads'	=> 	array( 
														'title'		=>	'IP.Downloads', 
														'enabled'	=> 	false 
													),
									'gallery'	=> 	array( 
														'title'		=>	'IP.Gallery', 
														'enabled'	=>	false 
													),
									'nexus'		=> 	array( 
														'title'		=>	'IP.Nexus', 
														'enabled'	=>	false 
													),
									# @todo in future
									/**
									'3rdparty'	=>	array(
														array(
															'title'		=>	'Tracker',
															'enabled'	=>	false,
															'key'		=>	'tracker',
														),
														array(
															'title'		=>	'Subscriptions',
															'enabled'	=>	false,
															'key'		=>	'subscriptions'
														)
													),
									**/
								);
		
		public static $source = array();
		
		/**
	     * Show Error Message
	     *
	     * @access	private
	     * @param	string		Error message
	     * @return	void
	    */
		public function sendError($message, $type='')
		{
			switch( $type )
			{
				case 'database':
					// Form defaults
					$this->request['hb_sql_driver'] 	= $this->app['db_driver'];
					$this->request['hb_sql_host'] 		= $this->app['db_host'];
					$this->request['hb_sql_user'] 		= $this->app['db_user'];
					$this->request['hb_sql_pass'] 		= $this->app['db_pass'];
					$this->request['hb_sql_database']	= $this->app['db_db'];
					$this->request['hb_sql_tbl_prefix']	= $this->app['db_prefix'];
					$this->request['hb_sql_charset'] 	= $this->app['db_charset'];
					$this->request['sw']				= $this->app['sw'];
					$this->request['choice']			= $this->app['app_key'];
					$this->request['app_name']			= $this->app['name'];
					
					$this->registry->output->html .= $this->html->convertShowOptions2( _interface::$software, $this->module->name, true, $message );
					$this->registry->output->html_main .= $this->registry->output->global_template->global_frame_wrapper();
					$this->registry->output->sendOutput();
					exit;
					break;
					
				default:
					ipsRegistry::getClass('output')->showError( $message );
			}
		}

		/**
	     * Display Menu
	     *
	     * @access	private
	     * @param	array		Array containing special infomation if converters should automatically move to next step
	     * @return	void
	    */
		protected function menu($special=array())
		{
			$parent = false;
			
			if ( $this->app['parent'] )
			{
				$parent = true;
			}
			
			$actionrows = array();
			foreach ($this->module->actions as $action => $pres)
			{
				if ( !empty($special) and array_key_exists($action, $special) )
				{
					$count = 0;
					foreach($special[$action]['multi'] as $multicount)
					{
						$count += $this->countRows($multicount);
					}

					$actionrows[] = $this->html->convertMenuRow($this->app, $this->menuRow($action), $this->module->countRows($special[$action]['single']), $this->getStatus($action, $parent), $this->getButton($action, $this->module->actions, $this->module->checkConf($action)), $count);
				}
				else
				{
					$actionrows[] = $this->html->convertMenuRow($this->app, $this->menuRow($action), $this->module->countRows($action), $this->getStatus($action, $parent), $this->getButton($action, $this->module->actions, $this->module->checkConf($action)));
				}
			}
			$this->registry->output->html .= $this->html->convertMenu(implode('', $actionrows), $this->getInfo());
		}

		/**
		 * Return a button
		 *
		 * @access 	public
		 * @param	string		action (e.g. 'members', 'forums', etc.)
		 * @param 	array 		actions that converter is capable of
		 * @param 	boolean		If true, actions has configuration options
		 * @return 	string 		html to display
		 **/
		private function getButton($action, $actions, $conf)
		{
			$info = $this->menuRow($action);
			$on = true;
			$missed = array();
			if (is_array($actions[$action]))
			{
				foreach ($actions[$action] as $pre)
				{
					if ($this->getStatus($pre) == '-' and $this->getStatus($pre, true) == '-')
					{
						$on = false;
					}
				}
			}
			if ($on)
			{
				if ($this->getStatus($action) == '-')
				{
					return $this->html->convertMenuRowButtonOn();
				}
				else
				{
					return $this->html->convertMenuRowButtonAgain($conf);
				}
			}
			else
			{
				foreach ($actions[$action] as $ppre)
				{
					$ipre = $this->menuRow($ppre, true);
					$pres[] = ($ipre) ? $ipre['name'] : ucwords(str_replace('_', ' ', $ppre));
				}
				return $this->html->convertMenuRowButtonOff($pres);
			}
		}

		/**
	     * Move to next step automatically
	     *
		 * @param 	string 		Action to do next
	     * @return	void
	    */
		public function goToNext($next)
		{
			if(is_array($next))
			{
				$this->registry->output->html .= $this->registry->output->global_template->temporaryRedirect("{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$next[0]}&st=0&cycle={$this->request['cycle']}&total=".$this->countRows($next[1]), "Continuing..." );
			}
			else
			{
				$this->registry->output->html .= $this->registry->output->global_template->temporaryRedirect("{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$next}&st=0&cycle={$this->request['cycle']}&total=".$this->countRows($next), "Continuing..." );
			}
			$this->sendOutput ( );
		}

		/**
	     * Display Finish Screen
	     *
	     * @access	private
		 * @param 	array 		Info from menuRow();
	     * @return	void
	    */
		protected function displayFinishScreen($info)
		{
			if (!empty($this->errors))
			{
				$es = 'The following errors occurred: <ul>';
				foreach ($this->errors as $e)
				{
					$es .= "<li>{$e}</li>";
				}
				$es .= '</ul>';
			}
			else
			{
				$es = 'No problems found.';
			}

			$this->registry->output->html .= $this->html->convertComplete($info['name'].' Conversion Complete.', array($es, $info['finish']));
			$this->sendOutput();
		}

		/**
		 * Loads the next cycle
		 *
		 * @access 	public
		 * @return 	void
		 **/
		public function next()
		{
			$total = $this->request['total'];
			$pc = round((100 / $total) * $this->end);
			$message = ($pc > 100) ? 'Finishing...' : "{$pc}% complete";
			IPSLib::updateSettings(array('conv_error' => serialize($this->errors)));
			$end = ($this->end > $total) ? $total : $this->end;
			$this->registry->output->html .= $this->registry->output->global_template->temporaryRedirect("{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$this->request['do']}&st={$this->end}&cycle={$this->request['cycle']}&total={$total}", "<strong>{$end} of {$total} converted</strong><br />{$message}<br /><br /><strong><a href='{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$this->request['do']}&st={$this->end}&cycle={$this->request['cycle']}&total={$total}'>Click here if you are not redirected.</a></strong>" );
			$this->sendOutput ( );
		}

		/**
		 * Reloads current cycle
		 *
		 * @access 	public
		 * @return void
		 **/
		public function reload()
		{
			// fix for reconfigure steps not truncating
			if ($this->request['request_method'] == 'post')
			{
				$this->registry->output->html .= $this->registry->output->global_template->temporaryRedirect("{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$this->request['do']}&st={$this->start}&cycle={$this->request['cycle']}&total={$this->request['total']}&empty={$this->request['empty']}&rc={$this->request['rc']}", "Loading..." );
			}
			else
			{
				$this->registry->output->html .= $this->registry->output->global_template->temporaryRedirect("{$this->settings['base_url']}app=convert&module={$this->app['sw']}&section={$this->app['app_key']}&do={$this->request['do']}&st={$this->start}&cycle={$this->request['cycle']}&total={$this->request['total']}&rc={$this->request['rc']}", "Loading..." );
			}
			$this->sendOutput ( );
		}

		/**
	    * Output to screen and exit
	    *
	    * @access	private
	    * @return	void
	    */
		protected function sendOutput()
		{
			$this->registry->output->html .= $this->html->convertFooter();
			$this->registry->output->html_main .= $this->registry->output->global_template->global_frame_wrapper();
			$this->registry->output->sendOutput();
			exit;
		}

		/**
		 * Ask for More Info
		 *
		 * @access	public
		 * @param	string		action (e.g. 'members', 'forums', etc.)
		 * @param	array 		values from self::load()
		 * @param	array 		Things to ask for
		 * @param 	string 		key for hint box (optional)
		 * @param	array 		Map data
		 * @return 	void
		 **/
		public function getMoreInfo($action, $loop, $custom=array(), $hint='', $mapfields=array())
		{
			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->app['name']];
			$us = is_array($us) ? $us : array();
			$extra = is_array($us[$action]) ? $us : array_merge($us, array($action => array()));

			if (!empty($mapfields))
			{
				$ask = array();
				foreach ($loop as $loop)
				{
					if (!array_key_exists($loop[$mapfields['idf']], $extra[$action]))
					{
						$ask[$loop[$mapfields['idf']]] = $loop[$mapfields['nf']];
					}
				}
				if (!empty($ask))
				{
					$ourrows = $this->loadLocalInfo($action);
					$options = "<option value='x'>{$custom['new']}</option>";
					foreach ($ourrows as $id => $name)
					{
						$options .= "<option value='{$id}'>{$name}</option>";
					}

					$row = '';
					foreach ($ask as $id => $name)
					{
						$select = "<select name='{$action}[$id]'>{$options}</select>";
						$rows .= $this->html->convertMoreInfoRow($name, $select);
					}
					$this->registry->output->html .= $this->html->convertMoreInfo($rows, $custom['ot'], $custom['nt']);
					$this->sendOutput();
				}
			}
			else
			{
				$this->generateMoreInfoPage($custom, $hint);
			}

		}

		/**
		 * Generate a page for arbitrary questions
		 *
		 * @access	private
		 * @param	array 		Things to ask for
		 * @param 	string 		key for hint box
		 * @return 	void
		 **/
		private function generateMoreInfoPage($input_array, $hint)
		{
			$get = unserialize($this->settings['conv_extra']);
			$us = $get[$this->app['name']];

			foreach ($input_array as $key => $question)
			{
				if ($question['override'])
				{
					if (!$us[$question['override']['name']][$question['override']['id']])
					{
						$ask[$key] = $question;
					}
				}
				else
				{
					if (!$us[$key])
					{
						$ask[$key] = $question;
					}
				}
			}

			if (!empty($ask))
			{
				$rows = '';
				foreach ($ask as $key => $qinfo)
				{
					if ($qinfo['type'] == 'text')
					{
						if ($qinfo['override'])
						{
							$key = $qinfo['override']['name'].']['.$qinfo['override']['id'];
						}
						$input = "<input name='input[{$key}]' size='50' />";
					}
					elseif ($qinfo['type'] == 'dupes')
					{
						$input = "<select name='input[{$key}]'>
							<option value='local'>Keep IP.Board settings</option>
							<option value='remote'>Overwrite with remote settings</option>
						</select>";
					}
					elseif ($qinfo['type'] == 'dropdown')
					{
						$input = "<select name='input[{$key}]'>";
						foreach ($qinfo['options'] as $key => $value)
						{
							$input .= "<option value='{$key}'>{$value}</option>";
						}
						$input .= '</select>';
					}
					else
					{
						$this->sendError('There is a problem with the converter: bad more info type');
					}
					if ($qinfo['extra'])
					{
						$input .= " {$qinfo['extra']}";
					}
					$rows .= $this->html->convertMoreInfoRow($qinfo['label'], $input);
				}
				$this->registry->output->html .= $this->html->convertMoreInfo($rows, '&nbsp;', '&nbsp;');

				if ($hint)
				{
					switch ($hint)
					{
						case 'path':
							$hint = 'The path to your IP.Board is: '.DOC_IPS_ROOT_PATH;
							break;

						case 'database':
							$hint = 'You must first create a database within IP.Content';
							break;
					}
					$this->registry->output->html .= $this->html->convertHint($hint);
				}

				$this->sendOutput();
			}
		}

		/**
		 * Load anything needed for the interface
		 *
		 * @access	public
		 * @return 	mixed 	Anything needed for the interface
		 **/
		public function loadInterface()
		{
			return $this->registry->output->loadTemplate( 'cp_skin_convert' );
		}

		/**
		 * Display a header message
		 *
		 * @access	public
		 * @return 	null
		 **/
		public function sendHeader($header)
		{
			$this->registry->output->html .= $this->html->convertHeader($header);
		}

	}

?>