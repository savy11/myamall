<?php
 
 namespace controllers;
 
 use Exception;
 
 class contact extends controller
 {
  
  public function __construct()
  {
   parent::__construct();
   $this->cms_page('contact');
  }
  
  public function contact_enq()
  {
   $this->validate_post_token(true);
   if ($this->post('contact') == '') {
    throw new Exception('Oops, something went wrong.');
   }
   $_POST = $this->post('contact');
   if ($this->post('name') == '') {
    throw new Exception('Please enter your name.');
   }
   if ($this->post('email') == '') {
    throw new Exception('Please enter your email address.');
   }
   if (filter_var($this->post('email'), FILTER_VALIDATE_EMAIL) === false) {
    throw new Exception('Please enter valid email address.');
   }
   /*   if ($this->post('no') == '') {
     throw new Exception('Please enter your contact number.');
     }
   */
   if ($this->post('subject') == '') {
    throw new Exception('Please enter your subject.');
   }
   if ($this->post('message') == '') {
    throw new Exception('Please enter your message.');
   }
   if ($this->post('captcha') != $this->session('captcha', 'contact')) {
    throw new Exception('Invalid security code, try again.');
   }
   $id = $this->db->insert('v_contacts', array(
    'display_name' => $this->post('name'),
    'email' => $this->post('email'),
    'contact_no' => $this->post('no'),
    'subject' => $this->post('subject'),
    'message' => $this->post('message'),
    'ip' => $this->server('REMOTE_ADDR'),
    'browser' => $this->get_browser(),
    'os' => $this->get_os(),
    'add_date' => date('Y-m-d H:i:s')
   ));
   
   $this->send_email('contact', $id);
   $this->send_email('contact_thanks', $id);
  }
  
 }
