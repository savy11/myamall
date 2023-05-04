<?php

 namespace admin\controllers;

 use Exception;
 use resources\models\pagination as pagination;

 class m_products_cat extends controller
 {

  public $pagination = null, $sno = 0;

  public function __construct()
  {
   parent::__construct();
   $this->require_login('m-products-cat');
   $this->list['parents'] = $this->get_products_parent();
  }

  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $id = $this->get('id');

   $this->db->delete('m_products_cat', array('id' => $id));
  }

  public function insert()
  {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);

   if ($this->db->select("SELECT id FROM m_products_cat WHERE category_name='" . $this->post('category_name') . "' AND parent_id='" . $this->post('parent_id') . "'")) {
    throw new Exception(_('Category name already exists in our records.'));
   }
   $id = $this->db->insert('m_products_cat', array(
    'parent_id' => $this->post('parent_id'),
    'category_name' => $this->post('category_name'),
    'publish' => $this->post('publish'),
    'page_title' => $this->post('page_title'),
    'page_heading' => $this->post('page_heading'),
    'page_url' => $this->post('page_url'),
    'meta_keywords' => $this->post('meta_keywords'),
    'meta_desc' => $this->post('meta_desc'),
    'add_date' => date('Y-m-d H:i:s')
   ));
   $this->save_file(array('category_image'), 'm_products_cat', $id);
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
   if ($this->db->select("SELECT id FROM m_products_cat WHERE category_name='" . $this->post('category_name') . "' AND parent_id='" . $this->post('parent_id') . "' AND id!='".$id."'")) {
    throw new Exception(_('Category name already exists in our records.'));
   }
   $this->db->update('m_products_cat', array(
    'parent_id' => $this->post('parent_id'),
    'category_name' => $this->post('category_name'),
    'publish' => $this->post('publish'),
    'page_title' => $this->post('page_title'),
    'page_heading' => $this->post('page_heading'),
    'page_url' => $this->post('page_url'),
    'meta_keywords' => $this->post('meta_keywords'),
    'meta_desc' => $this->post('meta_desc')), array(
    'id' => $id
   ));
   $this->save_file(array('category_image'), 'm_products_cat', $id);
  }

  public function select()
  {
   $query = "SELECT c.*,  f.meta_value as category_image FROM m_products_cat c "

    . "LEFT OUTER JOIN files f ON c.category_image=f.id "
    . "WHERE c.id='" . $this->replace_sql($this->get('id')) . "'";
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
    $where .= " WHERE c.category_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT c.*, CONCAT_WS(' &raquo; ', p.parent_name, c.category_name) as category_name FROM m_products_cat c "
    . "LEFT OUTER JOIN m_products_parent p ON c.parent_id=p.id "
    . "{$where} ORDER BY c.id ASC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

  function publish()
  {

   $this->db->update('m_products_cat', array(
    'publish' => $this->post('publish')), array(
    'id' => $this->post('id')
   ));
  }


 }
