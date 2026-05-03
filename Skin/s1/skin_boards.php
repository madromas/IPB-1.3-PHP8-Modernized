<?php

class skin_boards {

function Top_Five_Stats($TPosts,$TNew,$ToNew){
global $ibforums;
return <<<EOF
<div class="tableborder">
<div class=maintitle><{CAT_IMG}>&nbsp;<b>Statistics</b></div>
<table width="100%" border="0" cellspacing="1" cellpadding="4">
<tr>
<td class='titlemedium' width='40%'>new</td>
<td class='titlemedium' width='40%'>popular</td>
<td class='titlemedium' width='20%'>new user</td>
</tr>
<tr>
<td class='row2'>

<table width='100%'>
{$ToNew}
</table>

</td>
<td class='row2'>

<table width='100%'>
{$TPosts}
</table>

</td>
<td class='row2'>

<table width='100%'>
{$TNew}
</table>

</td></tr></table>
</div>
<br>
EOF;
}

function whoschatting_show($total, $names, $link, $txt) {
global $ibforums;
return <<<EOF
        <tr>
           <td class='pformstrip' colspan='2'>{$total} {$ibforums->lang['whoschatting_total']} <a href='$link'>{$ibforums->lang['whoschatting_loadchat']}</a></td>
    	</tr>
    	<tr>
          <td width="5%" class='row2'><{F_ACTIVE}></td>
          <td class='row4' width='95%'>
            {$names}<div class='desc' style='margin-top:5px'>$txt</div>
          </td>
        </tr>
EOF;
}

function whoschatting_empty($link) {
global $ibforums;
return <<<EOF
        <tr>
           <td class='pformstrip' colspan='2'>{$ibforums->lang['whoschatting_total']} <a href='$link'>{$ibforums->lang['whoschatting_loadchat']}</a></td>
    	</tr>
    	<tr>
          <td width="5%" class='row2'><{F_ACTIVE}></td>
          <td class='row4' width='95%'>
            <i>{$ibforums->lang['whoschatting_none']}</i>
          </td>
        </tr>
EOF;
}

function whoschatting_inline_link() {
global $ibforums;
return <<<EOF
{$ibforums->base_url}act=chat
EOF;
}

function whoschatting_popup_link() {
global $ibforums;
return <<<EOF
javascript:chat_pop({$ibforums->vars['chat_width']}, {$ibforums->vars['chat_height']});
EOF;
}

function active_list_sep() {
global $ibforums;
return <<<EOF
,
EOF;
}


function stats_header() {
global $ibforums;
return <<<EOF
<!-- Board Stats -->
	<div align='center'>
		<a href='{$ibforums->base_url}act=Stats&amp;CODE=leaders'>{$ibforums->lang['sm_forum_leaders']}</a> |
		<a href='{$ibforums->base_url}act=Search&amp;CODE=getactive'>{$ibforums->lang['sm_todays_posts']}</a> |
		<a href='{$ibforums->base_url}act=Stats'>{$ibforums->lang['sm_today_posters']}</a> |
		<a href='{$ibforums->base_url}act=Members&amp;max_results=10&amp;sort_key=posts&amp;sort_order=desc'>{$ibforums->lang['sm_all_posters']}</a>
	</div>
    <br />
	<div class="tableborder">
		<div class="maintitle">{$ibforums->lang['board_stats']}</div>
		<table cellpadding='4' cellspacing='1' border='0' width='100%'>
EOF;
}

function ActiveUsers($active) {
global $ibforums;
return <<<EOF
        <tr>
           <td class='pformstrip' colspan='2'>{$active['TOTAL']} {$ibforums->lang['active_users']}</td>
        </tr>
        <tr>
          <td width="5%" class='row2'><{F_ACTIVE}></td>
          <td class='row4' width='95%'>
            <b>{$active['GUESTS']}</b> {$ibforums->lang['guests']}, <b>{$active['MEMBERS']}</b> {$ibforums->lang['public_members']} <b>{$active['ANON']}</b> {$ibforums->lang['anon_members']}
            <div class='thin'>{$active['NAMES']}</div>
            {$active['links']}
          </td>
        </tr>
        EOF;
}

function active_user_links() {
global $ibforums;
return <<<EOF
{$ibforums->lang['oul_show_more']} <a href='{$ibforums->base_url}act=Online&amp;CODE=listall&amp;sort_key=click'>{$ibforums->lang['oul_click']}</a>, <a href='{$ibforums->base_url}act=Online&amp;CODE=listall&amp;sort_key=name&amp;sort_order=asc&amp;show_mem=reg'>{$ibforums->lang['oul_name']}</a>
EOF;
}

function ShowStats($text) {
global $ibforums;
return <<<EOF
		   <tr>
		     <td class='pformstrip' colspan='2'>{$ibforums->lang['board_stats']}</td>
		   </tr>
		   <tr>
			 <td class='row2' width='5%' valign='middle'><{F_STATS}></td>
			 <td class='row4' width="95%" align='left'>$text<br />{$ibforums->lang['most_online']}</td>
		   </tr>
EOF;
}

function birthdays($birthusers="", $total="", $birth_lang="") {
global $ibforums;
return <<<EOF
        <tr>
           <td class='pformstrip' colspan='2'>{$ibforums->lang['birthday_header']}</td>
    	</tr>
    	<tr>
          <td class='row2' width='5%' valign='middle'><{F_ACTIVE}></td>
          <td class='row4' width='95%'><b>$total</b> $birth_lang<br />$birthusers</td>
        </tr>
EOF;
}



function calendar_events($events = "") {
global $ibforums;
return <<<EOF
        <tr>
           <td class='pformstrip' colspan='2'>{$ibforums->lang['calender_f_title']}</td>
    	</tr>
    	<tr>
          <td class='row2' width='5%' valign='middle'><{F_ACTIVE}></td>
          <td class='row4' width='95%'>$events</td>
        </tr>
EOF;
}

function stats_footer() {
global $ibforums;
return <<<EOF
         </table>
	 </div>
    <!-- Board Stats -->
EOF;
}

function bottom_links() {
global $ibforums;
return <<<EOF
   <br />
   <div align='right'><a href="{$ibforums->base_url}act=Login&amp;CODE=06">{$ibforums->lang['d_delete_cookies']}</a> &middot; <a href="{$ibforums->base_url}act=Login&amp;CODE=05">{$ibforums->lang['d_post_read']}</a></div>
EOF;
}

          
function CatHeader_Expanded($Data) {
global $ibforums;
return <<<EOF
	<div class="tableborder">
	  <div class='maintitle' align='left'><{CAT_IMG}>&nbsp;<a href="{$ibforums->base_url}act=SC&c={$Data['id']}">{$Data['name']}</a></div>
      <table width="100%" border="0" cellspacing="1" cellpadding="4">
        <tr> 
          <th align="center" width="2%" class='titlemedium'><img src="{$ibforums->vars['img_url']}/spacer.gif" alt="" width="28" height="1" /></th>
          <th align="left" width="59%" class='titlemedium'>{$ibforums->lang['cat_name']}</th>
          <th align="center" width="7%" class='titlemedium'>{$ibforums->lang['topics']}</th>
          <th align="center" width="7%" class='titlemedium'>{$ibforums->lang['replies']}</th>
          <th align="left" width="25%" class='titlemedium'>{$ibforums->lang['last_post_info']}</th>
        </tr>
EOF;
}



function subheader() {
global $ibforums;
return <<<EOF
    <br />
	<div class="tableborder">
	  <table width="100%" border="0" cellspacing="1" cellpadding="4">
	  <tr> 
		<td align="center" class='titlemedium'><img src="{$ibforums->vars['img_url']}/spacer.gif" alt="" width="28" height="1" /></td>
		<th align='left' width="59%" class='titlemedium'>{$ibforums->lang['cat_name']}</th>
		<th align="center" width="7%" class='titlemedium'>{$ibforums->lang['topics']}</th>
		<th align="center" width="7%" class='titlemedium'>{$ibforums->lang['replies']}</th>
		<th align='left' width="27%" class='titlemedium'>{$ibforums->lang['last_post_info']}</th>
	  </tr>
EOF;
}

function end_this_cat() {
global $ibforums;
return <<<EOF
         <tr> 
          <td class='darkrow2' colspan="5">&nbsp;</td>
        </tr>
      </table>
    </div>
    <br />
EOF;
}

function end_all_cats() {
global $ibforums;
return <<<EOF
	
EOF;
}

function newslink( $fid="", $title="", $tid="" ) {
global $ibforums;
return <<<EOF
<b>{$ibforums->vars['board_name']} {$ibforums->lang['newslink']} <a href='{$ibforums->base_url}showtopic=$tid'>$title</a></b><br />
EOF;
}

function PageTop($lastvisit) {
global $ibforums;
return <<<EOF
<div align='left' style='text-align:left;padding-bottom:4px'>
  <!-- IBF.NEWSLINK -->{$ibforums->lang['welcome_back_text']} $lastvisit
</div><!-- STATPANEL -->
EOF;
}

function quick_log_in() {
global $ibforums;
return <<<EOF
<form style='display:inline' action="{$ibforums->base_url}act=Login&amp;CODE=01&amp;CookieDate=1" method="post">
<div align='right'><strong>{$ibforums->lang['qli_title']}</strong>
<input type="text" class="forminput" size="10" name="UserName" onfocus="this.value=''" value="{$ibforums->lang['qli_name']}" />
<input type='password' class='forminput' size='10' name='PassWord' onfocus="this.value=''" value='ibfrules' />
<input type='submit' class='forminput' value='{$ibforums->lang['qli_go']}' />
</div>
</form>
EOF;
}

function forum_img_with_link($img, $id) {
global $ibforums;
return <<<EOF
<a href='{$ibforums->base_url}act=Login&amp;CODE=04&amp;f={$id}' title='{$ibforums->lang['bi_markread']}'>{$img}</a>
EOF;
}

function subforum_img_with_link($img, $id) {
global $ibforums;
return <<<EOF
<a href='{$ibforums->base_url}act=Login&amp;CODE=04&amp;f={$id}&amp;i=1' title='{$ibforums->lang['bi_markallread']}'>{$img}</a>
EOF;
}


function ForumRow($info) {
global $ibforums;
return <<<EOF
        <tr> 
          <td class="row4" align="center">{$info['img_new_post']}</td>
          <td class="row4"><b><a href="{$ibforums->base_url}showforum={$info['id']}">{$info['name']}</a></b><br /><span class='desc'>{$info['description']}<br />{$info['moderator']}</span></td>
          <td class="row2" align="center">{$info['topics']}</td>
          <td class="row2" align="center">{$info['posts']}</td>
          <td class="row2" nowrap="nowrap">{$info['last_post']}<br />{$ibforums->lang['in']}:&nbsp;{$info['last_unread']}{$info['last_topic']}<br />{$ibforums->lang['by']}: {$info['last_poster']}</td>
        </tr>
EOF;
}

function forum_redirect_row($info) {
global $ibforums;
return <<<EOF
    <!-- Forum {$info['id']} entry -->
        <tr> 
          <td class="row4" align="center"><{BR_REDIRECT}></td>
          <td class="row4"><b><a href="{$ibforums->base_url}showforum={$info['id']}" {$info['redirect_target']}>{$info['name']}</a></b><br /><span class='desc'>{$info['description']}</span></td>
          <td class="row2" align="center">-</td>
          <td class="row2" align="center">-</td>
          <td class="row2">{$ibforums->lang['rd_hits']}: {$info['redirect_hits']}</td>
        </tr>
    <!-- End of Forum {$info['id']} entry -->
EOF;
}

function forumrow_lastunread_link($fid, $tid) {
global $ibforums;
return <<<EOF
<a href='{$ibforums->base_url}showtopic=$tid&amp;view=getlastpost' title='{$ibforums->lang['tt_golast']}'><{LAST_POST}></a>
EOF;
}

function WelcomePanel($data="") {
global $ibforums, $stats;

// Pre-process strings to avoid PHP 8 "Array to string conversion" inside heredoc
$lang_welcome = is_string($ibforums->lang['welcome_back'] ?? null) ? $ibforums->lang['welcome_back'] : "Welcome Back";
$lang_now     = is_string($ibforums->lang['its_now']     ?? null) ? $ibforums->lang['its_now']     : "";
$lang_lastv   = is_string($ibforums->lang['last_visit']   ?? null) ? $ibforums->lang['last_visit']   : "";
$lang_thbeen  = is_string($ibforums->lang['thbeen']       ?? null) ? $ibforums->lang['thbeen']       : "";
$lang_posts_n = is_string($ibforums->lang['posts_in']     ?? null) ? $ibforums->lang['posts_in']     : "posts in";
$lang_topics  = is_string($ibforums->lang['topics_since']  ?? null) ? $ibforums->lang['topics_since']  : "topics since your last visit";

return <<<EOF
    <div class="tableborder">
      <div class='maintitle' align='left'><{CAT_IMG}>&nbsp;{$lang_welcome} {$ibforums->member['name']}</div>
      <table width="100%" border="0" cellspacing="1" cellpadding="4">
        <tr> 
          <td class="row4" align="center">{$data['avatar']}</td>
          <td class="row2" width="45%">{$lang_now} {$data['time']}<br />
            {$lang_lastv} {$data['lastv']}<br />
            {$lang_thbeen} {$data['posts_scince']} {$lang_posts_n} {$data['topics_scince']} {$lang_topics}<br />
            <a href="{$ibforums->base_url}act=Search&amp;CODE=getnew">{$ibforums->lang['new_posts']}</a> | <a href="{$ibforums->base_url}act=Search&amp;CODE=getactive">{$ibforums->lang['sm_todays_posts']}</a>
          </td>
          <td class="row4" width="55%">
            {$ibforums->lang['tot_users']}: {$data['stats']['MEM_COUNT']} | {$ibforums->lang['tot_topics']}: {$data['stats']['TOTAL_TOPICS']} | {$ibforums->lang['tot_replies']}: {$data['stats']['TOTAL_REPLIES']} | {$ibforums->lang['tot_posts']}: {$data['stats']['TOTAL_POSTS']}<br />
            {$ibforums->lang['new_member']}: <a href="{$ibforums->base_url}showuser={$data['stats']['LAST_MEM_ID']}">{$data['stats']['LAST_MEM_NAME']}</a><br />
            {$ibforums->lang['top_starter']}: <a href="{$ibforums->base_url}showuser={$data['tt_id']}">{$data['tt_name']}</a> [{$data['tt_num']}]<br />
            {$ibforums->lang['top_poster']}: <a href="{$ibforums->base_url}showuser={$data['tp_id']}">{$data['tp_name']}</a> [{$data['tp_num']}]<br />
            {$ibforums->lang['most_online']}
          </td>
        </tr>
      </table>
    </div>
    <br />
EOF;
}

function GuestPanel($data="") {
global $ibforums, $stats;
return <<<EOF
	<div class="tableborder">
	  <div class='maintitle' align='left'><{CAT_IMG}>&nbsp;{$ibforums->lang['welcome_guest']} <a href="{$ibforums->base_url}&amp;act=Login&amp;CODE=00"><u>{$ibforums->lang['wel_login']}</u></a> {$ibforums->lang['wel_or']} <a href="{$ibforums->base_url}&amp;act=Reg&amp;CODE=00"><u>{$ibforums->lang['wel_reg']}</u></a>!</div>
      <table width="100%" border="0" cellspacing="1" cellpadding="4">
        <tr> 
          <td class="row4"><img src='{$ibforums->vars['board_url']}/html/avatars/noavatar.gif' border='0' alt='{$ibforums->lang['av_not_sel']}' /></td>
          <td class="row2" width="100%">{$ibforums->lang['its_now']} {$data['time']}<br />
		  {$ibforums->lang['today_thbeen']} {$data['posts_scince']} {$ibforums->lang['posts_in']} {$data['topics_scince']} темах<br />
		  <a href="{$ibforums->base_url}act=Search&amp;CODE=getactive">{$ibforums->lang['sm_todays_posts']}</a><br />
		  {$ibforums->lang['most_online']}
          </td>
          <td class="row4" align="right">
		  <form style='display:inline' action="{$ibforums->base_url}act=Login&amp;CODE=01&amp;CookieDate=1" method="post">
		    <table border="0" cellspacing="0" cellpadding="1">
			  <tr>
			    <td>{$ibforums->lang['youname']}:&nbsp;</td>
				<td><input type="text" class="forminput" size="20" name="UserName" onfocus="this.value=''" value="{$ibforums->lang['qli_name']}" /></td>
			  </tr>
			  <tr>
			    <td>{$ibforums->lang['youpasswd']}:&nbsp;</td>
				<td><input type='password' class='forminput' size='20' name='PassWord' onfocus="this.value=''" value='ibfrules!' /></td>
			  </tr>
			</table>
				<div align="center"><input type='submit' class='forminput' value='{$ibforums->lang['log_me']}' /></div>
		  </form>
          </td>
        </tr>
      </table>
    </div>
    <br />
EOF;
}


}
?>