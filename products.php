<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\products;
 if ($fn->is_ajax_call()) {
  header('Content-Type: application/json');
  $json = '';
  
  if ($fn->post('action') == 'filter') {
   $fn->products();
   $url = explode('?', $fn->server('HTTP_REFERER'))[0];
   if (count($fn->filter) > 0) {
    $url .= '?' . urldecode(http_build_query($fn->filter));
   }
   $json = ['success' => true, 'html' => include app_path . 'views' . ds . 'products.php', 'script' => 'app.change_url(\'\', \'' . $url . '\');'];
  }
  if ($fn->post('action') == 'view_type') {
   $action = $fn->post('action');
   $fn->products();
   $json = ['success' => true, 'html' => include app_path . 'views' . ds . 'products.php'];
   if ($action == 'view_type') {
    $json = array_merge($json, ['script' => '$(\'.product-view-mode a\').removeClass(\'active\');$(\'#' . $fn->post('view') . '-view\').addClass(\'active\');']);
   }
  }
  
  if ($json) {
   echo $fn->json_encode($json);
  }
  exit();
 }
 
 $fn->products();
 $fn->sidebar();
 $encrypt = [];
 if ($fn->get('type') != '') {
  $breadcrumb = ['Products' => $fn->permalink('products')];
  $encrypt = array_merge($encrypt, ['type' => $fn->get('type')]);
 }
 if ($fn->get('url') != '') {
  $breadcrumb = array_merge($breadcrumb, [ucfirst($fn->get('type')) => $fn->permalink('product-parent', ['page_url' => $fn->get('type')])]);
  $encrypt = array_merge($encrypt, ['url' => $fn->get('url')]);
 }
 ob_start();
?>
    <link rel="stylesheet" type="text/css" href="<?php echo $fn->permalink('resources/vendor/owl-carousel/owl.carousel.min.css'); ?>"/>
<?php
 $fn->style = ob_get_clean();
 include_once app_path . 'inc' . ds . 'head.php';
 include_once app_path . 'inc' . ds . 'header.php';
 include_once app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="header-for-light">
                    <h1 class="wow fadeInRight animated"
                        data-wow-duration="1s"><?php echo $fn->cms['page_heading']; ?></h1>
                </div>
                <div class="block-products-modes color-scheme-2">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="product-view-mode">
                                <a href="?view=grid" id="grid-view" data-ajaxify="true" data-page="true"
                                   data-url="products" data-action="view_type"
                                   data-app="<?php echo $fn->encrypt_post_data(array_merge($encrypt, ['view' => 'grid'])) ?>"
                                   data-recid="result"<?php echo $fn->session('view') == 'grid' ? ' class="active"' : ''; ?>>
                                    <i class="fa fa-th-large"></i>
                                </a>
                                <a href="?view=list" id="list-view" data-ajaxify="true" data-page="true"
                                   data-url="products" data-action="view_type"
                                   data-app="<?php echo $fn->encrypt_post_data(array_merge($encrypt, ['view' => 'list'])) ?>"
                                   data-recid="result"<?php echo $fn->session('view') == 'list' ? ' class="active"' : ''; ?>>
                                    <i class="fa fa-th-list"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="row">
                                <div class="col-md-3 col-md-offset-1">
                                    <label class="pull-right">Sort by</label>
                                </div>
                                <div class="col-md-5">
                                    <select name="sort" id="sort" class="form-control no-select"
                                            data-ajaxify="true"
                                            data-url="products" data-page="true" data-action="filter"
                                            data-app="<?php echo $fn->encrypt_post_data($encrypt); ?>"
                                            data-recid="result" data-event="change" data-input-val="per_page">
                                        <option value="">- Sort -</option>
                                     <?php echo $fn->show_list($fn->order, $fn->post_get('sort'), false); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="per_page" id="per_page" class="form-control no-select"
                                            data-ajaxify="true" data-url="products" data-page="true"
                                            data-action="filter" data-recid="result" data-event="change"
                                            data-app="<?php echo $fn->encrypt_post_data($encrypt); ?>"
                                            data-input-val="sort">
                                     <?php echo $fn->show_list($fn->per_page, $fn->post_get('per_page'), false); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="result">
                 <?php echo include_once app_path . 'views' . ds . 'products.php'; ?>
                </div>
            </div>
            <aside class="col-md-3">
             <?php echo include_once app_path . 'views' . ds . 'products_sidebar.php'; ?>
            </aside>

        </div>
    </div>
<?php
 ob_start();
?>
    <script type="text/javascript" src="<?php echo $fn->permalink('resources/vendor/owl-carousel/owl.carousel.min.js'); ?>"></script>
<?php
 $fn->script = ob_get_clean();
 
 include_once app_path . 'inc' . ds . 'footer.php';
 include_once app_path . 'inc' . ds . 'foot.php';
?>