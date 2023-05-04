<?php

namespace admin\controllers;

use Exception;

class upload extends controller {

 public function __construct() {
  parent::__construct();
  $this->require_login();
 }

 public function upload() {
  if ($this->file('up_file') == '') {
   throw new Exception('Please upload a file.');
  }
  $file = $this->file('up_file');
  $info = @pathinfo($file['name']);

  $ext = strtolower($info['extension']);
  if ($ext == 'jpeg') {
   $ext = 'jpg';
  }
  $filename = $info['filename'] . '.' . $ext;
  @move_uploaded_file($file['tmp_name'], $this->tmp_path($filename, $this->tmp_path));

  $prev_filename = $this->session($this->tmp_path, array($this->post('type'), 'filename'));
  if ($prev_filename != '') {
   @unlink($this->tmp_path($prev_filename, $this->tmp_path));
  }

  $_SESSION[$this->tmp_path][$this->post('type')] = array('filename' => $filename, 'size' => $file['size']);

  return array('success' => true, 'filename' => $filename, 'size' => $file['size'], 'type' => $this->post('type'));
 }

 public function delete() {
  @unlink($this->tmp_path($this->post('filename'), $this->tmp_path));
  $path = $this->tmp_path('', $this->tmp_path);
  if ($this->is_dir_empty($path)) {
   rmdir($path);
  }
  unset($_SESSION[$this->tmp_path][$this->post('type')]);
  if ($this->session($this->tmp_path) == '') {
   unset($_SESSION[$this->tmp_path]);
  }
 }

 public function is_dir_empty($dir) {
  if (!is_readable($dir))
   return NULL;
  return (count(scandir($dir)) == 2);
 }

}
