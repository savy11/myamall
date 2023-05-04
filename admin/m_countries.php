<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\m_countries;
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
 ob_start();
?>
<link rel="stylesheet" href="<?php echo $fn->permalink('resources/vendor/magnific-popup/magnific-popup.css', '', true); ?>" type="text/css"/>
<?php
 $fn->style = ob_get_clean();
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
      <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off" enctype="multipart/form-data">
          <div class="panel-body">
              <div class="row">
                  <div class="form-group col-sm-3">
                      <label for="country_name" class="input-label req"><?php echo _('Country Name'); ?></label>
                      <input type="text" name="country_name" id="country_name" class="form-control" value="<?php echo $fn->post('country_name'); ?>" required/>
                  </div>
                  <div class="form-group col-sm-3">
                      <label for="country_code" class="input-label req"><?php echo _('Country Code'); ?></label>
                      <input type="text" name="country_code" id="country_code" class="form-control" value="<?php echo $fn->post('country_code'); ?>" required/>
                  </div>
                  <div class="form-group col-sm-3">
                      <label for="currency_id" class="input-label req"><?php echo _('Currency'); ?></label>
                      <select name="currency_id" id="currency_id" class="form-control" data-placeholder="Currency">
                       <?php echo $fn->show_list($fn->list['currencies'], $fn->post('currency_id'), true); ?>
                      </select>
                  </div>
                  <div class="form-group col-sm-3">
                      <label for="publish" class="input-label req"><?php echo _('Publish'); ?></label>
                      <select name="publish" id="publish" class="form-control" data-placeholder="Publish">
                       <?php echo $fn->show_list($fn->yes_no, $fn->post('publish'), false); ?>
                      </select>
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
                       <th><?php echo _('Country Name'); ?></th>
                       <th><?php echo _('Country Code'); ?></th>
                       <th><?php echo _('Currency'); ?></th>
                       <th width="5%" class="text-center"><?php echo _('Publish'); ?></th>
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
                            <td><?php echo $row['country_name']; ?></td>
                            <td><?php echo $row['country_code']; ?></td>
                            <td><?php echo $row['currency_name']; ?></td>
                            <td align="center">
                                <div id="publish-<?php echo $row['id']; ?>">
                                 <?php echo $fn->get_view('button', 'YES_NO', array('status' => $row['publish'], 'id' => $row['id'], 'action' => 'publish')); ?>
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
<?php ob_start(); ?>
<script type="text/javascript" src="<?php echo $fn->permalink('resources/vendor/magnific-popup/jquery.magnific-popup.js', '', true); ?>"></script>
<?php
 $fn->script = ob_get_clean();
 include 'inc/footer.php';
 include 'inc/foot.php';
?>
