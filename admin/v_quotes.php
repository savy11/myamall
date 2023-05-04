<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\v_quotes;
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
                     <th width="10%"><?php echo _('Date'); ?></th>
                     <th><?php echo _('Quote'); ?></th>
                     <th width="10%"><?php echo _('IP Address'); ?></th>
                     <th width="10%"><?php echo _('Browser'); ?></th>
                     <th width="10%"><?php echo _('OS'); ?></th>
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
                          <td><?php echo $fn->date_format($row['add_date'], 'F d Y H:i A'); ?></td>
                          <td><?php echo $row['quote']; ?></td>
                          <td><?php echo $row['ip']; ?></td>
                          <td><?php echo $row['browser']; ?></td>
                          <td><?php echo $row['os']; ?></td>
                       <?php include 'inc/actions.php'; ?>
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
 include 'inc/footer.php';
 include 'inc/foot.php';
?>
