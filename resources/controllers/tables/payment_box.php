<?php
 $str = '';
 ob_start();
 
 if ($this->fn->varv('trans_details', $options)) {
  $data = $this->fn->json_decode($options['trans_details']);
  ?>
     <div style="background: #eaeaea; height: 1px; margin: 15px 0px; width: 100%;"></div>
     <table width="100%" cellspacing="0" border="0" cellpadding="5">
         <tr>
             <th colspan="2"
                 style="background-color: #388396; color: #fff; border: 1px solid #388396; text-align: left; padding: 5px 10px;">
                 Payment Details (<?php echo $this->fn->make_html($options['type']); ?>)
             </th>
         </tr>
      <?php if (isset($data)) { ?>
          <tr>
              <th width="20%">Referene No.</th>
              <td><?php echo $this->fn->varv('reference', $data['data']); ?></td>
          </tr>
          <tr>
              <th width="20%">Currency</th>
              <td><?php echo $this->fn->varv('currency', $data['data']); ?></td>
          </tr>
          <tr>
              <th width="20%">Paid Amount</th>
              <td><?php echo $this->fn->show_price($this->fn->varv('amount', $data['data']) / 100); ?></td>
          </tr>
          <tr>
              <th width="20%">Payment Date</th>
              <td><?php echo $this->fn->dt_format($this->fn->varv('transaction_date', $data['data']), 'F d, Y H:i A');
               ?></td>
          </tr>
       <?php
      }
      ?>
     </table>
  <?php
 }
 $str = preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
 return $str;
