<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 2               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_help_2 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['helpShowSection'] = array('notajax','isajax','notajax','notajax','isajax','notajax');
$this->_funcHooks['helpShowTopics'] = array('helpfiles','helpfiles');


}

/* -- helpShowSection --*/
function helpShowSection($one_text="",$two_text="",$three_text="", $text) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_help', $this->_funcHooks['helpShowSection'] ) )
{
$count_d50fa7c12630bd935dc09c00a3672ff9 = is_array($this->functionData['helpShowSection']) ? count($this->functionData['helpShowSection']) : 0;
$this->functionData['helpShowSection'][$count_d50fa7c12630bd935dc09c00a3672ff9]['one_text'] = $one_text;
$this->functionData['helpShowSection'][$count_d50fa7c12630bd935dc09c00a3672ff9]['two_text'] = $two_text;
$this->functionData['helpShowSection'][$count_d50fa7c12630bd935dc09c00a3672ff9]['three_text'] = $three_text;
$this->functionData['helpShowSection'][$count_d50fa7c12630bd935dc09c00a3672ff9]['text'] = $text;
}
$IPBHTML .= "" . ((!$this->request['xml']) ? ("
<div class='topic_controls'>
	<ul class='topic_buttons'>
		<li><a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=help", "public",'' ), "", "" ) . "\" title=\"{$this->lang->words['help_back_list_title']}\">{$this->lang->words['help_return_list']}</a></li>
	</ul>
</div>
") : ("")) . "
" . (($this->request['xml']) ? ("
<br />
") : ("")) . "
" . ((!$this->request['xml']) ? ("
	<h1 class='ipsType_pagetitle'>{$one_text}: {$three_text}</h1>
") : ("
	<h1 class='ipsType_subtitle'>{$one_text}: {$three_text}</h1>
")) . "
<br />
<div class='row2 help_doc ipsPad bullets'>
	{$text}
</div>
<br />";
return $IPBHTML;
}

/* -- helpShowTopics --*/
function helpShowTopics($one_text="",$two_text="",$three_text="",$rows) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_help', $this->_funcHooks['helpShowTopics'] ) )
{
$count_bf70b010319cde83ec5635e745aa9b64 = is_array($this->functionData['helpShowTopics']) ? count($this->functionData['helpShowTopics']) : 0;
$this->functionData['helpShowTopics'][$count_bf70b010319cde83ec5635e745aa9b64]['one_text'] = $one_text;
$this->functionData['helpShowTopics'][$count_bf70b010319cde83ec5635e745aa9b64]['two_text'] = $two_text;
$this->functionData['helpShowTopics'][$count_bf70b010319cde83ec5635e745aa9b64]['three_text'] = $three_text;
$this->functionData['helpShowTopics'][$count_bf70b010319cde83ec5635e745aa9b64]['rows'] = $rows;
}

if ( ! isset( $this->registry->templateStriping['help'] ) ) {
$this->registry->templateStriping['help'] = array( FALSE, "row1","row2");
}
$IPBHTML .= "" . $this->registry->getClass('output')->addJSModule("help", "0" ) . "
<p class='message unspecific'>{$two_text}</p>
<h2 class='maintitle'>{$this->lang->words['help_topics']}</h2>
<div class='generic_bar'></div>
<ol id='help_topics'>
		" . ((count($rows)) ? ("".$this->__f__a3bbaf5fdc575f50a0c5b88e475e9684($one_text,$two_text,$three_text,$rows)."	") : ("
		<li class='no_messages'>{$this->lang->words['no_help_topics']}</li>
	")) . "
</ol>";
return $IPBHTML;
}


function __f__a3bbaf5fdc575f50a0c5b88e475e9684($one_text="",$two_text="",$three_text="",$rows)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $rows as $entry )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<li class='" .  IPSLib::next( $this->registry->templateStriping["help"] ) . " helpRow'>
			<h3><a href=\"" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=help&amp;do=01&amp;HID={$entry['id']}", "public",'' ), "", "" ) . "\" title=\"{$this->lang->words['help_read_document']}\">{$entry['title']}</a></h3>
			<p>
				{$entry['description']}
			</p>
		</li>
		
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>