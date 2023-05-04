<?php
 
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\cart;
 
 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';
  
  /*
   * Add
   */
  if (in_array($fn->post('action'), ['add_to_cart', 'go_checkout'])) {
   try {
    $modal = false;
    $action = $fn->post('action');
    if (!$fn->post('modal')) {
     $fn->get_product($fn->post('id'));
     if ($fn->varv('colors', $fn->list) || $fn->varv('sizes', $fn->list)) {
      $modal = true;
     }
    }
    if ($modal) {
     $type = 'product';
     $json = array('success' => true, 'html' => include_once app_path . 'views' . ds . 'modals.php', 'modal' => true, 'modalBackdrop' => 'static', 'script' => 'app.classification();');
    } else {
     if (isset($_POST['cart_ids'])) {
      if ($fn->post('cart_ids') == '') {
       throw new Exception('Please select any one product to proceed.');
      }
     }
     $ids = $fn->post('cart_ids');
     if (!$fn->post('cart_ids')) {
      $ids = $fn->add_to_cart();
     }
     $fn->add_checkout($ids);
     $rec['top-cart'] = include app_path . 'views' . ds . 'top_cart.php';
     $script = '$(".cart-count").text("' . $fn->cart_count() . '");app.hide_modal(\'modal\');app.reset_form(\'cart-frm\');';
     if ($action == 'go_checkout') {
      $script = 'window.location.href="' . $fn->permalink('checkout') . '"';
     }
     $json = array('success' => true, 'g_title' => 'Cart', 'g_message' => 'Product has been added to the cart.', 'script' => $script, 'rec' => $rec);
    }
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
   }
  }
  
  /*
   * Update
   */
  if ($fn->post('action') == 'update') {
   try {
    if ($fn->post('qty') > 100) {
     throw new Exception('Only 100 will be purchased at one time.');
    }
    $fn->update_cart();
    $fn->update_payment();
    $rec['top-cart'] = include app_path . 'views' . ds . 'top_cart.php';
    if (in_array($fn->post('page_url'), ['cart', 'checkout'])) {
     $rec['cart'] = include(app_path . 'views' . ds . 'cart.php');
    }
    $json = array('success' => true, 'rec' => $rec, 'script' => '$(".cart-count").text("' . $fn->cart_count() . '");');
    if ($fn->cart_count() == 0) {
     $json = array('success' => true, 'rec' => $rec, 'script' => '$(\'.cart-count\').text("' . $fn->cart_count() . '");window.location.reload();');
    }
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
   }
  }
  
  /*
   * Remove
   */
  if ($fn->post('action') == 'remove') {
   try {
    $fn->remove_cart();
    $fn->update_payment();
    $rec['top-cart'] = include app_path . 'views' . ds . 'top_cart.php';
    if (in_array($fn->post('page_url'), ['cart', 'checkout'])) {
     $rec['cart'] = include(app_path . 'views' . ds . 'cart.php');
    }
    $json = array('success' => true, 'rec' => $rec, 'script' => '$(\'.cart-count\').text("' . $fn->cart_count() . '");');
    if ($fn->cart_count() == 0) {
     $json = array('success' => true, 'rec' => $rec, 'script' => '$(\'.cart-count\').text("' . $fn->cart_count() . '");window.location.reload();');
    }
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