<?php
 $str = '';
 ob_start();
 if ($fn->data) {
  foreach ($fn->data as $k => $v) {
   $files = $fn->get_product_images($v['product_image']);
   $front = $files[key($files)];
   $back = end($files) ? end($files) : $front;
   ?>
      <div class="col-sm-6 col-md-4 col-xs-6 text-center mb-25">
          <article class="product light">
              <figure class="figure-hover-overlay">
                  <a href="<?php echo $fn->permalink('product-detail', $v); ?>" class="figure-href"></a>
                  <div class="product-new">new</div>
               <?php if ($v['discount'] > 0) { ?>
                   <div class="product-sale"><?php echo $v['discount']; ?>% <br> off</div>
               <?php }
                if ($v['in_stock'] == 'Y') { ?>
                    <a href="<?php echo $fn->permalink('checkout'); ?>" class="product-wishlist" data-toggle="tooltip" data-placement="left" title="Buy Now" data-ajaxify="true" data-action="go_checkout" data-url="cart" data-app="<?php echo $fn->encrypt_post_data(array('id' => $v['id'], 'title' => $v['product_title'])); ?>" data-recid="modal">
                        <i class="fa fa-bolt"></i>
                    </a>
                    <a href="<?php echo $fn->permalink('product-detail', $v); ?>" class="product-compare" data-ajaxify="true" data-action="add_to_cart" data-url="cart" data-app="<?php echo $fn->encrypt_post_data(array('id' => $v['id'], 'title' => $v['product_title'])); ?>" data-recid="modal">
                        <i class="fa fa-shopping-cart"></i>
                    </a>
                <?php } ?>
                  <img class="img-overlay img-responsive lazy"
                       src="<?php echo $fn->permalink('assets/img/preloader.svg') ?>"
                       data-src="<?php echo $fn->get_file($back, 0, 315); ?>"
                       alt="<?php echo $v['product_title']; ?>"
                       title="<?php echo $v['product_title']; ?>">
                  <img class="img-responsive lazy" src="<?php echo $fn->permalink('assets/img/preloader.svg'); ?>"
                       data-src="<?php echo $fn->get_file($front, 0, 315); ?>"
                       alt="<?php echo $v['product_title']; ?>"
                       title="<?php echo $v['product_title']; ?>">
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
                  <p class="description"><?php echo $fn->show_string($v['product_desc'], 50); ?></p> */ ?>
              </div>
          </article>
      </div>
   <?php
  }
  if ($fn->rows['total'] > $fn->rows['load']) { ?>
      <div class="clearfix"></div>
      <div class="col-sm-12 block-pagination">
          <ul class="pagination">
           <?php echo $fn->pagination->display_all(); ?>
          </ul>
      </div>
  <?php }
 } else { ?>
     <div class="col-md-12">
         <div class="alert alert-danger">Oops, no products found. Please try after sometime or later.
         </div>
     </div>
 <?php }
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
?>

