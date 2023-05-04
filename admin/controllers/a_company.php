<?php

 namespace admin\controllers;

 use Exception;
 use \resources\models\pagination as pagination;

 class a_company extends controller {

  public $pagination = null, $sno = 0;

  public function __construct() {
   parent::__construct();
   $this->require_login('a-company');
   if ($this->get('action') != ''){
    $this->list['currencies'] = $this->get_currencies();
   }
  }

  public function delete() {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('a_company', array('id' => $this->get('id')));
  }

  public function insert() {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $id = $this->db->insert('a_company', array(
       'name' => $this->post('name'),
       'default_currency' => $this->post('default_currency'),
       'address' => $this->post('address'),
       'phone_no' => $this->post('phone_no'),
       'fax_no' => $this->post('fax_no'),
       'default_contact' => explode(',', $this->post('phone_no'))[0],
       'email' => ($this->post('email') ? $this->json_encode($this->post('email')) : '')
   ));
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
   $this->db->update('a_company', array(
       'name' => $this->post('name'),
       'default_currency' => $this->post('default_currency'),
       'address' => $this->post('address'),
       'phone_no' => $this->post('phone_no'),
       'fax_no' => $this->post('fax_no'),
       'default_contact' => explode(',', $this->post('phone_no'))[0],
       'email' => ($this->post('email') ? $this->json_encode($this->post('email')) : '')), array(
       'id' => $id
   ));
  }

  public function select() {
   $query = "SELECT * FROM a_company WHERE id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   if ($this->data['email']) {
    $this->data['email'] = $this->json_decode($this->data['email']);
   }
   $this->populate_post_data();
  }

  public function select_all() {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "WHERE 1=1";
   if ($this->get('keyword') != '') {
    $where .= " AND name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT * FROM a_company {$where} ORDER BY id";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

 }
 