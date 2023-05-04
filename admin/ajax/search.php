<?php

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\controller;

if ($fn->is_ajax_call()) {
 header('Content-Type: application/json');
 $json = '';

 /*
  * Families
  */
 if ($fn->get('action') == 'families') {
  $json['q'] = $fn->get('data', 'q');
  $json['results'] = $fn->search_families();
 }

 if ($json) {
  echo $fn->json_encode($json);
 }
}
