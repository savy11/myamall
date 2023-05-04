<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\forgot;
if ($fn->session('forgot', 'step') != 3) {
 $fn->not_found();
}
if ($fn->post('btn_reset') != '') {
 try {
  $fn->reset_password();
  $fn->session_msg('Your password has been reset successfully. Please login.', 'success', 'Login');
  $fn->redirecting('login');
 } catch (Exception $ex) {
  $fn->session_msg($ex->getMessage(), 'error', 'Reset Password');
 }
}
$fn->page['name'] = 'Reset Your Password';
include 'inc/header_1.php';
?>
<div class="unauth-page">
 <div class="panel panel-default">
  <div class="panel-heading">
   <h2>Reset Password</h2>
   <span>Enter your new password for <?php echo app_name; ?> in the below fields.</span>
  </div>
  <div class="panel-body">
   <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
    <div class="form-group">
     <div class="input-group">
      <span class="input-group-addon"><i class="s7-key"></i></span>
      <input type="password" name="reset[password]" id="reset-password" class="form-control input-lg" placeholder="New Password" required />
     </div>
    </div> 
    <div class="form-group">
     <div class="input-group">
      <span class="input-group-addon"><i class="s7-key"></i></span>
      <input type="password" name="reset[re_password]" id="reset-re-password" class="form-control input-lg" placeholder="Repeat New Password" data-rule-equalto="#reset-password" required />
     </div>
    </div> 
    <div class="form-group login-submit">
     <input type="hidden" name="token" class="form-control" value="<?php echo $fn->post_token(); ?>" />
     <button type="submit" name="btn_reset" value="Reset Password" class="btn btn-secondary btn-block btn-lg">Reset Password</button>
    </div>
   </form>
   <div class="text-center out-links">
    <a href="<?php echo $fn->permalink('login'); ?>">Back to Login</a>
   </div>
  </div>
 </div>
</div>
<?php
include 'inc/footer.php';
include 'inc/foot.php';
?>
