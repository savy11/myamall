<?php
 
 namespace admin\controllers;
 
 use Exception;
 use \resources\models\pagination as pagination;
 
 class a_nots extends controller
 {
  
  public $pagination = null, $sno = 0;
  
  public function __construct()
  {
   parent::__construct();
   $this->require_login('a-nots');
  }
  
  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('a_nots', array('id' => $this->get('id')));
  }
  
  public function insert()
  {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $id = $this->db->insert('a_nots', array(
    'not_key' => $this->post('not_key'),
    'not_title' => $this->post('not_title'),
    'not_subject' => $this->post('not_subject'),
    'not_desc' => $this->post('not_desc'),
    'for_admin' => $this->post('for_admin'),
    'for_user' => $this->post('for_user'),
    'is_prompt' => $this->post('is_prompt'),
    'not_date' => $this->post('not_date')
   ));
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
   $this->db->update('a_nots', array(
    'not_key' => $this->post('not_key'),
    'not_title' => $this->post('not_title'),
    'not_subject' => $this->post('not_subject'),
    'not_desc' => $this->post('not_desc'),
    'for_admin' => $this->post('for_admin'),
    'for_user' => $this->post('for_user'),
    'is_prompt' => $this->post('is_prompt'),
    'not_date' => $this->post('not_date')), array(
    'id' => $id
   ));
  }
  
  public function select()
  {
   $query = "SELECT * FROM a_nots WHERE id='" . $this->replace_sql($this->get('id')) . "'";
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
    $where .= " AND (not_key LIKE '%" . $this->replace_sql($this->get('keyword')) . "%' OR not_title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%' OR not_subject LIKE '%" . $this->replace_sql($this->get('keyword')) . "%')";
   }
   $query = "SELECT * FROM a_nots {$where} ORDER BY id";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }
  
  function per_update($type)
  {
   if ($type == "for_admin") {
    $this->db->update('a_nots', array(
     'for_admin' => $this->post('for_admin')), array(
     'id' => $this->post('id')
    ));
   }
   if ($type == "for_user") {
    $this->db->update('a_nots', array(
     'for_user' => $this->post('for_user')), array(
     'id' => $this->post('id')
    ));
   }
  }
  
 }
 