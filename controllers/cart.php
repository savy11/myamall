<?php
 
 namespace controllers;
 
 use Exception;
 
 class cart extends controller
 {
  
  public function __construct()
  {
   parent::__construct();
   $this->cms_page('cart');
  }
  
  public function add_to_cart()
  {
   $data = ['size' => '', 'color' => ''];
   $this->check_stock($this->post('id'));
   if ($this->post('size') != '') {
    $data['size_id'] = $this->post('size');
   }
   if ($this->post('color') != '') {
    $data['color_id'] = $this->post('color');
   }
   if ($this->post('sale') != '') {
    $data['sale_id'] = $this->post('sale');
   }
   
   $data = array_merge($data, $this->post());
   if ($this->session('cart')) {
    $where = "WHERE id IN(" . implode(',', $this->session('cart')) . ")";
    if ($this->post('id')) {
     $where .= " AND product_id='" . $this->post('id') . "'";
    }
    if ($this->post('size')) {
     $where .= " AND size_id='" . $this->post('size') . "'";
    }
    if ($this->post('color')) {
     $where .= " AND color_id='" . $this->post('color') . "'";
    }
    if ($this->post('sale')) {
     $where .= " AND sale_id='" . $this->post('sale') . "'";
    } else {
     $where .= " AND sale_id='0'";
    }
    $query = "SELECT * FROM tmp_cart {$where}";
    if ($dt = $this->db->select($query)) {
     $qty = $this->post('qty') ? $this->post('qty') : 1;
     $this->db->update('tmp_cart', array('qty' => $dt['qty'] + $qty), array('id' => $dt['id']));
     $id = $dt['id'];
    } else {
     $id = $this->insert_cart($data);
    }
   } else {
    $id = $this->insert_cart($data);
   }
   return $id;
  }
  
  public function insert_cart($data = array())
  {
   $id = $this->db->insert('tmp_cart', array(
    'product_id' => $this->varv('id', $data),
    'product_title' => $this->varv('title', $data),
    'qty' => ($this->varv('qty', $data) ? $this->varv('qty', $data) : '1'),
    'sale_id' => $this->varv('sale', $data),
    'size_id' => $this->varv('size', $data),
    'color_id' => $this->varv('color', $data),
    'add_date' => date('Y-m-d H:i:s')));
   $_SESSION['cart'][$id] = $id;
   $this->gen_cookie('cart', $_SESSION['cart']);
   return $id;
  }
  
  public function update_cart()
  {
   $id = $this->post('id');
   if ($this->post('qty') <= 0) {
    $this->remove_cart();
   } else {
    $this->db->update('tmp_cart', array('qty' => $this->post('qty')), array('id' => $id));
    $this->tmp_cart();
   }
  }
  
  public function remove_cart()
  {
   $id = $this->post('id');
   $this->db->delete('tmp_cart', array('id' => $id));
   unset($_SESSION['cart'][$id], $this->cart[$id], $_SESSION['checkout'][$id]);
   $this->gen_cookie('cart', $_SESSION['cart']);
  }
  
  public function add_checkout($ids = NULL)
  {
   if (!is_null($ids) && !empty($ids)) {
    if (stripos($ids, ',') != false) {
     $data = array_filter(array_unique(explode(',', $ids)));
    } else {
     $data = [$ids];
    }
    if ($data && is_array($data)) {
     foreach ($data as $v) {
      if (!isset($_SESSION['checkout'][$v])) {
       $_SESSION['checkout'][$v] = $v;
      }
     }
    }
   }
  }
  
 }
