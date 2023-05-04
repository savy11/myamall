<?php

 namespace admin\controllers;

 use Exception;
 use \resources\models\pagination as pagination;

 class v_quotes extends controller {

  public $pagination = null, $sno = 0;
  public $replies = array();

  public function __construct() {
   parent::__construct();
   $this->require_login('v-quotes');
  }

  public function delete() {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('v_quotes', array('id' => $this->get('id')));
  }


  public function select_all() {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "WHERE 1=1";
   if ($this->get('keyword') != '') {
    $where .= " AND display_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT * FROM v_quotes {$where} ORDER BY id DESC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

 }
 