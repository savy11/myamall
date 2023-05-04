<?php
 ob_start();
 $str = '';
?>
    <li class="media comment" id="comment-<?php echo $v['id']; ?>">
        <div class="pull-left">
            <img src="<?php echo $fn->permalink('assets/img/no-user.jpg'); ?>" alt="<?php echo $v['name']; ?>"/>
        </div>
        <div class="media-body">
            <h5 class="media-heading"><?php echo $v['name']; ?>
                <a href="javascript:;" class="btn-right-post btn-reply" data-id="<?php echo $v['id'] ?>"
                   data-parent="<?php echo $v['parent_id'] == 0 ? $v['id'] : $v['parent_id']; ?>">Reply</a>
                <span class="time-right"><?php echo $fn->time_ago($v['add_date']); ?></span></h5>
            <p><?php echo $v['comment']; ?></p>
         <?php
          if ($fn->varv($v['id'], $fn->replies)) {
           foreach ($fn->varv($v['id'], $fn->replies) as $k => $v) {
            ?>
               <div class="media">
                   <div class="pull-left">
                       <img src="<?php echo $fn->permalink('assets/img/no-user.jpg'); ?>"
                            alt="<?php echo $v['name']; ?>"/>
                   </div>
                   <div class="media-body">
                       <h5 class="media-heading"><?php echo $v['name']; ?>
                           <a href="javascript:;" class="btn-right-post btn-reply" data-id="<?php echo $v['id'] ?>"
                              data-parent="<?php echo $v['parent_id'] == 0 ? $v['id'] : $v['parent_id']; ?>">Reply</a>
                           <span class="time-right"><?php echo $fn->time_ago($v['add_date']); ?></span></h5>
                       <p><?php echo $v['comment']; ?></p>
                   </div>
               </div>
           <?php }
          } ?>
        </div>
    </li>
<?php
 $str .= ob_get_clean();
 return $str;
?>