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