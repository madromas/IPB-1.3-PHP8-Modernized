<?php

class skin_topic {

    function rep_options_links($stuff) {
    global $ibforums;
    return <<<EOF
<span style='white-space:nowrap;'>
    <a href='{$ibforums->base_url}act=rep&CODE=01&mid={$stuff['mid']}&f={$stuff['f']}&t={$stuff['t']}&p={$stuff['p']}' title='Positive Rep' style='color:#28a745; text-decoration:none;font-size:10px;'>
        <i class="fa-solid fa-circle-plus"></i>
    </a>
    <a href='{$ibforums->base_url}act=rep&CODE=02&mid={$stuff['mid']}&f={$stuff['f']}&t={$stuff['t']}&p={$stuff['p']}' title='Negative Rep' style='color:#dc3545; text-decoration:none;font-size:10px;'>
        <i class="fa-solid fa-circle-minus"></i>
    </a>
</span>
EOF;
}

function latest_posts($data) {
global $ibforums;
return <<<EOF
    <table cellpadding='0' cellspacing='0' border='0' width='100%' class='tableborder' align='center'>
      <tr>
        <td>
          <table cellpadding='4' cellspacing='0' border='0' width='100%'>
           <tr>
             <td colspan='2' class='maintitle' >{$ibforums->lang['latest_posts']}</td>
           </tr>
           <tr>
                 <td class='row5' colspan='2'>
                     {$data}
                     </td>
               </tr>           
              </table>
             </td>
           </tr>
          </table>
<br>
EOF;
}


function warn_level_warn($id, $percent) {
global $ibforums;
return <<<EOF
{$ibforums->lang['tt_warn']} (<a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$id}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$percent}</a>%)
EOF;
}

function warn_level_rating($id, $level,$min=0,$max=10) {
global $ibforums;
return <<<EOF
&lt;&nbsp;$min ( <a href="javascript:PopUp('{$ibforums->base_url}act=warn&amp;mid={$id}&amp;CODE=view','Pager','500','450','0','1','1','1')">{$level}</a> ) $max&nbsp;&gt;
EOF;
}


function report_link($data) {
global $ibforums;
return <<<EOF
<a href='{$ibforums->base_url}act=report&amp;f={$data['forum_id']}&amp;t={$data['topic_id']}&amp;p={$data['pid']}&amp;st={$ibforums->input['st']}'><{P_REPORT}></a>
EOF;
}

function ip_show($data) {
global $ibforums;
return <<<EOF
<span class='desc'><center>{$ibforums->lang['ip']}: $data</center></span>
EOF;
}

function golastpost_link($fid, $tid) {
global $ibforums;
return <<<EOF
( <a href='{$ibforums->base_url}act=ST&amp;f=$fid&amp;t=$tid&amp;view=getnewpost'>{$ibforums->lang['go_new_post']}</a> )
EOF;
}

function mm_start($tid) {
global $ibforums;
return <<<EOF
<br />
<form action='{$ibforums->base_url}act=mmod&amp;t=$tid' method='post'>
<input type='hidden' name='check' value='1'>
<select name='mm_id' class='forminput'>
<option value='-1'>{$ibforums->lang['mm_title']}</option>
EOF;
}

function mm_entry($id, $name) {
global $ibforums;
return <<<EOF
<option value='$id'>$name</option>
EOF;
}

function mm_end() {
global $ibforums;
return <<<EOF
</select>&nbsp;<input type='submit' value='{$ibforums->lang['mm_submit']}' class='forminput' /></form>
EOF;
}

