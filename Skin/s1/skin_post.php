<?php

class skin_post {



function nameField_unreg($data) {
global $ibforums;
return <<<EOF
<tr>
 <td colspan="2" class='pformstrip'>{$ibforums->lang['unreg_namestuff']}</td>
</tr>
<tr>
  <td class='pformleft'>{$ibforums->lang['guest_name']}</td>
  <td class='pformright'><input type='text' size='40' maxlength='40' name='UserName' value='$data' class='textinput' /></td>
</tr>

EOF;
}


function poll_box($data, $extra="") {
global $ibforums;
return <<<EOF
<tr>
  <td colspan="2" class='pformstrip'>{$ibforums->lang['tt_poll_settings']}</td>
</tr>
<tr>
  <td class='pformleft'><strong>{$ibforums->lang['poll_question']}</strong></td>
  <td class='pformright'><input type='text' size='40' maxlength='250' name='pollq' value='{$ibforums->input['pollq']}' class='textinput' /></td>
</tr>
<tr>
  <td class='pformleft'>{$ibforums->lang['poll_choices']}<br /><br />$extra</td>
  <td class='pformright'><textarea cols='60' rows='12' name='PollAnswers' class='textinput'>$data</textarea><!--IBF.POLL_OPTIONS--></td>
</tr>

EOF;
}

function poll_options() {
global $ibforums;
return <<<EOF
<br /><input type='checkbox' size='40' value='1' name='allow_disc' class='forminput' />&nbsp;{$ibforums->lang['poll_only']}
EOF;
}

function poll_end_form($data) {
global $ibforums;
return <<<EOF
 <tr>
  <td class='pformstrip' align='center' style='text-align:center' colspan="2">
	<input type="submit" name="submit" value="$data" tabindex='4' class='forminput' accesskey='s' />&nbsp;
  </td>
</tr>
</table>
</form>
<br />
<br clear="all" />
EOF;
}


function postbox_buttons($data) {
global $ibforums;
return <<<EOF
 <tr>
  <td class='pformstrip' colspan="2">{$ibforums->lang['post']}</td>
</tr>
<tr>
  <td class='pformleft' align='center'>
    <!--SMILIE TABLE-->
    <br /><div class='desc'><strong><a href='javascript:CheckLength()'>{$ibforums->lang['check_length']}</a></strong></div>
  </td>
  <td class="pformright" valign='top'>
    <!-- THE TEXTAREA: Now it just holds raw HTML -->
    <textarea name="Post" id="legacy-post-area" style="width:100%; height:350px; opacity:0;">$data</textarea>

    <script>
  // INITIALIZE TINYMCE
  tinymce.init({
      selector: '#legacy-post-area',
      height: 350,
      menubar: false,
      images_upload_url: 'tinymce_upload.php',
      automatic_uploads: true,
      images_reuse_filename: true,
      promotion: false,
      statusbar: false,
      plugins: 'image media link lists code emoticons codesample',
      toolbar: 'undo redo | bold italic underline blockquote | link media image emoticons | codesample',
      content_css: 'https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap',
     content_style: `body { font-family: 'Nunito', sans-serif; font-size: 15px; }
    blockquote {
         position: relative;
         font-size: 0.95rem;
         line-height: 1.6;
         margin: 1.5rem 0;
         padding: 5px 5px 5px 50px; 
         background-color: #f8fafc; 
         border: 1px solid #e2e8f0;
         border-left: 5px solid #007bff; 
         border-radius: 6px;
         color: #334155;
         font-style: italic;
     }

     blockquote::before {
         content: "“"; 
         position: absolute;
         left: 16px;
         top: 12px;
         font-family: Georgia, serif;
         font-size: 3rem;
         line-height: 1;
         color: #007bff;
         opacity: 0.3;
     }
`,
      quickbars_insert_toolbar: false,
      quickbars_selection_toolbar: false,

  
      images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
          const xhr = new XMLHttpRequest();
          xhr.withCredentials = false;
          xhr.open('POST', 'tinymce_upload.php');

          xhr.upload.onprogress = (e) => {
              progress(e.loaded / e.total * 100);
          };

          xhr.onload = () => {
              if (xhr.status === 403) {
                  reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                  return;
              }

              if (xhr.status < 200 || xhr.status >= 300) {
                  reject('HTTP Error: ' + xhr.status);
                  return;
              }

              const json = JSON.parse(xhr.responseText);

              if (!json || typeof json.location != 'string') {
                  reject('Invalid JSON: ' + xhr.responseText);
                  return;
              }

              resolve(json.location);
          };

          xhr.onerror = () => {
              reject('Image upload failed due to a Network Error.');
          };

          const formData = new FormData();
          formData.append('file', blobInfo.blob(), blobInfo.filename());

          xhr.send(formData);
      }),

      setup: function (ed) {
          ed.on('change keyup', function () {
              ed.save(); 
          });
      }
  }); 

  window.emoticon = function(char) {
      var ed = tinymce.get('legacy-post-area');
      if (ed) {
          ed.focus();
          ed.insertContent(char);
          ed.save(); 
      }
  };
