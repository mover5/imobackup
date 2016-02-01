<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_messaging_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['messengerDisabled'] = array('notByAdmin');
$this->_funcHooks['messengerTemplate'] = array('isMemberPartOpen','isMemberPartFloat','isMemberPartClose','userIsStarter','lastReadTime','messageIsDeleted','notification','blockUserLink','unbanUserLink','systemMessage','topicUnavailable','userIsBanned','userIsActive','participants','protectedFolder','allFolder','unprotectedFolder','dirs','PMDisabled','changeNotifications','unlimitedInvites','inviteMoreParticipants','hasParticipants','myDirectories','almostFull','storageBar','inlineError');
$this->_funcHooks['sendNewPersonalTopicForm'] = array('newtopicerrors','newTopicPreview','newTopicError','formReloadInvite','formReloadCopy','newTopicInvite','newTopicUploads');
$this->_funcHooks['sendReplyForm'] = array('replyerrors','replyForm','previewPm','formHeaderText','formErrors','attachmentForm','replyOptions','replyForm');
$this->_funcHooks['showConversation'] = array('hasAuthorId','authorOnline','accessModCP','authorPrivateIp','authorIpAddress','viewSigs','quickReply','reportPm','canEdit','canDelete','replies','disablelightbox','canReplyEditor','allAlone','reportPm','canEdit','canDelete','quickReply','replies','canReplyEditor');
$this->_funcHooks['showConversationForArchive'] = array('replies');
$this->_funcHooks['showFolder'] = array('folderLastPage','messagePages','hasStarterPhoto','folderNotifications','folderDrafts','folderNotificationsIgnore','folderStarter','folderToMember','folderFixPlural','folderMultipleUsers','folderNew','folderPages','folderBannedIndicator','hasPosterPhoto','folderToMember','folderBannedUser','folderMessages','folderNotDrafts','folderMessages','folderMultiOptions','folderJumpHtml','messages');
$this->_funcHooks['showSearchResults'] = array('folderNotifications','folderStarter','folderToMember','folderFixPlural','folderMultipleUsers','folderNew','folderBannedIndicator','folderBannedUser','messages','searchError','hasPagination','searchMessages');


}

/* -- messengerDisabled --*/
function messengerDisabled() {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['messengerDisabled'] ) )
{
$count_52757855c71f659b31d408d0dfe2b002 = is_array($this->functionData['messengerDisabled']) ? count($this->functionData['messengerDisabled']) : 0;
}
$IPBHTML .= "<error>Messanger Disabled</error>";
return $IPBHTML;
}

/* -- messengerTemplate --*/
function messengerTemplate($html, $jumpmenu, $dirData, $totalData=array(), $topicParticipants=array(), $inlineError='', $deletedTopic=0) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['messengerTemplate'] ) )
{
$count_c11def285b8654f5ddde4a43d90c0a4a = is_array($this->functionData['messengerTemplate']) ? count($this->functionData['messengerTemplate']) : 0;
$this->functionData['messengerTemplate'][$count_c11def285b8654f5ddde4a43d90c0a4a]['html'] = $html;
$this->functionData['messengerTemplate'][$count_c11def285b8654f5ddde4a43d90c0a4a]['jumpmenu'] = $jumpmenu;
$this->functionData['messengerTemplate'][$count_c11def285b8654f5ddde4a43d90c0a4a]['dirData'] = $dirData;
$this->functionData['messengerTemplate'][$count_c11def285b8654f5ddde4a43d90c0a4a]['totalData'] = $totalData;
$this->functionData['messengerTemplate'][$count_c11def285b8654f5ddde4a43d90c0a4a]['topicParticipants'] = $topicParticipants;
$this->functionData['messengerTemplate'][$count_c11def285b8654f5ddde4a43d90c0a4a]['inlineError'] = $inlineError;
$this->functionData['messengerTemplate'][$count_c11def285b8654f5ddde4a43d90c0a4a]['deletedTopic'] = $deletedTopic;
}
$IPBHTML .= "{$html}";
return $IPBHTML;
}

