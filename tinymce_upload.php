<?php
//-------------------------------------------------
// TinyMCE to IPB 1.3 Bridge for hub.glonks.com
//-------------------------------------------------

// We define this so the script knows it's being called by the system
define('IN_TINYMCE_UPLOAD', 1);

// 1. Load the first 90% of index.php (Everything BEFORE the $choice logic)
// To do this properly without hacking index.php, we'll manually bootstrap:
define( 'ROOT_PATH', "./" );
define ( 'USE_MODULES', 1 );
define ( 'IN_IPB', 1 );

require ROOT_PATH."conf_global.php";
require ROOT_PATH."sources/functions.php";
require ROOT_PATH."conf_mime_types.php";

$std   = new FUNC;
$sess  = new session();

// DB Driver Setup
require ROOT_PATH."sources/Drivers/".$INFO['sql_driver'].".php";
$DB = new db_driver;
$DB->obj = [
    'sql_database' => $INFO['sql_database'],
    'sql_user'     => $INFO['sql_user'],
    'sql_pass'     => $INFO['sql_pass'],
    'sql_host'     => $INFO['sql_host'],
    'sql_tbl_prefix' => $INFO['sql_tbl_prefix']
];
$DB->connect();

// Setup the $ibforums object
class info_bridge { var $member = array(); var $vars = array(); }
$ibforums = new info_bridge();
$ibforums->vars = $INFO;
$ibforums->member = $sess->authorise();

// 2. SECURITY & PERMISSION CHECKS
if (!$ibforums->member['id']) {
    header("HTTP/1.1 403 Forbidden");
    die(json_encode(['error' => 'Login required']));
}

if ($ibforums->member['g_attach_max'] < 1) {
    die(json_encode(['error' => 'No upload permission']));
}

// 3. FILE HANDLING (Direct from your Post.php logic)
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
    case 'image/gif':  $ext = '.gif'; break;
    case 'image/jpeg': 
    case 'image/pjpeg': $ext = '.jpg'; break;
    case 'image/png':
    case 'image/x-png': $ext = '.png'; break;
}

$real_file_name = "post-0-" . time() . $ext;

// 4. MOVE THE FILE
if (@move_uploaded_file($_FILES['file']['tmp_name'], $ibforums->vars['upload_dir'] . "/" . $real_file_name)) {
    @chmod($ibforums->vars['upload_dir'] . "/" . $real_file_name, 0777);
    
    // Return the URL for TinyMCE
    $url = $ibforums->vars['upload_url'] . "/" . $real_file_name;
    echo json_encode(['location' => $url]);
} else {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => 'Server failed to move file']);
}
?>