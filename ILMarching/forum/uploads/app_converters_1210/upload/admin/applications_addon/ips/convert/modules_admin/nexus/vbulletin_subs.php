<?php
/**
 * IPS Converters
 * IP.Nexus 1.2 Converters
 * vBulletin -> Nexus converter
 * Last Update: $Date$
 * Last Updated By: $Author$
 *
 * @package		IPS Converters
 * @author 		Alex Hobbs / Michael Burton
 * @copyright	(c) 2011 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 543 $
 */

$info = array (
	'key'	=> 'vbulletin_subs',
	'name'	=> 'vBulletin',
	'login'	=> false,
);

$parent = array (
	'required'	=> true,
	'choices'	=> array (
		array (	'app' => 'board', 'key' => 'vbulletin', 'newdb' => false ),
		array (	'app' => 'board', 'key' => 'vbulletin_legacy', 'newdb' => false ),
		array (	'app' => 'board', 'key' => 'vbulletin_legacy36', 'newdb' => false ),
	),
);

class admin_convert_nexus_vbulletin_subs extends ipsCommand
{
	private $convertGroup = 0;
	
	public function doExecute( ipsRegistry $registry )
	{
		$this->registry = $registry;
		
		// load libs.
		require_once( IPSLib::getAppDir( 'convert' ) . '/sources/lib_master.php' );
		require_once( IPSLib::getAppDir( 'convert' ) . '/sources/lib_nexus.php' );
		$this->lib = new lib_nexus ( $registry, $html, $this );
		
		$this->html = $this->lib->loadInterface ( );
		$this->lib->sendHeader( 'vBulletin &rarr; IP.Nexus Converter' );
		
		$this->HB = $this->lib->connect();
		
		// populate actions
		$this->actions	=	array(
								'nexus_customers'	=>	array(	
															'members' 
														),
								'nexus_packages'	=>	array(
															'groups'
														),
								'nexus_invoices'	=>	array(	
															'nexus_packages',
															'members'
														),
								'nexus_purchases'	=>	array(	
															'nexus_packages',
															'nexus_invoices',
															'members'
														),
							);
		
		// Check for converted group
		$row	=	$this->DB->buildAndFetch( 
									array( 
										'select' => '*', 
										'from' => 'nexus_package_groups', 
										'where' => "pg_name='Converted' and pg_parent=0" 
									) 
								);
		
		$this->convertGroup	=	$row['pg_id'];
		
		if ( !$row['pg_id'] )
		{
			// Insert conversion group into Nexus
			$this->DB->insert( 'nexus_package_groups', array(
					'pg_name'		=> 'Converted',
					'pg_seo_name'	=> 'converted',
					'pg_parent'		=> 0,
					'pg_position'	=> 1
				)
			);
			
			$id = $this->DB->getInsertId();
			
			$this->lib->addLink( $id, $id, 'nexus_package_groups' );
		}
		
		if ( array_key_exists ( $this->request['do'], $this->actions ) )
		{
			call_user_func ( array ( $this, 'convert_' . $this->request['do'] ) );
		}
		else
		{
			$this->lib->menu();
		}
		
		$this->sendOutput();
	}
	
	private function sendOutput()
	{
		$this->registry->output->html		.= $this->html->convertFooter();
		$this->registry->output->html_main	.= $this->registry->output->global_template->global_frame_wrapper();
		$this->registry->output->sendOutput();
		exit;
	}
	
	public function countRows( $action )
	{
		switch( $action )
		{
			case 'nexus_customers':
				return $this->lib->countRows( 'subscriptionlog' );
				break;
				
			case 'nexus_packages':
				return $this->lib->countRows( 'subscription' );
				break;
				
			case 'nexus_invoices':
				return $this->lib->countRows( 'subscriptionlog' );
				break;
				
			case 'nexus_purchases':
				return $this->lib->countRows( 'subscriptionlog' );
				break;
				
			default:
				return $this->lib->countRows($action);
				break;
		}
	}
	
	public function checkConf( $action )
	{
		return false;
	}
	
	private function fixPostData( $post )
	{
		return $post;
	}
		
	// Probably will need redone for customer fields.
	private function convert_nexus_customers()
	{
		$main = array(
					'select'	=>	'*',
					'from'		=>	'subscriptionlog',
					'order'		=>	'subscriptionlogid ASC',
				);
		
		$loop = $this->lib->load( 'nexus_customers', $main );
		
		while ( $row = ipsRegistry::DB('hb')->fetch( $this->lib->queryRes ) )
		{	
			$this->lib->convertCustomer( $row['userid'], array() );
		}
		
		$this->lib->next();
	}
	
