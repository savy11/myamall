<?php

namespace admin\controllers;

use Exception;

class lock extends controller {

 public function __construct() {
  parent::__construct();
  $this->page['name'] = 'Lock Account';
  if ($this->post('lock', 'token') != '') {
   $this->lock();
  }
  if ($this->lock == true) {
   if ($this->session('user', 'lock') == false) {
    if ($this->server('REDIRECT_URL') == $this->server('REQUEST_URI')) {
     $this->redirect();
    } else {
     $this->redirect($this->server('REQUEST_URI'));
    }
   }
  }
  $query = "SELECT u.*, g.group_name, u.image as image_id, f.meta_value as image FROM a_users u LEFT OUTER JOIN a_groups g ON u.group_id=g.id LEFT OUTER JOIN files f ON u.image=f.id WHERE u.id='" . $this->session('user', 'id') . "'";
  $this->user = $this->db->select($query);
  if ($this->session('lo_step') == '') {
   $_SESSION['lo_step'] = 0;
  }
 }

 public function lock() {
  $_SESSION['user']['lock'] = true;
  $this->redirecting('lock');
 }

 public function unlock() {
  $this->validate_post_token(true);
  $query = "SELECT id FROM a_users WHERE publish='Y' AND id='" . $this->replace_sql($this->session('user', 'id')) . "' AND password='" . $this->encrypt($this->post('lock', 'password')) . "'";
  if (!$data = $this->db->select($query)) {
   $_SESSION['lo_step'] ++;
   throw new Exception('Invalid password, Please try again!');
  }
  $_SESSION['user']['lock'] = false;
  unset($_SESSION['lo_step']);
 }

}
