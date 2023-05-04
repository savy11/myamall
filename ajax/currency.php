<?php
 
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\controller;
 
 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';
  
  if ($fn->post('action') == 'change') {
   $_SESSION['default'] = ['country' => $fn->post('country'), 'currency' => $fn->post('currency')];
   $url = $fn->permalink();
   $json = ['success' => true, 'script' => 'window.location.href=\'' . $url . '\''];
  }
  
  if ($json) {
   echo $fn->json_encode($json);
  }
  exit();
 }
?>