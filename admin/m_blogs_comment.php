<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\m_blogs_comment;
 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';
  if ($fn->post('action') == 'publish') {
   try {
    $fn->publish();
    $status = 'unpublished';
    if ($fn->post('publish') == 'Y') {
     $status = 'published';
    }
    $json = array('success' => true, 'html' => $fn->get_view('button', 'YES_NO', array('status' => $fn->post('publish'), 'id' => $fn->post('id'), 'action' => $fn->post('action'))), 'g_title' => $fn->page['name'], 'g_message' => 'Data has been ' . $status . ' successfully!');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => $fn->page['name'], 'g_message' => $ex->getMessage());
   }
  }
  echo $fn->json_encode($json);
  exit();
 }
 if ($fn->post('btn_save') == 'save' || $fn->post('btn_update') == 'update') {
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
 if (($fn->per_edit && in_array($fn->get('action'), ['view', 'edit']))) {
  $fn->select();
  ?>
     <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off"
           enctype="multipart/form-data">
         <input type="hidden" name="blog_id" value="<?php echo $fn->post('blog_id'); ?>"/>
         <div class="panel panel-default">
          <?php include 'inc/panel-head.php'; ?>
             <div class="panel-body">
                 <div class="row">
                     <div class="col-md-6">
                         <p><strong>Blog : </strong><a href="<?php echo $fn->permalink('blog-detail', $fn->post()); ?>"
                                                       target="_blank"><?php echo $fn->post('blog_title'); ?></a></p>
                         <p><strong>Name : </strong><?php echo $fn->post('name'); ?></p>
                         <p><strong>Email : </strong><a
                                     href="mailto:<?php echo $fn->post('email'); ?>"><?php echo $fn->post('email'); ?></a>
                             <label class="label label-<?php echo $fn->post('verified') == 'Y' ? 'success' : 'danger'; ?>"><?php echo $fn->post('verified') == 'Y' ? 'Verified' : 'Not Verified'; ?></label>
                         </p>
                         <p><strong>Phone : </strong><?php echo $fn->post('phone'); ?></p>
                         <p><strong>Website URL : </strong><?php echo $fn->post('website'); ?></p>
                     </div>
                     <div class="col-md-6">
                         <p><strong>Date
                                 : </strong> <?php echo $fn->dt_format($fn->post('add_date'), 'F d, Y h:i A'); ?></p>
                         <p><strong>IP Address : </strong><?php echo $fn->post('ip'); ?></p>
                         <p><strong>Browser : </strong><?php echo $fn->post('browser'); ?></p>
                         <p><strong>Operating System : </strong><?php echo $fn->post('os'); ?></p>
                         <p><strong>Publish : </strong><label
                                     class="label label-<?php echo $fn->post('publish') == 'Y' ? 'success' : 'danger'; ?>"><?php echo $fn->yes_no[$fn->post('publish')]; ?></label>
                         </p>
                     </div>
                 </div>
             </div>
         </div>
         <div class="panel panel-default panel-heading-fullwidth panel-borders">
             <div class="panel-heading">
                 <span class="title">Comments</span>
             </div>
             <div class="panel-body" style="padding-bottom: 15px;">
                 <div class="comments">
                     <div class="message">
                         <div class="message-body">
                             <strong>Comment By : </strong><?php echo $fn->post('name'); ?>
                             <p><?php echo $fn->make_html($fn->post('comment')); ?></p>
                         </div>
                         <small class="time"> <i class="s7-clock"></i>
                             on <?php echo $fn->dt_format($fn->post('add_date'), 'F d, Y h:i A'); ?> <i
                                     class="s7-check text-success"></i> </small>
                     </div>
                  <?php
                   if ($fn->post('verified') == 'Y' && $fn->post('publish') == 'Y') {
                   if ($fn->replies) {
                    foreach ($fn->replies as $k => $v) {
                     ?>
                        <div class="message<?php echo $v['type'] ? '' : ' front'; ?>">
                            <div class="message-body">
                                <strong>Reply By : </strong><?php echo $v['name']; ?>
                                <p><?php echo $fn->make_html($v['comment']); ?></p>
                             <?php if ($v['type'] != '1') { ?>
                                 <div id="publish-<?php echo $v['id']; ?>">
                                  <?php echo $fn->get_view('button', 'YES_NO', array('status' => $v['publish'], 'id' => $v['id'], 'action' => 'publish')); ?>
                                 </div>
                             <?php } ?>
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
              <?php if ($fn->get('action') == 'edit') { ?>
                  <div class="clearfix"></div>
                  <div class="add-comment">
                      <label for="reply" class="control-label req">Add Reply</label>
                      <textarea name="reply" id="reply" class="form-control" rows="3"
                                required><?php echo $fn->make_html($fn->post('reply')); ?></textarea>
                  </div>
               <?php
              }
               } ?>
             </div>
          <?php
           if ($fn->post('verified') == 'Y' && $fn->post('publish') == 'Y' && $fn->get('action') == 'edit') {
            include_once admin_path . 'inc' . ds . 'panel-footer.php';
           }
          ?>
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
                          <th><?php echo _('Date'); ?></th>
                          <th><?php echo _('Name'); ?></th>
                          <th><?php echo _('Email'); ?></th>
                          <th><?php echo _('Phone'); ?></th>
                          <th><?php echo _('Comment'); ?></th>
                          <th width="10%" class="text-center"><?php echo _('Publish'); ?></th>
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
                               <td><?php echo $row['add_date']; ?></td>
                               <td><?php echo $row['name']; ?></td>
                               <td><?php echo $row['email']; ?></td>
                               <td><?php echo $row['phone']; ?></td>
                               <td><?php echo $row['comment']; ?></td>
                               <td align="center">
                                <?php if ($row['type'] != '1') { ?>
                                    <div id="publish-<?php echo $row['id']; ?>">
                                     <?php echo $fn->get_view('button', 'YES_NO', array('status' => $row['publish'], 'id' => $row['id'], 'action' => 'publish')); ?>
                                    </div>
                                <?php } ?>
                               </td>
                            <?php ob_start(); ?>
                               <a href="<?php echo $fn->get_action_url('view', $row['id']); ?>"
                                  class="btn btn-sm btn-info"><span class="icon
        s7-note"></span> <?php
                                 echo _('View'); ?></a>
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
