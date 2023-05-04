<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Seo Section</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="page_title" class="input-label req"><?php echo _('Page Title'); ?></label>
                <input type="text" name="page_title" id="page_title" class="form-control"
                       value="<?php echo $fn->post('page_title'); ?>" data-page-title="true" required/>
            </div>
            <div class="form-group col-sm-4">
                <label for="page_heading" class="input-label req"><?php echo _('Page Heading'); ?></label>
                <input type="text" name="page_heading" id="page_heading" class="form-control"
                       value="<?php echo $fn->post('page_heading'); ?>" data-page-title="true" required/>
            </div>
            <div class="form-group col-sm-4">
                <label for="page_url" class="input-label req"><?php echo _('Page Url'); ?></label>
                <input type="text" name="page_url" id="page_url" class="form-control"
                       value="<?php echo $fn->post('page_url'); ?>" data-page-url="true"
                       required />
            </div>
            <div class="clearfix"></div>
            <div class="form-group col-sm-6">
                <label for="meta_keywords" class="input-label"><?php echo _('Meta Keywords'); ?>
                    <small>(Optional)</small></label>
                <textarea name="meta_keywords" id="meta_keywords" class="form-control tagsinput count-keywords" rows="5"
                          data-default-text="Add a keyword"><?php echo $fn->post('meta_keywords'); ?></textarea>
            </div>
            <div class="form-group col-sm-6">
                <label for="meta_desc" class="input-label"><?php echo _('Meta Description'); ?>
                    <small>(Optional)</small></label>
                <textarea name="meta_desc" id="meta_desc" class="form-control count-char" rows="5"
                          style="height: 104px;"><?php echo $fn->post('meta_desc'); ?></textarea>
            </div>
        </div>
    </div>
 <?php include admin_path . 'inc' . ds . 'panel-footer.php'; ?>
</div>