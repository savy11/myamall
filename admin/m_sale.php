<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\m_sale;
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
 include admin_path . 'inc' . ds . 'head.php';
 include admin_path . 'inc' . ds . 'header.php';
 if (($fn->per_add && $fn->get('action') == 'add') || ($fn->per_edit && $fn->get('action') == 'edit')) {
  if ($fn->get('action') == 'edit' && $fn->get('id')) {
   $fn->select();
  }
  if (!($fn->post('sp') != '' && count($fn->post('sp')) > 0)) {
   $_POST['sp'] = array(0 => '');
  }
  ?>
     <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off" enctype="multipart/form-data">
         <div class="panel panel-default">
          <?php include admin_path . 'inc' . ds . 'panel-head.php'; ?>
             <div class="panel-body">
                 <div class="row">
                     <div class="form-group col-sm-3">
                         <label for="sale_title" class="input-label req"><?php echo _('Sale Title'); ?></label>
                         <input type="text" name="sale_title" id="sale_title" class="form-control" value="<?php echo $fn->post('sale_title'); ?>" required/>
                     </div>
                     <div class="form-group col-sm-3">
                         <label for="start_date" class="input-label req"><?php echo _('Start date'); ?></label>
                         <input type="text" name="start_date" id="start_date" class="form-control dt-picker" value="<?php echo $fn->post('start_date'); ?>" data-date-only="true" required/>
                     </div>
                     <div class="form-group col-sm-3">
                         <label for="end_date" class="input-label req"><?php echo _('End date'); ?></label>
                         <input type="text" name="end_date" id="end_date" class="form-control dt-picker" value="<?php echo $fn->post('end_date'); ?>" data-date-only="true" required/>
                     </div>
                     <div class="form-group col-sm-3">
                         <label for="publish" class="input-label req"><?php echo _('Publish'); ?></label>
                         <select name="publish" id="publish" class="form-control" data-placeholder="Publish">
                          <?php echo $fn->show_list($fn->yes_no, $fn->post('publish'), false); ?>
                         </select>
                     </div>
                 </div>
             </div>
         </div>

         <div class="panel panel-default">
             <div class="panel-heading">
                 <h3 class="panel-title req">Sale Products</h3>
             </div>
             <div class="panel-body">
                 <input type="hidden" name="sp_del_ids" id="sp_del_ids" class="form-control"/>
                 <table class="table table-bordered multi-table grid mb-15" cellpadding="0" cellspacing="0"
                        data-btn-add="sp_row_add" data-btn-delete="sp_row_delete"
                        data-row-index="<?php echo count($fn->post('sp')) - 1; ?>" data-grid-body="sp_grid_body">
                     <thead>
                     <tr>
                         <th width="1%" class="text-center">
                             <a href="#" class="btn btn-success btn-sm" rel="sp_row_add" tabindex="-1">
                                 <span class="icon ti-plus"></span>
                             </a>
                         </th>
                         <th width="25%" class="req">Product</th>
                         <th width="15%" class="req">Discount Price</th>
                     </tr>
                     </thead>
                     <tbody class="sp_grid_body">
                     <?php
                      if ($fn->post('sp') != '') {
                       foreach ($fn->post('sp') as $k => $v) {
                        $tr = '_TR' . $k;
                        ?>
                           <tr id="<?php echo $tr; ?>">
                               <td align="center">
                                   <a href="#" class="btn btn-danger btn-sm" rel="sp_row_delete" data-id="<?php echo $tr; ?>" data-del-id="<?php echo $fn->varv('id', $v); ?>" data-del-input="#sp_del_ids" tabindex="-1">
                                       <span class="icon ti-close"></span>
                                   </a>
                                   <input type="hidden" name="sp[<?php echo $tr; ?>][id]" id="sp<?php echo $tr; ?>_id" value="<?php echo $fn->varv('id', $v); ?>"/>
                               </td>

                               <td class="relative">
                                   <select name="sp[<?php echo $tr; ?>][product_id]" id="sp<?php echo $tr; ?>_product_id" rel="sp<?php echo $tr; ?>_price" class="form-control sp-product" data-placeholder="Product" data-allow-clear="true" required>
                                    <?php echo $fn->show_list($fn->list['products'], $fn->varv('product_id', $v), true); ?>
                                   </select>
                               </td>
                               <td class="relative">
                                   <div class="input-group">
                                       <span class="input-group-addon"><?php echo $fn->currency; ?></span>
                                       <input type="text" name="sp[<?php echo $tr; ?>][discount_price]"
                                              id="sp<?php echo $tr; ?>_discount_price" class="form-control"
                                              value="<?php echo $fn->varv('discount_price', $v); ?>" required/>
                                   </div>
                               </td>
                           </tr>
                        <?php
                       }
                      }
                     ?>
                     </tbody>
                 </table>
             </div>
          <?php include admin_path . 'inc' . ds . 'panel-footer.php'; ?>
         </div>
         </div>
     </form>
  <?php
 } else {
  $fn->select_all();
  ?>
     <div class="panel panel-default">
      <?php include admin_path . 'inc' . ds . 'panel-head.php'; ?>
         <div class="panel-body">
          <?php if ($fn->data) { ?>
              <div class="table-responsive">
                  <table class="table table-striped table-bordered">
                      <thead>
                      <tr>
                          <th width="5%" class="text-center hidden-xs"><?php echo _('#'); ?></th>
                          <th><?php echo _('Sale Title'); ?></th>
                          <th><?php echo _('Start Date'); ?></th>
                          <th><?php echo _('End Date'); ?></th>
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
                               <td><?php echo $row['sale_title']; ?></td>
                               <td><?php echo $fn->dt_format($row['start_date'], 'F d, Y'); ?></td>
                               <td><?php echo $fn->dt_format($row['end_date'], 'F d, Y'); ?></td>
                               <td align="center">
                                   <div id="publish-<?php echo $row['id']; ?>">
                                    <?php echo $fn->get_view('button', 'YES_NO', array('status' => $row['publish'], 'id' => $row['id'], 'action' => 'publish')); ?>
                                   </div>
                               </td>
                            <?php include admin_path . 'inc' . ds . 'actions.php'; ?>
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
 include admin_path . 'inc' . ds . 'footer.php';
 include admin_path . 'inc' . ds . 'foot.php';
?>
