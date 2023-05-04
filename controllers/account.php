<?php
 
 namespace controllers;
 
 use Exception;
 use \resources\models\pagination as pagination;
 
 class account extends controller
 {
  
  public $order = false;
  
  public function __construct()
  {
   parent::__construct();
  }
  
  public function update_details()
  {
   $this->validate_post_token(true);
   if ($this->post('update') == '') {
    throw new Exception('Oops, something went wrong.');
   }
   $_POST = $this->post('update');
   if ($this->post('first_name') == '') {
    throw new Exception('Please enter your first name.');
   }
   if ($this->post('last_name') == '') {
    throw new Exception('Please enter your last name.');
   }
   if ($this->post('mobile_no') == '') {
    throw new Exception('Please enter your mobile no.');
   }
   $display_name = ($this->post('first_name') . ' ' . $this->post('last_name'));
   $id = $this->session('user', 'id');
   $this->db->update('users', array('display_name' => $display_name, 'first_name' => $this->post('first_name'), 'last_name' => $this->post('last_name'), 'mobile_no' => $this->post('mobile_no')), array('id' => $id));
   $this->send_email('profile_change', $id);
   $this->send_email('profile_thanks', $id);
  }
  
  public function change_password()
  {
   $this->validate_post_token(true);
   if ($this->post('change') == '') {
    throw new Exception('Oops, something went wrong.');
   }
   $_POST = $this->post('change');
   if ($this->post('old') == '') {
    throw new Exception('Please enter your old password.');
   }
   if ($this->encrypt($this->post('old')) != $this->session('USER', 'Password')) {
    throw new Exception('Your old password is not correct.');
   }
   if ($this->post('new') == '') {
    throw new Exception('Please enter your new password.');
   }
   if ($this->post('retype') == '') {
    throw new Exception('Please retype your new password.');
   }
   if ($this->post('new') != $this->post('retype')) {
    throw new Exception('Your new password and retype new password does not match.');
   }
   $this->db->update('users', array('password' => $this->post('new')), array('id' => $this->user['id']));
   $this->send_email('change_pass', $this->user['id']);
  }
  
  public function get_address()
  {
   $query = "SELECT * FROM users_address WHERE user_id='" . $this->session('user', 'id') . "' AND id='" . $this->replace_sql($this->get('id')) . "' AND deleted='N'";
   $this->data = $this->db->select($query);
   if (!$this->data) {
    $this->redirecting('account/addresses');
   }
   $this->populate_post_data();
  }
  
  public function edit_address()
  {
   $this->validate_post_token(true);
   if ($this->post('edit') == '') {
    throw new Exception('Oops, something went wrong.');
   }
   $_POST = $this->post('edit');
   if ($this->post('first_name') == '') {
    throw new Exception('Please enter your first name.');
   }
   if ($this->post('last_name') == '') {
    throw new Exception('Please enter your last name.');
   }
   if ($this->post('mobile_no') == '') {
    throw new Exception('Please enter your mobile no.');
   }
   if ($this->post('email') == '') {
    throw new Exception('Please enter your email.');
   }
   if ($this->post('address') == '') {
    throw new Exception('Please enter your address.');
   }
   if ($this->post('country') == '') {
    throw new Exception('Please enter your country.');
   }
   if ($this->post('state') == '') {
    throw new Exception('Please enter your state.');
   }
   if ($this->post('city') == '') {
    throw new Exception('Please enter your city.');
   }
   
   $display_name = $this->post('first_name') . ' ' . $this->post('last_name');
   $this->db->update('users_address', array(
    'user_id' => $this->session('user', 'id'),
    'display_name' => $display_name,
    'first_name' => $this->post('first_name'),
    'last_name' => $this->post('last_name'),
    'mobile_no' => $this->post('mobile_no'),
    'address' => $this->post('address'),
    'city' => $this->post('city'),
    'state' => $this->post('state'),
    'country' => $this->post('country')
   ), array('id' => $this->get('id')));
  }
  
  public function delete_address()
  {
   if ($this->get('id') == '') {
    throw new Exception('Oops, something went wrong.');
   }
   $this->db->update('users_address', array('deleted' => 'Y'), array('user_id' => $this->session('user', 'id'), 'id' => $this->get('id')));
  }
  
  public function orders()
  {
   if ($this->get('id') > 0) {
    $this->order = true;
    $query = "SELECT o.id, o.currency, o.sub_total, o.status, o.payment_status, o.total_amt, o.paid_amt, DATE_FORMAT(o.add_date,'%e %M, %Y') as add_date, o.update_date, " . "a.display_name as b_name, a.mobile_no as b_mobile, CONCAT_WS(', ', a.address, CONCAT(a.city, ' - ', a.zip_code), a.state, a.country) as b_address, " . "a1.display_name as s_name, a1.mobile_no as s_mobile, CONCAT_WS(', ', a1.address, CONCAT(a1.city, ' - ', a1.zip_code), a1.state, a1.country) as s_address " . "FROM orders o " . "LEFT OUTER JOIN users u ON o.user_id=u.id " . "LEFT OUTER JOIN users_address a ON o.b_address_id=a.id " . "LEFT OUTER JOIN users_address a1 ON o.s_address_id=a1.id " . "WHERE o.user_id='" . $this->session('user', 'id') . "' AND o.id='" . $this->replace_sql($this->get('id')) . "' AND o.deleted='N'";
    if (!$this->data = $this->db->select($query)) {
     $this->not_found();
    }
//   $query = "SELECT op.*, p.product_title FROM orders_product op "
//           . "LEFT OUTER JOIN m_products p ON op.product_id=p.id "
//           . "WHERE order_id='" . $this->data['id'] . "'";
    $query = "SELECT * FROM orders_product WHERE order_id='" . $this->data['id'] . "'";
    $this->data['products'] = $this->db->selectall($query);
    
    $query = "SELECT os.*, s.company_name, s.tracking_url, o.user_id, s.company_name as shipped_by, s.tracking_url, os1.tracking_no, DATE_FORMAT(os1.shipping_date,'%e %M, %Y') as shipping_date, os1.remarks as shipping_remarks, s1.subject " . "FROM orders_status os " . "LEFT OUTER JOIN orders o ON os.order_id=o.id " . "LEFT OUTER JOIN orders_shipping os1 ON os.shipping_id=os1.id " . "LEFT OUTER JOIN m_shipping s ON os1.company_id=s.id " . "LEFT OUTER JOIN m_return_subject s1 ON os.subject_id=s1.id " . "WHERE os.order_id='" . $this->data['id'] . "' " . "ORDER BY os.id DESC";
    $this->data['history'] = $this->db->selectall($query);
   } else {
    $query = "SELECT o.* FROM orders o " . "LEFT OUTER JOIN users u ON o.user_id=u.id " . "WHERE o.user_id='" . $this->session('user', 'id') . "' AND o.deleted='N' " . "ORDER BY o.id DESC";
    $this->pagination = new pagination($this, $this->db, $query, 15);
    $this->data = $this->pagination->paging('o.id');
    $this->sno = $this->pagination->get_sno();
   }
  }
  
  
  public function return_order()
  {
   $this->validate_post_token(true);
   if ($this->post('return') == '') {
    throw new Exception('Oops, something went wrong.');
   }
   $data = $this->post('return');
   if ($this->varv('subject', $data) == '') {
    throw new Exception('Please select the subject.');
   }
   if ($this->varv('remarks', $data) == '') {
    throw new Exception('Please enter your remarks.');
   }
   $id = $this->db->insert('orders_status', array('order_id' => $this->post('id'), 'subject_id' => $this->varv('subject', $data), 'status' => 'R', 'remarks' => $this->varv('remarks', $data), 'add_date' => date('Y-m-d H:i:s')));
   $this->db->update('orders', array('status' => 'R'), array('id' => $this->post('id')));
   $this->send_email('order_returned', $id);
  }
  
 }
