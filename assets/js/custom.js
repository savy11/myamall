app.owl_carousel = function () {
    var ob = $('[id^="owl-products-"]');
    if (ob.length > 0) {
        ob.owlCarousel({
            // Most important owl features
            items: 4,
            loop: false,
            lazyLoad: true,
            dots: false,
            nav: true,
            navigation: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                },
                600: {
                    items: 3,
                },
                1000: {
                    items: 4,
                }
            }
        });
    }

    var ob = $('[id="owl-sale"]');
    if (ob.length > 0) {
        ob.owlCarousel({
            // Most important owl features
            items: 3,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            loop: false,
            lazyLoad: true,
            nav: true,
            navigation: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                },
                600: {
                    items: 2,
                },
                1000: {
                    items: 3,
                }
            }
        });
    }

    var p = $("#owl-partners");
    if (p.length > 0) {
        p.owlCarousel({
            // Most important owl features
            items: 5,
            lazyLoad: true,
            dots: false,
            nav: false,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                },
                600: {
                    items: 3,
                },
                1000: {
                    items: 5,
                }
            }
        });
    }

    var p = $("#owl-category");
    if (p.length > 0) {
        p.owlCarousel({
            // Most important owl features
            items: 7,
            lazyLoad: true,
            dots: false,
            nav: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                },
                420: {
                    items: 4,
                },
                600: {
                    items: 6,
                },
                1000: {
                    items: 7,
                }
            }
        });
    }

    var pc = $('.products-carousel');
    if (pc.length > 0) {
        pc.owlCarousel({
            // Most important owl features
            items: 1,
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            lazyLoad: true,
            nav: true,
            navigation: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 1,
                },
                1000: {
                    items: 1,
                }
            }
        });
    }
}

app.popup = function () {
    var i = $('.image-link,.popup-link');
    if (!i.length > 0)
        return;
    i.unbind('magnificPopup');
    i.magnificPopup({
        type: 'image',
        gallery: {
            enabled: false
        }
    });
}

app.popup_gallery = function () {
    var g = $('.popup-gallery');
    if (!g.length > 0)
        return;
    g.unbind('magnificPopup');
    g.magnificPopup({
        type: 'image',
        tLoading: 'Loading...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function (item) {
                return item.el.attr('title');
            }
        }
    });
}

app.share_popup = function () {
    var sp = $('.share-popup');
    if (!sp.length > 0)
        return;
    sp.unbind('click');
    sp.click(function (e) {
        e.preventDefault();
        var that = $(this);
        popup(that.attr('href'), that.attr('title'), 620, 280);
        return false;
    });

    function popup(url, title, w, h) {
        var d_left = (window.screenLeft != undefined ? window.screenLeft : screen.left),
            d_top = (window.screenTop != undefined ? window.screenTop : screen.top),
            width = (window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width),
            height = (window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height),
            left = ((width / 2) - (w / 2)) + d_left,
            top = ((height / 2) - (h / 2)) + d_top,
            new_window = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
        if (window.focus) {
            new_window.focus();
        }
    }
}

app.blog_commenting = function () {
    var frm = $('#comment-frm'),
        btnr = $('.btn-reply'),
        btncr = $('.btn-cancel-reply'),
        header = 0;
    if (!btnr.length > 0)
        return;

    btnr.unbind('click');
    btnr.click(function () {
        var that = $(this),
            data = that.data();
        frm.find('input#comment-parent').val(data.parent);
        btncr.removeClass('hide').data('id', data.id);

        var top = (frm.offset().top - 180);
        app.scroll(top);
    });

    btncr.unbind('click');
    btncr.click(function () {
        var that = $(this),
            data = that.data();
        that.addClass('hide');
        frm.find('input#comment-parent').val(0);

        var top = ($('#comment-' + data.id).offset().top - 180);
        app.scroll(top);
    });

}

app.scroll = function (top) {
    $('html,body').animate({
        scrollTop: top
    }, 'slow');
}

app.tooltip = function () {
    $('[data-toggle="tooltip"]').tooltip();
}

app.address_type = function (t) {
    var type = $('.address-type'),
        newad = $('.new-address'),
        exist = $('.exist-address');
    if (!type.length > 0)
        return;

    if (t == 1) {
        newad.find(':input').removeAttr('disabled');
        exist.find(':input').attr('disabled', 'disabled');
    } else {
        newad.find(':input').attr('disabled', 'disabled');
        exist.find(':input').removeAttr('disabled');
    }
}

app.checkout = function () {
    var type = $('.address-type');
    if (type.length > 0) {
        type.unbind('change');
        type.change(function () {
            var that = $(this);
            app.address_type(that.val());
            app.ajax_init();
        });
        app.address_type($('.address-type:checked').val());
    }
}

