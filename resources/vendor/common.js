var loader = function () {
}
loader.prototype = {
    require: function (files, callback, ir) {
        this.loadCount = 0;
        this.totalRequired = files.length;
        this.callback = callback;
        if (files) {
            for (var i = 0; i < files.length; i++) {
                var ext = files[i].split('.').pop();
                if (ext == 'css') {
                    var len = $('link').filter(function () {
                        return ($(this).attr('href') == (ir ? root : hostname) + files[i]);
                    }).length;
                    if (!len) {
                        this.writestylesheet(files[i], ir);
                    } else {
                        this.loaded();
                    }
                } else {
                    $('script').filter(function () {
                        if ($(this).attr('src') == (ir ? root : hostname) + files[i]) {
                            $(this).remove();
                        }
                    });
                    this.writescript(files[i], ir);
                }
            }
        }
    },
    loaded: function (e) {
        this.loadCount++;
        if (this.loadCount == this.totalRequired && typeof this.callback == 'function')
            this.callback.call();
    },
    writescript: function (src, ir) {
        var self = this;
        var s = document.createElement('script');
        g = document.getElementsByTagName('script')[0];
        s.type = "text/javascript";
        s.src = (ir ? root : hostname) + src;
        s.async = true;
        s.addEventListener('load', function (e) {
            self.loaded(e);
        }, false);
        g.parentNode.insertBefore(s, g);
    },
    writestylesheet: function (src, ir) {
        var self = this;
        var s = document.createElement('link');
        g = document.getElementsByTagName('link')[0];
        s.rel = "stylesheet";
        s.href = (ir ? root : hostname) + src;
        s.type = "text/css";
        s.addEventListener('load', function (e) {
            self.loaded(e);
        }, false);
        g.parentNode.insertBefore(s, g);
    }
}
var app = {},
    is_mobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function () {
            return (is_mobile.Android() || is_mobile.BlackBerry() || is_mobile.iOS() || is_mobile.Opera() || is_mobile.Windows());
        }
    };
app.chosen = function (t) {
    var c = $('select:not(.no-select)');
    if (!c.length > 0)
        return;
    if (t !== false) {
        t = true;
    }
    var l = new loader();
    l.require(['resources/vendor/chosen/chosen.css', 'resources/vendor/chosen/chosen.js', 'resources/vendor/chosen/chosen-ajax.js'], function () {
        c.each(function () {
            var that = $(this);
            if (that.parent().find('.chosen-container').length > 0) {
                that.trigger('chosen:updated');
            } else {
                var data = that.data(),
                    params = {allow_single_deselect: false};
                if (app.checkdata(data, 'allowClear')) {
                    params.allow_single_deselect = data.allowClear;
                }
                if (that.parents('.form-group').find('.chosen-container').length > 0) {
                    that.parents('.form-group').find('.chosen-container').remove();
                }
                if (app.checkdata(data, 'ajax')) {
                    that.ajaxChosen({
                        url: hostname + 'ajax/' + data.url + '?token=' + token + '&rnd=' + Math.random(),
                        dataType: 'json',
                        delay: 250,
                        data: {search: true},
                        success: function (data, textStatus, jqXHR) {
                            //console.log(textStatus);
                        }
                    }, {
                        useAjax: true,
                        loadingImg: hostname + 'assets/img/loader.svg',
                    }, params).change(function () {
                        if (data.url == 'restaurants') {
                            app.send_ajax('restaurants', 'type=rest&id=' + $(this).val());
                        }
                    });
                } else {
                    that.chosen(params).change(function () {
                        that.valid();
                    });
                }
                if (t) {
                    if (that.get(0).hasAttribute('autofocus')) {
                        that.trigger('chosen:activate');
                    }
                }
            }
        });
    }, true);
}

app.captcha = function () {
    var c = $('.captcha');
    if (!c.length > 0)
        return;
    c.each(function () {
        var that = $(this),
            r = that.find('a.refresh-captcha'),
            co = that.find('img.captcha-code');
        r.unbind('click');
        r.click(function (e) {
            e.preventDefault();
            var url = co.attr('src'),
                sp = url.split('?');
            url = sp[0] + '?';
            if (sp[1].indexOf('key=') > -1) {
                var q = sp[1].split('&');
                url = sp[0] + '?' + q[0] + '&';
            }
            co.attr('src', url + Math.random());
        });
    });
}

