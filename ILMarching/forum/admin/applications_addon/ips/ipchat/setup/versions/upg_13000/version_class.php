<?php

/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.5
 * Upgrade Class
 *
 * Class to add options and notices for IP.Board upgrade
 * Last Updated: $Date: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 * </pre>
 * 
 * @author		Matt Mecham <matt@invisionpower.com>
 * @version		$Rev: 10721 $
 * @since		3.0
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @link		http://www.invisionpower.com
 * @package		IP.Board
 */ 

class version_class_ipchat_13000
{
	/**
	 * Constructor
	 *
	 * @param	object		$registry		Registry object
	 * @return	@e void
	 */
	public function __construct( ipsRegistry $registry ) 
	{
		/* Make object */
		$this->registry =  $registry;
		$this->DB       =  $this->registry->DB();
		$this->settings =& $this->registry->fetchSettings();
		$this->request  =& $this->registry->fetchRequest();
		$this->cache    =  $this->registry->cache();
		$this->caches   =& $this->registry->cache()->fetchCaches();
	}
	
	/**
	 * Add pre-upgrade options: Form
	 * 
	 * @return	string	 HTML block
	 */
	public function preInstallOptionsForm()
	{
return <<<EOF
	<ul>
		<li>
			<input type='checkbox' name='skipIgnoredUsers' value='1' />
			<strong>Skip</strong> ignored users conversion.  You will be able to convert ignored users later from the ACP. 
		</li>
	</ul>
EOF;
		
	}
	
	/**
	 * Add pre-upgrade options: Save
	 *
	 * Data will be saved in saved data array as: appOptions[ app ][ versionLong ] = ( key => value );
	 * 
	 * @return	array	 Key / value pairs to save
	 */
	public function preInstallOptionsSave()
	{
		/* Return */
		return array( 'skipIgnoredUsers' => intval( $_REQUEST['skipIgnoredUsers'] ),
					);
		
	}
	
	/**
	 * Return any post-installation notices
	 * 
	 * @return	array	 Array of notices
	 */
	public function postInstallNotices()
	{
		$options	= IPSSetUp::getSavedData('custom_options');
		$_skip		= $options['ipchat'][13000]['skipIgnoredUsers'];
		
		$notices   = array();
		
		if ( $_skip )
		{
			$notices[] = "Ignored chat users have not been converted.  You should run the tool in the ACP under Other Apps &gt; Chat &gt; Tools to convert the ignored users.";
		}

		return $notices;
	}
	
	
	/**
	 * Return any pre-installation notices
	 * 
	 * @return	array	 Array of notices
	 */
	public function preInstallNotices()
	{
		$notices = array();

		return $notices;
		
	}
}
	
