<?php

/**
 * Invision Power Services
 * IP.Board v3.0.4
 * Chat services
 * Last Updated: $Date: 2011-10-19 16:16:33 -0400 (Wed, 19 Oct 2011) $
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Chat
 * @link		http://www.invisionpower.com
 * @since		Fir 12th Aug 2005
 * @version		$Revision: 9640 $
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class public_ipchat_ajax_getemoticons extends ipsAjaxCommand
{
	/**
	 * Main class entry point
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void		[Outputs to screen]
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		/* INIT */
 		$smilie_id        = 0;

		/* Query the emoticons */
 		$this->DB->build( array( 'select' => 'typed, image', 'from' => 'emoticons', 'where' => "emo_set='" . $this->registry->output->skin['set_emo_dir'] . "'" ) );
		$this->DB->execute();
		
		/* Loop through and build output array */
		$rows = array();
		
		if( $this->DB->getTotalRows() )
		{
			while( $r = $this->DB->fetch() )
			{
				$smilie_id++;
				
				if( strstr( $r['typed'], "&quot;" ) )
				{
					$in_delim  = "'";
					$out_delim = '"';
				}
				else
				{
					$in_delim  = '"';
					$out_delim = "'";
				}
				
				$rows[] = array(
								'code'       => stripslashes( $r['typed'] ),
								'image'      => stripslashes( $r['image'] ),
								'in'         => $in_delim,
								'out'        => $out_delim,
								'smilie_id'	 =>	$smilie_id							
							);					
			}
		}
		
		$this->returnHtml( $this->registry->getClass('output')->getTemplate('legends')->emoticonPopUpList( 'message', $rows, 1 ) ); // The 3rd param indicated legacy editor
	}
}