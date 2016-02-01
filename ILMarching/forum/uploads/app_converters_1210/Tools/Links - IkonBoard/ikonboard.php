<?php
/**
 * IkonBoard Redirect Gateway
 * @author Ryan "Oh good, I'm back in PHP" Ashbrook
 */

/**
 * Configuration
 */

define( 'CONV_ID', 'ikonboard' );

/**
 * End Configuration
 */

define( 'IN_IPB', 1 );
require_once( './initdata.php' );
require_once( IPS_ROOT_PATH .'/sources/base/ipsRegistry.php' );

$registry = ipRegistry::instance();
$registry->init();

$class = new redirector( $registry );
$class->run();

class redirector
{
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $convId;
	
	public function __construct( ipsRegistry $registry )
	{
		$this->registry	= $registry;
		$this->DB		= $this->registry->DB();
		$this->settings	= &$this->registry->fetchSettings();
		$this->request	= &$this->registry->fetchRequest();
		
		$convId			= $this->DB->buildAndFetch( array( 'select' => 'conv_id', 'from' => 'conv_apps', 'where' => "name = '" . CONV_ID . "'" ) );
		$this->convId	= $convId['conv_id'];
	}
	
	public function run()
	{
		switch( $this->request['act'] )
		{
			case 'forums':
				$link = $this->DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id = {$this->request['id']} AND type = 'forums' AND app = {$this->convId}" ) );
				
				if ( $link['ipb_id'] )
				{
					$this->registry->output->silentRedirect( $this->settings['board_url'] . "/index.php?showforum={$link['ipb_id']}", '', true );
				}
				else
				{
					$this->registry->output->silentRedirect( $this->settings['board_url'], '', true );
				}
			break;
			
			case 'topics':
				$link = $this->DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link_topics', 'where' => "foreign_id = {$this->request['id']} AND type = 'topics' AND app = {$this->convId}" ) );
				
				if ( $link['ipb_id'] )
				{
					$this->registry->output->silentRedirect( $this->settings['board_url'] . "/index.php?showtopic={$link['ipb_id']}", '', true );
				}
				else
				{
					$this->registry->output->silentRedirect( $this->settings['board_url'], '', true );
				}
			break;
			
			case 'profile':
				$id = explode( '-', $this->request['id'] );
				$link = $this->DB->buildAndFetch( array( 'select' => 'ipb_id', 'from' => 'conv_link', 'where' => "foreign_id = {$id[0]} AND type = 'members' AND app = {$this->convId}" ) );
				
				if ( $link['ipb_id'] )
				{
					$this->registry->output->silentRedirect( $this->settings['board_url'] . "/index.php?showuser={$link['ipb_id']}", '', true );
				}
				else
				{
					$this->registry->output->silentRedirect( $this->settings['board_url'], '', true );
				}
			break;
			
			case 'members':
				$this->registry->output->silentRedirect( $this->settings['board_url'] . "/index.php?app=members", '', true );
			break;
			
			default:
				$this->registry->output->silentRedirect( $this->settings['board_url'], '', true );
			break;
		}
	}
}