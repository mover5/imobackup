<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_search_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['asForumPosts'] = array('notLastFtAsForum','topicsForumTrail','moderated','postMid','postModSelected','postModCheckbox','postMember','hasForumTrail');
$this->_funcHooks['asForumTopics'] = array('notLastFtAsForum','topicsForumTrail','haslastpage','pages','archivedBadge','hasPrefix','resultIsPostTR','isNewPostTR','hasTags','multipages','bothSearchUnderTitle','isFollowedStuff','replylang','bothSearch','forumsDigests','isArchivedCb','isAdmin','isFollowedStuff','notLastFtAsForum','topicsForumTrail','resultIsPostAsForum','isNewPostAsForum');
$this->_funcHooks['followData'] = array('hasFrequence','isAnonymous');
$this->_funcHooks['followedContentForumsWrapper'] = array('NCresultsAsForum');
$this->_funcHooks['followedContentForumsWrapperForums'] = array('showSubForumsLit','subforums','showSubForums','isFollowedStuff','hasphoto','lastPosterID','hideDateUrl','hasLastTopicId','hideLastInfo','forumsDigests','NCresultsAsForum');
$this->_funcHooks['followedContentView'] = array('appIsSearched','supportsLikes','apps','hasconfirm','haslikeserror','forumsTab','memberFollow','membersTab','memberFollow','searchedApp','forumsDigests','hasLikeForMod','NPTotal');
$this->_funcHooks['forumAdvancedSearchFilters'] = array('hasArchives');
$this->_funcHooks['forumsVncFilters'] = array('hasFiltersSet','depth','hasFiltersSet','hasTitle','forumlist','noFiltersSet','noFiltersSet');
$this->_funcHooks['helpSearchResult'] = array('showHelpContent');
$this->_funcHooks['memberCommentsSearchResult'] = array('isUpdate','hasMore','maxReplies');
$this->_funcHooks['newContentView'] = array('appIsSearched','appIsSearchable','apps','forumsTab','membersTab','helpTab','hazVNC','hazMember','checked','userModeAll','userModeTitle','hasFilters','vncFilterForumsOnly','canFollowFilter','searchismod','NPTotal');
$this->_funcHooks['searchAdvancedForm'] = array('appLoop','calendarlocale','searchTermsRemoved','searchError','isFullText','tagyouareit');
$this->_funcHooks['searchResults'] = array('subResult','results','hasResults','subResult','results');
$this->_funcHooks['searchResultsAsForum'] = array('NCresultsAsForum','asTawpiks','asPostsStart','asPostsEnd','isAdminBottom','isAdmin','disablelightbox','asTawpiks2');
$this->_funcHooks['searchResultsWrapper'] = array('appIsSearched','appIsSearchable','apps','hasSearchResultsCut','hasSearchResultsTags','hasSearchResults','forumsTab','membersTab','helpTab','searchismod','noResultsTerm','hasTotal');
$this->_funcHooks['searchRowGenericFormat'] = array('showGenericContent','isUpdated','hasMemberId','isUpdatedOrHasMemberId');
$this->_funcHooks['topicPostSearchResultAsForum'] = array('whichWayToGo');
$this->_funcHooks['userPostsView'] = array('appIsSearched','appIsSearchable','apps','NPhasResults','forumsTab','membersTab','helpTab','searchismod','NPTotal');


}

/* -- asForumPosts --*/
function asForumPosts($data) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['asForumPosts'] ) )
{
$count_4d965b0f9e1cf4acd2c877e1b94793e7 = is_array($this->functionData['asForumPosts']) ? count($this->functionData['asForumPosts']) : 0;
$this->functionData['asForumPosts'][$count_4d965b0f9e1cf4acd2c877e1b94793e7]['data'] = $data;
}
$IPBHTML .= "<template>searchResultsAsPost</template>
<searchResults>
</searchResults>";
return $IPBHTML;
}

/* -- asForumTopics --*/
function asForumTopics($data) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['asForumTopics'] ) )
{
$count_d868c5f3e5fc31e9599fc683b1650992 = is_array($this->functionData['asForumTopics']) ? count($this->functionData['asForumTopics']) : 0;
$this->functionData['asForumTopics'][$count_d868c5f3e5fc31e9599fc683b1650992]['data'] = $data;
}
$IPBHTML .= "<searchResult>
	<tid>{$data['tid']}</tid>
	<title>{$data['_shortTitle']}</title>
	<state>{$data['state']}</state>
	<url>" . (($data['_unreadUrl']) ? ("{$data['_unreadUrl']}") : ("" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showtopic={$data['type_id_2']}&amp;view=" . (($this->request['do']=='new_posts' OR $this->request['do']=='active') ? ("getnewpost") : ("" . (($data['misc']) ? ("findpost&amp;p={$data['misc']}") : ("")) . "")) . "&amp;hl={$this->request['search_higlight']}&amp;fromsearch=1", "public",'' ), "{$data['title_seo']}", "showtopic" ) . "")) . "</url>
	<isRead>" . (($data['_isRead'] == 1) ? ("1") : ("0")) . "</isRead>
	<authorid>{$data['author_id']}</authorid>
	<author>{$data['author_name']}</author>
	".$this->__f__83455986f511d9ae047d7c8a5a2c96f5($data)."</searchResult>";
