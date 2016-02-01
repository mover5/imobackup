<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD 3         */
/* CACHE FILE: Skin set id: 3               */
/* CACHE FILE: Generated: Tue, 28 Jan 2014 06:36:01 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_mlist_3 extends skinMaster{

/**
* Construct
*/
function __construct( ipsRegistry $registry )
{
	parent::__construct( $registry );
	

$this->_funcHooks = array();
$this->_funcHooks['member_list_show'] = array('customfields','filterdefault','filter','sortdefault','sort_key','orderdefault','sort_order','limitdefault','max_results','selected','letterdefault','chars','weAreSupmod','addfriend','notus','sendpm','blog','gallery','rate1','rate2','rate3','rate4','rate5','rating','norep','posrep','negrep','repson','filterViews','members','calendarlocale','namebox_begins','namebox_contains','photoonly','rating0','rating1','rating2','rating3','rating4','canFilterRate','hascfields','posts_ltmt_lt','posts_ltmt_mt','joined_ltmt_lt','joined_ltmt_mt','lastpost_ltmt_lt','lastpost_ltmt_mt','lastvisit_ltmt_lt','lastvisit_ltmt_mt','letterquickjump','filtermembers','filterposts','filterjoined','showmembers','members','showmembers');


}

/* -- member_list_show --*/
function member_list_show($members, $pages="", $dropdowns=array(), $defaults=array(), $custom_fields=null, $url='') {
$IPBHTML = "";
if( IPSLib::locationHasHooks( 'skin_mlist', $this->_funcHooks['member_list_show'] ) )
{
$count_ce88aa663f84436a48a25e3a7db763ba = is_array($this->functionData['member_list_show']) ? count($this->functionData['member_list_show']) : 0;
$this->functionData['member_list_show'][$count_ce88aa663f84436a48a25e3a7db763ba]['members'] = $members;
$this->functionData['member_list_show'][$count_ce88aa663f84436a48a25e3a7db763ba]['pages'] = $pages;
$this->functionData['member_list_show'][$count_ce88aa663f84436a48a25e3a7db763ba]['dropdowns'] = $dropdowns;
$this->functionData['member_list_show'][$count_ce88aa663f84436a48a25e3a7db763ba]['defaults'] = $defaults;
$this->functionData['member_list_show'][$count_ce88aa663f84436a48a25e3a7db763ba]['custom_fields'] = $custom_fields;
$this->functionData['member_list_show'][$count_ce88aa663f84436a48a25e3a7db763ba]['url'] = $url;
}
$IPBHTML .= "<template>memberList</template>
{$pages}
" . ((is_array( $members ) and count( $members )) ? ("
	<members>
	".$this->__f__3ef7d779060ef8335cb78462ea809aa8($members,$pages,$dropdowns,$defaults,$custom_fields,$url)."	</members>
") : ("")) . "";
return $IPBHTML;
}


function __f__3ef7d779060ef8335cb78462ea809aa8($members, $pages="", $dropdowns=array(), $defaults=array(), $custom_fields=null, $url='')
{
	$_ips___x_retval = '';
	$__iteratorCount = 0;
	foreach( $members as $member )
	{
		
		$__iteratorCount++;
		$_ips___x_retval .= "
		<member>
			<id>{$member['member_id']}</id>
			<url>" . $this->registry->getClass('output')->formatUrl( $this->registry->getClass('output')->buildUrl( "showuser={$member['member_id']}", "public",'' ), "{$member['members_seo_name']}", "showuser" ) . "</url>
			<name>{$member['members_display_name']}</name>
			<postCount>{$member['posts']}</postCount>
			<group>{$member['group']}</group>
			<avatar>{$member['pp_thumb_photo']}</avatar>
		</member>
	
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