app.show_modal = function (id, bd) {
    var id = $('#' + id);
    if (!id.length > 0) {
        console.warn('Modal id not found.')
        return;
    }
    if (bd == undefined || bd == '') {
        bd = true;
    }
    id.modal({
        backdrop: bd,
        show: true
    });
}

app.hide_modal = function (id) {
    if (typeof id == 'undefined' || id == '') {
        var id = $('.modal.in');
    } else {
        var id = $('#' + id);
    }
    if (!id.length > 0) {
        console.warn('Modal id not found.')
        return;
    }
    id.modal('hide');
    id.on('hidden.bs.modal', function () {
        id.html('');
    });
}
app.show_msg = function (title, message, type) {
    var l = new loader();
    l.require(['resources/vendor/toastr/toastr.js', 'resources/vendor/toastr/toastr.css'], function () {
        var cls = 'info';
        if (type != undefined && type != '') {
            cls = type;
        }
        if (title == undefined || title == '') {
            title = 'Message';
        }
        toastr.options = {
            debug: false,
            closeButton: true,
            preventDuplicates: true,
            positionClass: 'toast-bottom-left'
        };
        toastr[cls](message, title);
    }, true);
}

// Ajax
var ajax_cnt = 0;
app.checkdata = function (data, f, t) {
    if (data) {
        if (data.hasOwnProperty(f)) {
            if (t != undefined && t != '') {
                if (data[f] == t) {
                    return true;
                }
            } else if (t == undefined) {
                return true;
            } else if (data[f] == undefined || data[f] == '') {
                return true;
            }
        }
    }
    return false;
}

app.loader = function () {
    var _l = $('.loader');
    if (!_l.length > 0)
        return;
    if (_l.hasClass('active')) {
        _l.removeClass('active').hide();
    } else {
        _l.addClass('active').show();
    }
}

app.send_ajax = function (page, str, recid, obj, data, callback, errcall) {
    app.send_data('ajax/' + page, str, recid, obj, data, callback, errcall);
}

app.send_data = function (page, str, recid, obj, data, callback, errcall) {
    if (obj == undefined) {
        obj = '';
    }
    var ajaxdata = {};
    if (obj != '') {
        ajaxdata = obj.data();
    }
    if (data != undefined && data != '') {
        ajaxdata = JSON.parse(data);
    }
    app.loader();
    ajax_cnt++;
    xhr = $.ajax({
        method: 'POST',
        dataType: 'json',
        url: hostname + page + '?token=' + token + '&rnd=' + Math.random(),
        data: str,
        success: function (result) {
            app.receive_data(recid, result, ajaxdata, obj, callback, errcall);
        }
    });
}
app.receive_data = function (id, data, ajaxdata, obj, callback, errcall) {
    ajax_cnt--;
    if (data != '') {
        if (app.checkdata(data, 'success', true)) {
            if (app.checkdata(data, 'html')) {
                if (id != '' || app.checkdata(data, 'container') || app.checkdata(data, 'recid')) {
                    var recid = '';
                    if (id != '') {
                        recid = $('#' + id);
                    } else if (app.checkdata(data, 'container')) {
                        recid = $('.' + data.container);
                    } else if (app.checkdata(data, 'recid')) {
                        recid = $('#' + data.recid);
                    }
                    if (typeof recid == 'object') {
                        var that = recid,
                            value = '';
                        if (app.checkdata(data, 'set_current')) {
                            value = that.val();
                        }
                        if (app.checkdata(that.data(), 'isotope')) {
                            var htm = data.html;
                            htm = htm.replace(/(\r\n|\n|\r)/gm, "");
                            var html = $(htm);
                            that.append(html).imagesLoaded(function () {
                                that.find('.item').show();
                                that.isotope('appended', html);
                            });
                        } else if (app.checkdata(data, 'append', true)) {
                            that.append(data.html);
                        } else if (app.checkdata(data, 'prepend', true)) {
                            that.prepend(data.html);
                        } else {
                            that.html(data.html);
                            if (value) {
                                that.val(value);
                            }
                        }
                    }
                }
            }
// Callback Success
            if (typeof callback != 'undefined' && callback != '') {
                if (typeof callback == 'function') {
                    callback(data);
                } else {
                    eval(callback);
                }
            }
        } else {
            if (typeof errcall != 'undefined' && errcall != '') {
                if (typeof errcall == 'function') {
                    errcall(data);
                } else {
                    eval(errcall);
                }
            }
        }
    }

    if (app.checkdata(data, 'rec')) {
        $.each(data.rec, function (k, v) {
            $('#' + k).html(v);
        });
    }

    if (app.checkdata(data, 'g_title')) {
        app.show_msg(data.g_title, data.g_message, (app.checkdata(data, 'g_type') ? data.g_type : (app.checkdata(data, 'error') ? 'error' : 'success')));
    }

    if (app.checkdata(data, 'script')) {
        eval(data.script);
    }

    if (app.checkdata(data, 'modal', true)) {
        app.show_modal(id, (app.checkdata(data, 'modalBackdrop') ? data.modalBackdrop : ''));
    }

    app.ajax_init();
    app.loader();
}

