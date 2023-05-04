<?php

namespace controllers;

use Exception;

class checkout extends controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function checkout_step()
    {
        if ($this->session('cart') == '') {
            $this->redirecting();
        }
        if (!$this->validate_login()) {
            $this->set_login_referer();
            unset($_SESSION['ch_step']);
            $this->redirecting('login');
        } else {
            if ($this->session('ch_step') == '') {
                $_SESSION['ch_step'] = 1;
            }
        }
    }

    public function ch_step()
    {
        if ($this->post('ch_step') == 0) {
            unset($_SESSION['ch_step']);
        } else {
            $_SESSION['ch_step'] = $this->post('ch_step');
        }
    }

    public function billing()
    {

        $address_type = $this->post('address_type');
        if ($address_type == 0) {
            if ($this->post('address_id') == '') {
                throw new Exception('Please select a delivery address.');
            }
            $query = "SELECT id as address_id, first_name, last_name, display_name, email, mobile_no, CONCAT_WS(', ', a.address, CONCAT(a.city,' - ', a.zip_code), a.state, a.country) as address FROM users_address a WHERE a.id='" . $this->post('address_id') . "' AND a.user_id='" . $this->session('user', 'id') . "'";
            if (!$data = $this->db->select($query)) {
                throw new Exception('Opps, something went wrong. Please reload');
            }
            $_POST = $data;
            $_POST['address_type'] = $address_type;
        } else {
            $this->validate_post_token(true);
            if ($this->post('bill') == '') {
                throw new Exception('Address information is required.');
            }
            $_POST = $this->post('bill');
            if ($this->post('first_name') == '') {
                throw new Exception('Please enter your first name.');
            }
            if ($this->post('last_name') == '') {
                throw new Exception('Please enter your last name.');
            }
            if ($this->post('email') == '') {
                throw new Exception('Please enter your email.');
            }
            if ($this->post('mobile_no') == '') {
                throw new Exception('Please enter your mobile no.');
            }
            if ($this->post('address') == '') {
                throw new Exception('Please enter your address.');
            }
            if ($this->post('country') == '') {
                throw new Exception('Please enter your country.');
            }
            if ($this->post('city') == '') {
                throw new Exception('Please enter your city name.');
            }
            $_POST['display_name'] = $this->post('first_name') . ' ' . $this->post('last_name');
            $_POST['address_type'] = 1;
        }
        $_SESSION['bill'] = $this->post();
        $_SESSION['ch_step'] = 2;
    }

    public function pay_now()
    {
        try {
            $this->db->trans_start();
            $date = date('Y-m-d H:i:s');
            if ($this->session('bill', 'address_type') == 1) {
                $a_id['bill'] = $this->db->insert('users_address', array(
                    'user_id' => abs($this->session('user', 'id')),
                    'first_name' => $this->session('bill', 'first_name'),
                    'last_name' => $this->session('bill', 'last_name'),
                    'display_name' => $this->session('bill', 'display_name'),
                    'email' => $this->session('bill', 'email'),
                    'mobile_no' => $this->session('bill', 'mobile_no'),
                    'address' => $this->session('bill', 'address'),
                    'country' => $this->session('bill', 'country'),
                    'state' => $this->session('bill', 'state'),
                    'city' => $this->session('bill', 'city'),
                    'zip_code' => $this->session('bill', 'zip_code'),
                    'add_date' => $date));
            } else {
                $a_id['bill'] = $this->session('bill', 'address_id');
            }

            $payment_reference_id = date('YmdHis') . rand(0, 999);

            // Insert ORDER
            $order_id = $this->db->insert('orders', array(
                'payment_reference_id' => $payment_reference_id,
                'user_id' => $this->session('user', 'id'),
                'b_address_id' => $a_id['bill'],
                'currency' => $this->company['default_currency'],
                'sub_total' => $this->pay['sub_total'],
                'total_amt' => $this->pay['total'],
                'exchange_rate' => $this->company['rate'],
                'pay_mode' => $this->post('pay_mode'),
                'add_date' => $date,
                'update_date' => $date));

            $this->db->insert('orders_status', array('order_id' => $order_id, 'remarks' => 'Order Placed', 'add_date' => $date));

            $columns = array();
            if ($this->cart) {
                foreach ($this->cart as $k => $v) {
                    if ($this->session('checkout', $k)) {
                        $columns[] = array(
                            'user_id' => $this->session('user', 'id'),
                            'order_id' => $order_id,
                            'sale_id' => $v['sale_id'],
                            'product_id' => $v['product_id'],
                            'product_title' => $v['product_title'],
                            'size' => $v['size'],
                            'color' => $v['color'],
                            'price' => $v['price'],
                            'qty' => $v['qty'],
                            'total_price' => ($v['qty'] * $v['price']));
                        unset($_SESSION['cart'][$k]);
                    }
                }
            }
            if ($columns) {
                $this->db->batch('insert', 'orders_product', $columns);
            }

            // Empty Tmp Cart
            $query = "DELETE FROM tmp_cart WHERE id IN(" . implode(',', $this->session('checkout')) . ")";
            $this->db->query($query);
            $this->gen_cookie('cart', $_SESSION['cart']);

            $this->db->trans_commit();
            unset($_SESSION['checkout'], $_SESSION['bill'], $_SESSION['ch_step']);
            $this->send_email('order_placed', $order_id);
            if ($this->post('pay_mode') == 'delivery') {
                return $order_id;
            } else if ($this->post('pay_mode') == 'paystack' || $this->post('pay_mode') == 'uba') {
                $mode = $this->post('pay_mode');
                $_SESSION[$mode] = array('id' => $order_id, 'payment_reference_id' => $payment_reference_id, 'full_name' => $this->user['first_name'] . ' ' . $this->user['last_name'], 'mobile_no' => $this->user['mobile_no'], 'email' => $this->user['email'], 'amount' => $this->pay['total'], 'currency' => 'NGN');
            }
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            throw new Exception($ex->getMessage());
        }
    }


}
 