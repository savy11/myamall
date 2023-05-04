<?php
 
 namespace resources\controllers;
 
 require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
 
 use DateTime;
 use DateTimeZone;
 use Exception;
 use resources\controllers\email as email;
 use resources\models\model as db;
 
 abstract class controller
 {
  
  public $path = '/';
  public $currency = 'NGN';
  public $version = '0.3.1';
  public $data = array(), $list = array();
  public $allowed_image_formats = array('jpg', 'jpeg', 'png', 'pdf', 'doc');
  public $allowed_video_formats = array('mp4');
  public $allowed_file_formats = array();
  public $allowed_max_size = 25;
  public $yes_no = array('N' => 'No', 'Y' => 'Yes');
  public $yes_no_label = array('N' => 'danger', 'Y' => 'success');
  public $mime_types = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'mp4' => 'video/mp4');
  public $days = array('1' => 'Sunday', '2' => 'Monday', '3' => 'Tuseday', '4' => 'Wednesday', '5' => 'Thursday', '6' => 'Friday', '7' => 'Saturday');
  public $months = array('1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
  public $status_label = array('P' => 'warning', 'I' => 'info', 'S' => 'warning', 'Y' => 'success', 'C' => 'danger', 'F' => 'danger');
  // , 'S' => 'Shipped'
  public $order_user_status = array('P' => 'Placed', 'I' => 'In Progress', 'Y' => 'Delivered', 'C' => 'Cancelled', 'R' => 'Returned');
  // , 'S' => 'Shipped'
  public $order_status = array('P' => 'Pending', 'I' => 'In Progress', 'Y' => 'Delivered', 'C' => 'Cancelled', 'R' => 'Returned');
  public $payment_status = array('P' => 'Pending', 'I' => 'Timeout', 'F' => 'Failed', 'D' => 'Declined', 'C' => 'Cancelled', 'Y' => 'Success', 'R' => 'Refunded');
  protected $db = null;
  protected $email = null;
  
  public function __construct($main_db = false, $need_db = true, $c = true)
  {
   if ($need_db) {
    $this->db = new db($this, $main_db);
   }
   if ($c) {
    if ($this->is_ajax_call()) {
     $this->validate_get_token();
     $this->get_post_data();
    }
    $this->allowed_file_formats = array_merge($this->allowed_image_formats, $this->allowed_video_formats);
   }
  }
  
  public function is_ajax_call()
  {
   if (strtolower($this->server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') {
    return true;
   }
   return false;
  }
  
  public function server($k = '', $arr = '')
  {
   return $this->type_val('SERVER', $k, $arr);
  }
  
  public function type_val($type, $k = '', $arr = '', $data = array(), $isset = false)
  {
   $val = $this->get_var_value($type, $k, $data, $isset);
   if ($type == 'COOKIE') {
    $val = $this->decrypt_post_data($val);
   }
   if ($arr != '') {
    if (is_array($arr) && count($arr) > 0) {
     $val = $this->arr_val($val, $arr, $isset);
    } else {
     $val = $this->get_var_value('CUSTOM', $arr, $val, $isset);
    }
   }
   return $val;
  }
  
  protected function get_var_value($type, $k = '', $data = array(), $isset = false)
  {
   $val = '';
   if ($type == 'POST') {
    $post = $_POST;
   } else if ($type == 'GET') {
    $post = $_GET;
   } else if ($type == 'REQUEST') {
    $post = $_REQUEST;
   } else if ($type == 'SESSION') {
    $post = $_SESSION;
   } else if ($type == 'SERVER') {
    $post = $_SERVER;
   } else if ($type == 'FILES') {
    $post = $_FILES;
   } else if ($type == 'COOKIE') {
    $post = $_COOKIE;
   } else if ($type == 'CUSTOM') {
    $post = $data;
   }
   if ($k != '') {
    if (isset($post[$k])) {
     $val = $post[$k];
     if ($val == '') {
      if ($isset == true) {
       $val = true;
      }
     }
    }
   } else {
    $val = $post;
   }
   return $val;
  }
  
  public function decrypt_post_data($data)
  {
   $data = str_replace(" ", "+", $data);
   return $this->json_decode($this->decrypt($data));
  }
  
  public function json_decode($arr, $assoc = true)
  {
   return json_decode($arr, $assoc);
  }
  
  public function decrypt($string)
  {
   $string = str_replace(' ', '+', $string);
   $string = $this->replace_sql($string);
   $result = '';
   $string = base64_decode($string);
   for ($i = 0; $i < strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr(encrypt_key, ($i % strlen(encrypt_key)) - 1, 1);
    $char = chr(ord($char) - ord($keychar));
    $result .= $char;
   }
   return $result;
  }
  
  public function replace_sql($str, $case = null)
  {
   $str = trim(stripslashes($str));
   $str = str_replace("\\", "", $str);
   $str = str_replace("'", "\'", $str);
   if ($case == 'U') {
    $str = strtoupper($str);
   } else if ($case == 'L') {
    $str = strtolower($str);
   }
   return $str;
  }
  
  public function arr_val($data, $arr, $isset = false)
  {
   $val = '';
   foreach ($arr as $v) {
    if (is_array($data)) {
     $val = $this->get_var_value('CUSTOM', $v, $data, $isset);
     $data = $val;
    }
   }
   return $val;
  }
  
  public function validate_get_token()
  {
   if (!$this->session('get_token') || $this->session('get_token') !== $this->get('token')) {
    throw new Exception('Oops, something wrong. May be your token not found or expired, Please try again.');
   }
  }
  
  public function session($k = '', $arr = '', $isset = false)
  {
   return $this->type_val('SESSION', $k, $arr, '', $isset);
  }
  
  public function get($k = '', $arr = '', $isset = false)
  {
   return $this->type_val('GET', $k, $arr, '', $isset);
  }
  
  public function get_post_data()
  {
   $data = $this->decrypt_post_data($this->post('data'));
   if ($data) {
    foreach ($data as $k => $v) {
     $_GET[$k] = $v;
     $_POST[$k] = $v;
     $_REQUEST[$k] = $v;
    }
   }
   //unset($_POST['data'], $_REQUEST['data']);
  }
  
  public function post($k = '', $arr = '', $isset = false)
  {
   return $this->type_val('POST', $k, $arr, '', $isset);
  }
  
  public function get_token()
  {
   if ($this->session('get_token') == '') {
    $_SESSION['get_token'] = md5(rand(1000000, 9999999));
   }
   return $this->session('get_token');
  }
  
  public function post_token()
  {
   if ($this->session('post_token') == '') {
    $_SESSION['post_token'] = md5(rand(1000000, 9999999));
   }
   return $this->session('post_token');
  }
  
  public function delete_token()
  {
   if ($this->session('delete_token') == '') {
    $_SESSION['delete_token'] = md5(rand(1000000, 9999999));
   }
   return $this->session('delete_token');
  }
  
  public function validate_post_token($doexp = false)
  {
   if ($doexp) {
    if (!$this->session('post_token') || $this->session('post_token') !== $this->post('token')) {
     throw new Exception('Oops, something wrong. May be your token not found or expired, Please try again.');
    }
   } else {
    if ($this->is_ajax_call()) {
     if (!$this->session('post_token') || $this->session('post_token') !== $this->post('token')) {
      die('Oops, something wrong. May be your token not found or expired, Please try again.');
     }
    } else {
     if (!$this->session('post_token') || $this->session('post_token') !== $this->post('token')) {
      $_SESSION['MSG'] = array('Token Validation', 'Oops, something wrong. May be your token not found or expired, Please try again.', 'danger');
      header('Location:' . $this->server('REQUEST_URI'));
      exit();
     }
    }
   }
  }
  
  public function validate_delete_token($doexp = false)
  {
   $token = trim($this->post('token') ? $this->post('token') : $this->get('token'));
   if ($doexp) {
    if (!$this->session('delete_token') || $this->session('delete_token') !== $token) {
     throw new Exception('Oops, something wrong. May be your token not found or expired, Please try again.');
    }
   } else {
    if ($this->is_ajax_call()) {
     if (!$this->session('delete_token') || $this->session('delete_token') !== $token) {
      die('Oops, something wrong. May be your token not found or expired, Please try again.');
     }
    } else {
     if (!$this->session('delete_token') || $this->session('delete_token') !== $token) {
      $_SESSION['MSG'] = array('Token Validation', 'Oops, something wrong. May be your token not found or expired, Please try again.', 'danger');
      header('Location:' . $this->server('REQUEST_URI'));
      exit();
     }
    }
   }
  }
  
  public function img_replace($str)
  {
   $str = preg_replace('/[^a-zA-Z0-9.-]+/i', "", $str);
   return $str;
  }
  
  public function show_string($str, $len = 50, $html = true)
  {
   if ($html) {
    $str = $this->make_html($str);
   } else {
    $str = strip_tags($str);
   }
   $l = strlen($str);
   return substr($str, 0, $len) . ($l > $len ? '..' : '');
  }
  
  public function make_html($str)
  {
   $str = $this->html_sql($str);
   $str = str_replace("\'", "'", $str);
   $str = str_replace("\r\n", "<br>", $str);
   return $str;
  }
  
  public function html_sql($str)
  {
   $str = htmlentities($str);
   return $str;
  }
  
  public function get_page_name($rep = '.php')
  {
   $a = explode('/', $this->server('SCRIPT_NAME'));
   return str_replace($rep, '', $a[count($a) - 1]);
  }
  
  public function request($k = '', $arr = '', $isset = false)
  {
   return $this->type_val('REQUEST', $k, $arr, '', $isset);
  }
  
  public function post_get($k = '', $arr = '', $isset = false)
  {
   if ($val = $this->type_val('POST', $k, $arr, '', $isset)) {
    return $val;
   }
   return $this->type_val('GET', $k, $arr, '', $isset);
  }
  
  public function get_os()
  {
   $user_agent = $this->server('HTTP_USER_AGENT');
   $os_platform = 'Unknown OS Platform';
   $os_array = array('/windows nt 6.4/i' => 'Windows 10', '/windows nt 10.0/i' => 'Windows 10', '/windows nt 6.3/i' => 'Windows 8.1', '/windows nt 6.2/i' => 'Windows 8', '/windows nt 6.1/i' => 'Windows 7', '/windows nt 6.0/i' => 'Windows Vista', '/windows nt 5.2/i' => 'Windows Server 2003/XP x64', '/windows nt 5.1/i' => 'Windows XP', '/windows xp/i' => 'Windows XP', '/windows nt 5.0/i' => 'Windows 2000', '/windows me/i' => 'Windows ME', '/win98/i' => 'Windows 98', '/win95/i' => 'Windows 95', '/win16/i' => 'Windows 3.11', '/android/i' => 'Android', '/iphone/i' => 'iPhone', '/ipod/i' => 'iPod', '/ipad/i' => 'iPad', '/macintosh|mac os x/i' => 'Mac OS X', '/mac_powerpc/i' => 'Mac OS 9', '/linux/i' => 'Linux', '/ubuntu/i' => 'Ubuntu', '/blackberry/i' => 'BlackBerry', '/webos/i' => 'Mobile', '/symbianos/i' => 'Symbian',);
   
   foreach ($os_array as $regex => $value) {
    if (preg_match($regex, $user_agent, $matches, PREG_OFFSET_CAPTURE)) {
     $os_platform = $value;
     if ('Android' == $os_platform) {
      $index = $matches[0][1];
      $agent = substr($user_agent, $index);
      $agent = trim(substr($agent, strpos($agent, ';') + 1, strpos($agent, ')')));
      $agent = trim(stristr($agent, 'build', 1));
      $os_platform .= " (" . $agent . ")";
     }
     break;
    }
   }
   return $os_platform;
  }
  
  public function get_browser()
  {
   $user_agent = $this->server('HTTP_USER_AGENT');
   $browser = "Unknown Browser";
   $browser_array = array('/opera|opr/i' => 'Opera', '/edge/i' => 'Edge', '/trident|msie|masp/i' => 'Internet Explorer', '/chrome/i' => 'Chrome', '/ucbrowser/i' => 'UCBrowser', '/safari/i' => 'Safari', '/firefox/i' => 'Firefox', '/netscape/i' => 'Netscape', '/maxthon/i' => 'Maxthon', '/konqueror/i' => 'Konqueror', '/mobile/i' => 'Mobile Browser');
   foreach ($browser_array as $regex => $value) {
    if (preg_match($regex, $user_agent)) {
     $browser = $value;
     break;
    }
   }
   return $browser;
  }
  
  public function gen_cookie($name, $data = array(), $time = null)
  {
   $this->clear_cookie($name);
   if ($time == null) {
    $time = (30 * 24 * 3600);
   }
   $validity = time() + ((int)$time);
   $cookie_name = cookie_key . '_' . $name;
   if (cookie_encrypt == true) {
    $cookie_name = md5($cookie_name);
   }
   setcookie($cookie_name, $this->encrypt_post_data($data), $validity, $this->path, domain, false, true);
  }
  
  public function clear_cookie($name)
  {
   $validity = time() - ((int)(365 * 24 * 3600));
   if (is_array($name) && count($name) > 0) {
    foreach ($name as $v) {
     if ($this->cookie($v)) {
      $cookie_name = cookie_key . '_' . $v;
      if (cookie_encrypt == true) {
       $cookie_name = md5($cookie_name);
      }
      setcookie($cookie_name, '', $validity, $this->path, domain, false, true);
     }
    }
   } else {
    if ($this->cookie($name)) {
     $cookie_name = cookie_key . '_' . $name;
     if (cookie_encrypt == true) {
      $cookie_name = md5($cookie_name);
     }
     setcookie($cookie_name, '', $validity, $this->path, domain, false, true);
    }
   }
   header('Cache-Control: max-age=0');
   header('Cache-Control: max-age=1');
   header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
   header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
   header('Cache-Control: cache, must-revalidate');
   header('Pragma: public');
  }
  
  public function cookie($k, $arr = '')
  {
   $cookie_name = cookie_key . '_' . $k;
   if (cookie_encrypt == true) {
    $cookie_name = md5($cookie_name);
   }
   return $this->type_val('COOKIE', $cookie_name, $arr);
  }
  
  public function encrypt_post_data($dt = array())
  {
   return $this->encrypt($this->json_encode($dt));
  }
  
  public function encrypt($string)
  {
   $string = $this->replace_sql($string);
   $result = '';
   for ($i = 0; $i < strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr(encrypt_key, ($i % strlen(encrypt_key)) - 1, 1);
    $char = chr(ord($char) + ord($keychar));
    $result .= $char;
   }
   return base64_encode($result);
  }
  
  public function json_encode($arr)
  {
   return json_encode($arr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
  }
  
  public function populate_post_data($data = false)
  {
   if (!$data) {
    $data = $this->data;
   }
   if ($data) {
    foreach ($data as $k => $v) {
     if (strpos($k, 'password') !== false) {
      $v = $this->decrypt($v);
     }
     $_POST[$k] = $this->post($k) != '' ? $this->post($k) : $v;
    }
   }
  }
  
  public function db_date_format($date)
  {
   return $this->date_format($date, 'Y-m-d');
  }
  
  public function date_format($date, $format = '')
  {
   if (!$format) {
    $format = date_format;
   }
   if ($date != '' && $date != '0000-00-00') {
    return date($format, strtotime($date));
   }
   return '';
  }
  
  public function db_dt_format($date)
  {
   return $this->dt_format($date, 'Y-m-d H:i:s');
  }
  
  public function dt_format($date, $format = '')
  {
   if (!$format) {
    $format = date_format . ' ' . time_format;
   }
   if ($date != '' && $date != '0000-00-00 00:00:00') {
    return date($format, strtotime($date));
   }
   return '';
  }
  
  public function get_file_data($path)
  {
   if (function_exists('realpath')) {
    $path = realpath($path);
   }
   if (!$path || !@is_file($path)) {
    return '';
   }
   return @file_get_contents($path);
  }
  
  public function gen_code($code_len, $char = false)
  {
   $chars = '0123456789';
   if ($char) {
    $chars = 'AB0CD1EF2GH3IJ4KL5MN6OP7QR8ST9UVWXYZ';
   }
   $code = '';
   $i = 0;
   while ($i < $code_len) {
    $code .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    $i++;
   }
   return $code;
  }
  
  public function send_email($key, $id, $attachments = [])
  {
   $files = '[]';
   if ($attachments) {
    $files = $this->json_encode($attachments);
   }
   $exists = $this->db->select("SELECT id FROM cron_emails WHERE email_key='" . $this->replace_sql($key) . "' AND email_id='" . $this->replace_sql($id) . "' AND status='N'");
   if (!$exists) {
    $this->db->insert('cron_emails', ['email_key' => $key, 'email_id' => $id, 'attachments' => $files, 'add_date' => date('Y-m-d H:i:s')]);
   }
  }
  
  public function send_cron_email()
  {
   $query = "SELECT * FROM cron_emails WHERE status='N' ORDER BY id LIMIT 5";
   if ($crons = $this->db->selectall($query)) {
    foreach ($crons as $data) {
     $attachments = $this->json_decode($data['attachments']);
     if (!$this->email) {
      $this->email = new email($this, $this->db);
     }
     $this->email->send_auto_email($data['email_key'], $data['email_id'], $attachments);
     $this->db->update('cron_emails', ['status' => 'Y'], ['id' => $data['id']]);
    }
   }
  }
  
  public function field_string($str)
  {
   $str = preg_replace('/[^a-z0-9- \']+/i', '', strtolower($str));
   return preg_replace('/\s+/', '_', $str);
  }
  
  public function permalink($type = '', $data = array())
  {
   $url = app_url;
   switch ($type) {
    case in_array($type, array('register-verification', 'reset-verification', 'reset-password')):
     $url .= $type . ($data ? '?key=' . $this->encrypt_post_data($data) : '');
     break;
    case 'blog-detail':
     $url .= 'blog' . ($data ? ds . $data['page_url'] . ds . $data['id'] : '');
     break;
    case 'blog-cat':
     $url .= 'blog/category/' . $this->url_string($data['page_url']);
     break;
    case 'blog-tag':
     $url .= 'blog/tag/' . $this->url_string($data);
     break;
    case 'blog-archive':
     $url .= 'blog/' . $this->url_string($data['year']) . '/' . $this->url_string($data['month']);
     break;
    case 'product-parent':
     $url .= 'products' . ($data['page_url'] ? ds . $this->url_string($data['page_url']) : '');
     break;
    case 'product-cat':
     $url .= 'products' . ($data['page_url'] ? ds . $this->url_string($data['parent_url']) . ds . $this->url_string($data['page_url']) : '');
     break;
    case 'product-detail':
     $id = ($this->varv('product_id', $data) ? $data['product_id'] : $this->varv('id', $data));
     $url .= 'products' . ($id ? ds . $this->url_string($data['parent_url']) . ds . $this->url_string($data['cat_url']) . ds . $this->url_string($data['page_url']) . ds . $id : '');
     break;
    default:
     $info = pathinfo($url . $type);
     if (isset($info['extension']) && in_array($info['extension'], ['css', 'js', 'less'])) {
      $url .= $type . '?v=' . $this->version;
     } else {
      $url .= $type;
     }
     //$url .= (strrchr($type, '.') == '.css' || (strrchr($type, '.') == '.js') ? $type . '?v=' . $this->version : $type);
     break;
   }
   return $url;
  }
  
  public function url_string($str)
  {
   $str = preg_replace('/[^a-z0-9- \']+/i', '', strtolower($str));
   return preg_replace('/\s+/', '-', $str);
  }
  
  public function varv($k = '', $data = array(), $arr = '')
  {
   if ($k != '') {
    return $this->type_val('CUSTOM', $k, $arr, $data);
   }
   return '';
  }
  
  public function user_timing($now = '', $utc = true, $format = '', $timezone = 'UTC')
  {
   if (empty($now)) {
    $now = date('Y-m-d H:i:s');
   }
   $zone = new DateTimeZone($timezone);
   $datetime = new DateTime($now, $zone);
   $offset = $zone->getOffset($datetime) / 3600;
   $return = $datetime->format('l, F d, Y') . ' at ' . $datetime->format('h:sa T');
   if (!empty($format)) {
    $return = $datetime->format($format);
   }
   if ($utc == true) {
    //$return .= ' (' . date_default_timezone_get() . $this->timenumtotimevalue($offset) . ')';
   }
   return $return;
  }
  
  public function tmp_file_data($filename)
  {
   return $this->json_encode(array('folder' => 'tmp/', 'filename' => $filename));
  }
  
  public function tmp_file($filename = '')
  {
   $path = upload_url . 'tmp' . ds;
   if ($filename) {
    $path .= $filename;
   }
   return $path;
  }
  
  public function get_file($data, $w = 0, $h = 0, $r = 0)
  {
   if ($this->file_exists($data)) {
    $data = $this->json_decode($data);
    $sf = $this->encrypt($data['folder']);
    $url = app_url . 'files/';
    $file = explode('.', $data['filename']);
    $size = '';
    if ($w > 0 && $h > 0) {
     if ($w == $h) {
      $size = 'ss' . $w;
     } else {
      $size = 'sp' . $w . 'x' . $h;
     }
    } else if ($w > 0 && !$h > 0) {
     $size = 'sw' . $w;
    } else if (!$w > 0 && $h > 0) {
     $size = 'sh' . $h;
    } else if ($r > 0) {
     $size = 'sq' . $r;
    }
    if (!empty($size)) {
     $url .= $size . '/';
    }
    $url .= $data['filename'] . '?sf=' . $sf;
   } else {
    $url = app_url . 'assets/img/blank.gif';
   }
   return $url;
  }
  
  public function file_exists($data, $file = '', $thumb = '')
  {
   if ($file == '') {
    $data = $this->json_decode($data);
    if (!$data['filename']) {
     return false;
    }
    $file = upload_path . $data['folder'] . $thumb . $data['filename'];
   }
   if ($file != '') {
    if (file_exists($file)) {
     return true;
    }
   }
   return false;
  }
  
  public function format_size($bytes, $decimals = 2)
  {
   $size = array('Bytes', 'KB', 'MB', 'GB', 'TB');
   $factor = floor((strlen($bytes) - 1) / 3);
   return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
  }
  
  public function return_bytes($val)
  {
   $val = trim($val);
   $last = strtolower($val[strlen($val) - 1]);
   $val = substr($val, 0, -1);
   switch ($last) {
    case 'g':
     $val *= 1024;
    case 'm':
     $val *= 1024;
    case 'k':
     $val *= 1024;
   }
   return $val;
  }
  
  public function number_format($val, $dec = 2, $point = '.', $sep = '')
  {
   if ($val == '') {
    $val = 0;
   }
   return number_format($val, $dec, $point, $sep);
  }
  
  public function complie_string($string, $data = array())
  {
   if ($data) {
    foreach ($data as $k => $v) {
     $string = str_replace($k, $v, $string);
    }
   }
   echo $string;
  }
  
  public function unlink_files($path, $filename)
  {
   $info = @pathinfo($path . $filename);
   $this->del_files($path, $info['filename']);
  }
  
  public function del_files($path, $filename)
  {
   $files = glob($path . $filename . '??*');
   if ($files) {
    foreach ($files as $file) {
     @unlink($file);
    }
   }
  }
  
  public function in_query($ids)
  {
   return str_replace(",", "','", $this->replace_sql($ids));
  }
  
  public function console($data, $array = false, $exit = false)
  {
   echo '<pre>';
   if ($array) {
    print_r($data);
   } else {
    echo $data;
   }
   echo '</pre>';
   if ($exit) {
    exit();
   }
  }
  
  public function make_embed_code($url, $width = '100%', $height = '320')
  {
   return '<iframe src="' . $url . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
  }
  
  public function get_youtube_url($id = '')
  {
   if ($id) {
    return 'https://www.youtube.com/watch?v=' . $id;
   }
   return $id;
  }
  
  public function youtube_embed($url, $h = 450, $w = 0, $params = '')
  {
   $str = '';
   if ($w == 0) {
    $w = '100%';
   }
   if ($this->is_youtube_video_exists($url)) {
    $str = '<iframe width="' . $w . '" height="' . $h . '" src="https://www.youtube.com/embed/' . $this->get_youtube_id($url) . ($params ? '?' . $params : '') . '" frameborder="0" allowfullscreen></iframe>';
   }
   return $str;
  }
  
  public function is_youtube_video_exists($url)
  {
   $data = @file_get_contents('http://www.youtube.com/oembed?url=' . $url);
   if ($data) {
    return $this->json_decode($data);
   }
   return false;
  }
  
  public function get_youtube_id($url)
  {
   parse_str(parse_url($url, PHP_URL_QUERY), $url_vars);
   return $url_vars['v'];
  }
  
  function ytthumb($url, $t = '')
  {
   //        preg_match('/youtube\.com\/v\/([\w\-]+)/', $url, $match);
   //        if ($match[1] == '')
   preg_match('/youtube\.com\/embed\/([\w\-]+)/', $url, $match);
   if ($match[1] == '')
    preg_match('/v\=(.+)&/', $url, $match);
   if ($match[1] == '')
    preg_match('/v\=(.+)/', $url, $match);
   $thumb['xs'] = "http://img.youtube.com/vi/" . $match[1] . "/0.jpg";
   $thumb['sm'] = "http://img.youtube.com/vi/" . $match[1] . "/3.jpg";
   $thumb['md'] = "http://img.youtube.com/vi/" . $match[1] . "/2.jpg";
   $thumb['lg'] = "http://i.ytimg.com/vi/" . $match[1] . "/hqdefault.jpg";
   if (empty($t)) {
    return $thumb;
   }
   return $thumb[$t];
  }
  
  public function numbers_only($str)
  {
   return preg_replace('/[^0-9]/', '', $str);
  }
  
  public function str_pos_multi($haystack, $needle, $offset = 0)
  {
   if (!is_array($needle))
    $needle = array($needle);
   foreach ($needle as $query) {
    if (strpos($haystack, $query, $offset) !== false)
     return true; // stop on first true result
   }
   return false;
  }
  
  public function gen_password($length = 12, $case = 'luds')
  {
   $sets = array();
   if (strpos($case, 'l') !== false)
    $sets[] = 'abcdefghjkmnpqrstuvwxyz';
   if (strpos($case, 'u') !== false)
    $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
   if (strpos($case, 'd') !== false)
    $sets[] = '23456789';
   if (strpos($case, 's') !== false)
    $sets[] = '!@#$%&*?';
   $all = '';
   $password = '';
   foreach ($sets as $set) {
    $password .= $set[array_rand(str_split($set))];
    $all .= $set;
   }
   $all = str_split($all);
   for ($i = 0; $i < $length - count($sets); $i++)
    $password .= $all[array_rand($all)];
   $password = str_shuffle($password);
   try {
    if ($this->password_validation($password)) {
     return $password;
    }
   } catch (Exception $ex) {
    return $this->gen_password();
   }
  }
  
  public function password_validation($password)
  {
   if (empty($password)) {
    throw new Exception('Please enter your password.');
   }
   if (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $password)) {
    throw new Exception('Your password must be contains 1 Uppercase Letter, Lowercase Letter, Number and Special Character and Minimum 8 Characters.');
   }
   if (is_numeric($password[0])) {
    throw new Exception('Make sure your password first character not numeric.');
   }
   $error = false;
   if (preg_match('/\d{3}+/', $password, $matches)) {
    if (count($matches) > 0) {
     foreach ($matches as $i => $m) {
      if ($m[0] == ($m[1] + 1) && $m[1] == ($m[2] + 1)) {
       $error = true;
      }
     }
    }
   }
   if ($error) {
    throw new Exception('Make sure your password does not contain consecutive value like: 123');
   }
   return true;
  }
  
  public function is_image($ext)
  {
   if (in_array($ext, $this->allowed_image_formats) !== false) {
    return true;
   }
   return false;
  }
  
  public function get_default_icon($ext)
  {
   $url = upload_url . 'media/';
   switch ($ext) {
    case in_array($ext, array('xlsx', 'xls')):
     $url .= 'spreadsheet.png';
     break;
    case in_array($ext, array('csv', 'txt')):
     $url .= 'text.png';
     break;
    case in_array($ext, array('doc', 'docx', 'pdf')):
     $url .= 'document.png';
     break;
    case in_array($ext, array('css', 'php', 'js', 'html', 'htm')):
     $url .= 'code.png';
     break;
    case in_array($ext, array('wmw', 'avi', 'mp4', 'mov')):
     $url .= 'video.png';
     break;
    case in_array($ext, array('mp3', 'wav', 'ogg')):
     $url .= 'audio.png';
     break;
    default:
     $url .= 'default.png';
     break;
   }
   return $url;
  }
  
  public function show_price($amt, $cur = '', $round = 2)
  {
   if (!$cur) {
    $cur = $this->currency . ' ';
   }
   if (!empty($cur)) {
    $cur = $cur . ' ';
   }
   if (isset($this->company)) {
    if ($this->varv('default_currency', $this->company) != '') {
     $cur = $this->company['default_currency'] . ' ';
    }
    if ($this->varv('rate', $this->company) != '') {
     $amt = $amt * $this->company['rate'];
    }
   }
//   return sprintf($cur . '%.' . $round . 'f', $amt);
   return sprintf($cur . '%s', number_format($amt, $round));
  }
  
  public function disp_id($no, $n = 5)
  {
   return str_pad($no, $n, "0", STR_PAD_LEFT);
  }
  
  function str_replace_first($from, $to, $subject)
  {
   $from = '/' . preg_quote($from, '/') . '/';
   return preg_replace($from, $to, $subject, 1);
  }
  
  function time_ago($old_date)
  {
   $current = time();
   $old = strtotime($old_date);
   $sec_dif = $current - $old;
   $sec_dif = str_replace("-", "", $sec_dif);
   if ($sec_dif >= 0 && $sec_dif < 60) {
    $sec_dif = floor($sec_dif) ? floor($sec_dif) : 1;
    return $sec_dif . " secs ago";
   } elseif ($sec_dif >= 60 && $sec_dif < 3600) {
    $sec_dif = floor($sec_dif / 60) ? floor($sec_dif / 60) : 1;
    return $sec_dif . " mins ago";
   } elseif ($sec_dif >= 3600 && $sec_dif < 86400) {
    $sec_dif = floor($sec_dif / 3600) ? floor($sec_dif / 3600) : 1;
    return $sec_dif . " hrs ago";
   } elseif ($sec_dif >= 86400 && $sec_dif < 2592000) {
    $sec_dif = floor($sec_dif / 86400) ? floor($sec_dif / 86400) : 1;
    return $sec_dif . " days ago";
   } elseif ($sec_dif >= 2592000 && $sec_dif < 31104000) {
    $sec_dif = floor($sec_dif / 2592000) ? floor($sec_dif / 2592000) : 1;
    return $sec_dif . " months ago";
   } elseif ($sec_dif >= 31104000 && $sec_dif < 3110400000) {
    $sec_dif = floor($sec_dif / 31104000) ? floor($sec_dif / 31104000) : 1;
    return $sec_dif . " years ago";
   } elseif ($sec_dif >= 3110400000) {
    $sec_dif = floor($sec_dif / 3110400000) ? floor($sec_dif / 3110400000) : 1;
    return $sec_dif . " century ago";
   }
  }
  
  function currency_conversion($fcur, $tcur, $amt = 1)
  {
   $result = '1.0000';
   if (isset($fcur) && isset($tcur) && isset($amt)) {
    if (function_exists('curl_init')) {
     $curl = curl_init();
     curl_setopt_array($curl, array(
       CURLOPT_URL => "https://www.google.com/search?q=" . $fcur . "+to+" . $tcur,
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_SSL_VERIFYHOST => false,
       CURLOPT_SSL_VERIFYPEER => false,
       CURLOPT_HTTPHEADER => ["content-type: text/html", "cache-control: no-cache"])
     );
     
     $response = curl_exec($curl);
     $err = curl_error($curl);
     
     if ($err) {
      throw new Exception('Currency Conversion Error: ' . $err);
     }
     
     if ($response) {
      preg_match('/<div class="BNeawe iBp4i AP7Wnd">(.*?)<\/div>/s', $response, $matches);
      if (isset($matches[0]) && !empty($matches[0])) {
       $str = strip_tags($matches[0]);
       $result = sprintf("%.4f", $str);
      }
     }
    }
   }
   return $result;
  }
  
  public function save_file($type, $table, $id, $alt = '')
  {
   if (is_array($type)) {
    foreach ($type as $t) {
     $this->do_save_file($t, $table, $id, $alt);
    }
   } else {
    $this->do_save_file($type, $table, $id, $alt);
   }
  }
  
  public function do_save_file($type, $table, $id, $alt = '')
  {
   if ($this->file($type)) {
    $file = $this->file($type);
    $info = @pathinfo($file['name']);
    
    $ext = strtolower($info['extension']);
    if (in_array($ext, $this->allowed_file_formats) !== false) {
     if ($ext == 'jpeg') {
      $ext = 'jpg';
     }
     $this->update_uploaded_file($type, $table, $id);
     
     $filename = date('YmdHis') . '_' . rand(0000000, 9999999) . '.' . $ext;
     $path = $this->create_file_path(false, $filename);
     move_uploaded_file($file['tmp_name'], $path);
     
     $meta = array('folder' => $this->create_file_path(true), 'filename' => $filename, 'size' => $file['size']);
     $file_id = $this->db->insert('files', array('type_id' => $id, 'table_name' => $table, 'type' => $type, 'name' => $file['name'], 'alt_text' => $alt, 'meta_value' => $this->json_encode($meta), 'add_date' => date('Y-m-d H:i:s')));
     $this->db->update($table, array($type => $file_id), array('id' => $id));
    }
   }
  }
  
  public function file($k = '', $arr = '')
  {
   return $this->type_val('FILES', $k, $arr);
  }
  
  public function update_uploaded_file($type, $table, $id)
  {
   $query = "SELECT id FROM files WHERE type_id='" . $id . "' AND type='" . $type . "' AND table_name='" . $table . "' AND deleted='N'";
   if ($data = $this->db->select($query)) {
    $this->db_file_delete($data['id']);
   }
  }
  
  public function db_file_delete($id)
  {
   $data = $this->file_data($id);
   $path = upload_path . $data['folder'];
   
   $meta = pathinfo($path . $data['filename']);
   $this->del_files($path, $meta['filename']);
   $this->db->delete('files', array('id' => $data['id']));
  }
  
  public function file_data($id = 0)
  {
   if (!$id) {
    return false;
   }
   $query = "SELECT id, name, meta_value FROM files WHERE id='" . $this->replace_sql($id) . "'";
   if ($data = $this->db->select($query)) {
    if ($data['meta_value'] = $this->json_decode($data['meta_value'])) {
     foreach ($data['meta_value'] as $k => $v) {
      $data[$k] = $v;
     }
     unset($data['meta_value']);
    }
   }
   return $data;
  }
  
  public function create_file_path($c = false, $filename = '')
  {
   if ($c == true) {
    $path = date('Y') . ds . date('m') . ds;
   } else {
    $path = upload_path . date('Y') . ds;
    if (!file_exists($path)) {
     mkdir($path);
    }
    $path = $path . date('m') . ds;
    if (!file_exists($path)) {
     mkdir($path);
    }
    $path .= $filename;
   }
   return $path;
  }
  
  public function save_session_file($type, $table, $id)
  {
   $data = $this->session($this->tmp_path, $type);
   if (!$this->varv('filename', $data)) {
    return;
   }
   $info = @pathinfo($this->tmp_path($data['filename'], $this->tmp_path));
   $ext = $info['extension'];
   
   $this->update_uploaded_file($type, $table, $id);
   
   $filename = date('YmdHis') . '_' . rand(0000000, 9999999) . '.' . $ext;
   $path = $this->create_file_path(false, $filename);
   @copy($info['dirname'] . ds . $info['basename'], $path);
   @unlink($info['dirname'] . ds . $info['basename']);
   
   $meta = array('folder' => $this->create_file_path(true), 'filename' => $filename, 'size' => $data['size'], 'ext' => $ext);
   $file_id = $this->db->insert('files', array('type_id' => $id, 'table_name' => $table, 'type' => $type, 'name' => $info['basename'], 'meta_value' => $this->json_encode($meta), 'add_date' => date('Y-m-d H:i:s')));
   $this->db->update($table, array($type => $file_id), array('id' => $id));
   
   unset($_SESSION[$this->tmp_path][$type]);
  }
  
  public function tmp_path($filename = '', $dir = array())
  {
   $path = upload_path . 'tmp' . ds;
   if ($dir) {
    if (is_array($dir)) {
     foreach ($dir as $v) {
      $path .= $v . ds;
      if (!file_exists($path)) {
       mkdir($path);
      }
     }
    } else {
     $path .= $dir . ds;
     if (!file_exists($path)) {
      mkdir($path);
     }
    }
   }
   if ($filename) {
    $path .= $filename;
   }
   return $path;
  }
  
  public function save_session_files($type, $table, $id)
  {
   $data = $this->session($type);
   
   $ids = '';
   if ($this->post('default')) {
    foreach ($this->post('default') as $key => $val) {
     if (is_numeric($val)) {
      $ids .= $val . ',';
     }
    }
   }
   $ids = rtrim($ids, ',');
   if ($data == '') {
    return $ids;
   }
   if (count($data) == 0) {
    return $ids;
   }
   
   foreach ($data as $k => $v) {
    $info = @pathinfo($this->tmp_path($k));
    $ext = $info['extension'];
    
    $filearr = explode('.', $v['filename']);
    $this->move_files($this->tmp_path(), $filearr[0], $this->create_file_path());
    
    $meta = array('folder' => $this->create_file_path(true), 'filename' => $v['filename'], 'size' => $v['size'], 'ext' => $ext);
    $file_id = $this->db->insert('files', array('type_id' => $id, 'table_name' => $table, 'type' => $type, 'name' => $this->varv('name', $v), 'meta_value' => $this->json_encode($meta), 'add_date' => date('Y-m-d H:i:s')));
    
    if ($this->post('default')) {
     foreach ($this->post('default') as $key => $val) {
      if (!is_numeric($val)) {
       if ($val == $v['filename']) {
        $ids .= $file_id . ',';
       }
      }
     }
    }
   }
   unset($_SESSION[$type]);
   return rtrim($ids, ',');
  }
  
  public function move_files($tmp, $filename, $path)
  {
   $files = glob($tmp . $filename . '??*');
   if ($files) {
    foreach ($files as $file) {
     $info = pathinfo($file);
     @copy($file, $path . $info['basename']);
     @unlink($file);
    }
   }
  }
  
 }
