<?php

namespace controllers;

use Exception;

class logout extends controller {

 public function __construct() {
  parent::__construct();
  $this->logout();
 }

 public function logout() {
  $this->update_log($this->session('user', 'id'), 'logout');
  $this->clear_cookie('user');
  unset($_SESSION['user']);
  $this->redirecting();
 }

}
