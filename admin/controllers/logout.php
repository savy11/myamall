<?php

namespace admin\controllers;

class logout extends controller {

 public function __construct() {
  parent::__construct();
  $this->logout();
 }

 public function logout() {
  $this->db->update('a_codes', array(
   'status' => 'D'), array(
   'user_id' => $this->session('user', 'id'),
   'session_id' => session_id(),
   'type' => 'login',
   'status' => 'N'
  ));
  $this->update_log($this->session('user', 'id'), 'logout');
  $this->clear_cookie('user');
  unset($_SESSION['user'], $_SESSION['ref'], $_SESSION['lo_step']);
  $this->redirecting('login');
 }

}
