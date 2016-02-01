<?php
/**
 * IPS Converters
 * IP.Blog 2.0 Converters
 * Admin CP Skin
 * Last Update: $Date: 2013-04-19 08:16:08 -0400 (Fri, 19 Apr 2013) $
 * Last Updated By: $Author: AndyMillne $
 *
 * @package		IPS Converters
 * @author 		Mark Wade
 * @copyright	(c) 2009 Invision Power Services, Inc.
 * @link		http://external.ipslink.com/ipboard30/landing/?p=converthelp
 * @version		$Revision: 838 $
 */


class cp_skin_convert extends output
{

/**
* Prevent our main destructor being called by this class
*/
function __destruct()
{
}

function convertCSS() {

	$IPBHTML = "";
	//--starthtml--//

	$IPBHTML .= <<<HTML

<style type='text/css' media='all'>
	@import url({$this->settings['skin_app_url']}style.css);
</style>

HTML;

	//--endhtml--//
	return $IPBHTML;
}

//===========================================================================
// Header
//===========================================================================
function convertHeader($text) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$text}</h2>
</div>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Footer
//===========================================================================
function convertFooter() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<br />
HTML;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// App List
//===========================================================================
function convertShowSoftware($extra) {

	$extraHTML = "";
	$count = 0;
	foreach( $extra as $k => $v )
	{
		if ( $count % 3 == 0 )
		{
			$extraHTML .= "</tr><tr class='row2'>";
		}

		$options = "";

		if ( count( $v['software'] ) )
		{
			foreach( $v['software'] as $a => $b )
			{
				$options .= "<option value='{$a}'>{$b}</option>";
			}
		}

		if ( $v['third_party'] != true )
		{
			if ( $v['enabled'] )
			{
				$extraHTML .= <<<EOF
			<td style='width: 33%; max-width: 33%;'>
				<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=setup' method='post'>
					<input type='hidden' name='do' value='info' />
					<input type='hidden' name='sw' value='{$k}' />
					<div class='app_wrapper app_wrapper_{$k}'>
						<span class='convert_app {$k}'>
							<img src='{$this->settings['skin_app_url']}images/applications/{$k}.png' alt='' />
						</span>
						<span class='convert_app_title'><strong>{$v['title']}</strong>Convert from <br />&nbsp;<select name='choice'>{$options}</select>
							<div class='convert_go'>
								<input type='submit' value='Select' />
							</div>
						</span>
					</div>
				</form>
			</td>
EOF;
			}
			else
			{
				$freeApps = array('calendar', 'chat', 'tracker', 'subscriptions'); // adding chat, tracker and subscriptions just in case
				if ( !in_array( $k, $freeApps ) )
				{
					$verbiage	=	'Purchase';
					$appURL = 'http://invisionpower.com/apps/'.$k.'/';
				}
				else 
				{
					$verbiage	=	'Download';
					$appURL = 'http://invisionpower.com/clients/index.php?app=downloads';
				}
				
				$extraHTML .= "
							<td style='width: 33%; max-width: 33%;'>
								<div class='app_wrapper disabled'>
									<span class='convert_app'>
										<img src='{$this->settings['skin_app_url']}images/applications/{$k}.png' alt='' />
									</span>
									<span class='convert_app_title'>
										<strong>{$v['title']}</strong>
										<em>Application not installed</em>
										<div class='convert_go'>
											<input type='button' value='{$verbiage} {$v['title']}' onclick='window.open(\"".$appURL."\")' />
										</div>
									</span>
								</div>
							</td>";
			}
		}

		$count++;
	}

	$IPBHTML = "";
	//--starthtml--//

	$IPBHTML .= <<<HTML

<div class='section_title'>
     <h2>Welcome to the IPS Converters</h2>
</div>

<div class='acp-box ipsPadding row2'>
<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
<table class='form_table double_pad no_striping'>
	<tr>
		<th colspan='3'>Please select the product which you would like to convert from, from the selection below.</th>
	</tr>
	<tr class='row2'>
		{$extraHTML}
	</tr>
</table>
</div>
<br />				
<div class="information-box">
<b>Need Help?</b>
<br />
Read our <a href="http://www.invisionpower.com/support/guides/_/importing-and-conversion/convert-to-ips-community-suite-r37">easy to follow conversion guide.</a>
</div><br />
				

HTML;

		//--endhtml--//
		return $IPBHTML;
}


