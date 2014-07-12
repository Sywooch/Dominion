function attr_range_selection(xid) {
    this.xid = xid;
    this.catalogue_id = $('#catalogue_id').val();

    var values = $(".attr_range_view[xid=" + xid + "]").slider("option", "values");

    this.input_min = values[0];
    this.input_max = values[1];
}

attr_range_selection.prototype.doUrl = function (evnt) {
    var _xid = this.xid;
    $.post('/ajax/getrangeattr/',
        {xid: _xid,
            catalogue_id: this.catalogue_id,
            min: this.input_min,
            max: this.input_max
        }, function (data) {

            $('#attr_range_view_url_' + _xid).val(data);

            it_sel = new selection();
            it_sel.doUrl();
            it_sel.getRequest(evnt);
        });
}

$(document).ready(function () {
    if ($("div.fieldgroup[xid]").length > 0) {
        $.post(
            "/ajax/getattrisrangeview/",
            {
                "catalogue_id": $("input#catalogue_id").val()
            },
            "json"
        ).done(function (data) {
                $('div.fieldgroup[xid]').each(function (index) {
                    var xid = $(this).attr('xid');
                    $(".attr_range_view", $(this)).slider({
                        range: true,
                        min: data[xid]["left_side"],
                        max: data[xid]["right_side"],
                        values: [data[xid]["min"], data[xid]["max"]],
                        step: 5,
                        slide: function (event, ui) {
                            $(this).parent().find("input[id^='input_min']").val(ui.values[0]);
                            $(this).parent().find("input[id^='input_max']").val(ui.values[1]);
                        },
                        stop: function (event, ui) {
                            objectValueSelection.setAttributeObj($(this).parent().attr("xid"), 1, ui.values[0], ui.values[1]);
                        }
                    });

                    $(this).find("input[id^='input_min']").val(data[xid]["min"]);
                    $(this).find("input[id^='input_max']").val(data[xid]["max"]);

                    $(this).find("a.ui-slider-handle").append("<span></span>");
                    $(this).find("a.ui-slider-handle span").last().addClass("last");
                });
            });
    }

    var options_input_range_min = {
        callback: function () {
            var value = this.text;
            var xid = this.el.attributes[nodeValue = 'xid'].nodeValue;
            var parent = $('#input_min_' + xid).parent();

            var evnt = new Object();
            offset = $('#input_min_' + xid).offset();
            evnt.pageX = offset.left;
            evnt.pageY = offset.top;

            var price_max = $(parent).find('input[id*="input_max_"]').val();
            price_max = price_max == '' ? attr_range_view[xid][1] : price_max;

            $(parent).find(".attr_range_view[xid=" + xid + "]").slider("option", "values", [value, price_max]);

            rn_sel = new attr_range_selection(xid);
            rn_sel.doUrl(evnt);
        },
        wait: 1500,
        captureLength: 2
    }

    var options_input_range_max = {
        callback: function () {
            var value = this.text;
            var xid = this.el.attributes[nodeValue = 'xid'].nodeValue;
            var parent = $('#input_min_' + xid).parent();

            var evnt = new Object();

            offset = $("#price_input_max").offset();
            evnt.pageX = offset.left;
            evnt.pageY = offset.top;

            var price_min = $(parent).find('input[id*="input_min_"]').val();
            price_min = price_min == '' ? attr_range_view[xid][0] : price_min;

            $(parent).find(".attr_range_view[xid=" + xid + "]").slider("option", "values", [price_min, value]);

            rn_sel = new attr_range_selection(xid);
            rn_sel.doUrl(evnt);
        },
        wait: 1500,
        captureLength: 2
    }

    $('input[id*="input_min_"]').typeWatch(options_input_range_min);
    $('input[id*="input_max_"]').typeWatch(options_input_range_max);

    $('input[id*="input_min_"]').keyup(function (evnt) {
        var val = $(this).val();
        var xid = $(this).attr('xid');
        if (evnt.keyCode == 8 || evnt.keyCode == 46) {
            if (val == '' || val == 0) {
                var parent = $(this).parent();

                var price_max = $(parent).find('input[id*="input_max_"]').val();
                price_max = price_max == '' ? attr_range_view[xid][1] : price_max;

                $(parent).find(".attr_range_view[xid=" + xid + "]").slider("option", "values", [attr_range_view[xid][0], price_max]);

                var event = new Object();
                offset = $(this).offset();
                event.pageX = offset.left;
                event.pageY = offset.top;

                rn_sel = new attr_range_selection(xid);
                rn_sel.doUrl(event);
            }
        }
    });

    $('input[id*="input_max_"]').keyup(function (evnt) {
        var val = $(this).val();
        var xid = $(this).attr('xid');
        if (evnt.keyCode == 8 || evnt.keyCode == 46) {
            if (val == '' || val == 0) {
                var parent = $(this).parent();

                var price_min = $(parent).find('input[id*="input_min_"]').val();
                price_min = price_min == '' ? attr_range_view[xid][0] : price_min;

                $(parent).find(".attr_range_view[xid=" + xid + "]").slider("option", "values", [price_min, attr_range_view[xid][1]]);

                var event = new Object();
                offset = $(this).offset();
                event.pageX = offset.left;
                event.pageY = offset.top;

                rn_sel = new attr_range_selection(xid);
                rn_sel.doUrl(event);
            }
        }
    });
});    