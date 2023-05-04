<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\controller;
 $fn->cms_page('404');
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
?>
    <section>
        <div class="second-page-container">
            <div class="block">
                <div class="container">
                    <div class="header-for-light">
                        <h1 class="wow fadeInRight animated" data-wow-duration="1s">Page not <span>found</span></h1>
                    </div>
                    <div class="row">
                        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
                                <h3><i class="fa fa-file-excel-o"></i>404</h3>
                                <p>The page you were looking for could not be found.</p>
                                <hr>
                                <a href="404.html#" class="btn-default-1">Back Home</a>
                            </div>
                        </article>
                        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="block-form box-border wow fadeInRight animated" data-wow-duration="1s">
                                <h3><i class="fa fa-info"></i>Information</h3>
                                <hr>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                    exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute
                                    irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla
                                    pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                                    deserunt mollit anim id est laborum.
                                </p>

                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
 include app_path . 'inc' . ds . 'footer.php';
 include app_path . 'inc' . ds . 'foot.php';
?>