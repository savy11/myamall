<?php
 
 namespace admin\controllers;
 
 use Exception;
 use resources\models\pagination as pagination;
 
 class users extends controller
 {
  
  public $pagination = null, $sno = 0;
  
  public function __construct()
  {
   parent::__construct();
   $this->require_login('users');
  }
  
  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $query = "SELECT * FROM orders WHERE user_id='" . $this->replace_sql($this->get('id')) . "'";
   if ($data = $this->db->select($query)) {
    throw new Exception('User can not be deleted! Its in use.');
   }
   $this->db->delete('users', array('id' => $this->get('id')));
  }
  
  public function update()
  {
   if (!$this->per_edit) {
    throw new \Exception(_('You have no permission of update.'));
   }
   $this->validate_post_token(true);
   $id = $this->post('id');
   if ($id == '') {
    throw new \Exception(_('Invalid ID for update!'));
   }
   $this->db->update('users', array(
    'first_name' => $this->post('first_name'),
    'last_name' => $this->post('last_name'),
    'display_name' => ($this->post('first_name') . ' ' . $this->post('last_name')),
    'email' => $this->post('email'),
    'password' => $this->post('password'),
    'dob' => $this->post('dob'),
    'mobile_no' => $this->post('mobile_no'),
    'publish' => $this->post('publish')
   ), array('id' => $id));
  }
  
  public function select()
  {
   $query = "SELECT * FROM users WHERE id='" . $this->replace_sql($this->get('id')) . "'";
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
    $where .= " WHERE display_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT * FROM users {$where} ORDER BY id DESC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }
  
  function publish()
  {
   
   $this->db->update('users', array('publish' => $this->post('publish')), array('id' => $this->post('id')));
  }
  
  function verify()
  {
   
   $this->db->update('users', array('verified' => $this->post('verified')), array('id' => $this->post('id')));
  }
  
 }
