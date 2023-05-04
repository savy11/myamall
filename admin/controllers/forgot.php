<?php

namespace admin\controllers;

class forgot extends controller {

    public function __construct() {
        parent::__construct();
        $this->already_login();
        if ($this->session('forgot', 'step') == '') {
            $_SESSION['forgot']['step'] = 1;
        }
    }

    public function check_forgot() {
        $this->validate_post_token(true);
        if ($this->post('forgot') == '') {
            throw new \Exception('Oops, something went wrong.');
        }
        $_POST = $this->post('forgot');

        if ($this->post('email') == '') {
            throw new \Exception('Please enter your email address.');
        }
        if (filter_var($this->post('email'), FILTER_VALIDATE_EMAIL) === false) {
            throw new \Exception('Please enter valid email address.');
        }
        if ($this->post('captcha') == '') {
            throw new \Exception('Please enter the security captcha.');
        }
        if ($this->session('captcha', 'forgot') != $this->post('captcha')) {
            throw new \Exception('Invalid security captcha. Please try again.');
        }
        if (!$data = $this->db->select("SELECT id, publish, email FROM a_users WHERE email='" . $this->replace_sql($this->post('email')) . "'")) {
            throw new \Exception('No account is exists with this email address.');
        }
        if ($data['publish'] == 'N') {
            throw new Exception('Your account is not publish. Please contact with administrator.');
        }

        $a_code = $this->gen_code(6, false);
        $date = date('Y-m-d H:i:s');
        $code_expiry = date('Y-m-d H:i:s', strtotime($date . ' + 8 hour'));

        $this->db->update('a_users', array('a_code' => $a_code, 'code_expiry' => $code_expiry), array('id' => $data['id']));
        $_SESSION['forgot']['step'] = 2;
        $_SESSION['forgot']['id'] = $data['id'];
    }

    public function forgot_verify() {
        $this->validate_post_token(true);
        if ($this->post('verify') == '') {
            throw new \Exception('Oops, something went wrong.');
        }
        $_POST = $this->post('verify');
        if ($this->post('code') == '') {
            throw new \Exception('Please enter your verification code.');
        }
        if (!$data = $this->db->select("SELECT * FROM a_users WHERE id='" . $this->session('forgot', 'id') . "' AND a_code='" . $this->replace_sql($this->post('code')) . "'")) {
            throw new \Exception('Invalid verification code, Please try again.');
        }
        if (time() > strtotime($data['code_expiry'])) {
            unset($_SESSION['forgot']);
            throw new \Exception('Oops, Verification code is expired, Please try again.');
        }
        $_SESSION['forgot']['step'] = 3;
    }

    public function reset_password() {
        $this->validate_post_token(true);
        if ($this->post('reset') == '') {
            throw new \Exception('Oops, something went wrong.');
        }
        $_POST = $this->post('reset');
        if ($this->post('password') == '') {
            throw new \Exception('Please enter your new password.');
        }
        if ($this->post('re_password') == '') {
            throw new \Exception('Please enter your new password again.');
        }
        if ($this->post('password') != $this->post('re_password')) {
            throw new \Exception('Your both password does not match. Please try again.');
        }
        $this->db->update('a_users', array('password' => $this->post('password'), 'a_code' => '', 'code_expiry' => '0000-00-00 00:00:00'), array('id' => $this->session('forgot', 'id')));
        unset($_SESSION['forgot']);
    }

}