var data = null, obj = null;
app.ajax_call = function () {
    var ajax = $('[data-ajaxify="true"]');
    if (!ajax.length > 0)
        return;
    ajax.each(function (k, v) {
        var t = null,
            that = $(this),
            id = that.attr('id');
        if (that.hasClass('disabled')) {
            return;
        }
        t = 'click';
        if (that.data('event') != undefined) {
            t = that.data('event');
        }
        that.unbind(t);
        that.on(t, function (e) {
            e.preventDefault();
            obj = $(this);
            data = obj.data();
            if (data.ajaxify == false) {
                return;
            }
            if (app.checkdata(data, 'confirm')) {
                app.confirm(data.confirm, function () {
                    app.ajaxify(t);
                });
            } else {
                app.ajaxify(t);
            }
        });
    });
}

app.ajaxify = function (e) {
    if (data == null)
        return false;
    var call = [], type = null, action = null, extraparams = '', frmdata = {};
    if (app.checkdata(data, 'json')) {
        call = data.json;
    }
    if (app.checkdata(data, 'type')) {
        call['type'] = data.type;
    }
    if (app.checkdata(data, 'action')) {
        call['action'] = data.action;
    }
    if (app.checkdata(data, 'app')) {
        call['data'] = data.app;
    }

    if (e == 'change') {
        if (!app.checkdata(data, 'eventVal', false)) {
            var name = (app.checkdata(data, 'name') ? data.name : obj.attr('name')),
                value = obj.val();
            if (obj.attr('type') == 'checkbox') {
                value = (obj.prop('checked') == obj.val()) ? 1 : 0;
            }
            call[name] = value;
        }
    }

    if (app.checkdata(data, 'frm')) {
        var x = $('#' + data.frm).find(':input').serializeArray();
        $.each(x, function () {
            if (call[this.name] !== undefined) {
                if (!call[this.name].push) {
                    call[this.name] = [call[this.name]];
                }
                call[this.name].push(this.value || '');
            } else {
                call[this.name] = this.value || '';
            }
        });
    }

    var recid = '';
    if (app.checkdata(data, 'recid')) {
        recid = data.recid;
    }
    if (app.checkdata(data, 'prmv')) {
        var that = obj.parent(data.prmv);
        that.fadeOut(function () {
            that.remove();
        });
    }
    call = $.extend({}, call);
    call = $.param(call);
    if (app.checkdata(data, 'page', true)) {
        app.send_data(data.url, call, recid, obj);
    } else {
        app.send_ajax(data.url, call, recid, obj);
    }
}
app.reset_form = function (id) {
    var id = $('#' + id);
    if (!id.length > 0)
        return;
    id.get(0).reset();
}
app.bind_form = function () {
    var fv = $('.form-validate');
    if (!fv.length > 0)
        return;
    var l = new loader();
    l.require(['resources/vendor/validate/jquery.validate.js'], function () {
        fv.each(function () {
            var that = $(this);
            that.unbind('validate');
            that.validate({
                debug: false,
                ignore: '',
                submitHandler: function (form) {
                    var frm = that,
                        data = frm.data();
                    if (app.checkdata(data, 'ajax', true)) {
                        var recid = app.checkdata(data, 'recid') ? data.recid : '';
                        var call = [];
                        if (app.checkdata(data, 'action')) {
                            call['action'] = data.action;
                        }
                        if (app.checkdata(data, 'type')) {
                            call['type'] = data.type;
                        }
                        call = $.extend({}, call);
                        if (!$.isEmptyObject(call)) {
                            call = $.param(call) + '&' + frm.serialize();
                        } else {
                            call = frm.serialize();
                        }
                        if (app.checkdata(data, 'page', true)) {
                            app.send_data(data.url, call, recid, that);
                        } else {
                            app.send_ajax(data.url, call, recid, that);
                        }
                        return false;
                    } else {
                        form.submit();
                    }
                }
            });
        });
    }, true);
}
app.tiny_mce = function () {
    var t = $('.tinymce');
    if (!t.length > 0)
        return;
    var l = new loader();
    l.require(['resources/vendor/tinymce/tinymce.min.js'], function () {
        t.each(function () {
            var that = $(this),
                id = '#' + (that.attr('id'));
            tinymce.remove(id);
            tinymce.init({
                selector: 'textarea' + id,
                theme: 'modern',
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern'
                ],
                //toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons',
                image_advtab: true,
                rel_list: [{
                    title: 'Lightbox',
                    value: 'prettyPhoto'
                }],
                document_base_url: location.protocol + '//' + document.domain + '/' + location.pathname.split('/')[1] + '/',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                        $(id).valid();
                    });
                }
            });
        });
    }, true);
}
app.tooltip = function () {
    var t = $('.tip');
    if (!t.length > 0)
        return;
    t.tooltip();
}
app.show_password = function () {
    var s = $('[data-show-pass]');
    if (!s.length > 0)
        return;
    s.each(function () {
        var t = $(this);
        t.unbind('click');
        t.click(function () {
            var that = $(this),
                data = that.data(),
                rel = $('[rel="' + data.showPass + '"');
            if (rel.attr('type') == 'password') {
                rel.attr('type', 'text').addClass('active');
            } else {
                rel.attr('type', 'password').removeClass('active');
            }
        });
    });
}
app.tag_input = function () {
    var i = $('.tagsinput');
    if (!i.length > 0)
        return;
    var l = new loader();
    l.require(['resources/vendor/tagsinput/jquery.tagsinput.css', 'resources/vendor/tagsinput/jquery.tagsinput.min.js'], function () {
        i.each(function () {
            var that = $(this),
                data = that.data();
            that.tagsInput({
                width: 'auto',
                defaultText: (app.checkdata(data, 'defaultText') ? data.defaultText : 'Add a tag'),
                onAddTag: function (e) {
                    if (that.hasClass('count-keywords')) {
                        app.keyword_count(that, e);
                    }
                },
                onRemoveTag: function (e) {
                    if (that.hasClass('count-keywords')) {
                        app.keyword_count(that, e, true);
                    }
                }
            });
        });
    }, true);
}
app.date_time_picker = function () {
    var d = $('.dt-picker');
    if (!d.length > 0)
        return;
    var l = new loader();
    l.require(['resources/vendor/datetimepicker/bootstrap-datetimepicker.css', 'resources/vendor/datetimepicker/bootstrap-datetimepicker.js'], function () {
        d.each(function () {
            var that = $(this),
                data = that.data(),
                params = {
                    format: 'YYYY-MM-DD HH:mm s',
                    debug: true,
                };
            if (app.checkdata(data, 'dateOnly')) {
                params.format = 'YYYY-MM-DD';
            }
            that.datetimepicker(params);
        });
    }, true);
}
app.start_url = '';
app.change_url = function (page, url, start) {
    if (start == undefined) {
        if (app.start_url == '') {
            app.start_url = window.location.href;
        }
    }
    if (typeof (history.pushState) != 'undefined') {
        var obj = {Page: page, Url: url};
        history.pushState(obj, obj.Page, obj.Url);
    } else {
        app.show_msg('Error', 'Browser does not support HTML5.', 'error');
    }
}
app.load_more = function () {
    var l = $('.load-more button');
    if (!l.length > 0)
        return;
    var total = 0,
        load = 0;
    l.unbind('click');
    l.click(function (e) {
        e.preventDefault();
        var that = $(this),
            data = that.data(),
            p = (parseInt(data.paging) + 1),
            url = 'ajax/';
        if (app.checkdata(data, 'page')) {
            url = '';
        }
        url += data.url;
        var params = '';
        if (app.checkdata(data, 'action')) {
            params = 'action=' + data.action + '&';
        }
        app.send_data(url, params + 'p=' + p, data.recid, '', '', function (e) {
            if (app.checkdata(e, 'success')) {
                total = e.total;
                if (load == 0) {
                    load = (e.load + e.load);
                } else {
                    load += e.load;
                }
                if (e.count < e.load || load == total) {
                    that.parent().remove();
                } else {
                    that.data('paging', p).attr('data-paging', p);
                }
            }
        });
        return false;
    });
}
app.copy_text = function () {
    var c = $('.copy');
    if (!c.length > 0)
        return;
    app.tooltip();
    c.each(function () {
        var that = $(this);
        that.css({'cursor': 'pointer'});
        that.unbind('click');
        that.click(function (e) {
            e.preventDefault();
            var that = $(this),
                data = _this.data();
            if (!app.checkdata(data, 'copyElem')) {
                console.warn('Element not found.');
                return false;
            }
            var ce = $(data.copyElem),
                elem = document.createElement('input');
            elem.setAttribute('type', 'text');
            elem.setAttribute('value', '');
            that.parent().append(elem);
            $(elem).val(ce.val()).select();
            var res = false;
            try {
                res = document.execCommand('copy');
                app.reBuildTooltip(that, 'Copied!');
            } catch (err) {
                console.warn('Copy error: ' + err);
            }
            $(elem).remove();
            return res;
        });
    });
}

