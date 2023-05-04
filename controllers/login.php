<?php

namespace controllers;

use Exception;

class login extends controller {

  public function __construct() {
    parent::__construct();
    $this->already_login();
    $this->cms_page('login');
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
    if ($this->session('lstep') == '') {
      $_SESSION['lstep'] = 0;
    }
    try {
      $this->validate_post_token(true);
      $_POST = $this->post('login');
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
      if (!$this->is_ajax_call()) {
        $this->login_referer();
        exit();
      }
    } catch (Exception $ex) {
      $_SESSION['lstep'] ++;
      if (!$this->is_ajax_call()) {
        $this->session_msg($ex->getMessage(), 'error', 'Login');
      } else {
        throw new Exception($ex->getMessage());
      }
    }
  }

  public function login($id = null) {
    $query = "SELECT id, publish, verified FROM users WHERE email='" . $this->replace_sql($this->post('email')) . "' AND password='" . $this->encrypt($this->post('password')) . "'";
    if (!$data = $this->db->select($query)) {
      throw new Exception('Invalid email or password!');
    }
    if ($data['publish'] == 'N') {
      throw new Exception('Your account is blocked. Please contact with administrator.');
    }
    /*if ($data['verified'] == 'N') {
      throw new Exception('Your email is not verified. To verify go to forgot password.');
    }*/
    unset($data['publish'], $data['verified']);

    $_SESSION['user'] = $data;
    $_SESSION['user']['login_time'] = date('Y-m-d H:i:s');
    $_SESSION['user']['login'] = true;
    $_SESSION['user']['guest'] = false;

    if ($this->post('remember') == 1) {
      $this->gen_cookie('user', array('id' => $data['id']));
    }
    $this->update_log($this->session('user', 'id'), 'login');
  }

}
