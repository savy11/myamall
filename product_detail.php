<?php
 require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';
 $fn = new controllers\products;
 $fn->product();
 $fn->sidebar();
 ob_start();
?>
    <link rel="stylesheet" type="text/css" href="<?php echo $fn->permalink('resources/vendor/owl-carousel/owl.carousel.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo $fn->permalink('resources/vendor/lightgallery/css/lightgallery.css') ?>"/>
    <style type="text/css">
        #gal1 button.owl-prev, #gal1 button.owl-next {
            position: absolute;
            top: 30%;
            display: block;
            width: 40px;
            height: 40px;
            background-color: rgba(235, 44, 51, .8);
            font-size: 20px;
            line-height: 40px;
            color: #fff;
            left: 0;
            transition: all ease .3s;
            -webkit-transition: all ease .3s;
            -moz-transition: all ease .3s;
            -ie-transition: all ease .3s;
            -o-transition: all ease .3s;
            opacity: 0;
        }

        #gal1 button.owl-next {
            left: auto;
            right: 0;
        }

        #gal1:hover button {
            opacity: 1;
        }

        .list-inline > li {
            padding-left: 0;
        }
    </style>
<?php
 $fn->style = ob_get_clean();
 include_once app_path . 'inc' . ds . 'head.php';
 include_once app_path . 'inc' . ds . 'header.php';
 $breadcrumb = ['Products' => $fn->permalink('products')];
 if ($fn->get('parent') != '') {
  $breadcrumb = array_merge($breadcrumb, [ucfirst($fn->get('parent')) => $fn->permalink('product-parent', ['page_url' => $fn->get('parent')])]);
 }
 if ($fn->get('category') != '') {
  $breadcrumb = array_merge($breadcrumb, [ucfirst($fn->get('category')) => $fn->permalink('product-cat', ['parent_url' => $fn->get('parent'), 'page_url' => $fn->get('category')])]);
 }
 include_once app_path . 'inc' . ds . 'breadcrumb.php';
