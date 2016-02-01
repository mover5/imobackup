<?php

class boardIndexWhosChatting
{
	/**
	 * Registry Object
	 *
	 * @var		object
	 */
	protected $registry;
	
	public function __construct()
	{
		/* Make registry objects */
		$this->registry	= ipsRegistry::instance();
	}
	
	public function getOutput()
	{
		if( is_file( IPSLib::getAppDir('ipchat') . '/sources/hooks.php' ) )
		{
			$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir('ipchat') . '/sources/hooks.php', 'hooksApi', 'ipchat' );
			$chatting	 = new $classToLoad( $this->registry );
			return $chatting->whosChatting();
		}
	}
}