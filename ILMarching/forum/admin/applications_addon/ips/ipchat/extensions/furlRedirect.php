<?php

/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.5
 * RSS output plugin :: posts
 * Last Updated: $Date: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Chat
 * @link		http://www.invisionpower.com
 * @since		6/24/2008
 * @version		$Revision: 10721 $
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class furlRedirect_ipchat
{	
	/**
	 * Key type: Type of action (topic/forum)
	 *
	 * @var		string
	 */
	protected $_type = '';
	
	/**
	 * Key ID
	 *
	 * @var		int
	 */
	protected $_id = 0;
	
	/**
	 * Constructor
	 *
	 * @param	object	Registry
	 * @return	@e void
	 */
	function __construct( ipsRegistry $registry )
	{
		$this->registry =  $registry;
		$this->DB       =  $registry->DB();
		$this->settings =& $registry->fetchSettings();
	}

	/**
	 * Set the key ID
	 * <code>furlRedirect_forums::setKey( 'topic', 12 );</code>
	 *
	 * @param	string	Type
	 * @param	mixed	Value
	 * @return	@e void
	 */
	public function setKey( $name, $value )
	{
		$this->_type = $name;
		$this->_id   = $value;
	}
	
	/**
	 * Set up the key by URI
	 *
	 * @param	string		URI (example: index.php?showtopic=5&view=getlastpost)
	 * @return	bool
	 */
	public function setKeyByUri( $uri )
	{
		return FALSE;
	}
	
	/**
	 * Return the SEO title
	 *
	 * @return	string
	 */
	public function fetchSeoTitle()
	{
		return 'false';
	}
}