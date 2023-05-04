<?php
 
 namespace admin\controllers;
 
 use Exception;
 use \resources\models\pagination as pagination;
 
 class m_countries extends controller
 {
  
  public $pagination = null, $sno = 0;
  
  public function __construct()
  {
   parent::__construct();
   $this->require_login('m-countries');
   if ($this->get('action') != '') {
    $this->list['currencies'] = $this->get_currencies();
   }
  }
  
  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('m_countries', array('id' => $this->get('id')));
  }
  
  public function insert()
  {
   if (!$this->per_add) {
    throw new Exception(_('You have no permission of add.'));
   }
   $this->validate_post_token(true);
   $id = $this->db->insert('m_countries', array(
    'country_name' => $this->post('country_name'),
    'country_code' => $this->post('country_code'),
    'currency_id' => $this->post('currency_id'),
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
   $this->db->update('m_countries', array(
    'country_name' => $this->post('country_name'),
    'country_code' => $this->post('country_code'),
    'currency_id' => $this->post('currency_id'),
    'publish' => $this->post('publish')), array(
    'id' => $id
   ));
  }
  
  public function select()
  {
   $query = "SELECT p.* FROM m_countries p WHERE p.id='" . $this->replace_sql($this->get('id')) . "'";
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
    $where .= " WHERE p.country_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT p.*, CONCAT(c.currency_code, ' (', c.currency_name, ')') as currency_name FROM m_countries p LEFT OUTER JOIN m_currencies c ON p.currency_id=c.id {$where} ORDER BY p.country_name";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('p.id');
   $this->sno = $this->pagination->get_sno();
  }
  
  function publish()
  {
   
   $this->db->update('m_countries', array(
    'publish' => $this->post('publish')), array(
    'id' => $this->post('id')
   ));
  }
  
 }
 