<?php

class skin_downloads {

function wrapper($out,$ranpic_output) {
global $ibforums;
return <<<EOF

<table align="center" width="100%" border="0" cellspacing="1">
 <tr valign="top">
  <td align="left" width="20%" style='height:100%'>
<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$ibforums->lang['links']}</td>
  </tr>
</table>
   <table align='center' width='100%' border='1' cellspacing='0'>
    <tr>
	 <td class='row4' width='100%' align='left'><br />
	&nbsp;&middot;<a href="{$ibforums->base_url}act=Downloads">{$ibforums->lang['download_link']}</a><br />
	{$out['manage']}
	{$out['add']}
	{$out['edit']}
	{$out['favs']}
	&nbsp;&middot;<a href="{$ibforums->base_url}act=FileCP&amp;do=stats">{$ibforums->lang['stats_link']}</a><br />
	&nbsp;&middot;<a href="{$ibforums->base_url}act=Downloads&amp;do=search">{$ibforums->lang['search_link']}</a><br />
	<br />
	</td>
	</tr>
	</table><br />
	{$out['notes']}
	{$ranpic_output}
<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$ibforums->lang['stats']}</td>
  </tr>
</table>
   <table align='center' width='100%' border='1' cellspacing='0'>
    <tr>
	 <td class='row4' width='100%' align='left'><br />
	&nbsp;{$ibforums->lang['stats_files']}: {$out['files']}<br />
	&nbsp;{$ibforums->lang['stats_downs']}: {$out['down']}<br />
	&nbsp;{$ibforums->lang['stats_cats']}: {$out['cats']}<br />
	<br />
  </td>
  </tr>
  </table>
  </td>
  <td width="5%"><!-- Spacer --></td>
  <td width="75%" align="left">

EOF;
}

function random_pic($randompic){
global $ibforums;
return <<<EOF

	<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  	<tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$ibforums->lang['random_pic_header']}</td>
  	</tr>
	</table>
   <table align='center' width='100%' border='1' cellspacing='0'>
    <tr>
	 <td class='row4' width='100%' align='center'>
	 <br />
	<a href="{$ibforums->base_url}download={$randompic['id']}">
	<img src="{$randompic['screenshot']}" alt="{$randompic['name']}" border="0" width="150" /></a>
	<br />
	{$ibforums->lang['randompic_name']}: <a href="{$ibforums->base_url}download={$randompic['id']}">{$randompic['sname']}</a><br />
	{$ibforums->lang['randompic_author']}: <a href="{$ibforums->base_url}showuser={$randompic['mid']}">{$randompic['author']}</a><br />
	<br />
	</td>
	</tr>
	</table><br />	

EOF;
}

function show_global_notes($notes){
global $ibforums;
return <<<EOF

	<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  	<tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$ibforums->lang['global_notes_header']}</td>
  	</tr>
	</table>
   <table align='center' width='100%' border='1' cellspacing='0'>
    <tr>
	 <td class='row4' width='100%' align='center'>
	 <br />
		{$notes}<br />
	<br />
	</td>
	</tr>
	</table><br />	
	
EOF;
}

function size_row($info){
global $ibforums;
return <<<EOF

<tr><td class="row4" width="{$info['width1']}"><b>{$info['lang']}</b></td>
<td class="row4" width="{$info['width2']}">{$info['size']}</td></tr>

EOF;
}

function topic_row($info){
global $ibforums;
return <<<EOF

<tr><td class="row4" width="{$info['width1']}"><b>{$ibforums->lang['t_discussion']}</b></td>
<td class="row4" width="{$info['width2']}">{$info['topic']}</td></tr>

EOF;
}

function ip_view($info){
global $ibforums;
return <<<EOF

<tr><td class="row4" width="{$info['width1']}"><b>{$ibforums->lang['file_ipview']}</b></td>
<td class="row4" width="{$info['width2']}">{$info['ip_address']}</td></tr>

EOF;
}


function bottom( ) {
global $ibforums;
return <<<EOF

     </td>
 </tr>
</table>
<p align='center' id='copyright'>&copy;2003 {$ibforums->lang['copyright']}<br />
					   Based on code written by Parmeet Sing and Sno<br />
					   <a href='http://aceweb.ru/' target='_blank'>AW</a></p>

EOF;
}


