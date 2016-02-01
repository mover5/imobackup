<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_global_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['globalTemplate'] = array('showingapp','applications','didfirstappnow','navigationlink','closenavigationlink','forsuredidfirstnav','navigation','didfirstappnowbottom','navigationlink','closenavigationlink','forsuredidfirstnavbottom','navigationbottom','isCoreRC','hideRcForPerms','fbcenabled','isLargeTouch','isSmallTouch','showacplink','rclink','accessreports','notifications','messengerlink','notifications','showInboxNotify','ipsconnectRevalidateUrl','authenticating','updateTwitter','updateFacebook','update','canUpdateStatus','userLikeLink','nobbyNoMates','bloglink','pmLink','gallerylink','nexuslink','limFacebook','limTwitter','limWindows','memberbox','brandingBar','canSearch','showQuickNav','viewnewcontentapp','showhomeurl','applicationsloop','hasCustomPrimaryNavigation','didfirstnav','switchnavigation','countnav','hasHeaderAd','mainpageContent','hasFooterAd','didfirstnavbottom','switchnavigationbottom','countnavbottom','privvy','ruleslink','siterulestitletitle','siterulestitle','privvyMiddot','siteruleslink','isTouchDevice','skinchangerInner','uagentlocked','skinchangerOuter','langchooser','markRead','lastvisit','isfloat','sqldebuglink','closesqldebuglink','showdebuglevel','includeLightboxDoReal','vigLinkEnabled');
$this->_funcHooks['includeCSS'] = array('donotminifycss','cssImport','cssInline','minifycss','csstominify','hasimportcss','inlinecss');
$this->_funcHooks['includeFeeds'] = array('dhjavascript','dhcss','dhrsd','dhraw','documentHeadItems','headitemsType','hasdocheaditems');
$this->_funcHooks['includeJS'] = array('usehttpsprototype','usehttpsscriptaculous','remoteloadjs','hasjsmodules','nmusehttpsp','nominifyremoteloadjs','nmusehttpss','nominifyremoteloadjs2','minifyjs','isLargeTouch');
$this->_funcHooks['includeMeta'] = array('ogCaveman','hasIdentifier','hasDescription','metaTagsInner','metaTags','metatags');
$this->_funcHooks['includeRTL'] = array('isrtl','checkrtl','langData','importrtlcss','importrtlcss','importrtlcss','importrtlcss','RTLMargin','hasMemberTopicMax');
$this->_funcHooks['includeVars'] = array('hasFBCHash','istm','istl','canswfupload','usefurl','hasNotification','autodst');
$this->_funcHooks['inlineLogin'] = array('extrafields','facebook','twitterBox','haswindowslive','extraform','registerServices','anonymous','privvy','hasReplacement');
$this->_funcHooks['liveEditJs'] = array('hasSkinData','liveEditOn');
$this->_funcHooks['nextPreviousTemplate'] = array('useAjaxPrev','prevpage','useAjaxNext','nextpage','haspages');
$this->_funcHooks['paginationTemplate'] = array('hasRealTitle','hasRealTitle','hasPage','activepage','pagination','hasRealTitleFirst','firstpage','hasRealTitlePrev','prevpage','furlinfo','normalpages','hasRealTitleNext','nextpage','hasRealTitleLast','lastpage','notDisableSinglePage','haspages');
$this->_funcHooks['quickSearch'] = array('inThisAppSearch','inThisAppNotSearch','InSearchQuestionMark','thisAppSearchableNotCore','appLoop','hasSearchApp','showTopic','inTopic','notTopic','showForum','inForum','appContextSearch','notCoreApp','lookElsewhere','matchesForums','inThisAppForums','canSearchForums','inThisAppMembers','canSearchMembers','inThisAppCore','canSearchCore');
$this->_funcHooks['shareLinks'] = array('hasOverrideApp','hasCustom','isEnabled','cacheLoop','gotLinks');
$this->_funcHooks['signature_separator'] = array('notMe');
$this->_funcHooks['userHoverCard'] = array('hasClassName','hasTitle','canSeeProfiles');
$this->_funcHooks['userInfoPane'] = array('customFields','customFieldsOuter','membertitle','canSeeProfiles','hasVariable','canSeeProfiles2','avatar','rankimageimage','rankimage','postCount','hasWarningId','authorwarn','authorcfields');
$this->_funcHooks['userSmallPhoto'] = array('linkAvatarOpen','hasAlt','hasCustomClass','hasphoto','linkAvatarClose');
$this->_funcHooks['warnDetails'] = array('actionIsPermanent','hasAction','actions','hasReasonAndContent','hasContent','hasReason','hasExpireDate','canExpire','hasPoint','isVerbalOnly','hasModAndMemberNote','hasMemberNote','canSeeModNote');


}