app.numbers_only = function () {
    var n = $('.numbers-only');
    if (!n.length > 0)
        return;
    n.on('keypress', function (e) {
        var that = $(this),
            data = that.data(),
            unicode = (e.charCode ? e.charCode : e.keyCode);
        if (unicode == 46) {
            if (app.checkdata(data, 'intFormat', true)) {
                if (that.val().indexOf('.') >= -1) {
                    return false;
                }
            } else {
                if (that.val().indexOf('.', 0) > 0) {
                    return false;
                }
            }
        } else if (unicode == 9) {
        } else if (unicode != 8) {
            if (unicode < 48 || unicode > 57) {
                return false;
            }
        }
    });
}

app.numbers_row = function () {
    // Number Row
    var nr = $('.numbers-row');
    if (!nr.length > 0)
        return;
    nr.each(function () {
        var t = $(this),
            i = t.find('input'),
            button = t.find('.button');
        if (i.val() > 1) {
            t.find('.dec').removeClass('disabled');
        }
        button.unbind('click');
        button.on('click', function () {
            var that = $(this),
                input = that.parent().find('input'),
                data = input.data(),
                oldval = input.val();
            if (that.text() == '+') {
                var newval = parseFloat(oldval) + 1;
            } else {
                if (oldval > 0) {
                    var newval = parseFloat(oldval) - 1;
                } else {
                    newval = 0;
                }
            }
            if (newval == 0) {
                newval = (app.checkdata(data, 'min') ? data.min : 0);
            }
            input.val(newval).trigger('change');
            if (input.val() > 1) {
                that.parent().find('.dec').removeClass('disabled');
                if (app.checkdata(data, 'max')) {
                    if (input.val() == data.max) {
                        that.parent().find('.inc').addClass('disabled');
                    } else {
                        that.parent().find('.inc').removeClass('disabled');
                    }
                }
            } else {
                that.parent().find('.dec').addClass('disabled');
                that.parent().find('.inc').removeClass('disabled');
            }
        });
    });
}

