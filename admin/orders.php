<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\orders;
 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';
  
  if ($fn->post('type') == 'order_status') {
   try {
    $json = array('success' => true);
    if ($fn->order_status[$fn->post('status')] == 'Shipped') {
     $fn->list['shippers'] = $fn->get_shipping_companies();
     $type = 'shipping';
     $html = include admin_path . 'views' . ds . 'modals.php';
     $json = array('modal' => true, 'modalBackdrop' => 'static', 'html' => $html);
    } else if ($fn->order_status[$fn->post('status')] == 'Returned') {
     $fn->list['return_subject'] = $fn->get_return_subject();
     $type = 'return';
     $html = include admin_path . 'views' . ds . 'modals.php';
     $json = array('modal' => true, 'modalBackdrop' => 'static', 'html' => $html);
    } else {
     $fn->update_order_status();
     $html = include admin_path . 'views' . ds . 'order_status.php';
     $json = array('success' => true, 'html' => $html, 'g_title' => $fn->page['name'], 'g_message' => 'Order status has been updated successfully!');
    }
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
   }
  }
  
  
  if ($fn->post('action') == 'shipping') {
   try {
    $fn->shipping();
    $json = array('success' => true, 'html' => include admin_path . 'views' . ds . 'order_status.php', 'g_title' => $fn->page['name'], 'g_message' => 'Order status has been updated successfully!', 'script' => 'app.hide_modal();');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
   }
  }
  
  /*
   * Return
   */
  if ($fn->post('action') == 'return') {
   try {
    $fn->return_order();
    $json = array('success' => true, 'g_title' => 'Order', 'g_message' => 'Your request has been submitted successfully!', 'script' => 'app.hide_modal(); window.location.reload();');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
   }
  }
  
  if ($fn->post('type') == 'payment_status') {
   try {
    
    $type = $fn->post('type');
    
    $json = array('success' => true, 'html' => include admin_path . 'views' . ds . 'modals.php', 'modal' => true, 'modalBackdrop' => 'static');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
   }
  }
  
  /*
   * Payment Status
   */
  if ($fn->post('action') == 'payment_status') {
   try {
    $fn->update_payment_status();
    $json = array('success' => true, 'g_title' => 'Order', 'g_message' => 'Payment status has been updated successfully!', 'script' => 'app.hide_modal(); window.location.reload();');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
   }
  }
  
  if ($json) {
   echo $fn->json_encode($json);
  }
  exit();
 }
 if ($fn->get('action') == 'delete') {
  try {
   $fn->delete();
   $fn->session_msg('Data has been deleted successfully!', 'success');
  } catch (Exception $ex) {
   $fn->session_msg($ex->getMessage(), 'error');
  }
  $fn->return_ref();
 }
 include 'inc/head.php';
 include 'inc/header.php';
 if ($fn->get('action') == 'view' || $fn->get('action') == 'edit' && $fn->get('id')) {
  if ($fn->get('id')) {
   $fn->select();
  }
  $rate = $fn->post('exchange_rate');
  ?>
     <div class="panel panel-default">
      <?php include 'inc/panel-head.php'; ?>
         <div class="panel-body">
             <div class="row">
                 <div class="col-sm-8">
                     <div class="panel panel-default">
                         <div class="panel-heading">
                          <?php if ($fn->get('action') == 'edit') { ?>
                              <span class="pull-right" id="payment_status_<?php echo $fn->get('id'); ?>">
                                 <strong>Payment Status: </strong>
                           <?php echo include admin_path . 'views' . ds . 'payment_status.php'; ?>
                          </span>
                          <?php } ?>
                             <h4 class="panel-title">Order Information</h4>
                         </div>
                         <div class="panel-body">
                             <div class="row">
                                 <div class="col-sm-12">
                                     <table class="table table-striped table-bordered table-hover">
                                         <tbody>
                                         <tr>
                                             <th>Exchange Rate</th>
                                             <td><?php echo $rate; ?></td>
                                             <th>Sub Total</th>
                                             <td><?php echo $fn->show_price(($fn->post('sub_total') * $rate), $fn->post('currency')); ?></td>
                                         </tr>
                                         <tr>
                                             <th>Status</th>
                                             <td><?php echo $fn->order_status[$fn->post('status')]; ?></td>
                                             <th>Payment Status</th>
                                             <td><?php echo $fn->payment_status[$fn->post('payment_status')]; ?></td>
                                         </tr>
                                         <tr>
                                             <th>Order Total</th>
                                             <td><?php echo $fn->show_price(($fn->post('total_amt') * $rate), $fn->post('currency')); ?></td>
                                             <th>Paid Amount</th>
                                             <td><?php echo $fn->show_price(($fn->post('paid_amt') * $rate), $fn->post('currency')); ?></td>
                                         </tr>
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="panel panel-default">
                         <div class="panel-heading">
                             <h4 class="panel-title">Delivery Address</h4>
                         </div>
                         <div class="panel-body">
                             <address class="mb-0">
                                 <p class="mb-5"><span class="fw-600 mr-5"><?php echo $fn->post('b_name'); ?></span>
                                     <small class="size-13"><?php echo $fn->post('b_mobile'); ?></small>
                                 </p>
                                 <p class="mb-0"><span class="size-13"><?php echo $fn->post('b_address'); ?></span></p>
                             </address>
                         </div>
                     </div>
                 </div>

                 <div class="col-sm-4">
                     <div class="panel panel-default">
                         <div class="panel-heading custom">
                             <h4 class="panel-title">User Information</h4>
                         </div>
                         <div class="panel-body">
                          <?php if ($user = $fn->data['user']) { ?>
                              <table class="table table-striped table-bordered table-hover">
                                  <tbody>
                                  <tr>
                                      <th>First Name</th>
                                      <td><?php echo $user['first_name']; ?></td>
                                  </tr>

                                  <tr>
                                      <th>Last Name</th>
                                      <td><?php echo $user['last_name']; ?></td>
                                  </tr>

                                  <tr>
                                      <th>Email</th>
                                      <td><?php echo $user['email']; ?></td>
                                  </tr>

                                  <tr>
                                      <th>Mobile No.</th>
                                      <td><?php echo $user['mobile_no']; ?></td>
                                  </tr>

                                  <tr>
                                      <th>Join Date</th>
                                      <td><?php echo $fn->dt_format($user['add_date'], 'F d, Y H:i:s'); ?></td>
                                  </tr>

                                  <tr>
                                      <th>Verified</th>
                                      <td><label class="label label-<?php echo $fn->yes_no_label[$user['verified']];
                                       ?>"><?php echo $fn->yes_no[$user['verified']] ?></label></td>
                                  </tr>

                                  <tr>
                                      <th>Action</th>
                                      <td><a href="<?php echo $fn->permalink('users/edit/' . $user['id']) ?>"
                                             target="_blank"
                                             class="btn btn-warning btn-xs"><i class="ti-pencil"></i> Edit</a></td>
                                  </tr>

                                  </tbody>
                              </table>
                          <?php } ?>
                         </div>
                     </div>
                 </div>
              
              <?php if ($fn->post('products')) { ?>
                  <div class="col-sm-12">
                      <div class="panel panel-default">
                          <div class="table-responsive">
                              <table class="table table-bordered" cellspacing="0" cellpadding="0">
                                  <thead class="fast-tabel">
                                  <tr>
                                      <th width="6%" class="text-center">#</th>
                                      <th>Product</th>
                                      <th width="15%" class="text-center">Price</th>
                                      <th width="15%" class="text-center">Quantity</small></th>
                                      <th width="15%" class="text-center">Total Price</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                   foreach ($fn->post('products') as $k => $v) {
                                    ?>
                                       <tr>
                                           <td align="center"><?php echo $k + 1; ?></td>
                                           <td><?php echo $v['product_title']; ?>
                                            <?php if ($v['color'] || $v['size']) { ?>
                                                <span> | </span>
                                             <?php if ($v['color']) { ?>
                                                    <span><b>Color:</b> <?php echo $v['color']; ?></span>
                                             <?php }
                                             if ($v['size']) {
                                              ?>
                                                 <span> | <b>Size:</b> <?php echo $v['size']; ?></span>
                                             <?php } ?>
                                            <?php } ?>
                                           </td>
                                           <td align="center"><?php echo $fn->show_price(($v['price'] * $rate), $fn->post('currency')); ?></td>
                                           <td align="center"><?php echo $v['qty']; ?></td>
                                           <td align="center"
                                               width="18%"><?php echo $fn->show_price(($v['total_price'] * $rate), $fn->post('currency')); ?></td>
                                       </tr>
                                   <?php } ?>
                                  </tbody>
                                  <tfoot>
                                  <tr>
                                      <th colspan="4" class="text-right">Sub Total</th>
                                      <th class="text-center"><?php echo $fn->show_price(($fn->post('sub_total') * $rate), $fn->post('currency')); ?></th>
                                  </tr>
                                  <tr>
                                      <th colspan="4" class="text-right">Grand Total</th>
                                      <th class="text-center"><?php echo $fn->show_price(($fn->post('total_amt') * $rate), $fn->post('currency')); ?></th>
                                  </tr>
                                  <tfoot>
                              </table>
                          </div>
                      </div>
                  </div>
              <?php } ?>
                 <div class="col-sm-<?php echo $fn->post('trans') ? '6' : '12'; ?>">
                  <?php if ($fn->varv('history', $fn->data)) { ?>
                      <div class="panel panel-default">
                          <div class="panel-heading custom">
                           <?php if ($fn->get('action') == 'edit') { ?>
                               <span class="pull-right" id="order_status_<?php echo $fn->get('id'); ?>">
                                   <strong>Order Status: </strong>
                           <?php echo include admin_path . 'views' . ds . 'order_status.php'; ?>
                          </span>
                           <?php } ?>
                              <h4 class="panel-title">Order History</h4>
                          </div>
                          <div class="panel-body" style="max-height: 400px; overflow: auto;">
                           <?php foreach ($fn->data['history'] as $v) { ?>
                               <div class="order-history">
                                   <div class="head">
                                       <span class="timestamp"><?php echo $fn->dt_format($v['add_date'], 'F d, Y h:i A'); ?></span>
                                       <span class="status"><label
                                                   class="label label-<?php echo $fn->status_label[$v['status']]; ?>"><?php echo $fn->order_status[$v['status']]; ?></label></span>
                                   </div>
                                   <div class="remarks"><?php echo ($v['subject'] ? '<strong>Subject:</strong> ' . $v['subject'] . ' - ' : '') . $v['remarks']; ?></div>
                                   <div class="update-by">- Updated
                                       By <?php echo $v['by_admin'] == 'Y' ? 'Admin' : ($v['user_id'] > 0 ? 'Customer' : 'Guest'); ?></div>
                               </div>
                           <?php } ?>
                          </div>
                      </div>
                   <?php
                  }
                  ?>
                 </div>
              <?php
               if ($fn->post('trans')) {
                $data = $fn->json_decode($fn->post('trans', 'trans_details'));
                ?>
                   <div class="col-sm-6">
                       <div class="panel panel-default">
                           <table class="table table-bordered">
                               <thead>
                               <tr>
                                   <th colspan="4">Transaction Details
                                       (<?php echo ucfirst($fn->post('trans', 'type')); ?>
                                       )
                                   </th>
                               </tr>
                               </thead>
                               <tbody>
                               <?php
                                if (isset($data)) {
                                 ?>
                                    <tr>
                                        <th width="20%">Referene No.</th>
                                        <td><?php echo $fn->varv('reference', $data['data']); ?></td>
                                    </tr>
                                    <tr>
                                        <th width="20%">Currency</th>
                                        <td><?php echo $fn->varv('currency', $data['data']); ?></td>
                                    </tr>
                                    <tr>
                                        <th width="20%">Paid Amount</th>
                                        <td><?php echo $fn->show_price($fn->varv('amount', $data['data']) / 100, $fn->varv('currency', $data['data'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th width="20%">Payment Date</th>
                                        <td><?php echo $fn->dt_format($fn->varv('transaction_date', $data['data']), 'F d, Y H:i A'); ?></td>
                                    </tr>
                                 <?php
                                }
                               ?>
                               </tbody>
                           </table>
                       </div>
                   </div>
                <?php
               }
              ?>
             </div>
         </div>
     </div>
  <?php
 } else {
  $fn->select_all();
  ?>
     <div class="panel panel-default">
      <?php include 'inc/panel-head.php'; ?>
         <div class="panel-body">
          <?php if ($fn->data) {
           ?>
              <div class="table-responsive">
                  <table class="table table-striped table-bordered">
                      <thead>
                      <tr>
                          <th width="5%" class="text-center hidden-xs"><?php echo _('#'); ?></th>
                          <th width="15%"><?php echo _('Date'); ?></th>
                          <th><?php echo _('Name'); ?></th>
                          <th width="10%"><?php echo _('Amount'); ?></th>
                          <th width="12%" class="text-center"><?php echo _('Order Status'); ?></th>
                          <th width="15%" class="text-center"><?php echo _('Payment Status'); ?></th>
                       <?php if ($fn->check_per()) { ?>
                           <th width="5%" class="text-center">Actions</th>
                       <?php } ?>
                      </tr>
                      </thead>
                      <tbody>
                      <?php
                       $i = $fn->sno;
                       foreach ($fn->data as $row) {
                        ?>
                           <tr>
                               <td class="text-center hidden-xs"><?php echo $i++; ?></td>
                               <td><?php echo $fn->dt_format($row['add_date'], 'M d, Y h:i A'); ?></td>
                               <td><?php echo $row['display_name']; ?></td>
                               <td><?php echo $fn->show_price(($row['total_amt'] * $row['exchange_rate']), $row['currency']); ?></td>
                               <td align="center" id="order_status_<?php echo $row['id']; ?>">
                                <?php
                                 //                    if ($row['payment_status'] == 'Y') {
                                 echo include admin_path . 'views' . ds . 'order_status.php';
                                 /*} else {
                                   ?>
                                   <button type="button" class="btn btn-<?php echo $fn->status_label[$row['status']]; ?> btn-xs prevent"><?php echo $fn->order_status[$row['status']]; ?></button>
                                 <?php }*/
                                ?>
                               </td>
                               <td align="center" id="payment_status_<?php echo $row['id']; ?>">
                                <?php
                                 //                    if ($row['payment_status'] == 'Y') {
                                 echo include admin_path . 'views' . ds . 'payment_status.php';
                                 /*} else {
                                   ?>
                                   <button type="button" class="btn btn-<?php echo $fn->status_label[$row['payment_status']]; ?> btn-xs prevent"><?php echo $fn->payment_status[$row['payment_status']]; ?></button>
                                 <?php }*/
                                ?>
                               </td>
                            <?php
                             ob_start();
                            ?>
                               <a href="<?php echo $fn->get_action_url('view', $row['id']); ?>"
                                  class="btn btn-info btn-sm">
                                   <span class="icon ti-search"></span> <?php echo _('View'); ?></a>
                            <?php
                             $fn->actions_multi = ob_get_clean();
                             include 'inc/actions.php';
                            ?>
                           </tr>
                       <?php } ?>
                      </tbody>
                  </table>
              </div>
           <?php
           echo $fn->pagination->display_paging_info();
          } else {
           ?>
              <div class="alert alert-danger mb-0">Oops, nothing found.</div>
          <?php }
          ?>
         </div>
     </div>
  <?php
 }
 include 'inc/footer.php';
 include 'inc/foot.php';
?>
