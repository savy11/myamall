'use strict';

(function ($, window, document, undefined) {
 var c = $('.custom-file');
 if (c.length > 0) {
  c.each(function () {
   var that = $(this),
           input = that.find('input[type="file"]');
   that.on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
   }).on('dragover dragenter', function () {
    input.addClass('has-drag');
   }).on('dragleave dragend drop', function () {
    input.removeClass('has-drag');
   }).on('drop', function (e) {
    input.prop('files', e.originalEvent.dataTransfer.files);
   });
  });

  var input = c.find('input[type="file"]');
  input.each(function () {
   var that = $(this),
           label = that.next('label'),
           label_val = label.html();

   if (that.get(0).hasAttribute('multiple')) {
    if (!that.get(0).hasAttribute('data-multiple-caption')) {
     that.attr('data-multiple-caption', '{count} files selected');
    }
   }
   that.on('change', function (e) {
    var filename = '';
    if (this.files && this.files.length > 1) {
     filename = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
    } else if (e.target.value) {
     filename = e.target.value.split('\\').pop();
    }
    if (filename) {
     label.find('span').html(filename);
    } else {
     label.html(label_val);
    }
   });
   // Firefox bug fix
   that.on('focus', function () {
    that.addClass('has-focus');
   }).on('blur', function () {
    that.removeClass('has-focus');
   });
  });
 }
})(jQuery, window, document);