function Mod_Panel($data, $fid, $tid, $key="") {
global $ibforums;
return <<<EOF
  <div align='left' style='float:left;width:auto'>
    <form method='POST' style='display:inline' name='modform' action='{$ibforums->base_url}'>
    <input type='hidden' name='t' value='$tid' />
    <input type='hidden' name='f' value='$fid' />
    <input type='hidden' name='st' value='{$ibforums->input['st']}' />
    <input type='hidden' name='auth_key' value='$key' />
    <input type='hidden' name='act' value='Mod' />
    <select name='CODE' class='forminput' style="font-weight:bold;color:red">
    <option value='-1' style='color:black'>{$ibforums->lang['moderation_ops']}</option>
    $data
    </select>&nbsp;<input type='submit' value='{$ibforums->lang['jmp_go']}' class='forminput' /></form>
  </div>
        
EOF;
}

function mod_wrapper($id="", $text="") {
global $ibforums;
return <<<EOF
<option value='$id'>$text</option>
EOF;
}


function start_poll_link($fid, $tid) {
global $ibforums;
return <<<EOF
    <a href="{$ibforums->base_url}act=Post&amp;CODE=14&amp;f=$fid&amp;t=$tid" title="{$ibforums->lang['new_poll_link']}"><i class="fa-solid fa-chart-bar" style="color: #465584"></i></a> &nbsp;&nbsp;
EOF;
}



