<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\account;
 if ($fn->get('for') != 'account') {
  $fn->not_found();
 }
 $fn->require_login();
 $fn->cms_page('change-password');
 if ($fn->post('btn_update') != '') {
  try {
   $fn->change_password();
   $fn->session_msg('Your password has been changed successfully!', 'success', $fn->cms['page_title']);
  } catch (Exception $ex) {
   $fn->session_msg($fn->replace_sql($ex->getMessage()), 'error', $fn->cms['page_title']);
  }
  $fn->redirecting('account/change-password');
 }
 $breadcrumb = array('Account' => $fn->permalink('account'));
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 include app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
            <div class="row">
                <div class="header-for-light">
                    <h1 class="wow fadeInRight animated" data-wow-duration="1s">Change <span>Password</span></h1>
                </div>
                <form method="post" class="form-validate" id="update-frm" name="update-frm"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="change[old]" class="input-label">Current Password <span
                                        class="required">*</span></label>
                            <input type="password" name="change[old]" id="change-old" class="form-control"
                                   placeholder="Old Password" required/>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <label for="change-new" class="input-label req">New Password <span
                                        class="required">*</span></label>
                            <input type="password" name="change[new]" id="change-new" class="form-control"
                                   placeholder="New Password" required/>
                        </div>
                        <div class="col-md-6">
                            <label for="change-retype" class="input-label req">Retype New Password <span
                                        class="required">*</span></label>
                            <input type="password" name="change[retype]" id="change-retype" class="form-control"
                                   placeholder="Retype New Password"
                                   data-rule-equalto="#change-new" required/>
                        </div>

                    <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                    <button type="submit" name="btn_update" value="1" class="btn-default-1 pull-right">Save Changes
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
 include app_path . 'inc' . ds . 'footer.php';
 include app_path . 'inc' . ds . 'foot.php';
?>