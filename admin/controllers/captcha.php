<?php

namespace admin\controllers;

use \resources\controllers\captcha as create_captcha;

class captcha extends controller {

 protected $captcha = null;
 public $font_path = '';

 public function __construct() {
  parent::__construct();
  $this->captcha = new create_captcha($this);
  $this->font_path = admin_path . 'assets' . ds . 'fonts' . ds;
  $this->captcha->create_image($this->font_path);
 }

}
