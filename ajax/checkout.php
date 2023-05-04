<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new controllers\checkout;
if ($fn->is_ajax_call()) {
    header('Content-Type: application/json');
    $json = '';

    /*
     * Checkout Step
     */

    if ($fn->post('type') == 'ch_step') {
        try {
            if ($fn->post('ch_step') == 0) {
                $fn->redirecting();
            }
            $fn->ch_step();
            $json = array('success' => true, 'rec' => ['checkout' => include app_path . 'views' . ds . 'checkout.php']);
        } catch (Exception $ex) {
            $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
        }
    }

    /*
     * Billing
     */

    if ($fn->post('action') == 'billing') {
        try {
            $fn->billing();
            $json = array('success' => true, 'rec' => ['checkout' => include app_path . 'views' . ds . 'checkout.php']);
        } catch (Exception $ex) {
            $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
        }
    }

    /*
     * Payment
     */
    if ($fn->post('action') == 'payment') {
        try {
            if (!$fn->post('pay_mode')) {
                throw new Exception('Please select your payment method');
            }
            if ($fn->post('pay_mode') == '') {
                throw new Exception('Please select your payment method');
            }
            $script = 'window.location.reload();';
            if (empty($fn->session('cart'))) {
                $fn->session_msg('Oops, No items found in cart.', 'danger', '', 'Error');
                $script = 'window.location.href=\'' . $fn->permalink('products') . '\'';
            } else {
                $id = $fn->pay_now();
                if (in_array($fn->post('pay_mode'), ['stripe', 'paystack', 'uba'])) {
                    $script = 'window.location.href=\'' . $fn->permalink('payment') . '\'';
                } else if ($fn->post('pay_mode') == 'delivery') {
                    $script = 'window.location.href=\'' . $fn->permalink('account/orders?id=' . $id) . '\'';
                }
            }
            $json = array('success' => true, 'script' => $script);
        } catch (Exception $ex) {
            $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
        }
    }

    if ($json) {
        echo $fn->json_encode($json);
    }
    exit();
}