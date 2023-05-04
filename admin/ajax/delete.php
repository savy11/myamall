<?php

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\controller;

if ($fn->is_ajax_call()) {
 header('Content-Type: application/json');
 $json = '';
 /*
   Session Files
  */
 if ($fn->post('type') == 'tmp') {
  $type = $fn->post('for');
  $filename = $fn->post('filename');

  $fn->unlink_files($fn->tmp_path(), $filename);
  unset($_SESSION[$type][$filename]);

  if ($fn->session($type) != '' && count($fn->session($type)) == 0) {
   unset($_SESSION[$type]);
  }
  $json = array('success' => true, 'g_title' => 'File deleted', 'g_message' => 'File has been deleted successfully!');
 }

 /*
   Database file
  */
 if ($fn->post('type') == 'db') {
  $fn->db_file_delete($fn->post('id'));
  $json = array('success' => true, 'g_title' => 'File deleted', 'g_message' => 'File has been deleted successfully!');
 }

 if ($json) {
  echo $fn->json_encode($json);
 }
}
