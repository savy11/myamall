<?php
 
 namespace controllers;
 
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sessions.php';
 
 use Exception;
 use resources\controllers\controller as main;
 
 class controller extends main
 {
  
  public $style = '', $script = '', $pagination = null, $sno = 0, $rows = array();
  public $socials = array(), $modal = array(), $topparents = array(), $topcats = array();
  public $filter = array();
  
  public function __construct($main_db = false, $need_db = true, $c = true)
  {
   parent::__construct($main_db, $need_db);
   if ($need_db && $c) {
    if (!$this->session('ip') && $this->session('ip') == '') {
     $_SESSION['ip'] = $this->ip_api();
    }
    $this->company_detail();
    if (!$this->is_ajax_call()) {
     $this->auto_login();
    }
    if ($this->session('user', 'login')) {
     $this->auth();
    }
    if ($this->cookie('cart') && !isset($_SESSION['cart'])) {
     $_SESSION['cart'] = $this->cookie('cart');
    }
    $this->tmp_cart();
   }
  }
  
  public function company_detail()
  {
   $query = "SELECT a.*, c.id as currency_id, c.currency_code as default_currency, c.exchange_rate as rate FROM a_company a LEFT OUTER JOIN m_currencies c ON a.default_currency=c.id WHERE a.id='1'";
   $this->company = $this->db->select($query);
   $this->company['email'] = $this->json_decode($this->company['email']);
   $site_url = parse_url(app_url);
   $this->company['site_url'] = 'www.' . $site_url['host'];
   $this->socials = $this->db->selectall("SELECT * FROM a_socials WHERE publish='Y' ORDER BY title");
   
   if ($this->session('default')) {
    $where = "WHERE c.publish='Y'";
    if ($this->varv('country_id', $this->company) != $this->session('default', 'country')) {
     if ($this->session('default', 'country') != '') {
      $where .= " AND c.id='" . $this->replace_sql($this->session('default', 'country')) . "'";
     }
     $query = "SELECT c.id as country_id, c.country_name as country, c1.id as currency_id, c1.currency_code as default_currency, c1.exchange_rate as rate FROM m_countries c "
      . "LEFT OUTER JOIN m_currencies c1 ON c.currency_id=c1.id "
      . "{$where}";
    } else {
     if ($this->session('default', 'currency') != '') {
      $where .= " AND c.id='" . $this->replace_sql($this->session('default', 'currency')) . "'";
     }
     $query = "SELECT c.id as currency_id, c.currency_code as default_currency, c.exchange_rate as rate FROM m_currencies c {$where}";
    }
    if ($query) {
     $ip = $this->db->select($query);
     $this->company = array_replace_recursive($this->company, $ip);
    }
   } else {
    if ($this->session('ip')) {
     $query = "SELECT c.id as country_id, c.country_name as country, c1.id as currency_id, c1.currency_code as default_currency, c1.exchange_rate as rate FROM m_countries c "
      . "LEFT OUTER JOIN m_currencies c1 ON c.currency_id=c1.id "
      . "WHERE (c.country_name='" . $this->session('ip', 'country') . "' OR c.country_name LIKE '%" . $this->session('ip', 'country') . "') OR (c.country_name='" . $this->session('ip', 'countryCode') . "' OR c.country_code LIKE '%" . $this->session('ip', 'countryCode') . "')";
     $ip = $this->db->select($query);
     $this->company = array_replace_recursive($this->company, $ip);
    }
   }
   
   $query = "SELECT id, parent_name, page_url FROM m_products_parent WHERE publish='Y' ORDER BY parent_name";
   $this->topparents = $this->db->freg_all($query, ['id'], ['parent_name', 'page_url']);
   
   $query = "SELECT c.id, c.parent_id as parent_id, c.category_name, c.page_url, p.page_url as parent_url FROM m_products_cat c "
    . "LEFT OUTER JOIN m_products_parent p ON c.parent_id=p.id "
    . "WHERE c.publish='Y' AND c.parent_id != 0 "
    . "ORDER BY p.parent_name";
   $this->topcats = $this->db->freg_all($query, ['parent_id']);
  }
  
  function ip_api($ip = NULL)
  {
   $result = [];
   $ip = $_SERVER['REMOTE_ADDR'];
   if (!is_null($ip)) {
    $ip = $ip;
   }
   if (local) {
    $ip = '103.217.123.3';
//    $ip = '72.229.28.185';
   }
   if ($exists = $this->db->select("SELECT id, json FROM ip_info WHERE ip='" . $this->replace_sql($ip) . "'")) {
    $result = $this->json_decode($exists['json']);
   } else {
    $ip_json = file_get_contents('http://ip-api.com/json/' . $ip, true);
    $ip_data = $this->json_decode($ip_json);
    if ($ip_data['status'] == 'success') {
     $this->db->insert('ip_info', ['ip' => $ip, 'json' => $ip_json, 'add_date' => date('Y-m-d H:i:s')]);
     $result = $ip_data;
    }
   }
   return $result;
  }
  
  public function auto_login()
  {
   if ($this->cookie('user') && $this->session('user', 'login') == '') {
    try {
     $query = "SELECT id, publish FROM users WHERE publish='Y' AND id='" . $this->cookie('user', 'id') . "'";
     if (!$data = $this->db->select($query)) {
      $this->clear_cookie('user');
      throw new Exception('Your account does not exists. May be deleted by administrator.');
     }
     if ($data['publish'] == 'N') {
      $this->clear_cookie('user');
      throw new Exception('Your account is not publish. Please contact with administrator.');
     }
     unset($data['publish']);
     
     $_SESSION['user'] = $data;
     $_SESSION['user']['login_time'] = date('Y-m-d H:i:s');
     $_SESSION['user']['login'] = true;
     $_SESSION['user']['guest'] = false;
     
     $this->gen_cookie('user', array('id' => $data['id']));
     $this->update_log($this->session('user', 'id'), 'auto_login');
     $this->redirect($this->server('REQUEST_URI'));
    } catch (Exception $ex) {
     $this->session_msg($ex->getMessage(), 'error', 'Login');
     $this->redirecting('login');
    }
   }
  }
  
  public function update_log($id, $type)
  {
   $columns = array('user_id' => $id, 'session_id' => session_id(), 'type' => $type, 'ip' => $this->server('REMOTE_ADDR'), 'browser' => $this->get_browser(), 'os' => $this->get_os(), 'log_date' => date('Y-m-d H:i:s'));
   $this->db->insert('logs', $columns);
  }
  
  public function redirect($url = '')
  {
   if ($url == '') {
    $url = $this->permalink();
   }
   header('HTTP/1.1 301 Moved Permanently');
   header('Location:' . $url);
   exit();
  }
  
  public function session_msg($message, $type, $id = '', $title = '')
  {
   if ($title == '') {
    $title = $this->varv('page_title', $this->cms) ? $this->varv('page_title', $this->cms) : 'Message';
   }
   $_SESSION['er']['title'] = $title;
   $_SESSION['er']['message'] = $message;
   $_SESSION['er']['type'] = $type;
   if ($id) {
    $_SESSION['er']['id'] = $id;
   }
  }
  
  public function redirecting($type = '', $dt = array())
  {
   $this->redirect($this->permalink($type, $dt));
  }
  
  public function auth()
  {
   $query = "SELECT u.* FROM users u WHERE u.id='" . $this->session('user', 'id') . "'";
   $this->user = $this->db->select($query);
  }
  
  /** Cart * */
  public function tmp_cart($cart_ids = NULL)
  {
   if ($this->session('cart')) {
    $ids = implode(',', $this->session('cart'));
    if (!is_null($cart_ids)) {
     $ids = implode(',', $cart_ids);
    }
    $query = "SELECT t.id, p.id as product_id, t.product_title, IF(t.size_id > 0, s.size_name, '') as size, IF(t.color_id > 0, c1.title, '') as color, p.page_url as product_url, p.page_url, c.page_url as cat_url, p1.page_url as parent_url, IF(t.color_id > 0 , f.meta_value, f1.meta_value) as product_image, IF(t.color_id > 0, c1.basic_price, p.basic_price) as price, IF(t.color_id > 0, 0, p.special_price) as special_price, t.qty, t.sale_id "
     . "FROM tmp_cart t "
     . "LEFT OUTER JOIN m_products p ON t.product_id=p.id "
     . "LEFT OUTER JOIN m_products_cat c ON p.category_id=c.id "
     . "LEFT OUTER JOIN m_products_size s ON t.size_id=s.id "
     . "LEFT OUTER JOIN m_products_color c1 ON t.color_id=c1.id "
     . "LEFT OUTER JOIN m_products_parent p1 ON c.parent_id=p1.id "
     . "LEFT OUTER JOIN files f ON c1.image=f.id "
     . "LEFT OUTER JOIN files f1 ON p.product_image=f1.id "
     . "WHERE t.id IN(" . $ids . ") "
     . "ORDER BY t.id DESC";
    $this->cart = $this->db->freg_all($query, array('id'), array('product_id', 'product_title', 'size', 'color', 'product_url', 'page_url', 'cat_url', 'parent_url', 'product_image', 'price', 'special_price', 'qty', 'sale_id'));
    if (!$this->cart) {
     unset($_SESSION['cart']);
    }
    $this->update_payment();
   }
  }
  
  public function update_payment()
  {
   $pay = array('sub_total' => 0, 'total' => 0);
   if ($this->cart) {
    foreach ($this->cart as $k => $v) {
     if ($v['sale_id'] > 0) {
      $query = "SELECT sp.discount_price as price FROM m_sale s LEFT OUTER JOIN m_sale_products sp ON sp.sale_id=s.id WHERE s.id='" . $this->replace_sql($v['sale_id']) . "' AND s.publish='Y' AND sp.product_id='" . $this->replace_sql($v['product_id']) . "'";
      if ($sale = $this->db->select($query)) {
       $v['price'] = $sale['price'];
      }
     }
     $price = ($v['qty'] * ($v['special_price'] > 0 ? $v['special_price'] : $v['price']));
     $this->cart[$k]['price'] = $v['special_price'] > 0 ? $v['special_price'] : $v['price'];
     $this->cart[$k]['total_price'] = $price;
     $pay['sub_total'] += $price;
    }
    $pay['total'] = $pay['sub_total'];
   }
   $this->pay = $pay;
  }
  
  public function get_cats($limit = '', $all = FALSE)
  {
   $l = '';
   if ($limit != '') {
    $l = " LIMIT 0," . $limit;
   }
   $query = "SELECT id, category_name, page_url FROM m_products_cat WHERE publish='Y' ORDER BY category_name{$l}";
   if ($all) {
    return $this->db->freg_all($query, ['id'], ['id', 'category_name', 'page_url']);
   } else {
    return $this->db->freg($query, ['id'], 'category_name');
   }
  }
  
  public function get_quick_links($limit = '')
  {
   $l = "";
   if ($limit != '') {
    $l = " LIMIT 0," . $limit;
   }
   $query = "SELECT id, page_title, page_url FROM m_pages WHERE publish='Y' AND quick_link='Y' ORDER BY id{$l}";
   return $this->db->freg_all($query, ['id'], ['id', 'page_title', 'page_url']);
  }
  
  public function get_faqs()
  {
   $query = "SELECT id, question, answer FROM m_faqs WHERE publish='Y' ORDER BY id";
   return $this->db->freg_all($query, ['id'], ['question', 'answer']);
  }
  
  public function cms_page($page_url = '', $key_type = '', $freg = false)
  {
   $fields = "";
   if ($key_type) {
    $fields = ", '{$key_type}'as key_type";
   }
   $query = "SELECT f.meta_value as image,h.meta_value as header_image, p.page_title, p.page_heading, p.page_desc, p.meta_keywords, p.meta_desc{$fields} FROM m_pages p LEFT OUTER JOIN files f ON p.image=f.id LEFT OUTER JOIN files h ON p.header_image=h.id WHERE p.page_url='" . $this->replace_sql($page_url) . "' AND p.publish='Y'";
   if ($freg) {
    return $this->db->freg_all($query, array('key_type'), array('page_title', 'page_heading', 'page_desc', 'meta_keywords', 'meta_desc'));
   } else {
    $this->cms = $this->db->select($query);
   }
  }
  
  public function show_er_msg($id = null)
  {
   $msg = '';
   if ($this->session('er') != '' && count($this->session('er')) > 0) {
    if ($id) {
     if ($this->session('er', 'id') != $id) {
      return;
     }
    }
    $type = $this->session('er', 'type');
    if ($type == 'error') {
     $type = 'danger';
    }
    $msg = '<div class="alert-box alert alert-' . $type . '">' . $this->replace_sql($this->session('er', 'message')) . '</div>';
   }
   unset($_SESSION['er']);
   return $msg;
  }
  
  public function not_found()
  {
   header('HTTP/1.1 404 Not Found');
   include_once(app_path . ds . '404.php');
   exit();
  }
  
  public function current_url()
  {
   return request_scheme . domain . $this->server('REQUEST_URI');
  }
  
  public function files($ids = '')
  {
   if (!$ids) {
    return '';
   }
   $query = "SELECT id, meta_value FROM files WHERE id IN({$ids}) ORDER BY id";
   return $this->db->selectall($query);
  }
  
  public function check_v_code($id, $type, $code = '')
  {
   if (!$data = $this->get_v_code($id, $type)) {
    throw new Exception('May be verification expired or deleted.');
   }
   if (time() > strtotime($data['expiry_date'])) {
    $this->update_v_code($data['id'], 'E');
    throw new Exception('Verification code is expired.');
   }
   if ($data['code'] != $code) {
    throw new Exception('Verification code is invalid.');
   }
   return $data;
  }
  
  public function get_v_code($id, $type = null)
  {
   $query = "SELECT * FROM v_codes WHERE id='" . $id . "' AND type='" . $type . "' AND status='N'";
   if ($data = $this->db->select($query)) {
    if (time() > strtotime($data['expiry_date'])) {
     $this->update_v_code($data['id'], 'E');
     return false;
    }
   }
   return $data;
  }
  
  public function update_v_code($id, $status = 'Y')
  {
   //   $this->db->update('v_codes', array('status' => $status), array('id' => $id));
  }
  
  public function cart_count()
  {
   if ($this->session('cart')) {
    return count($this->cart);
   }
   return '0';
  }
  
  /** Auth * */
  public function already_login()
  {
   if ($this->session('user', 'id') != '') {
    if ($this->session('user', 'login') == true) {
     $this->redirecting();
    }
   }
  }
  
  public function require_login()
  {
   if ($this->session('user', 'login') == '') {
    $this->set_login_referer();
    $this->redirecting('login');
   }
  }
  
  public function set_login_referer()
  {
   $url = request_scheme . $this->server('HTTP_HOST') . $this->server('REQUEST_URI');
   if (!$this->is_ajax_call()) {
    $url = ($this->server('HTTP_REFERER') != '' ? $this->server('HTTP_REFERER') : '');
   }
   $_SESSION['login_url'] = $url;
  }
  
  public function validate_login()
  {
   if ($this->session('user', 'login') != '' || $this->guest_login()) {
    return true;
   }
   return false;
  }
  
  public function guest_login()
  {
   return $this->session('user', 'guest');
  }
  
  public function get_addresses($all = false)
  {
   if ($all) {
    $field = "CONCAT_WS(', ', a.address, if(a.zip_code!='', CONCAT(a.city, ' - ', a.zip_code), a.city), a.state, a.country)";
   } else {
    $field = "CONCAT_WS(' - ', a.display_name, CONCAT_WS(', ', a.address, if(a.zip_code!='', CONCAT(a.city, ' - ', a.zip_code), a.city), a.state, a.country))";
   }
   $query = "SELECT a.id, a.display_name, a.email, a.mobile_no, {$field} as address FROM users_address a WHERE a.user_id='" . $this->session('user', 'id') . "' AND a.deleted='N'";
   if ($all) {
    return $this->db->freg_all($query, array('id'), array('display_name', 'email', 'mobile_no', 'address'));
   } else {
    return $this->db->freg($query, array('id'), 'address');
   }
  }
  
  public function get_cat_by_parents()
  {
   $arr = array();
   $query = "SELECT c.category_url as id, p.parent_url, c.category_name FROM m_fabrics_cat c " . "LEFT OUTER JOIN m_fabrics_parent p ON c.parent_id=p.id " . "WHERE c.publish='Y' " . "ORDER BY c.category_name";
   if ($data = $this->db->selectall($query)) {
    foreach ($data as $v) {
     $arr[$v['parent_url']][] = $v;
    }
   }
   return $arr;
  }
  
  public function show_list($list, $sel = '', $empty = true)
  {
   $str = '';
   if ($empty) {
    $str = '<option value=""></option>';
   }
   if ($list) {
    $key = current(array_keys($list));
    if (isset($list[$key]) && is_array($list[$key]) && count($list[$key]) > 1) {
     $keys = array_keys($list[$key]);
     $ele = $keys[0];
     unset($keys[0]);
     $str = "<option value=\"\" data-" . strtolower(str_replace("_", "", implode("='' data-", $keys))) . "=\"\"></option>";
     foreach ($list as $k => $v) {
      $values = array();
      foreach ($v as $a => $b) {
       if ($ele != $a) {
        $values[] = "data-" . strtolower(str_replace("_", "", $a)) . "='" . $b . "'";
       }
      }
      $str .= '<option value="' . $k . '"' . (strpos(',' . $sel . ',', ',' . $k . ',') !== false ? ' selected' : '') . ' ' . implode(' ', $values) . '>' . $this->make_html($v[$ele]) . '</option>';
     }
    } else {
     foreach ($list as $k => $v) {
      $str .= '<option value="' . $k . '"' . (strpos(',' . $sel . ',', ',' . $k . ',') !== false ? 'selected' : '') . '>' . $this->make_html($v) . '</option>';
     }
    }
   }
   return $str;
  }
  
  public function filter_array($type)
  {
   $data = $this->post_get($type);
   if (is_array($data)) {
    $data = implode(',', $data);
   }
   $_POST[$type] = $data;
   $_GET[$type] = $data;
   $this->filter[$type] = $data;
   return $data;
  }
  
  public function share_url($type = 'fb', $dt = array())
  {
   $url = urlencode($dt['ogurl']);
   if ($type == 'fb') {
    $url = 'https://www.facebook.com/sharer.php?u=' . $url;
   } else if ($type == 'tw') {
    $site = tw_site;
    $url = 'https://twitter.com/intent/tweet?url=' . $dt['ogurl'] . '&text=' . $dt['ogtitle'] . (isset($site) && !empty($site) ? '&via=' . $site : '');
   } else if ($type == 'pi') {
    $url = 'https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $this->show_string($dt['ogtitle'], 250) . '&media=' . $dt['ogimage'];
   } else if ($type == 'gp') {
    $url = 'https://plus.google.com/share?url=' . $dt['ogurl'];
   }
   return $url;
  }
  
  /*
 * Register
 */
  
  public function register()
  {
   $this->validate_post_token(true);
   if ($this->post('register') == '') {
    throw new Exception(_('Oops, something went wrong.'));
   }
   $_POST = $this->post('register');
   if ($this->post('first_name') == '') {
    throw new Exception(_('Please enter your first name.'));
   }
   if ($this->post('last_name') == '') {
    throw new Exception(_('Please enter your last name.'));
   }
   if ($this->post('email') == '') {
    throw new Exception(_('Please enter your email address.'));
   }
   if (filter_var($this->post('email'), FILTER_VALIDATE_EMAIL) === false) {
    throw new Exception(_('Please enter valid email address.'));
   }
   if ($this->db->value_exists('users', 'email', $this->post('email'))) {
    throw new Exception(_('Email already exists in our records.'));
   }
   if ($this->post('dob') == '') {
    throw new Exception(_('Please enter your date of birth.'));
   }
   if ($this->post('mobile_no') == '') {
    throw new Exception(_('Please enter your mobile no.'));
   }
   $password = $this->post('password');
   if ($password == '') {
    throw new Exception(_('Please enter your password.'));
   }
   // $this->password_validation($password);
   if ($this->post('confirm_password') == '') {
    throw new Exception(_('Please enter your confirm password.'));
   }
   if ($password != $this->post('confirm_password')) {
    throw new Exception('Passwords does not matched.');
   }
   /* if ($this->post('captcha') == '') {
    throw new Exception(_('Please enter the security captcha.'));
   }
   if ($this->post('captcha') != $this->session('captcha', 'register')) {
    throw new Exception(_('Invalid security captcha, Please try again.'));
   }  */
   if ($this->post('terms') != 1) {
    throw new Exception('You must agree with our terms and conditions and privacy policy.');
   }
   
   
   $date = date('Y-m-d H:i:s');
   $id = $this->db->insert('users', array(
    'first_name' => $this->post('first_name'),
    'last_name' => $this->post('last_name'),
    'display_name' => ($this->post('first_name') . ' ' . $this->post('last_name')),
    'email' => $this->post('email'),
    'dob' => $this->post('dob'),
    'mobile_no' => $this->post('mobile_no'),
    'password' => $password,
    'add_date' => $date,
    'ip' => $this->server('REMOTE_ADDR'),
    'browser' => $this->get_browser(),
    'os' => $this->get_os()
   ));
   if ($this->post('subscribe') == 1) {
    $query = "SELECT * FROM v_subscribers WHERE email='" . $this->replace_sql($this->post('email')) . "'";
    if (!$data = $this->db->select($query)) {
     $this->db->insert('v_subscribers', array(
      'email' => $this->post('email'),
      'ip' => $this->server('REMOTE_ADDR'),
      'browser' => $this->get_browser(),
      'os' => $this->get_os(),
      'add_date' => $date
     ));
    }
   }
   
   $this->v_code($id, 'Register', 60);
   $this->send_email('register_verification', $id);
   $this->send_email('new_registration', $id);
   return $id;
  }
  
  /** V Code * */
  public function v_code($id, $type = null, $mins = 10)
  {
   $code = $this->gen_code(6, false);
   $date = date('Y-m-d H:i:s');
   $code_expiry = date('Y-m-d H:i:s', strtotime($date . ' + ' . $mins . ' mins'));
   
   $code_id = $this->db->insert('v_codes', array('type_id' => $id, 'session_id' => session_id(), 'code' => $code, 'expiry_date' => $code_expiry, 'type' => $type, 'ip' => $this->server('REMOTE_ADDR'), 'browser' => $this->get_browser(), 'os' => $this->get_os(), 'add_date' => $date, 'update_date' => $date));
   return array('code' => $code, 'code_id' => $code_id);
  }
  
  public function register_verification()
  {
   if ($this->get('key') == '') {
    throw new Exception();
   }
   $data = $this->decrypt_post_data($this->get('key'));
   $query = "SELECT * FROM users WHERE email='" . $this->replace_sql($data['email']) . "'";
   if (!$dt = $this->db->select($query)) {
    throw new Exception(_('Oops, something went wrong.'));
   }
   if ($dt['publish'] == 'N') {
    throw new Exception(_('Your account is blocked. Please contact with administrator.'));
   }
   if ($dt['verified'] == 'Y') {
    throw new Exception(_('Your account is already verified.'));
   }
   if (!$this->get_v_code($data['code_id'], 'Register', $data['code'])) {
    throw new Exception(_('Oops, Verification code is expired, Please try again.'));
   }
   $this->db->update('users', array('verified' => 'Y'), array('email' => $data['email']));
   $this->db->update('v_subscribers', array('verified' => 'Y'), array('email' => $data['email']));
   $this->update_v_code($data['code_id']);
  }
  
  public function login()
  {
   $query = "SELECT id, first_name, last_name, email, mobile_no, publish, verified FROM users WHERE email='" . $this->replace_sql($this->post('email')) . "' AND password='" . $this->encrypt($this->post('password')) . "'";
   if (!$data = $this->db->select($query)) {
    throw new Exception('Invalid email or password!');
   }
   if ($data['publish'] == 'N') {
    throw new Exception('Your account is blocked. Please contact with administrator.');
   }
   unset($data['publish'], $data['verified']);
   
   $_SESSION['user'] = $data;
   $_SESSION['user']['login_time'] = date('Y-m-d H:i:s');
   $_SESSION['user']['login'] = true;
   
   if ($this->post('remember') == 1) {
    $this->gen_cookie('user', array('id' => $data['id']));
   }
   $this->update_log($this->session('user', 'id'), 'login');
  }
  
  public function check_stock($id = '')
  {
   if ($id == '') {
    throw new Exception('Oops, something went wrong.');
   }
   $query = "SELECT id FROM m_products WHERE id='" . $this->replace_sql($id) . "' AND in_stock='N'";
   if ($this->db->select($query)) {
    throw new Exception('This product is currently out of stock.');
   }
   
  }
  
  public function insert_quote()
  {
   $this->validate_post_token(true);
   if ($this->post('quote') == '') {
    throw new Exception('Please add your quote.');
   }
   $id = $this->db->insert('v_quotes', ['quote' => $this->post('quote'), 'ip' => $this->server('REMOTE_ADDR'), 'browser' => $this->get_browser(), 'os' => $this->get_os(), 'add_date' => date('Y-m-d H:i:s')]);
   $this->send_email('quote', $id);
  }
  
  public function get_product($id = NULL)
  {
   $query = "SELECT id, product_title, basic_price, special_price FROM m_products WHERE id='" . $this->replace_sql($id) . "' AND publish='Y'";
   if ($this->data = $this->db->select($query)) {
    $query = "SELECT sp.sale_id, s.sale_title, sp.discount_price as sale_price FROM m_sale s LEFT OUTER JOIN m_sale_products sp ON sp.sale_id=s.id AND sp.product_id='" . $this->data['id'] . "' WHERE s.publish='Y' AND '" . date('Y-m-d') . "' BETWEEN start_date and end_date";
    if ($sale = $this->db->select($query)) {
     $this->data = array_replace_recursive($this->data, $sale);
    }
    $this->list['colors'] = $this->get_colors($this->data['id']);
    $this->list['sizes'] = $this->get_sizes($this->data['id']);
   }
  }
  
  public function get_colors($id = NULL)
  {
   $result = [];
   if (!is_null($id)) {
    $query = "SELECT c.*, f.meta_value as image FROM m_products_color c "
     . "LEFT OUTER JOIN files f ON c.image=f.id "
     . "WHERE c.product_id='" . $this->replace_sql($id) . "' "
     . "ORDER BY c.id";
    if ($data = $this->db->selectall($query)) {
     $result = $data;
    }
   }
   return $result;
  }
  
  public function get_sizes($id = NULL)
  {
   $result = [];
   if (!is_null($id)) {
    $query = "SELECT GROUP_CONCAT(DISTINCT sizes) as ids FROM m_products_color WHERE product_id='" . $this->replace_sql($id) . "'";
    $c = $this->db->select($query);
    if ($c['ids'] != '') {
     $arr = explode(',', $c['ids']);
     if ($arr) {
      $ids = array_filter(array_unique($arr));
      if ($ids) {
       $query = "SELECT id, size_name FROM m_products_size WHERE id IN (" . implode(',', $ids) . ") ORDER BY id";
       $result = $this->db->freg($query, ['id'], 'size_name');
      }
     }
    }
   }
   return $result;
  }
  
  public function get_countries()
  {
   $query = "SELECT id, country_name, currency_id FROM m_countries WHERE publish='Y' ORDER BY id";
   return $this->db->freg_all($query, ['id'], ['country_name', 'currency_id']);
  }
  
  public function get_currencies()
  {
   $query = "SELECT id, CONCAT(currency_code, ' (', currency_name, ')') as currency FROM m_currencies WHERE publish='Y' ORDER BY id";
   return $this->db->freg($query, ['id'], 'currency');
  }
  
  public function update_exchange_rates()
  {
   $default = $this->db->select("SELECT c.currency_code as code FROM m_currencies c LEFT OUTER JOIN a_company a ON a.default_currency=c.id WHERE c.publish='Y'");
   $data = $this->db->selectall("SELECT id, currency_code FROM m_currencies WHERE publish='Y' ORDER BY id");
   if ($data) {
    foreach ($data as $k => $v) {
     $rate = $this->currency_conversion($default['code'], $v['currency_code']);
     if ($v['currency_code'] == $default['code']) {
      $rate = '1.00';
     }
     $this->db->update('m_currencies', ['exchange_rate' => $rate, 'update_date' => date('Y-m-d H:i:s')], ['id' => $v['id']]);
    }
   }
   
  }
  
  public function clear_cart()
  {
   $query = "DELETE FROM tmp_cart WHERE DATEDIFF('" . date('Y-m-d') . "', add_date) > 30";
   $this->db->query($query);
  }
  
  public function send_prompt_not()
  {
   $query = "SELECT not_key FROM a_nots WHERE is_prompt='Y' AND for_user='Y' AND not_date='" . date('Y-m-d') . "'";
   $not = $this->db->select($query);
   if ($not) {
    $query = "SELECT id, display_name FROM users WHERE publish='Y' AND verified='Y'";
    $users = $this->db->freg($query, ['id'], 'display_name');
    if ($users) {
     foreach ($users as $id => $name) {
      $this->send_email($not['not_key'], $id);
     }
    }
   }
   
  }
  
  public function get_product_images($ids = NULL)
  {
   $result = [];
   if (!is_null($ids)) {
    $query = "SELECT id, meta_value FROM files WHERE id IN ('" . $this->in_query($ids) . "')";
    $result = $this->db->freg($query, ['id'], 'meta_value');
   }
   return $result;
  }
  
  
 }