/* -- PMQuickForm --*/
function PMQuickForm($toMemberData) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- sendNewPersonalTopicForm --*/
function sendNewPersonalTopicForm($displayData) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['sendNewPersonalTopicForm'] ) )
{
$count_79d337870af995eda07f8b44a3cb2daa = is_array($this->functionData['sendNewPersonalTopicForm']) ? count($this->functionData['sendNewPersonalTopicForm']) : 0;
$this->functionData['sendNewPersonalTopicForm'][$count_79d337870af995eda07f8b44a3cb2daa]['displayData'] = $displayData;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- sendReplyForm --*/
function sendReplyForm($displayData) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['sendReplyForm'] ) )
{
$count_67a10d3a9796544eda61d110ec95d6d2 = is_array($this->functionData['sendReplyForm']) ? count($this->functionData['sendReplyForm']) : 0;
$this->functionData['sendReplyForm'][$count_67a10d3a9796544eda61d110ec95d6d2]['displayData'] = $displayData;
}
$IPBHTML .= "<postingForm>
	<msgID>{$displayData['msgID']}</msgID>
	<topicID>{$displayData['topicID']}</topicID>
	<postKey>{$displayData['postKey']}</postKey>
	{$displayData['editor']}
	<authKey>{$this->member->form_hash}</authKey>
	
	" . (($displayData['type'] == 'reply') ? ("
			<submitURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=sendReply", "publicWithApp",'' ), "", "" ) . "]]></submitURL>
	") : ("
			<submitURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=sendEdit", "publicWithApp",'' ), "", "" ) . "]]></submitURL>
	")) . "
	
</postingForm>";
return $IPBHTML;
}

/* -- showConversation --*/
function showConversation($topic, $replies, $members, $jump="") {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['showConversation'] ) )
{
$count_afd9b517a7fff3b0417a7d0dca2894cf = is_array($this->functionData['showConversation']) ? count($this->functionData['showConversation']) : 0;
$this->functionData['showConversation'][$count_afd9b517a7fff3b0417a7d0dca2894cf]['topic'] = $topic;
$this->functionData['showConversation'][$count_afd9b517a7fff3b0417a7d0dca2894cf]['replies'] = $replies;
$this->functionData['showConversation'][$count_afd9b517a7fff3b0417a7d0dca2894cf]['members'] = $members;
$this->functionData['showConversation'][$count_afd9b517a7fff3b0417a7d0dca2894cf]['jump'] = $jump;
}
$IPBHTML .= "<template>messageView</template>
<pagination>{$topic['_pages']}</pagination>
" . (($topic['_canReply']) ? ("
<AssessoryButtonURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=members&amp;module=messaging&amp;section=send&amp;do=sendReply&amp;topicID={$topic['mt_id']}", "public",'' ), "", "" ) . "]]></AssessoryButtonURL>
") : ("")) . "
<message>
	<title><![CDATA[{$topic['mt_title']}]]></title>
	".$this->__f__94e1be3bb5f2f64749db56ab8f37dda2($topic,$replies,$members,$jump)."</message>";
return $IPBHTML;
}


