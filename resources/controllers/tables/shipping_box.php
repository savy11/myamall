<?php
$str = '';
ob_start();

if ($this->fn->varv('shipping_id', $options)) {
 ?>
 <div style="background: #eaeaea; height: 1px; margin: 15px 0px; width: 100%;"></div>
 <table width="100%" cellspacing="0" cellpadding="5" style="margin-top:15px;">
  <tr>
   <th colspan="2" style="background-color: #388396; color: #fff; border: 1px solid #388396; text-align: left; padding: 5px 10px;">Shipping Details</th>
  </tr>
  <tr>
   <td width="20%" style="background-color: #f5f5f5; border-left: 1px solid #e4e4e4; border-right: 1px solid #e4e4e4; padding: 5px 10px;"><strong>Shipped By</strong></td>
   <td style="border-right: 1px solid #e4e4e4; padding: 5px 10px;"><?php echo $this->fn->make_html($options['shipped_by']); ?></td>
  </tr>
  <tr>
   <td style="background-color: #f5f5f5; border-left: 1px solid #e4e4e4; border-right: 1px solid #e4e4e4; border-top: 1px solid #e4e4e4; padding: 5px 10px;"><strong>Tracking No.</strong></td>
   <td style="border-top: 1px solid #e4e4e4; border-right: 1px solid #e4e4e4; padding: 5px 10px;"><?php echo $this->fn->make_html($options['tracking_no']); ?></td>
  </tr>
  <tr>
   <td style="background-color: #f5f5f5; border-left: 1px solid #e4e4e4; border-right: 1px solid #e4e4e4; border-top: 1px solid #e4e4e4; padding: 5px 10px;"><strong>Shipping Date</strong></td>
   <td style="border-top: 1px solid #e4e4e4; border-right: 1px solid #e4e4e4; padding: 5px 10px;"><?php echo $this->fn->make_html($options['shipping_date']); ?></td>
  </tr>
  <tr>
   <td style="background-color: #f5f5f5; border-bottom: 1px solid #e4e4e4; border-left: 1px solid #e4e4e4; border-right: 1px solid #e4e4e4; border-top: 1px solid #e4e4e4; padding: 5px 10px;"><strong>Remarks</strong></td>
   <td style="border-bottom: 1px solid #e4e4e4; border-top: 1px solid #e4e4e4; border-right: 1px solid #e4e4e4; padding: 5px 10px;"><?php echo ($options['shipping_remarks'] ? $this->fn->make_html($options['shipping_remarks']) : '-'); ?></td>
  </tr>
 </table>
 <?php
}
$str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
return $str;
