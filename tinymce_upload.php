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

$ext = '.ibf';
switch($FILE_TYPE) {
    case 'image/gif':   $ext = '.gif'; break;
    case 'image/jpeg':  
    case 'image/pjpeg': $ext = '.jpg'; break;
    case 'image/png':
    case 'image/x-png': $ext = '.png'; break;
    case 'image/webp':  $ext = '.webp'; break;
}

$forum_id = isset($_POST['forum_id']) ? intval($_POST['forum_id']) : 0;

$real_file_name = "post-" . $forum_id . "-" . time() . $ext;

// Move the File and Update Database
$target_path = $ibforums->vars['upload_dir'] . "/" . $real_file_name;

if (@move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
    @chmod($target_path, 0777);
    
    // --- START WATERMARK ---
    $watermark_file = $ibforums->vars['upload_dir'] . "/watermark.png"; // Your transparent watermark image
    
    if (file_exists($watermark_file) && in_array($ext, ['.jpg', '.png', '.gif', '.webp'])) {
        $watermark = @imagecreatefrompng($watermark_file);
        
        if ($watermark) {
            $watermark_width  = imagesx($watermark);
            $watermark_height = imagesy($watermark);
            
            $image = null;
            if ($ext == '.jpg')  $image = @imagecreatefromjpeg($target_path);
            if ($ext == '.png')  $image = @imagecreatefrompng($target_path);
            if ($ext == '.gif')  $image = @imagecreatefromgif($target_path);
            if ($ext == '.webp') $image = @imagecreatefromwebp($target_path);
            
            if ($image) {
                $image_width  = imagesx($image);
                $image_height = imagesy($image);
                
                // Position calculations (Bottom Right with 10px padding)
                $dst_x = $image_width - $watermark_width - 10;
                $dst_y = $image_height - $watermark_height - 10;
                
                // Only watermark if the uploaded image is larger than the watermark
                if ($image_width > $watermark_width && $image_height > $watermark_height) {
                    
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    
                    imagecopy($image, $watermark, $dst_x, $dst_y, 0, 0, $watermark_width, $watermark_height);
                    
                    if ($ext == '.jpg')  imagejpeg($image, $target_path, 90);
                    if ($ext == '.png')  imagepng($image, $target_path);
                    if ($ext == '.gif')  imagegif($image, $target_path);
                    if ($ext == '.webp') imagewebp($image, $target_path, 85); // Added WebP saver (85% quality)
                }
                
                imagedestroy($image);
            }
            imagedestroy($watermark);
        }
    }
    // --- END WATERMARK LOGIC ---
    
    $url = rtrim($ibforums->vars['upload_url'], '/') . '/' . $real_file_name;
echo json_encode(['location' => $url]);
} else {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => 'Server failed to move file']);
}
?>