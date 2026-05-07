<?php

error_reporting(E_ALL);

// Display errors on the screen
ini_set('display_errors', 1);

// Display errors that occur during PHP's startup sequence
ini_set('display_startup_errors', 1);

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.0 PDR 1
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Time: Fri, 26 Mar 2004 15:41:26 GMT
|   Release: 17dca4732e9e529a939b2c15c6a8adbc
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > LO-FI VERSION!
|   > Script written by Matt Mecham
|   > Date started: 11th March 2004
|   > Interesting fact: Wrote this while listening to the Stereophonic's
|   > 'Performance and Cocktails' CD. That was when they were good.
|   > Lo-fi feature took about 1.5 days to write. That's a lot of CD
|   > repeating...
+--------------------------------------------------------------------------
*/

//-----------------------------------------------
// USER CONFIGURABLE ELEMENTS
//-----------------------------------------------

// Root path    exempl: _www.sitename.ru/forum/lofiversion

define( 'ROOT_PATH', "../" );
define( 'LOFI_NAME', 'lofiversion');
//-----------------------------------------------
// NO USER EDITABLE SECTIONS BELOW
//-----------------------------------------------

define ( 'IN_IPB', 1 );
define ( 'IN_DEV', 0 );

//===========================================================================
// DEBUG CLASS
//===========================================================================

class Debug
{
    function startTimer()
    {
        global $starttime;
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }
    function endTimer()
    {
        global $starttime;
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round (($endtime - $starttime), 5);
        return $totaltime;
    }
}

//===========================================================================
// INFO CLASS
//===========================================================================

class info {

        var $member       = array();
        var $input        = array();
        var $session_id   = "";
        var $base_url     = "";
        var $vars         = "";
        var $lang_id      = "en";
        var $skin         = "";
        var $lang         = "";
        var $server_load  = 0;
        var $version    = "v1.3.1";
        var $lastclick    = "";
        var $location     = "";
        var $debug_html   = "";
        var $perm_id      = "";
        var $forum_read   = array();
        var $topic_cache  = "";
        var $session_type = "";
        var $skin_global  = "";
        var $loaded_templates = array();

        function __construct()
        {
                global $sess, $std, $DB, $INFO;

                $this->vars = &$INFO;

                $this->vars['AVATARS_URL']     = 'style_avatars';
                $this->vars['EMOTICONS_URL']   = 'style_emoticons/<#EMO_DIR#>';
                $this->vars['mime_img']        = 'style_images/<#IMG_DIR#>/folder_mime_types';

        }
}

//===========================================================================
// MAIN PROGRAM
//===========================================================================

//--------------------------------
// Import $INFO, now!
//--------------------------------

$INFO = array();

require ROOT_PATH."conf_global.php";

//--------------------------------
// The clocks a' tickin'
//--------------------------------

$Debug = new Debug;
$Debug->startTimer();

//--------------------------------
// Load the DB driver and such
//--------------------------------

$INFO['sql_driver'] = !$INFO['sql_driver'] ? 'mySQL' : $INFO['sql_driver'];

require ( ROOT_PATH."sources/Drivers/".$INFO['sql_driver'].".php" );

$DB = new db_driver;

$DB->obj['sql_database']     = $INFO['sql_database'];
$DB->obj['sql_user']         = $INFO['sql_user'];
$DB->obj['sql_pass']         = $INFO['sql_pass'];
$DB->obj['sql_host']         = $INFO['sql_host'];
$DB->obj['sql_tbl_prefix']   = $INFO['sql_tbl_prefix'];
$DB->obj['use_shutdown']     = 0;
$DB->obj['debug']            = 0;

//--------------------------------
// Get a DB connection
//--------------------------------

$DB->connect();

//--------------------------------
// Wrap it all up in a nice easy to
// transport super class
//--------------------------------

$ibforums = new info();

//--------------------------------
// Require our global functions
//--------------------------------

require ROOT_PATH."sources/functions.php";

