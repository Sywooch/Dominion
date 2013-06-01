$(document).ready(function () {

    (function ($) {
        $.fn.showData = function (data) {
            $.each(data, function (n, element) {
                append(
                    "<a href='" + item.URL + "'><div class='product-li'><div class='product_name'>"
                        + item.PRODUCT_NAME + "</div><div class='product_typename'>" + item.TYPENAME +
                        "</div><div class='product_brand'>" + item.BRAND + "</div><div class='product_article'>"
                        + item.ARTICLE + "</div></div></a>");
            });
        }
    })(jQuery);

    $(function () {
        var cache = {};
        var term;
        var error;
        $("input#search_text").autocomplete({
            minLength: 2,
            source: function (request, response) {
                term = request.term;
                if (term in cache) {
                    cache[term].showData();

                    return;
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
                        response($.map(data, function (item) {
                            return {
                                label: item.TYPENAME + ", " + item.NAME_PRODUCT +
                                    ", " + item.BRAND + ", " + item.ARTICLE,
                                value: item.TYPENAME + ", " + item.NAME_PRODUCT +
                                    ", " + item.BRAND + ", " + item.ARTICLE,
                                url: item.URL
                            }
                        })
                        )
                        ;
                    }
                })
            },
            select: function (event, ui) {
                window.location.href = ui.item.url;
            },
            focus: function (event, ui) {
                $(this).val(ui.item.label);
            },
            delay: 500
        })
    })
})
