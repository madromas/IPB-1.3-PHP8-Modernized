<?php
/**
 * Master Edition Slim Installer for Legacy IPB 1.3
 * Modernized for PHP 8 & MariaDB with full configuration mapping.
 */

error_reporting(E_ERROR | E_PARSE);
define('IN_INSTALL', 1);

if (file_exists('install.lock')) {
    die("<html><head><style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#f0f2f5;}.card{background:#fff;padding:40px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.1);}</style></head><body><div class='card'><h2>Installer Locked</h2><p>The <b>install.lock</b> file was found in your root directory. Installation is disabled for security.</p><p>Delete the file manually if you need to re-install.</p></div></body></html>");
}

class InstallerUI {
    function header($title) {
        echo "<html><head><title>$title</title>
              <style>
                body { font-family: 'Verdana', sans-serif; background: #f0f2f5; color: #333; display: flex; justify-content: center; align-items: flex-start; margin: 0; padding: 10px 0px; box-sizing: border-box; }
                .card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); width: 100%; max-width: 550px; margin: 0 auto; }
                h2 { margin-top: 0; color: #1a1a1a; border-bottom: 2px solid #eee; padding-bottom: 10px; }
                h3 { margin-top: 30px; color: #007bff; border-bottom: 1px solid #eef; padding-bottom: 5px; }
                label { font-weight: bold; font-size: 14px; display: block; margin-top: 15px; }
                input[type='text'], input[type='password'], input[type='email'] { width: 100%; padding: 10px; margin: 8px 0 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
                input[type='submit'] { background: #007bff; color: #fff; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; transition: 0.2s; margin-top: 15px; }
                input[type='submit']:hover { background: #0056b3; }
                .notice { background: #fff3cd; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border: 1px solid #ffeeba; }
              </style></head><body><div class='card'><h2>$title</h2>";
    }
    function footer() { echo "</div></body></html>"; }
}

$ui = new InstallerUI();
$step = $_GET['step'] ?? 'requirements';


if ($step == 'requirements') {
    $ui->header("System Check");
    $checks = [
        'PHP 8.0+' => phpversion() >= 8.0,
        'MySQLi Extension' => function_exists('mysqli_connect'),
        'Folder Writable' => is_writable('.')
    ];

    foreach ($checks as $label => $pass) {
        echo "<p><strong>$label:</strong> " . ($pass ? "✅" : "❌") . "</p>";
    }

    if (!in_array(false, $checks)) {
        echo "<div class='notice'><strong>Ready!</strong> The installer will automatically import your database schema using the <b>database.sql</b> file included in your package.</div>";
        echo "<a href='sm_install.php?step=form'><input type='submit' value='Continue'></a>";
    } else {
        echo "<p style='color:red;'>Please resolve system errors to continue.</p>";
    }
    $ui->footer();
}


if ($step == 'form') {
    $ui->header("Configuration");
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . str_replace('/sm_install.php', '', $_SERVER['PHP_SELF']);
    $domain_name = $_SERVER['HTTP_HOST'];

    echo "<form action='sm_install.php?step=process' method='post'>
            <h3>Database Settings</h3>
            <label>SQL Host</label><input type='text' name='h' value='localhost'>
            <label>SQL Database</label><input type='text' name='d' placeholder='e.g. board_db'>
            <label>SQL Username</label><input type='text' name='u' placeholder='e.g. root'>
            <label>SQL Password</label><input type='password' name='p'>
            <label>Table Prefix</label><input type='text' name='pre' value='ibf_'>
            
            <h3>Board & Website Identity</h3>
            <label>Board Name</label><input type='text' name='board_name' value='My Board'>
            <label>Board URL (No trailing slash)</label><input type='text' name='url' value='$current_url'>
            <label>Website Name</label><input type='text' name='home_name' value='Site Home'>
            <label>Website Address</label><input type='text' name='home_url' value='$protocol$domain_name'>
            
            <h3>Administrator Account</h3>
            <label>Admin User</label><input type='text' name='an' value='Admin'>
            <label>Admin Password</label><input type='password' name='ap'>
            <label>Admin Email</label><input type='email' name='ae'>
            <input type='submit' value='Install Board'>
          </form>";
    $ui->footer();
}


if ($step == 'process') {
    $ui->header("Finalizing...");
    
    try {
        mysqli_report(MYSQLI_REPORT_OFF);
        $link = mysqli_connect($_POST['h'], $_POST['u'], $_POST['p'], $_POST['d']);
    } catch (Exception $e) {
        $link = false;
    }

    if (!$link) {
        echo "<p style='color:red; font-weight:bold;'>⚠️ Connection Failed:</p>";
        echo "<p style='background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; border:1px solid #f5c6cb; font-size:14px;'>" . mysqli_connect_error() . "</p>";
        echo "<p>Please go back and verify your SQL hostname, username, and password.</p>";
        echo "<a href='sm_install.php?step=form'><input type='submit' value='← Go Back' style='background:#6c757d;'></a>";
        $ui->footer();
        exit();
    }

    if (file_exists('database.sql')) {
        $sql_query = file_get_contents('database.sql');
        
        if ($_POST['pre'] != 'ibf_') {
            $sql_query = str_replace('ibf_', $_POST['pre'], $sql_query);
        }

        if (mysqli_multi_query($link, $sql_query)) {
            do {
                if ($result = mysqli_store_result($link)) { mysqli_free_result($result); }
            } while (mysqli_next_result($link));
            echo "<p>✅ Database tables imported successfully.</p>";
        } else {
            echo "<p style='color:orange;'>⚠️ SQL Import warning: " . mysqli_error($link) . "</p>";
        }
    } else {
        echo "<p style='color:red;'>⚠️ database.sql not found. Skipping import...</p>";
    }
    mysqli_close($link);

    $link2 = mysqli_connect($_POST['h'], $_POST['u'], $_POST['p'], $_POST['d']);
    if ($link2) {
        $prefix = $_POST['pre'];
        $pass = md5($_POST['ap']);
        $time = time();
        
        $admin_name  = mysqli_real_escape_string($link2, $_POST['an']);
        $admin_email = mysqli_real_escape_string($link2, $_POST['ae']);
        $ip_address  = $_SERVER['REMOTE_ADDR'];
        
        // Full field map to match your schema
        $admin_sql = "INSERT INTO {$prefix}members (
            id, name, mgroup, password, email, joined, ip_address, 
            posts, view_sigs, view_img, view_avs, view_pop, view_qr, 
            last_visit, last_activity, allow_rep, allow_anon, favorites
        ) VALUES (
            1, '$admin_name', 4, '$pass', '$admin_email', $time, '$ip_address', 
            0, 1, 1, 1, 1, 1, 
            $time, $time, 1, 1, ''
        )";
        
        if (mysqli_query($link2, $admin_sql)) {
            echo "<p>✅ Admin account generated with full schema mapping.</p>";
            
            // NOW update the stats using the same open connection
            $stats_sql = "UPDATE {$prefix}stats SET 
                          TOTAL_REPLIES = 0, 
                          TOTAL_TOPICS = 0, 
                          LAST_MEM_NAME = '" . mysqli_real_escape_string($link2, $admin_name) . "', 
                          LAST_MEM_ID = 1, 
                          MOST_DATE = $time, 
                          MOST_COUNT = 1, 
                          MEM_COUNT = 1";

            if (mysqli_query($link2, $stats_sql)) {
                echo "<p>✅ Global statistics initialized to zero.</p>";
            } else {
                echo "<p style='color:red;'>⚠️ Failed to sync stats: " . mysqli_error($link2) . "</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to create Admin account: " . mysqli_error($link2) . "</p>";
        }
        
        // Close the connection only after everything is finished
        mysqli_close($link2);
    }

    $base_path = realpath(dirname(__FILE__)) . '/';
    
    $INFO = [];
    $INFO['sql_driver']      = 'mysqli'; 
    $INFO['sql_host']        = $_POST['h'];
    $INFO['sql_database']    = $_POST['d'];
    $INFO['sql_user']        = $_POST['u'];
    $INFO['sql_pass']        = $_POST['p'];
    $INFO['sql_tbl_prefix']  = $prefix;
    $INFO['board_url']       = $_POST['url'];
    $INFO['base_dir']        = $base_path;
    $INFO['upload_dir']      = $base_path . 'uploads';
    $INFO['upload_url']      = $_POST['url'] . '/uploads';
    $INFO['html_dir']        = $base_path . 'html/';
    $INFO['html_url']        = $_POST['url'] . '/html';
    $INFO['email_in']        = $_POST['ae'];
    $INFO['email_out']       = $_POST['ae'];
    $INFO['board_start']     = $time;
    $INFO['board_name']      = $_POST['board_name'];
    $INFO['boardname']       = $_POST['board_name']; 
    $INFO['home_name']       = $_POST['home_name'];
    $INFO['home_url']        = $_POST['home_url'];
    $INFO['gd_font']         = $base_path . 'fonts/progbot.ttf';


    $defaults = [
        'EMOTICONS_URL' => 'html/emoticons',
        'admin_group' => '4',
        'allow_creator_vote' => '1',
        'allow_dup_email' => '0',
        'allow_dynamic_img' => '0',
        'allow_flash' => '0',
        'allow_images' => '1',
        'allow_online_list' => '1',
        'allow_result_view' => '0',
        'allow_search' => '1',
        'allow_skins' => '0',
        'au_cutoff' => '15',
        'auth_group' => '1',
        'av_gal_cols' => '5',
        'avatar_def' => '80x80',
        'avatar_dims' => '80x80',
        'avatar_ext' => 'gif|jpeg|jpg|swf|png|webp',
        'avatar_url' => '1',
        'avatars_on' => '1',
        'avup_size_max' => '20',
        'ban_email' => '',
        'ban_ip' => '',
        'ban_names' => '',
        'board_desc' => 'Best board in the world',
        'bot_antispam' => 'gif',
        'clock_joined' => 'j.m.Y',
        'clock_long' => 'j.m.Y - H:i',
        'clock_short' => 'j.m.Y - H:i',
        'cookie_domain' => '',
        'cookie_id' => '',
        'cookie_path' => '',
        'csite_article_chars' => '',
        'csite_article_date' => 'm-j-y H:i',
        'csite_article_forum' => '',
        'csite_article_len' => '30',
        'csite_article_max' => '15',
        'csite_article_recent_max' => '5',
        'csite_article_recent_on' => '1',
        'csite_configured' => '1',
        'csite_discuss_len' => '30',
        'csite_discuss_max' => '10',
        'csite_discuss_on' => '1',
        'csite_fav_show' => '0',
        'csite_nav_show' => '0',
        'csite_on' => '0',
        'csite_online_show' => '1',
        'csite_pm_show' => '1',
        'csite_poll_show' => '1',
        'csite_poll_url' => '',
        'csite_search_show' => '1',
        'csite_skinchange_show' => '1',
        'csite_stats_show' => '',
        'csite_title' => '',
        'custom_profile_topic' => '0',
        'debug_level' => '1',
        'disable_admin_anon' => '0',
        'disable_gzip' => '1',
        'disable_ipbsize' => '0',
        'disable_online_ip' => '0',
        'disable_reportpost' => '0',
        'display_max_posts' => '15',
        'display_max_topics' => '15',
        'email_footer' => '',
        'email_header' => 'This email generated via IBForums',
        'emo_per_row' => '3',
        'etfilter_punct' => '0',
        'etfilter_shout' => '0',
        'flood_control' => '30',
        'force_login' => '0',
        'forum_skin_1' => '',
        'forum_skin_8' => '',
        'gd_height' => '70',
        'gd_width' => '250',
        'guest_group' => '2',
        'guest_name_pre' => 'Guest_',
        'guest_name_suf' => '',
        'guests_ava' => '1',
        'guests_img' => '1',
        'guests_sig' => '1',
        'header_redirect' => 'location',
        'board_offline' => '0',
        'hot_topic' => '15',
        'html_detection_regex' => '<(p|div|span|ul|ol|table|br|iframe|video|source|blockquote|pre|code)',
        'html_purifier_allowed' => '<(div[class|style],b,strong,i[class|style],em,a[href|title|target],ul,ol,li,p[style|class],br,span[style|class],img[width|height|alt|src],iframe[src|width|height|frameborder|allowfullscreen|allow|sandbox],video[src|controls|width|height|poster|preload],source[src|type],blockquote,pre[class],code[class]',
        'img_ext' => 'gif|jpeg|jpg|png|webp',
        'index_news_link' => '0',
        'installed' => '1',
        'load_limit' => '',
        'match_browser' => '0',
        'max_emos' => '20',
        'max_h_flash' => '200',
        'max_images' => '10',
        'max_interest_length' => '500',
        'max_location_length' => '500',
        'max_messages' => '50',
        'max_poll_choices' => '10',
        'max_post_length' => '1000',
        'max_sig_length' => '500',
        'max_w_flash' => '200',
        'member_group' => '3',
        'min_search_word' => '',
        'msg_allow_code' => '1',
        'msg_allow_html' => '0',
        'new_reg_notify' => '0',
        'news_forum_id' => '1',
        'no_au_forum' => '0',
        'no_au_topic' => '0',
        'no_reg' => '0',
        'nocache' => '1',
        'number_format' => 'none',
        'offline_msg' => '',
        'photo_ext' => 'gif|jpg|jpeg|pngwebp',
        'php_ext' => 'php',
        'php_to_html' => '1',
        'poll_disable_noreply' => '0',
        'poll_tags' => '0',
        'portal_activemembers' => '1',
        'portal_birthdays' => '0',
        'portal_calendar_events' => '1',
        'portal_googlebar' => '0',
        'portal_latestposts' => '1',
        'portal_latestposts_big' => '0',
        'portal_loginbox' => '0',
        'portal_member_moment' => '0',
        'portal_navigation' => '0',
        'portal_new_members' => '1',
        'portal_newposts' => '0',
        'portal_newsforum' => '0',
        'portal_newsforum_expert' => '1,3',
        'portal_newsposts' => '10',
        'portal_num_latestposts' => '10',
        'portal_num_latestposts_big' => '10',
        'portal_num_newmembers' => '3',
        'portal_num_newposts' => '',
        'portal_num_old_news' => '5',
        'portal_num_top_forums' => '3',
        'portal_num_topposters' => '5',
        'portal_old_news' => '0',
        'portal_poll' => '0',
        'portal_post_stats' => '1',
        'portal_tease_length' => '',
        'portal_tease_news' => '0',
        'portal_top_forums' => '1',
        'portal_top_posters' => '1',
        'portal_welcomepanel' => '0',
        'post_order_column' => 'pid',
        'post_order_sort' => 'asc',
        'post_titlechange' => '5000',
        'post_wordwrap' => '',
        'postpage_contents' => '5,10,15,20,25,30,35,40',
        'pre_moved' => '<b><font color=blue>Moved:</font></b> ',
        'pre_pinned' => '<b><font color=red>Pinned:</font></b> ',
        'pre_polls' => '<b><font color=green>Polls:</font></b> ',
        'print_headers' => '0',
        'reg_auth_type' => '0',
        'rep_allow_anon' => '0',
        'rep_allow_comments' => '0',
        'rep_anon_posts' => '',
        'rep_bad_anon' => '',
        'rep_badnum' => '',
        'rep_badtitle' => '',
        'rep_change_exclude' => '',
        'rep_enable_emo' => '0',
        'rep_enable_ibc' => '0',
        'rep_good_anon' => '',
        'rep_goodnum' => '',
        'rep_goodtitle' => '',
        'rep_mems_limit' => '',
        'rep_memstats' => '0',
        'rep_msg_length' => '',
        'rep_per_page' => '',
        'rep_posts' => '',
        'rep_profile' => '0',
        'rep_remove' => '',
        'rep_remove_days' => '',
        'rep_time' => '',
        'rep_time_nomod' => '0',
        'rep_titlechange' => '',
        'rep_total_exclude' => '',
        'rep_use_ranks' => '0',
        'safe_mode_skins' => '0',
        'search_post_cut' => '',
        'session_expiration' => '3600',
        'short_forum_jump' => '0',
        'show_active' => '1',
        'show_birthdays' => '0',
        'show_img_upload' => '1',
        'show_totals' => '1',
        'show_user_posted' => '0',
        'sig_allow_html' => '1',
        'sig_allow_ibc' => '1',
        'siu_height' => '',
        'siu_thumb' => '1',
        'siu_width' => '600',
        'sql_debug' => '0',
        'sql_port' => '',
        'startpoll_cutoff' => '24',
        'strip_quotes' => '0',
        'strip_space_chr' => '0',
        'subs_autoprune' => '',
        'topicpage_contents' => '5,10,15,20,25,30,35,40',
        'use_mail_form' => '1',
        'use_ttf' => '1',
        'validate_day_prune' => '3',
        'warn_gmod_ban' => '0',
        'warn_gmod_day' => '1',
        'warn_gmod_modq' => '0',
        'warn_gmod_post' => '0',
        'warn_max' => '10',
        'warn_min' => '0',
        'warn_mod_ban' => '0',
        'warn_mod_day' => '1',
        'warn_mod_modq' => '0',
        'warn_mod_post' => '0',
        'warn_on' => '1',
        'warn_past_max' => '0',
        'warn_protected' => ',,',
        'warn_show_own' => '0',
        'warn_show_rating' => '0'
    ];

    $config_output = "<?php\n";
    foreach (array_merge($defaults, $INFO) as $key => $val) {
        $config_output .= "\$INFO['$key'] = '" . addslashes($val) . "';\n";
    }
    $config_output .= "?>";

    if (file_put_contents('conf_global.php', $config_output)) {
        @touch("install.lock");
        echo "<p>✅ Config generated with <b>mysqli</b> driver.</p>";
        echo "<p style='color:green;'><strong>Installation Complete! Delete sm_install.php and database.sql now.</strong></p>";
        echo "<a href='index.php'><input type='submit' value='Go to Board'></a>";
    } else {
        echo "<p style='color:red;'>Error: Could not write conf_global.php.</p>";
    }
    $ui->footer();
}
?>
