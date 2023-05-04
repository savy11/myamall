<?php
$action = ucfirst($fn->get('action'));
?>
<div class="panel-heading">
 <h3 class="panel-title"><?php echo ($action != '' ? _($action) . ' ' : '' ) . _($fn->page['name']); ?></h3>
</div>