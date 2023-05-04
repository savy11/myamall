<div class="panel panel-default">
    <div class="panel-heading">
        <strong class="panel-title"><a href="<?php echo $fn->permalink('account/orders'); ?>" class="btn btn-danger
        btn-xs pull-right">
                <i class="fa fa-long-arrow-left"></i> Go Back</a>
            Order #<?php echo $fn->disp_id($fn->data['id']); ?></strong>
    </div>
    <div class="panel-body">
        <div class="row m-b-15">
            <div class="col-md-12 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong class="panel-title">Billing Address</strong>
                    </div>
                    <div class="panel-body">
                        <p><span><?php echo $fn->data['b_name']; ?></span>
                            <small><?php echo $fn->data['b_mobile']; ?></small>
                        </p>
                        <p><span><?php echo $fn->data['b_address']; ?></span></p>
                    </div>
                </div>
            </div>
        </div>
     <?php if ($fn->data['products']) { ?>
      <div class="panel panel-default">
          <div class="panel-heading">
              <strong  class="panel-title">Products</strong>
          </div>
          <div class="panel-body">
              <div class="table-responsive">
                  <table class="table table-striped" cellspacing="0" cellpadding="0">
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
                       foreach ($fn->data['products'] as $k => $v) {
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
                               <td align="center"><?php echo $fn->show_price($v['price'], $fn->currency); ?></td>
                               <td align="center"><?php echo $v['qty']; ?></td>
                               <td align="center"
                                   width="18%"><?php echo $fn->show_price($v['total_price'], $fn->data['currency']); ?></td>
                           </tr>
                       <?php } ?>
                      </tbody>
                      <tfoot>
                      <tr>
                          <th colspan="4" class="text-right">Sub Total</th>
                          <th class="text-center"><?php echo $fn->show_price($fn->data['sub_total'], $fn->data['currency']); ?></th>
                      </tr>
                      <tr>
                          <th colspan="4" class="text-right">Grand Total</th>
                          <th class="text-center"><?php echo $fn->show_price($fn->data['total_amt'], $fn->data['currency']); ?></th>
                      </tr>
                      </tfoot>
                  </table>
              </div>
          </div>
      </div>

      <?php
     }
      if ($fn->data['status'] == 'Y') {
       $now = time();
       $update_date = strtotime($fn->data['update_date']);
       $diff = ($now - $update_date);
       $days = floor($diff / (60 * 60 * 24));
       if ($days <= $fn->company['return_days']) {
        ?>
           <div class="text-center mb-20">
               <button type="button" class="btn btn-danger return-btn" data-ajaxify="true" data-url="account/orders"
                       data-page="true" data-type="return"
                       data-app="<?php echo $fn->encrypt_post_data(array('id' => $fn->data['id'])); ?>"
                       data-recid="modal">
                   Return
               </button>
           </div>
        <?php
       }
      }
      if ($fn->varv('history', $fn->data)) {
       ?>
          <div class="panel panel-default">
              <div class="panel-heading">
                  <strong class="panel-title">Order History</strong>
              </div>
              <div class="panel-body" style="max-height: 400px; overflow: auto;">
               <?php foreach ($fn->data['history'] as $v) { ?>
                   <div class="order-history">
                       <div class="head">
                           <span class="timestamp"><?php echo $fn->dt_format($v['add_date'], 'F d, Y h:i A'); ?></span>
                           <span class="status">
                          <label class="badge badge-<?php echo $fn->status_label[$v['status']]; ?>"><?php echo $fn->order_user_status[$v['status']]; ?></label></span>
                       </div>
                       <div class="remarks"><?php echo ($v['subject'] ? '<strong>Subject:</strong> ' . $v['subject'] . ' - ' : '') . $v['remarks']; ?></div>
                       <div class="update-by">- Updated
                           By <?php echo $v['by_admin'] == 'Y' ? 'Admin' : ($v['user_id'] > 0 ? 'Customer' : 'Guest'); ?></div>
                    <?php if ($fn->varv('shipping_id', $v)) { ?>
                        <div class="shipping">
                            <table class="table mt-10 mb-0" cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr>
                                    <th width="15%">Shipped By</th>
                                    <td width="35%"><?php echo $v['shipped_by'] . ($v['tracking_url'] ? ' - <a href="' . $v['tracking_url'] . '" target="_blank">' . $v['tracking_url'] . '</a>' : ''); ?></td>
                                    <th width="15%">Tracking No.</th>
                                    <td width="35%"><?php echo($v['tracking_no'] ? $v['tracking_no'] : '-'); ?></td>
                                </tr>
                                <tr>
                                    <th>Shipping Date</th>
                                    <td><?php echo $v['shipping_date']; ?></td>
                                    <th>Remarks</th>
                                    <td><?php echo($v['shipping_remarks'] ? $fn->make_html($v['shipping_remarks']) : '-'); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                   </div>
               <?php } ?>
              </div>
          </div>
      <?php } ?>
    </div>
</div>