</script>
  </td>
</tr>
   </td>
 </tr>
 <tr>
  <td class='pformleft'><b>{$ibforums->lang['po_options']}</b></td>
  <td class='pformright'>
   <!--IBF.EMO-->
   <!--IBF.SIG-->
   <!--IBF.TRACK-->
  </td>
 </tr>
EOF;
}

function pm_postbox_buttons($data) {
    // We simply call the main postbox function and pass the $data
    // This bridges the PM area to use the exact same TinyMCE setup as regular posts
    return $this->postbox_buttons($data);
}

function get_box_enableemo($checked) {
global $ibforums;
return <<<EOF
<input type='checkbox' name='enableemo' class='checkbox' value='yes' $checked />&nbsp;{$ibforums->lang['enable_emo']}
EOF;
}

function get_box_enablesig($checked) {
global $ibforums;
return <<<EOF
<br /><input type='checkbox' name='enablesig' class='checkbox' value='yes' $checked />&nbsp;{$ibforums->lang['enable_sig']}
EOF;
}

function get_box_enabletrack($checked) {
global $ibforums;
return <<<EOF
<br /><input type='checkbox' name='enabletrack' class='checkbox' value='1' $checked />&nbsp;{$ibforums->lang['enable_track']}
EOF;
}

function get_box_alreadytrack() {
global $ibforums;
return <<<EOF
<br />{$ibforums->lang['already_sub']}
EOF;
}


function get_javascript() {
global $ibforums;
return <<<EOF
<script language="javascript1.2" type="text/javascript">
<!--
var MessageMax  = "{$ibforums->lang['the_max_length']}";
var Override    = "{$ibforums->lang['override']}";
MessageMax      = parseInt(MessageMax);

if ( MessageMax < 0 )
{
	MessageMax = 0;
}
	
function emo_pop()
{
  window.open('index.{$ibforums->vars['php_ext']}?act=legends&CODE=emoticons&s={$ibforums->session_id}','Legends','width=250,height=500,resizable=yes,scrollbars=yes'); 
}
function bbc_pop()
{
  window.open('index.{$ibforums->vars['php_ext']}?act=legends&CODE=bbcode&s={$ibforums->session_id}','Legends','width=700,height=500,resizable=yes,scrollbars=yes'); 
}	
function CheckLength() {
	MessageLength  = document.REPLIER.Post.value.length;
	message  = "";
		if (MessageMax > 0) {
			message = "{$ibforums->lang['js_post']}: {$ibforums->lang['js_max_length']} " + MessageMax + " {$ibforums->lang['js_characters']}.";
		} else {
			message = "";
		}
		alert(message + "      {$ibforums->lang['js_used']} " + MessageLength + " {$ibforums->lang['js_characters']}.");
}
	
	function ValidateForm(isMsg) {
		MessageLength  = document.REPLIER.Post.value.length;
		errors = "";
		
		if (isMsg == 1)
		{
			if (document.REPLIER.msg_title.value.length < 2)
			{
				errors = "{$ibforums->lang['msg_no_title']}";
			}
		}
	
		if (MessageLength < 2) {
			 errors = "{$ibforums->lang['js_no_message']}";
		}
		if (MessageMax !=0) {
			if (MessageLength > MessageMax) {
				errors = "{$ibforums->lang['js_max_length']} " + MessageMax + " {$ibforums->lang['js_characters']}. {$ibforums->lang['js_current']}: " + MessageLength;
			}
		}
		if (errors != "" && Override == "") {
			alert(errors);
			return false;
		} else {
			document.REPLIER.submit.disabled = true;
			return true;
		}
	}
	
	// IBC Code stuff
	var text_enter_url      = "{$ibforums->lang['jscode_text_enter_url']}";
	var text_enter_url_name = "{$ibforums->lang['jscode_text_enter_url_name']}";
	var text_enter_image    = "{$ibforums->lang['jscode_text_enter_image']}";
	var text_enter_email    = "{$ibforums->lang['jscode_text_enter_email']}";
	var text_enter_flash    = "{$ibforums->lang['jscode_text_enter_flash']}";
	var text_code           = "{$ibforums->lang['jscode_text_code']}";
	var text_quote          = "{$ibforums->lang['jscode_text_quote']}";
	var error_no_url        = "{$ibforums->lang['jscode_error_no_url']}";
	var error_no_title      = "{$ibforums->lang['jscode_error_no_title']}";
	var error_no_email      = "{$ibforums->lang['jscode_error_no_email']}";
	var error_no_width      = "{$ibforums->lang['jscode_error_no_width']}";
	var error_no_height     = "{$ibforums->lang['jscode_error_no_height']}";
	var prompt_start        = "{$ibforums->lang['js_text_to_format']}";
	
	var help_bold           = "{$ibforums->lang['hb_bold']}";
	var help_italic         = "{$ibforums->lang['hb_italic']}";
	var help_under          = "{$ibforums->lang['hb_under']}";
	var help_font           = "{$ibforums->lang['hb_font']}";
	var help_size           = "{$ibforums->lang['hb_size']}";
	var help_color          = "{$ibforums->lang['hb_color']}";
	var help_close          = "{$ibforums->lang['hb_close']}";
	var help_url            = "{$ibforums->lang['hb_url']}";
	var help_img            = "{$ibforums->lang['hb_img']}";
	var help_email          = "{$ibforums->lang['hb_email']}";
	var help_quote          = "{$ibforums->lang['hb_quote']}";
	var help_list           = "{$ibforums->lang['hb_list']}";
	var help_code           = "{$ibforums->lang['hb_code']}";
	var help_click_close    = "{$ibforums->lang['hb_click_close']}";
	var list_prompt         = "{$ibforums->lang['js_tag_list']}";
	
	// Modern Editor Sync Bridge
	function syncEditorContent(htmlContent) {
		if (document.REPLIER && document.REPLIER.Post) {
			document.REPLIER.Post.value = htmlContent;
		}
	}
	
	//-->
</script>


EOF;
}