	private function convert_nexus_packages()
	{
		$main = array(	
					'select' 	=>	'*',
					'from' 		=>	'subscription',
					'order'		=>	'subscriptionid ASC',
				);

		
		$loop = $this->lib->load( 'nexus_packages', $main );
		
		while ( $row = ipsRegistry::DB('hb')->fetch( $this->lib->queryRes ) )
		{
			// Get local stuff
			$default_currency = $this->DB->buildAndFetch(
												array(
													'select'	=>	'conf_value, conf_default, conf_key',
													'from'		=>	'core_sys_conf_settings',
													'where'		=>	'conf_key="nexus_currency"'
												)
											);

			// Get remote stuff
			$title	=	ipsRegistry::DB('hb')->buildAndFetch( 
													array( 
														'select'	=>	'text', 
														'from'		=>	'phrase', 
														'where'		=>	"varname = 'sub{$row['subscriptionid']}_title'" 
													) 
												);
			
			$desc	=	ipsRegistry::DB('hb')->buildAndFetch( 
													array( 
														'select'	=>	'text', 
														'from'		=>	'phrase', 
														'where'		=>	"varname = 'sub{$row['subscriptionid']}_desc'" 
													) 
												);
			
			// Loop through costs
			$costs	=	unserialize($row['cost']);
			
			// Gotta set the cost, if it's not been filled we need to use default.
			$cost	=	empty($default_currency['conf_value']) ? $costs['0']['cost'][IPSText::mbstrtolower($default_currency['conf_default'])] : $costs['0']['cost'][IPSText::mbstrtolower($default_currency['conf_value'])];
			
			// set up renewal options
			$renewOpts	=	array(
								'term'	=>	$costs['0']['length'],
								'unit'	=>	IPSText::mbstrtolower($costs['0']['units']),
								'price'	=>	$cost,
								'add'	=>	FALSE,
							);
			
			// serialize for db
			$renew		=	serialize( array( $renewOpts ) );
			
			$save	=	array(
							'p_name'				=>	$title['text'],
							'p_desc'				=>	$desc['text'],
							'p_stock'				=>	-1,
							'p_store'				=>	1,
							'p_group'				=>	$this->convertGroup,
							'p_member_groups'		=>	'*',
							'p_base_price'			=>	$cost,
							'p_renew_options'		=>	$renew,
							'p_primary_group'		=>	$row['nusergroupid'],
							'p_return_primary'		=>	1,
							'p_type'				=>	'product',
						);
						
			$product	=	array(
								'p_subscription' => 1,
							);
			
			$this->lib->convertPackage( $row['subscriptionid'], $save, $discounts, $product );
		}
		
		$this->lib->next();
	}
	
	private function convert_nexus_invoices()
	{
		$main	=	array(	
						'select'	=>	'*',
						'from'		=>	'subscriptionlog',
						'order'		=>	'subscriptionlogid ASC',
					);
		
		$loop	=	$this->lib->load( 'nexus_invoices', $main );
		
		while ( $row = ipsRegistry::DB('hb')->fetch( $this->lib->queryRes ) )
		{
			if ( !empty($row['subscriptionid']) && ($row['subscriptionid'] != '0') )
			{
				// fetch our dooblydoo
				$pkg	=	$this->DB->buildAndFetch( 
											array( 
												'select'	=>	'*', 
												'from'		=>	'nexus_packages', 
												'where'		=>	"p_id={$this->lib->getLink($row['subscriptionid'], 'nexus_packages')}" 
											) 
										);
				// invoice
				$save	=	array(
								'i_status'	=>	$row['status'] ? 'paid' : 'expd',
								'i_title'	=>	$pkg['p_name'],
								'i_member'	=>	$row['userid'],
								'i_total'	=>	$pkg['p_base_price'],
								'i_date'	=>	$row['regdate'],
								'i_total'	=>	$pkg['p_base_price'],
							);
	
				$renew	=	unserialize($pkg['p_renew_options']);
				
				$items	=	array(
								//'act'			=>	'new',
								//'app'			=>	'nexus',
								//'type'			=>	$pkg['p_type'],
								//'cost'			=>	$pkg['p_base_price'],
								//'tax'			=>	$pkg['p_tax'],
								//'renew_term'	=>	$renew['term'],
								//'renew_units'	=>	$renew['unit'],
								//'renew_cost'	=>	$renew['price'],
								//'quantity'		=>	'',
								//'physical'		=>	'',
								//'shipping'		=>	array(),
								//'weight'		=>	0,
								//'itemName'		=>	$save['i_title'],
								'itemID'		=>	$row['subscriptionid'],
								//'extra'			=>	'',
								//'associated'	=>	'',
								//'assocBought'	=>	'',
								//'_taxSet'		=>	0,
								//'_tax'			=>	0,
							
							);
				//$this->lib->xmpDebug($items);
				// save the world
				$this->lib->convertInvoice( $row['subscriptionlogid'], $save, $items, array() );
			}
		}
		
		$this->lib->next();
	}
	
	private function convert_nexus_purchases()
	{
		$main	=	array(	
						'select'	=>	'*',
						'from'		=>	'subscriptionlog',
						'order'		=>	'subscriptionlogid ASC',
					);
		
		$loop = $this->lib->load ( 'nexus_purchases', $main );
		
		while ( $row = ipsRegistry::DB ( 'hb' )->fetch ( $this->lib->queryRes ) )
		{
			$save	= array (
				'ps_member'				=> $row['userid'],
				'ps_name'				=> "Converted Subscription #" . $row['subscriptionlogid'],
				'ps_active'				=> $row['expirydate'] > time() ? 1 : 0,
				'ps_cancelled'			=> 0,
				'ps_start'				=> $row['regdate'],
				'ps_expire'				=> $row['expirydate'],
				'ps_app'				=> 'nexus',
				'ps_type'				=> 'product',
				'ps_item_id'			=> $row['subscriptionid'],
				'ps_custom_fields'		=> serialize ( array ( ) ), //TODO
				'ps_invoice_pending'	=> !$row['status'] ? 1 : 0,
				'ps_original_invoice'	=> $row['subscriptionlogid'],
			);
			
			$this->lib->convertPurchase ( $row['subscriptionlogid'], $save, array() );
		}
		
		$this->lib->next ( );
	}
}

?>