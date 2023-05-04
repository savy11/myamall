<?php
 $str = '';
 ob_start();
 $flag = $overview = false;
 if ($fn->session('cart')) {
  if ($fn->cart) {
   $flag = true;
   if (isset($type) && $type == 'overview') {
    $overview = true;
   }
  }
 }
 if ($flag) {
  ?>
     <div class="row">
         <div class="col-md-12">
             <table class="cart-table table wow fadeInLeft" data-wow-duration="1s">
                 <thead>
                 <tr>
                  <?php if (!$overview) { ?>
                      <th width="5%">
                          <div class="checkbox checkbox-default" data-toggle="tooltip" title="Select All Item">
                              <input type="checkbox" class="form-control" id="check_all" value="all" data-total="<?php echo count($fn->cart); ?>" data-price="<?php echo $fn->show_price($fn->pay['total']); ?>" data-id="<?php echo implode(',', array_keys($fn->cart)); ?>"/>
                              <label for="check_all"></label>
                          </div>
                      </th>
                      <th width="5%">Remove</th>
                  <?php } ?>
                     <th class="card_product_image">Image</th>
                     <th class="card_product_name">Product Name</th>
                     <th class="card_product_quantity">Quantity</th>
                     <th class="card_product_price">Unit Price</th>
                     <th class="card_product_total">Total</th>
                 </tr>
                 </thead>
                 <tbody>
                 <?php foreach ($fn->cart as $k => $v) { ?>
                     <tr<?php echo $fn->session('checkout', $k) ? ' class="bg-success"' : ''; ?>>
                      <?php if (!$overview) { ?>
                          <td width="10%">
                              <div class="checkbox checkbox-default" data-toggle="tooltip" title="Select Item">
                                  <input type="checkbox" class="form-control check-input" name="check_input[<?php echo $k; ?>]" id="check_input_<?php echo $k; ?>" value="<?php echo $k; ?>" data-price="<?php echo $fn->show_price($v['total_price']); ?>" data-id="<?php echo $k; ?>"<?php echo $fn->session('checkout', $k) ? ' checked' : ''; ?>/>
                                  <label for="check_input_<?php echo $k; ?>"></label>
                              </div>
                          </td>
                          <td>
                              &nbsp;<a href="<?php echo $fn->permalink('product-detail', $v); ?>" data-ajaxify="true"
                                       data-url="cart" data-action="remove"
                                       data-app="<?php echo $fn->encrypt_post_data(array('id' => $k, 'page_url' => $fn->post_get('page_url'))); ?>"
                                       data-recid="cart"><i class="fa fa-trash-o icon-large"></i> </a>
                          </td>
                      <?php } ?>
                         <td class="card_product_image product light">
                          <?php if ($v['sale_id'] > 0) { ?>
                              <div class="product-new">SALE</div>
                          <?php } ?>
                             <a href="<?php echo $fn->permalink('product-detail', $v); ?>">
                                 <img class="lazy"
                                      src="<?php echo $fn->permalink('assets/img/preloader.svg'); ?>"
                                      data-src="<?php echo $fn->get_file($v['product_image'], 0, 0, 100); ?>"
                                      alt="<?php echo $v['product_title']; ?>" title="<?php echo $v['product_title'];
                                 ?>"/>
                             </a>
                         <td class="card_product_name">
                             <a href="<?php echo $fn->permalink('product-detail', $v); ?>"><?php echo $v['product_title']; ?></a><br/>
                          <?php if ($v['color'] || $v['size']) { ?>
                              <p class="size-info">
                               <?php if ($v['color']) { ?>
                                   <span><b>Color:</b> <?php echo $v['color']; ?></span>
                               <?php }
                                if ($v['size']) {
                                 ?>
                                    <span><b>Size:</b> <?php echo $v['size']; ?></span>
                                <?php } ?>
                              </p>
                          <?php } ?>
                         </td>
                         <td class="card_product_quantity" align="center">
                             <input type="number" min="1" max="100" value="<?php echo $v['qty']; ?>" name="qty"
                                    id="qty"
                                    class="form-control styler"
                                    data-ajaxify="true"
                                    data-url="cart" data-action="update" data-event="change"
                                    data-app="<?php echo $fn->encrypt_post_data(array('id' => $k, 'page_url' => $fn->post_get('page_url'))); ?>" <?php echo ($overview) ? ' disabled' : ''; ?>/>
                         </td>
                         <td class="card_product_price"><?php echo $v['special_price'] > 0 ? $fn->show_price($v['special_price']) : $fn->show_price($v['price']); ?></td>
                         <td class="card_product_total"><?php echo $fn->show_price($v['total_price']); ?></td>
                     </tr>
                 <?php } ?>
                 </tbody>
                 <tfoot>
                 <tr>
                     <th colspan="<?php echo $overview ? 4 : 6; ?>">Sub Total</th>
                     <td><strong><?php echo $fn->show_price($fn->pay['sub_total']); ?></strong></td>
                 </tr>
                 <tr>
                     <th colspan="<?php echo $overview ? 4 : 6; ?>">Grand Total</th>
                     <td><strong><?php echo $fn->show_price($fn->pay['total']); ?></strong></td>
                 </tr>
                 </tfoot>
             </table>
             <div class="clearfix"></div>
          <?php if ($overview) { ?>
              <hr/>
              <a href="<?php echo $fn->permalink('cart'); ?>" class="btn-default-1">View Cart</a>
              <a href="<?php echo $fn->permalink('checkout'); ?>" class="btn-default-1 pull-right" data-ajaxify="true" data-url="checkout" data-type="ch_step" data-app="<?php echo $fn->encrypt_post_data(['ch_step' => 3]); ?>" data-recid="checkout">NEXT</a>
          <?php } else { ?>
              <div class="checkout-info">
                  <form class="form-validate" name="checkout-frm" id="checkout-frm" method="post" data-ajax="true" data-url="cart">
                      <div class="row">
                          <div class="col-sm-3">
                              <a href="<?php echo $fn->permalink('products'); ?>" class="btn-default-1">Back to Products</a>
                          </div>
                          <div class="col-sm-3 text-right">
                              <strong>Selected Products: </strong>
                              <span class="count" id="selected-count">0</span>
                              <b>/</b>
                              <span class="count"><?php echo $fn->cart_count(); ?></span>
                          </div>
                          <div class="col-sm-3 text-right">
                              <strong>Price: </strong>
                              <span class="price" id="selected-price"><?php echo $fn->show_price('0'); ?></span>
                          </div>
                          <div class="col-sm-3">
                              <input type="hidden" name="cart_ids" id="cart_ids" value="<?php echo $fn->session('checkout') ? implode(',', $fn->session('checkout')) : ''; ?>"/>
                              <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                              <button type="submit" name="action" value="go_checkout" class="btn-default-1 pull-right ">Checkout</button>
                          </div>
                      </div>
                  </form>
              </div>
          <?php } ?>
         </div>
     </div>
 <?php }
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
?>
