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

/**
 * Select by send ajax query to server
 *
 * @param dataObject
 */
selection.select = function (dataObject, currentElement) {
    var idAttribute = dataObject.attribute_id_checked;
    $.data(
        document.body,
        "status_attribute",
        idAttribute
    );

    var statusBrand = dataObject.check_brands;
    $.data(document.body, "status_brand", statusBrand);
    var attributeRangeId = dataObject.attribute_id_range_active;
    var priceRangeChecked = dataObject.price_range_check;
    $.ajax({
        type: "POST",
        url: "/ajax/getattrcount/",
        data: {
            catalogue_id: dataObject.catalogue_id,
            price_min: dataObject.price_min,
            price_max: dataObject.price_max,
            brands: dataObject.brands_id,
            attributes: dataObject.attributes_id,
            check_brands: statusBrand
        },
        success: function (resultData) {
            podbor_popup(resultData["count_items"] > 0 ? 'Найдено моделей:' + resultData["count_items"] + ' <a href="#" id="show_models">показать</a>' : 'Ничего не найдено', currentElement);

            var mainSelector = null;
            if (idAttribute == undefined || idAttribute == null) {
                mainSelector = $("input[rel=attr_value]:not(:checked)");
            } else {
                mainSelector = $("div.fieldgroup input[type=checkbox]:not(:checked, [atg=" + idAttribute + "]), div.fieldgroup input[type=checkbox][rel=attr_brand_id]:not(:checked)");
            }

            mainSelector.parent().addClass("noactive");

            $.each(resultData["attributes"], function (nameKey, value) {

                var selector = "";
                var objectValueSelector = {};
                var convertPrice = null;
                switch (nameKey) {
                    case "brands":
                        if (dataObject.isEmpty()) {
                            $("input[rel=attr_brand_id]").parent().removeClass("noactive");

                            return;
                        }

                        selector = servicesSelection.brands(value["buckets"]);

                        objectValueSelector = $(selector);
                        objectValueSelector.parent().removeClass("noactive");

                        break;
                    case "attributes":
                        var objectSelector = servicesSelection.attributes(value["attributes_identity"]["buckets"]);
                        selector = objectSelector.check;

                        objectValueSelector = $(selector);

                        objectValueSelector.removeAttr("disabled", "disabled");
                        objectValueSelector.parent().removeClass("noactive");

                        $.each(objectSelector.range, function (index, valRange) {
                            var elementRange = $(valRange.selectorRange);

                            if (attributeRangeId == elementRange.parent().attr("xid")) return;


                            elementRange.slider("values", 0, valRange.valueFrom);
                            elementRange.slider("values", 1, valRange.valueTo);
                            $(valRange.selectorInputMin).val(valRange.valueFrom);
                            $(valRange.selectorInputMax).val(valRange.valueTo);
                        });

                        break;
                    case "price_min":
                        if (priceRangeChecked) break;

                        convertPrice = Math.round(value.value);
                        $(".jquery_slider").slider("values", 0, convertPrice);
                        $("input#price_input_min").val(convertPrice);

                        break;
                    case "price_max":
                        if (priceRangeChecked) break;

                        convertPrice = Math.round(value.value);
                        $(".jquery_slider").slider("values", 1, convertPrice);
                        $("input#price_input_max").val(convertPrice);

                        break;
                }
            });
        }
    });
};

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
        right: 220,
        top: evnt.pageY - offset.top - 15
    });

    $(".podbor_popup").prepend(popup_text);
    setTimeout(function () {
        $(".podbor_popup").animate({"opacity": 0}, 300).remove;
    }, TIME_OUT);
}

$(document).ready(function (evnt) {
    objectValueSelection.catalogue_id = $("input#catalogue_id").val();

    if ($(".jquery_slider").length > 0) {
        $(".jquery_slider").slider({
            range: true,
            min: slider_min,
            max: slider_max,
            step: 20,
            values: [slide_values_min, slide_values_max],
            slide: function (event, ui) {
                $("#price_input_min").val(ui.values[0]);
                $("#price_input_max").val(ui.values[1]);
            },
            stop: function (event, ui) {
                objectValueSelection.price_min = ui.values[0];
                objectValueSelection.price_max = ui.values[1];

                objectValueSelection.price_range_check = 1;

                selection.select(objectValueSelection, event);
            }
        });
        $("input#price_input_min").val(slide_values_min);
        $("input#price_input_max").val(slide_values_max);
    }

    /**
     * Check brands
     */
    $('input[rel=attr_brand_id]').click(function (event) {
        if ($(this).is(":checked")) {
            objectValueSelection.brands_id = $(this).val();
            objectValueSelection.checkBrands = 1;

//            select = new selection();
//            select.doUrl();
        } else {
            objectValueSelection.unsetBrand($(this).val());
        }

        selection.select(objectValueSelection, event);
    });

    /**
     * Check attribute
     */
    $('input[rel=attr_value]').click(function (evnt) {
        if ($(this).is(":checked")) {
            var attrId = $(this).attr("atg");
            objectValueSelection.setAttributeArr(attrId, 0, $(this).attr("atid"));
            objectValueSelection.attributesIdChecked = attrId;

//            select = new selection();
//            select.doUrl();
        } else {
            var attrId = $(this).attr("atg");
            objectValueSelection.unsetAttributeArr(attrId, $(this).attr("atid"));
            objectValueSelection.attributesIdChecked = attrId;
        }

        selection.select(objectValueSelection, evnt);
    });

    $('#price_input_min').keyup(function (evnt) {
        var val = $(this).val();
        if (evnt.keyCode == 8 || evnt.keyCode == 46) {
            if (val == '' || val == 0) {
                var price_max = $("#price_input_max").val();
                price_max = price_max == '' ? slide_values_max : price_max;

                $(".jquery_slider").slider("option", "values", [slider_min, price_max]);
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
            }
        }
    });

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
        var resultUrlAttributes = buildUrl.mergeUrl(
            objectValueSelection.brands_id,
            objectValueSelection.attributes_id,
            objectValueSelection.price_min,
            objectValueSelection.price_max,
            $.data(document.body, "status_brand"),
            $.data(document.body, "status_attribute")
        );

        var action = $('#page_url').attr("value");

        window.location.href = action + resultUrlAttributes;
    });

//    it_sel = new selection();
//    it_sel.doUrl();
});