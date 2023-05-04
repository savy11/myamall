<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\contact;
 if ($fn->post('btn_contact')) {
  try {
   $fn->contact_enq();
   $fn->session_msg('Your contact request has been submitted successfully.', 'success');
   $fn->redirecting('contact');
  } catch (Exception $ex) {
   $fn->session_msg($ex->getMessage(), 'error');
  }
 }
 include_once app_path . 'inc' . ds . 'head.php';
 include_once app_path . 'inc' . ds . 'header.php';
 include_once app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="header-for-light">
            <h1 class="wow fadeInRight animated" data-wow-duration="1s"><span>Contact</span> Information </h1>
        </div>
        <div class="row">
            <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
                    <h3><i class="fa fa-envelope-o"></i>Send Message</h3>
                    <hr/>
                    <div id="form-wrapper">
                        <div id="form-inner">
                            <form class="form-validate" id="contact-frm" name="contact-frm" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Name <span class="text-error">*</span></label>
                                            <input type="text" class="form-control" name="contact[name]"
                                                   id="contact_name" value="<?php echo $fn->post('name'); ?>"
                                                   required/>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Email <span class="text-error">*</span></label>
                                            <input type="email" class="form-control" name="contact[email]"
                                                   id="contact_email" value="<?php echo $fn->post('email'); ?>"
                                                   required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Phone <span class="text-error">*</span></label>
                                            <input type="text" name="contact[no]" class="form-control"
                                                   id="contact_no" value="<?php echo $fn->post('no'); ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Subject <span
                                                        class="text-error">*</span></label>
                                            <input type="text" name="contact[subject]" class="form-control"
                                                   id="contact_subject" value="<?php echo $fn->post('subject'); ?>"
                                                   required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Message <span
                                                        class="text-error">*</span></label>
                                            <textarea name="contact[message]" id="contact_message" class="form-control"
                                                      rows="4" required><?php echo $fn->post('message'); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="captcha">
                                            <label class="control-label">Captcha</label><br/>
                                            <img src="<?php echo $fn->permalink('captcha') . '?key=' . $fn->encrypt_post_data(array('for' => 'contact', 'color' => 1)) . '&' . ((float)rand() / (float)getrandmax()); ?>"
                                                 alt="Captcha" class="captcha-code"/>
                                            <a class="btn-link pull-left refresh-captcha"
                                               tabindex="-1">Refresh captcha</a><br/>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Security Code <span
                                                        class="text-error">*</span></label>
                                            <input type="text" name="contact[captcha]"
                                                   id="contact-captcha"
                                                   class="form-control" maxlength="6"
                                                   required/>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>" />
                                <input type="submit" name="btn_contact" class="btn-default-1 contact-btn"
                                       value="Submit">
                            </form>
                        </div>
                    </div>
                </div>
            </article>
            <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div class="block-form box-border wow fadeInRight animated" data-wow-duration="1s">
                    <h3><i class="fa fa-adn"></i>Map</h3>
                    <hr>
                    <div class="google-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3423.616963952789!2d121.46113131546525!3d30.897374584729295!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x35b285467d7120dd%3A0x10529aa614951a77!2s95%20Jianghai%20S%20Rd%2C%20Fengxian%20Qu%2C%20Shanghai%20Shi%2C%20China%2C%20201416!5e0!3m2!1sen!2sin!4v1589828303443!5m2!1sen!2sin" style="overflow:hidden;height:100%;width:100%;" frameborder="0"></iframe>
                    </div>

                </div>
            </article>

        </div>
    </div>
<?php
 include_once app_path . 'inc' . ds . 'footer.php';
 include_once app_path . 'inc' . ds . 'foot.php';
?>