<?php
 
 namespace admin\controllers;
 
 use Exception;
 use \resources\models\pagination as pagination;
 
 class m_currencies extends controller
 {
  
  public $pagination = null, $sno = 0;
  
  public function __construct()
  {
   parent::__construct();
   $this->require_login('m-currencies');
  }
  
  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $exists = $this->db->get_value('m_countries', 'country_name', "currency_id='" . $this->get('id') . "'");
   if ($exists) {
    throw new Exception('Currency is in used by ' . $exists . '.It cannot be deleted.');
   }
   $this->db->delete('m_currencies', array('id' => $this->get('id')));
  }
  
  public function insert()
  {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $id = $this->db->insert('m_currencies', array(
    'currency_name' => $this->post('currency_name'),
    'currency_code' => $this->post('currency_code'),
    'exchange_rate' => $this->post('exchange_rate'),
    'publish' => $this->post('publish'),
    'add_date' => date('Y-m-d H:i:s')
   ));
  }
  
  public function update()
  {
   if (!$this->per_edit) {
    throw new Exception(_('You have no permission of update.'));
   }
   $this->validate_post_token(true);
   $id = $this->post('id');
   if ($id == '') {
    throw new Exception(_('Invalid ID for update!'));
   }
   $this->db->update('m_currencies', array(
    'currency_name' => $this->post('currency_name'),
    'currency_code' => $this->post('currency_code'),
    'exchange_rate' => $this->post('exchange_rate'),
    'publish' => $this->post('publish'),
    'update_date' => date('Y-m-d H:i:s')), array(
    'id' => $id
   ));
  }
  
  public function select()
  {
   $query = "SELECT p.* FROM m_currencies p WHERE p.id='" . $this->replace_sql($this->get('id')) . "'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }
   $this->populate_post_data();
  }
  
  public function select_all()
  {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "";
   if ($this->get('keyword') != '') {
    $where .= " WHERE c.currency_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%' OR c.currency_code LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT c.* FROM m_currencies c {$where} ORDER BY c.currency_name";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('c.id');
   $this->sno = $this->pagination->get_sno();
  }
  
  function publish()
  {
   
   $this->db->update('m_currencies', array(
    'publish' => $this->post('publish')), array(
    'id' => $this->post('id')
   ));
  }
  
 }
 