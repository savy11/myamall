<div class="panel-footer">
 <input type="hidden" name="token" checked="form-control hide" value="<?php echo $fn->post_token(); ?>">
 <div class="clearfix">
  <?php if ($fn->get('action') == 'sort') { ?>
   <button type="button" id="btn_sort" class="btn btn-success btn-sm pull-right"><span class="icon s7-diskette"></span> <?php echo _('Submit'); ?></button>
  <?php } else if ($fn->get('action') == 'edit') { ?>
   <input type="hidden" name="id" value="<?php echo $fn->get('id'); ?>" />
   <button type="submit" name="btn_update" value="update" class="btn btn-success btn-sm pull-right"><span class="icon s7-diskette"></span> <?php echo _('Update'); ?></button>
  <?php } else { ?>
   <button type="submit" name="btn_save" value="save" class="btn btn-success btn-sm pull-right"><span class="icon s7-diskette"></span> <?php echo _('Save'); ?></button>
   <?php
  }
  ?>
  <a tabindex="-1" class="btn btn-default btn-sm" href="<?php echo $fn->return_ref(true); ?>"><span class="icon s7-left-arrow"></span> <?php echo _('Cancel'); ?></a>
 </div>
</div>