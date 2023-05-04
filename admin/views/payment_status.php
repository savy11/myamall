<?php
 $str = '';
 $status = ($fn->post('payment_status') ? $fn->post('payment_status') : $row['payment_status']);
 $id = ($fn->post('id') ? $fn->post('id') : $row['id']);
 ob_start();
?>
<div class="btn-group">
 <button type="button" class="btn btn-<?php echo $fn->status_label[$status]; ?> btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <?php echo $fn->payment_status[$status]; ?> <span class="caret"></span>
 </button>
 <ul class="dropdown-menu right-arrow right-pos mw-100">
  <?php
   foreach ($fn->payment_status as $k => $v) {
    if (in_array($k, array('Y', 'R')) === false) {
     continue;
    }
    if ($k == $status) {
     ?>
     <li class="active"><a><?php echo $v; ?></a></li>
      <?php } else { ?>
     <li><a href="#" data-ajaxify="true" data-url="orders" data-page="true" data-type="payment_status" data-app="<?php echo $fn->encrypt_post_data(array('id' => $id, 'payment_status' => $k)); ?>" data-recid="modal" data-confirm="Are you sure you want to update the payment status?"><?php echo $v; ?></a></li>
     <?php
    }
   }
  ?>
 </ul>
</div>
<?php
 return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 