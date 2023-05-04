<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\a_emails_user;
if ($fn->get('action') == 'delete') {
 try {
  $fn->delete();
  $fn->session_msg('Data has been deleted successfully!', 'success');
 } catch (Exception $ex) {
  $fn->session_msg($ex->getMessage(), 'error');
 }
 $fn->return_ref();
}
if ($fn->post('btn_save') == 'save') {
 try {
  $fn->insert();
  $fn->session_msg('Data has been saved successfully!', 'success');
  $fn->return_ref();
 } catch (Exception $ex) {
  $fn->session_msg($ex->getMessage(), 'error');
 }
}
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
 if (($fn->per_add && $fn->get('action') == 'add') || ($fn->per_edit && $fn->get('action') == 'edit')) {
  if ($fn->get('action') == 'edit' && $fn->get('id')) {
   $fn->select();
  }
  ?>
  <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
   <div class="panel-body">
    <div class="row">
     <div class="form-group col-sm-8">
      <label for="email_id" class="input-label req"><?php echo _('Email Template'); ?></label>
      <select name="email_id" id="email_id" class="form-control" data-placeholder="Email Template">
       <?php if ($fn->user['group_id'] == 1) { ?>
        <option value="0">All Emails</option>
       <?php } echo $fn->show_list($fn->list['emails'], $fn->post('email_id'), false); ?>
      </select>
     </div>
     <div class="form-group col-sm-4">
      <label for="type_id" class="input-label req"><?php echo _('Sending Type'); ?></label>
      <select name="type_id" id="type_id" class="form-control" data-placeholder="Sending Type" required>
       <?php echo $fn->show_list($fn->sending_types, $fn->post('type_id'), true); ?>
      </select>
     </div> 
     <div class="clearfix"></div>
     <div class="form-group col-sm-12">
      <label for="emails" class="control-label req"><?php echo _('Emails'); ?></label>
      <textarea name="emails" id="emails" class="form-control tagsinput" rows="3" data-default-text="Add a email" required><?php echo $fn->post('emails'); ?></textarea>
     </div>
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
        <th><?php echo _('Email Template'); ?></th>
        <th width="10%"><?php echo _('Sending Type'); ?></th>
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
         <td><?php echo ($row['email_id'] == 0 ? 'All Emails' : $row['email_title']); ?></td>
         <td><?php echo $fn->sending_types[$row['type_id']]; ?></td>         
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
