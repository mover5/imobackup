<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_reports_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['reportsIndex'] = array('statuses','isUnread','statusesLoop','indexReportUrl','reports','noviewall','hasPages','indexHasReports','accessACP');
$this->_funcHooks['viewReport'] = array('setStatus','statuses','viewReports','canJoinPm','handlePmSpecial','statusesLoop','hasReports','disablelightbox');


}

/* -- basicReportForm --*/
function basicReportForm($name="", $url="", $extra_data="") {
$IPBHTML = "";
$IPBHTML .= "<postingForm>
	<action><![CDATA[{$this->settings['base_url']}app=core&amp;module=reports&amp;rcom={$this->request['rcom']}&amp;send=1]]></action>
	<formHash><![CDATA[{$this->member->form_hash}]]></formHash>
	<topicID>{$this->request['tid']}</topicID>
	<postID>{$this->request['pid']}</postID>
	<forumID>{$this->request['fid']}</forumID>
	</postingForm>";
return $IPBHTML;
}

/* -- reportsIndex --*/
function reportsIndex($reports=array(), $acts="", $pages="", $statuses=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_reports', $this->_funcHooks['reportsIndex'] ) )
{
$count_eb3fc5fc88b0f175c9444ea3a0d24700 = is_array($this->functionData['reportsIndex']) ? count($this->functionData['reportsIndex']) : 0;
$this->functionData['reportsIndex'][$count_eb3fc5fc88b0f175c9444ea3a0d24700]['reports'] = $reports;
$this->functionData['reportsIndex'][$count_eb3fc5fc88b0f175c9444ea3a0d24700]['acts'] = $acts;
$this->functionData['reportsIndex'][$count_eb3fc5fc88b0f175c9444ea3a0d24700]['pages'] = $pages;
$this->functionData['reportsIndex'][$count_eb3fc5fc88b0f175c9444ea3a0d24700]['statuses'] = $statuses;
}
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- statusIcon --*/
function statusIcon($img, $width, $height) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- viewReport --*/
function viewReport($options=array(), $reports=array(), $comments=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_reports', $this->_funcHooks['viewReport'] ) )
{
$count_60fa91a7d9f77e7cbbd3aaf39de3c4de = is_array($this->functionData['viewReport']) ? count($this->functionData['viewReport']) : 0;
$this->functionData['viewReport'][$count_60fa91a7d9f77e7cbbd3aaf39de3c4de]['options'] = $options;
$this->functionData['viewReport'][$count_60fa91a7d9f77e7cbbd3aaf39de3c4de]['reports'] = $reports;
$this->functionData['viewReport'][$count_60fa91a7d9f77e7cbbd3aaf39de3c4de]['comments'] = $comments;
}
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>