$std    = new FUNC;
$print  = new display();
$sess   = new session();

//--------------------------------
//  Set up our vars
//--------------------------------

$ibforums->input = $std->parse_incoming();

//--------------------------------
//  The rest :D
//--------------------------------

$ibforums->member     = $sess->authorise();
$std->load_skin();

$ibforums->vars['display_max_topics'] = 150;
$ibforums->vars['display_max_posts']  = 50;

$ibforums->session_id = "";
$ibforums->base_url   = $ibforums->vars['board_url'].'/index.'.$ibforums->vars['php_ext'].'?';

//--------------------------------
//  Do we have permission to view
//  the board?
//--------------------------------

if ($ibforums->member['g_view_board'] != 1)
{
        $std->boink_it( '../index.php' );
}

//--------------------------------
//  Is the board offline?
//--------------------------------

if ($ibforums->vars['board_offline'] == 1)
{
        if ($ibforums->member['g_access_offline'] != 1)
        {
                $std->boink_it( '../index.php' );
        }
}

//--------------------------------
//  Is log in enforced?
//--------------------------------

if ( (! $ibforums->member['id']) and ($ibforums->vars['force_login'] == 1) )
{
        $std->boink_it( '../index.php' );

}

//===========================================================================
// DO STUFF!
//===========================================================================

//--------------------------------
// Not index.php/ ? Redirect
// We do this so we can use relative
// links...
//--------------------------------

$main_string = $_SERVER['PHP_SELF'];

if ( ! strstr( $main_string, '/index.php/' ) )
{
        $std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php/' );
}

if ( strstr( $main_string, "/" ) )
{
        $main_string = str_replace( "/", "", strrchr( $main_string, "/" ) );
}

$main_string = str_replace( ".html", "", $main_string );

$action = 'index';
$id    = 0;
$st    = 0;

//--------------------------------
// Pages?
//--------------------------------

if ( strstr( $main_string, "-" ) )
{
        list( $main, $start ) = explode( "-", $main_string );

        $main_string = $main;
        $st          = $start;
}

$st = intval($st);

//--------------------------------
// What we doing?
//--------------------------------

if ( strstr( $main_string, 't' ) )
{
        $action = 'topic';
        $id    = intval( str_replace( "t", "", $main_string ) );
}
else if ( strstr( $main_string, 'f' ) )
{
        $action = 'forum';
        $id    = intval( str_replace( "f", "", $main_string ) );
}

//--------------------------------
// Require 'skin'
//--------------------------------

require_once( './lofi_skin.php' );

//--------------------------------
// Do it!
//--------------------------------

$output = "";

switch ( $action )
{
        case 'forum':
                $ibforums->real_link = $ibforums->base_url.'showforum='.$id;
                $output = get_forum_page($id, $st);
                break;
        case 'topic':
                $ibforums->real_link = $ibforums->base_url.'showtopic='.$id;
                $output = get_topic_page($id, $st);
                break;
        default:
                $ibforums->real_link = $ibforums->base_url;
                $output = get_index_page();
                break;
}

print_it($output);


//--------------------------------
// Board index
//--------------------------------

function get_index_page()
{
        global $ibforums, $std, $DB, $forums, $LOFISKIN;

        return LOFISKIN_forums( _get_forums() );
}

//--------------------------------
// Forums index
//--------------------------------

