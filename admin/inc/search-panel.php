
<div class="panel panel-default search-panel"<?php echo $fn->get('search') == 1 ? ' style="display: block;"' : ' style="display: none;"' ?>>
 <div class="panel-heading">
  <h3 class="panel-title">Search</h3>
 </div>
 <form action="<?php echo $fn->page['url']; ?>" method="get" id="search-frm" class="form-validate">
  <div class="panel-body">
   <div class="row">
    <?php
     if ($fn->list) {
      if ($fn->list['categories']) {
       ?>
       <div class="col-md-3 form-group">
        <select name="category_id" id="category_id" class="form-control" data-placeholder="Category" data-allow-clear="true">
   <?php echo $fn->show_list($fn->list['categories'], $fn->get('category_id'), true); ?>
        </select>
       </div>
      <?php
      }
     }
    ?>
    <div class="col-md-3 form-group">
     <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Enter Keywords..." value="<?php echo $fn->get('keyword'); ?>" />
    </div>
   </div>
  </div>
  <div class="panel-footer">
   <input type="hidden" name="search" value="1" />
   <a href="<?php echo $fn->page['url']; ?>" class="btn btn-default btn-sm"><i class="icon s7-left-arrow"></i> Clear Search</a>
   <button type="submit" class="btn btn-secondary btn-sm pull-right"><i class="icon s7-search"></i> Search</button>
  </div>
 </form>
</div>