function cats_header($cattitle){
global $ibforums;
return <<<EOF
<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$cattitle}</td>
  </tr>
</table>

EOF;
}

function cats_row_without_subs($row,$last) {
global $ibforums;
return <<<EOF
<table align="center" class='row4' width="100%" border="1" cellspacing="1" cellpadding="4">
<tr>
<td class="row2" width='100%' align='left'><span class="linkthru" align='left'><a href="index.php?dlcategory={$row['cid']}">{$row['cname']}</a></span><br /><br /><span class='desc'>{$row['cdesc']}</span><br /><br/>
{$ibforums->lang['total_files']}: {$last['files']}<br /><br />
<table width='80%' align='center' border='1' cellpadding='1' cellspacing='1' class='row4'>
 <tr>
  <td class='row4' colspan='2' align='center' width='100%'>{$ibforums->lang['latest_file_header']}</td></tr>
 <tr>
  <td class='row4' width='20%'>{$ibforums->lang['latest_file_title']}</td>
  <td class='row4' width='80%'>{$last['sname']}</td>
 </tr>
 <tr>
  <td class='row4' width='20%'>{$ibforums->lang['latest_file_author']}</td>
  <td class='row4' width='80%'>{$last['author']}</td>
 </tr>
 <tr>
  <td class='row4' width='20%'>{$ibforums->lang['latest_file_date']}</td>
  <td class='row4' width='80%'>{$last['date']}</td>
 </tr>
</table><br />
</td></tr></table>

EOF;
}

function cats_row_with_subs($row) {
global $ibforums;
return <<<EOF
<table align="center" class='row4' width="100%" border="1" cellspacing="1" cellpadding="4">
<tr>
<td class="row2" width='100%' align='left'><span class="linkthru" align='left'><a href="index.php?dlcategory={$row['cid']}">{$row['cname']}</a></span><br /><br /><span class='desc'>{$row['cdesc']}</span><br /><br />

<table width='80%' align='center' border='1' cellpadding='1' cellspacing='1' class='row4'>

EOF;
}

function cats_sub_row($row,$last) {
global $ibforums;
return <<<EOF
<tr>
<td class="row2" width='100%' align='left'><span class="linkthru" align='left'><a href="index.php?dlcategory={$row['cid']}">{$row['cname']}</a></span><br /><br /><span class='desc'>{$row['cdesc']}</span><br /><br />
{$ibforums->lang['total_files']}: {$last['files']}<br />
<table width='80%' align='center' border='1' cellpadding='1' cellspacing='1' class='row4'>
 <tr>
  <td class='row4' colspan='2' align='center' width='100%'>{$ibforums->lang['latest_file_header']}</td></tr>
 <tr>
  <td class='row4' width='20%'>{$ibforums->lang['latest_file_title']}</td>
  <td class='row4' width='80%'>{$last['sname']}</td>
 </tr>
 <tr>
  <td class='row4' width='20%'>{$ibforums->lang['latest_file_author']}</td>
  <td class='row4' width='80%'>{$last['author']}</td>
 </tr>
 <tr>
  <td class='row4' width='20%'>{$ibforums->lang['latest_file_date']}</td>
  <td class='row4' width='80%'>{$last['date']}</td>
 </tr>
</table><br />
</td></tr>

EOF;
}

function cats_row_with_subs_close() {
global $ibforums;
return <<<EOF

</table><br /></td></tr>

EOF;
}

function cats_bottom() {
global $ibforums;
return <<<EOF

</table>

EOF;
}

function cat_header($cattitle,$cats,$theoptions,$perpage) {
global $ibforums;
return <<<EOF

<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$cattitle}</td>
  </tr>
