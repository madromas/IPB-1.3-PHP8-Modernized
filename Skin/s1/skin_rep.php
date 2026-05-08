<?php

class skin_rep {

function rep_options_links($stuff) {
global $ibforums;
return <<<EOF
[ <a href='{$ibforums->base_url}act=rep&CODE=01&mid=$stuff[mid]&t=$stuff[t]'>+</a>
<span style='color:<{tbl_border}>'>|</span>
<a href='{$ibforums->base_url}act=rep&CODE=02&mid=$stuff[mid]&t=$stuff[t]'>—</a> ]
EOF;
}

function Links($links, $new) {
global $ibforums;
return <<<EOF
<div align="left"><table align="center" width="100%" celspacing="1" celpadding="4">
<tr><td align="left">{$links}</td>
<td align="right">{$new}</td></tr>
</table></div>
EOF;
}

function ShowForm($i) {
global $ibforums;
return <<<EOF
<script language="javascript">
<!--
	function Validate() {
		var Max = {$ibforums->vars['rep_msg_length']};
		Length = document.Reput.message.value.length;
		if (( Length > Max) && ( Max > 0 )) {
			alert("{$ibforums->lang['len_max']}" + Max + "{$ibforums->lang['len_current']}" + Length + "{$ibforums->lang['len_symbols']}");
			return false;
		} else {
			document.Reput.go.disabled = true;
			return true;
		}
	}
// -->
</script>
     <br>
     <form action="{$ibforums->base_url}" method="post" name='Reput' onSubmit='return Validate()'>
     <input type='hidden' name='CODE' value='{$i['code']}'>
     <input type='hidden' name='s' value='{$ibforums->session_id}'>
     <input type='hidden' name='rep_level' value="{$i['level']}">
     <input type='hidden' name='mid' value="{$i['memid']}">
     <input type='hidden' name='act' value='rep'>
     <input type='hidden' name='f' value='{$i['f']}'>
     <input type='hidden' name='t' value='{$i['t']}'>
     <input type='hidden' name='p' value='{$i['p']}'>
     <div class='tableborder'>
      <div class="maintitle">{$ibforums->lang['fill']}</div>
      <table cellpadding='4' cellspacing='1' border='0' width='100%' align='center'>
		<tr>
			<td class='row4' width='30%'>{$ibforums->lang['yourname']}</td> 
			<td class='row4'>{$ibforums->member['name']} {$i['anon']}</td>
		</tr>
		<tr>
			<td class='row4' width='30%'>{$ibforums->lang['whosename']}</td> 
			<td class='row4'>{$i['mem_name']}</td>
		</tr>
		<tr>
			<td class='row4' width='30%'>{$ibforums->lang['reason']}</td>
			<td class='row4'><textarea cols='60' rows='4' wrap='soft' name='message' class='textinput'></textarea></td>
		</tr>
        <tr>
			<td class='row4' width='30%'>{$ibforums->lang['act']}</td>
			<td class='row4'>{$i['action']}</td>
		</tr>
        <tr>
            <td class='darkrow1' width='20%'></td>
            <td class='darkrow1'><input type='submit' value='{$ibforums->lang['go']}' name='go'></td>
        </tr>
     </table>
    </div>
    </form>
EOF;
}

function ShowTitle($i) {
global $ibforums;
return <<<EOF
<div class='tableborder'>
 <div class="maintitle" align='center'>
 {$ibforums->lang['rep_name']} {$ibforums->lang['user']} <b>{$i['name']}</b>: {$i['rep']} [ +{$i['ups']} | -{$i['downs']} ] {$i['change']}
 </div>
 <table width='100%' cellpadding='4' cellspacing='1' border='0'>
  <tr>
EOF;
}

function ShowSelfTitle($i) {
global $ibforums;
return <<<EOF
<div class='tableborder'>
 <div class="maintitle" align='center'>
 <b>{$i['name']}</b> {$ibforums->lang['has_changed']} {$i['times']} {$ibforums->lang['has_times']} [ +{$i['ups']} | -{$i['downs']} ]
 </div>
 <table width='100%' cellpadding='4' cellspacing='1' border='0'>
  <tr>
EOF;
}

function ShowHeader() {
global $ibforums;
return <<<EOF
				<th align='center' class='pformstrip' width='15%'>{$ibforums->lang['who']}</td>
				<th align='center' class='pformstrip'>{$ibforums->lang['where']}</td>
				<th align='center' class='pformstrip'>{$ibforums->lang['why']}</td>
				<th align='center' class='pformstrip' width='5%'>{$ibforums->lang['code']}</td>
				<th align='center' class='pformstrip' width='20%'>{$ibforums->lang['when']}</td>
				</tr>
EOF;
}

function ShowSelfHeader() {
global $ibforums;
return <<<EOF
				<th align='center' class='pformstrip' width='15%'>{$ibforums->lang['whom']}</td>
				<th align='center' class='pformstrip'>{$ibforums->lang['where']}</td>
				<th align='center' class='pformstrip'>{$ibforums->lang['why']}</td>
				<th align='center' class='pformstrip' width='5%'>{$ibforums->lang['code']}</td>
				<th align='center' class='pformstrip' width='20%'>{$ibforums->lang['when']}</td>
				</tr>
EOF;
}

function ShowRow($i) { 
global $ibforums;
return <<<EOF
				<tr>
				<td class='row2' width='15%' align='center'>{$i['name']}</td>
				<td class='row2' width='25%'><a href={$i['url']}>{$i['title']}</a></td>
				<td class='row4'>{$i['message']}</td>
				<td align='center' class='row2' width='5%'><img src='{$i['img']}' border='0'></td>
				<td align='center' class='row4' width='15%'>{$i['date']}{$i['admin_undo']}</td>
				</tr>
EOF;
}

function ShowNone() {
global $ibforums;
return <<<EOF
<tr>
	<td align='center' colspan='6' class='row4'>{$ibforums->lang['no_changes']}</td>
</tr>
EOF;
}

function ShowFooter($link) {
global $ibforums;
return <<<EOF
                <tr>
				<td align='center' colspan='6' class='darkrow1'><a href='$link'>{$ibforums->lang['back']}</a></td>
                </tr>
				</table>
     </div>
EOF;
}

function StatsLinks($title) {
global $ibforums;
return <<<EOF
<div class='tableborder'>
 <div class="maintitle" align='center'>
 {$ibforums->lang['rep_name']}, $title
 </div>
 <table width="100%" border="0" cellspacing="1" cellpadding="4">
  <tr>
   <th align='center' class='pformstrip' width='50%'>{$ibforums->lang['member']}</th>
   <th align='center' class='pformstrip' width='25%'>{$ibforums->lang['rep_name']}</th>
   <th align='center' class='pformstrip' width='25%'>{$ibforums->lang['rep_name']}{$ibforums->lang['given']}</th>
  </tr>
EOF;
}

function ShowTotalsRow($i) { 
global $ibforums;
return <<<EOF
				<tr>
				<td class='row2' align='center' width='50%'>{$i['name']}</td>
				<td class='row4' align='center' width='25%'>{$i['rep']}</td>
				<td class='row4' align='center' width='25%'>{$i['times']}</td>
				</tr>
EOF;
}

function Page_end() {
global $ibforums;
return <<<EOF
	<form action='{$ibforums->base_url}act=rep&CODE=totals' method='POST'>
        <tr> 
          <td class='pformstrip' colspan="3" align='center' valign='middle'>
          {$ibforums->lang['sorting_text']}&nbsp;<input type='submit' value='{$ibforums->lang['sort_submit']}' class='forminput'></td>
        </tr>
	</form>
EOF;
}

function EditComment($i) {
global $ibforums;
return <<<EOF
<script language="javascript">
<!--
	function Validate() {
		var Max = {$ibforums->vars['rep_msg_length']};
		Length = document.Reput.comment.value.length;
		if (( Length > Max) && ( Max > 0 )) {
			alert("{$ibforums->lang['len_max']}" + Max + "{$ibforums->lang['len_current']}" + Length + "{$ibforums->lang['len_symbols']}");
			return false;
		} else {
			document.Reput.go.disabled = true;
			return true;
		}
	}
// -->
</script>
     <br>
     <form action="{$ibforums->base_url}" method="post" name='Reput' onSubmit='return Validate()'>
     <input type='hidden' name='CODE' value='save_comment'>
     <input type='hidden' name='s' value='{$ibforums->session_id}'>
     <input type='hidden' name='act' value='rep'>
     <input type='hidden' name='id' value='{$i['msg_id']}'>
     <div class='tableborder'>
      <div class="maintitle">{$ibforums->lang['fill']}</div>
      <table cellpadding='4' cellspacing='1' border='0' width='100%' align='center'>
		<tr>
			<td class='row4' width='30%'>{$ibforums->lang['yourname']}</td> 
			<td class='row4'>{$ibforums->member['name']}</td>
		</tr>
		<tr>
			<td class='row4' width='30%'>{$ibforums->lang['commented_message']}</td>
			<td class='row4'>{$i['message']}</td>
		</tr>
        <tr>
			<td class='row4' width='30%'>{$ibforums->lang['your_comment']}</td>
			<td class='row4'><textarea cols='60' rows='4' wrap='soft' name='comment' class='textinput'>{$i['comment']}</textarea></td>
		</tr>
        <tr>
            <td class='darkrow1' width='20%'></td>
            <td class='darkrow1'><input type='submit' value='{$ibforums->lang['go']}' name='go'></td>
        </tr>
     </table>
    </div>
    </form>
EOF;
}

}
?>