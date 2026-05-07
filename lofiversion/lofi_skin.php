<?php

$LOFISKIN['wrapper'] = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="robots" content="index,follow">
        <link rel="stylesheet" rev="stylesheet" href="<% FULL_URL %>lofiscreen.css" media="screen" />
        <link rel="stylesheet" rev="stylesheet" href="<% FULL_URL %>lofihandheld.css" media="handheld" />
        <link rel="stylesheet" rev="stylesheet" href="<% FULL_URL %>lofiprint.css" media="print" />
        <title><% TITLE %></title>
</head>
<body>
<div id='ipbwrapper'>
  <div class='ipbnavsmall'>
   <a href='{$ibforums->base_url}act=Help'>Help</a> - 
   <a href='{$ibforums->base_url}act=Search'>Search</a> - 
   <a href='{$ibforums->base_url}act=Members'>Members</a> - 
   <a href='{$ibforums->base_url}act=calendar'>Calendar</a>

  </div>
  <div id='largetext'>Full Version: <a href='<% LINK %>'><% LARGE_TITLE %></a></div>
  <% AUTHURL %><div class='ipbnav'>
  <table width=98%>
  <tr>
<td align=left><% NAV %></td>
<td align=right><% AUTHFORM %></td>
  </tr>
  </table>
  </div>
  </form>
  <% PAGES %>
  <div id='ipbcontent'>
  <% CONTENT %>
  </div>
  <div class='smalltext'>This is an archive version. <a href='<% LINK %>'>Here</a> is the full version of this page.</div>
</div>
<div id='ipbcopyright'><% COPYRIGHT %></div>

EOF;


function LOFISKIN_forums($forums="") {
return <<<EOF
<div class='forumwrap'>
<ul>
$forums
</ul>
</div>
EOF;
}
function ban() {

        }

function LOFISKIN_forums_entry( $forum_data ) {
return <<<EOF
\n<li><a href='f{$forum_data['id']}.html'>{$forum_data['name']}</a> <span class='desc'>({$forum_data['topics']} topics)</span></li>
EOF;
}

function LOFISKIN_forums_entry_end() {
return <<<EOF
\n</ul>
EOF;
}

function LOFISKIN_forums_entry_start() {
return <<<EOF
\n<ul>
EOF;
}

function LOFISKIN_forums_entry_first($forum_data) {
return <<<EOF
\n<li><strong>{$forum_data['name']}</strong></li>\n<ul>
EOF;
}


function LOFISKIN_topics($topics="") {
return <<<EOF
<div class='topicwrap'>
<ol>
$topics
</ul>
</div>
EOF;
}

function LOFISKIN_topics_entry($r) {
return <<<EOF
\n<li>{$r['_prefix']}<a href='t{$r['tid']}.html'>{$r['title']}</a> <span class='desc'>({$r['posts']} replies)</span></li>
EOF;
}

function LOFISKIN_posts_entry($r) {
return <<<EOF

<div class='postwrapper'>
 <div class='posttopbar'>
  <div class='postname'>{$r['author_name']}</div>
  <div class='postdate'>{$r['post_date']}</div>
 </div>
 <div class='postcontent'>
  {$r['post']}
 </div>
</div>
EOF;
}

function LOFISKIN_pages($pages="") {
return <<<EOF
<div class='ipbpagespan'>
Pages: $pages
</div>
EOF;
}

function LOFISKIN_fastreply_entry($topic,$key,$url)
{
 return <<<EOF
 <div class='replywrapper'>
 <div class='replytopbar'>
 <div class='postname'>Fast Reply:</div>
  <div class='postdate'>Powered by dgreen</div>
 </div>
 <div class='postcontent' align=center>
 <form name='REPLIER' action="{$url}" method='post' >
           <input type='hidden' name='act' value='Post' />
           <input type='hidden' name='CODE' value='03' />
           <input type='hidden' name='f' value='{$topic['forum_id']}' />
           <input type='hidden' name='t' value='{$topic['tid']}' />
           <input type='hidden' name='enabletrack' value='0' />
           <input type='hidden' name='st' value='' />
           <input type='hidden' name='auth_key' value='$key' />
           <input type='hidden' name='lofi' value='1' />
                 <textarea cols='70' rows='8' name='Post' class='textarea' tabindex="1"></textarea>
           <br /><input type='checkbox' name='enableemo' value='yes' class="checkbox" checked="checked" />&nbsp;Enable Smilies &#124;
           <input type='checkbox' name='enablesig' value='yes' class="checkbox" checked="checked" />&nbsp;Enable Signature
           <br /><input type='submit' name='submit' value='Add Reply' class='button' tabindex="2" accesskey="s"/>
</div>
</div>
EOF;
}
function LOFISKIN_logged($name,$url)
{
return <<<EOF
Logged in as: {$name} (<a href="{$url}act=Login&CODE=03&lofi=1" class="logout">Log Out</a/>)
EOF;

}

function LOFISKIN_auth_form()
{
return <<<EOF
        <input type=hidden name="lofi" value=1/>
        <input type="text" size="20" name="UserName" onfocus="this.value=''" value="User Name" class='input'/>
        <input type='password' size='20' name='PassWord' onfocus="this.value=''" value='ibfrules' class='input' />
        <input type=submit value="GO" class='submit'/>
EOF;
}

?>