</table>
<table align="center" border="1" cellspacing="1" cellpading="0" class='row4' width="100%">
<tr>
<td align="center" class='titlemedium' width='100%'>
<form method="post" action="{$info['base_url']}">
<input type="hidden" name="act" value="Downloads" />
<input type="hidden" name="do" value="view" />
<input type="hidden" name="type" value="cat" />
<input type='hidden' name='cur_num' value='{$perpage}' />
{$ibforums->lang['show']}
	<select name="num" class='forminput'>
	{$theoptions}
    </select>
{$ibforums->lang['from']}
	<select name="cat" class='forminput'>
    {$cats}
	<option value="">------------</option>
	<option value="all">{$ibforums->lang['view_all']}</option>
    </select>
{$ibforums->lang['group_by']}
	<select name="group" class='forminput'>
    <option value="sname">{$ibforums->lang['s_name']}</option>
    <option value="author">{$ibforums->lang['s_author']}</option>
    <option value="date">{$ibforums->lang['s_date']}</option>
    <option value="views">{$ibforums->lang['s_views']}</option>
    <option value="downloads">{$ibforums->lang['s_downloads']}</option>
    <option value="rating">{$ibforums->lang['s_rating']}</option>
  	</select>
{$ibforums->lang['ordered_by']}
	<select name="order" class='forminput'>
    <option value="ASC">{$ibforums->lang['asc']}</option>
    <option value="DESC">{$ibforums->lang['des']}</option>
	</select>
{$ibforums->lang['by_order']}
&nbsp;&nbsp;<input type="submit" name="Submit" class='forminput' value="{$ibforums->lang['sort_but']}">
</form></td>
</tr></table>
<table align="center" width="100%" border="1" cellspacing="1" cellpadding="4">

EOF;
}

