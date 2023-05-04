<?php

namespace admin\controllers;

class index extends controller {

 public function __construct() {
  parent::__construct();
  $this->require_login();
  $this->page['name'] = _('Dashboard');
  $this->page['icon'] = 's7-home';
 }

}
