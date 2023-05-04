<?php
 
 namespace admin\controllers;
 
 use Exception;
 use resources\models\pagination as pagination;
 
 class m_products extends controller
 {
  
  public function __construct()
  {
   parent::__construct();
   $this->require_login('m-products');
   $this->show_search = true;
   if ($this->get('action') != '') {
    $this->list['categories'] = $this->get_products_cat();
    $this->list['sizes'] = $this->get_sizes();
   }
  }
  
  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('m_products', array('id' => $this->get('id')));
  }
  
  public function insert()
  {
   try {
    if (!$this->per_add) {
     throw new Exception(_('you have no permission to add'));
    }
    if (!$this->session('products')) {
     throw new Exception('Please upload product images');
    }
    $this->validate_post_token(true);
    $this->db->trans_start();
    $id = $this->db->insert('m_products', array(
     'category_id' => $this->post('category_id'),
     'product_title' => $this->post('product_title'),
     'product_code' => $this->post('product_code'),
     'best_offer' => $this->post('best_offer'),
     'discount' => $this->post('discount'),
     'in_stock' => $this->post('in_stock'),
     'total_sold' => $this->post('total_sold'),
     'brand_id' => $this->post('brand_id'),
     'special_price' => $this->post('special_price'),
     'basic_price' => $this->post('basic_price'),
     'product_desc' => $this->post('product_desc'),
     'page_title' => $this->post('page_title'),
     'page_heading' => $this->post('page_heading'),
     'page_url' => $this->post('page_url'),
     'meta_keywords' => $this->post('meta_keywords'),
     'meta_desc' => $this->post('meta_desc'),
     'publish' => $this->post('publish'),
     'add_date' => date('Y-m-d H:i:s')
    ));
    
    if ($this->post('color')) {
     foreach ($this->post('color') as $k => $v) {
      if ($this->varv('title', $v) != '' || $this->varv('basic_price', $v) != '') {
       $color_id = $this->db->insert('m_products_color', array(
        'product_id' => $id,
        'title' => $v['title'],
        'sizes' => ($v['sizes'] ? implode(',', $v['sizes']) : ''),
        'basic_price' => $v['basic_price']
       ));
       $this->save_file(array('image'), 'm_products_color', $color_id);
      }
     }
    }
    
    if ($ids = $this->save_session_files('products', 'm_products', $id)) {
     $this->db->update('m_products', array('product_image' => $ids), array('id' => $id));
    }
    
    $this->db->trans_commit();
   } catch (Exception $ex) {
    throw new Exception($ex->getMessage());
   }
  }
  
  public function update()
  {
   try {
    if (!$this->per_edit) {
     throw new Exception(_('you have no permission to update'));
    }
    $this->validate_post_token(true);
    $id = $this->post('id');
    if ($id == '') {
     throw new Exception(_('Invalid ID for update!'));
    }
    if ($this->session('products') && !count($this->session('products')) > 0) {
     throw new Exception('Please upload product images');
    }
    $this->db->trans_start();
    
    $this->db->update('m_products', array(
     'category_id' => $this->post('category_id'),
     'product_title' => $this->post('product_title'),
     'product_code' => $this->post('product_code'),
     'best_offer' => $this->post('best_offer'),
     'discount' => $this->post('discount'),
     'brand_id' => $this->post('brand_id'),
     'in_stock' => $this->post('in_stock'),
     'total_sold' => $this->post('total_sold'),
     'special_price' => $this->post('special_price'),
     'basic_price' => $this->post('basic_price'),
     'product_desc' => $this->post('product_desc'),
     'page_title' => $this->post('page_title'),
     'page_heading' => $this->post('page_heading'),
     'page_url' => $this->post('page_url'),
     'meta_keywords' => $this->post('meta_keywords'),
     'meta_desc' => $this->post('meta_desc'),
     'publish' => $this->post('publish'),
     'add_date' => date('Y-m-d H:i:s')
    ), array(
     'id' => $id
    ));
    
    if ($this->post('color_del_ids') != '') {
     $query = "DELETE FROM m_products_color WHERE id IN(" . $this->post('color_del_ids') . ")";
     $this->db->query($query);
    }
    
    $images = [];
    
    if ($this->post('color')) {
     foreach ($this->post('color') as $k => $v) {
      if ($this->varv('id', $v) > 0) {
       $color_id = $v['id'];
       $this->db->update('m_products_color', array(
        'product_id' => $id,
        'title' => $v['title'],
        'sizes' => ($v['sizes'] ? implode(',', $v['sizes']) : ''),
        'basic_price' => $v['basic_price']
       ), array(
        'id' => $this->varv('id', $v)
       ));
       $images[$k]['id'] = $v['id'];
      } else {
       if ($this->varv('title', $v) != '' || $this->varv('basic_price', $v) != '')
        $color_id = $this->db->insert('m_products_color', array(
         'product_id' => $id,
         'title' => $v['title'],
         'sizes' => ($v['sizes'] ? implode(',', $v['sizes']) : ''),
         'basic_price' => $v['basic_price']
        ));
       $images[$k]['id'] = $color_id;
      }
     }
     $this->save_grid_file(array('color'), 'm_products_color', $images);
    }
    if ($ids = $this->save_session_files('products', 'm_products', $id)) {
     $this->db->update('m_products', array('product_image' => $ids), array('id' => $id));
    }
    $this->db->trans_commit();
   } catch (Exception $ex) {
    throw new Exception($ex->getMessage());
   }
  }
  
  public function select()
  {
   $query = "SELECT p.* FROM m_products p WHERE p.id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   $query = "SELECT * FROM files WHERE type_id='" . $this->data['id'] . "' AND type='products' AND table_name='m_products' ORDER BY id";
   $this->data['products'] = $this->db->selectall($query);
   
   $query = "SELECT c.*, f.meta_value as image FROM m_products_color c "
    . "LEFT OUTER JOIN files f ON c.image=f.id "
    . "WHERE c.product_id='" . $this->data['id'] . "' ORDER BY c.id";
   $this->data['color'] = $this->db->selectall($query);
   
   $this->populate_post_data();
  }
  
  public function select_all()
  {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "WHERE 1=1";
   if ($this->get('keyword') != '') {
    $where .= " AND p.product_title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT p.*, c.category_name, b.title as brand_name, CONCAT_WS(' &raquo; ', pp.parent_name, c.category_name) as category_name FROM m_products p " . "LEFT OUTER JOIN m_products_cat c ON p.category_id=c.id " . "LEFT OUTER JOIN m_products_parent pp ON c.parent_id=pp.id " . "LEFT OUTER JOIN m_brands b ON p.brand_id=b.id " . "{$where} " . "ORDER BY p.id DESC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('p.id');
   $this->sno = $this->pagination->get_sno();
  }
  
  public function publish()
  {
   
   $this->db->update('m_products', array('publish' => $this->post('publish')), array('id' => $this->post('id')));
  }
  
  public function update_actions()
  {
   if ($this->post('field') == '') {
    throw new Exception('Oops, something went wrong.');
   }
   $field = $this->post('field');
   
   $this->db->update('m_products', array($field => $this->post($field)), array('id' => $this->post('id')));
  }
  
  public function get_brands()
  {
   $query = "SELECT id, title FROM m_brands WHERE publish='Y' ORDER BY id";
   $this->db->freg($query, ['id'], 'title');
  }
  
  public function save_grid_file($type, $table, $ids = [])
  {
   $grid = [];
   if (is_array($type)) {
    foreach ($type as $t) {
     if ($this->file($t)) {
      $file = $this->file($t);
      if ($file) {
       foreach ($file as $fk => $fv) {
        foreach ($fv as $tk => $tv) {
         $grid[$tk][$fk] = $tv['image'];
        }
       }
      }
      unset($_FILES[$t]);
     }
    }
   } else {
    unset($_FILES[$type]);
   }
   unset($_FILES['file']);
   $type = 'image';
   if ($grid) {
    $grid = array_merge_recursive($grid, $ids);
    
    foreach ($grid as $t) {
     if (!$t['error'] > 0) {
      $file = $t;
      $info = @pathinfo($file['name']);
      
      $ext = strtolower($info['extension']);
      if (in_array($ext, $this->allowed_file_formats) !== false) {
       if ($ext == 'jpeg') {
        $ext = 'jpg';
       }
       $this->update_uploaded_file($type, $table, $t['id']);
       
       $filename = date('YmdHis') . '_' . rand(0000000, 9999999) . '.' . $ext;
       $path = $this->create_file_path(false, $filename);
       move_uploaded_file($file['tmp_name'], $path);
       
       $im = new \resources\controllers\image_resize;
       list($w, $h) = $im->get_wh(500, $path, 2000, 2000);
       $im->square_resize($path, $path, $w, $h);
       
       $meta = array('folder' => $this->create_file_path(true), 'filename' => $filename, 'size' => $file['size']);
       $file_id = $this->db->insert('files', array(
        'type_id' => $t['id'],
        'table_name' => $table,
        'type' => $type,
        'name' => $file['name'],
        'meta_value' => $this->json_encode($meta),
        'add_date' => date('Y-m-d H:i:s')
       ));
       $this->db->update($table, array($type => $file_id), array('id' => $t['id']));
      }
     }
    }
   }
   
  }
  
  
 }
