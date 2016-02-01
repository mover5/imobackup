<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_ipchat_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['ajaxNewUser'] = array('isMember3','isMember4','formatnameajax','hasname');
$this->_funcHooks['chatRoom'] = array('useprefix','usesuffix','formatname','forumidmap','isIgnoringChats','ignoredprivatechatters','badwordsloop','fixgroupname','useprefix','useprefix','grouploop','isMember','isMember2','formatname','hasname','ismoderatormenu','isprivmenu','isignoringuser','nokickself','isMember','cookiesound','hasignoredprivate','hasbadwords','notInPopup','soundon','notInPopup');
$this->_funcHooks['chatRules'] = array('showPopup');
$this->_funcHooks['chatUnbanModcp'] = array('chatBanned');


}

/* -- ajaxNewUser --*/
function ajaxNewUser($data=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_ipchat', $this->_funcHooks['ajaxNewUser'] ) )
{
$count_1420f76455b564d8929da89fe831d6c0 = is_array($this->functionData['ajaxNewUser']) ? count($this->functionData['ajaxNewUser']) : 0;
$this->functionData['ajaxNewUser'][$count_1420f76455b564d8929da89fe831d6c0]['data'] = $data;
}
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- chatRoom --*/
function chatRoom($options=array(), $chatters=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_ipchat', $this->_funcHooks['chatRoom'] ) )
{
$count_dedd0b31c5ead9123bd1a892597e5d58 = is_array($this->functionData['chatRoom']) ? count($this->functionData['chatRoom']) : 0;
$this->functionData['chatRoom'][$count_dedd0b31c5ead9123bd1a892597e5d58]['options'] = $options;
$this->functionData['chatRoom'][$count_dedd0b31c5ead9123bd1a892597e5d58]['chatters'] = $chatters;
}
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- chatRules --*/
function chatRules($rules) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_ipchat', $this->_funcHooks['chatRules'] ) )
{
$count_b0f96effb6d33b5d1a393f68685ada88 = is_array($this->functionData['chatRules']) ? count($this->functionData['chatRules']) : 0;
$this->functionData['chatRules'][$count_b0f96effb6d33b5d1a393f68685ada88]['rules'] = $rules;
}
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- chatUnbanModcp --*/
function chatUnbanModcp($member) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_ipchat', $this->_funcHooks['chatUnbanModcp'] ) )
{
$count_518139623dacf73bd598856168ecea88 = is_array($this->functionData['chatUnbanModcp']) ? count($this->functionData['chatUnbanModcp']) : 0;
$this->functionData['chatUnbanModcp'][$count_518139623dacf73bd598856168ecea88]['member'] = $member;
}
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- newWindow --*/
function newWindow() {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- tabCount --*/
function tabCount($count) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- whoschatting_empty --*/
function whoschatting_empty() {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- whoschatting_show --*/
function whoschatting_show($total="",$names="") {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>