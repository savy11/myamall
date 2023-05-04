<?php

 require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\controller;

 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';

  if ($fn->post('type') == 'crop') {
   try {
    $type = $fn->post('type');
    $html = include(admin_path . 'views' . ds . 'modals.php');
    $json = array('success' => true, 'html' => $html, 'modal' => true, 'modalbackdrop' => 'static', 'script' => 'app.img_crop();app.modal_width();');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => "Crop Image", 'g_message' => $ex->getMessage());
   }
  }
  if ($fn->post('type') == 'add-alt') {
   try {
    $fn->add_alt();
    $json = array('success' => 'true', 'g_title' => "Crop Image", 'g_message' => 'Alternate text been updated successfully', 'script' => 'app.reset_form(\'crop-frm\');');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => "Crop Image", 'g_message' => $ex->getMessage());
   }
  }
  if ($fn->post('type') == 'export') {
   try {
    $fn->crop_image();
    $json = array('success' => 'true', 'g_title' => "Crop Image", 'g_message' => 'Image has been updated successfully', 'script' => 'app.reset_form(\'crop-frm\');app.hide_modal();app.page_reload();');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => "Crop Image", 'g_message' => $ex->getMessage());
   }
  }

  if ($json) {
   echo $fn->json_encode($json);
  }
 }
