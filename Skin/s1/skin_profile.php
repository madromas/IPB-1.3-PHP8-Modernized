<?php

class skin_profile {

	function rep_options_links($stuff) {
    global $ibforums;
    return <<<EOF
<span style='font-size:1.1em; font-weight:bold;'>
    <a href='{$ibforums->base_url}act=rep&CODE=01&mid={$stuff['mid']}&t={$stuff['t']}' title='Upvote' style='color:#28a745; text-decoration:none;'>
        <i class="fa-solid fa-square-plus"></i>
    </a>
    <span style='color:#666; margin: 0 2px;'>|</span>
    <a href='{$ibforums->base_url}act=rep&CODE=02&mid={$stuff['mid']}&t={$stuff['t']}' title='Downvote' style='color:#dc3545; text-decoration:none;'>
        <i class="fa-solid fa-square-minus"></i>
    </a>
</span>
EOF;
}

function warn_level($mid, $img, $percent) {
global $ibforums;
return <<<EOF
  <tr>
	<td class="row3" valign='top'><b>{$ibforums->lang['warn_level']}</b></td>
	<td align='left' class='row1'><a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$mid}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$percent}</a>%: <a href='{$ibforums->base_url}act=warn&amp;type=minus&amp;mid={$mid}' title='{$ibforums->lang['tt_warn_minus']}'><{WARN_MINUS}></a>{$img}<a href='{$ibforums->base_url}act=warn&amp;type=add&amp;mid={$mid}' title='{$ibforums->lang['tt_warn_add']}'><{WARN_ADD}></a>
</td>
  </tr>
EOF;
}

function warn_level_no_mod($mid, $img, $percent) {
global $ibforums;
return <<<EOF
  <tr>
	<td class="row3" valign='top'><b>{$ibforums->lang['warn_level']}</b></td>
	<td align='left' class='row1'><a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$mid}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$percent}</a>%: {$img}</td>
  </tr>
EOF;
}

function warn_level_rating($mid, $level,$min=0,$max=10) {
global $ibforums;
return <<<EOF
 <tr>
	<td class="row3" valign='top'><b>{$ibforums->lang['rating_level']}</b></td>
	<td align='left' class='row1'><a href='{$ibforums->base_url}act=warn&amp;type=minus&amp;mid={$mid}' title='{$ibforums->lang['tt_warn_minus']}'><{WARN_MINUS}></a> &lt;&nbsp;$min ( <a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$mid}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$level}</a> ) $max&nbsp;&gt; <a href='{$ibforums->base_url}act=warn&amp;type=add&amp;mid={$mid}' title='{$ibforums->lang['tt_warn_add']}'><{WARN_ADD}></a></td>
  </tr>
EOF;
}

function warn_level_rating_no_mod($mid, $level,$min=0,$max=10) {
global $ibforums;
return <<<EOF
 <tr>
	<td class="row3" valign='top'><b>{$ibforums->lang['rating_level']}</b></td>
	<td align='left' class='row1'>&lt;&nbsp;$min ( <a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$mid}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$level}</a> ) $max&nbsp;&gt;</td>
  </tr>
EOF;
}

function get_photo($show_photo, $show_width, $show_height) {
global $ibforums;
return <<<EOF
<img src="$show_photo" border="0" alt="User Photo" $show_width $show_height />
EOF;
}

function show_photo($name, $photo) {
global $ibforums;
return <<<EOF
<div id="photowrap">
 <div id="phototitle">$name</div>
 <div id="photoimg">$photo</div>
</div>
EOF;
}