function PageTop($data) {
global $ibforums;
return <<<EOF
    <script language='javascript' type='text/javascript'>
    <!--
    
    function link_to_post(pid)
    {
        temp = prompt( "{$ibforums->lang['tt_prompt']}", "{$ibforums->base_url}showtopic={$ibforums->input['t']}&view=findpost&p=" + pid );
        return false;
    }
    
    function delete_post(theURL) {
       if (confirm('{$ibforums->lang['js_del_1']}')) {
          window.location.href=theURL;
       }
       else {
          alert ('{$ibforums->lang['js_del_2']}');
       } 
    }
    
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
    
    function ShowHide(id1, id2) {
      if (id1 != '') expMenu(id1);
      if (id2 != '') expMenu(id2);
    }
    
    function expMenu(id) {
      var itm = null;
      if (document.getElementById) {
        itm = document.getElementById(id);
      } else if (document.all){
        itm = document.all[id];
      } else if (document.layers){
        itm = document.layers[id];
      }
    
      if (!itm) {
       // do nothing
      }
      else if (itm.style) {
        if (itm.style.display == "none") { itm.style.display = ""; }
        else { itm.style.display = "none"; }
      }
      else { itm.visibility = "show"; }
    }
    //-->
    </script>
    
<a name='top'></a>
<!--IBF.FORUM_RULES-->

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
 <td align='left' width="20%" nowrap="nowrap">{$data['TOPIC']['SHOW_PAGES']}&nbsp;{$data['TOPIC']['go_new']}</td>
 <td align='right' width="80%" class="bottommenu">{$data['TOPIC']['REPLY_BUTTON']}<a href='{$ibforums->base_url}act=Post&amp;CODE=00&amp;f={$data['FORUM']['id']}' title='{$ibforums->lang['start_new_topic']}'><{A_POST}></a>{$data['TOPIC']['POLL_BUTTON']}</td>
</tr>
</table>
<br />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
 <td vAlign="top" width="79%">

<div class="tableborder">
    <div class='maintitle'>
        <!-- Right-aligned section -->
        <div style='float: right; font-weight: normal; display: flex; align-items: center;'>
            
            <div class="share-wrap" style="position: relative; margin-right: 10px;">

    <a href="javascript:void(0);" class="share-btn" title="Share" onclick="toggleShareMenu(event);">
    </a>
    
    <div id="share-menu-id" class="share-menu" style="display: none;">
        
        <!-- Facebook -->
    <a rel="nofollow" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={$ibforums->base_url}showtopic={$data['TOPIC']['tid']}">
        <i class="fab fa-facebook"></i> Facebook
    </a>

    <!-- X (Twitter) -->
    <a rel="nofollow" target="_blank" href="https://twitter.com/intent/tweet?url={$ibforums->base_url}showtopic={$data['TOPIC']['tid']}&text={$data['TOPIC']['title']}">
        <i class="fab fa-x-twitter"></i> X / Twitter
    </a>

    <!-- Reddit -->
    <a rel="nofollow" target="_blank" href="https://www.reddit.com/submit?url={$ibforums->base_url}showtopic={$data['TOPIC']['tid']}&title={$data['TOPIC']['title']}">
        <i class="fab fa-reddit"></i> Reddit
    </a>

    <!-- LinkedIn -->
    <a rel="nofollow" target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url={$ibforums->base_url}showtopic={$data['TOPIC']['tid']}">
        <i class="fab fa-linkedin"></i> LinkedIn
    </a>
    
    <!-- WhatsApp (Great for mobile users) -->
    <a rel="nofollow" target="_blank" href="https://api.whatsapp.com/send?text={$data['TOPIC']['title']}%20{$ibforums->base_url}showtopic={$data['TOPIC']['tid']}">
        <i class="fab fa-whatsapp"></i> WhatsApp
    </a>

    <!-- Copy Link -->
    <a href="javascript:void(0);" onclick="navigator.clipboard.writeText('{$ibforums->base_url}showtopic={$data['TOPIC']['tid']}'); alert('Link copied!');">
        <i class="fas fa-link"></i> Copy Link
    </a>
        
    </div>
</div>

            <div class="post-actions-wrap">
                <a href="{$ibforums->base_url}act=fav&topic={$data['TOPIC']['tid']}" title="{$data['TOPIC']['fav_title']}">
    <i class="{$data['TOPIC']['fav_icon']}" style="color: {$data['TOPIC']['fav_color']};"></i>
                </a> 
            </div>
            
        </div>

        <!-- Left-aligned Title -->
        <{CAT_IMG}>&nbsp;{$data['TOPIC']['title']} {$data['TOPIC']['description']} 
    </div>
    <!--{IBF.POLL}-->
    <div align='right' class='postlinksbar'>
      <!--{IBF.START_NEW_POLL}--><a href='{$ibforums->base_url}act=Track&amp;f={$data['FORUM']['id']}&amp;t={$data['TOPIC']['tid']}' title="{$ibforums->lang['track_topic']}"><i class="fa-solid fa-eye" style="color: #465584"></i></a> &nbsp;&nbsp; 
<a href='{$ibforums->base_url}act=Forward&amp;f={$data['FORUM']['id']}&amp;t={$data['TOPIC']['tid']}' title="{$ibforums->lang['forward']}"><i class="fa-solid fa-envelope" style="color: #465584"></i></a> &nbsp;&nbsp; 
<a href='{$ibforums->base_url}act=Print&amp;client=printer&amp;f={$data['FORUM']['id']}&amp;t={$data['TOPIC']['tid']}' title="{$ibforums->lang['print']}"><i class="fa-solid fa-print" style="color: #465584"></i></a>
    </div>
    
EOF;
}


