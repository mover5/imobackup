<?php

class chatUnbanModcp
{
	/**
	 * Registry Object
	 *
	 * @var		object
	 */
	protected $registry;
	
	/**
	 * Chat hook lib
	 *
	 * @var		object
	 */
	protected $chatting;
	
	public function __construct()
	{
		/* Make registry objects */
		$this->registry	= ipsRegistry::instance();

		if( is_file( IPSLib::getAppDir('ipchat') . '/sources/hooks.php' ) )
		{
			$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir('ipchat') . '/sources/hooks.php', 'hooksApi', 'ipchat' );
			$this->chatting	 = new $classToLoad( $this->registry );
		}
	}
	
	public function getOutput()
	{
	}

	/**
	 * Replace output
	 *
	 * @param	string		Output
	 * @param	string		Hook key
	 * @return	string		Output parsed
	 */
	public function replaceOutput( $output, $key )
	{
		if( is_array($this->registry->output->getTemplate('modcp')->functionData['membersList']) AND count($this->registry->output->getTemplate('modcp')->functionData['membersList']) )
		{
			$tag	= '<!--hook.' . $key . '-->';
			$last	= 0;
		
			foreach( $this->registry->output->getTemplate('modcp')->functionData['membersList'] as $_idx => $instance )
			{
				if( is_array($instance['members']) AND count($instance['members']) )
				{
					foreach( $instance['members'] as $member )
					{
						$pos	= strpos( $output, $tag, $last );
						
						if( $pos )
						{
							$string	= $this->chatting->chatUnbanModcp( $member );
							$output	= substr_replace( $output, $string . $tag, $pos, strlen( $tag ) ); 
							$last	= $pos + strlen( $tag . $string );
						}
					}
				}
			}
		}
		
		return $output;
	}
}