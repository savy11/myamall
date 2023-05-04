<?php

namespace admin\controllers;

use Exception;

class checkpoint extends controller {

 public function __construct() {
  parent::__construct();
  if ($this->checkpoint == true) {
   if ($this->session('user', 'checkpoint') == true) {
    if ($this->server('REDIRECT_URL') == $this->server('REQUEST_URI')) {
     $this->redirect();
    } else {
     $this->redirect($this->server('REQUEST_URI'));
    }
   }
  }
  $query = "SELECT u.*, g.group_name FROM a_users u LEFT OUTER JOIN a_groups g ON u.group_id=g.id WHERE g.id='" . $this->session('user', 'id') . "'";
  $this->user = $this->db->select($query);
 }

 public function checkpoint_validation() {
  try {
   $this->validate_post_token(true);
   $_POST = $this->post('checkpoint');
   if ($this->session('cstep') == '') {
    $_SESSION['cstep'] = 0;
   }
   if ($this->session('cstep') > 2) {
    if ($this->session('captcha', 'checkpoint') == '') {
     throw new Exception('Please enter the security captcha.');
    }
    if ($this->session('captcha', 'checkpoint') != $this->post('captcha')) {
     throw new Exception('Invalid security captcha, Please try again!');
    }
   }
   $this->login_checkpoint();
   unset($_SESSION['cstep'], $_SESSION['er']);
   $this->redirect();
  } catch (Exception $ex) {
   $_SESSION['cstep'] ++;
   $this->session_msg($ex->getMessage(), 'error', 'Checkpoint');
  }
 }

 public function login_checkpoint() {
  $query = "SELECT id, user_id, code, expiry_date FROM a_codes WHERE session_id='" . session_id() . "' AND user_id='" . $this->session('user', 'id') . "' AND type='login' AND status='N'";
  if (!$data = $this->db->select($query)) {
   throw new Exception('Oops, something went wrong.');
  }
  $interval = abs(strtotime($data['expiry_date']) - time());
  $minutes = round($interval / 60);
  if ($minutes >= $this->expiry_mins) {
   throw new Exception('Verification code is expired.');
  }
  if ($data['code'] != $this->post('code')) {
   throw new Exception('Verification code does not match.');
  }

  $this->db->update('a_codes', array('status' => 'Y'), array('id' => $data['id']));
  $_SESSION['user']['checkpoint'] = true;
  if ($this->post('remember') == 1) {
   $this->gen_cookie('validate_pc_' . $data['user_id'], array('status' => true));
  }
 }

}