function nameField_reg() {
global $ibforums;
return <<<EOF
<!-- REG NAME -->
EOF;
}


function mod_options($jump) {
global $ibforums;
return <<<EOF
  <tr>
   <td class='pformstrip' colspan="2">{$ibforums->lang['tt_options']}</td>
  </tr>
  <tr>
    <td class='pformleft'>{$ibforums->lang['mod_options']}</td>
    <td class='pformright'>$jump</select></td>
  </tr>

EOF;
}


function quote_box($data) {

global $ibforums;

return <<<EOF
<script>
function safeInitTinyMCE(selector, config) {
    if (document.querySelector(selector)) {
        config.selector = selector;
        tinymce.init(config);
    }
}
tinymce.init({
    selector: '#quote-editor',
    height: 250,
    menubar: false,
    statusbar: false,
    toolbar: false, // This removes the top panel entirely
    plugins: 'autolink',
    content_css: 'https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap',
      content_style: `body { font-family: 'Nunito', sans-serif; font-size: 15px; }
    blockquote {
         position: relative;
         font-size: 0.95rem;
         line-height: 1.6;
         margin: 1.5rem 0;
         padding: 5px 5px 5px 50px; 
         background-color: #f8fafc; 
         border: 1px solid #e2e8f0;
         border-left: 5px solid #007bff; 
         border-radius: 6px;
         color: #334155;
         font-style: italic;
     }

     blockquote::before {
         content: "“"; 
         position: absolute;
         left: 16px;
         top: 12px;
         font-family: Georgia, serif;
         font-size: 3rem;
         line-height: 1;
         color: #007bff;
         opacity: 0.3;
     }
`,
    setup: function (editor) {
        editor.on('change', function () {
            editor.save(); 
        });
    }
});
</script>
<tr>

  <td colspan="2" class='pformstrip'>{$ibforums->lang['post_to_quote']}</td>

</tr>

<tr>

  <td class='pformleft'>{$ibforums->lang['post_to_quote_txt']}</td>

  <td class='pformright'><textarea cols='60' rows='12' wrap='soft' id='quote-editor' name='QPost' class='textinput'>{$data['post']}</textarea><input type='hidden' name='QAuthor' value='{$data['author_id']}' /><input type='hidden' name='QAuthorN' value='{$data['author_name']}' /><input type='hidden' name='QDate'   value='{$data['post_date']}' /></td>

</tr>



EOF;

}


