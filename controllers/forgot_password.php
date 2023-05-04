<?php

namespace controllers;

use Exception;

class forgot_password extends controller {

 public function __construct() {
  parent::__construct();
  $this->already_login();
  $this->page['name'] = 'Forgot Password';
 }

 public function check_forgot() {
  $this->validate_post_token(true);
  if ($this->post('forgot') == '') {
   throw new Exception('Oops, something went wrong.');
  }
  $_POST = $this->post('forgot');

  if ($this->post('email') == '') {
   throw new Exception('Please enter your email address.');
  }
  if (filter_var($this->post('email'), FILTER_VALIDATE_EMAIL) === false) {
   throw new Exception('Please enter valid email address.');
  }
  if ($this->post('captcha') == '') {
   throw new Exception('Please enter the security captcha.');
  }
  if ($this->session('captcha', 'forgot') != $this->post('captcha')) {
   throw new Exception('Invalid security captcha. Please try again.');
  }
  if (!$data = $this->db->select("SELECT id, publish, email FROM users WHERE email='" . $this->replace_sql($this->post('email')) . "'")) {
   throw new Exception('No account is exists with this email address.');
  }
  if ($data['publish'] == 'N') {
   throw new Exception('Your account is blocked. Please contact with administrator.');
  }
  $this->v_code($data['id'], 'Forgot');
  $this->send_email('forgot_pass', $data['id']);
 }

 public function reset_verification() {
  if ($this->get('key') == '') {
   throw new Exception();
  }
  $data = $this->decrypt_post_data($this->get('key'));
  $this->check_v_code($data['code_id'], 'Forgot', $data['code']);
  return $data;
 }

 public function reset_validate() {
  try {
   return $this->reset_verification();
  } catch (Exception $ex) {
   if ($ex->getMessage() != '') {
    $this->session_msg($ex->getMessage(), 'error', 'Forgot Password');
   }
   $this->redirecting('forgot-password');
  }
 }

 public function reset_password() {
  $this->validate_post_token(true);
  if ($this->post('reset') == '') {
   throw new Exception('Oops, something went wrong.');
  }
  $_POST = $this->post('reset');
  if (!$data = $this->reset_verification()) {
   throw new Exception('Oops, something went wrong.');
  }
  if ($this->post('email') != $data['email']) {
   throw new Exception('Email address does not match.');
  }
  if ($this->post('password') == '') {
   throw new Exception('Please enter your new password.');
  }
//  $this->password_validation($this->post('password'));
  if ($this->post('re_password') == '') {
   throw new Exception('Please enter your new password again.');
  }
  if ($this->post('password') != $this->post('re_password')) {
   throw new Exception('Your both password does not match. Please try again.');
  }
  $id = $this->db->get_value('users', 'id', 'email=\'' . $data['email'] . '\'');
  $this->db->update('users', array(
      'password' => $this->post('password')), array(
      'id' => $id
  ));
  $this->update_v_code($data['code_id']);
  $this->send_email('change_pass', $id);
 }

}
