<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\m_blogs_cat;
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
include 'inc/head.php';
include 'inc/header.php';
if (($fn->per_add && $fn->get('action') == 'add') || ($fn->per_edit && $fn->get('action') == 'edit')) {
 if ($fn->get('action') == 'edit' && $fn->get('id')) {
  $fn->select();
 }
 ?>
 <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off" enctype="multipart/form-data">
  <div class="panel panel-default">
   <?php include 'inc/panel-head.php'; ?>
   <div class="panel-body">
    <div class="row">
     <div class="form-group col-sm-9">
      <label for="category_name" class="input-label req"><?php echo _('Category Name'); ?></label>
      <input type="text" name="category_name" id="category_name" class="form-control" value="<?php echo $fn->post('category_name'); ?>" data-rule-title="true" required />
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
    <h3 class="panel-title">Seo Section</h3>
   </div>
   <div class="panel-body">
    <div class="row">
     <div class="form-group col-sm-6">
      <label for="page_title" class="input-label req"><?php echo _('Page Title'); ?></label>
      <input type="text" name="page_title" id="page_title" class="form-control" value="<?php echo $fn->post('page_title'); ?>" data-page-title="true" required />
     </div>
     <div class="form-group col-sm-6">
      <label for="page_heading" class="input-label req"><?php echo _('Page Heading'); ?></label>
      <input type="text" name="page_heading" id="page_heading" class="form-control" value="<?php echo $fn->post('page_heading'); ?>" data-page-title="true" required />
     </div>
     <div class="clearfix"></div>
     <div class="form-group col-sm-12">
      <label for="page_url" class="input-label req"><?php echo _('Page Url'); ?></label>
      <input type="text" name="page_url" id="page_url" class="form-control" value="<?php echo $fn->post('page_url'); ?>" data-page-url="true" required />
     </div>     
     <div class="clearfix"></div>
     <div class="form-group col-sm-6">
      <label for="meta_keywords" class="input-label"><?php echo _('Meta Keywords'); ?> <small>(Optional)</small></label>
      <textarea name="meta_keywords" id="meta_keywords" class="form-control tagsinput count-keywords" rows="5" data-default-text="Add a keyword"><?php echo $fn->post('meta_keywords'); ?></textarea>
     </div>
     <div class="form-group col-sm-6">
      <label for="meta_desc" class="input-label"><?php echo _('Meta Description'); ?> <small>(Optional)</small></label>
      <textarea name="meta_desc" id="meta_desc" class="form-control count-char" rows="5" style="height: 104px;"><?php echo $fn->post('meta_desc'); ?></textarea>
     </div>
    </div>
   </div>
   <?php include 'inc/panel-footer.php'; ?>
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
        <th><?php echo _('Category Name'); ?></th>
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
         <td><?php echo $row['category_name']; ?></td>
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
 </div>
 <?php
}
include 'inc/footer.php';
include 'inc/foot.php';
?>
