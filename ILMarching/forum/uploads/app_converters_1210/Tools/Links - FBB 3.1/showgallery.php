<?php

/**
 * IPS Converters
 * FBB Links
 * showgallery.php
 * Last Update: $Date:  $
 * Last Updated By: $Author:  $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision:  $
 */


if (!file_exists(IPB_PATH.'/showforum.php'))
{
	echo 'Invalid IPB path';
	exit;
}

require('showforum.php');
exit;