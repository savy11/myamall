<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\v_contacts;
 if ($fn->post('btn_save') == 'save') {
  try {
   $fn->insert();
   $fn->session_msg('Data has been saved successfully!', 'success');
   $fn->return_ref();
  } catch (Exception $ex) {
   $fn->session_msg($ex->getMessage(), 'error');
  }
 }
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
 if (($fn->per_edit && $fn->get('action') == 'view') || ($fn->per_edit && $fn->get('action') == 'edit')) {
  $fn->select();
  ?>
     <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off"
           enctype="multipart/form-data">
         <div class="panel panel-default">
          <?php include 'inc/panel-head.php'; ?>
             <div class="panel-body">
                 <div class="row">
                     <div class="col-md-6">
                         <p><strong>Name : </strong><?php echo $fn->post('display_name'); ?></p>
                         <p><strong>Email : </strong><a
                                     href="mailto:<?php echo $fn->post('email'); ?>"><?php echo $fn->post('email'); ?></a>
                         </p>
                         <p><strong>Phone : </strong><?php echo $fn->post('contact_no'); ?></p>
                         <p><strong>Subject : </strong><?php echo $fn->post('subject'); ?></p>
                     </div>
                     <div class="col-md-6">
                         <p><strong>Date
                                 : </strong> <?php echo $fn->dt_format($fn->post('add_date'), 'F d, Y h:i A'); ?></p>
                         <p><strong>IP : </strong><?php echo $fn->post('ip'); ?></p>
                         <p><strong>Browser : </strong><?php echo $fn->post('browser'); ?></p>
                         <p><strong>Operating System : </strong><?php echo $fn->post('os'); ?></p>
                     </div>
                     <div class="col-md-12">
                         <p><strong>Message : </strong><?php echo $fn->post('message'); ?></p>
                     </div>
                 </div>
             </div>
         </div>
         <div class="panel panel-default panel-heading-fullwidth panel-borders">
             <div class="panel-heading">
                 <span class="title"><strong>Contact Reply</strong></span>
             </div>
             <div class="panel-body" style="padding-bottom: 15px;">
                 <div class="comments">
                     <div class="message">
                         <div class="message-body">
                             <strong>Reply To : </strong><?php echo $fn->post('display_name'); ?>
                             <p><?php echo $fn->make_html($fn->post('message')); ?></p>
                         </div>
                         <small class="time"> <i class="s7-clock"></i>
                             on <?php echo $fn->dt_format($fn->post('add_date'), 'F d, Y h:i A'); ?> <i
                                     class="s7-check text-success"></i> </small>
                     </div>
                  <?php
                   if ($fn->replies) {
                    foreach ($fn->replies as $k => $v) {
                     ?>
                        <div class="message<?php echo $v['type'] ? '' : ' front'; ?>">
                            <div class="message-body">
                                <strong>Reply By : </strong><?php echo $v['display_name']; ?>
                                <p><?php echo $fn->make_html($v['message']); ?></p>
                            </div>
                            <small class="time"> <i class="s7-clock"></i>
                                on <?php echo $fn->dt_format($v['add_date'], 'F d, Y h:i A'); ?> <i
                                        class="s7-check text-success"></i> </small>
                        </div>
                     <?php
                    }
                   }
                  ?>
                 </div>
                 <div class="clearfix"></div>
              <?php if ($fn->get('action') == 'edit') { ?>
                  <div class="add-comment">
                      <label for="reply" class="control-label req">Add Reply</label>
                      <textarea name="reply" id="reply" class="form-control" rows="3"
                                required><?php echo $fn->make_html($fn->post('reply')); ?></textarea>
                  </div>
              <?php } ?>
             </div>
          <?php if ($fn->get('action') == 'edit') {
           include_once admin_path . 'inc' . ds . 'panel-footer.php';
          } ?>
         </div>
         </div>
     </form>
  <?php
 } else {
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
                          <th><?php echo _('Display Name'); ?></th>
                          <th><?php echo _('Email'); ?></th>
                          <th><?php echo _('Contact No.'); ?></th>
                          <th><?php echo _('Subject'); ?></th>
                          <th><?php echo _('Message'); ?></th>
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
                               <td><?php echo $row['display_name']; ?></td>
                               <td><?php echo $row['email']; ?></td>
                               <td><?php echo $row['contact_no'] != '' ? $row['contact_no'] : '-'; ?></td>
                               <td><?php echo $row['subject'] != '' ? $row['subject'] : '-'; ?></td>
                               <td><?php echo $row['message'] != '' ? $row['message'] : '-'; ?></td>
                            <?php ob_start(); ?>
                               <a href="<?php echo $fn->get_action_url('view', $row['id']); ?>"
                                  class="btn btn-info btn-sm"><span class="icon s7-note"></span>
                                <?php echo _('View'); ?></a>
                            <?php
                             $fn->actions_multi = ob_get_clean();
                             include 'inc/actions.php';
                            ?>
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
 }
 include 'inc/footer.php';
 include 'inc/foot.php';
?>