function RenderRow($post, $author) {
global $ibforums;

$author_tag = "";
if (isset($post['topic_starter_id']) && $author['id'] > 0) {
    if ($author['id'] == $post['topic_starter_id']) {
        $author_tag = '<span class="post-author" title="Original Poster">OP</span>';
    }
}

return <<<EOF

    <!--Begin Msg Number {$post['pid']}-->
    <table width='100%' border='0' cellspacing='1' cellpadding='3'>
    <tr>
      <td align='center' valign='middle' class='row4' width="1%"><a name='entry{$post['pid']}'></a><span class='{$post['name_css']}'>{$author['name']}</span> {$author_tag}</td>
        <td class='row4' valign='top' width="99%">
        
        <!-- POSTED DATE DIV -->
        
        <div align='left' class='row4' style='float:left;padding-top:2px;padding-bottom:2px'>
        {$post['post_icon']}<span class='postdetails'><b><a title="{$ibforums->lang['tt_link']}" href="#" onclick="link_to_post({$post['pid']}); return false;">{$ibforums->lang['posted_on']}</a></b> {$post['post_date']}</span>
        </div>
        
        <!-- REPORT / DELETE / EDIT / QUOTE DIV -->
        
        <div align='right' class="btn-post-control">
        {$post['report_link']}{$post['delete_button']}{$post['edit_button']}<a href='{$ibforums->base_url}act=Post&amp;CODE=06&amp;f={$ibforums->input['f']}&amp;t={$ibforums->input['t']}&amp;p={$post['pid']}'><{P_QUOTE}></a>
      </div>
      
      </td>
    </tr>
    <tr>
      <td align='center' valign='top' class='{$post['post_css']}'>
    <span class='postdetails'>
        {$author['member_status']}
        <div class="avatar">{$author['avatar']}</div>
        {$author['title']}
        {$author['member_rank_img']}
        {$author['member_group']}
        {$author['member_posts']}
        {$author['member_joined']}
        {$author['award']}
        <div class="rep-box">{$author['rep']} {$post['rep_options']}</div>
        <br><br>
        <div class="warn-box">
            {$author['warn_text']} {$author['warn_minus']}{$author['warn_img']}{$author['warn_add']}
        </div>
    </span>
    <img src='{$ibforums->vars['img_url']}/spacer.gif' alt='' width='150' height='1' />
</td>
      <td width='100%' valign='top' class='{$post['post_css']}'>
      <div style='float:right; margin-left:10px;'>
        {$post['topic_rating_box']}
    </div>

    <div class='postcolor'>
        {$post['post']} 
        {$post['attachment']}
    </div>
        {$post['signature']}
        <!-- THE POST -->
      </td>
    </tr>
    <tr>
      <td class='darkrow3' align='left'><b>{$post['ip_address']}</b></td>
      <td class='darkrow3' nowrap="nowrap" align='left'>
      
        <!-- PM / EMAIL / WWW / MSGR -->
      
        <div align='left' class='darkrow3 btn-post-control' style='float:left;width:auto'>
        {$author['addresscard']}{$author['message_icon']}{$author['email_icon']}{$author['website_icon']}
        </div>
        
        <!-- REPORT / UP -->
         
        <div align='right' class='btn-post-control'>
        
        <a title="To quickly quote this message, highlight the text and click here." onmouseover="get_selection();" 
   onclick="Insertranged(window.txt, '{$post['name']}', '{$post['post_date']}'); return false;" 
   href="#">🗩</a>
        
        <a href='javascript:scroll(0,0);'>UP</a>
        </div>
      </td>
    </tr>
    </table>
    <div class='darkrow1' style='height:5px'><!-- --></div>
    
EOF;
}

function TableFooter($data) {
global $ibforums;
return <<<EOF
      <!--IBF.QUICK_REPLY_NEW-->
      <!--IBF.TOPIC_ACTIVE-->

<div class="activeuserstrip" align="center">&laquo; <a href='{$ibforums->base_url}showtopic={$data['TOPIC']['tid']}&amp;view=old'>{$ibforums->lang['t_old']}</a> &#0124; <strong><a href='{$ibforums->base_url}showforum={$data['FORUM']['id']}'>{$data['FORUM']['name']}</a></strong> &#0124; <a href='{$ibforums->base_url}act=showtopic={$data['TOPIC']['tid']}&amp;view=new'>{$ibforums->lang['t_new']}</a> &raquo;</div>
</div> </td> 

<td width="1%"></td> 
 <td vAlign="top" width="20%" class="topic-sidebar">
    {$data['latest_posts']}
 </td>

</tr>
</table>

<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
 <td align='left' width="20%" nowrap="nowrap"><!--IBF.TOPIC_OPTIONS_CLOSED-->{$data['TOPIC']['SHOW_PAGES']}</td>
 <td align='right' width="80%" class="bottommenu">{$data['TOPIC']['REPLY_BUTTON']}<a href='{$ibforums->base_url}act=Post&amp;CODE=00&amp;f={$data['FORUM']['id']}' title='{$ibforums->lang['start_new_topic']}'><{A_POST}></a>{$data['TOPIC']['POLL_BUTTON']}</td>
</tr>
</table>

<!--IBF.TOPIC_OPTIONS_OPEN-->

<br />
<!--IBF.MOD_PANEL-->
<div align='right'>{$data['FORUM']['JUMP']}</div>
<!--IBF.MULTIMOD-->
<br />
EOF;
}