//===========================================================================
// App List
//===========================================================================
function convertShowSoftwareOld($extra) {


$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class="information-box">
<b>Need Help?</b>
<br />
Read our <a href="http://www.invisionpower.com/support/guides/_/importing-and-conversion/convert-to-ips-community-suite-r37">easy to follow conversion guide.</a>
</div><br />

<div class='information-box'>
<h3>Select which application you would like to convert to:</h3>
<ul><br />
	<li><img src="{$this->settings['skin_acp_url']}/images/applications/core.png" /> <a href='{$this->settings['base_url']}module=setup&amp;section=setup&amp;do=save&amp;sw=board'>IP.Board</a></li><br />
	{$extra}
</ul>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// App List
//===========================================================================
function convertApp($key, $name) {

$image = '';
//if (file_exists(IPS_ROOT_PATH.'applications/ips/'.$key.'/skin_cp/appIcon.png'))
if (file_exists ( IPSLib::getAppDir ( 'convert') .'/skin_cp/images/applications/'.$key.'.png' ) )
{
	$image = '<img src="'.$this->settings['skin_app_url'].'images/applications/'.$key.'.png" />';
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<li>{$image} <a href='{$this->settings['base_url']}module=setup&amp;section=setup&amp;do=save&amp;sw={$key}'>{$name}</a></li><br />
HTML;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Convertor Options - PART ONE
//===========================================================================
function convertShowOptions1($options) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='acp-box'>
	<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=setup' method='post'>
		<input type='hidden' name='do' value='info'>
		<input type='hidden' name='sw' value='{$this->request['sw']}'>
		<input type='hidden' name='parent' value='0'>
		<table class='ipsTable double_pad'>
			<tr>
				<th colspan='2'>We need some more information...</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Current software</strong></td>
				<td class='field_field'>
					<select name='choice'>
						{$options}
					</select>
				</td>
			</tr>
			<tr>
				<td class='field_title'>
					<strong class='title'>ID</strong><br />
				</td>
				<td class='field_field'>
					<input name='app_name' /><br />
					<div class='desctext'>Any name that will identify the application.<br />For example: <em>old_forums</em></div>
				</td>
			</tr>
		</table>
		<div class='acp-actionbar'><input type='submit' value='Continue' class='button'></div>
	</form>
</div>
<br /><br />

<div class='warning'>
	<h4><img src='{$this->settings['skin_acp_url']}/_newimages/icons/warning.png' alt='' />&nbsp; Warning</h4>
	It is important to note before continuing that converting is not an exact science. Many factors can affect the final outcome and small oddities are to be expected following the conversion. Our technicians will be happy to assist you resolve these issues, but you should be aware that you may need to allow for extra downtime following the conversion while these issues are addressed.
</div>
<br /><br />

<div class='information-box'>
	<h4><img src='{$this->settings['skin_acp_url']}/_newimages/icons/help.png' alt='' />&nbsp; Information</h4>
	If the software you want is listed, but you are running an older version than what is available, you should first upgrade to the version listed here before running the converters.
</div>
<br /><br />


HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Converter Options - PART TWO
//===========================================================================
function convertShowOptions2($apps=array(), $product='', $inError=false, $errorMessage='') {

	if ( defined( 'CONVERTERS_TRUNCATE_MODE' ) ) {
		$displayCheck = ! isset( $info['merge'] ) ? "style='display:none;'" : '';
		$mergeCheck = $this->request['app_merge'] ? "checked='checked' " : '';
	}
	else
	{
		$displayCheck	=	"style='display:none;'";
		$mergeCheck		=	"checked='checked'";
	}
	
	if ( $product === '' )
	{
		$product = 'remote';
	}
	
	if ( $inError )
	{
		$errorMessage = "<div class='warning convert'>{$errorMessage}</div>";
	}
	
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
<h2>Converting {$product} to {$apps[$this->request['sw']]['title']}</h2>
				
<span class='convert_app {$this->request['sw']}' style='float:right; padding:5px; margin:4px; display:inline-block; z-index:10000000; margin-bottom:-25px;'>
<img src='{$this->settings['skin_app_url']}images/applications/{$this->request['sw']}.png' alt='' />
</span>
</div>

{$errorMessage}

<div class='acp-box'>
	<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=setup' method='post'>
		<input type='hidden' name='do' value='convert' />
		<input type='hidden' name='sw' value='{$this->request['sw']}' />
		<input type='hidden' name='parent' value='{$this->request['parent']}' />
		<input type='hidden' name='choice' value='{$this->request['choice']}' />
		<input type='hidden' name='app_name' value='{$this->request['app_name']}' />
		<table class='form_table double_pad'>
			<tr><th colspan='2'>Step 1: Database Information</th></tr>
			<tr>
				<!-- <td class='field_title'><strong class='title'>Database Driver</strong></td>
				<td class='field_field'>
					<select name='hb_sql_driver' />
						<option value='mysql'>MySQL</option>
					</select>
				</td> -->
				<input type='hidden' name='hb_sql_driver' value='mysql' />
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Host</strong></td>
				<td class='field_field'><input name='hb_sql_host' class='input_text' value='{$this->settings['sql_host']}' /><br />
				<span class='desctext'>This is usually "localhost" or "127.0.0.1". <em><b>If unsure you can leave this alone.</b></em></span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Username</strong></td>
				<td class='field_field'><input name='hb_sql_user' class='input_text' value='{$this->settings['sql_user']}' /><br />
				<span class='desctext'>This is the database username of the <b>{$product}</b> installation. This information can usually be found in the config file for {$product}.</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Password</strong></td>
				<td class='field_field'><input name='hb_sql_pass' class='input_text' value='{$this->settings['sql_pass']}' /><br />
				<span class='desctext'>This is the database password for the <b>{$product}</b> installation. This information can usually be found in the config file for {$product}.</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Name</strong></td>
				<td class='field_field'><input name='hb_sql_database' class='input_text' /><br />
				<span class='desctext'>This is the database name for the <b>{$product}</b> installation. This information can usually be found in the config file for {$product}.</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Table Prefix</strong></td>
				<td class='field_field'><input name='hb_sql_tbl_prefix' class='input_text' /><br />
				<span class='desctext'>This is the database table prefix for the <b>{$product}</b> installation. This information can usually be found in the config file for {$product}. <br /><em>You <b>can</b> leave this blank if you are unsure and the converter will alert you of any errors.</em></span>
				</td>
			</tr>
			<tr>
				<th colspan='2'>Advanced Configuration <a href='javascript:void(0)' id='toggleAdvancedOpts'>(Click to view)</a></th>
    		</tr>
    		<script>
    			jQ(document).ready(function() {
				jQ('#advancedOpts').hide();
	    			jQ('#toggleAdvancedOpts').click(function() {
					  jQ('#advancedOpts').toggle("slow");
					});
				});
			</script>
				<tr id='advancedOpts'>
					<td class='field_title'><strong class='title'>Database Charset</strong></td>
					<td class='field_field'>
						<input name='hb_sql_charset' value='UTF8' class='input_text' /><br />
						<div class='desctext'>
							In almost all cases, this can be left alone.
						</div>
					</td>
				</tr>
				<tr {$displayCheck}>
					<td class='field_title'><strong class='title'>Merge Databases</strong></td>
					<td class='field_field'>
						<input name='app_merge' type='checkbox' value='1' {$disableCheck}{$mergeCheck} /><br />
						<div class='desctext'>If you want to keep existing content on IP.Board, this must be ticked. <strong>This option cannot be changed mid-conversion.</strong><br /><span style='color:red;font-weight:bold;'>If you <u>do not</u> check this all local data will be <u>removed</u></span>.</div>
					</td>
				</tr>
		</table>
		<div class='acp-actionbar'><input type='submit' value='Continue' class='button'></div>
	</form>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Convertor Options - Custom
//===========================================================================
function convertShowOptionsCustom($fields) {

$rows = '';
foreach($fields as $k => $v)
{
	$rows .= "<tr>
				<td class='field_title'><strong class='title'>{$v}</strong></td>
				<td class='field_field'><input name='{$k}' /></td>
			</tr>";
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='acp-box'>
	<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=setup' method='post'>
		<input type='hidden' name='do' value='convert' />
		<input type='hidden' name='sw' value='{$this->request['sw']}' />
		<input type='hidden' name='parent' value='{$this->request['parent']}' />
		<input type='hidden' name='choice' value='{$this->request['choice']}' />
		<input type='hidden' name='app_name' value='{$this->request['app_name']}' />
		<input type='hidden' name='custom' value='1' />
		<table class='ipsTable double_pad'>
			<tr><th colspan='2'>We need some more information...</th></tr>
			{$rows}	
		</table>
		<div class='acp-actionbar'><input type='submit' value='Continue' class='button' /></div>
	</form>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}



//===========================================================================
// Who's your daddy?
//===========================================================================
function convertAskForParent($options, $hidden) {

$IPBHTML = "";
//--starthtml--//
if ( !empty ( $options ) ):
$IPBHTML .= <<<HTML

<div class='acp-box'>
	<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=setup' method='post'>
		<input type='hidden' name='do' value='info'>
		<input type='hidden' name='sw' value='{$this->request['sw']}'>
		<input type='hidden' name='app_name' value='{$this->request['app_name']}'>
		<input type='hidden' name='hb_sql_driver' value='{$this->request['hb_sql_driver']}'>
		<input type='hidden' name='hb_sql_host' value='{$this->request['hb_sql_host']}'>
		<input type='hidden' name='hb_sql_user' value='{$this->request['hb_sql_user']}'>
		<input type='hidden' name='hb_sql_pass' value='{$this->request['hb_sql_pass']}'>
		<input type='hidden' name='hb_sql_database' value='{$this->request['hb_sql_database']}'>
		<input type='hidden' name='hb_sql_tbl_prefix' value='{$this->request['hb_sql_tbl_prefix']}'>
		<input type='hidden' name='hb_sql_charset' value='{$this->request['hb_sql_charset']}'>
		{$hidden}
		<input type='hidden' name='choice' value='{$this->request['choice']}'>
		<table class='ipsTable double_pad'>
			<tr><th>We need some more information...</th></tr>
			<tr><td>Your choice requires a 'parent' application. Select the parent application:
					<select name='parent'>
						{$options}
					</select>
				</td>
			</tr>
		</table>
		<div class='acp-actionbar'><input type='submit' value='Continue' class='button' /></div>
	</form>
</div>
<br /><br />

HTML;
else:
$error = <<<HTML
Your choice requires a parent application. Please convert the parent application prior to converting the child application.<br /><br />
<em>For example, if you are converting vBulletin Blog to IP.Blog, please ensure that you have already performed your main vBulletin conversion.</em>
HTML;
$IPBHTML .= $this->convertError($error);
endif;
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Continue a conversion that has already been started
//===========================================================================
function convertContinue($options) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='acp-box'>
	<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=continue' method='post'>
		<input type='hidden' name='do' value='save'>
		<table class='ipsTable double_pad'>
			<tr><th>Select the conversion you would like to continue...</th></tr>
			<tr>
				<td>
					<select name='choice'>
						{$options}
					</select>
				</td>
			</tr>
		</table>
		<div class='acp-actionbar'><input type='submit' value='Continue' class='button'></div>
	</form>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Convertor Options - Row
//===========================================================================
function convertAddOption($info) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<option value='{$info['key']}'>{$info['name']}</option>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Convertor Options - Error
//===========================================================================
function convertError($error) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='warning'>
	<h4>Error</h4>
	{$error}
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Convertor Menu - Wrapper
//===========================================================================
function convertMenu($rows, $appinfo) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<script type='text/javascript'>
	function calculate( key, total )
	{
		url = '{$this->settings['base_url']}&app=convert&app=convert&module=setup&section=extra&do=count&newcycles='+$( 'input-' + key ).getValue()+'&total='+total;

		if ( ! isNumber( $( 'input-' + key ).getValue() ) )
		{
			$( 'cycles-' + key ).update( "<span style='color:red;'>??</span>" );
			return;
		}

		new Ajax.Request(
			url,
			{
				method: 'get',
				onSuccess: function(s)
				{
					if ( s.responseText != 'error' )
					{
						$( 'cycles-' + key ).update( s.responseText );
					}
				}
			}
		);
	}

	function isNumber( number )
	{
		return number.match( /^[\d]+?$/ );
	}


</script>

<div class='acp-box'>
	<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
	<table class='ipsTable'>
		<tr>
			<th>Type</th>
			<th>Local Rows</th>
			<th>Source Rows</th>
			<th>Status</th>
			<th>Per Cycle</th>
			<th>Empty local data?</th>
			<th>Go</th>
		</tr>
		{$rows}
	</table>
HTML;
if ( $this->request['module'] == 'board' ):
$IPBHTML .= <<<HTML
	<div class="acp-actionbar">
		<form action="{$this->settings['base_url']}module=setup&amp;section=finalize&sw={$this->request['section']}" method="POST">
			<input type="submit" value="Finalize Conversion" class="button" onclick='return window.confirm("WARNING: Finalizing a conversion will recache everything and lock the converters. You should only run this once the conversion is finalized. Do you want to proceed?");' />
		</form>
	</div>
HTML;
endif;
$IPBHTML .= <<<HTML
</div>
<br /><br />

HTML;
	
if ($appinfo && $this->request['module'] != 'board')
{

$IPBHTML .= <<<HTML

	<div class='information-box'>
		<h4>When you're finished...</h4>
		{$appinfo}
	</div>
	<br /><br />

HTML;

}

$IPBHTML .= <<<HTML

<!-- <div class='warning'>
	<h4>Lock</h4>
	<strong>Make sure that when you have finished, you <a href='{$this->settings['base_url']}module=setup&amp;section=lock'>lock the system</a>.</strong>
</div>
<br /><br /> -->

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Convertor Menu - Rows
//===========================================================================
function convertMenuRow($app, $info, $rows, $status, $button, $actualrows=false) {

$cycles = round($rows / $info['cycle']);
$cycles = ($cycles == 0) ? 1 : $cycles;

if ($actualrows === false)
{
	$actualrows = $rows;
}

if ( !$app['app_merge'] && $info['key'] != 'groups' && $info['key'] != 'pfields' )
{
	$checkbox = "<input type='checkbox' name='emptya' checked='checked' value='1' disabled='disabled' /><input type='hidden' name='empty' value='1' />";
}
else if ( ! $app['app_merge'] )
{
	$checkbox = "<input type='checkbox' name='emptya' value='0' disabled='disabled' /><input type='hidden' name='empty' value='0' />";
}
else
{
	$checkbox = "<input type='checkbox' name='empty' value='1' />";
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<form action='{$this->settings['base_url']}module={$info['app']}&amp;section={$info['section']}' method='post'>
	<input type='hidden' name='do' value='{$info['key']}'>
	<input type='hidden' name='total' value='{$rows}'>
	<tr>
		<td><img src='{$this->settings['skin_app_url']}/images/{$info['key']}.png' /> {$info['name']}</td>
		<td>{$info['rows']}</td>
		<td>{$actualrows}</td>
		<td>{$status}</td>
		<td><input name='cycle' value='{$info['cycle']}' size='5' onKeyUp="calculate( '{$info['key']}', '{$rows}' );" id='input-{$info['key']}' /> <span id='cycles-{$info['key']}'>{$cycles}</span> cycles</td>
		<td>{$checkbox}</td>
		<td>{$button}</td>
	</tr>
</form>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Convertor Menu - Button - Enabled
//===========================================================================
function convertMenuRowButtonOn() {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<input type='submit' value='Convert' class='button'>
<input type='hidden' name='info' value='1' />
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Convertor Menu - Button - Enabled - Convert again
//===========================================================================
function convertMenuRowButtonAgain($conf=false) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<input type='submit' value='Convert Again' class='button' onclick='return confirm("Are you sure you wish to reconvert this item?")' />
<input type='hidden' name='rc' value='1' />
HTML;

if ($conf)
{
$IPBHTML .= <<<HTML
<input type='checkbox' name='info' checked='checked' /> Reconfigure
HTML;
}


//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Convertor Menu - Button - Disabled
//===========================================================================
function convertMenuRowButtonOff($pres) {

$spres = implode(', ', $pres);

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<input type='button' class='button redbutton' value='Cannot Convert Yet' onclick='alert("You must first convert {$spres}")'>
HTML;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Completed Section Screen
//===========================================================================
function convertComplete($message, $extrarows=array()) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='acp-box'>
	<h3>{$message}</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=switch' method='post'>
		<table class='ipsTable double_pad'>
HTML;
foreach($extrarows as $content)
{
	if (!empty($content))
	{
$IPBHTML .= <<<HTML
			<tr><td>{$content}</td></tr>
HTML;
	}
}
$IPBHTML .= <<<HTML
		</table>
		<div class='acp-actionbar'><input type='submit' value='Continue' class='button' /></div>
	</form>
</div>
<br /><br />
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Ask for more info - wrapper
//===========================================================================
function convertMoreInfo($rows, $text_header, $input_header) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='acp-box'>
	<h3>We need some more information...</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module={$this->request['module']}&amp;section={$this->request['section']}&amp;do={$this->request['do']}' method='post'>
		<input type='hidden' name='info' value='1' />
		<input type='hidden' name='cycle' value='{$this->request['cycle']}' />
		<input type='hidden' name='total' value='{$this->request['total']}'>
		<table class='ipsTable double_pad'>
			<tr>
				<th>{$text_header}</th>
				<th>{$input_header}</th>
			</tr>
			{$rows}	
		</table>
		<div class='acp-actionbar'><input type='submit' value='Continue' class='button'></div>
	</form>
</div>
<br /><br />
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Ask for more info - row
//===========================================================================
function convertMoreInfoRow($text, $input) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<tr>
	<td class='field_title'><strong class='title'>{$text}</strong></td>
	<td class='field_field'>{$input}</td>
</tr>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Hint Box
//===========================================================================
function convertHint($hint) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='information-box'>
	<h4>Information</h4>
	{$hint}
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Manage Apps - Table
//===========================================================================
function convertAppTable($rows) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class="information-box">
<b>Need Help?</b>
<br />
Read our <a href="http://www.invisionpower.com/support/guides/_/importing-and-conversion/convert-to-ips-community-suite-r37">easy to follow conversion guide.</a>
</div><br />

<div class='acp-box'>
	<h3>Conversions</h3>
	<table class='ipsTable'>
		<tr>
			<th>ID</th>
			<th>Application</th>
			<th class='col_buttons'>&nbsp;</th>
		</tr>
		{$rows}
	</table>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Manage Apps - Row
//===========================================================================
function convertAppTableRow($info) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<tr class='ipsControlRow'>
	<td><span class='larger_text'>{$info['name']}</span></td>
	<td>{$info['app_key']}</td>
	<td>
		<ul class='ipsControlStrip'>
			<li class='i_edit'><a href='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=manage&amp;do=edit&amp;id={$info['app_id']}'>Edit</a></li>
			<li class='i_delete'><a href='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=manage&amp;do=delete&amp;id={$info['app_id']}' onclick='return confirm("Are you sure you wish to delete this conversion?")'>Delete</a></li>
		</ul>
	</td>
</tr>
HTML;
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Edit App
//===========================================================================
function convertEditApp($info) {

$mysql = ($info['db_driver'] == 'mysql') ? "<option value='mysql' selected='selected'>MySQL</option>" : "<option value='mysql'>MySQL</option>";
$mssql = ($info['db_driver'] == 'mssql') ? "<option value='mssql' selected='selected'>MSSQL</option>" : "<option value='mssql'>MSSQL</option>";

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class="information-box">
<b>Need Help?</b>
<br />
Read our <a href="http://www.invisionpower.com/support/guides/_/importing-and-conversion/convert-to-ips-community-suite-r37">easy to follow conversion guide.</a>
</div><br />

<div class='acp-box'>
	<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=manage' method='post'>
		<input type='hidden' name='do' value='edit_save' />
		<input type='hidden' name='id' value='{$this->request['id']}' />
		<table class='ipsTable double_pad'>
			<tr><th colspan='2'>We need some more information...</th></tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Driver</strong></td>
				<td class='field_field'><select name='hb_sql_driver' />{$mysql}{$mssql}</select></td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Host</strong></td>
				<td class='field_field'><input name='hb_sql_host' class='input_text' value='{$info['db_host']}' /></td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Username</strong></td>
				<td class='field_field'><input name='hb_sql_user' class='input_text'  value='{$info['db_user']}' /></td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Password</strong></td>
				<td class='field_field'><input name='hb_sql_pass' class='input_text' value='{$info['db_pass']}' /></td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Name</strong></td>
				<td class='field_field'><input name='hb_sql_database' class='input_text' value='{$info['db_db']}' /></td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Database Table Prefix</td>
				<td class='field_field'><input name='hb_sql_tbl_prefix' class='input_text' value='{$info['db_prefix']}' /></td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='Continue' class='button' />
		</div>
	</form>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}

function convertEditAppCustom($custom) {

$rows = '';
foreach($custom as $k => $v)
{
	$rows .= "<tr>
				<td class='field_title'><strong class='title'>{$v}</strong></td>
				<td class='field_field'><input name='{$k}' /></td>
			</tr>";
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='acp-box'>
	<h3><img src='{$this->settings['skin_app_url']}images/ips.png' height='24' width='24' alt='IPS, Inc.' />Converting to IPS</h3>
	<form action='{$this->settings['base_url']}&amp;app=convert&amp;module=setup&amp;section=manage' method='post'>
		<input type='hidden' name='do' value='edit_save' />
		<input type='hidden' name='id' value='{$this->request['id']}' />
		<input type='hidden' name='custom' value='1' />
		<table class='ipsTable double_pad'>
			<tr><th colspan='2'>We need some more information...</th></tr>
			{$rows}
		</table>
		<div class='acp-actionbar'><input type='submit' value='Continue' class='realbutton'></div>
	</form>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}


}

?>