function __f__94e1be3bb5f2f64749db56ab8f37dda2($topic, $replies, $members, $jump="")
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $replies as $msg_id => $msg )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<messageReply>
			<user>
				<id>{$msg['msg_author_id']}</id>
				<name><![CDATA[{$members[ $msg['msg_author_id'] ]['members_display_name']}]]></name>
				<date>" . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($msg['msg_date'],"DATE", 0)) . "</date>
				<avatar><![CDATA[{$members[ $msg['msg_author_id'] ]['pp_thumb_photo']}]]></avatar>
				<url><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$msg['msg_author_id']}", "public",'' ), "{$members[ $msg['msg_author_id'] ]['members_seo_name']}", "showuser" ) . "]]></url>
			</user>	
			<date>" . IPSText::htmlspecialchars($this->registry->getClass('class_localization')->getDate($msg['post']['post_date'],"DATE", 0)) . "</date>
			<text><![CDATA[{$msg['msg_post']}
			{$msg['attachmentHtml']}]]></text>
			<options>
			" . (($topic['_canReport'] and $this->memberData['member_id']) ? ("
				<reportURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=core&amp;module=reports&amp;rcom=messages&amp;topicID={$this->request['topicID']}&amp;st={$this->request['st']}&amp;msg={$msg['msg_id']}", "public",'' ), "", "" ) . "]]></reportURL>
			") : ("")) . "
			
			" . (($msg['_canEdit'] === TRUE) ? ("
				<editURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=editMessage&amp;topicID={$topic['mt_id']}&amp;msgID={$msg['msg_id']}", "publicWithApp",'' ), "", "" ) . "]]></editURL>
			") : ("")) . "
			
			" . (($msg['_canDelete'] === TRUE && $msg['msg_is_first_post'] != 1) ? ("
				<deleteURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=deleteReply&amp;topicID={$topic['mt_id']}&amp;msgID={$msg['msg_id']}&amp;authKey={$this->member->form_hash}", "publicWithApp",'' ), "", "" ) . "]]></deleteURL>
			") : ("")) . "
			
			" . (($topic['_canReply'] AND empty( $topic['_everyoneElseHasLeft'] )) ? ("
				<replyURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "module=messaging&amp;section=send&amp;do=replyForm&amp;topicID={$topic['mt_id']}&amp;msgID={$msg['msg_id']}", "publicWithApp",'' ), "", "" ) . "]]></replyURL>
			") : ("")) . "
				</options>							
		</messageReply>
	
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- showConversationForArchive --*/
function showConversationForArchive($topic, $replies, $members) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['showConversationForArchive'] ) )
{
$count_cc4a7dd28d1595264678a7d3204a3e88 = is_array($this->functionData['showConversationForArchive']) ? count($this->functionData['showConversationForArchive']) : 0;
$this->functionData['showConversationForArchive'][$count_cc4a7dd28d1595264678a7d3204a3e88]['topic'] = $topic;
$this->functionData['showConversationForArchive'][$count_cc4a7dd28d1595264678a7d3204a3e88]['replies'] = $replies;
$this->functionData['showConversationForArchive'][$count_cc4a7dd28d1595264678a7d3204a3e88]['members'] = $members;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- showFolder --*/
function showFolder($messages, $dirname, $pages, $currentFolderID, $jumpFolderHTML) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['showFolder'] ) )
{
$count_16d75951fc2601f381643430f931eefc = is_array($this->functionData['showFolder']) ? count($this->functionData['showFolder']) : 0;
$this->functionData['showFolder'][$count_16d75951fc2601f381643430f931eefc]['messages'] = $messages;
$this->functionData['showFolder'][$count_16d75951fc2601f381643430f931eefc]['dirname'] = $dirname;
$this->functionData['showFolder'][$count_16d75951fc2601f381643430f931eefc]['pages'] = $pages;
$this->functionData['showFolder'][$count_16d75951fc2601f381643430f931eefc]['currentFolderID'] = $currentFolderID;
$this->functionData['showFolder'][$count_16d75951fc2601f381643430f931eefc]['jumpFolderHTML'] = $jumpFolderHTML;
}
$IPBHTML .= "<template>popoverMessages</template>
<messages>
	".$this->__f__70a993bc8b1cdc61a609a5003a2d28eb($messages,$dirname,$pages,$currentFolderID,$jumpFolderHTML)."</messages>";
return $IPBHTML;
}


function __f__70a993bc8b1cdc61a609a5003a2d28eb($messages, $dirname, $pages, $currentFolderID, $jumpFolderHTML)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $messages as $message )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<message>
			<id>{$message['msg_topic_id']}</id>
			<title>{$message['mt_title']}</title>
			<url><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "app=members&amp;module=messaging&amp;section=view&amp;do=showConversation&amp;topicID={$message['msg_topic_id']}", "public",'' ), "", "" ) . "]]></url>
			<SenderName><![CDATA[{$message['_starterMemberData']['members_display_name']}]]></SenderName>
			<icon><![CDATA[{$message['_starterMemberData']['pp_mini_photo']}]]></icon>
		</message>
	
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- showSearchResults --*/
function showSearchResults($messages, $pages, $error) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_messaging', $this->_funcHooks['showSearchResults'] ) )
{
$count_82ca792029cc176e09d364ef05ba21f4 = is_array($this->functionData['showSearchResults']) ? count($this->functionData['showSearchResults']) : 0;
$this->functionData['showSearchResults'][$count_82ca792029cc176e09d364ef05ba21f4]['messages'] = $messages;
$this->functionData['showSearchResults'][$count_82ca792029cc176e09d364ef05ba21f4]['pages'] = $pages;
$this->functionData['showSearchResults'][$count_82ca792029cc176e09d364ef05ba21f4]['error'] = $error;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>