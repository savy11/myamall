<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\a_forms;
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
    $_POST['display_icon'] = $fn->post('display_icon') != '' ? $fn->post('display_icon') : 's7-settings';
    ?>
    <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
      <div class="panel-body">
        <div class="row">
          <div class="form-group col-sm-3">
            <label for="form_title" class="input-label req"><?php echo _('Form Title'); ?></label>
            <input type="text" name="form_title" id="form_title" class="form-control" value="<?php echo $fn->post('form_title'); ?>" required />
          </div>
          <div class="form-group col-sm-3">
            <label for="form_code" class="input-label req"><?php echo _('Form Code'); ?></label>
            <?php if ($fn->user['group_id'] != 1) { ?>
              <input type="text" class="form-control" value="<?php echo $fn->post('form_code'); ?>" readonly disabled />
            <?php } else { ?>
              <input type="text" name="form_code" id="form_code" class="form-control" value="<?php echo $fn->post('form_code'); ?>" placeholder="Add # if want blank" required />
            <?php } ?>
          </div>
          <div class="form-group col-sm-3">
            <label for="seo" class="input-label req"><?php echo _('SEO'); ?></label>
            <select name="seo" id="seo" class="form-control" data-placeholder="Yes/No" data-allow-clear="true" required>
              <?php echo $fn->show_list($fn->yes_no, $fn->post('seo'), true); ?>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <label for="parent_id" class="input-label"><?php echo _('Parent Module'); ?> <small>(Optional)</small></label>
            <select name="parent_id" id="parent_id" class="form-control" data-placeholder="Parent Module" data-allow-clear="true">
              <?php echo $fn->show_list($fn->list['parents'], $fn->post('parent_id'), true); ?>
            </select>
          </div>
          <div class="clearfix"></div>
          <div class="form-group col-sm-4">
            <label for="display_icon" class="input-label req"><?php echo _('Icon'); ?></label>
            <div class="input-group">
              <div class="input-group-addon"><span class="default-icon"><span class="icon <?php echo $fn->post('display_icon'); ?>"></span></span></div>
              <select name="display_icon" id="display_icon" class="form-control" data-placeholder="Display Icon" required>
                <?php
                if ($icons = $fn->get_icons('assets/css/icons.css')) {
                  foreach ($icons as $k => $v) {
                    ?>
                    <option value="<?php echo $k; ?>"<?php echo ($fn->post('display_icon') == $k) ? 'selected' : ''; ?>><?php echo $v; ?> </option>
                    <?php
                  }
                }
                if ($icons = $fn->get_icons('assets/css/themify-icons.css', 'ti')) {
                  foreach ($icons as $k => $v) {
                    ?>
                    <option value="<?php echo $k; ?>"<?php echo ($fn->post('display_icon') == $k) ? 'selected' : ''; ?>><?php echo $v; ?> </option>
                    <?php
                  }
                }
                ?>
              </select>
            </div>
          </div>     
          <div class="form-group col-sm-4">
            <label for="display_no" class="input-label req"><?php echo _('Display No'); ?></label>
            <input type="number" name="display_no" id="display_no" class="form-control" min="0" value="<?php echo $fn->post('display_no'); ?>" required />
          </div>   
          <div class="form-group col-sm-4">
            <label for="per_level" class="input-label"><?php echo _('Permissions'); ?> <small>(Optional)</small></label>      
            <select name="per_level" id="per_level" class="form-control" data-placeholder="Permissions" data-allow-clear="true"<?php echo $fn->user['group_id'] != 1 ? ' readonly disabled' : ''; ?>>       
              <option value=""></option>     
              <?php
              foreach ($fn->per_levels as $k => $v) {
                if ($fn->user['group_id'] == 1 || ($fn->user['group_id'] != 1 && $fn->post('per_level') == $k)) {
                  ?>
                  <option value="<?php echo $k ?>"<?php echo ($fn->post('per_level') == $k) ? ' selected' : ''; ?>><?php echo implode(', ', $v) ?></option>
                  <?php
                }
              }
              ?>
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
                <th><?php echo _('Form Title'); ?></th>
                <th><?php echo _('Form Code'); ?></th>
                <th><?php echo _('Parent Form'); ?></th>
                <th><?php echo _('Icon'); ?></th>
                <th><?php echo _('Permissions'); ?></th>
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
                  <td class="text-center hidden-xs"><?php echo ($row['parent_display_no'] ? $row['parent_display_no'] . '.' : '') . $row['display_no'] . ($row['parent_display_no'] ? '' : '.0'); ?></td>
                  <td><?php echo $row['form_title']; ?></td>
                  <td><?php echo $row['form_code']; ?></td>
                  <td><?php echo $row['parent_title'] ? $row['parent_title'] : '-'; ?></td>
                  <td><span class="icon <?php echo $row['display_icon']; ?>"></span> <?php echo $row['display_icon']; ?></td>
                  <td>
                    <?php
                    if ($row['per_level'] > 0) {
                      if (is_array($fn->per_levels[$row['per_level']])) {
                        echo implode(", ", $fn->per_levels[$row['per_level']]);
                      }
                    } else {
                      echo '-';
                    }
                    ?>
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
