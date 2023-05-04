<?php
 $str = '';
 ob_start();
 if ($type == 'shipping') {
  $fn->modal = array('title' => 'Shipping Details', 'md_class' => ' modal-sm');
  ?>
  <form method="post" class="form-validate" name="shipping-frm" id="shipping-frm" data-ajax="true" data-page="true" data-url="orders" data-action="shipping" data-recid="order_status_<?php echo $fn->post('id'); ?>">
   <div class="modal-body">
    <div class="form-group">
     <label class="input-label req">Shipping Company</label>
     <select name="shipping[company]" id="shipping-company" class="form-control" data-placeholder="Shipping Company" data-allow-clear="true" required>
      <?php echo $fn->show_list($fn->list['shippers'], '', true); ?>
     </select>
    </div>
    <div class="form-group">
     <label class="input-label">Tracking No. <small>(Optional)</small></label>
     <input type="text" name="shipping[tracking_no]" id="shipping-tracking-no" class="form-control" />
    </div>
    <div class="form-group">
     <label class="input-label req">Shipping Date</label>
     <input type="text" name="shipping[date]" id="shipping-date" class="form-control dt-picker" data-date-only="true" required />
    </div>
    <div class="form-group">
     <label class="input-label">Remarks <small>(Optional)</small></label>
     <textarea name="shipping[remarks]" id="shipping-remarks" class="form-control" rows="3"></textarea>
    </div>
   </div>
   <div class="modal-footer">
    <input type="hidden" name="data" value="<?php echo $fn->post('data'); ?>" />
    <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>" />
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-secondary">Submit</button>
   </div>
  </form>
  <?php
 }else if ($type == 'return') {
  $fn->modal = array('title' => 'Why are they returning this?');
  ?>
  <form method="post" name="return-frm" id="return-frm" class="form-validate" data-ajax="true" data-url="orders" data-page="true" data-action="return">
   <div class="modal-body"> 
    <div class="form-group">
     <label class="input-label req">Subject</label>
     <select name="return[subject]" id="return_subject" class="form-control" data-placeholder="Subject" data-allow-clear="true" required>
      <?php echo $fn->show_list($fn->list['return_subject'], $fn->post('subject'), true); ?>
     </select>
    </div>
    <div class="form-group">
     <label class="input-label req">Remarks</label>
     <textarea name="return[remarks]" id="remarks" class="form-control" rows="4" required></textarea>
    </div>
   </div>
   <div class="modal-footer">
    <input type="hidden" name="data" value="<?php echo $fn->post('data'); ?>" />
    <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>" />
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-success">Submit</button>
   </div>
  </form>
  <?php
 } else if ($type == 'payment_status') {
  $fn->modal = array('title' => 'Update Payment Status');
  ?>
  <form method="post" name="return-frm" id="payment-frm" class="form-validate" data-ajax="true" data-url="orders"
        data-page="true" data-action="payment_status">
   <div class="modal-body">
    <div class="form-group">
     <label class="input-label req">Remarks</label>
     <textarea name="remarks" id="remarks" class="form-control" rows="4" required></textarea>
    </div>
   </div>
   <div class="modal-footer">
    <input type="hidden" name="data" value="<?php echo $fn->post('data'); ?>" />
    <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>" />
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-success">Submit</button>
   </div>
  </form>
  <?php
 } else if ($type == 'crop') {
  $fn->modal = array('title' => 'Crop Image', 'md_class' => ' modal-lg');
  $file = $fn->json_encode($fn->post('file'));
  ?>
  <div class="modal-body crop-media">
   <div class="row">
    <div class="col-md-9 col-sm- col-xs-12">
     <div class="crop-container">
      <img src="<?php echo $fn->get_file($file); ?>" class="img-responsive" />
     </div>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-12 ">
     <div class="crop-group">
      <div class="crop-title">Preview</div>
      <div class="docs-preview clearfix">
       <div class="img-preview preview-lg"></div>
      </div>
      <div class="crop-panel">
       <div class="crop-title">SCALE IMAGE <a data-toggle="collapse" href="#collapse-scale"><i class="s7-info"></i></a></div>
       <div id="collapse-scale" class="collapse">
        <i>You can proportionally scale the original image. For best results, scaling should be done before you crop, flip, or rotate. Images can only be scaled down, not up.</i>
       </div>
       <div class="crop-body">
        <p>Original Dimensions <?php echo $fn->post('dimensions'); ?></p>
       </div>
      </div>
      <div class="crop-panel">
       <div class="crop-title">IMAGE CROP <a data-toggle="collapse" href="#collapse-crop"><i class="s7-info"></i></a></div>
       <div id="collapse-crop" class="collapse">
        <i>To crop the image, click on it and drag to make your selection.

         <strong>Crop Aspect Ratio</strong>
         The aspect ratio is the relationship between the width and height. You can preserve the aspect ratio by holding down the shift key while resizing your selection. Use the input box to specify the aspect ratio, e.g. 1:1 (square), 4:3, 16:9, etc.

         <strong>Crop Selection</strong>
         Once you have made your selection, you can adjust it by entering the size in pixels.</i>
       </div>
       <div class="crop-body">
        <form method="post" id="crop-frm" class="form-validate">
         <label class="crop-label">Aspect Ratio:</label>
         <div class="crop-group">
          <input type="text" name="crop[x]" id="crop-x" class="form-control" />
          <span class="crop-separator">:</span>
          <input type="text" name="crop[y]" id="crop-y" class="form-control" />
         </div>
         <label class="crop-label">Selection:</label>
         <div class="crop-group">
          <input type="text" name="crop[width]" id="crop-width" class="form-control" />
          <span class="crop-separator">x</span>
          <input type="text" name="crop[height]" id="crop-height" class="form-control" />
          <span class="crop-separator">px</span>
         </div>
         <div class="form-group">
          <label class="input-label">Alternate Text:</label>
          <input type="text" name="alt_text" id="alt_text" class="form-control" value="<?php echo $fn->post('alt_text'); ?>" data-ajaxify="true" data-type="add-alt" data-url="crop" data-app="<?php echo $fn->encrypt_post_data(array('file_id' => $fn->post('file_id'))); ?>" data-event="change" />
         </div>
         <div class="form-group">
          <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>" />
          <button type="button" class="btn btn-success" data-ajaxify="true" data-type="export" data-frm="crop-frm" data-confirm="You want to crop the image" data-url="crop" data-app="<?php echo $fn->encrypt_post_data(array('file_id' => $fn->post('file_id'), 'file' => $fn->post('file'))); ?>">Crop Image</button>
         </div>
        </form>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
  <?php
 } else {
  throw new Exception('Oops, ' . ucfirst($type) . ' modal not found.');
 }
 $str = ob_get_clean();
 if ($fn->is_ajax_call()) {
  $modal = '';
  ob_start();
  ?>
  <div class="modal-dialog<?php echo $fn->varv('md_class', $fn->modal); ?>" role="document"<?php echo $fn->varv('style', $fn->modal) ? ' style="' . $fn->modal['style'] . '"' : ''; ?>>
   <div class="modal-content">
    <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     <h4 class="modal-title"><?php echo $fn->modal['title']; ?></h4>
    </div>
    <?php echo $str; ?>
   </div>
  </div>
  <?php
  $modal = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
  return $modal;
 }
 return $str;
 