<?php
/*
+--------------------------------------------------------------------------
|   IP.Board v3.4.6
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2009 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
*/

$PRE = trim(ipsRegistry::dbFunctions()->getPrefix());
$DB  = ipsRegistry::DB();


$TABLE	= 'spam_service_log';
$SQL[]	= "ALTER TABLE spam_service_log CHANGE ip_address ip_address VARCHAR( 46 ) NOT NULL;";


