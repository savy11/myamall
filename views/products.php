<?php
 $str = '';
 ob_start();
 if ($fn->data) {
  echo include_once app_path . 'views' . ds . 'products_' . $fn->session('view') . '.php';
 } else { ?>
     <div class="col-md-12">
         <div class="alert alert-danger">Oops, no products found. Please try after sometime or later.
         </div>
     </div>
 <?php }
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
?>