app.classification = function () {
    var gallery = $('#gal1'),
        pz = $('#product-zoom');
    if (gallery.length > 0) {
        gallery.lightGallery();
        gallery.owlCarousel({
            items: 5,
            loop: false,
            lazyLoad: true,
            dots: false,
            nav: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                },
                600: {
                    items: 3,
                },
                992: {
                    items: 4,
                },
                1000: {
                    items: 5,
                }
            }
        });
    }
    if (pz.length > 0) {
        pz.elevateZoom({
            // scrollZoom: true,
            zoomType: "lens",
            lensShape: "round",
            lensSize: 200,
            zoomWindowFadeIn: 500,
            zoomWindowFadeOut: 500,
            cursor: "crosshair"
        });
        var ez = pz.data('elevateZoom'),
            a = gallery.find('a');
        a.unbind('hover');
        a.hover(function () {
            var smallImage = $(this).attr("data-image");
            var largeImage = $(this).attr("data-zoom-image");
            ez.swaptheimage(smallImage, largeImage);
        });
    }


    var anchor = $('ul.classification li a');

    if (!anchor.length > 0)
        return;
    anchor.unbind('click');
    anchor.on('click', function (e) {
        e.preventDefault();
        var that = $(this),
            data = that.data(),
            parent = that.parents('ul'),
            frm = that.parents('form');
        parent.find('li a').removeClass('active');
        that.addClass('active');
        if (pz.length > 0) {
            ez.swaptheimage(that.attr('href'), data.zoom);
        }
        if (data.price != undefined) {
            $('#price').text(data.price);
        }
        if (data.id != undefined) {
            frm.find('#' + data.recid).val(data.id).valid();
        }
        if (data.sizes != undefined) {
            var sizes = data.sizes.toString(),
                ids = Array();
            ids = Array(sizes);
            if (sizes.indexOf(",") > -1) {
                ids = sizes.split(',');
            }
            $('a.size').parent().addClass('hide');
            $('a.size').removeClass('active');
            $('input#size_id').val('');
            $.each(ids, function (k, v) {
                if ($('a.size[data-id="' + v + '"]').length > 0) {
                    $('a.size[data-id="' + v + '"]').parent().removeClass('hide');
                }
            })
        }
    })
}

app.change_currency = function () {
    var cct = $('select#change_country'),
        cc = $('select#change_currency');
    if (!cct.length > 0)
        return;
    cct.unbind('change');
    cct.on('change', function () {
        var that = $(this),
            value = that.find('option:selected').val(),
            cur_id = that.find('option:selected').attr('data-currencyid');
        if (cur_id > 0 && cur_id != undefined) {
            cc.val(cc.find('option[value="' + cur_id + '"]').val());
        } else {
            cur_id = that.data('defaultCurrency');
            cc.val(cc.find('option[value="' + cur_id + '"]').val());
        }
        app.chosen();
    })
}

app.check_input = function () {
    var ca = $('#check_all'),
        ci = $('.check-input');
    if (!ca.length > 0)
        return;
    ca.unbind('click');
    ca.click(function () {
        var that = $(this),
            data = that.data();
        ci.prop('checked', that.is(':checked'));
        $('.cart-table').find('tbody tr').addClass('bg-success');
        if (that.is(':checked') == false) {
            data = {'id': '', 'total': '0', 'price': default_currency + ' 0.00'};
            $('.cart-table').find('tbody tr').removeClass('bg-success');
        }
        if (data.id != undefined) {
            $('form#checkout-frm').find('input#cart_ids').val(data.id);
        }
        if (data.total != undefined) {
            $('form#checkout-frm').find('span#selected-count').text(data.total);
        }
        if (data.price != undefined) {
            $('form#checkout-frm').find('#selected-price').text(data.price);
        }
    })
    if (!ci.length > 0)
        return;
    $.each(ci, function (k, v) {
        var that = $(v),
            data = that.data();
        if (that.is(':checked') == true) {
            if (data.price != undefined) {
                var cf = $('form#checkout-frm');
                cf.find('span#selected-count').text(parseInt(cf.find('span#selected-count').text()) + 1);
                cf.find('#selected-price').text(default_currency + ' ' + (float_num(cf.find('#selected-price').text()) + float_num(data.price)).toFixed(2));
            }
        }
    })

    ci.unbind('click');
    ci.click(function () {
        var that = $(this),
            data = that.data();
        if (that.is(':checked') == true) {
            that.parents('tr').addClass('bg-success');
            if (data.id != undefined) {
                var cf = $('form#checkout-frm'),
                    input = cf.find('input#cart_ids');
                if (input.val() == '') {
                    input.val(data.id);
                } else {
                    input.val(input.val() + ',' + data.id);
                }
            }
            if (data.price != undefined) {
                cf.find('span#selected-count').text(parseInt(cf.find('span#selected-count').text()) + 1);
                cf.find('#selected-price').text(default_currency + ' ' + (float_num(cf.find('#selected-price').text()) + float_num(data.price)).toFixed(2));
            }
        } else {
            that.parents('tr').removeClass('bg-success');
            if (data.id != undefined) {
                var cf = $('form#checkout-frm'),
                    input = cf.find('input#cart_ids');
                if (input.val().indexOf(',' + data.id) > -1) {
                    input.val(input.val().replace(',' + data.id, ''));
                } else {
                    input.val(input.val().replace(data.id, ''));
                }
            }
            if (data.price != undefined) {
                cf.find('span#selected-count').text(parseInt(cf.find('span#selected-count').text()) - 1);
                cf.find('#selected-price').text(default_currency + ' ' + (float_num(cf.find('#selected-price').text()) - float_num(data.price)).toFixed(2));
            }
        }


    })

    function float_num(num) {
        var float_val = default_currency + ' 0.00';
        if (num != undefined) {
            float_val = Number(num.replace(/[^0-9\.-]+/g, ""));
            return float_val;
        }
    }
}


app.ajax_init = function () {
    app.c_ajax_init();
    app.owl_carousel();
    app.tooltip();
    app.popup();
    app.share_popup();
    app.address_type();
    app.checkout();
    app.change_currency();
};

$(window).on('DOMContentLoaded', function () {
    setTimeout(function () {
        $('.preloader').fadeOut('slow');
    }, 1000);
    app.c_init();
    app.owl_carousel();
    app.popup();
    app.popup_gallery();
    app.blog_commenting();
    app.share_popup();
    app.tooltip();
    app.address_type();
    app.checkout();
    app.change_currency();

    var hash = window.location.hash;
    if (hash != '' && hash != undefined) {
        app.scroll($(hash).offset().top - 200);
    }

    var td = $('.toggle-drop');
    td.unbind('click');
    td.click(function () {
        $(this).parent().toggleClass('active');
    })
});