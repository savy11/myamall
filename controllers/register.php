<?php
 
 namespace controllers;
 
 use Exception;
 
 class register extends controller
 {
  
  public function __construct()
  {
   parent::__construct();
   $this->cms_page('register');
   $this->already_login();
   $this->page['name'] = 'Register';
  }


  public function get_terms(){
   $query = "SELECT * FROM m_pages WHERE page_url='terms'";
   return $this->db->select($query);
  }
  
  
 }