function cat_noss_header( ) {
global $ibforums;
return <<<EOF
<tr><td width='100%'>
<table align="center" width="100%" border="1" cellspacing="1" cellpadding="4">
                <tr>
                  <td nowrap='nowrap' class='titlemedium' width="25%">
                    <div align="center"><b>{$ibforums->lang['cat_head_name']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="15%">
                    <div align="center"><b>{$ibforums->lang['cat_head_author']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="10%">
                    <div align="center"><b>{$ibforums->lang['cat_head_date']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="10%">
                    <div align="center"><b>{$ibforums->lang['cat_head_updated']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="15%">
                    <div align="center"><b>{$ibforums->lang['cat_head_views']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="5%">
                    <div align="center"><b>{$ibforums->lang['cat_head_dls']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="5%">
                    <div align="center"><b>{$ibforums->lang['cat_head_rating']}</b></div>
                  </td>
                </tr>

EOF;
}

function files1($info) {
global $ibforums;
return <<<EOF

<tr>
<td align="center" class="row4"><span class='linkthru'><a href="{$ibforums->base_url}download={$info['id']}">{$info['sname']}</a></span>{$info['cat_extra']}</td>
<td align="center" class="row4">{$info['author']}</td>
<td align="center" class="row4">{$info['date']}</td>
<td align="center" class="row4">{$info['updated']}</td>
<td align="center" class="row4">{$info['views']}</td>
<td align="center" class="row4">{$info['downloads']}</td>
<td align="center" class="row4">{$info['rating']}</td>
</tr>
EOF;
}

function files($info){
global $ibforums;
return <<<EOF

<tr>
<td align="center" class="row4" width='50%'>
<span class='linkthru'><a href="{$ibforums->base_url}download={$info['id']}">{$info['screenshot']}</a></span></td>
<td width='50%'><table cellpadding='4' cellspacing='1' border='1' width='100%'><tr>
<td align="left" class="row4">{$ibforums->lang['files_name']}: <span class='linkthru'><a href="{$ibforums->base_url}download={$info['id']}">{$info['sname']}</a></span></td></tr><tr>
<td align="left" class="row4">{$ibforums->lang['files_author']}: {$info['author']}</td></tr><tr>
<td align="left" class="row4">{$ibforums->lang['files_date']}: {$info['date']}</td></tr><tr>
<td align="left" class="row4">{$ibforums->lang['files_updated']}: {$info['updated']}</td></tr><tr>
<td align="left" class="row4">{$ibforums->lang['files_views']}: {$info['views']}</td></tr><tr>
<td align="left" class="row4">{$ibforums->lang['files_downloads']}: {$info['downloads']}</td></tr><tr>
<td align="left" class="row4">{$ibforums->lang['files_rating']}: {$info['rating']}</td></tr></table></td>
</tr>

EOF;
}

function cat_noss_footer( ) {
global $ibforums;
return <<<EOF

</table></td></tr>

EOF;
}

function cat_bottom($numbers){
global $ibforums;
return <<<EOF
</table>
<table width="100%" align="center" border="0" cellspacing="1" cellpadding="0">
<tr>
<td align="left" border="0">{$numbers}</td>
</tr>
</table>
EOF;
}

function rating( $info ) {
global $ibforums;
return <<<EOF
  <tr>
    <td class="row2" width="10%"><b>{$ibforums->lang['ratenow']}</b></td>
 	<td class="row2" width="40%"><a href='{$ibforums->base_url}act=Downloads&amp;do=rating&amp;id={$info['id']}&amp;rate=1'><img src='{$ibforums->vars['img_url']}/pip.gif' alt='1' /></a><a href='{$ibforums->base_url}act=Downloads&amp;do=rating&amp;id={$info['id']}&amp;rate=2'><img src='{$ibforums->vars['img_url']}/pip.gif' alt='2' /></a><a href='{$ibforums->base_url}act=Downloads&amp;do=rating&amp;id={$info['id']}&amp;rate=3'><img src='{$ibforums->vars['img_url']}/pip.gif' alt='3' /></a><a href='{$ibforums->base_url}act=Downloads&amp;do=rating&amp;id={$info['id']}&amp;rate=4'><img src='{$ibforums->vars['img_url']}/pip.gif' alt='4' /></a><a href='{$ibforums->base_url}act=Downloads&amp;do=rating&amp;id={$info['id']}&amp;rate=5'><img src='{$ibforums->vars['img_url']}/pip.gif' alt='5' /></a>
    </td>
  </tr>	
EOF;
}

function file_view1($info,$cattitle){
global $ibforums;
return <<<EOF
<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$cattitle}</td>
  </tr>
</table>
<table align="center" border="1" cellspacing="1" cellpading="0" class='row4' width="100%">
  <tr>
    <td class="row2" width="10%"><b>{$ibforums->lang['file_name']}</b></td>
    <td class="row2" width="40%">{$info['sname']}</td>
  </tr>
  <tr>	
    <td class="row2" width="10%"><b>{$ibforums->lang['file_author']}</b></td>
    <td class="row2" width="40%">{$info['author']}</td>
  </tr>
{$info['ipaddress']}
      <tr><td colspan='2' width='100%'>&nbsp;</td></tr>
  <tr>
    <td class="row4" width="10%"><b>{$ibforums->lang['file_date']}</b></td>
    <td class="row4" width="40%">{$info['date']}</td>
  </tr>
  <tr>	
  <td class="row4" width="10%"><b>{$ibforums->lang['file_updated']}</b></td>
  <td class="row4" width="40%">{$info['updated']}</td>  
  </tr>
  <tr><td colspan='2' width='100%'>&nbsp;</td></tr>
{$info['filesize']}
{$info['linksize']}
  <tr>		
    <td class="row4" width="10%"><b>{$ibforums->lang['file_ext']}</b></td>
    <td class="row4" width="40%">{$info['ext']}</td>
  </tr>
  <tr>
    <td class="row2" width="10%"><b>{$ibforums->lang['file_screen']}</b></td>
    <td class="row2" width="40%">{$info['screenshot']}</td>
  </tr>
    <tr><td colspan='2' width='100%'>&nbsp;</td></tr>
	{$info['custom_out']}
  <tr>
    <td class="row4" width="10%"><b>{$ibforums->lang['file_views']}</b></td>
    <td class="row4" width="40%">{$info['views']}</td>
  </tr>
  <tr>		
    <td class="row4" width="10%"><b>{$ibforums->lang['file_downloads']}</b></td>
    <td class="row4" width="40%">{$info['downloads']}</td>
  </tr>
{$info['topicrow']}
    <tr><td colspan='2' width='100%'>&nbsp;</td></tr>
 <tr>
   <td class="row4" width="10%"><b>{$ibforums->lang['file_rating']}</b> </td>
   <td class="row4" width="40%">{$info['rating']}</td>
  </tr>
  {$info['rate_now']}
    <tr><td colspan='2' width='100%'>&nbsp;</td></tr>
  <tr> 
   <td class='row4' width='10%'><b>{$ibforums->lang['file_ops']}:</b> </td> 
   <td class="row4" width="40%">
<a href="{$ibforums->base_url}act=Downloads&amp;do=search&amp;type=do_search&amp;mid={$info['mid']}">{$ibforums->lang['file_all_subs']}</a><br />
{$info['favid']}
{$info['edit_op']}
</td></tr></table>
<br />
<table align="center" border="1" cellspacing="1" cellpading="5" width="100%">
<tr>
<td class='maintitle'><font size="2px"><b>{$ibforums->lang['file_desc']}</b></font></td>
</tr><tr><td width='100%' class='row4'><br />
{$info['sdesc']}<br />
</td></tr></table>
<br />
<table align="center" border="1" cellspacing="1" cellpading="5" width="100%">
<tr>
<td class='maintitle' align='center'><font size='+1'>{$info['dl_link']}</font></td>
</tr></table>
{$info['dis_comm_now']}
</td></tr></table>
EOF;
}

function file_view($info,$cattitle) {
global $ibforums;
return <<<EOF

<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$cattitle}</td>
  </tr>
</table>
<table align="center" border="1" cellspacing="1" cellpading="0" class='row4' width="100%">
  <tr>

   <td class="row4" width="100%" colspan='2' align="center" valign="middle" >{$info['screenshot']}</td>
</tr><tr>
<td class="row4" width="100%">
<table class='row4' valign='top' width='100%' border='1'><tr>

   <td class="row4" width="30%"><b>{$ibforums->lang['file_name']}</b></td>
    <td class="row4" width="70%">{$info['sname']}</td></tr>
<tr><td class='row4' width=100% colspan='2'>&nbsp;</td></tr>
<tr>
    <td class="row4" width="30%"><b>{$ibforums->lang['file_author']}</b></td>
    <td class="row4" width="70%" >{$info['author']}</td>
  </tr>
{$info['ipaddress']}
<tr><td class='row4' width=100% colspan='2'>&nbsp;</td></tr>
{$info['filesize']}
{$info['linksize']}
    <td class="row4" width="30%"><b>{$ibforums->lang['file_ext']}</b></td>
    <td class="row4" width="70%">{$info['ext']}</td>
  </tr>
<tr><td class='row4' width=100% colspan='2'>&nbsp;</td></tr>
{$info['custom_out']}
<tr>
    <td class="row4" width="30%"><b>{$ibforums->lang['file_views']}</b></td>
    <td class="row4" width="70%">{$info['views']}</td></tr>
<tr>
    <td class="row4" width="30%"><b>{$ibforums->lang['file_downloads']}</b></td>
    <td class="row4" width="70%">{$info['downloads']}</td>
  </tr>
{$info['topicrow']}
<tr><td class='row4' width=100% colspan='2'>&nbsp;</td></tr>
<tr>
    <td class="row4" width="30%"><b>{$ibforums->lang['file_date']}</b></td>
    <td class="row4" width="70%">{$info['date']}</td>
  </tr>
<tr>
    <td class="row4" width="30%"><b>{$ibforums->lang['file_updated']}</b></td>
    <td class="row4" width="70%">{$info['updated']}</td>
  </tr>
<tr><td class='row4' width=100% colspan='2'>&nbsp;</td></tr>
  <tr>
    <td class="row4" width="30%"><b>{$ibforums->lang['file_rating']}</b></td>
    <td class="row4" width="70%">{$info['rating']}
</td>
  </tr>
  {$info['rate_now']}
<tr><td class='row4' width=100% colspan='2'>&nbsp;</td></tr>

  <tr>
    <td class='row4' width='30%'><b>{$ibforums->lang['file_ops']}</b></td>
     <td class="row4" width='70%'><a href="{$ibforums->base_url}act=Downloads&amp;do=search&amp;type=do_search&amp;mid={$info['mid']}">{$ibforums->lang['file_all_subs']}</a><br />
	 {$info['favid']}
	 {$info['edit_op']}
</td>

  </tr></table>
</td></tr></table>
<br />
<table align="center" border="1" cellspacing="1" cellpading="5" width="100%">
<tr>
<td class='maintitle'><font size="2px"><b>{$ibforums->lang['file_desc']}</b></font></td>
</tr><tr><td width='100%' class='row4'><br />
{$info['sdesc']}<br />
</td></tr></table>
<br />
<table align="center" border="1" cellspacing="1" cellpading="5" width="100%">
<tr>
<td class='maintitle' align='center'><font size='+1'>{$info['dl_link']}</font></td>
</tr></table>
{$info['dis_comm_now']}
</td></tr></table>

EOF;
}

function custom_field($title, $value="") {
global $ibforums;
return <<<EOF
		<tr>
              <td class="row4" valign='top'><b>{$title}</b></td>
              <td align='left' class='row4'>{$value}</td>
            </tr>
EOF;
}

function comment_is_on($the_comments,$info){
global $ibforums;
$ibcode=$ibforums->member['g_d_ibcode_files']?"{$ibforums->lang['code_en']}":"{$ibforums->lang['code_dis']}";
$html=$ibforums->member['g_d_html_files']?"{$ibforums->lang['code_en']}":"{$ibforums->lang['code_dis']}";
return <<<EOF

<br />
	<script type="text/javascript">
	<!--
	function emo_pop()
	{
	  window.open('index.{$ibforums->vars['php_ext']}?act=legends&amp;CODE=emoticons&amp;s={$ibforums->session_id}','Legends','width=250,height=500,resizable=yes,scrollbars=yes'); 
	}
	//-->
	</script>

<table align="center" border="1" cellspacing="1" cellpading="5" width="100%">
<tr>
<td class='maintitle' colspan='2'><font size="2px"><b>{$ibforums->lang['comments_header']}</b></font></td>
</tr><tr><td width='100%' class='row4' colspan='2' align='left'><br />
{$the_comments}<br />
</td></tr>
<tr><td width='20%' class='row4' align='left'>
	  <i>{$ibforums->lang['ibcode_is']}{$ibcode}</i><br />
	  <i>{$ibforums->lang['html_is']}{$html}<i>
</td><td width='80%' class='row4' align='center'><br />
<form action='index.php?act=Downloads&amp;do=post' method='POST' name='REPLIER'>
<input type='hidden' name='id' value='{$info['id']}' />
<textarea name='Post' cols='35' rows='5'></textarea><br /><br />
<a href='javascript:emo_pop();'>{$ibforums->lang['show_emo']}</a> &#124;
 <input type='submit' class='forminput' value='{$ibforums->lang['submit_but']}' />
</form><br /><br />
</td></tr></table>

EOF;
}


function comment_row($com) {
global $ibforums;
return <<<EOF

{$ibforums->lang['comment_by']} {$com['name']}<br />
{$ibforums->lang['comment_on']} {$com['date']}<br /><br />
{$com['comment']}<br /><br />
<hr width='70%' align='center' />

EOF;
}

function field_entry($title, $content, $reqq) {
global $ibforums;
return <<<EOF
<tr>
  <td class='row4' valign='top'>{$title} {$reqq}</td>
  <td class='row4'>{$content}</td>
</tr>
EOF;
}

function field_textinput($name, $value="") {
global $ibforums;
return <<<EOF
<input type='text' size='30' name='{$name}' value='{$value}' class='forminput' />
EOF;
}

function field_dropdown($name, $options) {
global $ibforums;
return <<<EOF
<select name='{$name}' class='forminput'>{$options}</select>
EOF;
}

function field_textarea($name, $value) {
global $ibforums;
return <<<EOF
<textarea cols='60' rows='5' name='{$name}' class='forminput'>{$value}</textarea>
EOF;
}


function show_add_form($cats,$link,$required_output,$optional_output) {
global $ibforums;
$ibcode=$ibforums->member['g_d_ibcode_files']?"{$ibforums->lang['code_en']}":"{$ibforums->lang['code_dis']}";
$html=$ibforums->member['g_d_html_files']?"{$ibforums->lang['code_en']}":"{$ibforums->lang['code_dis']}";
return <<<EOF
<form action="?act=Downloads&amp;do=do_add" method="POST" enctype="multipart/form-data" name="bfarber" onSubmit="return checkForm(this)">
<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$ibforums->lang['title']} -> {$ibforums->lang['title_add']}</td>
  </tr>
</table>
<table align="center" border="1" cellspacing="1" cellpading="0" class='row4' width="100%">
 <tr>
  <td colspan='2' align='center' width='100%' class='row2'><br />{$ibforums->lang['whats_required']}<br /><br /></td>
 </tr> 
  <tr>
      <td width="30%" valign="top" class="row2">{$ibforums->lang['add_filename']} *</td>
    <td width="70%" valign="top" class="row4"><input type="text" name="sname"></td>
  </tr>
  <tr>
      <td width="30%" valign="top" class="row2">{$ibforums->lang['add_author']}</td>
    <td width="70%" valign="top" class="row4"><input type="text" name="author"></td>
  </tr>
  <tr>
      <td width="30%" valign="top" class="row4">{$ibforums->lang['add_desc']} *<br />
	  <i>{$ibforums->lang['ibcode_is']}{$ibcode}</i><br />
	  <i>{$ibforums->lang['html_is']}{$html}<i></td>
    <td width="70%" class="row4"><textarea name="desc" cols="38" rows="9"></textarea></td>
  </tr>
  <tr>
      <td width="30%" class="row4">{$ibforums->lang['add_cat']} *</td>
    <td width="70%" class="row4"><select name="cat">
          <option value="">{$ibforums->lang['choose_cat']}</option>
          {$cats}
        </select></td>
  </tr>
{$required_output}
{$optional_output}
{$link}
  <tr>
    <td width="100%" colspan='2' align='center' class="row4"><input type="submit" name="Submit" value="{$ibforums->lang['submit_but']}"></td>
  </tr>
</table>
</form>
EOF;
}

function screen_upload($sext,$ssreq) {
global $ibforums;
return <<<EOF
 <tr>
    <td width="30%" class="row4">{$ibforums->lang['add_screenshot']} {$ssreq}<br />
	{$ibforums->lang['max_file_size']}:&nbsp;{$ibforums->vars['d_screen_max_dwnld_size']}&nbsp;{$ibforums->lang['kb']}<br />
	{$ibforums->lang['allowed_ext']}&nbsp; {$sext}</td>
    <td width="70%" class="row4"><input type="file" name="screen"></td>
  </tr>
EOF;
}

function upload_file($ext) {
global $ibforums;
return <<<EOF
  <tr>
    <td width="30%" class="row4">{$ibforums->lang['add_file_browse']}<br />
	{$ibforums->lang['max_file_size']}:&nbsp;{$ibforums->vars['d_max_dwnld_size']}&nbsp;{$ibforums->lang['kb']}<br />
	{$ibforums->lang['allowed_ext']}&nbsp;{$ext}</td>
    <td width="70%" class="row4"><input type="file" name="file"></td>
  </tr>
EOF;
}
function link($default,$ext) {
global $ibforums;
return <<<EOF
  <tr>
    <td width="30%" class="row4">{$ibforums->lang['add_link_browse']}<br />
	{$ibforums->lang['allowed_ext']}&nbsp;{$ext}</td>
    <td width="70%" class="row4"><input type="text" name="link" value='{$default}'></td>
  </tr>
EOF;
}


function search_form($cats,$theoptions) {
global $ibforums;
return <<<EOF
<form action="index.php?act=Downloads&amp;do=search&amp;type=do_search" method="post">
<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$ibforums->lang['title']} -> {$ibforums->lang['title_search']}</td>
  </tr>
</table>
<table align="center" border="1" cellspacing="1" cellpading="0" class='row4' width="100%">
<tr>
<td align="center" class='titlemedium' width='100%'>
{$ibforums->lang['show']}
	<select name="num" class='forminput'>
	{$theoptions}
    </select>
{$ibforums->lang['group_by']}
	<select name="group" class='forminput'>
    <option value="sname">{$ibforums->lang['s_name']}</option>
    <option value="author">{$ibforums->lang['s_author']}</option>
    <option value="date">{$ibforums->lang['s_date']}</option>
    <option value="views">{$ibforums->lang['s_views']}</option>
    <option value="downloads">{$ibforums->lang['s_downloads']}</option>
    <option value="rating">{$ibforums->lang['s_rating']}</option>
  	</select>
{$ibforums->lang['ordered_by']}
	<select name="order" class='forminput'>
    <option value="ASC">{$ibforums->lang['asc']}</option>
    <option value="DESC">{$ibforums->lang['des']}</option>
	</select>
{$ibforums->lang['by_order']}
</td>
</tr></table>
<table align="center" border="1" cellspacing="1" cellpading="0" class='row4' width="100%">
<tr>
<td class='row4' width='20%'>
<div align="left"><b>{$ibforums->lang['search_name']} </b></div></td>
<td class='row4' width='80%'><div align="left"><input type="text" name="name"></div>
</td></tr><tr>
<td class='row4' width='20%'>
<div align="left"><b>{$ibforums->lang['search_author']} </b></div></td>
<td class='row4 width='80%'><div align="left"><input type="text" name="author"></div>
</td>
</tr>
<tr>
<td class='row4' width='20%'>
<div align="left"><b>{$ibforums->lang['search_desc']} </b></div></td>
<td class='row4' width='80%'><div align="left"><textarea name="desc" cols="35" rows="5"></textarea></div>
</td>
</tr>
<tr>
<td width="20%" class='row4'>
<div align="left"><b>{$ibforums->lang['search_cats']} </b></div></td>
<td width='80%' class='row4'><div align="left">
<select name="cat"><option value="">{$ibforums->lang['choose_cat']}</option>
          {$cats}
        </select></div>
</td>
</tr>
<tr>
<td colspan="2" width="100%" align='center' class='row4'><input type="Submit" value="{$ibforums->lang['search_but']}"></td>
</tr>
</table>

EOF;
}

function search_top( ) {
global $ibforums;
return <<<EOF
<table width="100%" align="center" border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td class="maintitle"><img src="{$ibforums->vars['img_url']}/nav_m.gif"> {$ibforums->lang['title']} -> {$ibforums->lang['title_search_results']}</td>
  </tr>
</table>
<table align="center" border="1" cellspacing="1" cellpading="0" class='row4' width="100%">
                <tr>
                  <td nowrap='nowrap' class='titlemedium' width="20%">
                    <div align="center"><b>{$ibforums->lang['s_name']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="15%">
                    <div align="center"><b>{$ibforums->lang['s_author']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="15%">
                    <div align="center"><b>{$ibforums->lang['s_cat']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="15%">
                    <div align="center"><b>{$ibforums->lang['s_date']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="15%">
                    <div align="center"><b>{$ibforums->lang['s_desc']}</b></div>
                  </td>
                  <td nowrap='nowrap' class='titlemedium' width="5%">
                    <div align="center"><b>{$ibforums->lang['s_downloads']}</b></div>
                  </td>
                </tr>
EOF;
}

function search($s_info) {
global $ibforums;
return <<<EOF
<tr>
<td align="center" class="row4"><span class='linkthru'><a href="{$ibforums->base_url}download={$s_info['id']}">{$s_info['sname']}</a></span></td>
<td align="center" class="row4">{$s_info['author']}</td>
<td align="center" class="row4"><span class='linkthru'><a href="{$ibforums->base_url}dlcategory={$s_info['cat']}">{$s_info['cname']}</a></span></td>
<td align="center" class="row4">{$s_info['date']}</td>
<td align="center" class="row4">{$s_info['sdesc']}</td>
<td align="center" class="row4">{$s_info['downloads']}</td>
</tr>
EOF;
}

function search_bottom($numbers) {
global $ibforums;
return <<<EOF
</table>
<table width="100%" align="center" border="0" cellspacing="1" cellpadding="0">
<tr>
<td align="left" border="0">{$numbers}</td>
</tr>
</table>
EOF;
}

}
?>
