<?php

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new controllers\register;

try {
 $fn->register_verification();
 $fn->session_msg('Your email address verified successfully! Please login.', 'success', 'Register');
 $fn->redirecting('login');
} catch (Exception $ex) {
 if ($ex->getMessage() != '') {
  $fn->session_msg($ex->getMessage(), 'error', 'Register');
 }
 $fn->redirecting('register');
}
 