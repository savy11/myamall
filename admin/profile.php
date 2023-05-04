<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\profile;
if ($fn->is_ajax_call()) {
 header('Content-Type: application/json');
 $json = '';

 /*
  * Set Profile Image
  */

 if ($fn->post('action') == 'profile_image') {
  try {
   $fn->set_profile_image();
   $fn->session_msg('Profile image has been updated successfully!', 'success');
   $json = array('success' => true);
  } catch (Exception $ex) {
   $json = array('error' => true, 'g_title' => 'Error', 'g_message' => $ex->getMessage());
  }
 }

 if ($json) {
  echo $fn->json_encode($json);
 }
 exit();
}
if ($fn->post('btn_update') == 'update') {
 try {
  $fn->profile_update();
  $fn->session_msg('Data has been updated successfully!', 'success');
  $fn->redirecting('profile');
 } catch (Exception $ex) {
  $fn->session_msg($ex->getMessage(), 'error');
 }
}
ob_start();
?>
<style type="text/css">
 .cropit-image-preview {
  background-color: #f0f0f0;
  background-size: cover;
  border-radius: 50%;
  width: 200px;
  height: 200px;
  cursor: move;
  margin: 0 auto;
 }
 .cropit-range {
  max-width: 200px;
  margin: 15px auto 0;
 }
 .cropit-image-zoom-input {
  margin-top: 10px;
 }
</style>
<?php
$fn->style = ob_get_clean();
include 'inc/head.php';
include 'inc/header.php';
?>
<div class="row">
 <div class="col-sm-8">
  <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
   <div class="panel panel-default">
    <div class="panel-heading">
     <h4 class="panel-title">Edit Profile</h4>
    </div>
    <div class="panel-body">
     <div class="row">
      <div class="form-group col-sm-6">
       <label for="first_name" class="input-label req"><?php echo _('First Name'); ?></label>
       <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $fn->user['first_name']; ?>" autofocus required />
      </div>
      <div class="form-group col-sm-6">
       <label for="last_name" class="input-label req"><?php echo _('Last Name'); ?></label>
       <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo $fn->user['last_name']; ?>" required />
      </div>
      <div class="clearfix"></div>
      <div class="form-group col-sm-12">
       <label for="username" class="input-label req"><?php echo _('Username'); ?></label>
       <input type="text" name="username" id="username" class="form-control" value="<?php echo $fn->user['username']; ?>" required />
      </div>
      <div class="clearfix"></div>
      <div class="form-group col-sm-6">
       <label class="input-label"><?php echo _('Email'); ?></label>
       <input type="email" class="form-control" value="<?php echo $fn->user['email']; ?>" readonly disabled />
      </div>     
      <div class="form-group col-sm-6">
       <label for="mobile_no" class="input-label"><?php echo _('Mobile No.'); ?> <small>(Optional)</small></label>
       <div class="input-group">
        <div class="input-group-addon"><strong>+91</strong></div>
        <input type="text" name="mobile_no" id="mobile_no" class="form-control" value="<?php echo $fn->user['mobile_no']; ?>" />
       </div>
      </div>     
     </div>
    </div>
    <div class="panel-footer">
     <input type="hidden" name="token" checked="form-control hide" value="<?php echo $fn->post_token(); ?>" />
     <div class="clearfix">
      <button type="submit" name="btn_update" value="update" class="btn btn-success btn-sm pull-right"><span class="icon s7-diskette"></span> Update</button>
     </div>
    </div>
   </div>
  </form>
 </div>
 <div class="col-sm-4">
  <div class="panel panel-default">
   <div class="panel-heading">
    <h4 class="panel-title">Profile Image</h4>
   </div>
   <div class="panel-body">
    <div class="profile-image text-center">
     <div class="cropit-image-preview"></div>
     <div class="cropit-range" style="display: none;">
      <input type="range" class="cropit-image-zoom-input" min="0" max="1" step="0.01" value="0" />
     </div>
     <input type="file" class="cropit-image-input hide" accept="image/*" />
    </div>
   </div>
   <div class="panel-footer">
    <div class="clearfix">
     <button type="button" class="btn btn-sm btn-warning select-image-btn"><i class="s7-upload"></i> Upload</button>
     <button type="button" class="btn btn-sm btn-success set-image pull-right" style="display: none;">Crop It</button>
    </div>
   </div>
  </div>
 </div>
</div>
<?php
ob_start();
?>
<script type="text/javascript">
 app.cropit = function () {
  var l = new loader();
  l.require(['resources/vendor/cropit/cropit.js'], function () {
   var filename = null;

   // Bind Upload Button
   var select = $('.select-image-btn');
   select.unbind('click');
   select.click(function () {
    $('.cropit-image-input').click();
   });
   // Bind CropIt
   var profile = $('.profile-image'),
           set = $('.set-image'),
           range = $('.cropit-range');
   profile.cropit({
    imageBackground: true,
    imageState: {
     src: '<?php echo ($fn->file_exists($fn->user['image']) ? $fn->get_file($fn->user['image']) : ''); ?>',
    },
    onImageError: function (data) {
     app.show_msg('Error', data.message, 'error');
    },
    onFileChange: function (data) {
     filename = data.target.files[0].name;
     set.fadeIn();
     range.fadeIn();
    }
   });

   // Set Image
   set.click(function () {
    set.fadeOut();
    range.fadeOut();
    app.send_data('profile', 'action=profile_image&filename=' + filename + '&uri=' + profile.cropit('export'), '', '', '', function () {
     window.location.reload();
    }, function () {
     set.fadeIn();
     range.fadeIn();
    });
   });
  }, true);
 }
 $(function () {
  app.cropit();
 }
 );
</script>
<?php
$fn->script = ob_get_clean();
include 'inc/footer.php';
include 'inc/foot.php';
?>
