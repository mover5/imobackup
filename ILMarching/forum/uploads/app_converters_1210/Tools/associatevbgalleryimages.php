<?php
/**
 * Tool to reassociate images with image_member_id of 0 to the same author as the corresponding album_id.
 * Works with Gallery 5, only for Albums and not Categories.
 * 
 * @author Michael Burton
 * @copyright Invision Power Services, 2012
 */

	class moo
	{
		private $go = 100;
		
		public function __construct()
		{
			if (file_exists('cache/supascript_lock.txt'))
			{
				$time = file_get_contents('cache/supascript_lock.txt');
				if ( $time < ( time() - 36000 ) )
				{
					echo 'Locked.';
					exit;
				}
			}
			else
			{
				@file_put_contents( 'cache/supascript_lock.txt', time() );
			}
			
			require_once( 'initdata.php' );
			require_once( CP_DIRECTORY.'/sources/base/ipsRegistry.php' );
			require_once( CP_DIRECTORY.'/sources/base/ipsController.php' );
			$this->registry	=	ipsRegistry::instance();
			$this->registry->init();				
			$this->settings =&	$this->registry->fetchSettings();
			$this->DB       =	$this->registry->DB();
			$this->next 	=	$_REQUEST['st'] + $this->go;
		}
		
		public function load()
		{
			$limit = array( $_REQUEST['st'], $this->go );
			$this->DB->build( 
							array( 
								'select'	=>	'*', 
								'from'		=>	array('gallery_albums' => 'a', 'gallery_images' => 'i'), 
								'where'		=>	'i.image_album_id=a.album_id',
								'limit'		=>	$limit
								) 
							);
			$this->DB->execute();

			if ( !$this->DB->getTotalRows() )
			{
				echo 'Complete';
				exit;
			}
			
			while( $row = $this->DB->fetch() )
			{
				$rows[] = $row;
			}
			
			return $rows;
		}
		
		public function process($row)
		{	
			if ($_REQUEST['reverse'])
			{
				$this->DB->update( 'gallery_images', array( 'image_album_id' => $row['album_id'] ), 'image_member_id=' . $row['album_owner_id'] );
			}
			else
			{
				$this->DB->update( 'gallery_images', array( 'image_member_id' => $row['album_owner_id'] ), 'image_album_id=' . $row['album_id'] );
			}
		}
	}
	
	// Init
	$moo = new moo();
	
	// Get posts
	$loop = $moo->load();
	
	// Loop through
	foreach($loop as $row)
	{
		$moo->process($row);
	}
		
	// Next
	echo "Up to {$moo->next} done
	<script type='text/javascript'>window.location = '{$_SERVER['PHP_SELF']}?st={$moo->next}';</script>";
