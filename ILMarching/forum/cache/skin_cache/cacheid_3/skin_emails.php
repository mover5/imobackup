<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_emails_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['forward_form'] = array('language','lang','hasError','hasSubject','hasText','hasCaptcha');


}

/* -- boardRules --*/
function boardRules($title="",$body="") {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- forward_form --*/
function forward_form($title="",$text="",$lang="", $captchaHTML='', $msg='') {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_emails', $this->_funcHooks['forward_form'] ) )
{
$count_d3c2d75fb357f97183245f153fb04700 = is_array($this->functionData['forward_form']) ? count($this->functionData['forward_form']) : 0;
$this->functionData['forward_form'][$count_d3c2d75fb357f97183245f153fb04700]['title'] = $title;
$this->functionData['forward_form'][$count_d3c2d75fb357f97183245f153fb04700]['text'] = $text;
$this->functionData['forward_form'][$count_d3c2d75fb357f97183245f153fb04700]['lang'] = $lang;
$this->functionData['forward_form'][$count_d3c2d75fb357f97183245f153fb04700]['captchaHTML'] = $captchaHTML;
$this->functionData['forward_form'][$count_d3c2d75fb357f97183245f153fb04700]['msg'] = $msg;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>