var ww = document.body.clientWidth;

$(document).ready(function () {
    convertElement();

    $(".nav li a").each(function () {
        if ($(this).next().length > 0) {
            $(this).addClass("parent");
        }
    })

    $(".toggleMenu").click(function (e) {
        e.preventDefault();
        $(this).toggleClass("active");
        $(".nav").toggle();
    });
    adjustMenu();

    $(".catalog-box ul").each(function () {
        $(this).find('li:eq(7)').nextAll().addClass('hidden');
    });

    $('.show-more').click(function () {
        var overHidden = $(this).prev('ul').find('li:eq(7)').nextAll();

        if (overHidden.hasClass('hidden')) {
            overHidden.removeClass('hidden');
            $(this).text('Скрыть лишние');
        }
        else {
            overHidden.addClass('hidden');
            $(this).text('Показать еще');
        }
        ;
    });
    $('.catalog-box h3').click(function () {
        $(this).next('ul').toggleClass('visible');
    });
})

$(window).bind('resize orientationchange', function () {
    ww = document.body.clientWidth;
    adjustMenu();
});

var convertElement = function () {

//    $("second-level")


    $("ul.second-level li[xid=parentLi]").each(function () {
            var len = $(this).find("div.col").length;
            var mainWidth = len > 1 ? $(this).width() * 3.5 : $(this).find("div.col").width();

            $(this).find("div.third-level-wraper").width(mainWidth);


//
//            if (!(len > 1)) return true;
//
//            var heightOfElements = [];
//            var index = 0;
//            var nameSelector = "";
//            var mainElement = $(this);
//            $(this).find("div.col").each(function () {
//                    nameSelector += "div.col:eq(" + index + "), ";
//                    index++;
//                    heightOfElements.push($(this).height());
//
//                    if (index % 3 == 0) {
//                        nameSelector = nameSelector.substring(0, nameSelector.length - 2);
//
//                        mainElement.find(nameSelector).height(Math.max.apply(Math, heightOfElements));
//                        nameSelector = "";
//                        heightOfElements = [];
//                    } else if (index == len) {
//                        nameSelector = nameSelector.substring(0, nameSelector.length - 2);
//
//                        mainElement.find(nameSelector).height(Math.max.apply(Math, heightOfElements));
//                    }
//                }
//            );
        }
    )
    ;
};

var adjustMenu = function () {
    if (ww <= 768) {
        $(".toggleMenu").css("display", "inline-block");
        if (!$(".toggleMenu").hasClass("active")) {
            //$(".nav").hide();
        } else {
            $(".nav").show();
        }
        $(".nav li").unbind('mouseenter mouseleave');
        $(".nav li a.parent").unbind('click').bind('click', function (e) {
            // must be attached to anchor element to prevent bubbling
            e.preventDefault();
            $(this).parent("li").toggleClass("hover");
        });
    }
    else if (ww > 768) {
        $(".toggleMenu").css("display", "none");
        $(".nav").show();
        $(".nav li").removeClass("hover");
        $(".nav li a").unbind('click');
        $(".nav li").unbind('mouseenter mouseleave').bind('mouseenter mouseleave', function () {
            // must be attached to li so that mouseleave is not triggered when hover over submenu
            $(this).toggleClass('hover');
        });
    }
}