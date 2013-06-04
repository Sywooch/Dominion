$(document).ready(function () {
    $(function () {
        var cache = {};
        var term;
        var error;
        $("input#search_text").autocomplete({
            minLength: 2,
            source: function (request, response) {
                term = request.term;
                if (term in cache) {
                    response(
                        cache[term]
                    );
                }
                $.ajax({
                    type: "POST",
                    url: "/elasticsearch/",
                    data: {
                        term: term,
                        event: "GET"
                    },
                    dataType: "json",
                    success: function (data) {
                        cache[term] = data;
                        $("ul.ui-autocomplete").hover("color", "#009933");
                        response(
                            data
                        );
                    }
                })
            },
            select: function (event, ui) {
                $(this).val(ui.item.TYPENAME + ", " + ui.item.BRAND + ", " + ui.item.PRICE);
                window.location.href = ui.item.URL;
            },
            focus: function (event, ui) {
                $(this).val(ui.item.TYPENAME + ", " + ui.item.BRAND + ", " + ui.item.PRICE);
            },
            delay: 500
        }).data("uiAutocomplete")._renderItem = function (ul, item) {
            var itemArray = null;
            $.each(item, function (n, value) {
                if (value == null) {
                    item[n] = "";
                }
            });

            return $("<li></li>").data("item.autocomplete", item).append("<a href='" + item['URL'] + "'><div class='products'><img src='/images/it/" + item['IMAGE1'] + "' /></div><div class='details'>" + item['TYPENAME'] + ", " + item['BRAND'] + "</div><div class='price'>цена: " + item['PRICE'] + "</div></a>").appendTo(ul);
        }
    })
})
