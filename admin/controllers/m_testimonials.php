<?php

namespace admin\controllers;

use Exception;
use \resources\models\pagination as pagination;

class m_testimonials extends controller {

 public $pagination = null, $sno = 0;

 public function __construct() {
  parent::__construct();
  $this->require_login('m-testimonials');
  $this->show_sort = true;
 }

 public function delete() {
  if (!$this->per_delete) {
   throw new Exception(_('You have no permission of delete.'));
  }
  $this->validate_delete_token(true);
  $id = $this->get('id');
  $this->db->delete('m_testimonials', array('id' => $id));
 }

 public function insert() {
  if (!$this->per_add) {
   throw new Exception(_('You have no permission of add.'));
  }
  $this->validate_post_token(true);
  $id = $this->db->insert('m_testimonials', array(
   'testi_name' => $this->post('testi_name'),
   'testi_desc' => $this->post('testi_desc'),
   'testi_designation' => $this->post('testi_designation'),
   'publish' => $this->post('publish'),
   'add_date' => date('Y-m-d H:i:s')
  ));
  $this->save_file(array('testi_image'), 'm_testimonials', $id);
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
  $this->db->update('m_testimonials', array(
   'testi_name' => $this->post('testi_name'),
   'testi_desc' => $this->post('testi_desc'),
   'testi_designation' => $this->post('testi_designation'),
   'publish' => $this->post('publish')), array(
   'id' => $id
  ));
  $this->save_file(array('testi_image'), 'm_testimonials', $id);
 }

 public function select() {
  $query = "SELECT t.*, f.meta_value as testi_image "
          . "FROM m_testimonials t "
          . "LEFT OUTER JOIN files f ON t.testi_image=f.id "
          . "WHERE t.id='" . $this->replace_sql($this->get('id')) . "'";
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
   $where .= " WHERE testi_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
  }
  $query = "SELECT * FROM m_testimonials {$where} ORDER BY display_no";
  $this->pagination = new pagination($this, $this->db, $query);
  $this->data = $this->pagination->paging('id');
  $this->sno = $this->pagination->get_sno();
 }
 
   function publish() {

   $this->db->update('m_testimonials', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));
  }

  function sort() {
   if ($this->post('SORT') == '') {
    throw new Exception('Oops, nothing to sort.');
   }
   $cnt = 1;
   foreach ($this->post('SORT') as $v) {
    $this->db->update("m_testimonials", array(
        'display_no' => $this->replace_sql($cnt)), array(
        'id' => $this->replace_sql($v)
    ));
    $cnt ++;
   }
  }

}
