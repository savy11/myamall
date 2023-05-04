<section>
    <div class="second-page-container">
        <div class="block-breadcrumb">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="<?php echo $fn->permalink(); ?>">Home</a></li>
                 <?php
                  if (isset($breadcrumb) && $breadcrumb) {
                   foreach ($breadcrumb as $k => $v) {
                    ?>
                       <li><a href="<?php echo $v; ?>"><?php echo $k; ?></a></li>
                    <?php
                   }
                  }
                 ?>
                    <li class="active"><?php echo $fn->varv('page_heading', $fn->cms); ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>