<?php

namespace admin\controllers;

use Exception;

class login extends controller {

 public function __construct() {
  parent::__construct();
  $this->already_login();
 }

 public function login_referer() {
  $url = $this->permalink();
  if ($this->session('login_url') != '') {
   $url = $this->session('login_url');
   unset($_SESSION['login_url']);
  }
  $this->redirect($url);
 }

 public function login_validation() {
  try {
   $this->validate_post_token(true);
   $_POST = $this->post('login');
   if ($this->session('lstep') == '') {
    $_SESSION['lstep'] = 0;
   }
   if ($this->session('lstep') > 2) {
    if ($this->session('captcha', 'login') == '') {
     throw new Exception('Please enter the security captcha.');
    }
    if ($this->session('captcha', 'login') != $this->post('captcha')) {
     throw new Exception('Invalid security captcha, Please try again!');
    }
   }
   $this->login();
   unset($_SESSION['lstep'], $_SESSION['er']);
   $this->login_checkpoint();
   $this->login_referer();
   exit();
  } catch (Exception $ex) {
   $_SESSION['lstep'] ++;
   $this->session_msg($ex->getMessage(), 'error', 'Login');
  }
 }

 public function login($id = null) {
  $query = "SELECT id, publish FROM a_users WHERE (email='" . $this->replace_sql($this->post('email')) . "' OR username='" . $this->replace_sql($this->post('email')) . "') AND password='" . $this->encrypt($this->post('password')) . "'";
  if (!$data = $this->db->select($query)) {
   throw new Exception('Invalid email or password!');
  }
  if ($data['publish'] == 'N') {
   throw new Exception('Your account is not publish. Please contact with administrator.');
  }
  unset($data['publish']);

  $_SESSION['user'] = $data;
  $_SESSION['user']['login_time'] = date('Y-m-d H:i:s');
  $_SESSION['user']['login'] = true;
  $_SESSION['user']['checkpoint'] = false;
  $_SESSION['user']['lock'] = false;

  if ($this->post('remember') == 1) {
   $this->gen_cookie('user', array('id' => $data['id']));
  }
  $this->update_log($this->session('user', 'id'), 'login');
 }

}
