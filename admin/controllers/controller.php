<?php
 
 namespace admin\controllers;
 
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sessions.php';
 
 use Exception;
 use resources\controllers\controller as main;
 
 class controller extends main
 {
  
  public $lock = true, $checkpoint = false, $expiry_mins = 3;
  public $per_add = false, $per_edit = false, $per_delete = false, $show_sort = false, $show_buttons = true, $show_search = false;
  public $user = array(), $menus = array(), $pers = array(), $nots = array(), $page = array(), $modal = array();
  public $tmp_path = '', $pagination = null, $sno = 0, $style = '', $script = '', $actions = '', $actions_multi = '';
  public $sending_types = array(1 => 'CC Mail', 2 => 'BCC Mail', 3 => 'Prevent Mail');
  public $per_levels = array('1' => array('View' => 'View',), '2' => array('View' => 'View', 'Edit' => 'Edit',), '3' => array('View' => 'View', 'Add' => 'Add',), '4' => array('View' => 'View', 'Delete' => 'Delete',), '5' => array('View' => 'View', 'Add' => 'Add', 'Edit' => 'Edit',), '6' => array('View' => 'View', 'Add' => 'Add', 'Delete' => 'Delete',), '7' => array('View' => 'View', 'Edit' => 'Edit', 'Delete' => 'Delete',), '8' => array('View' => 'View', 'Add' => 'Add', 'Edit' => 'Edit', 'Delete' => 'Delete',),);
  
  public function __construct($main_db = false, $need_db = true)
  {
   parent::__construct($main_db, $need_db);
   $this->path = 'admin/';
   if (!$this->is_ajax_call()) {
    $this->auto_login();
   }
  }
  
  public function auto_login()
  {
   if ($this->cookie('user') && $this->session('user', 'login') == '') {
    try {
     $query = "SELECT id, publish FROM a_users WHERE id='" . $this->cookie('user', 'id') . "'";
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
     $_SESSION['user']['checkpoint'] = false;
     $_SESSION['user']['lock'] = false;
     
     $this->gen_cookie('user', array('id' => $data['id']));
     $this->update_log($this->session('user', 'id'), 'auto_login');
     
     $this->login_checkpoint();
     $this->redirect($this->server('REQUEST_URI'));
    } catch (Exception $ex) {
     $this->session_msg($ex->getMessage(), 'danger', 'Login');
     $this->redirecting('login');
    }
   }
  }
  
  public function update_log($id, $type)
  {
   $columns = array('user_id' => $id, 'session_id' => session_id(), 'type' => $type, 'ip' => $this->session('REMOTE_ADDR'), 'browser' => $this->get_browser(), 'os' => $this->get_os(), 'log_date' => date('Y-m-d H:i:s'));
   $this->db->insert('a_logs', $columns);
  }
  
  public function login_checkpoint()
  {
   if ($this->checkpoint == true) {
    if ($this->cookie('validate_pc_' . $this->session('user', 'id'), 'status') == false) {
     $code_expire = 0;
     $query = "SELECT id, user_id, code, expiry_date FROM a_codes WHERE session_id='" . session_id() . "' AND user_id='" . $this->session('user', 'id') . "' AND type='login' AND status='N'";
     if ($data = $this->db->select($query)) {
      $interval = (strtotime($data['expiry_date']) - time());
      $minutes = round($interval / 60);
      if ($minutes <= 0) {
       $code_expire = true;
      }
     }
     
     $date = date('Y-m-d H:i:s');
     $expiry_date = date('Y-m-d H:i:s', strtotime($date . ' + ' . $this->expiry_mins . ' mins'));
     $query = "";
     if ($code_expire == true || !$data) {
      $code = $this->gen_code(6, false);
      if ($code_expire) {
       $this->db->update('a_codes', array('status' => 'E'), array('id' => $data['id']));
      }
      $this->db->insert('a_codes', array('user_id' => $this->session('user', 'id'), 'session_id' => session_id(), 'code' => $code, 'expiry_date' => $expiry_date, 'type' => 'login', 'ip' => $this->server('REMOTE_ADDR'), 'browser' => $this->get_browser(), 'os' => $this->get_os(), 'add_date' => $date, 'update_date' => $date));
     } else {
      $this->db->update('a_codes', array('expiry_date' => $expiry_date, 'update_date' => $date), array('id' => $data['id']));
     }
     $this->send_email('a_login_checkpoint', $this->session('user', 'id'));
     $this->redirecting('checkpoint');
    } else {
     $this->gen_cookie('validate_pc_' . $this->session('user', 'id'), array('status' => true));
     $_SESSION['user']['checkpoint'] = true;
    }
   }
  }
  
  public function redirecting($type = '', $dt = array())
  {
   $this->redirect($this->permalink($type, $dt));
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
  
  public function permalink($type = '', $data = array(), $app = false)
  {
   if ($app) {
    $url = app_url;
   } else {
    $url = admin_url;
   }
   switch ($type) {
    case 'blog-detail':
     $url = app_url . 'blog' . ($data ? ds . $data['page_url'] . ds . $data['blog_id'] : '');
     break;
    default:
     $url .= $type;
     break;
   }
   return $url;
  }
  
  public function session_msg($message, $type, $title = '')
  {
   if ($title == '') {
    $title = $this->page['name'];
   }
   $_SESSION['er']['title'] = $title;
   $_SESSION['er']['message'] = $message;
   $_SESSION['er']['type'] = $type;
  }
  
  public function return_ref($r = false)
  {
   $url = $this->permalink();
   if ($this->session('ref', $this->page['page_url'])) {
    $url = $this->session('ref', $this->page['page_url']);
   }
   if (!$r) {
    $this->redirect($url);
   } else {
    return $url;
   }
  }
  
  public function already_login()
  {
   if ($this->session('user', 'id') != '') {
    if ($this->session('user', 'login') == true) {
     $this->redirecting();
    }
   }
  }
  
  public function require_login($murl = false)
  {
   if ($this->session('user', 'login') == '') {
    $this->redirecting('login');
   }
   $this->auth();
   $this->tmp_path = 'u' . $this->user['id'];
   
   if ($this->session('user', 'login') == true) {
    if ($murl !== false) {
     if ($this->varv($murl, $this->pers)) {
      $this->per_add = $this->pers[$murl]['A'];
      $this->per_edit = $this->pers[$murl]['E'];
      $this->per_delete = $this->pers[$murl]['D'];
      $this->set_page_title($murl);
     } else {
      $this->not_found();
     }
     $this->check_page_per();
    }
    if ($this->lock) {
     if ($this->session('user', 'lock') == true) {
      $this->redirecting('lock');
     }
    }
    if ($this->checkpoint) {
     if ($this->session('user', 'checkpoint') == false) {
      $this->redirecting('checkpoint');
     }
    }
   } else {
    if (strpos($this->server('REQUEST_URI'), '/index.php') !== true) {
     $_SESSION['REF_URL'] = str_replace('/ajax', '', $this->server('REQUEST_URI'));
    }
    $this->redirect();
   }
  }
  
  public function auth()
  {
   $query = "SELECT u.*, g.group_name, u.image as image_id, f.meta_value as image FROM a_users u LEFT OUTER JOIN a_groups g ON u.group_id=g.id LEFT OUTER JOIN files f ON u.image=f.id WHERE u.id='" . $this->session('user', 'id') . "'";
   $this->user = $this->db->select($query);
   
   $query = "SELECT f.id, f.form_code, f.form_title, f.display_icon, f.per_level, p.per_view, p.per_add, p.per_edit, p.per_delete " . "FROM a_forms f " . "LEFT OUTER JOIN a_forms_per p ON f.id=p.form_id AND p.group_id='" . $this->user['group_id'] . "' " . "WHERE f.parent_id=0 AND f.form_code!='' " . "ORDER BY f.display_no";
   if ($forms = $this->db->selectall($query)) {
    foreach ($forms as $v) {
     $query = "SELECT f.id, f.form_code, f.form_title, f.seo, f.display_icon, p.per_view, p.per_add, p.per_edit, p.per_delete " . "FROM a_forms_per p " . "INNER JOIN a_forms f ON p.form_id=f.id AND p.group_id='" . $this->user['group_id'] . "' " . "WHERE f.parent_id='" . $v['id'] . "' AND p.per_view='1' " . "ORDER BY f.display_no";
     if (!$data = $this->db->selectall($query)) {
      if ($v['form_code'] != '#') {
       $this->menus[$v['form_code']] = array('name' => $v['form_title'], 'form_code' => $v['form_code'], 'icon' => $v['display_icon']);
       $this->pers[$v['form_code']] = array('A' => $v['per_add'], 'E' => $v['per_edit'], 'D' => $v['per_delete'], 'V' => $v['per_view']);
      }
     }
     if ($data) {
      $this->menus[$v['form_title']]['icon'] = $v['display_icon'];
      foreach ($data as $r) {
       $this->menus[$v['form_title']]['data'][$r['form_code']] = array('name' => $r['form_title'], 'icon' => $r['display_icon'], 'seo' => $r['seo']);
       $this->pers[$r['form_code']] = array('A' => $r['per_add'], 'E' => $r['per_edit'], 'D' => $r['per_delete'], 'V' => $r['per_view']);
      }
     }
    }
   }
   
   $query = "SELECT p.*, e.not_key FROM a_nots_per p LEFT OUTER JOIN a_nots e ON p.not_id=e.id WHERE p.group_id='" . $this->user['group_id'] . "'";
   if ($nots = $this->db->selectall($query)) {
    foreach ($nots as $k => $v) {
     $this->nots[$v['not_key']] = $v;
    }
   }
  }
  
  public function set_page_title($code)
  {
   if ($this->menus) {
    if ($this->varv($code, $this->menus)) {
     $v = $this->varv($code, $this->menus);
     $this->page['name'] = $v['name'];
     $this->page['page_url'] = $code;
     $this->page['url'] = $this->permalink($code);
     $this->page['icon'] = $v['icon'];
    } else {
     foreach ($this->menus as $k => $v) {
      if ($this->varv('data', $v, $code)) {
       $this->page['page_url'] = $code;
       $this->page['url'] = $this->permalink($code);
       $this->page['icon'] = $this->varv('data', $v, array($code, 'icon'));
       $this->page['name'] = $this->varv('data', $v, array($code, 'name'));
       $this->page['parent'] = $k;
       $this->page['parent_icon'] = $v['icon'];
      }
     }
    }
   }
  }
  
  public function not_found()
  {
   header('HTTP/1.1 404 Not Found');
   include_once(admin_path . 'inc' . ds . '404.php');
   exit();
  }
  
  public function check_page_per()
  {
   if ($this->get('action') == 'add' && !$this->per_add) {
    $this->not_found();
   }
   if ($this->get('action') == 'edit' && !$this->per_edit) {
    $this->not_found();
   }
   if ($this->get('action') == 'delete' && !$this->per_delete) {
    $this->not_found();
   }
   if (in_array($this->get('action'), array('view', 'add', 'edit', 'delete', 'sort', 'clone')) === false) {
    if (!$this->is_ajax_call()) {
     $this->set_cur_url();
    }
   }
  }
  
  public function set_cur_url()
  {
   $_SESSION['ref'][$this->page['page_url']] = request_scheme . $this->server('HTTP_HOST') . $this->server('REQUEST_URI');
  }
  
  public function check_per($type = '')
  {
   if ($type != '') {
    $per = 'per_' . $type;
    return $this->$per;
   }
   $i = 0;
   if ($this->per_edit) {
    $i++;
   }
   if ($this->per_delete) {
    $i++;
   }
   return $i;
  }
  
  public function unauthorized($page = false)
  {
   if ($page) {
    $this->redirecting('unauthorized');
   } else {
    include_once(admin_path . 'inc' . ds . '401.php');
    exit();
   }
  }
  
  public function get_action_url($type = '', $id = '', $token = '', $url = '')
  {
   if ($url == '') {
    $url = $this->page['url'];
   }
   return $url . '/' . $type . ($id != '' ? '/' . $id : '') . ($token != '' ? '/' . $token : '');
  }
  
  public function get_view($name, $type = '', $data = array())
  {
   $string = (include admin_path . 'views' . ds . $name . '.php');
   return $string;
  }
  
  function get_icons($path, $p = 's7')
  {
   $pattern = '/\.(' . $p . '-(?:\w+(?:-)?)+):before/';
   $content = file_get_contents(admin_path . $path);
   preg_match_all($pattern, $content, $matchs, PREG_SET_ORDER);
   $icons = array();
   foreach ($matchs as $v) {
    $icons[$v[1]] = ucwords(str_replace('-', ' ', str_replace($p . '-', '', $v[1])));
   }
   ksort($icons);
   return $icons;
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
      $str .= '<option value="' . $k . '"' . (strpos(',' . $sel . ',', ',' . $k . ',') !== false ? ' selected' : '') . ' ' . implode(' ', $values) . '>' . $v[$ele] . '</option>';
     }
    } else {
     foreach ($list as $k => $v) {
      $str .= '<option value="' . $k . '"' . (strpos(',' . $sel . ',', ',' . $k . ',') !== false ? 'selected' : '') . '>' . $v . '</option>';
     }
    }
   }
   return $str;
  }
  
  public function display_pass($pass)
  {
   $pass = $this->decrypt($pass);
   $len = strlen($pass);
   return str_repeat('*', $len);
  }
  
  
  public function galleries($type = '', $data = array(), $default = 0)
  {
   include admin_path . 'views' . ds . 'galleries.php';
  }
  
  public function files_layout($type, $file_id = 0)
  {
   if ($file_id) {
    $query = "SELECT id, name, meta_value FROM files WHERE id='" . $this->replace_sql($file_id) . "' AND deleted='N'";
    if ($data = $this->db->select($query)) {
     if ($data['meta_value'] = $this->json_decode($data['meta_value'])) {
      foreach ($data['meta_value'] as $k => $v) {
       $data[$k] = $v;
      }
      unset($data['meta_value']);
     }
    }
   }
   include admin_path . 'views' . ds . 'files.php';
  }
  
  public function load_view($name = '', $data = array(), $ret = false)
  {
   if (is_array($data) && count($data) > 0) {
    $r = '';
    foreach ($data as $k => $v) {
     $r .= "\$" . $k . "='" . $v . "';";
    }
    eval($r);
   }
   if ($ret) {
    ob_start();
    include admin_path . 'views' . ds . $name . '.php';
    return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
   }
   include admin_path . 'views' . ds . $name . '.php';
  }
  
  public function get_blog_cats($id = '')
  {
   $query = "SELECT id, category_name FROM m_blogs_cat WHERE publish='Y' ORDER BY id";
   return $this->db->freg($query, array('id'), 'category_name');
  }
  
  public function get_products_parent()
  {
   $query = "SELECT id, parent_name FROM m_products_parent WHERE publish='Y' ORDER BY id";
   return $this->db->freg($query, array('id'), 'parent_name');
  }
  
  public function get_products_cat()
  {
   $query = "SELECT c.id, CONCAT_WS(' &raquo; ', p.parent_name, c.category_name) as name FROM m_products_cat c " . "LEFT OUTER JOIN m_products_parent p ON c.parent_id=p.id " . "WHERE c.publish='Y' " . "ORDER BY c.id";
   return $this->db->freg($query, array('id'), 'name');
  }
  
  public function get_cats()
  {
   $query = "SELECT id, category_name FROM m_categories WHERE publish='Y' ORDER BY id";
   return $this->db->freg($query, array('id'), 'category_name');
  }
  
  public function get_colors()
  {
   $query = "SELECT id, color_name FROM m_products_color WHERE publish='Y' ORDER BY id";
   return $this->db->freg($query, array('id'), 'color_name');
  }
  
  public function get_sizes()
  {
   $query = "SELECT id, size_name FROM m_products_size WHERE publish='Y' ORDER BY id";
   return $this->db->freg($query, array('id'), 'size_name');
  }
 
  public function get_currencies()
  {
   $query = "SELECT id, CONCAT(currency_code, ' (', currency_name, ')') as currency_name FROM m_currencies WHERE publish='Y' ORDER BY id";
   return $this->db->freg($query, array('id'), 'currency_name');
  }
  
 }
