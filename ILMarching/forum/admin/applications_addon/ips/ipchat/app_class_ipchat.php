<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.5
 * Forums application initialization
 * Last Updated: $LastChangedDate: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Chat
 * @link		http://www.invisionpower.com
 * @since		14th May 2003
 * @version		$Rev: 10721 $
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class app_class_ipchat
{
	/**#@+
	 * Registry Object Shortcuts
	 *
	 * @var		object
	 */
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $lang;
	protected $member;
	protected $memberData;
	protected $cache;
	protected $caches;
	/**#@-*/
	
	/**
	 * Constructor
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void
	 */
	public function __construct( ipsRegistry $registry )
	{
		/* Make object */
		$this->registry = $registry;
		$this->DB       = $this->registry->DB();
		$this->settings =& $this->registry->fetchSettings();
		$this->request  =& $this->registry->fetchRequest();
		$this->cache    = $this->registry->cache();
		$this->caches   =& $this->registry->cache()->fetchCaches();
		$this->lang     = $this->registry->getClass('class_localization');
		$this->member   = $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
	}
	
	/**
	 * Do some set up after ipsRegistry::init()
	 * 
	 * @return	@e void
	 */
	public function afterOutputInit()
	{
		/* Check TOPIC permalink... */
		//$this->registry->getClass('output')->checkPermalink( 'false' );
		
		/* Add canonical tag */
		$this->registry->getClass('output')->addCanonicalTag( 'app=ipchat', 'false', 'app=ipchat' );
		
		/* Store root doc URL */
		$this->registry->getClass('output')->storeRootDocUrl( $this->registry->getClass('output')->buildSEOUrl( 'app=ipchat', 'publicNoSession', 'false', 'app=ipchat' ) );
	}
}