/* -- defaultHeader --*/
function defaultHeader() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- facebookShareButton --*/
function facebookShareButton($url, $title) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- forum_jump --*/
function forum_jump($html) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- globalTemplate --*/
function globalTemplate($html, $documentHeadItems, $css, $jsModules, $metaTags, array $header_items, $items=array(), $footer_items=array(), $stats=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['globalTemplate'] ) )
{
$count_e38492b6932fc63788b3da2cede5c7e9 = is_array($this->functionData['globalTemplate']) ? count($this->functionData['globalTemplate']) : 0;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['html'] = $html;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['documentHeadItems'] = $documentHeadItems;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['css'] = $css;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['jsModules'] = $jsModules;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['metaTags'] = $metaTags;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['header_items'] = $header_items;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['items'] = $items;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['footer_items'] = $footer_items;
$this->functionData['globalTemplate'][$count_e38492b6932fc63788b3da2cede5c7e9]['stats'] = $stats;
}

$uses_name		= false;
	$uses_email		= false;
	$_redirect		= '';
	
	foreach( $this->cache->getCache('login_methods') as $method )
	{
		if( $method['login_user_id'] == 'username' or $method['login_user_id'] == 'either' )
		{
			$uses_name	= true;
		}
		
		if( $method['login_user_id'] == 'email' or $method['login_user_id'] == 'either' )
		{
			$uses_email	= true;
		}
		
		if( $method['login_login_url'] )
		{
			$_redirect	= $method['login_login_url'];
		}
	}
	//These strings are hardcoded for a reason :)
	if( $uses_name AND $uses_email )
	{
		$this->lang->words['enter_name']	= "USERNAME OR EMAIL";
	}
	else if( $uses_email )
	{
		$this->lang->words['enter_name']	= "EMAIL";
	}
	else
	{
		$this->lang->words['enter_name']	= "USERNAME";
	}
$IPBHTML .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<ipb>
<title><![CDATA[{$header_items['title']}]]></title>
<boardURL><![CDATA[{$this->settings['board_url']}]]></boardURL>
<publicURL><![CDATA[{$this->settings['public_dir']}]]></publicURL>
<forumHome><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "act=idx", "public",'' ), "", "" ) . "]]></forumHome>
<styleRevision><![CDATA[{$this->settings['style_last_updated']}]]></styleRevision>


<memberData>
	" . ((IPSMember::canReceiveMobileNotifications($this->memberData)) ? ("
	<push_enabled>1</push_enabled>
    ") : ("")) . "
	<member_id>{$this->memberData['member_id']}</member_id>
	<notificationCnt>{$this->memberData['notification_cnt']}</notificationCnt>
	<messageCnt>{$this->memberData['msg_count_new']}</messageCnt>
	<isSuperMod>{$this->memberData['g_is_supmod']}</isSuperMod>
	<isMod>{$this->memberData['is_mod']}</isMod>
	<isAdmin>{$this->memberData['g_access_cp']}</isAdmin>
	<membersDisplayName><![CDATA[{$this->memberData['members_display_name']}]]></membersDisplayName>
	<secureHash>{$this->member->form_hash}</secureHash>
	<sessionId>{$this->member->session_id}</sessionId>
	<avatarThumb><![CDATA[{$this->memberData['pp_thumb_photo']}]]></avatarThumb>
	<profileURL><![CDATA[" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$this->memberData['member_id']}", "public",'' ), "{$this->memberData['members_seo_name']}", "showuser" ) . "]]></profileURL>
</memberData>
<admob>
   <adLocation>{$this->settings['admob_top']}|{$this->settings['admob_bottom']}</adLocation>
   <adCode>{$this->settings['admob_pub_id']}</adCode>
</admob><loginMethod>{$this->lang->words['enter_name']}</loginMethod>

{$html}
</ipb>";
return $IPBHTML;
}

