<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\a_nots;
 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';
  if ($fn->post('action') == 'for_admin') {
   try {
    $fn->per_update($fn->post('action'));
    $json = array('success' => true, 'html' => $fn->get_view('button', 'FOR_ADMIN', array('for_admin' => $fn->post('for_admin'), 'id' => $fn->post('id'), 'action' => $fn->post('action'))), 'g_title' => $fn->page['name'], 'g_message' => 'Data has been updated successfully!');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => $fn->page['name'], 'g_message' => $ex->getMessage());
   }
  }
  if ($fn->post('action') == 'for_user') {
   try {
    $fn->per_update($fn->post('action'));
    $json = array('success' => true, 'html' => $fn->get_view('button', 'FOR_USER', array('for_user' => $fn->post('for_user'), 'id' => $fn->post('id'), 'action' => $fn->post('action'))), 'g_title' => $fn->page['name'], 'g_message' => 'Data has been updated successfully!');
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => $fn->page['name'], 'g_message' => $ex->getMessage());
   }
  }
  if ($json) {
   echo $fn->json_encode($json);
  }
  exit();
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
   $_POST['is_prompt'] = $fn->post('is_prompt') ? $fn->post('is_prompt') : 'N';
   ?>
      <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
          <div class="panel-body">
              <div class="row">
                  <div class="form-group col-sm-3">
                      <label for="not_key" class="input-label req"><?php echo _('Key'); ?></label>
                      <input type="text" name="not_key" id="not_key" class="form-control" value="<?php echo $fn->post('not_key'); ?>" required/>
                  </div>
                  <div class="form-group col-sm-3">
                      <label for="not_title" class="input-label req"><?php echo _('Title'); ?></label>
                      <input type="text" name="not_title" id="not_title" class="form-control" value="<?php echo $fn->post('not_title'); ?>" required/>
                  </div>
                  <div class="form-group col-sm-6">
                      <label for="not_subject" class="input-label req"><?php echo _('Email Subject'); ?></label>
                      <input type="text" name="not_subject" id="not_subject" class="form-control" value="<?php echo $fn->post('not_subject'); ?>" required/>
                  </div>
                  <div class="clearfix"></div>
                  <div class="form-group col-sm-3">
                      <label for="is_prompt" class="input-label req"><?php echo _('Prompt'); ?></label>
                      <select name="is_prompt" id="is_prompt" class="form-control">
                       <?php echo $fn->show_list($fn->yes_no, $fn->post('is_prompt'), false); ?>
                      </select>
                  </div>
                  <div class="form-group col-sm-3">
                      <label for="not_date" class="input-label req"><?php echo _('Email date'); ?></label>
                      <input type="text" name="not_date" id="not_date" class="form-control dt-picker" value="<?php echo $fn->post('not_date'); ?>" data-date-only="true" required<?php echo $fn->post('is_prompt') == 'N' ? ' disabled' : ''; ?>/>
                  </div>
                  <div class="form-group col-sm-3">
                      <label for="for_admin" class="input-label req"><?php echo _('For Admin'); ?></label>
                      <select name="for_admin" id="for_admin" class="form-control">
                       <?php echo $fn->show_list($fn->yes_no, $fn->post('for_admin'), false); ?>
                      </select>
                  </div>
                  <div class="form-group col-sm-3">
                      <label for="for_user" class="input-label req"><?php echo _('For User'); ?></label>
                      <select name="for_user" id="for_user" class="form-control">
                       <?php echo $fn->show_list($fn->yes_no, $fn->post('for_user'), false); ?>
                      </select>
                  </div>
                  <div class="clearfix"></div>
                  <div class="form-group col-sm-12">
                      <label for="not_desc" class="control-label req"><?php echo _('Email Template'); ?></label>
                      <textarea name="not_desc" id="not_desc" class="form-control tinymce" rows="10" required><?php echo $fn->post('not_desc'); ?></textarea>
                  </div>
              </div>
          </div>
       <?php include 'inc/panel-footer.php'; ?>
      </form>
  <?php
   ob_start();
  ?>
      <script type="text/javascript">
          $(function () {
              var p = $('select#is_prompt');
              if (!p.length > 0)
                  return;
              p.unbind('change');
              p.change(function () {
                  if ($(this).val() == 'Y') {
                      $('#not_date').removeAttr('disabled');
                  } else {
                      $('#not_date').val('').attr('disabled', true);
                  }
              })
          })
      </script>
  <?php
   $fn->script = ob_get_clean();
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
                       <th><?php echo _('Title'); ?></th>
                       <th><?php echo _('Email Subject'); ?></th>
                       <th width="6%"><?php echo _('Not Date'); ?></th>
                       <th width="5%" class="text-center"><?php echo _('For Admin'); ?></th>
                       <th width="5%" class="text-center"><?php echo _('For User'); ?></th>
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
                            <td><?php echo $row['not_title']; ?></td>
                            <td><?php echo $row['not_subject']; ?></td>
                            <td><?php echo $row['not_date'] != '0000-00-00' ? $fn->date_format($row['not_date'], 'F d, Y') : '-'; ?></td>
                            <td align="center">
                                <div id="for_admin-<?php echo $row['id']; ?>">
                                 <?php echo $fn->get_view('button', 'FOR_ADMIN', array('for_admin' => $row['for_admin'], 'id' => $row['id'], 'action' => 'for_admin')); ?>
                                </div>
                            </td>
                            <td align="center">
                                <div id="for_user-<?php echo $row['id']; ?>">
                                 <?php echo $fn->get_view('button', 'FOR_USER', array('for_user' => $row['for_user'], 'id' => $row['id'], 'action' => 'for_user')); ?>
                                </div>
                            </td>
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
