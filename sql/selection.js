$(document).ready(function () {
    /**
     * Ini slider
     *
     * @param leftSide
     * @param rightSide
     * @param minValue
     * @param maxValue
     * @param selectorMin
     * @param selectorMax
     */
    $.initSlider = function (leftSide, rightSide, minValue, maxValue, selectorMin, selectorMax) {
        $(".attr_range_view", $(this)).slider({
            range: true,
            min: leftSide,
            max: rightSide,
            values: [minValue, maxValue],
            step: 5,
            slide: function (event, ui) {
                selectorMin.val(ui.values[0]);
                selectorMax.val(ui.values[1]);
            },
            stop: function (event, ui) {

            }
        });
    };

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

                        }
                    });
                    $(this).find("a.ui-slider-handle").append("<span></span>");
                    $(this).find("a.ui-slider-handle span").last().addClass("last");
                });

                $('div.fieldgroup[xid]').each(function (index) {
                    var xid = $(this).attr('xid');

                });

            });
    }
});