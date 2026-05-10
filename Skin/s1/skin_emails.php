<?php

class skin_emails {

function board_rules( $title="", $body="") {
global $ibforums;
return <<<EOF
<div class="tableborder">
 <div class="pformstrip">$title</div>
 <div class="tablepad">$body</div>
</div>
EOF;
}


function errors($data) {
global $ibforums;
return <<<EOF
<div class="tableborder">
  <div class="pformstrip">{$ibforums->lang['errors_found']}</div>
  <div class="tablepad"><span class='postcolor'>$data</span></div>
</div>
<br />
EOF;
}

function chat_inline($acc_no, $lang, $w, $h, $user="",$pass="") {
global $ibforums;
return <<<EOF
<div class='tableborder'>
 <div class='maintitle'><{CAT_IMG}>&nbsp;{$ibforums->lang['chat_title']}</div>
 <div class='tablepad' align='center'>
  <applet
	 codebase="http://client.invisionchat.com/current/"
	 code="Client.class" archive="scclient_$lang.zip"
	 width=$w height=$h
	 style='border: 1px solid #000'>
	 <param name="room" value="$acc_no">
	 <param name="cabbase" value="scclient_$lang.cab">
	 <param name="username" value="$user">
	 <param name="password" value="$pass">
	 <param name="autologin" value="yes">
  </applet>
 </div>
</div>
<br />
<div class='tableborder'>
 <div class='maintitle'><{CAT_IMG}>&nbsp;{$ibforums->lang['chat_help']}</div>
 <div class='tablepad'>
   {$ibforums->lang['chat_help_text']}
 </div>
</div>
EOF;
}

function chat_pop($acc_no, $lang, $w, $h, $user="",$pass="") {
global $ibforums;
return <<<EOF
<div align='center'>
 <applet
	codebase="http://client.invisionchat.com/current/"
	code="Client.class" archive="scclient_$lang.zip"
	width=$w height=$h
	style='border: 1px solid <{tbl_border}>'>
	<param name="room" value="$acc_no">
	<param name="cabbase" value="scclient_$lang.cab">
	<param name="username" value="$user">
	<param name="password" value="$pass">
	<param name="autologin" value="yes">
 </applet>
</div>
EOF;
}

function report_form($fid, $tid, $pid, $st, $topic_title) {
global $ibforums;
return <<<EOF
<form action="{$ibforums->base_url}act=report&amp;send=1&amp;f=$fid&amp;t=$tid&amp;p=$pid&amp;st=$st" method="post" name='REPLIER'>
<div class='tableborder'>
  <div class='maintitle'><{CAT_IMG}>&nbsp;{$ibforums->lang['report_title']}</div>
  <div class='pformstrip'>&nbsp;</div>
  <table cellpadding='4' cellspacing='1' border='0' width='100%'>
   <tr>
   <td class='row1' align='left'  width='30%' valign='top'><b>{$ibforums->lang['report_topic']}</b></td>
   <td class='row1' width='80%'><a href='{$ibforums->base_url}showtopic=$tid&amp;st=$st&amp;&#35;entry$pid'>$topic_title</a>
   </td>
   </tr>
   <tr>
   <td class='row1' align='left'  width='30%' valign='top'>{$ibforums->lang['report_message']}</td>
   <td class='row1' width='80%'><textarea cols='60' rows='12' wrap='soft' name='message' class='textinput'></textarea>
   </td>
   </tr>
  </table>
  <div align='center' class='pformstrip'><input type="submit" value="{$ibforums->lang['report_submit']}" class='forminput' /></div>
 </div>
</form>
EOF;
}





function end_table() {
global $ibforums;
return <<<EOF
            <!-- End content Table -->
            </table>
            </td>
            </tr>
            <tr>
            <td class='darkrow1' colspan='2'>&nbsp;</td>
            </tr>
            </table>
EOF;
}

function pager_header($data) {
global $ibforums;
return <<<EOF
       <table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='<{tbl_border}>' align='center'>
        <tr>
            <td>
              <table cellpadding='4' cellspacing='0' border='0' width='100%'>
                <tr>
                   <td colspan='2' align='center' class='titlemedium'>{$data[TITLE]}</td>
EOF;
}



function forward_form($title, $text, $lang) {
global $ibforums;
return <<<EOF
<form action="{$ibforums->base_url}" method="post" name='REPLIER'>
<input type='hidden' name='act'  value='Forward'>
<input type='hidden' name='CODE' value='01'>
<input type='hidden' name='s'    value='{$ibforums->session_id}'>
<input type='hidden' name='st'   value='{$ibforums->input['st']}'>
<input type='hidden' name='f'    value='{$ibforums->input['f']}'>
<input type='hidden' name='t'    value='{$ibforums->input['t']}'>
<div class='tableborder'>
 <div class='maintitle'><{CAT_IMG}>&nbsp;{$ibforums->lang['title']}</div>
 <table cellpadding='4' cellspacing='0' border='0' width='100%'>
   <tr>
   <td class='row1' align='left'  width='30%' valign='top'><b>{$ibforums->lang['send_lang']}</b></td>
   <td class='row1' width='80%'>$lang</td>
   </tr>
   <tr>
   <td class='row1' align='left'  width='30%' valign='top'><b>{$ibforums->lang['to_name']}</b></td>
   <td class='row1' width='80%'><input type='text' class='forminput' name='to_name' value='' size='30' maxlength='100'></td>
   </tr>
   <tr>
   <td class='row1' align='left'  width='30%' valign='top'><b>{$ibforums->lang['to_email']}</b></td>
   <td class='row1' width='80%'><input type='text' class='forminput' name='to_email' value='' size='30' maxlength='100'></td>
   </tr>
   <tr>
   <td class='row1' align='left'  width='30%' valign='top'><b>{$ibforums->lang['subject']}</b></td>
   <td class='row1' width='80%'><input type='text' class='forminput' name='subject' value='{$title}' size='30' maxlength='120'></td>
   </tr>
   <tr>
   <td class='row1' align='left'  width='30%' valign='top'><b>{$ibforums->lang['message']}</b></td>
   <td class='row1' width='80%'><textarea cols='60' rows='12' wrap='soft' name='message' class='textinput'>{$text}</textarea>
   </td>
   </tr>
  </table>
  <div align='center' class='pformstrip'><input type="submit" value="{$ibforums->lang['submit_send']}" class='forminput' /></div>
</div>
</form>
EOF;
}

function show_address($data) {
global $ibforums;
return <<<EOF
<div class='tableborder'>
  <div class='maintitle'>{$ibforums->lang['send_email_to']} {$data[NAME]}</div>
  <div class='tablepad'>{$ibforums->lang['show_address_text']}
  <br />
  &gt;&gt;<b><a href="mailto:{$data[ADDRESS]}" class='misc'>{$ibforums->lang['send_email_to']} {$data[NAME]}</a></b>
 </div>
</div>
EOF;
}

function send_form($data) {
global $ibforums;
return <<<EOF
<form action="{$ibforums->base_url}" method="post" name='REPLIER'>
<input type='hidden' name='act' value='Mail'>
<input type='hidden' name='CODE' value='01'>
<input type='hidden' name='to' value='{$data['TO']}'>
<div><strong>{$ibforums->lang['imp_text']}</strong></div>
<br />
<div class='tableborder'>
  <div class='maintitle'>{$ibforums->lang['send_title']}</div>
  <div class='pformstrip'>{$ibforums->lang['send_email_to']} {$data['NAME']}</div>
  <table width='100%' cellspacing='1'>
	<tr>
	  <td class='pformleftw' valign='top'><b>{$ibforums->lang['subject']}</b></td>
	  <td class='pformright'><input type='text' name='subject' value='{$data['subject']}' size='50' maxlength='50' class='forminput' /></td>
	</tr>
	<tr>
	  <td class='pformleftw' valign='top'><b>{$ibforums->lang['message']}</b><br /><br />{$ibforums->lang['msg_txt']}</td>
	  <td class='pformright'><textarea cols='60' rows='12' wrap='soft' name='message' class='textinput'>{$data['content']}</textarea></td>
	</tr>
   </table>
   <div class='pformstrip' align='center'><input type="submit" value="{$ibforums->lang['submit_send']}" class='forminput' /></div>
</div>
</form>
EOF;
}

function sent_screen($member_name) {
global $ibforums;
return <<<EOF
<div class='tableborder'>
  <div class='maintitle'>{$ibforums->lang['email_sent']}</div>
  <div class='tablepad'>{$ibforums->lang['email_sent_txt']} $member_name</div>
</div>

EOF;
}

function forum_jump($data) {
global $ibforums;
return <<<EOF
      <table cellpadding='0' cellspacing='1' border='0' width='<{tbl_width}>' align='center'>
        <tr>
            <td align='right'>$data</td>
        </tr>
       </table>
EOF;
}


}
?>