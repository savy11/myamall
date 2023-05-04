<?php
if ($type == 'row') {
 if ($fn->user['group_id'] != 1) {
  if ($fn->varv('form_code', $row) != '#' && !$fn->varv('per_view', $row)) {
   return;
  }
 }
 ?>
 <tr<?php echo $sub ? ' class="main-tr"' : ''; ?>>
  <th><strong><?php echo $row['form_title']; ?></strong></th>
  <?php
  $j = 4;
  $f = true;
  if ($sub == '') {
   $t = count($fn->per_levels[$row['per_level']]);
   if ($t < $j) {
    $j = ($j - $t);
   }
   foreach ($fn->per_levels[$row['per_level']] as $k => $v) {
    $k = strtolower($k);
    ?>
    <th class="text-center" width="15%">
     <?php
     if ($fn->user['group_id'] != 1) {
      if ($fn->varv($row['form_code'], $fn->pers, strtoupper($k[0])) == 1) {
       $type = 'checkbox';
       include 'pers.php';
      }
     } else {
      $type = 'checkbox';
      include 'pers.php';
     }
     ?>
    </th>
    <?php
   }
  }
  ?>
  <?php
  if ($f) {
   for ($i = 0; $i < $j; $i++) {
    ?>
    <th width="15%"></th>
    <?php
   }
  }
  ?>
 </tr>
 <?php
} else if ($type == 'checkbox') {
 ?>
 <div class="checkbox">
  <input type="checkbox" value="1" name="form_per[<?php echo $row['id']; ?>][<?php echo $k; ?>]" id="form_per_<?php echo $row['id']; ?>_<?php echo $k; ?>"<?php echo $row['per_' . $k] == '1' ? ' checked' : ''; ?>>
  <label for="form_per_<?php echo $row['id']; ?>_<?php echo $k; ?>"><?php echo $v; ?></label>
 </div>
<?php } else if ($type == 'row2') {
 ?>
 <tr>
  <td><?php echo $row2['form_title']; ?></td>
  <?php
  if ($row2['per_level'] != '') {
   foreach ($fn->per_levels[$row2['per_level']] as $k => $v) {
    $k = strtolower($k);
    ?>
    <td class="text-center">
     <?php
     if ($fn->user['group_id'] != 1) {
      if ($fn->varv($row2['form_code'], $fn->pers, strtoupper($k[0])) == 1) {
       $type = 'checkbox2';
       include 'pers.php';
      }
     } else {
      $type = 'checkbox2';
      include 'pers.php';
     }
     ?>
    </td>
    <?php
   }
  }
  ?>
  <?php for ($i = count($fn->per_levels[$row2['per_level']]); $i < 4; $i++) { ?>
   <td />
  <?php } ?>
 </tr>
<?php } else if ($type == 'checkbox2') { ?>
 <div class="checkbox">
  <input type="checkbox" value="1" name="form_per[<?php echo $row2['id']; ?>][<?php echo $k; ?>]" id="form_per_<?php echo $row2['id']; ?>_<?php echo $k; ?>"<?php echo $row2['per_' . $k] == '1' ? ' checked' : ''; ?>>
  <label for="form_per_<?php echo $row2['id']; ?>_<?php echo $k; ?>"><?php echo $v; ?></label>
 </div>
 <?php
}
/*
 * Nots Permission
 */ else if ($type == 'not_checkbox') {
 ?>
 <tr>
  <td colspan="4"><?php echo $row['not_title']; ?></td>
  <td width="15%" class="text-center">
   <div class="checkbox">
    <input type="checkbox" value="1" name="not_per[<?php echo $row['id']; ?>]" id="not_per_<?php echo $row['id']; ?>"<?php echo $row['per_id'] != '' ? ' checked' : ''; ?>>
    <label for="not_per_<?php echo $row['id']; ?>">Send</label>
   </div>
  </td>
 </tr>
 <?php
}