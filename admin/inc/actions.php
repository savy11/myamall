<?php
$cp = $fn->check_per();
if ($cp) {
    ?>
    <td align="center" nowrap>
        <?php if ($cp > 1 || $fn->actions_multi) {
            echo $fn->actions_multi;
            if ($fn->per_edit) {
                ?>
                <a href="<?php echo $fn->get_action_url('edit', $row['id']); ?>" class="btn btn-warning btn-sm">
                    <span class="icon ti-pencil"></span> <?php echo _('Edit'); ?></a>
            <?php }
            if ($fn->per_delete) { ?>
                <a href="<?php echo $fn->get_action_url('delete', $row['id'], $dtoken); ?>"
                   onclick="return confirm('<?php echo _("You are going to remove this record. Press OK to proceed and Cancel to Go Back"); ?>')" class="btn btn-danger btn-sm">
                    <span class="icon ti-trash"></span> <?php echo _('Delete'); ?></a>
                <?php
            }
        } else if ($fn->check_per('edit')) { ?>
            <a href="<?php echo $fn->get_action_url('edit', $row['id']); ?>" class="btn btn-warning btn-sm">
                <span class="icon ti-pencil"></span> <?php echo _('Edit'); ?></a>
        <?php } else if ($fn->check_per('delete')) { ?>
            <a href="<?php echo $fn->get_action_url('delete', $row['id'], $dtoken); ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('<?php echo _("You are going to remove this record. Press OK to proceed and Cancel to Go Back"); ?>')"><span
                        class="icon ti-trash"></span> <?php echo _('Delete'); ?></a>
        <?php }
        echo $fn->actions; ?>
    </td>
<?php } ?>