return $IPBHTML;
}


function __f__83455986f511d9ae047d7c8a5a2c96f5($data)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $data['_forum_trail'] as $i => $f )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "" . (($i+1 == count( $data['_forum_trail'] )) ? ("<forum>{$f[0]}</forum>") : ("")) . "
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- followData --*/
function followData($followData) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['followData'] ) )
{
$count_5a749269147025d0a338456451fff027 = is_array($this->functionData['followData']) ? count($this->functionData['followData']) : 0;
$this->functionData['followData'][$count_5a749269147025d0a338456451fff027]['followData'] = $followData;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- followedContentForumsWrapper --*/
function followedContentForumsWrapper($results) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['followedContentForumsWrapper'] ) )
{
$count_48c4361416bc4fecb25f23aeffb35961 = is_array($this->functionData['followedContentForumsWrapper']) ? count($this->functionData['followedContentForumsWrapper']) : 0;
$this->functionData['followedContentForumsWrapper'][$count_48c4361416bc4fecb25f23aeffb35961]['results'] = $results;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- followedContentForumsWrapperForums --*/
function followedContentForumsWrapperForums($results) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['followedContentForumsWrapperForums'] ) )
{
$count_3075f84d867b1a6fb8a5d6b3f0e4ca6a = is_array($this->functionData['followedContentForumsWrapperForums']) ? count($this->functionData['followedContentForumsWrapperForums']) : 0;
$this->functionData['followedContentForumsWrapperForums'][$count_3075f84d867b1a6fb8a5d6b3f0e4ca6a]['results'] = $results;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- followedContentView --*/
function followedContentView($results, $pagination, $total, $error, $contentTypes) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['followedContentView'] ) )
{
$count_f76d986e554d6c63e99ab38d72983287 = is_array($this->functionData['followedContentView']) ? count($this->functionData['followedContentView']) : 0;
$this->functionData['followedContentView'][$count_f76d986e554d6c63e99ab38d72983287]['results'] = $results;
$this->functionData['followedContentView'][$count_f76d986e554d6c63e99ab38d72983287]['pagination'] = $pagination;
$this->functionData['followedContentView'][$count_f76d986e554d6c63e99ab38d72983287]['total'] = $total;
$this->functionData['followedContentView'][$count_f76d986e554d6c63e99ab38d72983287]['error'] = $error;
$this->functionData['followedContentView'][$count_f76d986e554d6c63e99ab38d72983287]['contentTypes'] = $contentTypes;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- forumAdvancedSearchFilters --*/
function forumAdvancedSearchFilters($forums, $archivedPostCount=0) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['forumAdvancedSearchFilters'] ) )
{
$count_8f5a55b9186dcd1381c419b946634aef = is_array($this->functionData['forumAdvancedSearchFilters']) ? count($this->functionData['forumAdvancedSearchFilters']) : 0;
$this->functionData['forumAdvancedSearchFilters'][$count_8f5a55b9186dcd1381c419b946634aef]['forums'] = $forums;
$this->functionData['forumAdvancedSearchFilters'][$count_8f5a55b9186dcd1381c419b946634aef]['archivedPostCount'] = $archivedPostCount;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- forumsVncFilters --*/
function forumsVncFilters($data, $currentPrefs) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['forumsVncFilters'] ) )
{
$count_9bc97006d6288331fd37a34ee576b621 = is_array($this->functionData['forumsVncFilters']) ? count($this->functionData['forumsVncFilters']) : 0;
$this->functionData['forumsVncFilters'][$count_9bc97006d6288331fd37a34ee576b621]['data'] = $data;
$this->functionData['forumsVncFilters'][$count_9bc97006d6288331fd37a34ee576b621]['currentPrefs'] = $currentPrefs;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- helpSearchResult --*/
function helpSearchResult($r, $resultAsTitle=false) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['helpSearchResult'] ) )
{
$count_29e7bb93696813ae892bbebad3c58213 = is_array($this->functionData['helpSearchResult']) ? count($this->functionData['helpSearchResult']) : 0;
$this->functionData['helpSearchResult'][$count_29e7bb93696813ae892bbebad3c58213]['r'] = $r;
$this->functionData['helpSearchResult'][$count_29e7bb93696813ae892bbebad3c58213]['resultAsTitle'] = $resultAsTitle;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- memberCommentsSearchResult --*/
function memberCommentsSearchResult($r, $resultAsTitle=false) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['memberCommentsSearchResult'] ) )
{
$count_dcd81aeba1828174397645cfb7d5136f = is_array($this->functionData['memberCommentsSearchResult']) ? count($this->functionData['memberCommentsSearchResult']) : 0;
$this->functionData['memberCommentsSearchResult'][$count_dcd81aeba1828174397645cfb7d5136f]['r'] = $r;
$this->functionData['memberCommentsSearchResult'][$count_dcd81aeba1828174397645cfb7d5136f]['resultAsTitle'] = $resultAsTitle;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- memberSearchResult --*/
function memberSearchResult($r, $resultAsTitle=false) {
$IPBHTML = "";
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- newContentView --*/
function newContentView($results, $pagination, $total, $sortDropDown, $sortIn, $dateCutSet=0) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['newContentView'] ) )
{
$count_eb456aa1efb1ea01255c48f50037d20a = is_array($this->functionData['newContentView']) ? count($this->functionData['newContentView']) : 0;
$this->functionData['newContentView'][$count_eb456aa1efb1ea01255c48f50037d20a]['results'] = $results;
$this->functionData['newContentView'][$count_eb456aa1efb1ea01255c48f50037d20a]['pagination'] = $pagination;
$this->functionData['newContentView'][$count_eb456aa1efb1ea01255c48f50037d20a]['total'] = $total;
$this->functionData['newContentView'][$count_eb456aa1efb1ea01255c48f50037d20a]['sortDropDown'] = $sortDropDown;
$this->functionData['newContentView'][$count_eb456aa1efb1ea01255c48f50037d20a]['sortIn'] = $sortIn;
$this->functionData['newContentView'][$count_eb456aa1efb1ea01255c48f50037d20a]['dateCutSet'] = $dateCutSet;
}
$IPBHTML .= "{$results}";
return $IPBHTML;
}

