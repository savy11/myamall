<?php
 $str = '';
 ob_start();
 $flag = false;
 if ($fn->session('cart')) {
  $fn->tmp_cart();
  if ($fn->cart) {
   $flag = true;
  }
 }
 if ($flag) {
  foreach ($fn->cart as $k => $v) {
   ?>
      <div class="item">
          <img src="<?php echo $fn->permalink('assets/img/preloader.svg'); ?>"
               data-src="<?php echo $fn->get_file($v['product_image'], 0, 0, 70); ?>"
               alt="<?php echo $v['product_title']; ?>"
               class="pull-left lazy"/>
          <div>
              <a href="<?php echo $fn->permalink('product-detail', $v); ?>"
                 class="text-ellipsis"><?php echo $v['product_title']; ?></a>
           <?php $price = $v['special_price'] > 0 ? $fn->show_price($v['special_price']) : $fn->show_price($v['price']); ?></span>
              <p><?php echo $price; ?>&nbsp;<strong>x <?php echo $v['qty']; ?></strong></p>
           <?php if ($v['color'] || $v['size']) { ?>
               <p class="size-info text-ellipsis">
                <?php if ($v['color']) { ?>
                    <span><b>Color:</b> <?php echo $v['color']; ?></span>
                <?php }
                 if ($v['size']) {
                  ?>
                     <span><b>Size:</b> <?php echo $v['size']; ?></span>
                 <?php } ?>
               </p>
           <?php } ?>
          </div>
          <a href="<?php echo $fn->permalink(); ?>" class="trash" data-ajaxify="true" data-url="cart"
             data-action="remove"
             data-app="<?php echo $fn->encrypt_post_data(['id' => $k]); ?>">
              <i class="fa fa-trash-o pull-left"></i></a>
      </div>
  <?php } ?>
     <div class="total pull-left">
         <table>
             <tbody class="pull-right">
             <tr class="color-active">
                 <td><b>Grand Total:</b></td>
                 <td><?php echo $fn->show_price($fn->pay['sub_total']); ?></td>
             </tr>
             </tbody>
         </table>
         <a href="<?php echo $fn->permalink('checkout'); ?>" class="btn-read pull-right">Checkout</a>
         <a href="<?php echo $fn->permalink('cart'); ?>" class="btn-read pull-right">View Cart</a>
     </div>
 <?php } else { ?>
     <div class="alert alert-info m-b-0">No products found in cart.</div>
 <?php }
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
?>
