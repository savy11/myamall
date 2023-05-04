<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new controllers\payment;
/* if ($fn->session('paystack') == '') { */
/*  $fn->redirecting('cart'); */
/* } */
/* $fn->paystack(); */

if ($fn->session('uba') == '') {
    $fn->redirecting('cart');
}
$fn->uba();