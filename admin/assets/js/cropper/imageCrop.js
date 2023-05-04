var img_crop = function () {
 var content = $('div.img-container').attr('data-content')
 $('#r_size').html(content);
 if (content !== 'undefined') {
  var size = content.split(' x ');
  $('#setDataWidth').val(size[0]);
  $('#setDataHeight').val(size[1]);
 }

 var $image = $('.img-container > img');
 var $dataX = $('#dataX');
 var $dataY = $('#dataY');
 var $dataHeight = $('#dataHeight');
 var $dataWidth = $('#dataWidth');
 var $cropsize = $('#crop_size');
 var $base64data = $('#base64data');
 var options = {
  preview: '.img-preview',
  crop: function (e) {
   $dataX.val(Math.round(e.x));
   $dataY.val(Math.round(e.y));
   $dataHeight.val(Math.round(e.height));
   $dataWidth.val(Math.round(e.width));
   $cropsize.html('<span style="font-weight: 600;">Crop Size :</span> ' + Math.round(e.detail.width) + ' x ' + Math.round(e.detail.height));
  }
 };
 $image.cropper(options);
}
