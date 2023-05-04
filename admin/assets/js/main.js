$(function () {

 /*
  * Mobile Menu
  */
 var m = $('.mobile-menu'),
         cc = $('.content-container'),
         tb = $('.toggle-btn');

 if ($('body').hasClass('sidebar-open')) {
  tb.addClass('active');
 }
 if (tb.length > 0) {
  tb.unbind('click');
  tb.click(function () {
   var w = 0,
           ml = m.width();
   if (tb.hasClass('active')) {
    w = '-' + m.width(), ml = 0;
   }
   tb.toggleClass('active');
   $('body').toggleClass('sidebar-open');
  });
 }

 $(window).load(function () {
  var that = $(this);
  if (that.width() <= 767) {
   if ($('body').hasClass('sidebar-open')) {
    $('body').toggleClass('sidebar-open');
    tb.toggleClass('active');
   }
  }
 });

 /*$(window).resize(function () {
  var that = $(this);
  console.log(that.width);  
  if (that.width() > 767) {
  if (tb.hasClass('active')) {
  tb.toggleClass('active');
  m.animate({
  left: -220
  });
  cc.animate({
  'margin-left': 0
  });
  }
  }
  });*/

 /*
  * Collapse In Open One
  */
 var mnm = $('#main-nav-menu'),
         c = mnm.find('a[data-toggle="collapse"]');
 if (c.length > 0) {
  c.each(function () {
   var that = $(this);
   $(that.attr('href')).on('show.bs.collapse', function () {
    mnm.find('.collapse.in').collapse('hide');
   });
  });
 }

 /*
  * Back Btn
  */
 var b = $('.back-btn');
 if (b.length > 0) {
  b.unbind('click');
  b.click(function (e) {
   e.preventDefault();
   window.history.back();
  });
 }

 /* 
  * Lock Screen
  */
 var l = $('.lock');
 if (l.length > 0) {
  l.unbind('click');
  l.click(function (e) {
   e.preventDefault();
   document.getElementById('lock-frm').submit();
   return false;
  });
 }
});