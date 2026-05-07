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
  <div class='ipbnav'><% NAV %></div>
  <% PAGES %>
  <div id='ipbcontent'>
  <% CONTENT %>
  </div>
  <div class='smalltext'>This is an archive version. <a href='<% LINK %>'>Here</a> is the full version of this page.</div>
</div>
<div id='ipbcopyright'><% COPYRIGHT %></div>
</body>
</html>
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

?>
