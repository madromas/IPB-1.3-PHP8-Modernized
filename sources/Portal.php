<?php
/*
                         ''~``
                        ( o o )
+------------------.oooO--(_)--Oooo.------------------+
|                     iBF Portal v4.0                 |
|                    .oooO                            |
|                    (   )   Oooo.                    |
+---------------------\ (----(   )--------------------+
                       \_)    ) /
                             (_/


           ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
           |The Logo Looks Best in Courier New|
           ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

                          ---====---
                iBF Portal v4.0 for IBF v1.2
                         By bammerboy

*/

$idx = new Portal;

class Portal {

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";

    var $data       = array();
    var $read_array = array();
    var $to_echo = "";


    function __construct() {
            global $ibforums, $DB, $std, $print;


            //--------------------------------------------
            // Require the HTML and language modules
            //--------------------------------------------
            
                $ibforums->lang = $std->load_words($ibforums->lang, 'lang_portal', $ibforums->lang_id );
            
            require "./Skin/".$ibforums->skin_id."/skin_portal.php";
            
            $this->html = new skin_portal();
            
            $this->base_url        = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?s={$ibforums->session_id}";
  
                  // 'read' topics
                   if ( $read = $std->my_getcookie('topicsread') )
        {
                  $this->read_array = @unserialize(stripslashes($read ?? ''));

if ($this->read_array === false && $read !== serialize(false)) {
    $this->read_array = array();
}
        }

        // gather information
        $this->data['navigation']=$this->do_navigation();
        $this->data['stats'] = $this->html->stats_start();
        $this->data['stats'] .= $this->do_active();
        $this->data['stats'] .= $this->do_birthdays();
        $this->data['stats'] .= $this->do_calendar_events();
        $this->data['stats'] .= $this->do_member_moment(); 
        $this->data['stats'] .= $this->do_stats();
        $this->data['stats'] .= $this->html->stats_end();
        $this->data['news'] .= $this->do_news();
        $this->data['old_news'] .= $this->do_old_news();
        $this->data['latest_posts'] = $this->do_latest_posts();
        $this->data['latest_posts_big'] = $this->do_latest_posts_big();
//        $this->data['new_posts'] = $this->do_new_posts();
        $this->data['new_posts_big'] = $this->do_new_posts_big();
        $this->data['top_posters'] = $this->do_top_posters();
        $this->data['new_members'] = $this->do_new_members();
        $this->data['top_forums'] = $this->do_top_forums();
        $this->data['loginbox'] = $this->do_loginbox();
        $this->data['welcomepanel'] = $this->do_welcomepanel();
        
        // IF there is an poll selected, give it here:
        if ($ibforums->vars['portal_poll']!=0) $this->data['poll'] = $this->do_poll($ibforums->vars['portal_poll']);
        else $this->data['poll']="";

        // render the portal
        $this->output = $this->html->render_portal($this->data);

        // done
                 $this->page_title = $ibforums->vars['board_name'];
                 $this->nav        = array( $ibforums->lang['page_title'] );

            $print->add_output("$this->output");
        $print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, 'NAV' => $this->nav ) );
         }

    //*********************************************/
    //  Show Calendar Events (code from IBF)
    //*********************************************/
    function do_calendar_events() {
        global $DB, $ibforums, $std;

        if ( $ibforums->vars['portal_calendar_events'] ) {

            if ($ibforums->vars['calendar_limit'] < 2)
            {
                $ibforums->vars['calendar_limit'] = 2;
            }
            
            $our_unix         = time() + ($ibforums->member['time_offset'] * 3600 );
            $max_date         = $our_unix + ($ibforums->vars['calendar_limit'] * 86400);
    
            $DB->query("SELECT eventid, title, read_perms, priv_event, userid, unix_stamp FROM ibf_calendar_events WHERE unix_stamp > '$our_unix' and unix_stamp < '$max_date' ORDER BY unix_stamp ASC");
            
            $show_events = array();
            
            while ($event = $DB->fetch_row())
            {
                if ($event['priv_event'] == 1 and $ibforums->member['id'] != $event['userid'])
                {
                    continue;
                }
            
                //-----------------------------------------
                // Do we have permission to see the event?
                //-----------------------------------------
                
                if ( $event['read_perms'] != '*' )
                {
                    if ( ! preg_match( "/(^|,)".$ibforums->member['mgroup']."(,|$)/", $event['read_perms'] ) )
                    {
                        continue;
                    }
                }
                
                $c_time = $std->get_date($event['unix_stamp'] - 86000, 'JOINED');
                
                $show_events[] = "<a href='{$ibforums->base_url}&act=calendar&code=showevent&eventid={$event['eventid']}' title='$c_time'>".$event['title']."</a>";
            }
            
            if ( count($show_events) > 0 )
            {
                $event_string = implode( ", ", $show_events );
            }
            else
            {
                $event_string = $ibforums->lang['no_calendar_events'];
            }
            
            $ibforums->lang['calender_f_title'] = sprintf( $ibforums->lang['calender_f_title'], $ibforums->vars['calendar_limit'] );
    
            $stats_html .= $this->html->calendar_events( $event_string  );
            return $stats_html;
        }
    }
  
    //*********************************************/
    //   Welcome Panel 
    //*********************************************/
    function do_welcomepanel() {
       global $DB, $ibforums, $std;
       if ( $ibforums->vars['portal_welcomepanel'] ) 
       {
                    if ($ibforums->member['id']) {
                            $DB->query("SELECT * FROM ibf_members WHERE id='".$ibforums->member['id']."'"); 
                            $member = $DB->fetch_row();
                            $avatar_size = $member['avatar_size'];
                            list($w,$h) = explode("x","$avatar_size",2);
                            $DB->query("SELECT `starter_id` AS id, `starter_name` AS name, COUNT(*) AS `num` FROM `ibf_topics` GROUP BY id ORDER BY num DESC LIMIT 1");
                            $row = $DB->fetch_row();
                            $data['tt_id'] = $row['id'];
                            $data['tt_name'] = $row['name'];
                            $data['tt_num'] = $row['num'];
                            $DB->query("SELECT `id`,`name`,`posts` FROM `ibf_members` WHERE 1 ORDER BY `posts` DESC LIMIT 1");
                            $row = $DB->fetch_row();
                            $data['tp_id'] = $row['id'];
                            $data['tp_name'] = $row['name'];
                            $data['tp_num'] = $row['posts'];
                            $DB->query("SELECT COUNT(DISTINCT(t.tid)) as tcnt, COUNT(DISTINCT(p.pid)) as pcnt FROM ibf_posts p, ibf_topics t WHERE p.post_date < ".time()." AND p.post_date > ".$ibforums->member['last_visit']." AND p.topic_id=t.tid");
                            $row = $DB->fetch_row();
                            $data['topics_since'] = $row['tcnt'];
                            $data['posts_since'] = $row['pcnt'];
                            $DB->query("SELECT * FROM ibf_stats");
                            $data['stats'] = $DB->fetch_row();
                            $data['stats']['TOTAL_POSTS'] = $data['stats']['TOTAL_TOPICS'] + $data['stats']['TOTAL_REPLIES'];
                            $data['lastv'] = $std->get_date($ibforums->member['last_visit'], 'LONG');
                            $data['time'] = $std->get_date(time(), 'LONG');
                      $data['avatar'] = $std->get_avatar( $member['avatar'], 1, $member['avatar_size'] );
                            return $this->html->welcomepanel($data);
            } else {

                            $data['time'] = $std->get_date(time(), 'LONG');
                            $today = mktime(0, 0, 0, (int)date("n"), (int)date("j"), (int)date("Y"));
                            $DB->query("SELECT COUNT(DISTINCT(t.tid)) as tcnt, COUNT(DISTINCT(p.pid)) as pcnt FROM ibf_posts p, ibf_topics t WHERE p.post_date > ".$today." AND p.topic_id=t.tid");
                            $row = $DB->fetch_row();
                            $data['topics_since'] = $row['tcnt'];
                            $data['posts_since'] = $row['pcnt'];
                return $this->html->guestpanel($data);
                    }
        }
    }
    
    //*********************************************/
    // Top Forums
    //*********************************************/
   function do_top_forums() {
        global $DB, $ibforums, $std;

        if ($ibforums->vars['portal_top_forums'])
        {
            if ($ibforums->vars['portal_num_top_forums'])
               $number_of_forums = $ibforums->vars['portal_num_top_forums'];
            else
               $number_of_forums = 5;            

            // Get User Data
            $query = $DB->query( "SELECT f.* FROM ibf_forums AS f WHERE f.read_perms = '*' OR f.read_perms LIKE '".$ibforums->member['mgroup']."' OR f.read_perms LIKE '%,".$ibforums->member['mgroup']."' OR f.read_perms LIKE '".$ibforums->member['mgroup'].",%' OR f.read_perms LIKE '%,".$ibforums->member['mgroup'].",%' ORDER BY (topics+posts) DESC LIMIT 0,".$number_of_forums );

            $rating = 0;
            $data = ""; // Initialize string to avoid undefined variable notices
            
            while( $row = $DB->fetch_row($query) ) {
                $rating++;
                
                // Explicitly inject the rating key into the row data array
                $row['rating'] = $rating;
                
                $data .= $this->html->top_forums_row($row);
            }

            return $this->html->top_forums($data);
        }
        else
        {
            return '';
        }        
    }

    //*********************************************/
    // Latest Threads Since Last Visit (BIG version)
    //*********************************************/
    function do_new_posts_big() {
            global $DB, $ibforums, $std;

            if ($ibforums->vars['portal_newposts'] && $ibforums->member['id'])
            {

            if ($ibforums->vars['portal_num_newposts'])
               $number_of_posts = $ibforums->vars['portal_num_newposts'];
            else
               $number_of_posts = 10;            

                // Get User Data
                    if ( ($ibforums->vars['show_user_posted'] == 1) and ($ibforums->member['id']) )
                    {
                        $query = $DB->query("SELECT DISTINCT ibf_posts.author_id, ibf_topics.*, ibf_forums.name, ibf_forums.read_perms
                                             FROM ibf_topics, ibf_forums
                                             LEFT JOIN ibf_posts ON (ibf_topics.tid = ibf_posts.topic_id AND ibf_posts.author_id = '".$ibforums->member['id']."') 
                                 WHERE ibf_topics.forum_id = ibf_forums.id AND 
                                        ibf_topics.last_post > ".$ibforums->input['last_visit']." AND
                                        (ibf_forums.read_perms = '*'
                                        OR ibf_forums.read_perms LIKE '".$ibforums->member['mgroup']."' 
                                        OR ibf_forums.read_perms LIKE '%,".$ibforums->member['mgroup']."' 
                                        OR ibf_forums.read_perms LIKE '".$ibforums->member['mgroup'].",%' 
                                        OR ibf_forums.read_perms LIKE '%,".$ibforums->member['mgroup'].",%')
                                             ORDER BY last_post DESC" );
                    }
                    else
                    {
            $query = $DB->query( "SELECT ibf_topic.*, f.name
                                  FROM ibf_topics
                                  RIGHT JOIN ibf_forums ON i.forum_id = ibf_forums.id 
                                  WHERE ibf_topics.forum_id = ibf_forums.id AND 
                                        ibf_topics.last_post > ".$ibforums->input['last_visit']." AND
                                        (ibf_forums.read_perms = '*'
                                        OR ibf_forums.read_perms LIKE '".$ibforums->member['mgroup']."' 
                                        OR ibf_forums.read_perms LIKE '%,".$ibforums->member['mgroup']."' 
                                        OR ibf_forums.read_perms LIKE '".$ibforums->member['mgroup'].",%' 
                                        OR ibf_forums.read_perms LIKE '%,".$ibforums->member['mgroup'].",%')
                                  ORDER BY last_post DESC" );
            }

                while( $row = $DB->fetch_row($query) ) {
                
                if ( $forum = $std->my_getcookie('fread_'.$row['forum_id']) )
                {
                        $ibforums->input['last_visit'] = $forum > $ibforums->input['last_visit'] ? $forum : $ibforums->input['last_visit'];
                }
                    $data.= $this->render_entry($row);
                }

                return $this->html->new_posts_big($data);
            }
        else
        {
            return '';
        }        
        
    }

    //*********************************************/
    // Latest Threads (BIG version)
    //*********************************************/
    function do_latest_posts_big() {
            global $DB, $ibforums, $std;

            if ($ibforums->vars['portal_latestposts_big'])
            {

            if ($ibforums->vars['portal_num_latestposts_big'])
               $number_of_posts = $ibforums->vars['portal_num_latestposts_big'];
            else
               $number_of_posts = 10;            

                // Get User Data
                    if ( ($ibforums->vars['show_user_posted'] == 1) and ($ibforums->member['id']) )
                    {
                        $query = $DB->query("SELECT DISTINCT ibf_posts.author_id, ibf_topics.*, ibf_forums.name, ibf_forums.read_perms
                                             FROM ibf_topics, ibf_forums
                                             LEFT JOIN ibf_posts ON (ibf_topics.tid = ibf_posts.topic_id AND ibf_posts.author_id = '".$ibforums->member['id']."') 
                                 WHERE ibf_topics.forum_id = ibf_forums.id AND 
                                        (ibf_forums.read_perms = '*'
                                        OR ibf_forums.read_perms LIKE '".$ibforums->member['mgroup']."' 
                                        OR ibf_forums.read_perms LIKE '%,".$ibforums->member['mgroup']."' 
                                        OR ibf_forums.read_perms LIKE '".$ibforums->member['mgroup'].",%' 
                                        OR ibf_forums.read_perms LIKE '%,".$ibforums->member['mgroup'].",%')
                                             ORDER BY last_post DESC LIMIT 0,".$number_of_posts );
                    }
                    else
                    {
            $query = $DB->query( "SELECT ibf_topics.*, ibf_forums.name, ibf_forums.read_perms
                                  FROM ibf_topics, ibf_forums
                                 WHERE ibf_topics.forum_id = ibf_forums.id AND 
                                        (ibf_forums.read_perms = '*'
                                        OR ibf_forums.read_perms LIKE '".$ibforums->member['mgroup']."' 
                                        OR ibf_forums.read_perms LIKE '%,".$ibforums->member['mgroup']."' 
                                        OR ibf_forums.read_perms LIKE '".$ibforums->member['mgroup'].",%' 
                                        OR ibf_forums.read_perms LIKE '%,".$ibforums->member['mgroup'].",%')
                                  ORDER BY last_post DESC LIMIT 0,".$number_of_posts );
            }

                while( $row = $DB->fetch_row($query) ) {
                
                if ( $forum = $std->my_getcookie('fread_'.$row['forum_id']) )
                {
                        $ibforums->input['last_visit'] = $forum > $ibforums->input['last_visit'] ? $forum : $ibforums->input['last_visit'];
                }
                    $data.= $this->render_entry($row);
                }

                return $this->html->latest_posts_big($data);
            }
        else
        {
            return '';
        }        
        
    }

    //*********************************************/
    // New Members
    //*********************************************/
    function do_new_members() {
            global $DB, $ibforums, $std;

            if ($ibforums->vars['portal_new_members'])
            {

            if ($ibforums->vars['portal_num_newmembers'])
               $newmembers = $ibforums->vars['portal_num_newmembers'];
            else
               $newmembers = 5;            
        
                // Get User Data
            $query = $DB->query( "SELECT * FROM ibf_members WHERE id>0 ORDER BY `joined` DESC LIMIT 0,".$newmembers);

                while( $row = $DB->fetch_row($query) ) {
                        $row['joined'] = $std->get_date( $row['joined'], 'LONG' );
                        $list.=$this->html->new_members_row($row);
                }

                return $this->html->new_members($list);
            }
        else
        {
            return '';
        }        
        
    }

    //*********************************************/
    // Top 10: Posters
    //*********************************************/
    function do_top_posters() {
            global $DB, $ibforums, $std;

            if ($ibforums->vars['portal_top_posters'])
            {

            if ($ibforums->vars['portal_num_topposters'])
               $halloffame = $ibforums->vars['portal_num_topposters'];
            else
               $halloffame = 5;            
                
                // Get User Data
            $query = $DB->query( "SELECT * FROM ibf_members WHERE `posts` > 0 ORDER BY `posts` DESC LIMIT 0,".$halloffame);

            $rating = 0;
                while( $row = $DB->fetch_row($query) ) {
                    $rating++;
                        $list.=$this->html->top_posters_row($row+array("rating"=>$rating));
                }

                return $this->html->top_posters($list);
            }
        else
        {
            return '';
        }        
        
    }


    //*********************************************/
    // Upload Form
    //*********************************************/
    function do_upload_form()
    {
        global $DB, $ibforums, $std;
        
        if ($ibforums->vars['portal_loginbox']) return $this->html->upload_form(1,512000);
        else return '';
    }

    //*********************************************/
    // Login Box
    //*********************************************/
    function do_loginbox()
    {
        global $DB, $ibforums, $std;
        
        if ($ibforums->vars['portal_loginbox'] && !$ibforums->member['id']) return $this->html->loginbox();
        else return '';
    }


    //*********************************************/
    // Board Navigation
    //*********************************************/
    function do_navigation()
    {
        global $DB, $ibforums, $std;
        
        if ($ibforums->vars['portal_navigation']) return $this->html->navigation();
        else return '';
    }

    //*********************************************/
    // Add in post stats
        //*********************************************/
        // Written by iBF
        //*********************************************/
    function do_stats()
    {
            global $DB, $ibforums, $std;
        if ($ibforums->vars['portal_post_stats'])
        {        
                        $DB->query("SELECT * FROM ibf_stats");
                        $stats = $DB->fetch_row();
                        
                        // Update the most active count if needed
                        
                        if (($active['TOTAL'] ?? 0) > ($stats['MOST_COUNT'] ?? 0))
                        {
                                $DB->query("UPDATE ibf_stats SET MOST_DATE='".time()."', MOST_COUNT='".$active[TOTAL]."'");
                                $stats['MOST_COUNT'] = $active[TOTAL];
                                $stats['MOST_DATE']  = time();
                        }
                        
                        $most_time = $std->get_date( $stats['MOST_DATE'], 'LONG' );
                        
                        $ibforums->lang['most_online'] = preg_replace( "/<#NUM#>/" ,   $stats['MOST_COUNT']  , $ibforums->lang['most_online'] );
                        $ibforums->lang['most_online'] = preg_replace( "/<#DATE#>/",   $most_time            , $ibforums->lang['most_online'] );
                        
                        $total_posts = $stats['TOTAL_REPLIES']+$stats['TOTAL_TOPICS'];
                        
                        $ibforums->lang['total_word_string'] = preg_replace( "/<#posts#>/" , "$total_posts"          , $ibforums->lang['total_word_string'] );
                        $ibforums->lang['total_word_string'] = preg_replace( "/<#reg#>/"   , $stats['MEM_COUNT']     , $ibforums->lang['total_word_string'] );
                        $ibforums->lang['total_word_string'] = preg_replace( "/<#mem#>/"   , $stats['LAST_MEM_NAME'] , $ibforums->lang['total_word_string'] );
                        
                        $stats_html .= $this->html->stats_posts($ibforums->lang['total_word_string']);
        
            return $stats_html;
        }
        else
        {
            return '';
        }    
    }

    //*********************************************/
    // Add in show online users
        //*********************************************/
        // Written by iBF
        //*********************************************/
    function do_active()
    {
            global $DB, $ibforums, $std;
            
            if ($ibforums->vars['portal_activemembers'])
            {
                    $active = array( 'TOTAL'   => 0 ,
                                                 'NAMES'   => "",
                                                 'GUESTS'  => 0 ,
                                                 'MEMBERS' => 0 ,
                                                 'ANON'    => 0 ,
                                           );
                                           
                
                        // Get the users from the DB
                        
                        $time = time() - 900;
                        
                        $DB->query("SELECT s.member_id, s.member_name, s.login_type, g.suffix, g.prefix FROM ibf_sessions s, ibf_groups g WHERE running_time > '$time' AND g.g_id=s.member_group ORDER BY running_time DESC");
                        
                        // cache all printed members so we don't double print them
                        $cached = array();
                        while ($result = $DB->fetch_row() )
                        {
                                if ($result['member_id'] == 0)
                                {
                                        $active['GUESTS']++;
                                }
                                else
                                {
                                        if (empty( $cached[ $result['member_id'] ] ) )
                                        {
                                                $cached[ $result['member_id'] ] = 1;
                                                if ($result['login_type'] == 1)
                                                {
                                                        $active['ANON']++;
                                                }
                                                else
                                                {
                                                        $active['MEMBERS']++;
                                                        $active['NAMES'] .= "<a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?s={$ibforums->session_id}&act=Profile&MID={$result['member_id']}'>{$result['prefix']}{$result['member_name']}{$result['suffix']}</a>,&nbsp;";
                                                }
                                        }
                                        
                                }
                        }
                        
                        $active['TOTAL'] = $active['MEMBERS'] + $active['GUESTS'] + $active['ANON'];
                        
                        // Show a link?
                        
                        if ($ibforums->vars['allow_online_list'])
                        {
                                $active['LINK'] = '[ '."<a href='".$this->base_url."&act=Online&CODE=listall'>".$ibforums->lang['browser_user_list']."</a>".' ]';
                        }
                        
                        $stats_html .= $this->html->stats_active($active);
                        return $stats_html;
                }
                else
                {
                    return '';
                }
    }
 
    //*********************************************/
    // Add in birthdays
        //*********************************************/
        // Written by iBF
        //*********************************************/
    function do_birthdays()
    {
            global $DB, $ibforums, $std;
            
            if ($ibforums->vars['portal_birthdays'])
            {
                                $user_time = time() + ($ibforums->vars['TIME_ZONE'] * 3600) + ($ibforums->member['TIME_ADJUST'] * 3600 );
                                
                                $date = getdate($user_time);
                                
                                $day   = $date['mday'];
                                $month = $date['mon'];
                                $year  = $date['year'];
                                
                                $birthstring = "";
                                $count       = 0;
                                
                                $DB->query("SELECT id, name, bday_day as DAY, bday_month as MONTH, bday_year as YEAR from ibf_members WHERE bday_day='$day' and bday_month='$month'");
                                
                                while ($user = $DB->fetch_row())
                                {
                                        $birthstring .= "<span id='highlight'>&gt;</span><a href='{$this->base_url}&act=Profile&CODE=03&MID={$user['id']}'>{$user['name']}</a> ";
                                        if ($user['YEAR'])
                                        {
                                                $pyear = $year - $user['YEAR'];  // $year = 2002 and $user['YEAR'] = 1976
                                                $birthstring .= "(<b>$pyear</b>) ";
                                        }
                                        $count++;
                                }
                                
                                $lang = $ibforums->lang['no_birth_users'];
                                
                                if ($count > 0) {
                                        $lang = ($count > 1) ? $ibforums->lang['birth_users'] : $ibforums->lang['birth_user'];
                                }
                                else
                                {
                                        $count = "";
                                }
                                
                                $stats_html = $this->html->stats_birthdays( $birthstring, $count, $lang  );
            return $stats_html;
        }
        else
        {
            return '';
        }
    } 

//+-------------------------------------------------
// Display Member Of The Moment
//+-------------------------------------------------
function do_member_moment()
{
        ################
        # Most of this code taken from IBF: Profile.php
        ################
    global $DB, $ibforums, $std;
    
    if ( $ibforums->vars['portal_member_moment'] ) 
    {
        $ibforums->input['filter'] = $ibforums->input['arg1'];
    
            // Load the template...
            //$template = load_template("member_moment.html");
            //$to_echo = "";                
    
            // Get Number of Members
            $DB->query( "SELECT MEM_COUNT FROM ibf_stats" );
            $num_mems = $DB->fetch_row();
            $num_mems = $num_mems['MEM_COUNT'];
            // Get Random Member Number (record position, not id!)
            $rand = rand(0, $num_mems - 1);
    
            // Get Member Data
            $DB->query( "SELECT m.*, g.g_id, g.g_title as group_title FROM ibf_members m, ibf_groups g WHERE (m.mgroup=g.g_id AND m.id>0) LIMIT ".$rand.",1" );
            $member        = $DB->fetch_row();
               $member['password'] = "";
        
            // Get Total Posts
               $DB->query("SELECT posts as total_posts FROM ibf_members WHERE id='".$member['id']."'");
               $total_posts        = $DB->fetch_row();

            // Get Favourite Forum  (from IBF: Profile.php)
               $DB->query("SELECT id, read_perms FROM ibf_forums");
    
               $forum_ids = array('0');
    
               while ( $r = $DB->fetch_row() )
               {
                       if ($r['read_perms'] == '*')
                       {
                               $forum_ids[] = $r['id'];
                       }
                       else if ( preg_match( "/(^|,)".$member['mgroup']."(,|$)/", $r['read_perms']) )
                       {
                               $forum_ids[] = $r['id'];
                       }
               }
    
               $forum_id_str = implode( ",", $forum_ids );
    
               $DB->query("SELECT DISTINCT(p.forum_id), f.name, COUNT(p.author_id) as f_posts FROM ibf_posts p, ibf_forums f ".
                                   "WHERE p.forum_id IN ($forum_id_str) AND p.author_id='".$member['id']."' AND p.forum_id=f.id GROUP BY p.forum_id ORDER BY f_posts DESC");
               $favourite   = $DB->fetch_row();
        
            // Set Values
            $data['member_name'] = $member['name'];
            $data['member_id']   = $member['id'];
            $data['profile_url'] = $ibforums->base_url . "&act=Profile&CODE=03&MID=".$member_id;
            $data['total_posts'] = $total_posts['total_posts'] ? $total_posts['total_posts'] : 0;
            $data['join_date']   = $std->get_date( $member['joined'], 'LONG' );
            $data['avatar']                 = $std->get_avatar( $member['avatar'], 1, $member['avatar_size'] );
            $data['fav_forum']         = $total_posts['total_posts'] > 0 && $favourite['name'] != '' ? $favourite['name'] : '--';
              $data['fav_id']                 = $favourite['forum_id'];
               $data['fav_posts']         = $favourite['f_posts'] ? $favourite['f_posts'] : 0;
            $data['forum_url']         = $total_posts['total_posts'] > 0 && $favourite['name'] != '' ? "<a href=\"".$ibforums->base_url . "&act=SF&f=".$data['fav_id']        ."\">".$data['fav_forum']."</a>" : '--';
    
        return $this->html->member_moment($data);
    }
    else
    {
        return '';
    }    
}
    
    //+-------------------------------------------------
    // Display latest threads
    //+-------------------------------------------------
    function do_latest_posts() {
            global $DB, $ibforums, $std;

            if ($ibforums->vars['portal_latestposts'])
            {

            if ($ibforums->vars['portal_num_latestposts'])
               $number_of_posts = $ibforums->vars['portal_num_latestposts'];
            else
               $number_of_posts = 10;            

                // Get User Data
            $query = $DB->query( "SELECT i.last_poster_name, i.last_poster_id, i.title, i.tid, i.forum_id, i.last_post FROM ibf_topics AS i RIGHT JOIN ibf_forums AS f ON i.forum_id = f.id WHERE f.read_perms = '*' OR f.read_perms LIKE '".$ibforums->member['mgroup']."' OR f.read_perms LIKE '%,".$ibforums->member['mgroup']."' OR f.read_perms LIKE '".$ibforums->member['mgroup'].",%' OR f.read_perms LIKE '%,".$ibforums->member['mgroup'].",%' ORDER BY last_post DESC LIMIT 0,".$number_of_posts );

            //var_dump($query);
                while( $out = $DB->fetch_row($query) ) {
    $thread_urls .= "<div style='padding:4px; border-bottom:1px solid #eee;'>
                        <a href='{$ibforums->base_url}act=ST&f=".$out['forum_id']."&t=".$out['tid']."&view=getlastpost' style=''>".$out['title']."</a> 
                        <div class='desc'>{$ibforums->lang['by']} ".$out['last_poster_name']."</div>
                     </div>";
}

                $to_echo = $this->html->latest_posts($thread_urls);
                return $to_echo;
            }
        else
        {
            return '';
        }        
    }

    function do_old_news()
    {
            global $DB, $ibforums, $std;
        
            if ($ibforums->vars['portal_old_news'])
            {
            // nothing inserted?
            if ( $ibforums->vars['portal_newsforum'] == "" )
            {
                $forumid="t.forum_id=1";
            }

            // multiple forums
            elseif ( $ibforums->vars['portal_newsforum'] == 0 )
            {
                if ( $ibforums->vars['portal_newsforum_expert'] == "" )
                {
                    $forumid="t.forum_id=1";
                }
                else
                {        
                    $forums = explode(",", $ibforums->vars['portal_newsforum_expert']);
                    $forumid = "t.forum_id=". join (" OR t.forum_id=", $forums);
                
                    // 'special' mode
                    $special = 1;
                }
                }

            // normal
            else 
            {
                $forumid = "t.forum_id=". $ibforums->vars['portal_newsforum'];
            }
        
            $max = $ibforums->vars['portal_newsposts'] ? $ibforums->vars['portal_newsposts'] : 5;
            if ($ibforums->vars['portal_num_old_news'])
               $limit = $ibforums->vars['portal_num_old_news'];
            else
                $limit = 5;            
            
                // Get the topics, member info and other stuff
                $DB->query("SELECT m.name as member_name, m.id as member_id, m.title as member_title, m.avatar, m.avatar_size, m.posts, t.*, p.*, f.* FROM ibf_members m, ibf_posts p, ibf_topics t, ibf_forums f ".
            "WHERE (f.read_perms = '*' OR f.read_perms LIKE '".$ibforums->member['mgroup']."' OR f.read_perms LIKE '%,".$ibforums->member['mgroup']."' OR f.read_perms LIKE '".$ibforums->member['mgroup'].",%' OR f.read_perms LIKE '%,".$ibforums->member['mgroup'].",%') 
            AND (".$forumid.") AND ( p.topic_id=t.tid AND p.new_topic=1 AND m.id=t.starter_id AND f.id=t.forum_id )".
            "ORDER BY t.tid DESC LIMIT {$max}, {$limit}");

                require_once ROOT_PATH . "sources/lib/post_parser.php";
                $parser = new post_parser(1);

                while ( $row = $DB->fetch_row() )
                {
                $row['start_date'] = $std->get_date( $row['start_date'], 'JOINED' );

                $row['title'] = $parser->bad_words($row['title']);

                // PARSE CUSTOM TAGS FOR PORTAL OUTPUT
            // Handle [spoiler]
            $row['post'] = preg_replace_callback("#\[spoiler\](.+?)\[/spoiler\]#is", function($matches) use ($parser) {
                return $parser->regex_spoiler_tag($matches[1]);
            }, $row['post']);

            // Handle [mod]
            $row['post'] = preg_replace_callback("#\[mod\](.+?)\[/mod\]#is", function($matches) use ($parser) {
                return $parser->regex_mod_tag($matches[1]);
            }, $row['post']);

            // Handle [ex]
            $row['post'] = preg_replace_callback("#\[ex\](.+?)\[/ex\]#is", function($matches) use ($parser) {
                return $parser->regex_ex_tag($matches[1]);
            }, $row['post']);

                // we don't need an icon in here
                //$row['icon'] = $std->folder_icon($row);
            
                //-----------------
                // if multiple export forums add 'title >from General Chat<
                //---------
                if ($special)
                    $row['extra'] = $ibforums->lang['news_from']." ".$row['name'];
                else
                    $row['extra'] = "";

               $data.="[<a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?s={$ibforums->session_id}&act=ST&f={$row['forum_id']}&t={$row['tid']}'>{$row['title']}</a>]<br><a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?s={$ibforums->session_id}&act=Profile&CODE=03&MID={$row['member_id']}'>{$row['member_name']}</a> {$row['start_date']}<br><br>";
                }
        
                return $this->html->old_news($data);
            }
            else
            {
                return '';
            }    
    }
function do_news()
    {
        global $DB, $ibforums, $std, $INFO;
        
        $to_echo = ""; 

        require_once ROOT_PATH . "sources/lib/post_parser.php";
        $parser = new post_parser(1);
        
        if ( ($ibforums->vars['portal_newsforum'] ?? "") == "" )
        {
            $forumid = "t.forum_id=1";
        }
        
        elseif ( $ibforums->vars['portal_newsforum'] == 0 )
{
    $expert_setting = $ibforums->vars['portal_newsforum_expert'] ?? "";

    if ( $expert_setting == "" )
    {
        $forumid = "t.forum_id=1";
    }
    else
    {       
        $forums = explode(",", $expert_setting);
        $requested_forum = intval($ibforums->input['news']);

        if( $requested_forum && in_array($requested_forum, $forums) )
        {
            $forumid = "t.forum_id=" . $requested_forum;
        }
        else
        {
            // Use IN() for cleaner SQL and map to intval for security
            $clean_ids = implode(',', array_map('intval', $forums));
            $forumid = "t.forum_id IN ($clean_ids)";
            $special = 1;
        }
    }
}

        else 
        {
            $forumid = "t.forum_id=". $ibforums->vars['portal_newsforum'];
        }
    
        $max = ($ibforums->vars['portal_newsposts'] ?? 0) > 0 ? $ibforums->vars['portal_newsposts'] : 5;

        // 1. Get the current page offset from the URL parameter (st = start)
        $start = intval($ibforums->input['st']) ? intval($ibforums->input['st']) : 0;

        // 2. Count the total topics available across your news forum selector
        $DB->query("SELECT COUNT(t.tid) as total_news_topics 
                    FROM ibf_topics t 
                    INNER JOIN ibf_posts p ON t.tid = p.topic_id 
                    WHERE ($forumid) AND p.new_topic=1");
        $total_rows = $DB->fetch_row();
        $total_news = intval($total_rows['total_news_topics']);

        
        $page_links = $std->build_pagelinks( array(
            'TOTAL_POSS' => $total_news,
            'PER_PAGE'   => $max,
            'CUR_ST_VAL' => $start,
            'BASE_URL'   => $this->base_url . "&act=portal" . ($requested_forum ? "&news=" . $requested_forum : ""),
        ) );

        if (!empty($page_links)) {
            $page_links = str_replace( array('[', ']', '&#91;', '&#93;'), '', $page_links );
        }
        
        $DB->query("SELECT m.name as member_name, m.id as member_id, t.*, p.*, f.name as forum_name 
                    FROM ibf_topics t
                    INNER JOIN ibf_posts p ON t.tid = p.topic_id
                    LEFT JOIN ibf_members m ON m.id = t.starter_id
                    LEFT JOIN ibf_forums f ON f.id = t.forum_id
                    WHERE ($forumid) AND p.new_topic=1
                    ORDER BY t.tid DESC LIMIT $start, ".$max);

       $first = 1;
        while ( $row = $DB->fetch_row() )
        {
            // 1. Map 'posts' to 'replies' so the portal template can see it
            $row['replies'] = $row['posts'] ?? 0;
            $row['views']   = $row['views'] ?? 0;



            $row['start_date'] = $std->get_date( $row['start_date'], 'LONG' );
            
            if ( ! ($row['member_name'] ?? "") ) 
            {
                $row['member_name'] = ($row['starter_name'] ?? "") ? $row['starter_name'] : "Guest";
            }
            
            if (!$first) $to_echo .= "<br>";
            
            $row['icon'] = $std->folder_icon($row);
            $row['extra'] = (isset($special) && $special) ? ($ibforums->lang['news_from'] ?? "From") . " " . ($row['forum_name'] ?? "") : "";

            $row['post'] = $parser->bad_words($row['post']);
            $row['title'] = $parser->bad_words($row['title']);

            // PARSE CUSTOM TAGS FOR PORTAL OUTPUT
            // Handle [spoiler]
            $row['post'] = preg_replace_callback("#\[spoiler\](.+?)\[/spoiler\]#is", function($matches) use ($parser) {
                return $parser->regex_spoiler_tag($matches[1]);
            }, $row['post']);

            // Handle [mod]
            $row['post'] = preg_replace_callback("#\[mod\](.+?)\[/mod\]#is", function($matches) use ($parser) {
                return $parser->regex_mod_tag($matches[1]);
            }, $row['post']);

            // Handle [ex]
            $row['post'] = preg_replace_callback("#\[ex\](.+?)\[/ex\]#is", function($matches) use ($parser) {
                return $parser->regex_ex_tag($matches[1]);
            }, $row['post']);
            
            // Handle teaser/full post logic
            if ( ($ibforums->vars['portal_tease_news'] ?? 0) )
            {        
                $length = $ibforums->vars['portal_newstease'] ?? 250; // Use your actual setting name
                $row['post_body'] = substr(trim($row['post']), 0, $length);
                $row['post_body_extra'] = " ... [<a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?s={$ibforums->session_id}&act=ST&f=".$row['forum_id']."&t=".$row['tid']."'>{$ibforums->lang['news_more']}</a>]";
            }
            else
            {   
                $row['post_body'] = $row['post'];
                $row['post_body_extra'] = "";
            }

            $first = 0;
            
            // Modernized formatting for PHP 8
            $row['posted_by_formatted'] = "By <strong>{$row['member_name']}</strong> on {$row['start_date']}";
            
            // Pass the updated $row to the skin
            $to_echo .= $this->html->news($row);
        }

        if ($page_links != "") {
            $to_echo .= $this->html->portal_pagination($page_links);
        }
        
        return $to_echo;
    }

    /*********************************************************************/
    // Process and parse the poll
    /*********************************************************************/   
        
    function do_poll($pollid) {

            global $DB, $ibforums, $std;

            $check       = 0;
            $poll_footer = "";
        
        // Get the poll information...
        
        $query = $DB->query("SELECT * FROM ibf_polls WHERE tid='".$pollid."'");
        $poll_data = $DB->fetch_row($query);

        if ($DB->get_num_rows($query)) {
    
            $DB->query("SELECT * from ibf_topics WHERE tid='".$pollid."'");
            $topic = $DB->fetch_row();

            //----------------------------------
        
            $voter = array( 'id' => 0 );
        
            // Have we voted in this poll?
        
            $DB->query("SELECT member_id from ibf_voters WHERE member_id='".$ibforums->member['id']."' and tid='".$pollid."'");
            $voter = $DB->fetch_row();
        
            if ($voter['member_id'] != 0)
            {
                    $check = 1;
                    $data2['poll_footer'] = $ibforums->lang['p_voted'];
            }
        
            if ( ($poll_data['starter_id'] == $ibforums->member['id']) and ($ibforums->vars['allow_creator_vote'] != 1) )
            {
                    $check = 1;
                    $data2['poll_footer'] = $ibforums->lang['p_creator'];
            }
                
            if (! $ibforums->member['id'] ) {
                    $check = 1;
                    $data2['poll_footer'] = $ibforums->lang['p_guest'];
            }
        
            if ($check == 1)
            {
                    // Show the results
                    $poll_answers = unserialize(stripslashes($poll_data['choices']));
                    reset($poll_answers);
                    foreach ($poll_answers as $entry)
                    {
                            $data['id']     = $entry[0];
                            $data['choice'] = $entry[1];
                            $data['votes']  = $entry[2];
                        
                            if (!$data['choice'])
                            {
                                    continue;
                            }
                        
                            $data['percent'] = $data['votes'] == 0 ? 0 : $data['votes'] / $poll_data['votes'] * 100;
                            $data['percent'] = sprintf( '%.0f' , $data['percent'] );
                            $data['width']   = $data['percent'] > 0 ? (int) $data['percent'] * 0.7 : 0;
                          
                    $data2['choices'].=$this->html->poll_voted($data);
                    }
            }
            else
            {
                    $poll_answers = unserialize(stripslashes($poll_data['choices']));
                    
                    reset($poll_answers);
                    foreach ($poll_answers as $entry)
                    {
                            $data['id']     = $entry[0];
                            $data['choice'] = $entry[1];
                            $data['votes']  = $entry[2];
                        
                            if (!$data['choice'])
                            {
                                    continue;
                            }
                        
                    $data2['choices'].=$this->html->poll_vote($data);                         
                    }
                    $data2['poll_footer'] = "<input type='submit' name='submit'   value='Vote!' class='forminput'>&nbsp;".
                                   "<input type='submit' name='nullvote' value='View Results (null vote)' class='forminput'>";
            }
        
    
            return $this->html->poll($data2+$topic+array('tid'=>$pollid));
        }
        else    
        {
            return '';
            }
        }

    //+----------------------------------------------------------------
        //
        // Crunches the data into pwetty html
        //
        // Written by iBF
        //+----------------------------------------------------------------

        function render_entry($topic) {
                global $DB, $std, $ibforums;
                
                $topic['last_text']   = $ibforums->lang['last_post_by'];
                
                $topic['last_poster'] = ($topic['last_poster_id'] != 0)
                                                                ? "<b><a href='{$this->base_url}&act=Profile&CODE=03&MID={$topic['last_poster_id']}'>{$topic['last_poster_name']}</a></b>"
                                                                : "-".$topic['last_poster_name']."-";
                                                                
                $topic['starter']     = ($topic['starter_id']     != 0)
                                                                ? "<a href='{$this->base_url}&act=Profile&CODE=03&MID={$topic['starter_id']}'>{$topic['starter_name']}</a>"
                                                                : "-".$topic['starter_name']."-";
         
                if ($topic['poll_state'])
                {
                        $topic['prefix']     = $ibforums->vars['pre_polls'].' ';
                }
                
                if ( ($ibforums->member['id']) and ($topic['author_id']) )
                {
                        $show_dots = 1;
                }
        
                $topic['folder_img']     = $std->folder_icon($topic, $show_dots, $this->read_array[$topic['tid']]);
                
                $topic['topic_icon']     = $topic['icon_id']  ? '<img src="'.$ibforums->vars['img_url'] . '/icon' . $topic['icon_id'] . '.gif" border="0" alt="">'
                                                                                                          : '&nbsp;';
                
                $topic['start_date'] = $std->get_date( $topic['start_date'], 'LONG' );
        
                $pages = 1;
                
                if ($topic['posts'])
                {
                        if ( (($topic['posts'] + 1) % $ibforums->vars['display_max_posts']) == 0 )
                        {
                                $pages = ($topic['posts'] + 1) / $ibforums->vars['display_max_posts'];
                        }
                        else
                        {
                                $number = ( ($topic['posts'] + 1) / $ibforums->vars['display_max_posts'] );
                                $pages = ceil( $number);
                        }
                        
                }
                
                if ($pages > 1) {
                        $topic['PAGES'] = "<span id='small'>({$ibforums->lang['topic_sp_pages']} ";
                        for ($i = 0 ; $i < $pages ; ++$i ) {
                                $real_no = $i * $ibforums->vars['display_max_posts'];
                                $page_no = $i + 1;
                                if ($page_no == 4) {
                                        $topic['PAGES'] .= "<a href='{$this->base_url}&act=ST&f={$topic['forum_id']}&t={$topic['tid']}&st=" . ($pages - 1) * $ibforums->vars['display_max_posts'] . "'>...$pages </a>";
                                        break;
                                } else {
                                        $topic['PAGES'] .= "<a href='{$this->base_url}&act=ST&f={$topic['forum_id']}&t={$topic['tid']}&st=$real_no'>$page_no </a>";
                                }
                        }
                        $topic['PAGES'] .= ")</span>";
                }
                
                if ($topic['posts'] < 0) $topic['posts'] = 0;
                
                $last_time = $this->read_array[ $topic['tid'] ] > $ibforums->input['last_visit'] ? $this->read_array[ $topic['tid'] ] : $ibforums->input['last_visit'];
                
                if ($last_time  && ($topic['last_post'] > $last_time))
                {
                        $topic['go_last_page'] = "<a href='{$this->base_url}&act=ST&f={$topic['forum_id']}&t={$topic['tid']}&view=getlastpost'><{GO_LAST_OFF}></a>";
                        $topic['go_new_post']  = "<a href='{$this->base_url}&act=ST&f={$topic['forum_id']}&t={$topic['tid']}&view=getnewpost'><{NEW_POST}></a>";
                }
                else
                {
                        $topic['go_last_page'] = "<a href='{$this->base_url}&act=ST&f={$topic['forum_id']}&t={$topic['tid']}&view=getlastpost'><{GO_LAST_OFF}></a>";
            $topic['go_new_post']  = "";

                }
        
                $topic['last_post']  = $std->get_date( $topic['last_post'], 'SHORT' );
                
                //+----------------------------------------------------------------
                        
                if ($topic['state'] == 'link')
                {
            $t_array = explode("&", $topic['moved_to']);
                        $topic['tid']       = $t_array[0];
                        $topic['forum_id']  = $t_array[1];
                        $topic['title']     = $topic['title'];
                        $topic['views']     = '--';
                        $topic['posts']     = '--';
                        $topic['prefix']    = $ibforums->vars['pre_moved']." ";
                        $topic['go_new_post'] = "";
                }
                
                if ($topic['pinned'] == 1)
                {
                        $topic['prefix']     = $ibforums->vars['pre_pinned'];
                        $topic['topic_icon'] = '<{B_PIN}>';
                        
                }
                
                return $this->html->RenderRow( $topic );
        }
}
?>
