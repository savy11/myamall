<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\blog;
 if ($fn->post('btn_comment')) {
  try {
   $fn->insert_comment();
   $fn->session_msg('Your comment has been submitted successfully. It will be publish after admin approval.', 'success');
   $fn->redirect($fn->permalink('blog-detail', $fn->cms));
  } catch (Exception $ex) {
   $fn->session_msg($ex->getMessage(), 'error');
  }
 }
 $fn->blog();
 $fn->populate_filters();
 include_once app_path . 'inc' . ds . 'head.php';
 include_once app_path . 'inc' . ds . 'header.php';
 $breadcrumb = ['Blog' => $fn->permalink('blog')];
 include_once app_path . 'inc' . ds . 'breadcrumb.php';
?>

    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="block-blog">
                    <img src="<?php echo $fn->get_file($fn->cms['blog_image']); ?>"
                         alt="<?php echo $fn->cms['blog_title']; ?>"
                         class="img-responsive blog-img"/>
                    <div class="block">
                        <div class="header-for-light">
                            <h1 class="wow fadeInRight animated"
                                data-wow-duration="1s"><?php echo $fn->cms['blog_title']; ?></h1>
                        </div>
                        <div><?php echo $fn->cms['blog_desc']; ?></div>
                     <?php if ($fn->list['recent']) { ?>
                         <div class="block">
                             <div class="header-for-light">
                                 <h4 class="wow fadeInRight animated" data-wow-duration="1s">RELATED <span>POSTS</span>
                                 </h4>
                             </div>

                             <div class="row">
                              <?php foreach ($fn->list['recent'] as $k => $v) { ?>
                                  <article class="col-md-4 text-center">
                                      <div class="blog">
                                          <figure class="figure-hover-overlay">
                                              <a href="<?php echo $fn->permalink('blog-detail', $v); ?>"
                                                 class="figure-href"></a>
                                              <i class="fa fa-comment"></i>
                                              <a href="<?php echo $fn->permalink('blog-detail', $v); ?>"
                                                 class="blog-link"><?php echo $v['total_comments']; ?></a>
                                              <img class="img-responsive"
                                                   src="<?php echo $fn->get_file($v['blog_image'], 0, 150); ?>"
                                                   alt="<?php echo $v['blog_title']; ?>"
                                                   title="<?php echo $v['blog_title']; ?>"/>
                                              <span class="bar"></span>
                                          </figure>
                                          <div class="blog-caption">
                                              <h3>
                                                  <a href="<?php echo $fn->permalink('blog-detail', $v); ?>"
                                                     class="blog-name"><?php echo $v['blog_title']; ?></a></h3>
                                              <p class="post-information">
                                                  <span><i class="fa fa-user"></i> By Admin</span>
                                                  <span><i class="fa fa-clock-o"></i> <?php echo $fn->dt_format($v['blog_date'], 'd F, Y'); ?></span>
                                              </p>
                                              <p><?php echo $fn->show_string($v['blog_desc'], 100); ?></p>
                                              <a href="<?php echo $fn->permalink('blog-detail', $v); ?>"
                                                 class="btn-read">Read more</a>
                                          </div>
                                      </div>
                                  </article>
                              <?php } ?>
                             </div>
                             <div class="header-for-light">
                                 <h4 class="wow fadeInRight animated" data-wow-duration="1s">Clients
                                     <span>comments</span></h4>
                             </div>
                          <?php if ($fn->comments) { ?>
                              <ul class="media-list list-unstyled">
                               <?php foreach ($fn->comments as $k => $v) {
                                echo include_once app_path . 'views' . ds . 'blog_comments.php';
                               } ?>
                              </ul>
                          <?php } else { ?>
                              <div class="alert alert-info">Be the first one to write comment using the below form.
                              </div>
                          <?php } ?>
                         </div>
                     <?php } ?>
                        <div class="block-form box-border">
                            <div class="header-for-light">
                                <h4 class="wow fadeInRight animated" data-wow-duration="1s">Leave a <span>comment</span>
                                    <button name="button" class="btn-default-1 btn-cancel-reply hide pull-right">Cancel
                                        Reply
                                    </button>
                                </h4>
                            </div>
                            <form name="comment-frm" id="comment-frm" class="form-validate" method="post"
                                  autocomplete="off">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">Name
                                                <span class="text-error">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   value="<?php echo $fn->post('name'); ?>" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="control-label">Email
                                                <span class="text-error">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                   value="<?php echo $fn->post('email'); ?>" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone" class="control-label">Phone No.
                                                <span class="text-error">*</span></label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                   value="<?php echo $fn->post('phone'); ?>" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="website" class="control-label">Website Url</label>
                                            <input type="url" class="form-control" id="website" name="website"
                                                   value="<?php echo $fn->post('website'); ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="comment" class="control-label">Comment
                                                <span class="text-error">*</span></label>
                                            <textarea class="form-control" id="comment" name="comment"
                                                      required><?php echo $fn->post('comment'); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="captcha">
                                            <label class="control-label">Captcha</label><br/>
                                            <img src="<?php echo $fn->permalink('captcha') . '?key=' . $fn->encrypt_post_data(array('for' => 'blog', 'color' => 1)) . '&' . ((float)rand() / (float)getrandmax()); ?>"
                                                 alt="Captcha" class="captcha-code"/>
                                            <a class="btn-link refresh-captcha"
                                               tabindex="-1">Refresh captcha</a><br/>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Security Code <span
                                                        class="text-error">*</span></label>
                                            <input type="text" name="captcha"
                                                   id="captcha"
                                                   class="form-control" maxlength="6"
                                                   required/>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <input type="hidden" name="parent" id="comment-parent" value="0"/>
                                <input type="hidden" name="id" value="<?php echo $fn->cms['id']; ?>"/>
                                <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
                                <input type="submit" class="btn-default-1" name="btn_comment" value="Submit">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <aside class="col-md-3">
             <?php echo include_once app_path . 'views' . ds . 'blog_sidebar.php'; ?>
            </aside>

        </div>
    </div>

<?php
 include_once app_path . 'inc' . ds . 'footer.php';
 include_once app_path . 'inc' . ds . 'foot.php';
?>