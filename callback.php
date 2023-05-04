<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new controllers\payment;
if ($fn->session('uba') == '') {
    $fn->redirecting();
}
$fn->uba_verify_trans();