/* -- googlePlusOneButton --*/
function googlePlusOneButton($url, $title) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- include_highlighter --*/
function include_highlighter($load_when_needed=0) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- include_lightbox --*/
function include_lightbox() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- include_lightbox_real --*/
function include_lightbox_real() {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeCSS --*/
function includeCSS($css) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['includeCSS'] ) )
{
$count_f9286ece4f607acca453b3c2070797bf = is_array($this->functionData['includeCSS']) ? count($this->functionData['includeCSS']) : 0;
$this->functionData['includeCSS'][$count_f9286ece4f607acca453b3c2070797bf]['css'] = $css;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeFeeds --*/
function includeFeeds($documentHeadItems) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['includeFeeds'] ) )
{
$count_0b5c91eadc93d7b7d957b3d93a1ea82d = is_array($this->functionData['includeFeeds']) ? count($this->functionData['includeFeeds']) : 0;
$this->functionData['includeFeeds'][$count_0b5c91eadc93d7b7d957b3d93a1ea82d]['documentHeadItems'] = $documentHeadItems;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeJS --*/
function includeJS($jsModules) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['includeJS'] ) )
{
$count_c0d27596f6d2f51ea5a609a449dcab42 = is_array($this->functionData['includeJS']) ? count($this->functionData['includeJS']) : 0;
$this->functionData['includeJS'][$count_c0d27596f6d2f51ea5a609a449dcab42]['jsModules'] = $jsModules;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeMeta --*/
function includeMeta($metaTags) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['includeMeta'] ) )
{
$count_4601d0bd031abe64333bc664432a008b = is_array($this->functionData['includeMeta']) ? count($this->functionData['includeMeta']) : 0;
$this->functionData['includeMeta'][$count_4601d0bd031abe64333bc664432a008b]['metaTags'] = $metaTags;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeRTL --*/
function includeRTL() {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['includeRTL'] ) )
{
$count_47818fad39fe618f823661a16964799d = is_array($this->functionData['includeRTL']) ? count($this->functionData['includeRTL']) : 0;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- includeVars --*/
function includeVars($header_items=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['includeVars'] ) )
{
$count_d21ae20bc8c636f19120fee71a1f4eb1 = is_array($this->functionData['includeVars']) ? count($this->functionData['includeVars']) : 0;
$this->functionData['includeVars'][$count_d21ae20bc8c636f19120fee71a1f4eb1]['header_items'] = $header_items;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- inlineLogin --*/
function inlineLogin() {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['inlineLogin'] ) )
{
$count_0eb41cf41faa155c27c457c3852e5eec = is_array($this->functionData['inlineLogin']) ? count($this->functionData['inlineLogin']) : 0;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- liveEditJs --*/
function liveEditJs() {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['liveEditJs'] ) )
{
$count_29461ed07ad01feb3f888d4dbe68f0d9 = is_array($this->functionData['liveEditJs']) ? count($this->functionData['liveEditJs']) : 0;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- metaEditor --*/
function metaEditor($tags, $url) {
$IPBHTML = "";
$IPBHTML .= "<!--no data in this master skin-->";
return $IPBHTML;
}

/* -- nextPreviousTemplate --*/
function nextPreviousTemplate($data) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['nextPreviousTemplate'] ) )
{
$count_5b3ca7ee52028084b78b7177d832e3dd = is_array($this->functionData['nextPreviousTemplate']) ? count($this->functionData['nextPreviousTemplate']) : 0;
$this->functionData['nextPreviousTemplate'][$count_5b3ca7ee52028084b78b7177d832e3dd]['data'] = $data;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- paginationTemplate --*/
function paginationTemplate($work, $data) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['paginationTemplate'] ) )
{
$count_fe051791556dd14f694758abe19a0ee9 = is_array($this->functionData['paginationTemplate']) ? count($this->functionData['paginationTemplate']) : 0;
$this->functionData['paginationTemplate'][$count_fe051791556dd14f694758abe19a0ee9]['work'] = $work;
$this->functionData['paginationTemplate'][$count_fe051791556dd14f694758abe19a0ee9]['data'] = $data;
}
$IPBHTML .= "<paginationBase>" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "{$data['baseUrl']}&amp;{$data['startValueKey']}={$data['anchor']}", "{$data['base']}",'' ), "{$data['seoTitle']}", "{$data['seoTemplate']}" ) . "</paginationBase>
<itemsPerPage>{$data['itemsPerPage']}</itemsPerPage>
<totalPages>{$work['pages']}</totalPages>
<currentPage>{$work['current_page']}</currentPage>
<lastReadPage>" . intval( ( $work['pages'] - 1 ) * $data['itemsPerPage'] ) . "{$data['anchor']}</lastReadPage>";
return $IPBHTML;
}

