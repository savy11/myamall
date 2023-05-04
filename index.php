<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "autoload.php";
 $fn = new controllers\index;
 $fn->get_data();
 ob_start();
?>
    <link rel="stylesheet" type="text/css" href="<?php echo $fn->permalink('resources/vendor/owl-carousel/owl.carousel.min.css'); ?>"/>
<?php
 $fn->style = ob_get_clean();
 include app_path . 'inc' . ds . 'head.php';
 include app_path . 'inc' . ds . 'header.php';
 if ($fn->list['sliders']) {
  ?>
     <div class="revolution-container">
         <div class="revolution">
             <ul class="list-unstyled">
              <?php foreach ($fn->list['sliders'] as $k => $v) { ?>
                  <li data-transition="fade" data-slotamount="7" data-masterspeed="1500">
                      <!-- MAIN IMAGE -->
                      <img src="<?php echo $fn->get_file($v['slider_image']); ?>"
                           alt="<?php echo $v['slider_title']; ?>" class="defaultimg"
                           data-bgfit="cover"
                           data-bgposition="right center" data-bgrepeat="no-repeat"/>
                      <!-- LAYERS -->
                      <div class="tp-caption skewfromrightshort customout"
                           data-x="20"
                           data-y="250"
                           data-customout="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0.75;scaleY:0.75;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"
                           data-speed="500"
                           data-start="500"
                           data-easing="Power4.easeOut"
                           data-endspeed="500"
                           data-endeasing="Power4.easeIn"
                           data-captionhidden="on"
                           style="z-index: 4">
                          <h1><?php echo $v['slider_title']; ?></h1>
                      </div>
                      <div class="tp-caption punchline skewfromrightshort customout"
                           data-x="20"
                           data-y="335"
                           data-customout="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0.75;scaleY:0.75;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"
                           data-speed="500"
                           data-start="700"
                           data-easing="Power4.easeOut"
                           data-endspeed="500"
                           data-endeasing="Power4.easeIn"
                           data-captionhidden="on"
                           style="z-index: 4">
                          <h2><?php echo $v['slider_punchline']; ?></h2>
                      </div>
                      <div class="tp-caption customin customout hidden-xs"
                           data-x="20"
                           data-y="450"
                           data-customin="x:0;y:100;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:1;scaleY:3;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:0% 0%;"
                           data-customout="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0.75;scaleY:0.75;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"
                           data-speed="500"
                           data-start="1000"
                           data-easing="Power4.easeOut"
                           data-endspeed="500"
                           data-endeasing="Power4.easeIn"
                           data-captionhidden="on"
                           style="z-index: 2">
                          <a href="<?php echo $fn->permalink('products'); ?>" class="btn-home">Shop All</a>
                      </div>
                   <?php if ($v['slider_url'] != '') { ?>
                       <div class="tp-caption customin customout hidden-xs"
                            data-x="145"
                            data-y="430"
                            data-customin="x:0;y:100;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:1;scaleY:3;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:0% 0%;"
                            data-customout="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0.75;scaleY:0.75;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"
                            data-speed="500"
                            data-start="1200"
                            data-easing="Power4.easeOut"
                            data-endspeed="500"
                            data-endeasing="Power4.easeIn"
                            data-captionhidden="on"
                            style="z-index: 2">
                           <a href="<?php echo $v['slider_url']; ?>" class="btn-home">Read more</a>
                       </div>
                   <?php } ?>
                  </li>
              <?php } ?>
             </ul>
             <div class="revolutiontimer"></div>
         </div>
     </div>
  <?php
 }
 if ($fn->list['sale']) {
  foreach ($fn->list['sale'] as $k => $v) {
   ?>
      <section id="sale-<?php echo $v['id']; ?>" class="sale-section">
          <div class="block color-scheme-white-90">
              <div class="container">
                  <div class="header-for-light">
                      <h1 class="wow fadeInRight animated" data-wow-duration="1s"><?php echo $v['sale_title']; ?></h1>
                  </div>
                  <div class="row">
                      <div class="col-md-3">
                          <div class="title-block light wow fadeInLeft">
                              <h2>Hurry Up! Sale Ends In:</h2>
                              <div class="countdown" data-cnt-date="<?php echo $fn->date_format($v['end_date'] . ' 23:59:59', 'M d, Y H:i:s'); ?>"></div>
                          </div>
                      </div>
                      <div class="col-md-9">
                          <div id="owl-sale" class="owl-carousel">
                           <?php
                            if ($v['products']) {
                             foreach ($v['products'] as $key => $val) {
                              $discount = round(($val['basic_price'] * 100 / $val['special_price']));
                              $files = $fn->get_product_images($val['product_image']);
                              $front = $files[key($files)];
                              $back = end($files) ? end($files) : $front;
                              ?>
                                 <div class="text-center">
                                     <article class="product light wow fadeInUp">
                                         <figure class="figure-hover-overlay">
                                             <a href="<?php echo $fn->permalink('product-detail', $val); ?>" class="figure-href"></a>
                                             <div class="product-new">SALE</div>
                                          <?php if ($discount > 0) { ?>
                                              <div class="product-sale"><?php echo $discount; ?>% <br> off</div>
                                          <?php }
                                           if ($val['in_stock'] == 'Y') { ?>
                                               <a href="<?php echo $fn->permalink('checkout'); ?>" class="product-wishlist" data-toggle="tooltip" data-placement="left" title="View Checkout">
                                                   <i class="fa fa-check"></i>
                                               </a>
                                               <a href="<?php echo $fn->permalink('product-detail', $val); ?>" data-toggle="tooltip" data-placement="left" title="Add to Cart" class="product-compare" data-ajaxify="true" data-action="add_to_cart" data-url="cart" data-app="<?php echo $fn->encrypt_post_data(array('id' => $val['id'], 'sale' => $v['id'], 'title' => $val['product_title'])); ?>" data-recid="modal">
                                                   <i class="fa fa-shopping-cart"></i>
                                               </a>
                                           <?php } ?>
                                             <img class="img-overlay img-responsive owl-lazy" src="<?php echo $fn->permalink('assets/img/preloader.svg') ?>" data-src="<?php echo $fn->get_file($back, 0, 315); ?>" alt="<?php echo $val['product_title']; ?>"/>
                                             <img class="img-responsive owl-lazy" src="<?php echo $fn->permalink('assets/img/preloader.svg') ?>" data-src="<?php echo $fn->get_file($front, 0, 315); ?>" alt="<?php echo $val['product_title']; ?>"/>
                                         </figure>
                                         <div class="product-caption">
                                             <div class="block-name">
                                              <?php if ($val['total_sold'] > 0) { ?>
                                                  <span class="sold"><?php echo 'Sold ' . $val['total_sold']; ?></span>
                                              <?php } ?>
                                                 <a href="<?php echo $fn->permalink('product-detail', $val); ?>" class="product-name text-ellipsis"><?php echo $val['product_title']; ?></a>
                                                 <p class="product-price">
                                                  <?php echo $val['special_price'] > 0 ? '<span>' . $fn->show_price($val['special_price']) . '</span>' : ''; ?>
                                                  <?php echo $fn->show_price($val['basic_price']); ?>
                                                 </p>
                                             </div>
                                         </div>
                                     </article>
                                 </div>
                              <?php
                             }
                            } ?>
                          </div>
                      </div>
                  </div>

              </div>
          </div>
      </section>
   <?php
  }
 }
 if ($fn->list['categories']) { ?>
     <section class="categories">
         <div class="block color-scheme-3">
             <div class="container">
                 <div id="owl-category" class="owl-carousel">
                  <?php foreach ($fn->list['categories'] as $v) { ?>
                      <div class="item">
                          <div class="category">
                              <a href="<?php echo $fn->permalink('product-cat', $v); ?>">
                                  <img src="<?php echo $fn->permalink('assets/img/preloader.svg'); ?>"
                                       data-src="<?php echo $fn->get_file($v['category_image']); ?>" class="img-responsive owl-lazy"
                                       alt="<?php echo $v['category_name']; ?>"/>
                                  <h6><?php echo $v['category_name']; ?></h6>
                              </a>
                          </div>
                      </div>
                  <?php } ?>
                 </div>
             </div>
         </div>
     </section>
 <?php }
 if ($fn->list['products']) { ?>
     <section class="block color-scheme-2">
         <div class="container">
          <?php
           foreach ($fn->list['products'] as $cat_id => $data) {
            $cat_data = $fn->varv($cat_id, $fn->list['cats']);
            ?>
               <div class="header-for-light">
                   <h1 class="wow fadeInRight animated"
                       data-wow-duration="1s">
                    <?php echo $fn->varv('category_name', $cat_data); ?>
                       <a href="<?php echo $fn->permalink('product-cat', $cat_data); ?>" class="btn btn-xs btn-info">View All</a>
                   </h1>
               </div>
               <div id="owl-products-<?php echo $cat_id; ?>" class="owl-carousel">
                <?php foreach ($data as $k => $v) {
                 $files = $fn->get_product_images($v['product_image']);
                 $front = $files[key($files)];
                 $back = end($files) ? end($files) : $front;
                 ?>
                    <div class="text-center item">
                        <article class="product light">
                            <figure class="figure-hover-overlay">
                                <a href="<?php echo $fn->permalink('product-detail', $v); ?>" class="figure-href"></a>
                                <div class="product-new">NEW</div>
                             <?php if ($v['discount'] > 0) { ?>
                                 <div class="product-sale"><?php echo $v['discount']; ?>% <br> off</div>
                             <?php }
                              if ($v['in_stock'] == 'Y') { ?>
                                  <a href="<?php echo $fn->permalink('checkout'); ?>" class="product-wishlist" data-toggle="tooltip" data-placement="left" title="Buy Now" data-ajaxify="true" data-action="go_checkout" data-url="cart" data-app="<?php echo $fn->encrypt_post_data(array('id' => $v['id'], 'title' => $v['product_title'])); ?>" data-recid="modal">
                                      <i class="fa fa-bolt"></i>
                                  </a>
                                  <a href="<?php echo $fn->permalink('product-detail', $v); ?>" data-toggle="tooltip" data-placement="left" title="Add to Cart" class="product-compare" data-ajaxify="true" data-action="add_to_cart" data-url="cart" data-app="<?php echo $fn->encrypt_post_data(array('id' => $v['id'], 'title' => $v['product_title'])); ?>" data-recid="modal">
                                      <i class="fa fa-shopping-cart"></i>
                                  </a>
                              <?php } ?>
                                <img class="img-overlay img-responsive lazy"
                                     src="<?php echo $fn->permalink('assets/img/preloader.svg') ?>"
                                     data-src="<?php echo $fn->get_file($back, 0, 315); ?>"
                                     alt="<?php echo $v['product_title']; ?>"/>
                                <img class="img-responsive lazy"
                                     src="<?php echo $fn->permalink('assets/img/preloader.svg') ?>"
                                     data-src="<?php echo $fn->get_file($front, 0, 315); ?>"
                                     alt="<?php echo $v['product_title']; ?>"/>
                            </figure>
                            <div class="product-caption">
                                <div class="block-name">
                                 <?php if ($v['total_sold'] > 0) { ?>
                                     <span class="sold"><?php echo 'Sold ' . $v['total_sold']; ?></span>
                                 <?php } ?>
                                    <a href="<?php echo $fn->permalink('product-detail', $v); ?>"
                                       class="product-name text-ellipsis"><?php echo $v['product_title']; ?></a>
                                    <p class="product-price">
                                     <?php echo $v['special_price'] > 0 ? '<span>' . $fn->show_price($v['basic_price']) . '</span> ' . $fn->show_price($v['special_price']) : $fn->show_price($v['basic_price']); ?>
                                    </p>
                                </div>
                             <?php /* <div class="product-rating">
                                       <div class="stars">
                                           <span class="star"></span><span class="star active"></span><span
                                                   class="star active"></span><span class="star active"></span><span
                                                   class="star active"></span>
                                       </div>
                                       <a href="<?php echo $fn->permalink('product-detail', $v); ?>" class="review">8
                                           Reviews</a>
                                   </div>
                               <p class="description"><?php echo $fn->show_string($v['product_desc'], 50);
                                ?></p>*/ ?>
                            </div>

                        </article>
                    </div>
                <?php } ?>
               </div>
           
           <?php } ?>
         </div>
     </section>
  <?php
 }
 if ($fn->list['brands']) { ?>
     <section class="partners">
         <div class="block color-scheme-dark-90">
             <div class="container">
                 <div class="header-for-light">
                     <h1 class="wow fadeInRight animated" data-wow-duration="2s">Our <span>Brands</span></h1>
                 </div>
                 <div id="owl-partners" class="owl-carousel">
                  <?php foreach ($fn->list['brands'] as $v) { ?>
                      <div class="partner">
                          <img src="<?php echo $fn->permalink('assets/img/preloader.svg') ?>"
                               data-src="<?php echo $fn->get_file($v['image']); ?>" class="img-responsive owl-lazy"
                               alt="<?php echo $v['title']; ?>" style="max-height: 100px;"/>
                      </div>
                  <?php } ?>
                 </div>
             </div>
         </div>
     </section>
 <?php } ?>
