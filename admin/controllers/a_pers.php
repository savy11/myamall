<?php

namespace admin\controllers;

use Exception;
use \resources\models\pagination as pagination;

class a_pers extends controller {

 public $pagination = null, $sno = 0;

 public function __construct() {
  parent::__construct();
  $this->require_login('a-pers');
  if (!$this->is_ajax_call()) {
   if ($this->get('id') == 1 && $this->user['group_id'] != 1) {
    $this->not_found();
   }
  }
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
  $this->db->delete('a_forms_per', array('group_id' => $id));
  if (is_array($this->post('form_per')) && count($this->post('form_per')) > 0) {
   foreach ($this->post('form_per') as $form_id => $pers) {
    if ($pers['view'] == 1) {
     $pers['add'] = ($this->varv('add', $pers) ? $pers['add'] : 0);
     $pers['edit'] = ($this->varv('edit', $pers) ? $pers['edit'] : 0);
     $pers['delete'] = ($this->varv('delete', $pers) ? $pers['delete'] : 0);

     $this->db->insert('a_forms_per', array(
      'form_id' => $form_id,
      'group_id' => $id,
      'per_add' => ($this->varv('add', $pers) ? $pers['add'] : 0),
      'per_edit' => ($this->varv('edit', $pers) ? $pers['edit'] : 0),
      'per_delete' => ($this->varv('delete', $pers) ? $pers['delete'] : 0),
      'per_view' => $pers['view'],
     ));
    }
   }
  }

  $this->db->delete('a_nots_per', array('group_id' => $id));
  if (is_array($this->post('not_per')) && count($this->post('not_per')) > 0) {
   foreach ($this->post('not_per') as $not_id => $per) {
    $this->db->insert('a_nots_per', array(
     'not_id' => $not_id,
     'group_id' => $id,
    ));
   }
  }
 }

 public function select() {
  $id = $this->replace_sql($this->get('id'));
  $query = "SELECT * FROM a_groups WHERE id='" . $id . "' AND publish='Y'";
  if (!$this->data = $this->db->select($query)) {
   $this->not_found();
  }

  // Main
  $query = "SELECT f.id, f.form_title, f.form_code, f.per_level, p.per_add, p.per_edit, p.per_delete, p.per_view "
          . "FROM a_forms f "
          . "LEFT OUTER JOIN a_forms_per p ON (f.id=p.form_id AND p.group_id='" . $id . "') "
          . "WHERE f.parent_id='0' AND f.form_code!='' ORDER BY f.display_no";
  $this->data['main'] = $this->db->selectall($query);

  // Sub 
  $where = "WHERE f.parent_id!=0";
  if ($this->user['group_id'] != 1) {
   $where .= " AND p.per_view=1";
  }
  $query = "SELECT f.id, f.parent_id, f.form_title, f.form_code, f.per_level, p.per_add, p.per_edit, p.per_delete, p.per_view "
          . "FROM a_forms f "
          . "LEFT OUTER JOIN a_forms_per p ON (f.id=p.form_id AND p.group_id='" . $id . "') "
          . "{$where} "
          . "ORDER BY f.display_no";
  if ($sub = $this->db->selectall($query)) {
   foreach ($sub as $v) {
    $this->data['sub'][$v['parent_id']][] = $v;
   }
  }

  // nots
  $query = "SELECT e.id, e.not_title, e.not_key, p.id as per_id FROM a_nots e LEFT OUTER JOIN a_nots_per p ON (e.id=p.not_id and p.group_id='" . $id . "') WHERE e.for_admin='Y' ORDER BY e.not_key";
  $this->data['nots'] = $this->db->selectall($query);
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

}
