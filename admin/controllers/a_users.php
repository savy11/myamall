<?php

 namespace admin\controllers;

 use \resources\models\pagination as pagination;

 class a_users extends controller {

  public $pagination = null, $sno = 0;

  public function __construct() {
   parent::__construct();
   $this->require_login('a-users');
   if (!$this->is_ajax_call()) {
    if ($this->get('id') == 1 && $this->user['group_id'] != 1) {
     $this->not_found();
    }
    if (($this->per_add && $this->get('action') == 'add') || ($this->per_edit && $this->get('action') == 'edit')) {
     $this->list['groups'] = $this->db->freg("SELECT id, group_name FROM a_groups WHERE publish='Y' " . ($this->user['group_id'] != 1 ? " AND id!=1" : "") . " ORDER BY group_name DESC", array("id"), "group_name");
    }
   }
  }

  public function delete() {
   if (!$this->per_delete) {
    throw new \Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('a_users', array('id' => $this->get('id')));
  }

  public function insert() {
   if (!$this->per_add) {
    throw new \Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $this->db->insert('a_users', array(
       'first_name' => $this->post('first_name'),
       'last_name' => $this->post('last_name'),
       'display_name' => ($this->post('first_name') . ' ' . $this->post('last_name')),
       'username' => $this->post('username'),
       'email' => $this->post('email'),
       'password' => $this->post('password'),
       'mobile_no' => $this->post('mobile_no'),
       'group_id' => $this->post('group_id'),
       'publish' => $this->post('publish'),
       'add_date' => date('Y-m-d H:i:s')
   ));
  }

  public function update() {
   if (!$this->per_edit) {
    throw new \Exception(_('You have no permission of update.'));
   }
   $this->validate_post_token(true);
   $id = $this->post('id');
   if ($id == '') {
    throw new \Exception(_('Invalid ID for update!'));
   }
   $this->db->update('a_users', array(
       'first_name' => $this->post('first_name'),
       'last_name' => $this->post('last_name'),
       'display_name' => ($this->post('first_name') . ' ' . $this->post('last_name')),
       'username' => $this->post('username'),
       'email' => $this->post('email'),
       'password' => $this->post('password'),
       'mobile_no' => $this->post('mobile_no'),
       'group_id' => $this->post('group_id'),
       'publish' => $this->post('publish')
           ), array(
       'id' => $id
   ));
  }

  public function select() {
   $query = "SELECT * FROM a_users WHERE id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   $this->populate_post_data();
  }

  public function select_all() {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "WHERE 1=1";
   if ($this->get('keyword') != '') {
    $where .= " AND u.display_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   if ($this->user['group_id'] != 1) {
    $where .= " AND u.group_id!=1";
   }
   $query = "SELECT u.*, g.group_name FROM a_users u LEFT OUTER JOIN a_groups g ON u.group_id=g.id {$where} ORDER BY u.id";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('u.id');
   $this->sno = $this->pagination->get_sno();
  }

  function publish() {

   $this->db->update('a_users', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));
  }

 }
 