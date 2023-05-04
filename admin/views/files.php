<?php
if ($file = $this->session($this->tmp_path, $type)) {
 ?>
 <div class="file" tabindex="-1">
  <a href="#"><div class="filename"><?php echo $file['filename']; ?></div> <div class="filesize">(<?php echo $this->format_size($file['size']); ?>)</div></a>
  <div class="delete-btn" tabindex="-1" data-type="<?php echo $type ?>" data-filename="<?php echo $file['filename']; ?>"><span class="ti-close"></span></div>
 </div>
<?php } if (isset($data)) { ?>
 <div class="file" tabindex="-1">
  <a href="#"><div class="filename"><?php echo $data['name']; ?></div> <div class="filesize">(<?php echo $this->format_size($data['size']); ?>)</div></a>
 </div>
<?php } ?>
