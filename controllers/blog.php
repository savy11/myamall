<?php
 
 namespace controllers;
 
 use Exception;
 use resources\models\pagination as pagination;
 
 class blog extends controller
 {
  
  public $url = '';
  public $comments = array(), $replies = array();
  
  public function __construct()
  {
   parent::__construct();
   $this->rows['load'] = 4;
   if (!$this->is_ajax_call()) {
    $this->url = $this->str_replace_first(domain_path, '', $this->server('REQUEST_URI'));
   }
  }
  
  public function blogs()
  {
   $where = "WHERE b.publish='Y'";
   if ($this->get('q') != '') {
    $where .= " AND (b.blog_title LIKE '%" . $this->replace_sql($this->get('q')) . "%' OR b.blog_tags LIKE '%" . $this->replace_sql($this->get('q')) . "%' OR b.blog_desc LIKE '%" . $this->replace_sql($this->get('q')) . "%' OR c.category_name LIKE '%" . $this->replace_sql($this->get('q')) . "%')";
   }
   
   
   if ($this->get('type') != '') {
    switch ($this->get('type')) {
     case 'category':
      $query = "SELECT * FROM m_blogs_cat WHERE page_url='" . $this->replace_sql($this->get('url')) . "'";
      if ($this->cms = $this->db->select($query)) {
       $where .= " AND b.category_id='" . $this->replace_sql($this->cms['id']) . "'";
      }
      break;
     case 'tag':
      $where .= " AND (b.blog_tags LIKE '%" . str_replace('-', ' ', $this->replace_sql($this->get('url'))) . "%')";
      $this->cms['page_title'] = $this->cms['page_heading'] = ucwords(str_replace('-', ' ', $this->get('url')));
      break;
     case 'archive':
      $where .= " AND YEAR(b.blog_date)='" . $this->replace_sql($this->get('year')) . "' AND LPAD(MONTH(b.blog_date),2,0)='" . $this->replace_sql($this->get('month')) . "'";
      $this->cms['page_title'] = $this->cms['page_heading'] = date('F', mktime(0, 0, 0, ($this->get('month') + 1), 0)) . ' ' . $this->get('year');
      break;
     default:
      $this->not_found();
      break;
    }
   }
   
   $query = "SELECT b.*, c.category_name, c.page_url as category_url, YEAR(b.blog_date) as year, LPAD(MONTH(b.blog_date),2,0) as month, f.meta_value as blog_image " . "FROM m_blogs b " . "LEFT OUTER JOIN m_blogs_cat c ON b.category_id=c.id " . "LEFT OUTER JOIN files f ON b.blog_image=f.id " . "{$where} ORDER BY b.blog_date DESC";
   $page = ($this->post('p') != '' ? $this->post('p') : 1);
   $this->pagination = new pagination($this, $this->db, $query, $this->rows['load'], $page);
   $this->data = $this->pagination->paging('b.id');
   $this->sno = $this->pagination->get_sno();
   $this->rows = array_merge($this->rows, array('count' => count($this->data), 'total' => $this->pagination->total_rows()));
  }
  
  function blog()
  {
   $query = "SELECT b.*, c.category_name, c.page_url as category_url, YEAR(b.blog_date) as year, LPAD(MONTH(b.blog_date),2,0) as month, f.meta_value as blog_image " . "FROM m_blogs b " . "LEFT OUTER JOIN m_blogs_cat c ON b.category_id=c.id " . "LEFT OUTER JOIN files f ON b.blog_image=f.id " . "WHERE b.id='" . $this->replace_sql($this->get('id')) . "' AND b.publish='Y'";
   if (!$this->cms = $this->db->select($query)) {
    $this->not_found();
   }
   
   $this->blog_url = $this->permalink('blog-detail', $this->cms);
   $this->meta = array('ogurl' => $this->blog_url, 'ogtype' => 'article', 'ogtitle' => $this->cms['blog_title'], 'ogdesc' => $this->cms['blog_desc'], 'ogimage' => $this->get_file($this->cms['blog_image']));
   
   $query = "SELECT * " . "FROM m_blogs_comment " . "WHERE publish='Y' AND verified='Y' AND deleted='N' AND blog_id='" . $this->replace_sql($this->cms['id']) . "' " . "ORDER BY add_date";
   if ($comments = $this->db->selectall($query)) {
    foreach ($comments as $k => $v) {
     if (!empty($v['parent_id'])) {
      $this->replies[$v['parent_id']][] = $v;
     } else {
      $this->comments[] = $v;
     }
    }
    $this->comments = array_reverse($this->comments);
   }
  }
  
  function populate_filters()
  {
   $this->list['recent'] = [];
   if ($this->get('id') != '') {
    $query = "SELECT b.*, f.meta_value as blog_image FROM m_blogs b LEFT OUTER JOIN files f ON b.blog_image=f.id WHERE b.id!='" . $this->replace_sql($this->get('id')) . "' AND b.publish='Y' ORDER BY RAND() DESC LIMIT 3";
    $this->list['recent'] = $this->db->selectall($query);
   }
   
   $query = "SELECT GROUP_CONCAT(blog_tags) as tags FROM m_blogs " . "WHERE publish='Y' " . "AND blog_tags!='' " . "ORDER BY RAND() " . "LIMIT 0,15";
   if ($data = $this->db->select($query)) {
    $this->list['tags'] = array_filter(array_unique(explode(',', $data['tags'])));
   }
   
   $query = "SELECT COUNT(b.id) as total, c.* " . "FROM m_blogs b " . "LEFT OUTER JOIN m_blogs_cat c ON b.category_id=c.id " . "WHERE b.publish='Y' " . "GROUP BY b.category_id " . "ORDER BY c.category_name";
   $this->list['cats'] = $this->db->selectall($query);
   
   $query = "SELECT blog_date, YEAR(blog_date) as year, LPAD(MONTH(blog_date),2,0) as month, COUNT(*) as total " . "FROM m_blogs " . "WHERE publish='Y' " . "GROUP BY year, month";
   $this->list['archives'] = $this->db->selectall($query);
  }
  
  function insert_comment()
  {
   $this->validate_post_token();
   if ($this->post('id') == '') {
    throw new Exception('Oops, something went wrong!');
   }
   $data = $this->db->select("SELECT id FROM m_blogs WHERE id='" . $this->replace_sql($this->post('id')) . "'");
   if (!$data) {
    throw new Exception('Sorry, blog not exists.');
   }
   if ($this->post('name') == '') {
    throw new Exception('Please enter your name.');
   }
   if ($this->post('email') == '') {
    throw new Exception('Please enter your email.');
   }
   if ($this->post('phone') == '') {
    throw new Exception('Please enter your phone number.');
   }
   if ($this->post('comment') == '') {
    throw new Exception('Please enter your comment.');
   }
   if ($this->post('captcha') == '') {
    throw new Exception('Please enter the security captcha.');
   }
   if ($this->post('captcha') != $this->session('captcha', 'blog')) {
    throw new Exception('Invalid security captcha, try again.');
   }
   if ($this->post('parent') > 0) {
    $p = $this->db->select("SELECT id FROM m_blogs_comment WHERE publish='Y' AND id='" . $this->replace_sql($this->post('parent')) . "'");
    if (!$p) {
     throw new Exception('Comment not found. Please reload the page.');
    }
    $_POST['parent_id'] = $p['id'];
   }
   
   $date = date('Y-m-d H:i:s');
   $id = $this->db->insert('m_blogs_comment', array('parent_id' => $this->post('parent_id'), 'blog_id' => $data['id'], 'name' => $this->post('name'), 'email' => $this->post('email'), 'phone' => $this->post('phone'), 'website' => $this->post('website'), 'comment' => $this->post('comment'), 'verified' => 'Y', 'ip' => $this->server('REMOTE_ADDR'), 'browser' => $this->get_browser(), 'os' => $this->get_os(), 'add_date' => $date));
  }
  
  function initials($str)
  {
   $ret = '';
   foreach (explode(' ', $str) as $word)
    $ret .= strtoupper($word[0]);
   return $ret;
  }
  
 }
