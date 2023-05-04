<?php
 
 namespace controllers;
 
 use Exception;
 
 class index extends controller
 {
  
  public function __construct()
  {
   parent::__construct();
   $this->cms_page('index');
  }
  
  function get_data()
  {
   $query = "SELECT s.slider_title, s.slider_punchline, s.slider_url, f.meta_value as slider_image "
    . "FROM m_sliders s "
    . "LEFT OUTER JOIN files f ON s.slider_image=f.id "
    . "WHERE s.publish='Y' "
    . "ORDER BY s.display_no";
   $this->list['sliders'] = $this->db->selectall($query);
   
   $sale = [];
   $query = "SELECT * FROM m_sale WHERE publish='Y' AND '" . date('Y-m-d') . "' BETWEEN start_date and end_date";
   if ($sale = $this->db->selectall($query)) {
    foreach ($sale as $k => $v) {
     $query = "SELECT p.id, p.category_id, p.product_title, p.discount, p.in_stock, p.basic_price as special_price, p.total_sold, sp.discount_price as basic_price, p.product_image, p.page_url, c.page_url as cat_url, pp.page_url as parent_url FROM m_sale_products sp "
      . "LEFT OUTER JOIN m_products p ON sp.product_id=p.id "
      . "LEFT OUTER JOIN m_products_cat c ON p.category_id=c.id "
      . "LEFT OUTER JOIN m_products_parent pp ON c.parent_id=pp.id "
      . "WHERE sp.sale_id='" . $this->replace_sql($v['id']) . "' "
      . "ORDER BY sp.id";
     $sale[$k]['products'] = $this->db->selectall($query);
    }
   }
   $this->list['sale'] = $sale;
   
   $query = "SELECT b.title, f.meta_value as image FROM m_brands b "
    . "LEFT OUTER JOIN files f ON b.image=f.id "
    . "WHERE b.publish='Y'";
   $this->list['brands'] = $this->db->selectall($query);
   
   $query = "SELECT c.id, CONCAT_WS(' &raquo; ', p.parent_name, c.category_name) as category_name, f.meta_value as category_image, c.page_url, p.page_url as parent_url FROM m_products_cat c "
    . "LEFT OUTER JOIN files f ON c.category_image=f.id "
    . "LEFT OUTER JOIN m_products_parent p ON c.parent_id=p.id "
    . "WHERE c.publish='Y' AND c.category_image!='0' ORDER BY c.category_name";
   $this->list['categories'] = $this->db->freg_all($query, ['id'], ['category_name', 'category_image', 'page_url', 'parent_url']);
   
   $query = "SELECT c.id, c.category_name, c.page_url, p.page_url as parent_url FROM m_products_cat c LEFT OUTER JOIN m_products_parent p ON c.parent_id=p.id WHERE c.publish='Y' ORDER BY c.category_name";
   $this->list['cats'] = $this->db->freg_all($query, ['id'], ['category_name', 'page_url', 'parent_url']);
   
   $this->list['products'] = [];
   if ($this->list['cats']) {
    foreach ($this->list['cats'] as $id => $cat) {
     $query = "SELECT "
      . "p.id, p.category_id, p.product_title, p.discount, p.in_stock, p.special_price, p.basic_price, p.total_sold, p.product_image, p.page_url, '{$cat['page_url']}' as cat_url, '{$cat['parent_url']}' as parent_url "
      . "FROM m_products p WHERE p.publish='Y' AND p.category_id='" . $this->replace_sql($id) . "' "
      . "GROUP BY p.id "
      . "ORDER BY RAND() "
      . "LIMIT 8";
     if ($data = $this->db->selectall($query)) {
      $this->list['products'][$id] = $data;
     }
    }
   }
  }
  
  
 }
