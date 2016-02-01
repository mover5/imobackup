<?php
/**
 * <pre>
 * IPS Converters
 * e107 Redirects - Configuration file.
 * Last Updated: $Date: 2012-12-17 16:35:57 -0500 (Mon, 17 Dec 2012) $ BY $Author: MikeyB $
 * </pre>
 *
 * @author 		Ryan Ashbrook
 * @copyright	(c) 2011 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/community/board/license.html
 * @package		IP.Board
 * @link		http://www.invisionpower.com
 * @version		$Rev: 712 $
 *
 */

// THE URL TO YOUR IPB FORUMS (no trailing slash or index.php)
define('IPB_URL', 'http://localhost/ipb_mysql');
	
// THE PATH TO YOUR IPB FORUMS (no trailing slash)
define('IPB_PATH', 'C:/Zend/Apache2/htdocs/ipb_mysql');
	
// THE CONVERSION ID
// This would have been asked for when setting up the converters
define('CONV_ID', 'myka_test_1');

// GATEWAY - DO NOT EDIT
// This stops a 'same domain' check which stops redirect scripts working in certain circumstances.
define( 'CCS_GATEWAY_CALLED', true );

?>