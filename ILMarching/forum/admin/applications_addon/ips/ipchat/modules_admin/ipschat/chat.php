<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.0.4
 * Chat default section
 * Last Updated: $LastChangedDate: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Chat
 * @link		http://www.invisionpower.com
 * @since		17 February 2003
 * @version		$Revision: 10721 $
 */

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

class admin_ipchat_ipschat_chat extends ipsCommand
{
	/**
	 * Main class entry point
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void		[Outputs to screen]
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		//-----------------------------------------
		// Load HTML
		//-----------------------------------------
		
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_chat' );
		
		$this->registry->class_localization->loadLanguageFile( array( 'admin_chat' ) );
		
		//-----------------------------------------
		// Set up stuff
		//-----------------------------------------
		
		$this->form_code	= $this->html->form_code	= 'module=ipschat&amp;section=chat';
		$this->form_code_js	= $this->html->form_code_js	= 'module=ipschat&section=chat';
		
		switch($this->request['do'])
		{
			case 'chatsettings':
				$this->_chatConfig();
			break;
			case 'chatsave':
				$this->_chatSave();
			break;
			
			default:
			case 'splash':
				$this->_chatSplash();
			break;
		}
		
		//-----------------------------------------
		// Pass to CP output hander
		//-----------------------------------------
		
		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}
	
	/**
	 * Chat splash page
	 *
	 * @return	@e void		[Outputs to screen]
	 */
	protected function _chatSplash()
	{
		//-----------------------------------------
		// Do we have an order number
		//-----------------------------------------
		
		$licenseData = $this->cache->getCache('licenseData');
		if ( empty( $licenseData ) )
		{
			$message = sprintf( $this->lang->words['chat_enter_key'], "{$this->settings['base_url']}app=core&module=tools&section=licensekey" );
		}
		else
		{
			if( is_array($licenseData['addons']) AND count($licenseData['addons']) )
			{
				foreach( $licenseData['addons'] as $addon )
				{
					if ( $addon['name'] == 'IP.Chat' and $addon['status'] == 'Ok' )
					{
						$this->_chatConfig();
						return;
					}
				}
			}
			
			$message = sprintf( $this->lang->words['chat_no_chat'] );
		}
	
		$this->registry->output->html 				= $this->html->ipschatKey( $message );
	}
	
	/**
	 * Save your key to enable chat
	 *
	 * @return	@e void		[Outputs to screen]
	 */
	protected function _chatSave()
	{
		$acc_number = trim($this->request['account_no']);
		
		if ( $acc_number == "" )
		{
			$this->registry->output->showError( $this->lang->words['chat_invalid_key'], 1193 );
		}

		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------
		
		$classToLoad = IPSLib::loadActionOverloader( IPSLib::getAppDir('core') . '/modules_admin/settings/settings.php', 'admin_core_settings_settings' );
		$settings    = new $classToLoad();
		$settings->makeRegistryShortcuts( $this->registry );
		
		$settings->html			= $this->registry->output->loadTemplate( 'cp_skin_settings', 'core' );
		ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'admin_tools' ), 'core' );
		$settings->form_code	= $settings->html->form_code    = 'module=settings&amp;section=settings';
		$settings->form_code_js	= $settings->html->form_code_js = 'module=settings&section=settings';

		$this->DB->update( 'core_sys_conf_settings', array( 'conf_value' => $acc_number ), "conf_key='ipb_reg_number'" );

		$settings->settingsRebuildCache();

		//-----------------------------------------
		// Show config
		//-----------------------------------------
		
		$this->settings['ipb_reg_number']	= $acc_number;
		
		$this->_chatConfig();
	}
	
	/**
	 * Show the configuration page
	 *
	 * @return	@e void		[Outputs to screen]
	 */
	protected function _chatConfig()
	{
		//-----------------------------------------
		// Load libby-do-dah
		//-----------------------------------------
		
		$classToLoad = IPSLib::loadActionOverloader( IPSLib::getAppDir('core') . '/modules_admin/settings/settings.php', 'admin_core_settings_settings' );
		$settings    = new $classToLoad();
		$settings->makeRegistryShortcuts( $this->registry );
		
		$settings->html			= $this->registry->output->loadTemplate( 'cp_skin_settings', 'core' );
		ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'admin_tools' ), 'core' );
		$settings->form_code	= $settings->html->form_code    = 'module=settings&amp;section=settings';
		$settings->form_code_js	= $settings->html->form_code_js = 'module=settings&section=settings';
		
		//-----------------------------------------
		// Did we reset the component?
		//-----------------------------------------
		
		if ( ! $this->settings['ipschat_account_key'] AND !$this->settings['ipb_reg_number'] )
		{
			$this->_chatSplash();
		}
		
		$this->request['conf_title_keyword']	= 'ipschat';
		$settings->return_after_save			= $this->settings['base_url'] . $this->form_code . '&do=chatsettings';
		
		$settings->_viewSettings();
	}
}