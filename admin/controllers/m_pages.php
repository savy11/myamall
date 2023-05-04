<?php
 
 namespace admin\controllers;
 
 use Exception;
 use \resources\models\pagination as pagination;
 
 class m_pages extends controller
 {
  
  public $pagination = null, $sno = 0;
  
  public function __construct()
  {
   parent::__construct();
   $this->require_login('m-pages');
   /*        $this->unlink_files($this->tmp_path(), $this->session('image', 'filename'));
           $this->unlink_files($this->tmp_path(), $this->session('header_image', 'filename'));
           unset($_SESSION['image']);
           unset($_SESSION['header_image']);*/
  }
  
  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('m_pages', array('id' => $this->get('id')));
   //$this->delete_file($data['image']);
  }
  
  public function insert()
  {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $id = $this->db->insert('m_pages', array(
    'page_title' => $this->post('page_title'),
    'page_heading' => $this->post('page_heading'),
    'page_url' => $this->url_string($this->post('page_url')),
    'page_desc' => $this->post('page_desc'),
    'meta_keywords' => $this->post('meta_keywords'),
    'meta_desc' => $this->post('meta_desc'),
    'quick_link' => $this->post('quick_link'),
    'publish' => $this->post('publish'),
    'add_date' => date('Y-m-d H:i:s')
   ));
   $this->save_file(array('image', 'header_image'), 'm_pages', $id);
  }
  
  public function update()
  {
   if (!$this->per_edit) {
    throw new Exception(_('You have no permission of update.'));
   }
   $this->validate_post_token(true);
   $id = $this->post('id');
   if ($id == '') {
    throw new Exception(_('Invalid ID for update!'));
   }
   $this->db->update('m_pages', array(
    'page_title' => $this->post('page_title'),
    'page_heading' => $this->post('page_heading'),
    'page_url' => $this->url_string($this->post('page_url')),
    'page_desc' => $this->post('page_desc'),
    'meta_keywords' => $this->post('meta_keywords'),
    'meta_desc' => $this->post('meta_desc'),
    'quick_link' => $this->post('quick_link'),
    'publish' => $this->post('publish')), array(
    'id' => $id
   ));
   $this->save_file(array('image', 'header_image'), 'm_pages', $id);
  }
  
  public function select()
  {
   $query = "SELECT p.*, f.meta_value as image,h.meta_value as header_image FROM m_pages p LEFT OUTER JOIN files f ON p.image=f.id LEFT OUTER JOIN files h ON p.header_image=h.id WHERE p.id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   $this->populate_post_data();
  }
  
  public function select_all()
  {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "";
   if ($this->get('keyword') != '') {
    $where .= " WHERE page_title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT p.*, f.meta_value as image, f1.meta_value as header_image FROM m_pages p "
    . "LEFT OUTER JOIN files f ON p.image=f.id "
    . "LEFT OUTER JOIN files f1 ON p.header_image=f1.id "
    . "{$where} ORDER BY page_title";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }
  
  public function publish()
  {
   
   $this->db->update('m_pages', array(
    'publish' => $this->post('publish')), array(
    'id' => $this->post('id')
   ));
  }
  
  public function update_field()
  {
   
   if ($this->post('field') == '') {
    throw new Exception('Oops, somethimng went wrong');
   }
   $field = $this->post('field');
   
   $this->db->update('m_pages', array(
    $field => $this->post($field)), array(
    'id' => $this->post('id')
   ));
  }
  
  
 }
