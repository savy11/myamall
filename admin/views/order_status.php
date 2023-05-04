<?php
 $str = '';
 $status = ($fn->post('status') ? $fn->post('status') : $row['status']);
 $id = ($fn->post('id') ? $fn->post('id') : $row['id']);
 ob_start();
?>
<div class="btn-group">
 <button type="button" class="btn btn-<?php echo $fn->status_label[$status]; ?> btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <?php echo $fn->order_status[$status]; ?> <span class="caret"></span>
 </button>
 <ul class="dropdown-menu right-arrow right-pos mw-100">
  <?php
   foreach ($fn->order_status as $k => $v) {
    if ($k == $status) {
     ?>
     <li class="active"><a><?php echo $v; ?></a></li>
      <?php } else { ?>
     <li><a href="#" data-ajaxify="true" data-url="orders" data-page="true" data-type="order_status" data-app="<?php echo $fn->encrypt_post_data(array('id' => $id, 'status' => $k)); ?>" data-recid="<?php echo in_array($v ,array('Shipped','Returned')) ? 'modal' : 'order_status_' . $id; ?>" data-confirm="Are you sure you want to update the order status?"><?php echo $v; ?></a></li>
     <?php
    }
   }
  ?>
 </ul>
</div>
<?php
 return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 