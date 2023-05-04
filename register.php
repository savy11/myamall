<?php
 require dirname(__FILE__) . DIRECTORY_SEPARATOR . "autoload.php";
 $fn = new controllers\register;
 if ($fn->post('btn_register')) {
  try {
   $fn->register();
   $fn->session_msg('Your account has been registered successfully. Please login to your account.', 'success', '');
   $fn->redirecting('login');
  } catch (Exception $ex) {
   $fn->session_msg($ex->getMessage(), 'error');
  }
 }
 $terms = $fn->get_terms();
 ob_start();
?>
<style type="text/css">
    .scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .scrollbar::-webkit-scrollbar-thumb {
        background: #000000;
        border-radius: 10px;
    }

    .scrollbar::-webkit-scrollbar-thumb:hover {
        background: #eb2c33;
    }
</style>
<?php
 $fn->style = ob_get_clean();
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 include app_path . 'inc' . ds . 'breadcrumb.php';
?>
<section>
    <div class="container">
        <div class="header-for-light">
            <h1 class="wow fadeInRight animated" data-wow-duration="1s">Create new <span>Account</span></h1>
        </div>
        <div class="row">
            <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
                    <h3><i class="fa fa-user"></i>Personal Information</h3>
                    <hr>
                    <form class="form-validate form-horizontal" role="form" method="post" name="register-frm"
                          id="register-frm" autocomplete="off">
                        <div class="form-group">
                            <label for="register-first-name" class="col-sm-3 control-label">First Name:<span class="text-error">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="register[first_name]" id="register-first-name" value="<?php echo $fn->post('first_name'); ?>" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="register-last-name" class="col-sm-3 control-label">Last Name:<span class="text-error">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="register-last-name" name="register[last_name]" value="<?php echo $fn->post('last_name'); ?>" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for=register-email class="col-sm-3 control-label">E-Mail:<span class="text-error">*</span></label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="register-email" name="register[email]" value="<?php echo $fn->post('email'); ?>" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="register-mobile-no" class="col-sm-3 control-label">Date of Birth: <span class="text-error">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control dt-picker" data-date-only="true" id="register-dob" name="register[dob]" value="<?php echo $fn->post('dob'); ?>" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="register-mobile-no" class="col-sm-3 control-label">Mobile No.: <span class="text-error">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control numbers-only" id="register-mobile-no" name="register[mobile_no]" value="<?php echo $fn->post('mobile_no'); ?>" required/>
                            </div>
                        </div>
                        <h3><i class="fa fa-lock"></i>Password</h3>
                        <hr>
                        <div class="form-group">
                            <label for="register-password" class="col-sm-3 control-label">Password: <span class="text-error">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="register-password" name="register[password]" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="register-confirm-password" class="col-sm-3 control-label">Re-Password: <span class="text-error">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="register-confirm-password" name="register[confirm_password]" data-not-equato="#register-password" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="newsletter" class="col-sm-3 control-label">Newsletter: <span class="text-error">*</span></label>
                            <div class="col-sm-9">
                                <div class="radio">
                                    <input type="radio" name="register[subscribe]" id="register-subscribe-yes" value="1" checked/>
                                    <label for="register-subscribe-yes">Subscribe</label>
                                </div>
                                <div class="radio">
                                    <input type="radio" name="register[subscribe]" id="register-subscribe-no" value="0"/>
                                    <label for="register-subscribe-no">Unsubscribe</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <div class="checkbox">
                                    <input type="checkbox" name="register[terms]" id="register-terms" value="1" required/>
                                    <label for="register-terms">I'v read and agreed on Terms & Conditions.</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                                <button type="submit" class="btn-default-1" name="btn_register" value="Register">Register</button>
                            </div>
                        </div>
                    </form>
                </div>
            </article>
            <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                <div class="block-form box-border wow fadeInRight animated" data-wow-duration="1s">
                 <?php if ($terms) { ?>
                     <h3><i class="fa fa-check"></i><?php echo $terms['page_heading']; ?></h3>
                     <hr>
                     <div class="scrollbar" style="position: relative; max-height: 773px; overflow-y: auto;">
                      <?php echo $terms['page_desc']; ?>
                     </div>
                 <?php } ?>

                </div>
            </article>
        </div>
    </div>
</section>
<?php
 include app_path . 'inc' . ds . 'footer.php';
 include app_path . 'inc' . ds . 'foot.php';
?>
	