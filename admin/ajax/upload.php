<?php

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\upload;

if ($fn->is_ajax_call()) {
 header('Content-Type: application/json');
 $json = '';

 if ($fn->post('action') == 'upload') {
  try {
   $json = $fn->upload();
  } catch (Exception $ex) {
   $json = array('error' => true, 'message' => $ex->getMessage());
  }
 }

 if ($fn->post('action') == 'delete') {
  $fn->delete();
  $json = array('success' => true);
 }

 if ($json) {
  echo $fn->json_encode($json);
 }
}