function topic_opts_open($fid, $tid) {
global $ibforums;
return <<<EOF
<div id='topic_open' style='display:none;z-index:2;'>
    <div class="tableborder">
      <div class='maintitle'><{CAT_IMG}>&nbsp;<a href="javascript:ShowHide('topic_open','topic_closed')">{$ibforums->lang['to_close']}</a></div>
      <div class='tablepad'>
       <b><a href='{$ibforums->base_url}act=Track&amp;f={$fid}&amp;t={$tid}'>{$ibforums->lang['tt_title']}</a></b>
       <br />
       <span class='desc'>{$ibforums->lang['tt_desc']}</span>
       <br /><br />
       <b><a href='{$ibforums->base_url}act=Track&amp;f={$fid}&amp;type=forum'>{$ibforums->lang['ft_title']}</a></b>
       <br />
       <span class='desc'>{$ibforums->lang['ft_desc']}</span>
       <br /><br />
       <b><a href='{$ibforums->base_url}act=Print&amp;client=choose&amp;f={$fid}&amp;t={$tid}'>{$ibforums->lang['av_title']}</a></b>
       <br />
       <span class='desc'>{$ibforums->lang['av_desc']}</span>
     </div>
   </div>
</div>
EOF;
}

function topic_opts_closed() {
global $ibforums;
return <<<EOF
<a href="javascript:ShowHide('topic_open','topic_closed')" title="{$ibforums->lang['to_open']}"><{T_OPTS}></a>
EOF;
}


function topic_active_users($active=array()) {
global $ibforums;
return <<<EOF
      <div class="activeuserstrip">{$ibforums->lang['active_users_title']} ({$ibforums->lang['active_users_detail']})</div>
      <div class='row2 group-legend' style='padding:6px'>{$ibforums->lang['active_users_members']} {$active['names']}</div>
EOF;
}

function Show_attachments_img($file_name) {
global $ibforums;
return <<<EOF
<br />
<br />
<strong><span class='edit'>{$ibforums->lang['pic_attach']}</span></strong>
<br />
<img src='{$ibforums->vars['upload_url']}/$file_name' class='attach' alt='{$ibforums->lang['pic_attach']}' />
EOF;
}

function Show_attachments_img_thumb($file_name, $width, $height, $aid) {
global $ibforums;
return <<<EOF
<br />
<br />
<strong><span class='edit'>{$ibforums->lang['pic_attach_thumb']}</span></strong>
<br />
<a href='{$ibforums->base_url}act=Attach&amp;type=post&amp;id=$aid' title='{$ibforums->lang['pic_attach_thumb']}' target='_blank'><img src='{$ibforums->vars['upload_url']}/$file_name' width='$width' height='$height' class='attach' alt='{$ibforums->lang['pic_attach']}' /></a>
EOF;
}

function Show_attachments($data) {
global $ibforums;
return <<<EOF
<br />
<br />
<strong><span class='edit'>{$ibforums->lang['attached_file']} ( {$ibforums->lang['attach_hits']}: {$data['hits']} )</span></strong>
<br />
<a href='{$ibforums->base_url}act=Attach&amp;type=post&amp;id={$data['pid']}' title='{$ibforums->lang['attach_dl']}' target='_blank'><img src='{$ibforums->vars['mime_img']}/{$data['image']}' border='0' alt='{$ibforums->lang['attached_file']}' /></a>
&nbsp;<a href='{$ibforums->base_url}act=Attach&amp;type=post&amp;id={$data['pid']}' title='{$ibforums->lang['attach_dl']}' target='_blank'>{$data['name']}</a>
EOF;
}

