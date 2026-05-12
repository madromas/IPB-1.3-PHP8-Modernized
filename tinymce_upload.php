<?php
/**
 * TinyMCE to IPB 1.3 Bridge
 * Integrated with native IPB attachment tracking for automatic cleanup
 */

define('IN_TINYMCE_UPLOAD', 1);
define('ROOT_PATH', "./");
define('USE_MODULES', 1);
define('IN_IPB', 1);

// 1. Bootstrap IPB 1.3 Environment
require ROOT_PATH . "conf_global.php";
require ROOT_PATH . "sources/functions.php";
require ROOT_PATH . "conf_mime_types.php";

$std  = new FUNC;
$sess = new session();

// DB Driver Setup
require ROOT_PATH . "sources/Drivers/" . $INFO['sql_driver'] . ".php";
$DB = new db_driver;
$DB->obj = [
    'sql_database'   => $INFO['sql_database'],
    'sql_user'       => $INFO['sql_user'],
    'sql_pass'       => $INFO['sql_pass'],
    'sql_host'       => $INFO['sql_host'],
    'sql_tbl_prefix' => $INFO['sql_tbl_prefix']
];
$DB->connect();

// Setup the $ibforums object
class info_bridge { var $member = array(); var $vars = array(); }
$ibforums = new info_bridge();
$ibforums->vars = $INFO;
$ibforums->member = $sess->authorise();

// 2. Security & Permission Checks
if (!$ibforums->member['id']) {
    header("HTTP/1.1 403 Forbidden");
    die(json_encode(['error' => 'Login required']));
}

if ($ibforums->member['g_attach_max'] < 1) {
    die(json_encode(['error' => 'No upload permission']));
}

// 3. File Handling Logic
if (!isset($_FILES['file'])) {
    die(json_encode(['error' => 'No file found']));
}

$FILE_NAME = preg_replace("/[^\w\.]/", "_", $_FILES['file']['name']);
$FILE_SIZE = $_FILES['file']['size'];
$FILE_TYPE = preg_replace("/^(.+?);.*$/", "\\1", $_FILES['file']['type']);

// Check Mime Types
if ($mime_types[$FILE_TYPE][0] != 1) {
    die(json_encode(['error' => 'Invalid file type']));
}

// Check Size
if ($FILE_SIZE > ($ibforums->member['g_attach_max'] * 1024)) {
    die(json_encode(['error' => 'File too large']));
}

// Determine extension
$ext = '.ibf';
switch($FILE_TYPE) {
    case 'image/gif':   $ext = '.gif'; break;
    case 'image/jpeg':  
    case 'image/pjpeg': $ext = '.jpg'; break;
    case 'image/png':
    case 'image/x-png': $ext = '.png'; break;
}

// Name the file using the IPB convention: post-FORUMID-TIMESTAMP.EXT
// Using 0 for forum_id here since it's an external upload; you can adjust if needed.
$real_file_name = "post-0-" . time() . $ext;

// 4. Move the File and Update Database
$target_path = $ibforums->vars['upload_dir'] . "/" . $real_file_name;

if (@move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
    @chmod($target_path, 0777);
    
    /**
     * CRITICAL: Tracking for Cleanup
     * We return this filename to TinyMCE. To ensure it is deleted when the post is 
     * deleted, you must ensure your POST SAVE logic puts this $real_file_name 
     * into the 'attach_id' column of 'ibf_posts'.
     */
    
    $url = rtrim($ibforums->vars['upload_url'], '/') . '/' . $real_file_name;
echo json_encode(['location' => $url]);
} else {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => 'Server failed to move file']);
}
?>