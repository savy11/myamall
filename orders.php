<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\account;
 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';
  
  /*
   * Return Modal
   */
  
  if ($fn->post('type') == 'return') {
   try {
    $fn->list['return_subject'] = $fn->get_return_subject();
    $type = $fn->post('type');
    $json = array('success' => true, 'html' => include(app_path . 'views' . ds . 'modals.php'), 'modal' => true, 'modalBackdrop' => 'static');
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
  
  
  if ($json) {
   echo $fn->json_encode($json);
  }
  exit();
 }
 if ($fn->get('for') != 'account') {
  $fn->not_found();
 }
 $breadcrumb = array('Account' => $fn->permalink('account'));
 $fn->cms_page('orders');
 $fn->require_login();
 $fn->orders();
 ob_start();
?>
    <style type="text/css">
        .badge-primary {
            color: #004085;
            background-color: #cce5ff;
            border-color: #b8daff;
        }
        .badge-secondary {
            color: #383d41;
            background-color: #e2e3e5;
            border-color: #d6d8db;
        }
        .badge-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .badge-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .badge-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }
        .badge-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>
<?php
 $fn->style = ob_get_clean();
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 include app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
    <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
        <div class="row"    >
                <div class="col-md-12">
                    <div class="header-for-light">
                        <h1 class="wow fadeInRight animated" data-wow-duration="1s">My <span>Orders</span></h1>
                    </div>
                         <?php
                          if (!$fn->order) {
                           if ($fn->data) {
                            ?>
                               <div class="table-responsive">
                                   <table class="table table-striped table-hover">
                                       <thead>
                                       <tr>
                                           <th class="text-center">Date</th>
                                           <th class="text-center">Order Id</th>
                                           <th class="text-center">Status</th>
                                           <th class="text-center">Payment Status</th>
                                           <th class="text-center">Action</th>
                                       </tr>
                                       </thead>
                                       <tbody>
                                       <?php foreach ($fn->data as $k => $v) { ?>
                                           <tr>
                                               <td align="center"
                                                   class="size-13"><?php echo date('F, d Y h:i A', strtotime($v['add_date'])); ?></td>
                                               <td align="center"
                                                   class="size-13"><?php echo '#' . $fn->disp_id($v['id']); ?></td>
                                               <td align="center">
                                                   <span class="badge badge-<?php echo $fn->status_label[$v['status']];
                                                   ?>"><?php echo $fn->order_status[$v['status']]; ?></span>
                                               </td>
                                               <td align="center">
                                                   <span class="badge badge-<?php echo $fn->status_label[$v['payment_status']]; ?>"><?php echo $fn->payment_status[$v['payment_status']]; ?></span>
                                               </td>
                                               <td align="center" class="size-13">
                                                   <a href="<?php echo $fn->permalink('account/orders?id=' . $v['id']); ?>"
                                                      class="badge badge-info">View</a></td>
                                           </tr>
                                       <?php } ?>
                                       </tbody>
                                   </table>
                               </div>
                            <?php
                            echo $fn->pagination->display_paging_info();
                           } else {
                            ?>
                               <div class="alert alert-warning mb-0">You haven't placed any orders yet.</div>
                            <?php
                           }
                          } else {
                           include app_path . 'views' . ds . 'order.php';
                          }
                         ?>
                </div>
            </div>
        </div>
    </div>
<?php
 include app_path . 'inc' . ds . 'footer.php';
 include app_path . 'inc' . ds . 'foot.php';
?>