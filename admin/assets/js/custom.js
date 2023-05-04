app.display_icon = function () {
    var d = $('#display_icon');
    if (!d.length > 0)
        return;
    d.unbind('change');
    d.change(function () {
        var that = $(this);
        $('.default-icon span').attr('class', 'icon ' + that.val());
    });
}


app.url_string = function (str) {
    str = $.trim(str).toLowerCase();
    str = str.replace(/\s\s+/g, '');
    str = str.replace(/[^a-z0-9- \s]/gi, '');
    str = str.replace(/~+$/, '');
    str = str.replace(/\s\s/g, ' ');
    return str.replace(/[\s+]/g, '-')
}

app.seo_section = function () {
    var p = $('[data-rule-title]');
    if (!p.length > 0)
        return;
    var iva = $('[data-page-title]'),
        ipu = $('[data-page-url]');
    p.each(function () {
        var that = $(this);
        if (ipu.val() == '') {
            that.unbind();
            if (that.get(0).tagName == 'SELECT') {
                that.on('change', function () {
                    var that = $(this),
                        data = that.find('option:selected').data();
                    if (app.checkdata(data, 'url')) {
                        $(that.data('replacewith'))
                    }
                });
            } else {
                that.keyup(function () {
                    var str = $(this).val();
                    iva.val(str).valid();
                    str = app.url_string(str);
                    str = str.replace(/-\s*$/, '');
                    ipu.val(str).valid();
                });
            }
        }
    });
}

app.count_char = function () {
    var c = $('.count-char');
    if (!c.length > 0)
        return;
    var count = c.attr('data-count');
    count = (count != '' && count != 'undefined' && count > 0) ? count : 160;
    var limit = count,
        cls = 'total-char';
    c.each(function () {
        var that = $(this),
            parent = that.parent();
        parent.append('<span class="' + cls + '"><span>0</span> Characters</span>');
        that.unbind('input');
        that.on('input', function (e) {
            var that = $(this),
                len = that.val().length;
            parent.find('.' + cls + ' > span').text(that.val().length);
            if (len > limit) {
                parent.find('.' + cls).addClass('over');
            } else {
                parent.find('.' + cls).removeClass('over');
            }
        });
    });
}

app.total_keyword = 0;
app.keyword_count = function (that, e, t) {
    var limit = 8,
        cls = 'total-keywords',
        parent = that.parent();
    if (!parent.find('.' + cls).length > 0) {
        parent.append('<span class="' + cls + '"><span>0</span> Keywords</span>');
    }
    if (e == undefined)
        return;
    if (t == true) {
        app.total_keyword--;
    } else {
        app.total_keyword++;
    }
    parent.find('.' + cls + ' > span').text(app.total_keyword);
    if (app.total_keyword > limit) {
        parent.find('.' + cls).addClass('over');
    } else {
        parent.find('.' + cls).removeClass('over');
    }
}
app.bind_grid = function () {
    var grid = $('.grid');
    if (!grid.length > 0)
        return;
    grid.each(function () {
        var that = $(this),
            data = that.data();
        // Add
        var add = $('[rel="' + data.btnAdd + '"]');
        if (add.length > 0) {
            add.unbind('click');
            add.click(function (e) {
                e.preventDefault();
                app.row_add(that, data);
                return false;
            });
        }

        // Delete
        var del = $('[rel="' + data.btnDelete + '"]');
        if (del.length > 0) {
            del.unbind('click');
            del.click(function (e) {
                e.preventDefault();
                app.row_delete(this);
                return false;
            });
        }
    });
}
app.row_add = function (that, data) {
    var index = parseInt(data.rowIndex),
        ins_id = '',
        tr_prefix = '_TR';
    var prev_tr = $('.' + data.gridBody + ' > tr:last-child'),
        sp_id = prev_tr.find('select.sp-product option:selected').val();

    if (that.find('.' + data.gridBody + ' > tr:last-child').length <= 0 && app.checkdata(data, 'html')) {
        ins_id = $('.' + data.html).attr('id');
    } else {
        index++;
        that.attr('data-row-index', index).data('row-index', index);
        ins_id = that.find('.' + data.gridBody + ' > tr:last-child').attr('id');
    }


    if (ins_id != 0) {
        var ind = ins_id.replace(/[^\d.]/g, '');
        tr_prefix = ins_id.replace(ind, '');
    }

    var tr_id = tr_prefix + (index),
        hm = '';
    if (that.find('.' + data.gridBody + '> tr#' + ins_id).length <= 0 && app.checkdata(data, 'html')) {
        hm = $('.' + data.html).html();
    } else {
        hm = that.find('.' + data.gridBody + '> tr#' + ins_id).html();
    }
    hm = hm.replace(eval('/' + tr_prefix + ind + '/g'), tr_id);

    if (app.checkdata(data, 'pid')) {
        $.each(data.pid, function (k, v) {
            var no = v.replace(/[^\d.]/g, ''),
                prefix = v.replace(no, '');
            hm = hm.replace(eval('/' + prefix + index + '/g'), v);
        });
    }

    if (that.find('.' + data.gridBody + '> tr#' + ins_id).length <= 0 && app.checkdata(data, 'html')) {
        $('.' + data.gridBody).html('<tr id="' + tr_id + '">' + hm + '</tr>').find(':input').removeAttr('disabled');
    } else {
        $('<tr id="' + tr_id + '"></tr>').insertAfter('.' + data.gridBody + ' > tr#' + ins_id).html(hm);
    }

    var tr = $('.' + data.gridBody + ' > tr#' + tr_id);
    if (tr.find('select.sp-product').length > 0) {
        tr.find('select.sp-product option[value="' + sp_id + '"]').hide();
    }
    tr.find('select,input,textarea').val('');
    tr.find('.form-group').removeClass('has-success').removeClass('has-error');
    tr.find('label.error').remove();
    tr.find('div.chosen-container').remove();
    tr.find('div.custom-img').remove();

    var child = tr.find('.grid');
    if (child.length > 0) {
        var c_data = child.data();
        $('.' + c_data.gridBody).find('tr:gt(0)').remove();
    }

    app.custom_file();
    app.chosen();
    app.bind_grid();
    app.bind_form();
    app.date_time_picker();
}

