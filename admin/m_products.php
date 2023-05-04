<?php
 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new admin\controllers\m_products;
 if ($fn->is_ajax_call()) {
  header('Content-Type:application/json');
  $json = "";
  
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
  
  if (in_array($fn->post('action'), ['offer', 'saver', 'stock'])) {
   try {
    $fn->update_actions();
    $field = $fn->post('field');
    $json = ['success' => true, 'html' => $fn->get_view('button', 'COMMON', [$field => $fn->post($field), 'field' => $field, 'id' => $fn->post('id'), 'action' => $fn->post('action')]), 'g_title' => $fn->page['name'], 'g_message' => 'Data has been updated successfully.'];
   } catch (Exception $ex) {
    $json = array('error' => true, 'g_title' => $fn->page['name'], 'g_message' => $ex->getMessage());
   }
  }
  echo $fn->json_encode($json);
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
<link rel="stylesheet"
      href="<?php echo $fn->permalink('resources/vendor/magnific-popup/magnific-popup.css', '', true); ?>"
      type="text/css"/>
<style type="text/css">
    .multi-table > tbody > tr > td {
        vertical-align: top;
    }
</style>
<?php
 $fn->style = ob_get_clean();
 include 'inc/head.php';
 include 'inc/header.php';
 include 'inc/search-panel.php';
 if (($fn->per_add && $fn->get('action') == 'add') || ($fn->per_edit && $fn->get('action') == 'edit')) {
 if ($fn->get('action') == 'edit' && $fn->get('id')) {
  $fn->select();
 }
 if (!($fn->post('color') != '' && count($fn->post('color')) > 0)) {
  $_POST['color'] = array(0 => '');
 }
 ?>
    <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off"
          enctype="multipart/form-data">
        <div class="panel panel-default">
         <?php include 'inc/panel-head.php'; ?>
            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="category_id" class="input-label req"><?php echo _('Category'); ?></label>
                        <select name="category_id" id="category_id" class="form-control" data-placeholder="Category"
                                data-allow-clear="true" required>
                         <?php echo $fn->show_list($fn->list['categories'], $fn->post('category_id'), true); ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-9">
                        <label for="product_title" class="input-label req"><?php echo _('Product Title'); ?></label>
                        <input type="text" name="product_title" id="product_title" class="form-control"
                               value="<?php echo $fn->post('product_title'); ?>" data-rule-title="true" required/>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-3">
                        <label for="in_stock" class="input-label req"><?php echo _('In Stock'); ?></label>
                        <select name="in_stock" id="in_stock" class="form-control" data-placeholder="Yes/No"
                                required>
                         <?php echo $fn->show_list($fn->yes_no, $fn->post('in_stock'), false); ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="best_offer" class="input-label req"><?php echo _('Best Offer'); ?></label>
                        <select name="best_offer" id="best_offer" class="form-control" data-placeholder="Yes/No"
                                required>
                         <?php echo $fn->show_list($fn->yes_no, $fn->post('best_offer'), false); ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="brand_id" class="input-label"><?php echo _('Brand'); ?></label>
                        <select name="brand_id" id="brand_id" class="form-control" data-placeholder="Brand"
                                data-allow-clear="true">
                         <?php echo $fn->show_list($fn->get_brands(), $fn->post('brand_id'), true); ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="publish" class="input-label req"><?php echo _('Publish'); ?></label>
                        <select name="publish" id="publish" class="form-control" data-placeholder="Publish">
                         <?php echo $fn->show_list($fn->yes_no, $fn->post('publish'), false); ?>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-3">
                        <label for="discount" class="input-label"><?php echo _('Discount'); ?>
                            <small>(Optional)</small>
                        </label>
                        <div class="input-group">
                            <input name="discount" id="discount" class="form-control" placeholder="Discount"
                                   value="<?php echo $fn->post('discount'); ?>"/>
                            <div class="input-group-addon">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="product_code" class="input-label"><?php echo _('Product Code'); ?>
                            <small>(Optional)</small>
                        </label>
                        <input type="text" name="product_code" id="product_code" class="form-control"
                               value="<?php echo $fn->post('product_code'); ?>"/>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="special_price" class="input-label"><?php echo _('Special Price'); ?>
                            <small>(Optional)</small>
                        </label>
                        <input type="text" name="special_price" id="special_price" class="form-control"
                               value="<?php echo $fn->post('special_price'); ?>" data-rule-number="true"/>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="basic_price" class="input-label req"><?php echo _('Basic Price'); ?> </label>
                        <input type="text" name="basic_price" id="basic_price" class="form-control"
                               value="<?php echo $fn->post('basic_price'); ?>" data-rule-number="true" required/>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="total_sold" class="input-label req"><?php echo _('Total Sold'); ?> </label>
                        <input type="number" name="total_sold" id="total_sold" class="form-control"
                               value="<?php echo $fn->post('total_sold'); ?>" required/>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-12">
                        <label for="product_desc" class="input-label"><?php echo _('Product Description'); ?></label>
                        <textarea name="product_desc" id="product_desc" class="form-control tinymce" rows="10"><?php echo $fn->post('product_desc'); ?></textarea>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-12">
                        <label for="products" class="input-label req"><?php echo _('Product Images'); ?></label>
                        <div>
                            <div class="uploader" data-list-id="files" data-type="products"
                                 data-upload-button-text="Upload Images">Upload Images
                            </div>
                            <ul class="files check-it clearfix" id="files" data-columns="8" data-checked-limit="2">
                             <?php echo $fn->galleries('products', $fn->post('products'), $fn->post('product_image')); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Color Classification
                    <small>(Optional)</small>
                </h3>
            </div>
            <div class="panel-body">
                <input type="hidden" name="color_del_ids" id="color_del_ids" class="form-control"/>
                <table class="table table-bordered multi-table grid mb-15" cellpadding="0" cellspacing="0"
                       data-btn-add="color_row_add" data-btn-delete="color_row_delete"
                       data-row-index="<?php echo count($fn->post('color')) - 1; ?>" data-grid-body="color_grid_body">
                    <thead>
                    <tr>
                        <th width="5%" class="text-center"><a href="#" class="btn btn-success btn-sm"
                                                              rel="color_row_add"
                                                              tabindex="-1"><span class="icon ti-plus"></span></a></th>
                        <th width="25%">Title</th>
                        <th width="25%">Sizes</th>
                        <th width="25%">Price</th>
                        <th>Image <small>(Size: 1000 x 1000 px)</small></th>
                    </tr>
                    </thead>
                    <tbody class="color_grid_body">
                    <?php
                     if ($fn->post('color') != '') {
                      foreach ($fn->post('color') as $k => $v) {
                       $tr = '_TR' . $k;
                       ?>
                          <tr id="<?php echo $tr; ?>">
                              <td align="center">
                                  <a href="#" class="btn btn-danger btn-sm" rel="color_row_delete"
                                     data-id="<?php echo $tr; ?>"
                                     data-del-id="<?php echo $fn->varv('id', $v); ?>" data-del-input="#color_del_ids"
                                     tabindex="-1"><span class="icon ti-close"></span></a>
                                  <input type="hidden" name="color[<?php echo $tr; ?>][id]"
                                         id="color<?php echo $tr; ?>_id"
                                         class="form-control" value="<?php echo $fn->varv('id', $v); ?>"/>
                              </td>
                              <td class="relative">
                                  <input type="text" name="color[<?php echo $tr; ?>][title]"
                                         id="color<?php echo $tr; ?>_title" class="form-control"
                                         value="<?php echo $fn->varv('title', $v); ?>"/>
                              </td>
                              <td class="relative">
                                  <select name="color[<?php echo $tr; ?>][sizes][]" id="color<?php echo $tr; ?>_sizes" class="form-control" data-placeholder="Sizes"
                                          data-allow-clear="true" multiple>
                                   <?php echo $fn->show_list($fn->list['sizes'], $fn->varv('sizes', $v), true); ?>
                                  </select>
                              </td>
                              <td class="relative">
                                  <input type="text" name="color[<?php echo $tr; ?>][basic_price]"
                                         id="color<?php echo $tr; ?>_basic_price" class="form-control"
                                         value="<?php echo $fn->varv('basic_price', $v); ?>"/>
                              </td>
                              <td class="relative">
                                  <div class="clearfix custom-file">
                                      <input type="file" name="color[<?php echo $tr; ?>][image]" id="color<?php echo $tr; ?>_image"/>
                                      <label for="color<?php echo $tr; ?>_image"><span></span> <strong>Choose a file...</strong></label>
                                  </div>
                               <?php if ($fn->file_exists($fn->varv('image', $v))) { ?>
                                   <div class="custom-img">
                                       <a href="<?php echo $fn->get_file($fn->varv('image', $v)); ?>" title="<?php echo $fn->varv('title', $v); ?>" class="magnific-gallery">
                                           <img src="<?php echo $fn->get_file($fn->varv('image', $v), 0, 0, 50); ?>" width="50"/>
                                       </a>
                                   </div>
                               <?php } ?>
                              </td>
                          </tr>
                       <?php
                      }
                     }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
     <?php include admin_path . 'inc' . ds . 'seo_section.php'; ?>
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
                     <th><?php echo _('Category'); ?></th>
                     <th><?php echo _('Product'); ?></th>
                     <th width="10%"><?php echo _('Product Code'); ?></th>
                     <th width="5%"><?php echo _('Discount'); ?></th>
                     <th width="5%"><?php echo _('Special Price'); ?></th>
                     <th width="5%"><?php echo _('Sale Price'); ?></th>
                     <th width="5%" class="text-center"><?php echo _('Best Offer'); ?></th>
                     <th width="5%" class="text-center"><?php echo _('In Stock'); ?></th>
                     <th width="5%" class="text-center"><?php echo _('Publish'); ?></th>
                  <?php if ($fn->check_per()) { ?>
                      <th width="5%" class="text-center"><?php echo _('Actions'); ?></th>
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
                          <td><?php echo $row['category_name']; ?></td>
                          <td><?php echo $row['product_title']; ?></td>
                          <td><?php echo $row['product_code'] ? $row['product_code'] : '-'; ?></td>
                          <td><?php echo $row['discount'] . '%'; ?></td>
                          <td><?php echo $fn->show_price($row['special_price']); ?></td>
                          <td><?php echo $fn->show_price($row['basic_price']); ?></td>
                          <td align="center">
                              <div id="offer-<?php echo $row['id']; ?>">
                               <?php echo $fn->get_view('button', 'COMMON', array('best_offer' => $row['best_offer'], 'field' => 'best_offer', 'id' => $row['id'], 'action' => 'offer')); ?>
                              </div>
                          </td>
                          <td align="center">
                              <div id="stock-<?php echo $row['id']; ?>">
                               <?php echo $fn->get_view('button', 'COMMON', array('in_stock' => $row['in_stock'], 'field' => 'in_stock', 'id' => $row['id'], 'action' => 'stock')); ?>
                              </div>
                          </td>
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
         <div class="alert alert-danger mb-0"><?php echo _('Oops, nothing found.'); ?></div>
     <?php }
     ?>
    </div>
 <?php } ?>
</div>
<?php ob_start(); ?>
<script type="text/javascript"
        src="<?php echo $fn->permalink('resources/vendor/magnific-popup/jquery.magnific-popup.js', '', true); ?>"></script>
<?php
 $fn->script = ob_get_clean();
 include 'inc/footer.php';
 include 'inc/foot.php';
?>