<?php
 include app_path . 'inc' . ds . 'footer.php';
 ob_start();
?>
    <script type="text/javascript" src="<?php echo $fn->permalink('resources/vendor/owl-carousel/owl.carousel.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo $fn->permalink('assets/js/vendor/jquery.themepunch.plugins.min.js');
    ?>"></script>
    <script type="text/javascript"
            src="<?php echo $fn->permalink('assets/js/vendor/jquery.themepunch.revolution.min.js'); ?>"></script>
    <script type="text/javascript">
        $(function () {
            var r = $('.revolution');
            if (r.length > 0) {
                r.show().revolution({
                    sliderType: "standard",
                    delay: 9000,
                    // startWidth: 1170,
                    // startHeight: 500,
                    // sliderLayout: "fullscreen",
                    responsiveLevels: [1240, 1024, 778, 480],
                    visibilityLevels: [1240, 1024, 778, 480],
                    gridWidth: [1240, 1024, 778, 480],
                    gridHeight: [500, 450, 400, 300],
                    autoHeight: 'off',
                    lazyType: "onDemand",
                    hideThumbs: 10,
                    spinner: 'spinner0',
                    fullWidth: "off",
                    fullScreen: "off",
                    navigationType: "none",
                    navigationArrows: "solo",
                    navigationStyle: "round",
                    navigationHAlign: "center",
                    navigationVAlign: "bottom",
                    navigationHOffset: 30,
                    navigationVOffset: 30,
                    soloArrowLeftHalign: "left",
                    soloArrowLeftValign: "center",
                    soloArrowLeftHOffset: 20,
                    soloArrowLeftVOffset: 0,
                    soloArrowRightHalign: "right",
                    soloArrowRightValign: "center",
                    soloArrowRightHOffset: 20,
                    soloArrowRightVOffset: 0,
                    touchenabled: "on"
                });
            }

            var c = $('.countdown');
            if (!c.length > 0)
                return;
            var t = c.data('cnt-date');
            if (t == undefined)
                return;
            var time = new Date(t).getTime();
            if (time > 0) {
                var x = setInterval(function () {

                    var now = new Date().getTime();

                    var distance = time - now;

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    var result = "<span class='days'><b>" + days + "</b> days </span><span class='hours'><b>" + hours + "</b> hours </span><span class='mins'><b>" + minutes + "</b> mins </span><span class='sec'><b>" + seconds + "</b> sec</span>";
                    c.html(result);

                    if (distance < 0) {
                        clearInterval(x);
                        c.parents('section').hide();
                    }
                }, 1000);
            }
        })
    </script>
<?php
 $fn->script = ob_get_clean();
 include app_path . 'inc' . ds . 'foot.php';
?>