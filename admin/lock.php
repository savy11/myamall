<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
$fn = new admin\controllers\lock;
if ($fn->post('btn_submit') != '') {
 try {
  $fn->unlock();
  $fn->redirect();
 } catch (Exception $ex) {
  if ($fn->session('lo_step') == 3) {
   $fn->session_msg('Your unlock limit is reached. Please try login again.', 'error');
   $fn->redirect('logout');
  }
  $fn->session_msg($ex->getMessage(), 'error');
 }
}
include 'inc/header_1.php';
?>
<div class="unauth-page">
 <div class="panel panel-default">
  <div class="panel-heading">
   <img src="<?php echo $fn->file_exists($fn->user['image']) ? $fn->get_file($fn->user['image']) : $fn->permalink('assets/img/no-user.jpg'); ?>" class="img-circle mb-15" width="100" />
   <h2><?php echo $fn->user['display_name']; ?></h2>
   <span>Enter your password to access your account.</span>
  </div>
  <div class="panel-body">
   <form id="data-frm" name="data-frm" method="post" class="form-validate" autocomplete="off">
    <div class="form-group">
     <div class="input-group">
      <span class="input-group-addon"><i class="s7-key"></i></span>
      <input type="password" name="lock[password]" id="lock-password" class="form-control input-lg" placeholder="Password" required />
     </div>
    </div>
    <div class="form-group login-submit">
     <input type="hidden" name="token" class="form-control" value="<?php echo $fn->post_token(); ?>" />
     <button type="submit" name="btn_submit" value="Log In" class="btn btn-secondary btn-block btn-lg">Log In</button>
    </div>
   </form>
   <div class="text-center">
    <span class="text-grey">Not <?php echo $fn->user['display_name']; ?>? <a href="<?php echo $fn->permalink('logout'); ?>" tabindex="-1">Logout</a></span>
   </div>
  </div>
 </div>
</div>
<?php
include 'inc/footer.php';
include 'inc/foot.php';
?>