function TopicSummary_top() {
global $ibforums;
return <<<EOF
<br />
<div class="tableborder">
  <div class="pformstrip">{$ibforums->lang['last_posts']}</div>
  <table cellpadding='6' cellspacing='1' border='0' width='100%'>
EOF;
}

function TopicSummary_body($data) {
global $ibforums;
return <<<EOF
  <tr>
    <td class='row4' valign='top' width='20%'><b>{$data['author']}</b></td>
    <td class='row4' valign='top' width='80%'>{$ibforums->lang['posted_on']} {$data['date']}</td>
  </tr>
  <tr>
    <td class='row1' valign='top' width='20%'>&nbsp;</td>
    <td class='row1' valign='top' width='80%'><span class='postcolor'>{$data['post']}</span></td>
  </tr>
EOF;
}


function TopicSummary_bottom() {
global $ibforums;
return <<<EOF

  </table>
  <div class="pformstrip"><a href="javascript:PopUp('index.{$ibforums->vars['php_ext']}?act=ST&amp;f={$ibforums->input['f']}&amp;t={$ibforums->input['t']}','TopicSummary',700,450,1,1)">{$ibforums->lang['review_topic']}</a></div>
</div>

EOF;
}



function preview($data) {
global $ibforums;
return <<<EOF
<div class="tableborder">
  <div class="pformstrip">{$ibforums->lang['post_preview']}</div>
  <div class="row1" style="padding:6px"><div class='postcolor'>$data</div></div>
</div>
<br />
EOF;
}





function edit_upload_field($data, $file_name="") {
global $ibforums;
return <<<EOF
<tr> 
          <td class="pformstrip" colspan="2">{$ibforums->lang['upload_title']}</td>
        </tr>
        <tr> 
          <td class='pformleft'>{$ibforums->lang['upload_text']} $data</td>
          <td class='pformright' width="100%">
           <table cellpadding='4' cellspacing='0' width='100%' border='0'>
            <tr>
             <td><input type='radio' name='editupload' value='keep' checked></td>
             <td width='100%'><b>{$ibforums->lang['eu_keep']}</b> ( $file_name )</td>
            </tr>
            <tr>
             <td><input type='radio' name='editupload' value='delete'></td>
             <td width='100%'><b>{$ibforums->lang['eu_delete']}</b></td>
            </tr>
            <tr>
             <td valign='middle'><input type='radio' name='editupload' value='new'></td>
             <td><b>{$ibforums->lang['eu_new']}</b><br /><input class='textinput' type='file' size='30' name='FILE_UPLOAD' onclick='document.REPLIER.editupload[2].checked=true;' /></td>
            </tr>
           </table>
          </td>
        </tr>
EOF;
}


