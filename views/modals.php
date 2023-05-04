<?php
 $str = '';
 ob_start();
 if ($type == 'product') {
  $fn->modal = array('title' => $fn->data['product_title'], 'md_class' => ' modal-sm');
  ?>
     <form method="post" class="form-validate" name="cart-frm" id="cart-frm" data-ajax="true" data-url="cart"
           data-action="<?php echo $fn->post('action'); ?>">
         <div class="modal-body">
             <div class="row">
              <?php if ($fn->list['colors']) { ?>
                  <div class="col-sm-12 form-group">
                      <label class="control-label">Price: </label>
                      <span class="m-l-5"<?php echo (!$fn->varv('sale_id', $fn->data)) ? ' id="price"' : ''; ?>>
                        <?php if ($fn->varv('sale_id', $fn->data)) {
                         echo '<del>' . ($fn->data['special_price'] > 0 ? '<del><small>' . $fn->show_price($fn->data['basic_price']) . '</small></del> ' . $fn->show_price($fn->data['special_price']) : $fn->show_price($fn->data['basic_price'])) . '</del>';
                        } else {
                         echo $fn->data['special_price'] > 0 ? '<del><small>' . $fn->show_price($fn->data['basic_price']) . '</small></del> ' . $fn->show_price($fn->data['special_price']) : $fn->show_price($fn->data['basic_price']);
                        } ?>
                      </span>
                  </div>
               <?php if ($fn->varv('sale_id', $fn->data)) { ?>
                      <div class="col-sm-12 form-group">
                          <label class="control-label">Sale Price:</label>
                          <span class="m-l-5"><?php echo $fn->show_price($fn->data['sale_price']); ?></span>
                      </div>
               <?php } ?>
                  <div class="col-sm-12 form-group">
                      <label class="control-label">Color Classification</label>
                      <div class="clearfix"></div>
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
              <?php }
               if ($fn->list['sizes']) { ?>
                   <div class="col-sm-12 form-group">
                       <label class="control-label">Sizes</label>
                       <div class="clearfix"></div>
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
               <?php } ?>
                 <div class="col-sm-12">
                     <label class="control-label">Quantity</label>
                     <input type="number" name="qty" id="qty"
                            class="form-control numbers-only"
                            value="1" min="1" max="100" required/>
                 </div>
             </div>
         </div>
         <div class="modal-footer">
             <input type="hidden" name="modal" value="1"/>
             <input type="hidden" name="data" value="<?php echo $fn->post('data'); ?>"/>
             <input type="hidden" name="token" value="<?php echo $fn->post_token(); ?>"/>
             <button type="submit" class="btn btn-default"><?php echo $fn->post('action') == 'go_checkout' ? 'Buy Now' : 'Add to Cart'; ?></button>
         </div>
     </form>
  <?php
 } else {
  throw new Exception('Oops, ' . ucfirst($type) . ' modal not found.');
 }
 $str = ob_get_clean();
 if ($fn->is_ajax_call()) {
  $modal = '';
  ob_start();
  ?>
     <div class="modal-dialog<?php echo $fn->varv('md_class', $fn->modal); ?>"
          role="document"<?php echo $fn->varv('style', $fn->modal) ? ' style="' . $fn->modal['style'] . '"' : ''; ?>>
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                 </button>
                 <h4 class="modal-title"><?php echo $fn->modal['title']; ?></h4>
             </div>
          <?php echo $str; ?>
         </div>
     </div>
  <?php
  $modal = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
  return $modal;
 }
 return $str;
 