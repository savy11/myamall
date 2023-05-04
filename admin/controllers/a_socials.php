<?php

 namespace admin\controllers;

 use Exception;
 use \resources\models\pagination as pagination;

 class a_socials extends controller {

  public $pagination = null, $sno = 0;

  public function __construct() {
   parent::__construct();
   $this->require_login('a-socials');
  }

  public function delete() {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('a_socials', array('id' => $this->get('id')));
  }

  public function insert() {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $id = $this->db->insert('a_socials', array(
       'title' => $this->post('title'),
       'url' => $this->post('url'),
       'class' => $this->post('class'),
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
   $this->db->update('a_socials', array(
       'title' => $this->post('title'),
       'url' => $this->post('url'),
       'class' => $this->post('class'),
       'publish' => $this->post('publish')), array(
       'id' => $id
   ));
  }

  public function select() {
   $query = "SELECT * FROM a_socials WHERE id='" . $this->replace_sql($this->get('id')) . "'";
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
    $where .= " AND title LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT * FROM a_socials {$where} ORDER BY id";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

  function publish() {

   $this->db->update('a_socials', array(
       'publish' => $this->post('publish')), array(
       'id' => $this->post('id')
   ));
  }

 }
 