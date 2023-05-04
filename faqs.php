<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\controller;
 $fn->cms_page($fn->get('page_url'));
 $data = $fn->get_faqs();
 if (!$fn->cms || !$data) {
  $fn->not_found();
 }
 ob_start();
?>
    <style type="text/css">
        .panel-group .panel-title a:focus {
            text-decoration: none;
        }
    </style>
<?php
 $fn->style = ob_get_clean();
 include_once app_path . 'inc' . ds . 'head.php';
 include_once app_path . 'inc' . ds . 'header.php';
 include_once app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <section>
        <div class="container">
            <div class="row">
                <div class="block-form box-border wow fadeInLeft animated" data-wow-duration="1s">
                    <h3><i class="fa fa-thumb-tack"></i><?php echo $fn->varv('page_heading', $fn->cms); ?></h3>
                 <?php if ($data) { ?>
                     <div id="faqs" class="panel-group accordion">
                      <?php
                       $i = 0;
                       foreach ($data as $k => $v) { ?>
                          <div class="panel">
                              <div class="panel-title">
                                  <a data-parent="#faqs" data-toggle="collapse" href="#faq_<?php echo $k ?>"<?php echo $i == 0 ? ' class="active" aria-expanded="true"' : ' class="collapsed" aria-expanded="false"'; ?>><strong>Q. <?php echo $v['question']; ?></strong></a>
                              </div>
                              <div id="faq_<?php echo $k ?>" class="panel-collapse collapse<?php echo $i == 0 ? ' in' : ''; ?>" role="tablist" aria-expanded="<?php echo $k == 0 ? 'true' : 'false'; ?>">
                                  <div class="panel-content">
                                      <div class="panel-body">
                                       <?php echo $v['answer']; ?>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      <?php
                       $i++;
                       } ?>
                     </div>
                 <?php } else { ?>
                     <div class="alert alert-danger">Oops, No FAQ's found right now. Please try after sometime or later.</div>
                 <?php } ?>
                </div>
            </div>
        </div>
    </section>

<?php
 include_once app_path . 'inc' . ds . 'footer.php';
 include_once app_path . 'inc' . ds . 'foot.php';
?>