<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\users;
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
  if ($fn->post('action') == 'verified') {
    try {
      $fn->verify();
      $status = 'unverified';
      if ($fn->post('verified') == 'Y') {
        $status = 'verified';
      }
      $json = array('success' => true, 'html' => $fn->get_view('button', 'VERIFIED', array('verified' => $fn->post('verified'), 'id' => $fn->post('id'), 'action' => $fn->post('action'))), 'g_title' => $fn->page['name'], 'g_message' => 'Data has been ' . $status . ' successfully!');
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
    ?>
    <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
      <div class="panel-body">
        <div class="row">
          <div class="form-group col-sm-3">
            <label for="first_name" class="input-label req"><?php echo _('First Name'); ?></label>
            <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $fn->post('first_name'); ?>" required />
          </div>
          <div class="form-group col-sm-3">
            <label for="last_name" class="input-label req"><?php echo _('Last Name'); ?></label>
            <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo $fn->post('last_name'); ?>" required />
          </div>     
          <div class="form-group col-sm-3">
            <label for="publish" class="input-label req"><?php echo _('Publish'); ?></label>
            <select name="publish" id="publish" class="form-control">
              <?php echo $fn->show_list($fn->yes_no, $fn->post('publish'), false); ?>
            </select>
          </div>          
          <div class="form-group col-sm-3">
            <label for="verified" class="input-label req"><?php echo _('Verified'); ?></label>
            <select name="verified" id="verified" class="form-control">
              <?php echo $fn->show_list($fn->yes_no, $fn->post('verified'), false); ?>
            </select>
          </div>          
          <div class="clearfix"></div>
          <div class="form-group col-sm-4">
            <label for="email" class="input-label req"><?php echo _('Email'); ?></label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo $fn->post('email'); ?>" required />
          </div>
          <div class="form-group col-sm-4">
            <label for="password" class="input-label req"><?php echo _('Password'); ?></label>
            <div class="input-group">
              <input type="password" name="password" id="password" class="form-control" value="<?php echo $fn->post('password'); ?>" rel="password" required />
              <div class="input-group-btn">
                <button type="button" class="btn btn-info" data-show-pass="password" tabindex="-1"><span class="icon s7-key"></span></button>
              </div>
            </div>
          </div>
          <div class="form-group col-sm-2">
            <label for="mobile_no" class="input-label"><?php echo _('Mobile No.'); ?> <small>(Optional)</small></label>
            <input type="text" name="mobile_no" id="mobile_no" class="form-control" value="<?php echo $fn->post('mobile_no'); ?>" />
          </div>
            <div class="form-group col-sm-2">
                <label for="dob" class="input-label"><?php echo _('Date of Birth'); ?> <small>(Optional)</small></label>
                <input type="text" name="dob" id="dob" class="form-control dt-picker" data-date-only="true" value="<?php echo $fn->post('dob'); ?>" />
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
                <th><?php echo _('Date'); ?></th>
                <th><?php echo _('Name'); ?></th>
                <th><?php echo _('Email'); ?></th>
                <th><?php echo _('Date of Birth'); ?></th>
                <th><?php echo _('Mobile No'); ?></th>
                <th width="5%" class="text-center"><?php echo _('Verified'); ?></th>
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
                  <td><?php echo $fn->date_format($row['add_date'], 'F d, Y'); ?></td>
                  <td><?php echo $row['display_name']; ?></td>
                  <td><?php echo $row['email']; ?></td>
                  <td><?php echo $row['dob'] != '0000-00-00' ? $fn->date_format($row['dob'], 'F d, Y') : '-'; ?></td>
                  <td><?php echo $row['mobile_no']; ?></td>
                  <td align="center">
                    <div id="verified-<?php echo $row['id']; ?>">
                      <?php echo $fn->get_view('button', 'VERIFIED', array('verified' => $row['verified'], 'id' => $row['id'], 'action' => 'verified')); ?>
                    </div>
                  </td>
                  <td align="center">
                    <div id="publish-<?php echo $row['id']; ?>">
                      <?php echo $fn->get_view('button', 'YES_NO', array('status' => $row['publish'], 'id' => $row['id'], 'action' => 'publish')); ?>
                    </div>
                  </td>
                  <?php /* <td align="center"><button type="button" class="btn btn-<?php echo $fn->yes_no_label[$row['verified']]; ?> btn-sm prevent"><?php echo $fn->yes_no[$row['verified']]; ?></button></td>
                    <td align="center"><button type="button" class="btn btn-<?php echo $fn->yes_no_label[$row['publish']]; ?> btn-sm prevent"><?php echo $fn->yes_no[$row['publish']]; ?></button></td> */ ?>
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
      <?php } ?>
    </div>
  <?php } ?>
</div>
<?php
include 'inc/footer.php';
include 'inc/foot.php';
?>
