<?php

 namespace admin\controllers;

 use Exception;
 use resources\models\pagination as pagination;

 class m_products_size extends controller
 {

  public $pagination = null, $sno = 0;

  public function __construct()
  {
   parent::__construct();
   $this->require_login('m-products-size');
  }

  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $id = $this->get('id');

   $this->db->delete('m_products_size', array('id' => $id));
  }

  public function insert()
  {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   if ($this->db->value_exists('m_products_size', 'size_name', $this->post('size_name'))) {
    throw new Exception(_('Size name already exists in our records.'));
   }
   $id = $this->db->insert('m_products_size', array('size_name' => $this->post('size_name'), 'publish' => $this->post('publish'), 'add_date' => date('Y-m-d H:i:s')));
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
   if ($this->db->value_exists('m_products_size', 'size_name', $this->post('size_name'), 'id', $id)) {
    throw new Exception(_('Size name already exists in our records.'));
   }
   $this->db->update('m_products_size', array('size_name' => $this->post('size_name'), 'publish' => $this->post('publish')), array('id' => $id));
  }

  public function select()
  {
   $query = "SELECT c.* FROM m_products_size c WHERE c.id='" . $this->replace_sql($this->get('id')) . "'";
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
    $where .= " WHERE size_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT * FROM m_products_size {$where} ORDER BY id ASC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

  function publish()
  {

   $this->db->update('m_products_size', array('publish' => $this->post('publish')), array('id' => $this->post('id')));
  }


 }