app.confirm = function (content, callback) {
    var l = new loader();
    l.require(['resources/vendor/jquery-confirm/jquery-confirm.min.js', 'resources/vendor/jquery-confirm/jquery-confirm.min.css'], function () {
        $.confirm({
            theme: 'modern',
            title: 'Are you sure?',
            content: content,
            icon: 'fa fa-smile-o',
            closeIcon: true,
            animation: 'scale',
            type: 'red',
            buttons: {
                confirm: {
                    text: 'Okay',
                    btnClass: 'btn-danger',
                    action: function () {
                        callback();
                    }
                },
                cancel: {
                    text: 'Close',
                    btnClass: 'btn-default'
                }
            }
        });
    }, true);
}

app.fadeout_alert = function () {
    var f = $('.alert-box');
    if (!f.length > 0)
        return;
    setTimeout(function () {
        f.delay(10000).fadeOut();
        $('html, body').animate({
            scrollTop: (f.offset().top - 15)
        }, 'slow');
    }, 1000);
}

app.image_popup = function () {
    var mg = $('.magnific-gallery');
    if (!mg.length > 0)
        return;
    mg.magnificPopup({
        type: 'image',
        tLoading: 'Loading...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
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
app.video_popup = function () {
    var mv = $('.magnific-video');
    if (!mv.length > 0)
        return;
    mv.magnificPopup({
        type: 'iframe'
    });
}
app.news_slider = function () {
    var $m = $('.news-slider');
    if ($m.length > 0) {
        var l = new loader();
        l.require(["resources/vendor/limarquee/liMarquee.css", "resources/vendor/limarquee/jquery.liMarquee.min.js"], function () {
            $('.news-slider .marquee').liMarquee({circular: false});
        });
    }

}

app.input_placeholder = function (t) {
    var i = $('.i-placeholder');
    if (!i.length > 0)
        return;
    i.each(function () {
        var that = $(this),
            input = that.find(':input'),
            data = input.data(),
            top = 0,
            left = 0;

        if (that.hasClass('i-active')) {
            return;
        }
        that.addClass('i-active');
        that.addClass('clearfix').css({
            position: 'relative'
        });
        top = parseInt(input.css('padding-top')) + 1;
        left = parseInt(input.css('padding-left')) + 1;

        that.prepend('<span class="placeholder' + (input.get(0).hasAttribute('required') ? ' req' : '') + '" style="position: absolute; top: ' + top + 'px; left: ' + left + 'px; line-height: 15px; z-index: 9; margin-left: 10px; color: #999; cursor: auto; pointer-events: none;' + (input.val() == '' ? '' : ' display: none;') + '">' + data.iPlaceholder + '</span>');
        input.unbind();
        input.on({
            input: function (e) {
                if ($(this).val() != '') {
                    that.find('.placeholder').hide();
                } else {
                    that.find('.placeholder').show();
                }
            },
            change: function (e) {
                if ($(this).val() != '') {
                    that.find('.placeholder').hide();
                } else {
                    that.find('.placeholder').show();
                }
            }
        });
    });
}

app.colorpicker = function () {
    var c = $('#colorpicker');
    if (!c.length > 0)
        return;
    var l = new loader();
    l.require(['resources/vendor/colorpicker/css/colorpicker.css', 'resources/vendor/colorpicker/js/colorpicker.js'], function () {
        c.unbind('ColorPicker');
        c.ColorPicker({
            color: '#000000',
            onChange: function (hsb, hex, rgb) {
                c.val('#' + hex);
            }
        })
    }, true);
}

app.lazy_load = function () {

    var lazyloadImages;
    if ('IntersectionObserver' in window) {
        lazyloadImages = document.querySelectorAll('.lazy');
        var imageObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var image = entry.target;
                    if (image.dataset.src) {
                        image.src = image.dataset.src;
                    }
                    image.classList.remove('lazy');
                    imageObserver.unobserve(image);
                }
            });
        });
        lazyloadImages.forEach(function (image) {
            imageObserver.observe(image);
        });
    } else {
        var lazyloadThrottleTimeout;
        lazyloadImages = document.querySelectorAll('.lazy');

        function lazyload() {
            if (lazyloadThrottleTimeout) {
                clearTimeout(lazyloadThrottleTimeout);
            }

            lazyloadThrottleTimeout = setTimeout(function () {
                var scrollTop = window.pageYOffset;
                lazyloadImages.forEach(function (img) {
                    if (img.offsetTop < (window.innerHeight + scrollTop)) {
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                        }
                        img.classList.remove('lazy');
                    }
                });
                if (lazyloadImages.length == 0) {
                    document.removeEventListener('scroll', lazyload);
                    window.removeEventListener('resize', lazyload);
                    window.removeEventListener('orientationChange', lazyload);
                }
            }, 20);
        }

        document.addEventListener('scroll', lazyload);
        window.addEventListener('resize', lazyload);
        window.addEventListener('orientationChange', lazyload);
    }
}


app.c_ajax_init = function () {
    app.fadeout_alert();
    app.chosen();
    app.captcha();
    app.ajax_call();
    app.bind_form();
    app.date_time_picker();
    app.numbers_only();
    app.numbers_row();
    app.input_placeholder();
    app.image_popup();
    app.video_popup();
    app.colorpicker();
    app.lazy_load();
}

app.c_init = function () {
    app.fadeout_alert();
    app.chosen();
    app.captcha();
    app.ajax_call();
    app.bind_form();
    app.tooltip();
    app.tiny_mce();
    app.show_password();
    app.tag_input();
    app.date_time_picker();
    app.load_more();
    app.numbers_only();
    app.numbers_row();
    app.image_popup();
    app.video_popup();
    app.colorpicker();
    app.input_placeholder();
    app.lazy_load();
}
