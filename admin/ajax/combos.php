<?php

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\controller;

if ($fn->is_ajax_call()) {
 header('Content-Type: application/json');
 $json = '';

 if ($fn->post('type') == 'fabrics_cat') {
  $parent_id = implode(',', $fn->post('parent_id'));
  $fn->list['cats'] = $fn->get_fabrics_cat($parent_id);

  $str = $fn->show_list($fn->list['cats'], $fn->post('category_id'), true);
  $json = array('success' => true, 'html' => $str);
 }

 /*
  * States
  */
 if ($fn->post('type') == 'states') {
  $fn->list['states'] = $fn->get_states($fn->post('country_id'));
  $str = $fn->show_list($fn->list['states'], $fn->post('state_id'), true);
  $json = array('success' => true, 'html' => $str);
 }

 /*
  * Cities
  */
 if ($fn->post('type') == 'cities') {
  $fn->list['cities'] = $fn->get_cities($fn->post('state_id'));
  $str = $fn->show_list($fn->list['cities'], $fn->post('city_id'), true);
  $json = array('success' => true, 'html' => $str);
 }

 if ($json) {
  echo $fn->json_encode($json);
 }
}
