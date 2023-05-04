<?php

 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\cart;

 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';

  if ($fn->post('action') == 'send_quote') {
   try {
    $fn->insert_quote();
    $json = array('success' => true, 'g_title' => 'Quote', 'g_message' => 'Quote has been sent successfully.', 'script' => 'app.reset_form(\'quote-frm\')');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
   }
  }

  if ($json) {
   echo $fn->json_encode($json);
  }
  exit();
 }
?>