function quick_reply_new($fid="",$tid="",$key="") {
global $ibforums;
return <<<EOF
<script language="javascript1.2" type="text/javascript">
<!--
var MessageMax  = "{$ibforums->lang['the_max_length']}";
var Override    = "{$ibforums->lang['override']}";
MessageMax      = parseInt(MessageMax);

if ( MessageMax < 0 ) { MessageMax = 0; }

function safeInitTinyMCE(selector, config) {
    if (document.querySelector(selector)) {
        config.selector = selector;
        tinymce.init(config);
    }
}

// TinyMCE Initialization for Quick Reply

tinymce.PluginManager.add('spoiler_plugin', function(editor) {
    
    editor.ui.registry.addIcon('eye_icon', 
       '<svg width="20" height="20" viewBox="0 0 16 16" focusable="false" fill="currentColor" style="display: block;"><path fill-rule="evenodd" d="M3.03 1.97a.75.75 0 0 0-1.06 1.06l.83.83A8.206 8.206 0 0 0 .5 6.876l-.26.585a1.328 1.328 0 0 0 0 1.079l.26.585a8.208 8.208 0 0 0 11.434 3.87l1.036 1.035a.75.75 0 1 0 1.06-1.06zm7.788 9.908l-1.294-1.293a3 3 0 0 1-4.109-4.109L3.866 4.927A6.707 6.707 0 0 0 1.87 7.486L1.641 8l.23.515a6.708 6.708 0 0 0 8.947 3.363M6.55 7.611A1.502 1.502 0 0 0 8.389 9.45zm1.658-2.604l2.784 2.784a3 3 0 0 0-2.784-2.784m5.92 3.508a6.704 6.704 0 0 1-.915 1.496l1.065 1.066A8.203 8.203 0 0 0 15.5 9.125l.26-.585a1.328 1.328 0 0 0 0-1.08l-.26-.584A8.208 8.208 0 0 0 5.572 2.37L6.81 3.61a6.708 6.708 0 0 1 7.32 3.877l.228.514l-.228.515Z" clip-rule="evenodd"></path></svg>'
    );

    editor.ui.registry.addToggleButton('spoiler', {
        icon: 'eye_icon',
        tooltip: 'Insert Spoiler Tag',
        onAction: function (api) {
            var selectedText = editor.selection.getContent({ format: 'text' });
            if (selectedText.length === 0) {
                editor.insertContent('[spoiler]Spoiler text[/spoiler]');
            } else {
                editor.insertContent('[spoiler]' + selectedText + '[/spoiler]');
            }
            api.setActive(!api.isActive());
        }
    });
});

tinymce.init({
    selector: '#qr-editor',
    height: 300,
    menubar: false,
    images_upload_url: 'tinymce_upload.php',
      automatic_uploads: true,
      images_reuse_filename: true,
    statusbar: false,
    plugins: 'autolink link image media emoticons codesample spoiler_plugin',
    toolbar: 'bold italic underline spoiler|link image media emoticons|codesample',
    content_css: 'https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap',
      content_style: "body { font-family: 'Nunito', sans-serif; font-size: 15px; }",
    
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

          

const urlParams = new URLSearchParams(window.location.search);
let forumId = urlParams.get('f') || '0';

forumId = parseInt(forumId.replace(/[^\d]/g, ''), 10) || 0;

formData.append('forum_id', forumId);


          xhr.send(formData);
      }),

    setup: function (editor) {
        editor.on('change', function () {
            editor.save(); 
        });
    }
});