/* -- quickSearch --*/
function quickSearch() {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['quickSearch'] ) )
{
$count_4e983c8a5e3a77c2e2d3689fc93c95ba = is_array($this->functionData['quickSearch']) ? count($this->functionData['quickSearch']) : 0;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- shareLinks --*/
function shareLinks($links, $title='', $url='', $cssClass='topic_share left') {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['shareLinks'] ) )
{
$count_d79e045daaf452544ed7f68adcc05fa9 = is_array($this->functionData['shareLinks']) ? count($this->functionData['shareLinks']) : 0;
$this->functionData['shareLinks'][$count_d79e045daaf452544ed7f68adcc05fa9]['links'] = $links;
$this->functionData['shareLinks'][$count_d79e045daaf452544ed7f68adcc05fa9]['title'] = $title;
$this->functionData['shareLinks'][$count_d79e045daaf452544ed7f68adcc05fa9]['url'] = $url;
$this->functionData['shareLinks'][$count_d79e045daaf452544ed7f68adcc05fa9]['cssClass'] = $cssClass;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- signature_separator --*/
function signature_separator($sig="", $author_id=0, $can_ignore=true) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['signature_separator'] ) )
{
$count_ea2abf6b6b2bd2fe0ef174120b48789a = is_array($this->functionData['signature_separator']) ? count($this->functionData['signature_separator']) : 0;
$this->functionData['signature_separator'][$count_ea2abf6b6b2bd2fe0ef174120b48789a]['sig'] = $sig;
$this->functionData['signature_separator'][$count_ea2abf6b6b2bd2fe0ef174120b48789a]['author_id'] = $author_id;
$this->functionData['signature_separator'][$count_ea2abf6b6b2bd2fe0ef174120b48789a]['can_ignore'] = $can_ignore;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- userHoverCard --*/
function userHoverCard($member=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['userHoverCard'] ) )
{
$count_1fea1896e6a71cdfa0c8fa49fa6c55d7 = is_array($this->functionData['userHoverCard']) ? count($this->functionData['userHoverCard']) : 0;
$this->functionData['userHoverCard'][$count_1fea1896e6a71cdfa0c8fa49fa6c55d7]['member'] = $member;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- userInfoPane --*/
function userInfoPane($author, $contentid, $options) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['userInfoPane'] ) )
{
$count_de2b58bc396fdcdf7f0251a33cb99cff = is_array($this->functionData['userInfoPane']) ? count($this->functionData['userInfoPane']) : 0;
$this->functionData['userInfoPane'][$count_de2b58bc396fdcdf7f0251a33cb99cff]['author'] = $author;
$this->functionData['userInfoPane'][$count_de2b58bc396fdcdf7f0251a33cb99cff]['contentid'] = $contentid;
$this->functionData['userInfoPane'][$count_de2b58bc396fdcdf7f0251a33cb99cff]['options'] = $options;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- userSmallPhoto --*/
function userSmallPhoto($member=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['userSmallPhoto'] ) )
{
$count_6a06bf79e81fd80ce799cbc7f5873ac9 = is_array($this->functionData['userSmallPhoto']) ? count($this->functionData['userSmallPhoto']) : 0;
$this->functionData['userSmallPhoto'][$count_6a06bf79e81fd80ce799cbc7f5873ac9]['member'] = $member;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- warnDetails --*/
function warnDetails($warning, $canSeeModNote) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_global', $this->_funcHooks['warnDetails'] ) )
{
$count_cb94310d18fa38cf6ea626b6f02971d2 = is_array($this->functionData['warnDetails']) ? count($this->functionData['warnDetails']) : 0;
$this->functionData['warnDetails'][$count_cb94310d18fa38cf6ea626b6f02971d2]['warning'] = $warning;
$this->functionData['warnDetails'][$count_cb94310d18fa38cf6ea626b6f02971d2]['canSeeModNote'] = $canSeeModNote;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>