?>
    <div class="container">
        <div class="row">

            <div class="col-md-9">

                <div class="header-for-light">
                    <h1 class="wow fadeInRight animated"
                        data-wow-duration="1s"><?php echo $fn->cms['product_title']; ?></h1>

                </div>

                <div class="block-product-detail">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                         <?php if ($fn->cms['files']) {
                          $default = $fn->cms['files'][0];
                          ?>
                             <div class="product-image">
                                 <img id="product-zoom" src="<?php echo $fn->get_file($default['meta_value']);
                                 ?>" data-zoom-image="<?php echo $fn->get_file($default['meta_value']); ?>"
                                      alt="<?php echo $fn->cms['product_title']; ?>"/>
                             </div>
                             <div id="gal1" class="owl-carousel">
                              <?php foreach ($fn->cms['files'] as $k => $v) { ?>
                                  <a href="<?php echo $fn->get_file($v['meta_value']); ?>" class="item"
                                     data-title="<?php echo $fn->cms['product_title']; ?>"
                                     data-src="<?php echo $fn->get_file($v['meta_value'], 0, 315); ?>"
                                     data-image="<?php echo $fn->get_file($v['meta_value'], 0, 315); ?>"
                                     data-zoom-image="<?php echo $fn->get_file($v['meta_value']); ?>">
                                      <img class="owl-lazy" src="<?php echo $fn->permalink('assets/img/preloader.svg'); ?>" data-src="<?php echo $fn->get_file($v['meta_value'], 0, 0, 80); ?>" alt="<?php echo $fn->cms['product_title']; ?>">
                                  </a>
                              <?php } ?>
                             </div>
                         <?php } ?>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

                            <div class="product-detail-section">
                             <?php /*<div class="product-rating">
                                    <div class="stars">
                                        <span class="star"></span><span class="star active"></span><span
                                                class="star active"></span><span
                                                class="star active"></span><span class="star active"></span>
                                    </div>
                                    <a href="product-detail2.html" class="review">150 Reviews</a>
                                </div> */ ?>
                             
                             <?php if ($fn->cms['total_sold'] > 0) { ?>
                                 <div class="alert alert-info"><strong><?php echo $fn->cms['total_sold']; ?></strong> products already sold. <strong>Hurry up</strong> to get yours!</div>
                             <?php } ?>
                                <div class="product-information row">
                                 <?php if ($fn->varv('sale_id', $fn->cms)) { ?>
                                     <div class="form-group clearfix">
                                         <label class="col-sm-3">In Sale:</label>
                                         <div class="col-sm-9"><a href="<?php echo $fn->permalink() . '#sale-' . $fn->cms['sale_id']; ?>"><i class="fa fa-link"></i> <?php echo $fn->cms['sale_title']; ?></a></div>
                                     </div>
                                 <?php } ?>
                                    <div class="form-group clearfix">
                                        <label class="col-sm-3">Code:</label>
                                        <div class="col-sm-9"><?php echo $fn->cms['product_code'] ? $fn->cms['product_code'] : '-' ?></div>
                                    </div>
                                 <?php /* <div class="clearfix">
                                        <label class="pull-left">Size:</label>
                                        <select name="size" class="form-control">
                                            <option value="" selected="selected">...</option>
                                            <option value="1">L</option>
                                            <option value="2">XL</option>
                                            <option value="3">XLL</option>
                                        </select>
                                    </div>*/ ?>
                                    <div class="form-group clearfix">
                                        <label class="col-sm-3">Availability:</label>
                                        <p class="col-sm-9">
                                            <span class="label label-<?php echo $fn->varv($fn->cms['in_stock'], $fn->yes_no_label); ?>"><?php echo $fn->cms['in_stock'] == 'Y' ? 'In Stock' : 'Out of Stock'; ?></span>
                                        </p>
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-sm-3">Description:</label>
                                        <p class="description col-sm-9"><?php echo $fn->show_string($fn->cms['product_desc'], 80); ?></p>
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-sm-3">Price:</label>
                                        <p class="product-price col-sm-9"<?php echo (!$fn->varv('sale_id', $fn->cms)) ? ' id="price"' : ''; ?>>
                                         <?php if ($fn->varv('sale_id', $fn->cms)) {
                                          echo '<del>' . ($fn->cms['special_price'] > 0 ? '<span>' . $fn->show_price($fn->cms['basic_price']) . '</span> ' . $fn->show_price($fn->cms['special_price']) : $fn->show_price($fn->cms['basic_price'])) . '</del>';
                                         } else {
                                          echo $fn->cms['special_price'] > 0 ? '<span>' . $fn->show_price($fn->cms['basic_price']) . '</span> ' . $fn->show_price($fn->cms['special_price']) : $fn->show_price($fn->cms['basic_price']);
                                         } ?>
                                        </p>
                                    </div>
                                 <?php if ($fn->varv('sale_id', $fn->cms)) { ?>
                                     <div class="form-group clearfix">
                                         <label class="col-sm-3">Sale Price:</label>
                                         <p class="product-price col-sm-9"><?php echo $fn->show_price($fn->cms['sale_price']); ?></p>
                                     </div>
                                  <?php
                                 }
                                  if ($fn->cms['in_stock'] == 'Y') { ?>
                                      <form class="form-validate" method="post" name="cart-frm" id="cart-frm"
                                            autocomplete="off" data-ajax="true" data-url="cart">
                                       <?php if ($fn->list['colors']) { ?>
                                           <div class="form-group clearfix">
                                               <label class="col-sm-3">Color Classification:</label>
                                               <div class="col-sm-9">
                                                   <ul class="list-inline classification">
                                                    <?php foreach ($fn->list['colors'] as $k => $v) { ?>
                                                        <li class="item">
                                                            <a href="<?php echo $fn->get_file($v['image'], 0, 315); ?>" data-zoom="<?php echo $fn->get_file($v['image'], 0, 315); ?>" data-toggle="tooltip" title="<?php echo $v['title']; ?>" data-sizes="<?php echo $v['sizes']; ?>" data-price="<?php echo $fn->show_price($v['basic_price']); ?>" data-id="<?php echo $v['id']; ?>" data-recid="color_id"
                                                               class="color<?php echo $fn->post('color') == $v['id'] ? ' active' : ''; ?>">
                                                                <img class="lazy" src="<?php echo $fn->permalink('assets/img/preloader.svg'); ?>" data-src="<?php echo $fn->get_file($v['image'], 0, 0, 50); ?>" alt="<?php echo $v['title']; ?>"/>

                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                   </ul>
                                                   <input type="hidden" name="color" id="color_id" value="<?php echo $fn->post('color'); ?>" required/>
                                               </div>
                                           </div>
                                       <?php }
                                        if ($fn->list['sizes']) { ?>
                                            <div class="form-group clearfix">
                                                <label class="col-sm-3">Size:</label>
                                                <div class="col-sm-9">
                                                    <ul class="list-inline classification">
                                                     <?php foreach ($fn->list['sizes'] as $id => $name) { ?>
                                                         <li class="item">
                                                             <a href="javascript:;" data-toggle="tooltip" title="<?php echo $name; ?>" data-id="<?php echo $id; ?>" data-recid="size_id" class="size<?php echo $fn->post('size') == $id ? ' active' : ''; ?>">
                                                                 <strong><?php echo $name; ?></strong>
                                                             </a>
                                                         </li>
                                                     <?php } ?>
                                                    </ul>
                                                    <input type="hidden" name="size" id="size_id" value="<?php echo $fn->post('size'); ?>" required/>
                                                </div>
                                            </div>
                                        <?php } ?>
                                          <div class="form-group clearfix">
                                              <label class="col-sm-3">Quantity:</label>
                                              <div class="col-sm-9">
                                                  <input type="number" name="qty" id="qty"
                                                         class="form-control numbers-only"
                                                         value="1" min="1" max="100" required/>
                                              </div>
                                          </div>
                                          <div class="col-sm-12 block-form">
                                              <input type="hidden" name="id" value="<?php echo $fn->cms['id']; ?>"/>
                                              <input type="hidden" name="title"
                                                     value="<?php echo $fn->cms['product_title']; ?>"/>
                                           <?php if ($fn->varv('sale_id', $fn->cms)) { ?>
                                               <input type="hidden" name="sale" value="<?php echo $fn->cms['sale_id']; ?>"/>
                                           <?php } ?>
                                              <input type="hidden" name="token"
                                                     value="<?php echo $fn->post_token(); ?>"/>
                                              <input type="hidden" name="modal" value="1"/>
                                              <input type="hidden"/>
                                              <button type="submit" name="action" value="add_to_cart" class="btn-default-1"><i class="fa fa-shopping-cart"></i> Add to cart</button>
                                              <button typeof="submit" name="action" value="go_checkout" class="btn-default-1"><i class="fa fa-bolt"></i> Buy Now</button>
                                          </div>
                                      </form>
                                  <?php } else { ?>
                                      <div class="alert alert-info">This product will be back soon.</div>
                                  <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-border block-form">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills  nav-justified">
                        <li class="active"><a href="#description" data-toggle="tab">Description</a>
                        </li>
                     <?php /*<li><a href="#additional" data-toggle="tab" class="disabled">Additional</a>
                        </li>
                        <li><a href="#review" data-toggle="tab">Review</a></li>*/ ?>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="description">
                            <br>
                            <h3>Product Details</h3>
                            <hr>
                         <?php
                          if ($fn->cms['product_desc']) {
                           echo $fn->cms['product_desc'];
                          } else {
                           echo '<div class="alert alert-info">Oops, No description found against this product.</div>';
                          } ?>
                        </div>
                     <?php /*<div class="tab-pane" id="additional">
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <h3>Sizes</h3>
                                    <hr>
                                    <p>
                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit ollit anim id
                                        est laborum.
                                    </p>

                                </div>
                                <div class="col-md-4 block-color">
                                    <h3>Colors</h3>
                                    <hr>
                                    <ul class="colors clearfix list-unstyled">
                                        <li><a href="product-detail2.html" rel="003d71"></a></li>
                                        <li><a href="product-detail2.html" rel="c42c39"></a></li>
                                        <li><a href="product-detail2.html" rel="f4bc08"></a></li>
                                        <li><a href="product-detail2.html" rel="02882c"></a></li>
                                        <li><a href="product-detail2.html" rel="000000"></a></li>
                                        <li><a href="product-detail2.html" rel="caccce"></a></li>
                                        <li><a href="product-detail2.html" rel="ffffff"></a></li>
                                        <li><a href="product-detail2.html" rel="f9e7b6"></a></li>
                                        <li><a href="product-detail2.html" rel="ef8a07"></a></li>
                                        <li><a href="product-detail2.html" rel="5a433f"></a></li>
                                        <li><a href="product-detail2.html" rel="ff9bb5"></a></li>
                                        <li><a href="product-detail2.html" rel="8c56a9"></a></li>
                                    </ul>

                                </div>
                                <div class="col-md-4">
                                    <h3>Other</h3>
                                    <hr>
                                    <p>
                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit ollit anim id
                                        est laborum.
                                    </p>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane" id="review">
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>Clients review</h3>
                                    <hr>
                                    <div class="review-header">
                                        <h5>John Smith</h5>
                                        <div class="product-rating">
                                            <div class="stars">
                                                <span class="star active"></span><span
                                                        class="star active"></span><span
                                                        class="star active"></span><span
                                                        class="star active"></span><span
                                                        class="star active"></span>
                                            </div>
                                        </div>
                                        <small class="text-muted">26/06/2014</small>
                                    </div>
                                    <div class="review-body">
                                        <p>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
                                            enim ad minim veniam.</p>

                                    </div>
                                    <hr>
                                    <div class="review-header">
                                        <h5>Tom Carry</h5>
                                        <div class="product-rating">
                                            <div class="stars">
                                                <span class="star"></span><span class="star active"></span><span
                                                        class="star active"></span><span
                                                        class="star active"></span><span
                                                        class="star active"></span>
                                            </div>
                                        </div>
                                        <small class="text-muted">05/07/2014</small>
                                    </div>
                                    <div class="review-body">
                                        <p>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
                                            enim ad minim veniam.</p>

                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <form role="form" method="post" action="product-detail2.html#">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="inputFirstName" class="control-label">First Name:<span
                                                        class="text-error">*</span></label>
                                            <div>
                                                <input type="text" class="form-control" id="inputFirstName">
                                            </div>
                                        </div>


                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="inputCompany" class="control-label">Company:</label>
                                            <div>
                                                <input type="text" class="form-control" id="inputCompany">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea class="form-control">    </textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Your Rate:</label>
                                            <div class="product-rating">
                                                <div class="stars">
                                                    <span class="star"></span><span class="star"></span><span
                                                            class="star"></span><span class="star"></span><span
                                                            class="star"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="submit" class="btn-default-1" value="Add Review">
                            </form>

                        </div>*/ ?>

                    </div>


                </div>


            </div>
            <div class="col-md-3">
             <?php echo include_once app_path . 'views' . ds . 'products_sidebar.php'; ?>
            </div>

        </div>
    </div>
<?php
 ob_start();
?>
    <script type="text/javascript" src="<?php echo $fn->permalink('resources/vendor/owl-carousel/owl.carousel.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo $fn->permalink('resources/vendor/lightgallery/js/lightgallery.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo $fn->permalink('assets/js/vendor/jquery.elevateZoom-3.0.8.min.js'); ?>"></script>
    <script type="text/javascript">
        $(function () {
            app.classification();
        })
    </script>
<?php
 $fn->script = ob_get_clean();
 include_once app_path . 'inc' . ds . 'footer.php';
 include_once app_path . 'inc' . ds . 'foot.php';
?>