<?php

 namespace admin\controllers;

 use Exception;
 use \resources\models\pagination as pagination;

 class m_brands extends controller {

  public $pagination = null, $sno = 0;

  public function __construct() {
   parent::__construct();
   $this->require_login('m-brands');
  }

  public function delete() {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('m_brands', array('id' => $this->get('id')));
  }

  public function insert() {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   if ($this->file('image', 'tmp_name') == '') {
    throw new Exception('Please upload the image.');
   }
   $id = $this->db->insert('m_brands', array(
       'title' => $this->post('title'),
       'publish' => $this->post('publish'),
       'add_date' => date('Y-m-d H:i:s')
   ));
   $this->save_file(array('image'), 'm_brands', $id);
  }

  public function update() {
   if (!$this->per_edit) {
    throw new Exception(_('You have no permission of update.'));
   }
   $this->validate_post_token(true);
   $id = $this->post('id');
   if ($id == '') {
    throw new Exception(_('Invalid ID for update!'));
   }
   $this->db->update('m_brands', array(
       'title' => $this->post('title'),
       'publish' => $this->post('publish')), array(
       'id' => $id
   ));
   $this->save_file(array('image'), 'm_brands', $id);
  }

  public function select() {
   $query = "SELECT p.*, f.meta_value as image FROM m_brands p LEFT OUTER JOIN files f ON p.image=f.id WHERE p.id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   $this->populate_post_data();
  }

  public function select_all() {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "";
   if ($this->get('keyword') != '') {
    $where .= " WHERE p.title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT p.*, f.meta_value as image FROM m_brands p LEFT OUTER JOIN files f ON p.image=f.id {$where} ORDER BY p.title";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('p.id');
   $this->sno = $this->pagination->get_sno();
  }

  function publish() {

   $this->db->update('m_brands', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));
  }

 }
 