/* -- searchAdvancedForm --*/
function searchAdvancedForm($filters='', $msg='', $current_app, $removed_search_terms=array(), $isFT=false, $canTag=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['searchAdvancedForm'] ) )
{
$count_0452aca20ac5e4a760f4a559b1aa0c04 = is_array($this->functionData['searchAdvancedForm']) ? count($this->functionData['searchAdvancedForm']) : 0;
$this->functionData['searchAdvancedForm'][$count_0452aca20ac5e4a760f4a559b1aa0c04]['filters'] = $filters;
$this->functionData['searchAdvancedForm'][$count_0452aca20ac5e4a760f4a559b1aa0c04]['msg'] = $msg;
$this->functionData['searchAdvancedForm'][$count_0452aca20ac5e4a760f4a559b1aa0c04]['current_app'] = $current_app;
$this->functionData['searchAdvancedForm'][$count_0452aca20ac5e4a760f4a559b1aa0c04]['removed_search_terms'] = $removed_search_terms;
$this->functionData['searchAdvancedForm'][$count_0452aca20ac5e4a760f4a559b1aa0c04]['isFT'] = $isFT;
$this->functionData['searchAdvancedForm'][$count_0452aca20ac5e4a760f4a559b1aa0c04]['canTag'] = $canTag;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- searchResults --*/
function searchResults($results, $titlesOnly) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['searchResults'] ) )
{
$count_17e8c898761a4f9612bd43e5cb62985c = is_array($this->functionData['searchResults']) ? count($this->functionData['searchResults']) : 0;
$this->functionData['searchResults'][$count_17e8c898761a4f9612bd43e5cb62985c]['results'] = $results;
$this->functionData['searchResults'][$count_17e8c898761a4f9612bd43e5cb62985c]['titlesOnly'] = $titlesOnly;
}
$IPBHTML .= "" . ((is_array( $results )) ? ("".$this->__f__96249b8b88bc1ede54719f27103eab16($results,$titlesOnly)."		") : ("")) . "";
return $IPBHTML;
}


function __f__96249b8b88bc1ede54719f27103eab16($results, $titlesOnly)
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $results as $result )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
			" . (($result['sub']) ? ("
					{$result['html']}
				
			") : ("
					{$result['html']}
				
			")) . "
		
";
	}
	$_ips___x_retval .= '';
	unset( $__iteratorCount );
	return $_ips___x_retval;
}

