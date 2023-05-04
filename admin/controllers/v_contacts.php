<?php

 namespace admin\controllers;

 use Exception;
 use \resources\models\pagination as pagination;

 class v_contacts extends controller {

  public $pagination = null, $sno = 0;
  public $replies = array();

  public function __construct() {
   parent::__construct();
   $this->require_login('v-contacts');
  }

  public function delete() {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('v_contacts', array('id' => $this->get('id')));
  }

  public function insert() {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $get_id = $this->get('id');
   $date = date('Y-m-d H:i:s');
   $data = $this->db->select("SELECT email FROM v_contacts WHERE id='" . $this->replace_sql($get_id) . "'");
   $id = $this->db->insert('v_contacts', array(
       'parent_id' => $get_id,
       'display_name' => 'Admin',
       'email' => $this->replace_sql($data['email']),
       'message' => $this->post('reply'),
       'type' => '1',
       'ip' => $this->server('REMOTE_ADDR'),
       'browser' => $this->get_browser(),
       'os' => $this->get_os(),
       'add_date' => $date
   ));
   $this->send_email("contact_reply", $id);
  }

  public function select() {
   $query = "SELECT * FROM v_contacts WHERE id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   $this->populate_post_data();

   $query = "SELECT * FROM v_contacts WHERE parent_id='" . $this->data['id'] . "' ORDER BY add_date";
   $this->replies = $this->db->selectall($query);
  }

  public function select_all() {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "WHERE parent_id=0";
   if ($this->get('keyword') != '') {
    $where .= " WHERE display_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT * FROM v_contacts {$where} ORDER BY id DESC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('id');
   $this->sno = $this->pagination->get_sno();
  }

 }
 