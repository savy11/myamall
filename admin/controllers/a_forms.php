<?php

namespace admin\controllers;

use \resources\models\pagination as pagination;

class a_forms extends controller {

 public $pagination = null, $sno = 0;

 public function __construct() {
  parent::__construct();
  $this->require_login('a-forms');
  if (!$this->is_ajax_call()) {
   if (($this->per_add && $this->get('action') == 'add') || ($this->per_edit && $this->get('action') == 'edit')) {
    $this->list['parents'] = $this->db->freg("SELECT id, form_title FROM a_forms WHERE parent_id=0 AND form_code='#' " . ($this->get('action') == 'edit' ? " AND id!='" . $this->replace_sql($this->get('id')) . "'" : "") . " ORDER BY display_no, form_title", array("id"), "form_title");
   }
  }
 }

 public function delete() {
  if (!$this->per_delete) {
   throw new \Exception(_('You have no permission of delete.'));
  }
  $this->validate_delete_token(true);
  $this->db->delete('a_forms', array('id' => $this->get('id')));
 }

 public function insert() {
  if (!$this->per_add) {
   throw new \Exception(_('You have no permission of add.'));
  }
  $this->validate_post_token(true);
  $id = $this->db->insert('a_forms', array(
   'parent_id' => $this->post('parent_id'),
   'form_title' => $this->post('form_title'),
   'form_code' => $this->post('form_code'),
   'seo' => $this->post('seo'),
   'display_icon' => $this->post('display_icon'),
   'display_no' => $this->post('display_no'),
   'per_level' => $this->post('per_level')
  ));

  $this->db->insert('a_forms_per', $this->permissions($id));
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
  $this->db->update('a_forms', array(
   'parent_id' => $this->post('parent_id'),
   'form_title' => $this->post('form_title'),
   'form_code' => $this->post('form_code'),
   'seo' => $this->post('seo'),
   'display_icon' => $this->post('display_icon'),
   'display_no' => $this->post('display_no'),
   'per_level' => $this->post('per_level')
          ), array(
   'id' => $id
  ));
  $this->db->update('a_forms_per', $this->permissions($id), array('form_id' => $id, 'group_id' => 1));
 }

 public function permissions($id) {
  $columns = array(
   'form_id' => $id,
   'group_id' => 1,
   'per_add' => 0,
   'per_edit' => 0,
   'per_delete' => 0,
   'per_view' => 0
  );
  if ($pers = $this->per_levels[$this->post('per_level')]) {
   foreach ($pers as $v) {
    $columns['per_' . strtolower($v)] = 1;
   }
  }
  return $columns;
 }

 public function select() {
  $query = "SELECT * FROM a_forms WHERE id='" . $this->replace_sql($this->get('id')) . "'";
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
   $where .= " WHERE i.form_title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%' OR f.form_title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
  }
  $query = "SELECT i.*, if(f.display_no IS NULL, 0, f.display_no) as parent_display_no, f.form_title as parent_title "
          . "FROM a_forms i "
          . "LEFT OUTER JOIN a_forms f ON i.parent_id=f.id "
          . "{$where} "
          . "ORDER BY CONCAT_WS('.', f.display_no, i.display_no)";
  $this->pagination = new pagination($this, $this->db, $query);
  $this->data = $this->pagination->paging('i.id');
  $this->sno = $this->pagination->get_sno();
 }

}