function get_forum_page($id, $st)
{
        global $ibforums, $std, $DB, $LOFISKIN, $navarray;

        $output = "";

    $forum = _get_forums_info($id);

        if ( $std->check_perms($forum['read_perms']) != TRUE || $forum['redirect_on'] || $forum['password'] != "" )
        {
                $std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php/' );
        }

        //--------------------------------
        // Nav array...
        //--------------------------------

        $navarray = _get_nav_array($forum);

        $ibforums->title = $forum['name'];

        //--------------------------------
        // Show topics...
        //--------------------------------

        $ibforums->pages = _get_pages( $forum['topics'], $ibforums->vars['display_max_topics'], 'f'.$id );


        //--------------------------------
        // Topics...
        //--------------------------------

    $DB->query("SELECT * FROM ibf_topics
                WHERE approved=1 AND forum_id='".$id."'
                ORDER BY pinned DESC, last_post DESC
                LIMIT {$st}, {$ibforums->vars['display_max_topics']}");


        while( $r = $DB->fetch_row() )
        {
                        if ( $r['pinned'] )
                        {
                                $r['_prefix'] = 'Pinned: ';;
                        }
                        else
                        {
                                $r['_prefix'] = "";
                        }

                        if ($r['state'] == 'link')
                        {
                                $t_array = explode("&", $r['moved_to']);
                                $r['tid']       = $t_array[0];
                                $r['forum_id']  = $t_array[1];
                                $r['title']     = $r['title'];
                                $r['posts']     = '--';
                                $r['_prefix']   = 'Moved: ';
                        }

                        $output .= LOFISKIN_topics_entry($r);
        }

        //--------------------------------
        // Return..
        //--------------------------------

        return LOFISKIN_topics($output);

}

//--------------------------------
// Topics index
//--------------------------------

function get_topic_page($id, $st)
{
        global $ibforums, $std, $DB, $forums, $LOFISKIN, $navarray;

        $output = "";

        //--------------------------------
        // get topic
        //--------------------------------

    $DB->query("SELECT * FROM ibf_topics WHERE approved=1 AND tid='".$id."'");

    $topic = $DB->fetch_row();

        if ( ! $topic['tid'] )
        {
                $std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php/' );
        }

    $forum = _get_forums_info($topic['forum_id']);

    if ( $std->check_perms($forum['read_perms']) != TRUE || $forum['redirect_on'] || $forum['password'] != "" )
    {
        $std->boink_it( $ibforums->vars['board_url'].'/'.LOFI_NAME.'/index.php/' );
    }

        $ibforums->pages = _get_pages( $topic['posts'], $ibforums->vars['display_max_posts'], 't'.$id );

        $ibforums->title = $topic['title'];

        //--------------------------------
        // get posts...
        //--------------------------------

    $DB->query("SELECT * FROM ibf_posts
                WHERE topic_id={$id} AND queued <> 1
                ORDER BY pid
                LIMIT {$st}, {$ibforums->vars['display_max_posts']}");


        while( $r = $DB->fetch_row() )
        {

                $r['post_date'] = $std->get_date( $r['post_date'], 'LONG', 1 );

                // Fix relative TinyMCE image and link paths for the Lo-Fi version
    if ( isset($r['post']) )
    {
        $r['post'] = str_replace('src="uploads/', 'src="'.$ibforums->vars['board_url'].'/uploads/', $r['post']);
        $r['post'] = str_replace('href="uploads/', 'href="'.$ibforums->vars['board_url'].'/uploads/', $r['post']);
    }

                $output .= LOFISKIN_posts_entry($r);
        }

        //--------------------------------
        // Nav array...
        //--------------------------------

        $navarray   = _get_nav_array( $forum );
        $output.=LOFISKIN_fastreply_entry($topic,$std->return_md5_check(),$ibforums->base_url);

        return $output;

}

//--------------------------------
// Print it
//--------------------------------

function print_it($content, $title='')
{
        global $ibforums, $std, $DB, $forums, $LOFISKIN, $navarray;

        $fullurl   = $ibforums->vars['board_url'].'/'.LOFI_NAME.'/';

        $copyright = "Invision Power Board &copy; 2001-".date("Y")." <a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>";

        //--------------------------------
        // Nav
        //--------------------------------

        $nav = "<a href='./'>".$ibforums->vars['board_name']."</a>";

        if ( is_array($navarray) && count($navarray) )
        {
                $nav .= " &gt; " . implode( " &gt; ", $navarray );
        }

        $title = ($ibforums->title ?? null) ? $ibforums->vars['board_name'].' &gt; '.$ibforums->title : $ibforums->vars['board_name'];

        $pages = "";

        if ( $ibforums->pages ?? null )
        {
                $pages = LOFISKIN_pages( $ibforums->pages );
        }
        if($ibforums->member['id']>0)  $authform=LOFISKIN_logged($ibforums->member['name'],$ibforums->base_url);
        else $authform=LOFISKIN_auth_form();
        $authurl="<form action=\"".$ibforums->vars['board_url']."/index.php?act=Login&amp;CODE=01&amp;CookieDate=1\" method=post>";

        $output = str_replace( '<% TITLE %>'    , $title    , $LOFISKIN['wrapper'] );
        $output = str_replace( '<% CONTENT %>'  , $content  , $output );
        $output = str_replace( '<% FULL_URL %>' , $fullurl  , $output );
        $output = str_replace( '<% AUTHFORM %>' , $authform , $output );
        $output = str_replace( '<% AUTHURL %>' , $authurl , $output );
        $output = str_replace( '<% COPYRIGHT %>', $copyright, $output );
        $output = str_replace( '<% NAV %>'      , $nav      , $output );
        $output = str_replace( '<% LINK %>'     , $ibforums->real_link, $output );
        $title_to_use = ( !empty($ibforums->title) ) ? $ibforums->title : $ibforums->vars['board_name'];
$output = str_replace( '<% LARGE_TITLE %>', $title_to_use, $output );
        $output = str_replace( '<% PAGES %>'     , $pages, $output );

        $img_dir = (is_array($ibforums->skin) && isset($ibforums->skin['_imagedir'])) ? $ibforums->skin['_imagedir'] : 'style_images/1';
$output = str_replace( "<#IMG_DIR#>", $img_dir, $output );
        
        $emo_dir = (is_array($ibforums->skin) && isset($ibforums->skin['_emodir'])) ? $ibforums->skin['_emodir'] : 'style_emoticons/default';
$output = str_replace( "<#EMO_DIR#>", $emo_dir, $output );

        $output = str_replace( "style_emoticons/", $ibforums->vars['board_url']."/style_emoticons/", $output );

        $bad_chars = array(
        chr(194).chr(160), // UTF-8 non-breaking space
        "&nbsp;",           // HTML entity
        "\xa0"             // ISO-8859-1 non-breaking space
    );

    $output = str_replace($bad_chars, " ", $output);

    // Final check: if characters are still mangled, force-encode to UTF-8
    if (!mb_check_encoding($output, 'UTF-8')) {
        $output = mb_convert_encoding($output, 'UTF-8', 'ISO-8859-1');
    }

        print $output;
}


//--------------------------------
// Recursively get forums
//--------------------------------

function _get_forums()
{
        global $ibforums, $forums, $LOFISKIN, $DB, $children;

         $html_string = ""; 
            $temp_html = "";

            $last_c_id = -1;

            $DB->query("SELECT f.*, c.id as cat_id, c.position as cat_position, c.state as cat_state, c.name as cat_name, c.description as cat_desc
                        FROM ibf_forums f, ibf_categories c
                        WHERE c.id=f.category
                        ORDER BY c.position, f.position");


            while ( $r = $DB->fetch_row() )
            {
                if ($last_c_id != $r['cat_id'])
                {
                    $cats[ $r['cat_id'] ] = array(   'id'          => $r['cat_id'],
                                                     'position'    => $r['cat_position'],
                                                     'state'       => $r['cat_state'],
                                                     'name'        => $r['cat_name'],
                                                     'description' => $r['cat_desc'],
                                                   );

                $last_c_id = $r['cat_id'];
                }

                if ($r['parent_id'] > 0)
                {
                    $children[ $r['parent_id'] ][$r['id']] = $r;
                }
                else
                {
                    $forums[ $r['id'] ] = $r;
                }

            }

       foreach ($cats as $cat_id => $cat_data)
        {

            //----------------------------
            // Is this category turned on?
            //----------------------------

            if ( $cat_data['state'] != 1 )
            {
                continue;
            }

            foreach ($forums as $forum_id => $forum_data)
            {
                $depth_guide = "";
                if ($forum_data['category'] == $cat_id)
                {
                    $temp_html .= process_forum($forum_id, $forum_data);
                }
            }

            if ($temp_html != "")
            {
                $html_string .= LOFISKIN_forums_entry_first($cat_data);
                $html_string .= $temp_html;
                $html_string .= LOFISKIN_forums_entry_end($depth_guide);
            }

            unset($temp_html);
        }

        return $html_string;
}

    function process_forum($forum_id="", $forum_data="")
    {
        global $std, $ibforums, $children;

        $sub_html = "";
    $tmp_html = "";

            if ( $std->check_perms($forum_data['read_perms']) != TRUE )
            {
                return "";
            }

            //--------------------------------------
            // Redirect only forum?
            //--------------------------------------

            if ( $forum_data['redirect_on'] || $forum_data['password'] != "")
            {
                return "";
            }


            if ($forum_data['subwrap'] == 1)
            {
                if ( (isset($children[ $forum_data['id'] ])) and (count($children[ $forum_data['id'] ]) > 0 ) )
                {

                    $printed_children = 0;

                    foreach($children[ $forum_data['id'] ] as $idx => $data)
                    {
                        //--------------------------------------
                        // Check permissions...
                        //--------------------------------------

                        if ( $std->check_perms($data['read_perms']) != TRUE )
                        {
                            continue;
                        }

                        if ( $data['redirect_on'] || $data['password'] != "")
                        {
                            continue;
                        }

                        $tmp_html .= LOFISKIN_forums_entry($data );

                        $printed_children++;
                    }



                }

                if ($printed_children > 0)
                {
                    $sub_html  = LOFISKIN_forums_entry_start();
                    $sub_html .= $tmp_html;
                    $sub_html .= LOFISKIN_forums_entry_end();
                }
            }

            $html = LOFISKIN_forums_entry($forum_data ).$sub_html;

            return $html;

    }


function _get_forums_info($id)
{
    global  $DB;

    $DB->query("SELECT * FROM ibf_forums WHERE id='".$id."' LIMIT 0,1");

    $forum = $DB->fetch_row();

    if ($forum['parent_id'] > 0)
    {
        $DB->query("SELECT name FROM ibf_forums WHERE id='".$forum['parent_id'] ."' LIMIT 0,1");

        $r = $DB->fetch_row();

        $forum['parent'] = $r['name'];
    }

    return $forum;
}

function _get_nav_array($data)
{
        global $ibforums, $forums, $LOFISKIN;

        $navarray[] = "<a href='f{$data['id']}.html'>{$data['name']}</a>";

        if ( isset($data['parent']) && $data['parent'] )
        {

        $navarray[] = "<a href='f{$data['parent_id']}.html'>{$data['parent']}</a>";

        }

        return array_reverse($navarray);
}


function _get_pages( $total, $pp, $id )
{
        global $ibforums, $forums, $LOFISKIN, $navarray;

        $page_array = array();

        //-----------------------------------------------
        // Get the number of pages
        //-----------------------------------------------

        $pages = ceil( $total / $pp );

        $pages = $pages ? $pages : 1;

        if ( $pages < 2 )
        {
                return "";
        }

        //-----------------------------------------------
        // Loppy loo
        //-----------------------------------------------

        if ($pages > 1)
        {
                for( $i = 0; $i <= $pages - 1; ++$i )
                {
                        $RealNo = $i * $pp;
                        $PageNo = $i+1;

                        $page_array[] = "<a href='{$id}-{$RealNo}.html'>{$PageNo}</a>";
                }

        }

        return implode( ", ", $page_array );
}

//+-------------------------------------------------
// GLOBAL ROUTINES
//+-------------------------------------------------

function fatal_error($message="", $help="")
{
        echo("$message<br><br>$help");
        exit;
}


?>
