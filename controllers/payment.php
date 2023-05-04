<?php

namespace controllers;

use Exception;

class payment extends controller
{

    private $secret_key = uba_secret_key, $public_key = uba_public_key;

    public function __construct()
    {
        parent::__construct();
        $this->cms['page_title'] = 'Payment In Process..';
        if (in_array($this->varv('email', $this->user), array('xamaranoconcept@gmail.com', 'savyv6215@gmail.com'))) {
            $this->secret_key = test_uba_secret_key;
            $this->public_key = test_uba_public_key;
        }
    }


    public function get_uba_token()
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://seerbitapi.com/api/v2/encrypt/keys",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POSTFIELDS => json_encode(['key' => $this->secret_key . '.' . $this->public_key]),
                CURLOPT_HTTPHEADER => ["content-type: application/json"]));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                throw new Exception('Token Curl Error: ' . $err);
            }

            $auth = json_decode($response, true);

            if ($this->varv('error', $auth)) {
                throw new Exception('Token Error: ' . $this->varv('error', $auth) . ' - ' . $this->varv('message', $auth));
            }
            return $auth;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function get_uba_hash_string($token)
    {
        try {
            $curl = curl_init();

            $amount = $this->session('uba', 'amount');

            $callback_url = $this->permalink('callback');

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://seerbitapi.com/api/v2/encrypt/hashs",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POSTFIELDS => json_encode(['publicKey' => $this->public_key, 'amount' => $amount, 'currency' => 'NGN', 'country' => 'NG', 'paymentReference' => $this->session('uba', 'payment_reference_id'), 'email' => $this->session('uba', 'email'), 'productId' => $this->session('uba', 'id'), 'productDescription' => 'New Order has been placed with order id: ' . $this->session('uba', 'id'), 'callbackUrl' => $callback_url]),
                CURLOPT_HTTPHEADER => ["authorization: Bearer " . $token, "content-type: application/json"]));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                throw new Exception('Hash Curl Error: ' . $err);
            }

            $hash = json_decode($response, true);

            if ($this->varv('error', $hash)) {
                throw new Exception('Token Error: ' . $this->varv('error', $hash) . ' - ' . $this->varv('message', $hash));
            }
            return $hash;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }


    public function uba()
    {
        try {

            $auth = $this->get_uba_token();

            $auth_token = $auth['data']['EncryptedSecKey']['encryptedKey'];

            $hash = $this->get_uba_hash_string($auth_token);

            $hash_token = $hash['data']['hash']['hash'];

            $curl = curl_init();

            $amount = $this->session('uba', 'amount');

            $full_name = $this->session('uba', 'full_name');

            $mobile_no = $this->session('uba', 'mobile_no');

            $callback_url = $this->permalink('callback');

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://seerbitapi.com/api/v2/payments",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POSTFIELDS => json_encode(['publicKey' => $this->public_key, 'amount' => $amount, 'currency' => 'NGN', 'country' => 'NG', 'paymentReference' => $this->session('uba', 'payment_reference_id'), 'fullName' => $full_name, 'mobileNumber' => $mobile_no, 'email' => $this->session('uba', 'email'), 'productId' => $this->session('uba', 'id'), 'productDescription' => 'New Order has been placed with order id: ' . $this->session('uba', 'id'), 'callbackUrl' => $callback_url, 'hash' => $hash_token, 'hashType' => 'sha256']),
                CURLOPT_HTTPHEADER => ["authorization: Bearer " . $auth_token, "content-type: application/json"]));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                throw new Exception('Payment Curl Error: ' . $err);
            }

            $payment = json_decode($response, true);

            if ($this->varv('error', $payment)) {
                throw new Exception('Payment Error: ' . $this->varv('error', $payment) . ' - ' . $this->varv('message', $payment));
            }

            header('Location: ' . $payment['data']['payments']['redirectLink']);
        } catch (Exception $ex) {
            $this->session_msg($ex->getMessage(), 'error');
            $this->redirecting('checkout');
        }

    }

    public function uba_verify_trans()
    {
        try {
            $auth = $this->get_uba_token();

            $auth_token = $auth['data']['EncryptedSecKey']['encryptedKey'];

            $curl = curl_init();
            $reference = $this->get('reference') ? $this->get('reference') : '';
            if (!$reference) {
                throw new Exception('No reference supplied');
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://seerbitapi.com/api/v2/payments/query/" . rawurlencode($reference),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => ["accept: application/json", "authorization: Bearer " . $auth_token]));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                // there was an error contacting the Paystack API
                throw new Exception('Verify Curl Error: ' . $err);
            }

            $tranx = $this->json_decode($response, true);

            if ($this->varv('error', $tranx)) {
                throw new Exception('Verify Error: ' . $this->varv('error', $tranx) . ' - ' . $this->varv('message', $tranx));
            }


            $payment_status = strtolower($this->varv('status', $tranx));

            if (in_array($payment_status, array('success', 'pending', 'failed', 'timeout'))) {
                $date = date('Y-m-d H:i:s');
                $status = 'P';
                if (in_array($payment_status, array('success'))) {
                    $status = 'I';
                } else if (in_array($payment_status, array('failed'))) {
                    $status = 'C';
                }

                $p_status = array_flip($this->payment_status);;
                $query = "SELECT * FROM orders WHERE id='" . $this->replace_sql($this->session('uba', 'id')) . "'";
                if ($dt = $this->db->select($query)) {
                    $this->db->update('orders', array(
                        'status' => $status,
                        'payment_status' => $p_status[ucfirst($payment_status)],
                        'paid_amt' => ($payment_status == 'success' ? ($this->varv('amount', $tranx['data'])) : '')
                    ), array('id' => $dt['id']));

                    if ($payment_status == 'success') {
                        $id = $this->db->insert('trans_pay', array('user_id' => $dt['user_id'], 'order_id' => $dt['id'], 'txn_id' => $this->varv('gatewayref', $tranx['data']['payments']), 'type' => 'UBA', 'amount' => $this->varv('amount', $tranx['data']['payments']), 'currency' => $this->varv('currency', $tranx['data']['payments']), 'add_date' => $date, 'trans_details' => $this->json_encode($tranx)));
                        $this->send_email('order_paid', $dt['id']);
                        $this->send_email('payment_receive', $id);
                    }
                }
                $this->session_msg('Thank you for making a purchase. Your file has bee sent your email.', 'success');
                $this->redirecting('account/orders');
            }
        } catch (Exception $ex) {
            $this->session_msg($ex->getMessage(), 'error');
            $this->redirecting('cart');
        }
    }


    public function paystack()
    {
        try {
            $curl = curl_init();

            $amount = $this->session('paystack', 'amount') * 100;  //the amount in kobo. This value is actually NGN 300

            // url to go to after payment
            $callback_url = $this->permalink('callback');

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode(['amount' => $amount, 'first_name' => $this->session('paystack', 'first_name'), 'last_name' => $this->session('paystack', 'last_name'), 'email' => $this->session('paystack', 'email'), 'callback_url' => $callback_url]),
                CURLOPT_HTTPHEADER => ["authorization: Bearer " . $this->secret_key, "content-type: application/json", "cache-control: no-cache"]));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                // there was an error contacting the Paystack API
                throw new Exception('Curl returned error: ' . $err);
            }

            $tranx = json_decode($response, true);

            if (!$tranx['status']) {
                throw new Exception('API returned error: ' . $tranx['message']);
            }

            // comment out this line if you want to redirect the user to the payment page
            // redirect to page so User can pay
            // uncomment this line to allow the user redirect to the payment page
            header('Location: ' . $tranx['data']['authorization_url']);
        } catch (Exception $ex) {
            $this->session_msg($ex->getMessage(), 'error');
            $this->redirecting('checkout');
        }

    }

    public function verify_trans()
    {
        try {
            $curl = curl_init();
            $reference = $this->get('reference') ? $this->get('reference') : '';
            if (!$reference) {
                throw new Exception('No reference supplied');
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ["accept: application/json", "authorization: Bearer " . $this->secret_key, "cache-control: no-cache"]));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                // there was an error contacting the Paystack API
                throw new Exception('Curl returned error: ' . $err);
            }

            $tranx = $this->json_decode($response, true);

            if (!$tranx['status']) {
                // there was an error from the API
                throw new Exception('API returned error: ' . $tranx['message']);
            }
            $payment_status = strtolower($this->varv('status', $tranx['data']));

            if (in_array($payment_status, array('success', 'pending', 'failed', 'timeout'))) {
                $date = date('Y-m-d H:i:s');
                $status = 'P';
                if (in_array($payment_status, array('success'))) {
                    $status = 'I';
                } else if (in_array($payment_status, array('failed'))) {
                    $status = 'C';
                }

                $p_status = array_flip($this->payment_status);;
                $query = "SELECT * FROM orders WHERE id='" . $this->replace_sql($this->session('paystack', 'id')) . "'";
                if ($dt = $this->db->select($query)) {
                    $this->db->update('orders', array(
                        'status' => $status,
                        'payment_status' => $p_status[ucfirst($payment_status)],
                        'paid_amt' => ($payment_status == 'success' ? ($this->varv('amount', $tranx['data']) / 100) : '')
                    ), array('id' => $dt['id']));

                    if ($payment_status == 'success') {
                        $id = $this->db->insert('trans_pay', array('user_id' => $dt['user_id'], 'order_id' => $dt['id'], 'txn_id' => $this->varv('reference', $tranx['data']), 'type' => 'Paystack', 'amount' => $this->varv('amount', $tranx['data']) / 100, 'currency' => $this->varv('currency', $tranx['data']), 'add_date' => $date, 'trans_details' => $this->json_encode($tranx)));
                        $this->send_email('order_paid', $dt['id']);
                        $this->send_email('payment_receive', $id);
                    }
                }
                $this->session_msg('Thank you for making a purchase. Your file has bee sent your email.', 'success');
                $this->redirecting('account/orders');
            }
        } catch (Exception $ex) {
            $this->session_msg($ex->getMessage(), 'error');
            $this->redirecting('cart');
        }
    }

}
