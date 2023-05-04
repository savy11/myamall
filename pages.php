<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\controller;
 $fn->cms_page($fn->get('page_url'));
 if (!$fn->cms) {
  $fn->not_found();
 }
 include_once app_path . 'inc' . ds . 'head.php';
 include_once app_path . 'inc' . ds . 'header.php';
 include_once app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <section>
        <div class="container">
            <div class="row">
                <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
                    <h3><i class="fa fa-thumb-tack"></i><?php echo $fn->varv('page_heading', $fn->cms); ?></h3>
                    <div><?php echo $fn->cms['page_desc']; ?></div>
                </div>
            </div>
        </div>
    </section>

<?php
 include_once app_path . 'inc' . ds . 'footer.php';
 include_once app_path . 'inc' . ds . 'foot.php';
?>