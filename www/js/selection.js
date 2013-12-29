/**
 * Время задержки для исчезновения хинта с количеством найденного товара
 * @author Ruslan
 * @example 6000 = 6 second
 */
const TIME_OUT = 6000;

function selection() {
    this.page_url = $('#page_url').val();
    this.catalogue_id = $('#catalogue_id').val();
    this.action_brand = '';
    this.action_attr = '';
    this.attr_brand_count = 0;
    this.attr_value_count = 0;
    this.attribute_range = {};

    this.price_min = $('#price_input_min').val();
    this.price_max = $('#price_input_max').val();

    var tempRange = {};
    $("div.attr_range_view").each(function (index) {
        var min = $(this).parent().find("input[type=text][id^=input_min]");
        var max = $(this).parent().find("input[type=text][id^=input_max]");

        if (!min.val().length && !max.val().length) return;

        var id = $(this).parent().find("input[type=text][id^=input_min]").attr("xid");

        tempRange[id] = {min: min.val(), max: max.val()};
    });

    this.attribute_range = tempRange;
}

selection.prototype.doUrl = function () {
    var result_action = '';
    var attr_brand_count = 0;
    var attr_value_count = 0;
    var attr_range_count = 0;

    var action_brand = '';
    var action_attr = '';
    var action_attr_range = '';

    $('input[rel=attr_brand_id]:checked').each(function () {
        attr_brand_count++;
        val = $(this).val();
        action_brand += 'b' + val;
    });

    $('input[rel=attr_value]:checked').each(function () {
        attr_value_count++;
        val = $(this).val();
        action_attr += val;
    });

    if ($('input[id*="attr_range_view_url_"]').length > 0) {
        $('input[id*="attr_range_view_url_"]').each(function (index) {
            var val = $(this).val();
            action_attr += val;

            var parent = $(this).parent();
            var _min = $(parent).find('input[name*="attr_range_min"]').val();
            var _max = $(parent).find('input[name*="attr_range_max"]').val();

            var xid = $(parent).find('input[name*="attr_range_max"]').attr('xid');

            if (parseInt(_min) > 0 || parseInt(_max) > 0) {
                attr_range_count++;
                action_attr_range += 'a' + xid + 'v' + _min + '-' + _max;
            }
        });
    }

    this.action_brand = action_brand;
    this.action_attr = action_attr;

    if (attr_brand_count > 0) {
        result_action += 'br/' + this.action_brand + '/';
    }
    if (attr_value_count > 0) {
        result_action += 'at/' + this.action_attr + '/';
    }

    if (attr_range_count > 0) {
        result_action += 'ar/' + action_attr_range + '/';
    }

    if (this.price_min > 0) {
        result_action += 'pmin/' + this.price_min + '/';
    }
    if (this.price_max > 0) {
        result_action += 'pmax/' + this.price_max + '/';
    }

    this.page_url += result_action;

    $('#catalog_compare_products_form').attr({'action': this.page_url});
}


selection.prototype.getRequest = function (evnt, attr_gr_id) {
    $.getJSON('/ajax/getattrcount/', {catalogue_id: this.catalogue_id, br: this.action_brand, at: this.action_attr, pmin: this.price_min, pmax: this.price_max, attribute_range: this.attribute_range}, function (data) {
        podbor_popup(data.items_count > 0 ? 'Найдено моделей:' + data.items_count + ' <a href="#" id="show_models">показать</a>' : 'Ничего не найдено', evnt);
        if (data == null) {
//            $('input[rel=attr_brand_id]').removeAttr("disabled");
            $('input[rel=attr_brand_id]').parent().removeClass('noactive');
            $('input[rel=attr_value]').parent().removeClass("noactive");
//            $('input[rel=attr_value]').removeAttr('disabled');
        }

        if (data.brands_count > 0) {
//            $('input[rel=attr_brand_id]:not(:checked)').attr({'disabled': 'disabled'});
            $('input[rel=attr_brand_id]').parent().addClass('noactive');
            $.each(data.brands, function (key, value) {
//                $('input[rel=attr_brand_id][value=' + value + ']').removeAttr('disabled');
                $('input[rel=attr_brand_id][value=' + value + ']').parent().removeClass('noactive');
            });
        }
        else {
            $('input[rel=attr_brand_id]:not(:checked)').removeAttr('disabled');
        }

        if (data.attrib_count > 0) {
            $.each(data.attrib, function (key, value) {
                if (key == attr_gr_id)  return;

                $('input[rel=attr_value][atg=' + key + ']').parent().addClass('noactive');

                $.each(value, function (i, attr) {
                    $('input[rel=attr_value][atid=' + attr + ']').removeAttr('disabled');
                    $('input[rel=attr_value][atid=' + attr + ']').parent().removeClass('noactive');
                });
            });
        }
        else {
            $('input[rel=attr_value]:not(:checked)').parent().addClass('noactive');
        }
    });
}