function show_card_download($name, $photo, $info) {
global $ibforums;
return <<<EOF
<html>
 <head>
  <title>$name</title>
  <META http-equiv="Content-Type" content="text/html;charset=windows-1251">
  <style type="text/css">
	 form { display:inline; }
	 img  { vertical-align:middle }
	 BODY { font-family: Verdana, Tahoma, Arial, sans-serif; font-size: 11px; color: #000; margin-left:5%;margin-right:5%;margin-top:5px;  }
	 TABLE, TR, TD { font-family: Verdana, Tahoma, Arial, sans-serif; font-size: 11px; color: #000; }
	 a:link, a:visited, a:active { text-decoration: underline; color: #000 }
	 a:hover { color: #465584; text-decoration:underline }
	 #profilename { font-size:28px; font-weight:bold; }
	 #photowrap { padding:6px; }
	 #phototitle { font-size:24px; border-bottom:1px solid black }
	 #photoimg   { text-align:center; margin-top:15px } 
	 .plainborder { border:1px solid #345487;background-color:#F5F9FD }
	 .tableborder { border:1px solid #345487;background-color:#FFF }
	 .tablefill   { border:1px solid #345487;background-color:#F5F9FD;padding:6px }
	 .tablepad    { background-color:#F5F9FD;padding:6px }
	 .tablebasic  { width:100%; padding:0px 0px 0px 0px; margin:0px; border:0px }
	 .row1 { background-color: #F5F9FD }
	 .row2 { background-color: #DFE6EF }
	 .row3 { background-color: #EEF2F7 }
	 .row4 { background-color: #E4EAF2 }
  </style>
  <script language='javascript' type="text/javascript">
  <!--
   function redirect_to(where, closewin)
   {
	  document.location= '$ibforums->base_url' + where;
	  
	  if (closewin == 1)
	  {
		  self.close();
	  }
   }
  //-->
  </script>
 </head>
<body>
<table width="100%" height="100%">
<tr>
 <td valign="middle" align="center" width="400">
	<div id="phototitle">$name</div>
	<br />
	<table class="tablebasic" cellspacing="6">
	<tr>
	 <td valign="middle" class="row1">$photo</td>
	 <td width="100%" class="row1" valign="bottom">
	   <table class="tablebasic" cellpadding="5">
		 <tr>
		   <td nowrap="nowrap">{$ibforums->lang['email']}</td>
		   <td width="100%">{$info['email']}</td>
		 </tr>
		 <tr>
		   <td nowrap="nowrap">{$ibforums->lang['pm']}</b></td>
		   <td><a href='javascript:redirect_to("&act=Msg&;CODE=4&MID={$info['mid']}", 1);'>{$ibforums->lang['click_here']}</a></td>
		 </tr>
		</td>
	   </tr>
	  </table>
	 </td>
	</tr>
	</table>
  </td>
 </tr>
</table>
</body>
</html>
EOF;
}

function show_card($name, $photo, $info) {
global $ibforums;
return <<<EOF
<script language='javascript' type="text/javascript">
<!--
 function redirect_to(where, closewin)
 {
 	opener.location= '$ibforums->base_url' + where;
 	
 	if (closewin == 1)
 	{
 		self.close();
 	}
 }
//-->
</script>
<div id="photowrap">
 <div id="phototitle">$name</div>
 <br />
 <table class="tablebasic" cellspacing="6">
 <tr>
  <td valign="middle" class="row1">$photo</td>
  <td width="100%" class="row1" valign="bottom">
    <table class="tablebasic" cellpadding="5">
      <tr>
        <td nowrap="nowrap">{$ibforums->lang['email']}</td>
		<td width="100%">{$info['email']}</td>
	  </tr>
	  <tr>
		<td nowrap="nowrap">{$ibforums->lang['pm']}</b></td>
		<td><a href='javascript:redirect_to("&amp;act=Msg&amp;CODE=4&amp;MID={$info['mid']}", 1);'>{$ibforums->lang['click_here']}</a></td>
	  </tr>
     </td>
    </tr>
   </table>
  </td>
 </tr>
 </table>
</div>
<div align="center">
  <a href="{$ibforums->base_url}act=Profile&amp;CODE=showcard&amp;MID={$info['mid']}&amp;download=1">{$ibforums->lang['ac_download']}</a>
  &middot; <a href="javascript:self.close();">{$ibforums->lang['ac_close']}</a>
</div>
EOF;
}



function user_edit($info) {
global $ibforums;
return <<<EOF
&middot; <a href='{$info['base_url']}act=UserCP&amp;CODE=22'>{$ibforums->lang['edit_my_sig']}</a> &middot;
<a href='{$info['base_url']}act=UserCP&amp;CODE=24'>{$ibforums->lang['edit_avatar']}</a> &middot;
<a href='{$info['base_url']}act=UserCP&amp;CODE=01'>{$ibforums->lang['edit_profile']}</a>
EOF;
}

function show_profile($info) {
global $ibforums, $INFO;
return <<<EOF

<script language='Javascript' type='text/javascript'>
		<!--
		function PopUp(url, name, width,height,center,resize,scroll,posleft,postop) {
			if (posleft != 0) { x = posleft }
			if (postop  != 0) { y = postop  }
		
			if (!scroll) { scroll = 1 }
			if (!resize) { resize = 1 }
		
			if ((parseInt (navigator.appVersion) >= 4 ) && (center)) {
			  X = (screen.width  - width ) / 2;
			  Y = (screen.height - height) / 2;
			}
			if (scroll != 0) { scroll = 1 }
		
			var Win = window.open( url, name, 'width='+width+',height='+height+',top='+Y+',left='+X+',resizable='+resize+',scrollbars='+scroll+',location=no,directories=no,status=no,menubar=no,toolbar=no');
	     }
		//-->
	</script>

<script language='Javascript' type='text/javascript'>
	<!--
	function PopUp(url, name, width,height,center,resize,scroll,posleft,postop) {
		if (posleft != 0) { x = posleft }
		if (postop  != 0) { y = postop  }
	
		if (!scroll) { scroll = 1 }
		if (!resize) { resize = 1 }
	
		if ((parseInt (navigator.appVersion) >= 4 ) && (center)) {
		  X = (screen.width  - width ) / 2;
		  Y = (screen.height - height) / 2;
		}
		if (scroll != 0) { scroll = 1 }
	
		var Win = window.open( url, name, 'width='+width+',height='+height+',top='+Y+',left='+X+',resizable='+resize+',scrollbars='+scroll+',location=no,directories=no,status=no,menubar=no,toolbar=no');
	 }
	//-->
</script>
<table class="tablebasic" cellspacing="0" cellpadding="2">
<tr>
 <td style="padding-right: 15px; vertical-align: middle;">
    <div style="border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.15); display: inline-block; overflow: hidden;">
        {$info['photo']}
    </div>
 </td>
 <td width="100%" style="vertical-align: middle;">
   <div style="display: flex; flex-direction: column; justify-content: center; min-height: 100px;">
     <div id="profilename" style="font-size: 2em; font-weight: bold; margin-bottom: 8px; line-height: 1.2;">{$info['name']}</div>
     <div style="font-size: 0.9em; color: #555;">
	   <a href='{$info['base_url']}act=Search&amp;CODE=getalluser&amp;mid={$info['mid']}'>{$ibforums->lang['find_posts']}</a> &middot;
	   <a href='{$info['base_url']}act=Msg&amp;CODE=02&amp;MID={$info['mid']}'>{$ibforums->lang['add_to_contact']}</a>
	   <!--MEM OPTIONS-->
	   </div>
   </div>
 </td>
</tr>
</table>
<br />
<table cellpadding='0' align='center' cellspacing='2' border='0' width="100%">
  <tr>
	<td width='50%' valign='top' class="plainborder">
	 <table cellspacing="1" cellpadding='6' width='100%'>
	  <tr>
		<td align='center' colspan='2' class='maintitle'>{$ibforums->lang['active_stats']}</td>
	  </tr>
	  <tr>
		<td class="row3" width='30%' valign='top'><b>{$ibforums->lang['total_posts']}</b></td>
		<td align='left' width='70%' class='row1'><b>{$info['posts']}</b><br />( {$info['total_pct']}% {$ibforums->lang['total_percent']} )</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['posts_per_day']}</b></td>
		<td align='left' class='row1'><b>{$info['posts_day']}</b></td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['joined']}</b></td>
		<td align='left' class='row1'><b>{$info['joined']}</b></td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['fav_forum']}</b></td>
		<td align='left' class='row1'><a href='{$info['base_url']}act=SF&amp;f={$info['fav_id']}'>{$info['fav_forum']}</a><br />{$info['fav_posts']} {$ibforums->lang['fav_posts']}<br />( {$info['percent']}% {$ibforums->lang['fav_percent']} )</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['user_local_time']}</b></td>
		<td align='left' class='row1'>{$info['local_time']}</td>
	  </tr>
	  </table>
	</td>
	
	<!-- Communication -->
	
   <td width='50%' valign='top' class="plainborder">
	 <table cellspacing="1" cellpadding='6' width='100%'>
	  <tr>
		<td align='center' colspan='2' class='maintitle'>{$ibforums->lang['communicate']}</td>
	  </tr>
	  <tr>
		<td class="row3" width='30%' valign='top'><b>{$ibforums->lang['email']}</b></td>
		<td align='left' width='70%' class='row1'>{$info['email']}</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['pm']}</b></td>
		<td align='left' class='row1'><a href='{$info['base_url']}act=Msg&amp;CODE=4&amp;MID={$info['mid']}'>{$ibforums->lang['click_here']}</a></td>
	  </tr>
	  </table>
	</td>
	
	<!-- END CONTENT ROW 1 -->
	<!-- information -->
	
  </tr>
  <tr>
	<td width='50%' valign='top' class="plainborder">
	 <table cellspacing="1" cellpadding='6' width='100%'>
	  <tr>
		<td align='center' colspan='2' class='maintitle'>{$ibforums->lang['info']}</td>
	  </tr>
	  <tr>
		<td class="row3" width='30%' valign='top'><b>{$ibforums->lang['homepage']}</b></td>
		<td align='left' width='70%' class='row1'>{$info['homepage']}</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['birthday']}</b></td>
		<td align='left' class='row1'>{$info['birthday']}</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['location']}</b></td>
		<td align='left' class='row1'>{$info['location']}</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['interests']}</b></td>
		<td align='left' class='row1'>{$info['interests']}</td>
	  </tr>
	  <!--{CUSTOM.FIELDS}-->
	  </table>
	</td>
	
	<!-- Profile -->
	
   <td width='50%' valign='top' class="plainborder">
	 <table cellspacing="1" cellpadding='6' width='100%'>
	  <tr>
		<td align='center' colspan='2' class='maintitle'>{$ibforums->lang['post_detail']}</td>
	  </tr>
	  <tr>
		<td class="row3" width='30%' valign='top'><b>{$ibforums->lang['mgroup']}</b></td>
		<td align='left' width='70%'  class='row1'>{$info['group_title']}</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['mtitle']}</b></td>
		<td align='left' class='row1'>{$info['member_title']}</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['avatar']}</b></td>
		<td align='left' class='row1 avatar'>{$info['avatar']}</td>
	  </tr>
	  <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['siggie']}</b></td>
		<td align='left' class='row1'>{$info['signature']}</td>
	  </tr>
	   <tr>
		<td class="row3" valign='top'><b>{$ibforums->lang['award']}</b></td>
		<td align='left' class='row1'>{$info['award']}</td>
	  </tr>
	   <tr>
        <td class="row3" valign='top'><b>{$ibforums->lang['rep_name']}:</b></td>
        <td align='left' class='row1'>{$info['rep']} <a href='{$ibforums->base_url}act=rep&CODE=03&mid={$info['mid']}'>{$ibforums->lang['rep_details']}</a></td>
      </tr>
	  <!--{WARN_LEVEL}-->
	  </table>
	</td>
	</tr>
	<tr>
    <td width='100%' valign='top' class="plainborder" colspan=2>
    	<table cellspacing="1" cellpadding='6' width='100%'>
        <tr>
        <td align='center' class='maintitle'>{$INFO['latest_amount']} {$ibforums->lang['latest_x_posts']} {$info['name']}</td>
        </tr>
        {$info['last_five']}
        </table>
    </td>
</tr>
</table>
<div class='tableborder'>
 <div class='pformstrip' align='center'>&lt;( <a href='javascript:history.go(-1)'>{$ibforums->lang['back']}</a> )</div>
</div>
	
EOF;
}

function custom_field($title, $value="") {
global $ibforums;
return <<<EOF
			<tr>
              <td class="row3" valign='top'><b>$title</b></td>
              <td align='left' class='row1'>$value</td>
            </tr>
EOF;
}

}
?>