function Upload_field($data) {
global $ibforums;
return <<<EOF
  <tr>
    <td colspan="2" class='pformstrip'>{$ibforums->lang['upload_title']}</td>
  </tr>
  <tr>
    <td class='pformleft'>{$ibforums->lang['upload_text']} $data</td>
    <td class='pformright'><input class='textinput' type='file' size='30' name='FILE_UPLOAD' /></td>
  </tr>
  
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




function EndForm($data) {
global $ibforums;
return <<<EOF
 <tr>
  <td class='pformstrip' align='center' style='text-align:center' colspan="2">
	<input type="submit" name="submit" value="$data" tabindex='4' class='forminput' accesskey='s' />&nbsp;
	<input type="submit" name="preview" value="{$ibforums->lang['button_preview']}" tabindex='5' class='forminput' />
  </td>
</tr>
</table>
</form>
<br />
<br clear="all" />
EOF;
}


function smilie_table() {
global $ibforums;
return <<<EOF
<table class='tablefill' cellpadding='4' align='center' style="font-family: 'Nunito', sans-serif; border-collapse: separate; border-spacing: 10px;">
    <tr>
        <td align="center" style="font-weight: bold; font-size: 14px; padding-bottom: 5px;">
            {$ibforums->lang['click_smilie']}
        </td>
    </tr>
    <tr>
        <td align="center">
            <!-- Modern Emoji Grid -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; font-size: 18px;">
                <a href="javascript:void(0)" onclick="emoticon('😀')" style="text-decoration: none;">😀</a>
                <a href="javascript:void(0)" onclick="emoticon('😂')" style="text-decoration: none;">😂</a>
                <a href="javascript:void(0)" onclick="emoticon('🙄')" style="text-decoration: none;">🙄</a>
                <a href="javascript:void(0)" onclick="emoticon('😎')" style="text-decoration: none;">😎</a>
                <a href="javascript:void(0)" onclick="emoticon('👍')" style="text-decoration: none;">👍</a>
                <a href="javascript:void(0)" onclick="emoticon('🔥')" style="text-decoration: none;">🔥</a>
                <a href="javascript:void(0)" onclick="emoticon('🤔')" style="text-decoration: none;">🤔</a>
                <a href="javascript:void(0)" onclick="emoticon('😮')" style="text-decoration: none;">😮</a>
                <a href="javascript:void(0)" onclick="emoticon('😢')" style="text-decoration: none;">😢</a>
                <a href="javascript:void(0)" onclick="emoticon('🎉')" style="text-decoration: none;">🎉</a>
                <a href="javascript:void(0)" onclick="emoticon('❤️')" style="text-decoration: none;">❤️</a>
                <a href="javascript:void(0)" onclick="emoticon('✨')" style="text-decoration: none;">✨</a>
                <a href="javascript:void(0)" onclick="emoticon('🚀')" style="text-decoration: none;">🚀</a>
                <a href="javascript:void(0)" onclick="emoticon('💯')" style="text-decoration: none;">💯</a>
                <a href="javascript:void(0)" onclick="emoticon('🙌')" style="text-decoration: none;">🙌</a>
            </div>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding-top: 10px; border-top: 1px solid #eee;">
            <!-- Triggers the full TinyMCE emoji picker -->
            <b><a href="javascript:void(0)" onclick="tinymce.execCommand('mceEmoticons');" style="color: #004a99; text-decoration: underline;">
                {$ibforums->lang['all_emoticons']}
            </a></b>
        </td>
    </tr>
</table>
EOF;
}




function PostIcons() {
    global $ibforums, $post_icons_list;
    
    $icons_html = "";
    
    // Point the loop to our new indestructible list
    if ( isset($post_icons_list) && is_array($post_icons_list) ) {
        foreach($post_icons_list as $id => $data) {

            // $data[0] = class, $data[1] = color, $data[2] = title
            $icons_html .= "<label style='margin-right:15px; display: inline-flex; align-items: center; cursor: pointer;'>
                                <input type='radio' class='radiobutton' name='iconid' value='{$id}' /> 
                                <i class='{$data[0]}' style='color:{$data[1]}' title='{$data[2]}'></i>
                            </label>";
        }
    }

    return <<<EOF
 <tr>
  <td class='pformleft'>{$ibforums->lang['post_icon']}</td>
  <td class='pformright' style='vertical-align:middle; line-height:2;'>
    {$icons_html}
    <br />
    <label><input type="radio" class="radiobutton" name="iconid" value="0" checked="checked" /> [ Use None ]</label>
  </td>
 </tr>
EOF;
}


function table_top($data) {
global $ibforums;
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

<table class='tableborder' cellpadding="0" cellspacing="0" width="100%">
<tr>
 <td class='maintitle' colspan="2">&nbsp;&nbsp;$data</td>
</tr>
      
EOF;
}




function table_structure() {
global $ibforums;
return <<<EOF
<!--FORUM RULES--><br />
<!--START TABLE-->
<!--NAME FIELDS-->
<!--TOPIC TITLE-->
<!--POLL BOX-->
<!--POST BOX-->
<!--QUOTE BOX-->
<!--POST ICONS-->
<!--UPLOAD FIELD-->
<!--MOD OPTIONS-->
<!--END TABLE-->
EOF;
}


function add_edit_box($checked="") {
global $ibforums;
return <<<EOF
<tr>
  <td class='pformleft'><b>{$ibforums->lang['edit_ops']}</b></td>
  <td class='pformright'><input type='checkbox' name='add_edit' value='1' $checked class='forminput' />&nbsp;{$ibforums->lang['append_edit']}</td>
</tr>
EOF;
}


function topictitle_fields($data) {
global $ibforums;
return <<<EOF
<tr>
 <td colspan="2" class='pformstrip'>{$ibforums->lang['tt_topic_settings']}</td>
</tr>
<tr>
  <td class='pformleft'>{$ibforums->lang['topic_title']}</td>
  <td class='pformright'><input type='text' size='40' maxlength='50' name='TopicTitle' value='{$data['TITLE']}' tabindex='1' class='forminput' /></td>
</tr>
<tr>
   <td class='pformleft'>{$ibforums->lang['topic_desc']}</td>
   <td class='pformright'><input type='text' size='40' maxlength='40' name='TopicDesc' value='{$data['DESC']}' tabindex='2' class='forminput' /></td>
</tr>
EOF;
}


}
?>