function podbor_popup(popup_text, evnt) {
    if ($('div.podbor_popup').length > 0) {
        $('div.podbor_popup').remove();
    }

    $("*:not(.podbor_popup)").click(function (e) {
        kids = e.target;
        var _a = kids;
        while (true) {
            _atag = _a.tagName;
            if (_atag && ($(_a).hasClass('podbor_popup'))) {
                break;
            }
            else {
                if (!_atag) {
                    $(".podbor_popup").remove();
                    break;
                }
                _a = _a.parentNode;
            }
        }
    });

//    right: winWidth - evnt.pageX+15 ,

    var offset = $("#catalog_compare_products_form").offset();

    $("#catalog_compare_products_form").append('<div class="podbor_popup"></div>');
    $(".podbor_popup").css({
        right: 200,
        top: evnt.pageY - offset.top - 15
    });

    $(".podbor_popup").prepend(popup_text);
    setTimeout(function () {
        $(".podbor_popup").animate({"opacity": 0}, 300).remove;
    }, TIME_OUT);
}

$(document).ready(function (evnt) {
//    if(this.catalogue_id != undefined){
//        var selection = new selection();
//        selection.doUrl();
//        selection.getRequest(evnt, 0);
//    }
    if ($(".jquery_slider").length > 0) {
        $(".jquery_slider").slider({
            range: true,
            min: slider_min,
            max: slider_max,
            step: 5,
            values: [slide_values_min, slide_values_max],
            slide: function (event, ui) {
                $("#price_input_min").val(ui.values[0]);
                $("#price_input_max").val(ui.values[1]);
            },
            stop: function (event, ui) {
                it_sel = new selection();
                it_sel.doUrl();
                it_sel.getRequest(event, 0);
            }
        });
    }

    $('input[rel=attr_brand_id]').click(function (evnt) {
        it_sel = new selection();
        it_sel.doUrl();
        it_sel.getRequest(evnt, 0);
    });

    $('input[rel=attr_value]').click(function (evnt) {
        attr_gr_id = $(this).attr('atg');
        it_sel = new selection();
        it_sel.doUrl();

        $.cookie('attr_gr_id', attr_gr_id);
        it_sel.getRequest(evnt, attr_gr_id);
    });

    var options_input_min = {
        callback: function () {
            var evnt = new Object();

            offset = $("#price_input_min").offset();
            evnt.pageX = offset.left;
            evnt.pageY = offset.top;

            var price_max = $("#price_input_max").val();
            price_max = price_max == '' ? slide_values_max : price_max;

            $(".jquery_slider").slider("option", "values", [$("#price_input_min").val(), price_max]);

            it_sel = new selection();
            it_sel.doUrl();
            it_sel.getRequest(evnt);
        },
        wait: 1500,
        captureLength: 2
    }


    var options_input_max = {
        callback: function () {
            var event = new Object();
            var slide_values_min;

            var offset = $("#price_input_max").offset();
            event.pageX = offset.left;
            event.pageY = offset.top;

            var price_min = $("#price_input_min").val()
            price_min = price_min == '' ? slide_values_min : price_min;

            $(".jquery_slider").slider("option", "values", [price_min, $("#price_input_max").val()]);

            var it_sel = new selection();
            it_sel.doUrl();
            it_sel.getRequest(event);
        },
        wait: 1500,
        captureLength: 2
    };

    $("#price_input_min").typeWatch(options_input_min);
    $("#price_input_max").typeWatch(options_input_max);

    $('#price_input_min').keyup(function (evnt) {
        var val = $(this).val();
        if (evnt.keyCode == 8 || evnt.keyCode == 46) {
            if (val == '' || val == 0) {
                var price_max = $("#price_input_max").val();
                price_max = price_max == '' ? slide_values_max : price_max;

                $(".jquery_slider").slider("option", "values", [slider_min, price_max]);

                it_sel = new selection();
                it_sel.doUrl();
                it_sel.getRequest(evnt);
            }
        }
    });

    $('#price_input_max').keyup(function (evnt) {
        var val = $(this).val();
        if (evnt.keyCode == 8 || evnt.keyCode == 46) {
            if (val == '' || val == 0) {
                var price_min = $("#price_input_min").val()
                price_min = price_min == '' ? slide_values_min : price_min;

                $(".jquery_slider").slider("option", "values", [price_min, slider_max]);

                it_sel = new selection();
                it_sel.doUrl();
                it_sel.getRequest(evnt);
            }
        }
    });

    $("input[name^=attr_range_min]").click(function (evnt) {
        if (evnt.keyCode == 8 || evnt.keyCode == 46) {

        }
    })

    $(".applay_filters a.product_button").click(function (ev) {
        ev.preventDefault();
    });
    $(".applay_filters a.product_button").click(function (ev) {

        var action = $(this).parents('form').attr('action');
        window.location.href = action;
    });

    $('#show_models').live('click', function (ev) {
        ev.preventDefault();
    });
    $('#show_models').live('click', function () {
        var action = $('#catalog_compare_products_form').attr('action');
        window.location.href = action;
    });

    it_sel = new selection();
    it_sel.doUrl();
});