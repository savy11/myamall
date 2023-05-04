<?php

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new controllers\forgot_password;

try {
 $data = $fn->reset_verification();
 $fn->redirecting('reset-password', $data);
} catch (Exception $ex) {
 if ($ex->getMessage() != '') {
  $fn->session_msg($ex->getMessage(), 'error', 'Forgot Password');
 }
 $fn->redirecting('forgot-password');
}
 