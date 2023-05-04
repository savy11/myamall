<?php
 
 namespace admin\controllers;
 
 use Exception;
 use \resources\models\pagination as pagination;
 
 class m_sale extends controller
 {
  
  public $pagination = null, $sno = 0;
  
  public function __construct()
  {
   parent::__construct();
   $this->require_login('m-sale');
   if ($this->get('action') != '') {
    $this->list['products'] = $this->get_products();
   }
  }
  
  public function get_products()
  {
   $query = "SELECT id, CONCAT(product_title, ' (', basic_price, ')') as title FROM m_products WHERE publish='Y' ORDER BY product_title";
   return $this->db->freg($query, ['id'], 'title');
  }
  
  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('m_sale_products', array('sale_id' => $this->get('id')));
   $this->db->delete('m_sale', array('id' => $this->get('id')));
  }
  
  public function insert()
  {
   try {
    if (!$this->per_add) {
     throw new Exception(_('You have no permission of add.'));
    }
    $this->validate_post_token(true);
    $this->db->trans_start();
    $id = $this->db->insert('m_sale', array(
     'sale_title' => $this->post('sale_title'),
     'start_date' => $this->post('start_date'),
     'end_date' => $this->post('end_date'),
     'publish' => $this->post('publish'),
     'add_date' => date('Y-m-d H:i:s')
    ));
    
    $sp = [];
    if ($this->post('sp')) {
     foreach ($this->post('sp') as $k => $v) {
      if ($this->varv('product_id', $v) != '' || $this->varv('discount_price', $v) != '') {
       $sp[] = array(
        'sale_id' => $id,
        'product_id' => $v['product_id'],
        'discount_price' => $v['discount_price']
       );
      }
     }
    }
    if ($sp) {
     $this->db->batch('insert', 'm_sale_products', $sp);
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
     throw new Exception(_('You have no permission of update.'));
    }
    $this->validate_post_token(true);
    $id = $this->post('id');
    if ($id == '') {
     throw new Exception(_('Invalid ID for update!'));
    }
    $this->db->update('m_sale', array(
     'sale_title' => $this->post('sale_title'),
     'start_date' => $this->post('start_date'),
     'end_date' => $this->post('end_date'),
     'publish' => $this->post('publish')), array(
     'id' => $id
    ));
	
	if ($this->post('sp_del_ids') != '') {
     $query = "DELETE FROM m_sale_products WHERE id IN(" . $this->post('sp_del_ids') . ")";
     $this->db->query($query);
    }
	
    $sp = $sp_where = [];
    if ($this->post('sp')) {
     foreach ($this->post('sp') as $k => $v) {
      if ($this->varv('product_id', $v) != '' || $this->varv('discount_price', $v) != '') {
       if ($this->varv('id', $v) != '') {
        $sp['update'][] = array(
         'sale_id' => $id,
         'product_id' => $v['product_id'],
         'discount_price' => $v['discount_price']
        );
        $sp_where[] = ['id' => $v['id']];
       } else {
        $sp['insert'][] = array(
         'sale_id' => $id,
         'product_id' => $v['product_id'],
         'discount_price' => $v['discount_price']
        );
       }
      }
     }
    }
    if ($sp) {
     foreach ($sp as $k => $v) {
      $this->db->batch($k, 'm_sale_products', $v, $k == 'update' ? $sp_where : '');
     }
    }
    
   } catch (Exception $ex) {
    throw new Exception($ex->getMessage());
   }
  }
  
  public function select()
  {
   $query = "SELECT * FROM m_sale WHERE id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   $query = "SELECT * FROM m_sale_products WHERE sale_id='" . $this->replace_sql($this->data['id']) . "'";
   $this->data['sp'] = $this->db->selectall($query);
   $this->populate_post_data();
  }
  
  public function select_all()
  {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "WHERE 1=1";
   if ($this->get('keyword') != '') {
    $where .= " AND sale_title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT * FROM m_sale {$where} ORDER BY id DESC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }
  
  function publish()
  {
   
   $this->db->update('m_sale', array(
    'publish' => $this->post('publish')), array(
    'id' => $this->post('id')
   ));
  }
  
  
 }
