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
	$path  = $settings['gallery_images_path'];
	
	// Grab records
	$DB->build( array(
		'select' 	=> '*',
		'from'		=> 'gallery_images',
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
		if ( $row['masked_file_name'] == $row['file_name'] )
		{
			echo $row['id']." SKIPPED: File name and masked file name are the same, no need to rename again<br />";			
			continue;
		}		
						
		$old = ($row['directory']) ? $path.'/'.$row['directory'].'/'.$row['masked_file_name'] : $path.'/'.$row['masked_file_name'];
		$saveNew = $row['file_name'];
		$new = ($row['directory']) ? $path.'/'.$row['directory'].'/'.$saveNew : $path.'/'.$saveNew;
		while (file_exists($new))
		{
			$saveNew = '_'.$saveNew;
			$new = ($row['directory']) ? $path.'/'.$row['directory'].'/'.$saveNew : $path.'/'.$saveNew;
		}
		
		if (!file_exists($old))
		{
			echo "Couldn't find file: {$old}<br />";
		}
		else
		{
			if(rename($old, $new))
			{
				$DB->update( 'gallery_images', array( 'masked_file_name' => $saveNew, 'file_name' => $saveNew ), 'id='.$row['id'] );
			}
			else
			{
				echo "Rename failed (OLD: {$old} - NEW: {$new})<br />";
			}
		}
	}
	
	// Boink
	echo "Up to {$end} done
	<script type='text/javascript'>window.location = '{$_SERVER['PHP_SELF']}?st={$end}';</script>";
