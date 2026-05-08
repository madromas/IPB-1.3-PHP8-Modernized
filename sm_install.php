<?php
/**
 * Finalized Slim Installer for Legacy IPB 1.3
 * Modernized for PHP 8 & MariaDB
 */

error_reporting(E_ERROR | E_PARSE);
define('IN_INSTALL', 1);

class InstallerUI {
    function header($title) {
        echo "<html><head><title>$title</title>
              <style>
                body { font-family: 'Verdana', sans-serif; background: #f0f2f5; color: #333; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
                .card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); width: 100%; max-width: 550px; }
                h2 { margin-top: 0; color: #1a1a1a; border-bottom: 2px solid #eee; padding-bottom: 10px; }
                label { font-weight: bold; font-size: 14px; display: block; margin-top: 10px; }
                input[type='text'], input[type='password'], input[type='email'] { width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
                input[type='submit'] { background: #007bff; color: #fff; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; transition: 0.2s; margin-top: 10px; }
                input[type='submit']:hover { background: #0056b3; }
                .notice { background: #fff3cd; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border: 1px solid #ffeeba; }
              </style></head><body><div class='card'><h2>$title</h2>";
    }
    function footer() { echo "</div></body></html>"; }
}

$ui = new InstallerUI();
$step = $_GET['step'] ?? 'requirements';

// --- STEP 1: REQUIREMENTS ---
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
        echo "<div class='notice'>Ensure you have imported your SQL dump via phpMyAdmin before proceeding.</div>";
        echo "<a href='sm_install.php?step=form'><input type='submit' value='Continue'></a>";
    } else {
        echo "<p style='color:red;'>Please resolve system errors to continue.</p>";
    }
    $ui->footer();
}

// --- STEP 2: SETUP FORM ---
if ($step == 'form') {
    $ui->header("Configuration");
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . str_replace('/sm_install.php', '', $_SERVER['PHP_SELF']);

    echo "<form action='sm_install.php?step=process' method='post'>
            <label>SQL Host</label><input type='text' name='h' value='localhost'>
            <label>SQL Database</label><input type='text' name='d' placeholder='e.g. board_db'>
            <label>SQL Username</label><input type='text' name='u' placeholder='e.g. root'>
            <label>SQL Password</label><input type='password' name='p'>
            <label>Table Prefix</label><input type='text' name='pre' value='ibf_'>
            <label>Board URL (No trailing slash)</label><input type='text' name='url' value='$current_url'>
            <hr>
            <label>Admin User</label><input type='text' name='an' value='Admin'>
            <label>Admin Password</label><input type='password' name='ap'>
            <label>Admin Email</label><input type='email' name='ae'>
            <input type='submit' value='Install Board'>
          </form>";
    $ui->footer();
}

// --- STEP 3: PROCESSING ---
if ($step == 'process') {
    $ui->header("Finalizing...");
    
    $link = @mysqli_connect($_POST['h'], $_POST['u'], $_POST['p'], $_POST['d']);
    if (!$link) {
        die("Connection Failed: " . mysqli_connect_error() . "<br><a href='sm_install.php?step=form'>Back</a>");
    }

    // --- NEW: SQL AUTO-IMPORT ---
    if (file_exists('database.sql')) {
        $sql_query = file_get_contents('database.sql');
        
        /* Replace default prefix if necessary */
        if ($_POST['pre'] != 'ibf_') {
            $sql_query = str_replace('ibf_', $_POST['pre'], $sql_query);
        }

        if (mysqli_multi_query($link, $sql_query)) {
            do {
                /* flush multi_queries */
                if ($result = mysqli_store_result($link)) { mysqli_free_result($result); }
            } while (mysqli_next_result($link));
            echo "<p>✅ Database tables imported successfully.</p>";
        } else {
            echo "<p style='color:orange;'>⚠️ SQL Import warning: " . mysqli_error($link) . "</p>";
        }
    } else {
        echo "<p style='color:red;'>⚠️ database.sql not found. Skipping import...</p>";
    }

    // Admin Creation
    $prefix = $_POST['pre'];
    $pass = md5($_POST['ap']);
    $time = time();
    $admin_sql = "INSERT INTO {$prefix}members (name, password, email, mgroup, joined) 
                  VALUES ('" . mysqli_real_escape_string($link, $_POST['an']) . "', '$pass', '" . mysqli_real_escape_string($link, $_POST['ae']) . "', 4, '$time')";
    
    if (@mysqli_query($link, $admin_sql)) {
        echo "<p>✅ Admin account created.</p>";
    }

    // Auto-detect Paths
    $base_path = realpath(dirname(__FILE__)) . '/';
    
    // Build Complete Config Array
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
    
    $defaults = [
        'EMOTICONS_URL' => 'html/emoticons', 'admin_group' => '4', 'allow_images' => '1',
        'avatar_ext' => 'gif|jpeg|jpg|swf|png|webp', 'avatars_on' => '1', 'avup_size_max' => '20',
        'bot_antispam' => 'gif', 'clock_long' => 'j.m.Y - H:i', 'gd_font' => $base_path . 'fonts/progbot.ttf',
        'installed' => '1', 'php_ext' => 'php', 'use_ttf' => '1', 'warn_on' => '1'
    ];

    $config_output = "<?php\n";
    foreach (array_merge($defaults, $INFO) as $key => $val) {
        $config_output .= "\$INFO['$key'] = '" . addslashes($val) . "';\n";
    }
    $config_output .= "?>";

    if (file_put_contents('conf_global.php', $config_output)) {
        echo "<p>✅ Config generated with <b>mysqli</b> driver.</p>";
        echo "<p style='color:green;'><strong>Installation Complete! Delete sm_install.php and database.sql now.</strong></p>";
        echo "<a href='index.php'><input type='submit' value='Go to Board'></a>";
    } else {
        echo "<p style='color:red;'>Error: Could not write conf_global.php.</p>";
    }
    $ui->footer();
}
?>
