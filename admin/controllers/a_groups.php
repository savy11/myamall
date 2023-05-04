<?php

 namespace admin\controllers;

 use Exception;
 use \resources\models\pagination as pagination;

 class a_groups extends controller {

  public $pagination = null, $sno = 0;

  public function __construct() {
   parent::__construct();
   $this->require_login('a-groups');
   if (!$this->is_ajax_call()) {
    if ($this->get('id') == 1 && $this->user['group_id'] != 1) {
     $this->not_found();
    }
    if (($this->per_add && $this->get('action') == 'add') || ($this->per_edit && $this->get('action') == 'edit')) {
     $this->list['parents'] = $this->db->freg("SELECT id, form_title FROM a_forms WHERE parent_id=0 " . ($this->get('action') == 'edit' ? " AND id!='" . $this->replace_sql($this->get('id')) . "'" : "") . " ORDER BY display_no, form_title", array("id"), "form_title");
    }
   }
  }

  public function delete() {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $query = "SELECT * FROM a_users WHERE group_id='" . $this->replace_sql($this->get('id')) . "'";
   if ($this->data = $this->db->select($query)) {
    throw new Exception(_('Oops, Group not deleted because already in use.'));
   }
   $this->db->delete('a_groups', array('id' => $this->get('id')));
  }

  public function insert() {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $id = $this->db->insert('a_groups', array(
       'group_name' => $this->post('group_name'),
       'publish' => $this->post('publish'),
       'add_date' => date('Y-m-d H:i:s')
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
   $this->db->update('a_groups', array(
       'group_name' => $this->post('group_name'),
       'publish' => $this->post('publish')), array(
       'id' => $id
   ));
  }

  public function select() {
   $query = "SELECT * FROM a_groups WHERE id='" . $this->replace_sql($this->get('id')) . "'";
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
    $where .= " AND group_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   if ($this->user['group_id'] != 1) {
    $where .= " AND id!=1";
   }
   $query = "SELECT * FROM a_groups {$where} ORDER BY id";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

  function publish() {

   $this->db->update('a_groups', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));
  }

 }
 