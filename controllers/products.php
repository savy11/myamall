<?php
 
 namespace controllers;
 
 use Exception;
 use resources\models\pagination as pagination;
 
 class products extends controller
 {
  public $url;
  public $order = ['rand' => 'Relevance', 'lowtohigh' => 'Price: Lowest first', 'hightolow' => 'Price: Highest 
  first', 'ascname' => 'Product Name: A to Z', 'descname' => 'Product Name: Z to A'];
  public $per_page = ['12' => '12', '15' => '15', '18' => '18', '21' => '21', '24' => '24', '27' => '27 ', '30' => '30'];
  
  public function __construct()
  {
   parent::__construct();
   $this->rows['load'] = 12;
   $this->cms_page('products');
   if (!$this->is_ajax_call()) {
    $this->url = parse_url($this->current_url());
    if (!$this->session('view')) {
     $_SESSION['view'] = 'grid';
    }
   }
   
  }
  
  public function products()
  {
   $where = "WHERE p.publish='Y'";
   if ($this->post_get('q')) {
    $this->filter_array('q');
    $where .= " AND p.product_title LIKE '%" . $this->replace_sql($this->filter['q']) . "%'";
   }
   
   if ($this->post_get('view') != '') {
    $_SESSION['view'] = $this->post_get('view');
   }
   
   if ($this->post_get('type') != '') {
    if ($parent = $this->db->select("SELECT id, page_title, page_heading, page_url FROM m_products_parent WHERE page_url='" . $this->replace_sql($this->post_get('type')) . "'")) {
     $where .= " AND c.parent_id='" . $this->replace_sql($parent['id']) . "'";
     $this->cms = $parent;
     if ($this->post_get('url') != '') {
      if ($cat = $this->db->select("SELECT id, page_title, page_heading, page_url FROM m_products_cat WHERE page_url='" . $this->replace_sql($this->post_get('url')) . "' AND parent_id='" . $this->replace_sql($parent['id']) . "'")) {
       $where .= " AND p.category_id='" . $this->replace_sql($cat['id']) . "'";
       $this->cms = $cat;
      }
     }
    }
   }
   
   if ($this->post_get('category')) {
    $this->filter_array('category');
    $where .= " AND p.category_id IN('" . $this->replace_sql($this->in_query($this->filter['category'])) . "')";
   }
   
   if ($this->post_get('per_page')) {
    $this->filter_array('per_page');
    $this->rows['load'] = $this->post_get('per_page');
   }
   
   
   $order = "ORDER BY p.id DESC";
   if ($this->post_get('sort')) {
    $this->filter_array('sort');
    switch ($this->post_get('sort')) {
     case 'rand':
      $order = "ORDER BY RAND()";
      break;
     case 'lowtohigh';
      $order = "ORDER BY p.basic_price";
      break;
     case 'hightolow';
      $order = "ORDER BY p.basic_price DESC";
      break;
     case 'ascname';
      $order = "ORDER BY p.product_title";
      break;
     case 'descname';
      $order = "ORDER BY p.product_title DESC";
      break;
    }
   }
   
   $query = "SELECT p.id, p.discount, p.in_stock, p.product_title, p.basic_price, p.special_price, p.total_sold, p.product_desc, p.product_image, p.page_url, p1.page_url as parent_url, c.page_url as cat_url FROM m_products p "
    . "LEFT OUTER JOIN m_products_cat c ON p.category_id=c.id "
    . "LEFT OUTER JOIN m_products_parent p1 ON c.parent_id=p1.id "
    . "{$where} "
    . "GROUP BY p.id "
    . "{$order}";
   $this->pagination = new pagination($this, $this->db, $query, $this->rows['load']);
   $this->data = $this->pagination->paging('p.id');
   $this->sno = $this->pagination->get_sno();
   $this->rows = array_merge($this->rows, array('count' => count($this->data), 'total' => $this->pagination->total_rows()));
  }
  
  public function product()
  {
   $query = "SELECT p.*, c.page_url as cat_url, pc.page_url as parent_url FROM m_products p "
    . "LEFT OUTER JOIN m_products_cat c ON p.category_id=c.id "
    . "LEFT OUTER JOIN m_products_parent pc ON c.parent_id=pc.id "
    . "WHERE p.id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->cms = $this->db->select($query)) {
    $this->not_found();
   }
   $url = $this->permalink('product-detail', $this->cms);
   if ($this->current_url() != $url) {
    $this->redirect($url);
   }
   $query = "SELECT sp.sale_id, s.sale_title, sp.discount_price as sale_price FROM m_sale s LEFT OUTER JOIN m_sale_products sp ON sp.sale_id=s.id AND sp.product_id='" . $this->cms['id'] . "' WHERE s.publish='Y'  AND '" . date('Y-m-d') . "' BETWEEN start_date and end_date";
   if ($sale = $this->db->select($query)) {
    $this->cms = array_replace_recursive($this->cms, $sale);
   }
   
   $query = "SELECT * FROM files WHERE type_id='" . $this->cms['id'] . "' AND type='products' AND table_name='m_products' ORDER BY id";
   $this->cms['files'] = $this->db->selectall($query);
   
   $this->list['colors'] = $this->get_colors($this->cms['id']);
   $this->list['sizes'] = $this->get_sizes($this->cms['id']);
  }
  
  public function sidebar()
  {
   $query = "SELECT page_url as id, parent_name as name FROM m_products_parent WHERE publish='Y' ORDER BY parent_name";
   $this->list['parents'] = $this->db->freg($query, ['id'], 'name');
   
   $query = "SELECT c.page_url as id, c.category_name as name, p.page_url as parent_url, COUNT(pc.id) as total FROM m_products pc " . "LEFT OUTER JOIN m_products_cat c ON pc.category_id=c.id " . "LEFT OUTER JOIN m_products_parent p ON c.parent_id=p.id " . "WHERE c.publish='Y' " . "GROUP BY p.page_url, c.page_url " . "ORDER BY p.id ";
   $this->list['categories'] = $this->db->freg_all($query, ['parent_url']);
   
   $query = "SELECT p.id, p.product_title, p.product_desc, p.basic_price, p.special_price, p.page_url, p1.page_url as parent_url, c.page_url as cat_url, f.meta_value as image FROM m_products p "
    . "LEFT OUTER JOIN files as f ON p.product_image=f.id "
    . "LEFT OUTER JOIN m_products_cat c ON p.category_id=c.id "
    . "LEFT OUTER JOIN m_products_parent p1 ON c.parent_id=p1.id "
    . "WHERE p.publish='Y' AND best_offer='Y' "
    . "GROUP BY p.id "
    . "ORDER BY RAND() "
    . "LIMIT 10";
   $this->list['best_offer'] = $this->db->selectall($query);
  }
  
 }
