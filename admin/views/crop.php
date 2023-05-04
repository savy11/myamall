<?php
 $str = '';
 ob_start();
 if ($type == 'crop') {
  $fn->modal = array('title' => 'Crop Image', 'md_class' => ' modal-lg');
  ?>
  <div class="modal-body">
   <div class="row">
    <div class="col-md-9">
     <div class="img-container">
      <img src="<?php echo $fn->permalink('assets/img/test.jpg') ?>" class="img-responsive" />
     </div>
    </div>
    <div class="col-md-3">
     <div class="ratio" style="background: #f3f3f3; border-left: 1px solid #ddd; padding: 15px; height: 100%; display: block; min-height: 445px;">
      <h2>Scale Image <i class="fa fa-info"></i></h2>
      <span>Original Dimensions <?php echo $fn->post('width'); ?> x <?php echo $fn->post('height'); ?></span>
      <div class="docs-preview clearfix">
       <div class="img-preview preview-lg"></div>
      </div>
      <p style="font-size:13px;"><span style="font-weight: 600;">Recommend Size :</span> <span id="r_size"></span><br/>
       <span id="crop_size"></span></p>
      <form method="post" id="crop-frm" data-ajax="true" data-url="<?php echo $fn->page['page_url']; ?>" data-page="true" data-action="export">
       <input type="hidden" name="action" value="export" />
       <input type="hidden" id="dataX" name="dataX" />
       <input type="hidden" id="dataY" name="dataY" />
       <input type="hidden" id="dataWidth" name="dataWidth" />
       <input type="hidden" id="dataHeight" name="dataHeight" />
       <input type="hidden" name="org_path" value="<?php echo $fn->permalink('assets/img/test.jpg') ?>" />
       <input type="hidden" name="filename" value="test.jpg" />
       <input type="submit" value="Crop Image" class="btn btn-primary" />
       <button class="btn btn-default" data-dismiss="modal">Cancel</button>
      </form>
     </div>
    </div>
   </div>
  </div>
  <?php
 }
 $str = ob_get_clean();
 return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
 