function CheckLength() {
    tinymce.triggerSave();
    MessageLength = document.REPLIER.Post.value.length;
    message = "";
    if (MessageMax > 0) {
        message = "{$ibforums->lang['js_post']}: {$ibforums->lang['js_max_length']} " + MessageMax + " {$ibforums->lang['js_characters']}.";
    }
    alert(message + "      Used: " + MessageLength + " characters.");
}

function ValidateForm(isMsg) {
    // Sync TinyMCE to the textarea before checking length
    tinymce.triggerSave();
    
    MessageLength = document.REPLIER.Post.value.length;
    errors = "";

    if (isMsg == 1 && document.REPLIER.msg_title.value.length < 2) {
        errors = "{$ibforums->lang['msg_no_title']}";
    }

    if (MessageLength < 2) {
         errors = "{$ibforums->lang['js_no_message']}";
    }
    
    if (MessageMax != 0 && MessageLength > MessageMax) {
        errors = "{$ibforums->lang['js_max_length']} " + MessageMax + ". Current: " + MessageLength;
    }

    if (errors != "" && Override == "") {
        alert(errors);
        return false;
    } else {
        document.REPLIER.submit.disabled = true;
        return true;
    }
}
window.emoticon = function(char) {
    var ed = tinymce.get('qr-editor');
    if (ed) {
        ed.focus();
        ed.insertContent(char);
        ed.save(); 
    }
};
//-->
</script>

<form name='REPLIER' action="{$ibforums->base_url}" method='post' onsubmit='return ValidateForm()' enctype='multipart/form-data'>
<input type='hidden' name='act' value='Post' />
<input type='hidden' name='CODE' value='03' />
<input type='hidden' name='f' value='$fid' />
<input type='hidden' name='t' value='$tid' />
<input type='hidden' name='st' value='{$ibforums->input['st']}' />
<input type='hidden' name='auth_key' value='$key' />

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
    <td class='maintitle' colspan="2">&nbsp;&nbsp;{$ibforums->lang['qr_title']}</td>
</tr>
<!--IBF.NAME_FIELD-->
<tr>
    <td colspan="2" class='pformstrip'>{$ibforums->lang['post']}</td>
</tr>
<tr>
    <td class='pformleft' align='center' valign='middle'>
        <!--SMILIE TABLE-->
        <br />
        <div class='desc'>
            <strong>&middot; <a href='javascript:CheckLength()'>{$ibforums->lang['check_length']}</a> &middot;</strong>
        </div>
    </td>
    <td class="pformright" valign='top'>
        <textarea cols="80" rows="15" name="Post" id='qr-editor' tabindex="3" class="textinput"></textarea>
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
<tr>
  <td class='pformstrip' align='center' style='text-align:center' colspan="2">
    <input type="submit" name="submit" value="{$ibforums->lang['submit_reply']}" tabindex='4' class='forminput' accesskey='s' />&nbsp;
    <input type="submit" name="preview" value="{$ibforums->lang['button_preview']}" tabindex='5' class='forminput' />
  </td>
</tr>
</table>
</form>

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
            <b><a href="javascript:void(0)" onclick="tinymce.execCommand('mceEmoticons');" style="color: #004a99;">
                {$ibforums->lang['all_emoticons']}
            </a></b>
        </td>
    </tr>
</table>
EOF;
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

function nameField_reg() {
global $ibforums;
return <<<EOF
<!-- REG NAME -->
EOF;
}

function rate($data) {
global $ibforums;
return <<<EOF
<form action='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}' method='post' style='display:inline;'>
<input type='hidden' name='act' value='ST'>
<input type='hidden' name='f' value='{$data['forum']}'>
<input type='hidden' name='t' value='{$data['topic']}'>
<input type='hidden' name='s' value='{$ibforums->session_id}'>
<input type='hidden' name='CODE' value='00'>

    <div style='float:right;padding:.3em;'>
       {$data['rating']} {$data['choices']}
    </div>

</form>
EOF;
}

}
?>