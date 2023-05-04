<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\forgot;
if ($fn->session('forgot', 'step') != 2) {
 $fn->not_found();
}
if ($fn->post('btn_verify') != '') {
 try {
  $fn->forgot_verify();
  $fn->redirecting('reset-password');
 } catch (Exception $ex) {
  $fn->session_msg($ex->getMessage(), 'error', 'Verification');
 }
}
$fn->page['name'] = 'Verification';
include 'inc/header_1.php';
?>
<div class="unauth-page">
 <div class="panel panel-default">
  <div class="panel-heading">
   <h2>Verification</h2>
   <span>Please enter the verification code in below field that already sent at your email address.</span>
  </div>
  <div class="panel-body">
   <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
    <div class="form-group">
     <div class="input-group">
      <span class="input-group-addon"><i class="s7-key"></i></span>
      <input type="number" name="verify[code]" id="verify-code" class="form-control input-lg" placeholder="Verification Code" required />
     </div>
    </div>        
    <div class="form-group login-submit">
     <input type="hidden" name="token" class="form-control" value="<?php echo $fn->post_token(); ?>" />
     <button type="submit" name="btn_verify" value="Submit" class="btn btn-secondary btn-block btn-lg">Submit</button>
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
