<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\a_pers;
if ($fn->post('btn_update') == 'update') {
 try {
  $fn->update();
  $fn->session_msg('Data has been updated successfully!', 'success');
  $fn->return_ref();
 } catch (Exception $ex) {
  $fn->session_msg($ex->getMessage(), 'error');
 }
}
include 'inc/head.php';
include 'inc/header.php';
?>
<div class="panel panel-default">
 <?php
 include 'inc/panel-head.php';
 if ($fn->per_edit && $fn->get('action') == 'edit') {
  if ($fn->get('action') == 'edit' && $fn->get('id')) {
   $fn->select();
  }
  ?>
  <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
   <div class="panel-body pa-0">
    <div class="table-responsive permission">
     <table class="table table-striped mb-0">
      <?php
      if (is_array($fn->post('main')) && count($fn->post('main')) > 0) {
       foreach ($fn->post('main') as $row) {
        $sub = $fn->post('sub', $row['id']);
        if (is_array($sub) && count($sub) > 0) {
         $type = 'row';
         include 'inc/pers.php';
         foreach ($sub as $row2) {
          if ($fn->user['group_id'] == 1 || ($fn->user['group_id'] != 1 && $fn->varv($row2['form_code'], $fn->pers, 'V') == 1)) {
           $type = 'row2';
           include 'inc/pers.php';
          }
         }
        }
       }
      }
      if (is_array($fn->post('nots')) && count($fn->post('nots')) > 0) {
       if ($fn->user['group_id'] == 1 || ($fn->user['group_id'] != 1 && count($fn->post('nots')) > 0)) {
        ?>
        <tr class="main-tr">
         <th><strong>Emails</strong></th>
         <th width="15%"></th>
         <th width="15%"></th>
         <th width="15%"></th>
         <th width="15%"></th>
        </tr>
        <?php
       }
       foreach ($fn->post('nots') as $row) {
        if ($fn->user['group_id'] == 1 || ($fn->user['group_id'] != 1 && $fn->varv($row['not_key'], $fn->nots) != '')) {
         $type = 'not_checkbox';
         include 'inc/pers.php';
        }
       }
      }
      ?>
     </table>
    </div>
   </div>
   <?php include 'inc/panel-footer.php'; ?>
  </form>
  <?php
 } else {
  $fn->select_all();
  ?>
  <div class="panel-body">
   <?php if ($fn->data) {
    ?> 
    <div class="table-responsive">
     <table class="table table-striped table-bordered">
      <thead>
       <tr>
        <th width="5%" class="text-center hidden-xs"><?php echo _('#'); ?></th>
        <th><?php echo _('Group Name'); ?></th>
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
         <td><?php echo $row['group_name']; ?></td>   
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
 <?php }
 ?>
</div>
<?php
include 'inc/footer.php';
include 'inc/foot.php';
?>
