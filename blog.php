<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\blog;
 $fn->cms_page('blog');
 include_once app_path . 'inc' . ds . 'head.php';
 include_once app_path . 'inc' . ds . 'header.php';
 $fn->blogs();
 $fn->populate_filters();
 if ($fn->get('type') != ''){
     $breadcrumb = ['Blog' => $fn->permalink('blog')];
 }
 include_once app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="row">

            <div class="col-md-9">
                <div class="header-for-light">
                    <h1 class="wow fadeInRight animated" data-wow-duration="1s">Our <span>Blog</span></h1>
                </div>
             <?php if ($fn->data) { ?>
                 <div class="block">
                     <div class="row">
                      <?php foreach ($fn->data as $k => $v) { ?>
                          <article class="col-md-6 text-center">
                              <div class="blog">
                                  <figure class="figure-hover-overlay">
                                      <a href="<?php echo $fn->permalink('blog-detail', $v); ?>"
                                         class="figure-href"></a>

                                      <i class="fa fa-comment"></i>
                                      <a href="<?php echo $fn->permalink('blog-detail', $v); ?>#comments"
                                         class="blog-link"><?php echo $v['total_comments']; ?></a>
                                      <img class="img-responsive" src="<?php echo $fn->get_file($v['blog_image'], 0,
                                       228); ?>" alt="<?php echo $v['blog_title']; ?>"
                                           title="<?php echo $v['blog_title'];
                                           ?>"/>
                                      <span class="bar"></span>
                                  </figure>
                                  <div class="blog-caption">
                                      <h3><a href="<?php echo $fn->permalink('blog-detail', $v); ?>"
                                             class="blog-name text-ellipsis"><?php echo $v['blog_title']; ?></a></h3>
                                      <p class="post-information">
                                          <span><i class="fa fa-user"></i> By Admin</span>
                                          <span><i class="fa fa-clock-o"></i> <?php echo $fn->dt_format
                                           ($v['blog_date'], 'd F, Y'); ?></span>
                                      </p>
                                      <p><?php echo $fn->show_string($v['blog_desc'], 100); ?></p>
                                      <a href="<?php echo $fn->permalink('blog-detail', $v); ?>" class="btn-read">Read
                                          more</a>
                                  </div>
                              </div>
                          </article>
                      <?php }
                      ?>
                     </div>
                 </div>
              <?php
             } else { ?>
                 <div class="alert alert-danger">Oops, No blog found. Please try again later or sometime.</div>
             <?php } ?>

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