app.row_delete = function (that) {
    var that = $(that),
        data = that.data();
    if (app.checkdata(data, 'delInput')) {
        var input = $(data.delInput);
        if (input.val() == '') {
            input.val(data.delId);
        } else {
            input.val(input.val() + ',' + data.delId);
        }
    }
    that.closest('tr#' + data.id).remove();
}

app.sorting = function () {
    var s = $('.sorting');
    if (!s.length > 0)
        return;
    var l = new loader();
    l.require(['resources/vendor/jquery/jquery-ui.min.js'], function () {
        var data = s.data();
        s.sortable({
            opacity: 0.6,
            cursor: 'move',
            update: function () {
                var order = $(this).sortable('serialize'),
                    auto = $('#auto_sort');
                if (auto.prop('checked') == false) {
                    var u = $('#btn_sort');
                    u.unbind('click');
                    u.click(function () {
                        sort(order, data);
                    });
                } else {
                    sort(order, data);
                }
            }
        });

        function sort(order, data) {
            order = order + '&action=sort' + (app.checkdata(data, 'input') ? '&' + (data.input) + '=' + ($('input[name="' + data.input + '"]').val()) : '');
            app.send_data(data.url, order);
        }
    }, true);
}
app.uploader = function () {
    var u = $('.uploader');
    if (!u.length > 0)
        return;
    var l = new loader();
    l.require(['resources/vendor/uploader/uploads.js'], function () {
        u.each(function () {
            var that = $(this),
                data = that.data(),
                uploader = new qq.FileUploader({
                    element: that.get(0),
                    uploadButtonText: (app.checkdata(data, 'uploadButtonText') ? data.uploadButtonText : 'Select Pictures'),
                    listElement: document.getElementById(data.listId),
                    action: hostname + 'uploads?token=' + token + '&rnd=' + Math.random() + '&type=' + data.type,
                    multiple: true
                });
        });
    }, true);
}
app.up_default = function () {
    var check_it = $('.check-it li div.attachment');
    if (!check_it.length > 0)
        return;
    var checked = $('.check-it li input:checked').length,
        data = $('.check-it').data();
    check_it.each(function () {
        var that = $(this);
        that.unbind('click');
        that.click(function () {
            var t = $(this),
                parent = t.parent('li');
            if (parent.hasClass('selected')) {
                checked--;
                parent.removeClass('selected').find('input[type="checkbox"]').prop('checked', false);
            } else {
                if (checked == data.checkedLimit) {
                    app.show_msg('Limit exceed!', 'You can\'t set more than ' + data.checkedLimit + ' images as default.', 'error');
                    return false;
                }
                checked++;
                parent.addClass('selected').find('input[type="checkbox"]').prop('checked', true);
            }
        });
    });
}

app.page_reload = function () {
    setTimeout(function () {
        window.location.reload();
    }, 3000)
}

app.modal_width = function () {
    var w = $("body").width() - 200;
    $("#modal .modal-dialog").css({width: w});
}

app.img_crop = function () {
    var $image = $('.crop-container > img');
    if (!$image.length > 0)
        return;
    var l = new loader();
    l.require(['assets/css/cropper/cropper.css', 'assets/css/cropper/main.css', 'assets/js/cropper/cropper.js'], function () {
        var $dataX = $('#crop-x');
        var $dataY = $('#crop-y');
        var $dataHeight = $('#crop-height');
        var $dataWidth = $('#crop-width');
        var options = {
            preview: '.img-preview',
            crop: function (e) {
                $dataX.val(Math.round(e.detail.x));
                $dataY.val(Math.round(e.detail.y));
                $dataHeight.val(Math.round(e.detail.height));
                $dataWidth.val(Math.round(e.detail.width));
            }
        };
        $image.cropper('destroy').cropper(options);
    });
}


app.custom_file = function () {
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
}

app.ajax_init = function () {
    app.c_ajax_init();
}

$(function () {
    app.c_init();
    app.display_icon();
    app.seo_section();
    app.count_char();
    app.bind_grid();
    app.sorting();
    app.uploader();
    app.up_default();
})
