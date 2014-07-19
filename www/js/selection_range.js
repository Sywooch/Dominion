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
                    objectValueSelection.setAttributeObj(xid, 1, data[xid]["min"], data[xid]["max"]);

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
                            var attributeId = $(this).parent().attr("xid");
                            objectValueSelection.setAttributeObj(attributeId, 1, ui.values[0], ui.values[1]);
                            objectValueSelection.attribute_id_range_active = attributeId;
                            objectValueSelection.attributesIdChecked = attributeId;

                            selection.select(objectValueSelection, event);
                        }
                    });

                    $(this).find("input[id^='input_min']").val(data[xid]["min"]);
                    $(this).find("input[id^='input_max']").val(data[xid]["max"]);

                    $(this).find("a.ui-slider-handle").append("<span></span>");
                    $(this).find("a.ui-slider-handle span").last().addClass("last");

                    var options_attributes_range_min = {
                        callback: function () {
                            var event = new Object();
                            var idAttribute = $("body").data("current_attribute_id");
                            var min = $("input[id=input_min_" + idAttribute + "]");

                            event.pageX = min.offset().left;
                            event.pageY = min.offset().top;

                            var attr_min = min.val();

                            $("div.fieldgroup[xid=" + xid + "] div.attr_range_view").slider("values", 0, attr_min);

                            objectValueSelection.setAttributeObj(xid, 1, attr_min, $("input[id=input_max_" + idAttribute + "]").val());
                            objectValueSelection.attribute_id_range_active = xid;
                            selection.select(objectValueSelection, event);
                        },
                        wait: 1500,
                        captureLength: 2
                    };

                    var options_attributes_range_max = {
                        callback: function () {
                            var event = new Object();
                            var idAttribute = $("body").data("current_attribute_id");
                            var max = $("input[id=input_min_" + idAttribute + "]");

                            event.pageX = max.offset().left;
                            event.pageY = max.offset().top;

                            var attr_max = max.val();

                            $("div.fieldgroup[xid=" + xid + "] div.attr_range_view").slider("values", 1, attr_max);

                            objectValueSelection.setAttributeObj(xid, 1, $("input[id=input_min_" + idAttribute + "]").val(), attr_max);
                            objectValueSelection.attribute_id_range_active = xid;
                            selection.select(objectValueSelection, event);
                        },
                        wait: 1500,
                        captureLength: 2
                    };

                    $(this).find("input[id^='input_min']").typeWatch(options_attributes_range_min);
                    $(this).find("input[id^='input_max']").typeWatch(options_attributes_range_max);
                });
            });

        /**
         * Listener for keyup
         */
        $("input[id^=input_min_], input[id^=input_max_]").keyup(function () {
            var textVal = $(this).attr("xid");

            if (!textVal.toString().length) return;

            $("body").data("current_attribute_id", textVal);
        });
    }
});    