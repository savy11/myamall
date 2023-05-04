<?php

 namespace admin\controllers;

 use Exception;
 use resources\models\pagination as pagination;

 class orders extends controller
 {

  public $pagination = null, $sno = 0;

  public function __construct()
  {
   parent::__construct();
   $this->require_login('orders');
  }

  public function delete()
  {
   if (!$this->per_delete) {
    throw new Exception(_('You have no permission of delete.'));
   }
   $this->validate_delete_token(true);
   $this->db->delete('orders_product', array('order_id' => $this->get('id')));
   $this->db->delete('orders_status', array('order_id' => $this->get('id')));
   $this->db->delete('orders_shipping', array('order_id' => $this->get('id')));
   $this->db->delete('orders', array('id' => $this->get('id')));
  }

  public function select()
  {
   $query = "SELECT o.id, o.user_id, o.currency, o.sub_total, o.exchange_rate, o.status, o.payment_status, o.total_amt, o.paid_amt, DATE_FORMAT(o.add_date,'%e %M, %Y') as add_date, " . "a.display_name as b_name, a.mobile_no as b_mobile, CONCAT_WS(', ', a.address, CONCAT(a.city, ' - ', a.zip_code), a.state, a.country) as b_address " . "FROM orders o " . "LEFT OUTER JOIN users u ON o.user_id=u.id " . "LEFT OUTER JOIN users_address a ON o.b_address_id=a.id " . "WHERE o.id='" . $this->replace_sql($this->get('id')) . "' AND o.deleted='N'";
   if (!$this->data = $this->db->select($query)) {
    $this->not_found();
   }

   $query = "SELECT * FROM users WHERE id='" . $this->data['user_id'] . "'";
   $this->data['user'] = $this->db->select($query);

   $query = "SELECT op.*, p.product_title FROM orders_product op " . "LEFT OUTER JOIN m_products p ON op.product_id=p.id " . "WHERE order_id='" . $this->data['id'] . "'";
   $this->data['products'] = $this->db->selectall($query);

   $query = "SELECT os.*, s.company_name, s.tracking_url, o.user_id, s.company_name as shipped_by, s.tracking_url, os1.tracking_no, DATE_FORMAT(os1.shipping_date,'%e %M, %Y') as shipping_date, os1.remarks as shipping_remarks, s1.subject " . "FROM orders_status os " . "LEFT OUTER JOIN orders o ON os.order_id=o.id " . "LEFT OUTER JOIN orders_shipping os1 ON os.shipping_id=os1.id " . "LEFT OUTER JOIN m_shipping s ON os1.company_id=s.id " . "LEFT OUTER JOIN m_return_subject s1 ON os.subject_id=s1.id " . "WHERE os.order_id='" . $this->data['id'] . "' " . "ORDER BY os.id DESC";
   $this->data['history'] = $this->db->selectall($query);

   $query = "SELECT * FROM trans_pay WHERE order_id='" . $this->data['id'] . "'";
   $this->data['trans'] = $this->db->select($query);
   $this->populate_post_data();
  }

  public function select_all()
  {
   global $dtoken;
   $dtoken = $this->delete_token();
   $where = "WHERE o.deleted='N'";
   if ($this->get('keyword') != '') {
    $where .= " AND u.display_name LIKE '%" . $this->replace_sql($this->get('keyword')) . "%'";
   }
   $query = "SELECT o.*, u.display_name " . "FROM orders o " . "LEFT OUTER JOIN users u ON o.user_id=u.id " . "{$where} ORDER BY id DESC";
   $this->pagination = new pagination($this, $this->db, $query);
   $this->data = $this->pagination->paging('o.id');
   $this->sno = $this->pagination->get_sno();
  }

  public function shipping()
  {
   $this->validate_post_token();
   if ($this->post('shipping') == '') {
    throw new Exception('Oops, something went wrong.');
   }

   $data = $this->post('shipping');
   $id = $this->db->insert('orders_shipping', array('order_id' => $this->post('id'), 'company_id' => $this->varv('company', $data), 'tracking_no' => $this->varv('tracking_no', $data), 'shipping_date' => $this->db_date_format($this->varv('date', $data)), 'remarks' => $this->varv('remarks', $data), 'add_date' => date('Y-m-d H:i:s')));
   $this->update_order_status($id);
   $this->send_email('order_shipped', $id);
  }

  public function update_order_status($shipping_id = 0)
  {
   $date = date('Y-m-d H:i:s');
   $this->db->update('orders', array('status' => $this->post('status'), 'update_date' => $date), array('id' => $this->post('id')));

   $this->db->insert('orders_status', array('order_id' => $this->post('id'), 'shipping_id' => $shipping_id, 'status' => $this->post('status'), 'remarks' => 'Order has been ' . strtolower($this->order_status[$this->post('status')]) . '.', 'by_admin' => 'Y', 'add_date' => $date));

   if ($this->post('status') == 'Y') {
    $this->send_email('order_delivered', $this->post('id'));
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
   $id = $this->db->insert('orders_status', array('order_id' => $this->post('id'), 'subject_id' => $this->varv('subject', $data), 'status' => 'R', 'remarks' => $this->varv('remarks', $data), 'by_admin' => 'Y', 'add_date' => date('Y-m-d H:i:s')));
   $this->db->update('orders', array('status' => 'R'), array('id' => $this->post('id')));
   $this->send_email('order_returned', $id);
  }

  public function update_payment_status($shipping_id = 0)
  {
   $date = date('Y-m-d H:i:s');
   $status = 'P';
   $status_remarks = 'Order Placed';
   if ($this->post('payment_status') == 'Y') {
    $status = 'I';
    $status_remarks = 'Order has been in progress.';
   }
   if ($this->post('payment_status') == 'R') {
    $status = 'C';
    $status_remarks = 'Payment has been refunded and order cancelled.';
   }
   $this->db->update('orders', array('status' => $status, 'payment_status' => $this->post('payment_status'), 'reason' => $this->post('remarks'), 'update_date' => $date), array('id' => $this->post('id')));


   $this->db->insert('orders_status', array('order_id' => $this->post('id'), 'status' => $status, 'remarks' => $status_remarks, 'by_admin' => 'Y', 'add_date' => $date));

   if ($this->post('payment_status') == 'Y') {
    $this->send_email('order_paid', $this->post('id'));
   } else if ($this->post('payment_status') == 'R') {
    $this->send_email('order_refunded', $this->post('id'));
   }
  }

 }
