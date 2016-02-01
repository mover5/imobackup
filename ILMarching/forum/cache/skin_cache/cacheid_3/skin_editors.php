<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_editors_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['ajaxEditBox'] = array('jsNotLoaded','ajaxerror','forceStd','showreason','appendedit','showappendedit','htmlstatus','showeditoptions');
$this->_funcHooks['editor'] = array('hasWrningInfo','jsNotLoaded','ismini','ismini','hasContent','hasType','ismini','hasHeight','hasMinimize','hasCallback','hasSaveKey','showEditor','hasToAcknowledge');
$this->_funcHooks['editorLoadJs'] = array('bypassCkEditor','hasimages','hasPastePlain');
$this->_funcHooks['mediaGenericWrapper'] = array('haswidth','hasheight','hasimage','hasdescription','genericmedia','hasrows');
$this->_funcHooks['sharedMedia'] = array('mediatabs');


}

/* -- ajaxEditBox --*/
function ajaxEditBox($post="", $pid=0, $error_msg="", $extraData) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_editors', $this->_funcHooks['ajaxEditBox'] ) )
{
$count_010bc6759aa1a6872ed06beec23ca3d3 = is_array($this->functionData['ajaxEditBox']) ? count($this->functionData['ajaxEditBox']) : 0;
$this->functionData['ajaxEditBox'][$count_010bc6759aa1a6872ed06beec23ca3d3]['post'] = $post;
$this->functionData['ajaxEditBox'][$count_010bc6759aa1a6872ed06beec23ca3d3]['pid'] = $pid;
$this->functionData['ajaxEditBox'][$count_010bc6759aa1a6872ed06beec23ca3d3]['error_msg'] = $error_msg;
$this->functionData['ajaxEditBox'][$count_010bc6759aa1a6872ed06beec23ca3d3]['extraData'] = $extraData;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- editor --*/
function editor($formField='post', $content='', $options=array(), $autoSaveData=array(), $warningInfo='', $acknowledge=FALSE) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_editors', $this->_funcHooks['editor'] ) )
{
$count_25d7ee20885049177cdb94372739ea02 = is_array($this->functionData['editor']) ? count($this->functionData['editor']) : 0;
$this->functionData['editor'][$count_25d7ee20885049177cdb94372739ea02]['formField'] = $formField;
$this->functionData['editor'][$count_25d7ee20885049177cdb94372739ea02]['content'] = $content;
$this->functionData['editor'][$count_25d7ee20885049177cdb94372739ea02]['options'] = $options;
$this->functionData['editor'][$count_25d7ee20885049177cdb94372739ea02]['autoSaveData'] = $autoSaveData;
$this->functionData['editor'][$count_25d7ee20885049177cdb94372739ea02]['warningInfo'] = $warningInfo;
$this->functionData['editor'][$count_25d7ee20885049177cdb94372739ea02]['acknowledge'] = $acknowledge;
}
$IPBHTML .= "<content><![CDATA[{$content}]]></content>";
return $IPBHTML;
}

/* -- editorLoadJs --*/
function editorLoadJs($options='') {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_editors', $this->_funcHooks['editorLoadJs'] ) )
{
$count_8cfcf5d68b8586d43dd3aef55662b244 = is_array($this->functionData['editorLoadJs']) ? count($this->functionData['editorLoadJs']) : 0;
$this->functionData['editorLoadJs'][$count_8cfcf5d68b8586d43dd3aef55662b244]['options'] = $options;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- editorSettings --*/
function editorSettings() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- mediaGenericWrapper --*/
function mediaGenericWrapper($rows, $pages, $app, $plugin) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_editors', $this->_funcHooks['mediaGenericWrapper'] ) )
{
$count_344241a2fd031d99f4be9f8d0ae15079 = is_array($this->functionData['mediaGenericWrapper']) ? count($this->functionData['mediaGenericWrapper']) : 0;
$this->functionData['mediaGenericWrapper'][$count_344241a2fd031d99f4be9f8d0ae15079]['rows'] = $rows;
$this->functionData['mediaGenericWrapper'][$count_344241a2fd031d99f4be9f8d0ae15079]['pages'] = $pages;
$this->functionData['mediaGenericWrapper'][$count_344241a2fd031d99f4be9f8d0ae15079]['app'] = $app;
$this->functionData['mediaGenericWrapper'][$count_344241a2fd031d99f4be9f8d0ae15079]['plugin'] = $plugin;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- sharedMedia --*/
function sharedMedia($tabs) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_editors', $this->_funcHooks['sharedMedia'] ) )
{
$count_9f2656466408d23fb6c30ded967b32ba = is_array($this->functionData['sharedMedia']) ? count($this->functionData['sharedMedia']) : 0;
$this->functionData['sharedMedia'][$count_9f2656466408d23fb6c30ded967b32ba]['tabs'] = $tabs;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- sharedMediaDefault --*/
function sharedMediaDefault() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>