/* -- searchResultsAsForum --*/
function searchResultsAsForum($results, $titlesOnly) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['searchResultsAsForum'] ) )
{
$count_bd53f820ae4b34f0777f88c913ce5d52 = is_array($this->functionData['searchResultsAsForum']) ? count($this->functionData['searchResultsAsForum']) : 0;
$this->functionData['searchResultsAsForum'][$count_bd53f820ae4b34f0777f88c913ce5d52]['results'] = $results;
$this->functionData['searchResultsAsForum'][$count_bd53f820ae4b34f0777f88c913ce5d52]['titlesOnly'] = $titlesOnly;
}
$IPBHTML .= "<template>searchResultsAsTopic</template>
<searchResults>
" . ( method_exists( $this->registry->getClass('output')->getTemplate('search'), 'searchResults' ) ? $this->registry->getClass('output')->getTemplate('search')->searchResults($results, $titlesOnly) : '' ) . "
</searchResults>";
return $IPBHTML;
}

/* -- searchResultsWrapper --*/
function searchResultsWrapper($results, $sortDropDown, $sortIn, $pagination, $total, $showing, $search_term, $url_string, $current_key, $removed_search_terms=array(), $limited=0, $wasLimited=false, $search_tags) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['searchResultsWrapper'] ) )
{
$count_38df9ac4352a6bca7b5d7fec83ecf63f = is_array($this->functionData['searchResultsWrapper']) ? count($this->functionData['searchResultsWrapper']) : 0;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['results'] = $results;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['sortDropDown'] = $sortDropDown;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['sortIn'] = $sortIn;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['pagination'] = $pagination;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['total'] = $total;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['showing'] = $showing;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['search_term'] = $search_term;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['url_string'] = $url_string;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['current_key'] = $current_key;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['removed_search_terms'] = $removed_search_terms;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['limited'] = $limited;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['wasLimited'] = $wasLimited;
$this->functionData['searchResultsWrapper'][$count_38df9ac4352a6bca7b5d7fec83ecf63f]['search_tags'] = $search_tags;
}
$IPBHTML .= "{$results}";
return $IPBHTML;
}

/* -- searchRowGenericFormat --*/
function searchRowGenericFormat($r, $resultAsTitle=false) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['searchRowGenericFormat'] ) )
{
$count_95955af8e7099b6a4d7a60192bb4011b = is_array($this->functionData['searchRowGenericFormat']) ? count($this->functionData['searchRowGenericFormat']) : 0;
$this->functionData['searchRowGenericFormat'][$count_95955af8e7099b6a4d7a60192bb4011b]['r'] = $r;
$this->functionData['searchRowGenericFormat'][$count_95955af8e7099b6a4d7a60192bb4011b]['resultAsTitle'] = $resultAsTitle;
}
$IPBHTML .= "<!-- NoData -->";
return $IPBHTML;
}

/* -- topicPostSearchResultAsForum --*/
function topicPostSearchResultAsForum($data, $resultAsTitle=false) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['topicPostSearchResultAsForum'] ) )
{
$count_40c724e32c1e914e49ab0afc31b96cf1 = is_array($this->functionData['topicPostSearchResultAsForum']) ? count($this->functionData['topicPostSearchResultAsForum']) : 0;
$this->functionData['topicPostSearchResultAsForum'][$count_40c724e32c1e914e49ab0afc31b96cf1]['data'] = $data;
$this->functionData['topicPostSearchResultAsForum'][$count_40c724e32c1e914e49ab0afc31b96cf1]['resultAsTitle'] = $resultAsTitle;
}
$IPBHTML .= "" . ( method_exists( $this->registry->getClass('output')->getTemplate('search'), 'asForumTopics' ) ? $this->registry->getClass('output')->getTemplate('search')->asForumTopics($data) : '' ) . "";
return $IPBHTML;
}

/* -- userPostsView --*/
function userPostsView($results, $pagination, $total, $member, $limited=0, $wasLimited=false, $beginTime=0, $sortIn=null, $sortDropDown=array()) {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_search', $this->_funcHooks['userPostsView'] ) )
{
$count_21343aa11404cfe02588c425046fefe8 = is_array($this->functionData['userPostsView']) ? count($this->functionData['userPostsView']) : 0;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['results'] = $results;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['pagination'] = $pagination;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['total'] = $total;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['member'] = $member;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['limited'] = $limited;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['wasLimited'] = $wasLimited;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['beginTime'] = $beginTime;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['sortIn'] = $sortIn;
$this->functionData['userPostsView'][$count_21343aa11404cfe02588c425046fefe8]['sortDropDown'] = $sortDropDown;
}
$IPBHTML .= "test
<pagination>{$pagination}</pagination>
{$results}";
return $IPBHTML;
}


}


/*--------------------------------------------------*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>