<?php
 $str = '';
 ob_start();
?>
<div class="main-category-block ">
    <div class="main-category-title">
        <i class="fa fa-list"></i> Category
    </div>

    <div class="widget-block">
     <?php if ($fn->list['cats']) { ?>
         <ul class="list-unstyled catalog">
          <?php foreach ($fn->list['cats'] as $k => $v) { ?>
              <li><a href="<?php echo $fn->permalink('blog-cat', $v); ?>">
                      <i class="fa fa-link"></i><?php echo $v['category_name']; ?></a></li>
          <?php } ?>
         </ul>
     <?php } else { ?>
         <div class="alert alert-info">No category found.</div>
     <?php } ?>
    </div>
</div>
<div class="main-category-block ">
    <div class="main-category-title">
        <i class="fa fa-archive"></i> Archives
    </div>

    <div class="widget-block">
     <?php if ($fn->list['archives']) { ?>
         <ul class="list-unstyled catalog">
          <?php foreach ($fn->list['archives'] as $k => $v) { ?>
              <li><a href="<?php echo $fn->permalink('blog-archive', $v); ?>">
                      <i class="fa fa-archive"></i><?php echo $fn->dt_format($v['blog_date'], 'F Y');
                ?></a></li>
          <?php } ?>
         </ul>
     <?php } else { ?>
         <div class="alert alert-info">No archives found.</div>
     <?php } ?>
    </div>
</div>
<div class="main-category-block ">
    <div class="main-category-title">
        <i class="fa fa-tag"></i> Tags
    </div>

    <div class="widget-block">
     <?php if ($fn->list['tags']) { ?>
         <ul class="list-unstyled tags">
          <?php foreach ($fn->list['tags'] as $k => $v) { ?>
              <li>
                  <a href="<?php echo $fn->permalink('blog-tag', $v); ?>"><?php echo $v; ?></a>
              </li>
          <?php } ?>
         </ul>
     <?php } else { ?>
         <div class="alert alert-info">No tags found.</div>
     <?php } ?>
    </div>
</div>
<?php if ($fn->list['recent']) { ?>
    <div class="widget-title">
        <i class="fa fa-thumbs-up"></i> Recent Blogs
    </div>
 <?php foreach ($fn->list['recent'] as $k => $v) { ?>
        <div class="widget-block">
            <div class="row">
                <div class="col-md-4 col-sm-2 col-xs-3">
                    <img class="img-responsive"
                         src="<?php echo $fn->get_file($v['blog_image'], 0, 0, 100); ?>"
                         alt="<?php echo $v['blog_title']; ?>" title="<?php echo $v['blog_title']; ?>"/>
                </div>
                <div class="col-md-8  col-sm-10 col-xs-9">
                    <div class="block-name">
                        <a href="<?php echo $fn->permalink('blog-detail', $v); ?>"
                           class="product-name"><?php echo $v['blog_title']; ?></a>
                    </div>
                    <p class="description"><?php echo $fn->show_string($v['blog_desc'], 50); ?></p>
                </div>
            </div>
        </div>
 <?php }
}
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
?>

