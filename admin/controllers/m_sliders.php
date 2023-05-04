<?php

 namespace admin\controllers;

 use Exception;
 use \resources\models\pagination as pagination;

 class m_sliders extends controller {

  public $pagination = null, $sno = 0;

  public function __construct() {
   parent::__construct();
   $this->require_login('m-sliders');
   $this->show_sort = true;
  }

  public function delete() {
   if (!$this->per_delete) {
    throw new Exception(_('you have no permission to delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('m_sliders', array('id' => $this->get('id')));
   //$this->delete_file($data['image']);
  }

  public function insert() {
   if (!$this->per_add) {
    throw new Exception(_('you have no permission to add'));
   }
   $this->validate_post_token(true);
   $id = $this->db->insert('m_sliders', array(
       'slider_title' => $this->post('slider_title'),
       'slider_punchline' => $this->post('slider_punchline'),
       'slider_url' => $this->post('slider_url'),
       'publish' => $this->post('publish'),
       'add_date' => date('Y-m-d H:i:s')
   ));
   $this->save_file(array('slider_image'), 'm_sliders', $id);
  }

  public function update() {
   if (!$this->per_edit) {
    throw new Exception(_('you have no permission to update'));
   }
   $this->validate_post_token(true);
   $id = $this->post('id');
   if ($id == '') {
    throw new Exception(_('empty user id'));
   }
   $this->db->update('m_sliders', array(
       'slider_title' => $this->post('slider_title'),
       'slider_punchline' => $this->post('slider_punchline'),
       'slider_url' => $this->post('slider_url'),
       'publish' => $this->post('publish')), array(
       'id' => $id
   ));
   $this->save_file(array('slider_image'), 'm_sliders', $id);
  }

  public function select() {
   $query = "SELECT s.*, f.meta_value as slider_image FROM m_sliders s LEFT OUTER JOIN files f ON s.slider_image=f.id WHERE s.id='" . $this->replace_sql($this->get('id')) . "'";
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
    $where .= "WHERE slider_title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT s.*, f.meta_value as slider_image FROM m_sliders s LEFT OUTER JOIN files f ON s.slider_image=f.id {$where} ORDER BY display_no";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

  function sort() {
   if ($this->post('SORT') == '') {
    throw new Exception('Oops, nothing to sort.');
   }
   $cnt = 1;
   foreach ($this->post('SORT') as $v) {
    $this->db->update("m_sliders", array(
        'display_no' => $this->replace_sql($cnt)), array(
        'id' => $this->replace_sql($v)
    ));
    $cnt ++;
   }
  }

  function publish() {

   $this->db->update('m_sliders', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));
  }

 }
 