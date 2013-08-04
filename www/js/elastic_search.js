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
                    type: "GET",
                    url: "/elasticsearch/",
                    data: {
                        term: term,
                        event: "GET"
                    },
                    dataType: "json",
                    success: function (data) {
                        cache[term] = data;
                        var len = data.length;
                        $("ul.ui-autocomplete").hover("color", "#009933");
                        response(
                            data
                        );
                    }
                })
            },
            select: function (event, ui) {
                $(this).val(ui.item.name + " " + ui.item.brand + " " + ui.item.name_product);
                window.location.href = ui.item.url;
            },
            focus: function (event, ui) {
                $(this).val(ui.item.name + " " + ui.item.brand + " " + ui.item.name_product);
            },
            delay: 500
        }).data("uiAutocomplete")._renderItem = function (ul, item) {
            if (item.else_results) {
                item.url = "/search/" + item.else_results;
                return $("<li></li>").data("item.autocomplete", item).append(
                    "<a href=" + item.url + "><span class='else_results'>Еще результаты &#8594;</a></span>"
                ).appendTo(ul);
            }
            return $("<li></li>").data("item.autocomplete", item).append(
                "<a href='" +
                    item.url +
                    "'><div class='products'><img src='/images/it/" + item.image.url + "' width= '" + item.image.width + "' height= '" + item.image.height + "' />" +
                    "</div><div class='details'>" +
                    item.name +
                    " " +
                    item.brand +
                    " " +
                    item.name_product +
                    "</div><div class='price'>цена: " +
                    item.price +
                    "</div></a>"
            ).appendTo(ul);
        }
    })
})
