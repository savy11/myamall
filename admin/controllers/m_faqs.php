<?php

namespace admin\controllers;

use Exception;
use \resources\models\pagination as pagination;

class m_faqs extends controller {

 public $pagination = null, $sno = 0;

 public function __construct() {
  parent::__construct();
  $this->require_login('m-faqs');
 }

 public function delete() {
  if (!$this->per_delete) {
   throw new Exception(_('You have no permission of delete.'));
  }
  $this->validate_delete_token(true);
  $this->db->delete('m_faqs', array('id' => $this->get('id')));
 }

 public function insert() {
  if (!$this->per_add) {
   throw new Exception(_('You have no permission of add.'));
  }
  $this->validate_post_token(true);
  $id = $this->db->insert('m_faqs', array(
   'question' => $this->post('question'),
   'answer' => $this->post('answer'),
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
  $this->db->update('m_faqs', array(
   'question' => $this->post('question'),
   'answer' => $this->post('answer'),
   'publish' => $this->post('publish')), array(
   'id' => $id
  ));
 }

 public function select() {
  $query = "SELECT * FROM m_faqs WHERE id='" . $this->replace_sql($this->get('id')) . "'";
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
   $where .= " AND question LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
  }
  $query = "SELECT * FROM m_faqs {$where} ORDER BY id DESC";
  $this->pagination = new pagination($this, $this->db, $query);
  $this->data = $this->pagination->paging('id');
  $this->sno = $this->pagination->get_sno();
 }
 
 function publish() {

   $this->db->update('m_faqs', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));
  }


}
