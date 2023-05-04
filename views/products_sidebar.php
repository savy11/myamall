<?php
 $str = '';
 ob_start();
?>
<div class="main-category-block ">
    <div class="main-category-title">
        <i class="fa fa-list"></i> Category
    </div>
</div>
<div class="widget-block">
    <ul class="list-unstyled ul-side-category">
     <?php
      if ($fn->list['categories']) {
       foreach ($fn->list['categories'] as $k => $v) {
        $total = array_sum(array_column($v, 'total'));
        ?>
           <li>
               <a href="<?php echo $fn->permalink('product-parent', ['page_url' => $k]); ?>">
                   <i class="fa fa-angle-right"></i> <?php echo $fn->varv($k, $fn->list['parents']) . ' / ' . $total; ?>
               </a>
               <ul class="sub-category"<?php echo $fn->get('type') == $k ? ' style="display: block;"' : ''; ?>>
                <?php foreach ($v as $key => $val) { ?>
                    <li>
                        <a href="<?php echo $fn->permalink('product-cat', ['parent_url' => $k, 'page_url' => $val['id']]); ?>"><?php echo $val['name'] . ' / ' . $val['total']; ?></a>
                    </li>
                <?php } ?>
               </ul>
           </li>
        <?php
       }
      } else {
       ?>
          <li>
              <div class="alert alert-info">No categories found.</div>
          </li>
       <?php
      }
     ?>
    </ul>

</div>
<?php /*
                <div class="widget-title">
                    <i class="fa fa-money"></i> Price range
                </div>
                <div class="widget-block">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="text" id="price-from" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="text" id="price-to" class="form-control" value="500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widget-title">
                    <i class="fa fa-dashboard"></i> Colors

                </div>
                <div class="widget-block">
                    <ul class="colors clearfix list-unstyled">
                        <li><a href="products-grid.html" rel="003d71"></a></li>
                        <li><a href="products-grid.html" rel="c42c39"></a></li>
                        <li><a href="products-grid.html" rel="f4bc08"></a></li>
                        <li><a href="products-grid.html" rel="02882c"></a></li>
                        <li><a href="products-grid.html" rel="000000"></a></li>
                        <li><a href="products-grid.html" rel="caccce"></a></li>
                        <li><a href="products-grid.html" rel="ffffff"></a></li>
                        <li><a href="products-grid.html" rel="f9e7b6"></a></li>
                        <li><a href="products-grid.html" rel="ef8a07"></a></li>
                        <li><a href="products-grid.html" rel="5a433f"></a></li>
                    </ul>
                </div>  */ ?>
<?php if ($fn->list['best_offer']) { ?>
    <div class="widget-title">
        <i class="fa fa-thumbs-up"></i> Bestseller
    </div>
    <div class="owl-carousel products-carousel">
     <?php foreach ($fn->list['best_offer'] as $k => $v) { ?>
         <div class="widget-block">
             <div class="row">
                 <div class="col-md-4 col-sm-2 col-xs-3">
                     <img class="img-responsive owl-lazy" src="<?php echo $fn->permalink('assets/img/preloader.svg'); ?>"
                          data-src="<?php echo $fn->get_file($v['image'], 0, 0, 100); ?>"
                          alt="<?php echo $v['product_title']; ?>" title="<?php echo $v['product_title']; ?>"/>
                 </div>
                 <div class="col-md-8  col-sm-10 col-xs-9">
                     <div class="block-name">
                         <a href="<?php echo $fn->permalink('product-detail', $v); ?>"
                            class="product-name"><?php echo $v['product_title']; ?></a>
                         <p class="product-price">
                          <?php echo $v['special_price'] > 0 ? '<span>' . $fn->show_price($v['special_price']) . '</span>' : ''; ?>
                          <?php echo $fn->show_price($v['basic_price']); ?></p>

                     </div>
                     <p class="description"><?php echo $fn->show_string($v['product_desc'], 40); ?></p>
                 </div>
             </div>
         </div>
     <?php }
     ?>
    </div>
 <?php
}
 
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
?>

