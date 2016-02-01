<?php
//-----------------------------------------
// This tool will convert vBulletin Gallery's
// "file.picture" files to their correct filenames
//-----------------------------------------

	// Init
	require_once 'conf_global.php';
	require_once( 'initdata.php' );
	require_once( 'admin/sources/base/ipsRegistry.php' );
	require_once( 'admin/sources/base/ipsController.php' );
	$registry = ipsRegistry::instance();
	$registry->init();				
	$settings =& $registry->fetchSettings();
	$DB       = $registry->DB();
	
	/* Setup some values */
	$pergo = 100; # Change here how many images you want to parse each cycle 
	$start = intval($_REQUEST['st']);
	$end   = $start + $pergo;
	$path  = $settings['upload_dir'];
	
	// Grab records
	$DB->build( array(
		'select' 	=> '*',
		'from'		=> 'attachments',
		'limit'		=> array( $start, $pergo )
		) );
	$DB->execute();
	
	// Got any left?
	if (!$DB->getTotalRows())
	{
		echo 'Complete';
		exit;
	}
	
	// Loop
	while ($row = $DB->fetch())
	{
		$do[] = $row;
	}
	
	foreach($do as $row)
	{
		$old = $path.'/'.$row['attach_location'];
		
		if ( strpos( $row['attach_location'], '/' ) !== FALSE )
		{	
			$exploded = explode( '/', $row['attach_location'] );
			$filename = array_pop( $exploded );
			$saveNew = str_replace( $filename, $row['attach_file'], $row['attach_location'] );
		}
		else
		{
			$saveNew = $row['attach_file'];
		}
		
		$new = $path.'/'.$saveNew;
		while (file_exists($new))
		{
			$saveNew = '_'.$saveNew;
			$new = $path.'/'.$saveNew;
		}
		
		
		if (!file_exists($old))
		{
			echo "Couldn't find file: {$old}<br />";
			continue;
		}
		
		if(rename($old, $new))
		{
			$DB->update( 'attachments', array( 'attach_location' => $saveNew ), 'attach_id='.$row['attach_id'] );
			echo "Done {$row['attach_id']}<br />";
		}
		else
		{
			echo "Rename failed (OLD: {$old} - NEW: {$new})<br />";
		}
	}
	
	// Boink
	echo "Up to {$end} done
	<script type='text/javascript'>window.location = '{$_SERVER['PHP_SELF']}?st={$end}';</script>";
