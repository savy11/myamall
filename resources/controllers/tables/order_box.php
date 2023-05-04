<?php
 $str = '';
 ob_start();
 $products = $this->db->selectall("SELECT op.*, p.product_title FROM orders_product op LEFT OUTER JOIN m_products p ON op.product_id=p.id WHERE op.order_id='" . $options['id'] . "'");
 if ($products) {
  ?>
     <table width="100%" cellpadding="5" cellspacing="0"
            style="border: 1px solid #e4e4e4; border-bottom-width: 2px; margin-top: 10px; font-size: 11px;">
         <tbody>
         <tr>
             <td width="33.33333333333333%" align="center" style="border-right: 1px solid #e4e4e4;">Order Date:
                 <strong><?php echo $options['add_date']; ?></strong></td>
             <td width="33.33333333333333%" align="center" style="border-right: 1px solid #e4e4e4;">Order Status:
                 <strong><?php echo $this->fn->order_status[$options['status']]; ?></strong></td>
             <td width="33.33333333333333%" align="center">Payment Status:
                 <strong><?php echo $this->fn->payment_status[$options['payment_status']]; ?></strong></td>
         </tr>
         </tbody>
     </table>
     <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 15px;">
         <tr>
             <td width="100%" valign="top">
                 <table width="100%" cellpadding="10" cellspacing="0" style="font-size: 12px;">
                     <tbody>
                     <tr>
                         <th width="100%" align="left" style="border: 1px solid #e4e4e4; background: #f1f1f1;">Billing
                             Address
                         </th>
                     </tr>
                     <tr>
                         <td width="100%" align="left" style="border: 1px solid #e4e4e4; border-top: 0;">
                             <span style="font-size: 13px; font-weight: 700; margin-right: 5px;"><?php echo $options['b_name']; ?></span>
                             <span style="font-size: 11px;"><?php echo $options['b_mobile']; ?></span>
                             <p style="margin-top: 5px; margin-bottom: 0;"><?php echo $options['b_address']; ?></p>
                         </td>
                     </tr>
                     </tbody>
                 </table>
             </td>
         </tr>
     </table>
     <table width="100%" cellspacing="0" border="0" cellpadding="5"
            style="border: 1px solid #e4e4e4; margin-top: 15px; font-size: 12px;">
         <tbody>
         <tr style="background-color:#fff;">
             <th width="8%" style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4;">#</th>
             <th style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; text-align: left; padding: 5px 10px;">
                 Product
             </th>
             <th width="15%"
                 style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; text-align: center;">Price
             </th>
             <th width="15%"
                 style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; text-align: center;">Quantity
             </th>
             <th width="15%" style="border-bottom: 1px solid #e4e4e4; text-align: center;">Total Price</th>
         </tr>
         <?php
          foreach ($products as $k => $v) {
           ?>
              <tr>
                  <td align="center"
                      style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4;"><?php echo($k + 1); ?></td>
                  <td align="center" style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; text-align:
        left; padding: 5px 10px;"><?php echo $this->fn->make_html($v['product_title']); ?>
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
                  <td align="center"
                      style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; text-align: center;"><?php echo $this->fn->show_price($v['price']); ?></td>
                  <td align="center"
                      style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; text-align: center;"><?php echo $this->fn->make_html($v['qty']); ?></td>
                  <td style="border-bottom: 1px solid #e4e4e4; text-align: center;"><?php echo $this->fn->show_price($v['total_price']); ?></td>
              </tr>
          <?php } ?>
         <tr style="background-color: #fff;">
             <td colspan="4"
                 style="border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; text-align: right; padding: 5px 10px;">
                 <strong>Sub Total</strong></td>
             <td width="20%" style="border-bottom: 1px solid #e4e4e4; text-align: center;">
                 <strong><?php echo $this->fn->show_price($options['sub_total']); ?></strong></td>
         </tr>
         <tr style="background-color: #fff;">
             <td colspan="4" style="border-right: 1px solid #e4e4e4; text-align: right; padding: 5px 10px;"><strong>Grand
                     Total</strong></td>
             <td width="20%" style="text-align: center;">
                 <strong><?php echo $this->fn->show_price($options['total_amt']); ?></strong></td>
         </tr>
         </tbody>
     </table>
  <?php
 }
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
