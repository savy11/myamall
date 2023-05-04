<?php
 if ($data) {
  foreach ($data as $k => $v) {
   $meta = $this->json_decode($v['meta_value']);
   $checked = (strpos(',' . $default . ',', ',' . $v['id'] . ',') !== false ? true : false);
   ?>
      <li<?php echo $checked ? ' class="selected"' : ''; ?>>
          <div class="attachment">
              <div class="thumbnail">
                  <div class="centered">
                      <img class="lazy" src="<?php echo $this->permalink('assets/img/loader.svg'); ?>" data-src="<?php echo $this->is_image($meta['ext']) ? $this->get_file($v['meta_value'], 0, 0, 200) : $this->get_default_icon($meta['ext']); ?>" alt=""/>
                  </div>
              </div>
          </div>
          <a href="#" class="button-link remove-item" data-ajaxify="true" data-url="delete" data-type="db"
             data-app="<?php echo $this->encrypt_post_data(array('id' => $v['id'])); ?>" data-prmv="li">
              <span class="icon"></span>
          </a>
          <div class="checked">
              <input type="checkbox" name="default[]"
                     value="<?php echo $v['id']; ?>"<?php echo $checked ? ' checked' : ''; ?> />
          </div>
      </li>
   <?php
  }
 }
 if ($type) {
  if ($data = $this->session($type)) {
   foreach ($data as $k => $v) {
    ?>
       <li>
           <div class="attachment">
               <div class="thumbnail">
                   <div class="centered">
                       <img class="lazy" src="<?php echo $this->permalink('assets/img/loader.svg'); ?>" data-src="<?php echo $this->is_image($v['ext']) ? $this->get_file($this->tmp_file_data($v['filename']), 0, 0, 200) : $this->get_default_icon($v['ext']); ?>" alt=""/>
                   </div>
               </div>
           </div>
           <a href="#" class="button-link remove-item" data-ajaxify="true" data-url="delete" data-type="tmp"
              data-app="<?php echo $this->encrypt_post_data(array('for' => $type, 'filename' => $v['filename'])); ?>"
              data-prmv="li">
               <span class="icon"></span>
           </a>
           <div class="checked">
               <input type="checkbox" name="default[]" value="<?php echo $v['filename']; ?>">
           </div>
       </li>
    <?php
   }
  }
 }
?>
