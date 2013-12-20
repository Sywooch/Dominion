function reload() {
    var rndval = new Date().getTime();
    $('#capcha').html('<img src="/kcaptcha/kcaptcha_view.php?f=' + rndval + '" width="120" height="50" border="0" alt="" />');
}

$(document).ready(function () {
    if ($("#header_slider ul.slider_holder").length) {
        galleries("#header_slider ul.slider_holder", 500, 7000, "#header_slider .slider_buttons", 1);
    }

    if ($("#sidebar_slider ul.slider_holder").length) {
        galleries("#sidebar_slider ul.slider_holder", 500, 7000, "#sidebar_slider .slider_buttons", 1);
    }

    $('#search_text').focus(function () {
        var val = $(this).val();
        if (val == 'Поиск по сайту') $(this).val('');
    });
    $('#search_text').blur(function () {
        var val = $(this).val();
        if (val == '') $(this).val('Поиск по сайту');
    });

    $("*:not(.pseudo), *:not(.phone_icon)").click(function (e) {
        kids = e.target;
        var _a = kids;
        while (true) {
            _acl = _a.className;
            _atag = _a.tagName;
            if ((_atag && (_acl == 'dialog_box login')) || (_acl == 'pseudo')) {
                break;
            }
            else {
                if (!_atag) {
                    $("#login_form_box").hide();
                    break;
                }
                _a = _a.parentNode;
            }
        }

        kids = e.target;
        var _a = kids;
        while (true) {
            _acl = _a.className;
            _atag = _a.tagName;
            if ((_atag && (_acl == 'dialog_box phone')) || (_acl == 'pseudo') || (_acl == 'phone_icon')) {
                break;
            }
            else {
                if (!_atag) {
                    $("#phones_box_dialog").hide();
                    break;
                }
                _a = _a.parentNode;
            }
        }

        kids = e.target;
        var _a = kids;
        while (true) {
            _acl = _a.className;
            _atag = _a.tagName;
            if ((_atag && (_acl == 'dialog_box call')) || (_acl == 'pseudo') || (_acl == 'phone_icon')) {
                break;
            }
            else {
                if (!_atag) {
                    $("#call_form_box").hide();
                    break;
                }
                _a = _a.parentNode;
            }
        }

        kids = e.target;
        var _a = kids;
        while (true) {
            _acl = _a.className;
            _atag = _a.tagName;
            if ((_atag && (_acl == 'dialog_box breadcrumb')) || (_acl == 'pseudo')) {
                break;
            }
            else {
                if (!_atag) {
                    $(".dialog_box.breadcrumb").hide();
                    break;
                }
                _a = _a.parentNode;
            }
        }

        kids = e.target;
        var _a = kids;
        while (true) {
            _acl = _a.className;
            _atag = _a.tagName;
            if ((_atag && (_acl == 'dialog_box warranty')) || (_acl == 'pseudo')) {
                break;
            }
            else {
                if (!_atag) {
                    $(".dialog_box.warranty").hide();
                    break;
                }
                _a = _a.parentNode;
            }
        }
    });

    function galleries(gSelector, time1, time2, numbSelector, bool) {
        var gItem = $(gSelector + " li");
        var gLength = $(gItem).length;
        var numbItem = $(numbSelector + " li a");
        var n, m;
        var fIter;

        if (bool) {
            $.each(gItem, function (i, gtItem) {
                $(gItem[i]).css({"zIndex": gLength - i, opacity: 0});
            });
            playHead(0);
        }

        function playHead(activePos) {
            $(gItem).css({"opacity": "0"});
            $(gItem[activePos]).css({"opacity": "1"});
            $(numbItem).removeClass("active");
            $(numbItem[activePos]).addClass("active");
            fIter = setTimeout(function () {
                gPlay(activePos);
            }, time2);
        }

        function gPlay(n) {
            if (n < gLength - 1) {
                m = n + 1;
                gNext(n, m);
                n++;
            } else {
                gNext(gLength - 1, 0);
                n = 0;
            }
            fIter = setTimeout(function () {
                gPlay(n);
            }, time2);
        }

        function gNext(gi, gj) {
            $(gItem[gi]).animate({opacity: 0}, time1).css({"zIndex": 0});
            $(gItem[gj]).animate({opacity: 1}, time1).css({"zIndex": 40});
            $(numbItem[gi]).removeClass("active");
            $(numbItem[gj]).addClass("active");
        }

        $(numbItem).live("click", function (event) {
            event.preventDefault();
            changePos(this)
        });

        function changePos(thisItm) {
            clearTimeout(fIter);
            var iNew = $(numbItem).index(thisItm);
            thisActive = $(numbItem).parent().find(".active");
            var iOld = $(numbItem).index(thisActive);
            gNext(iOld, iNew);
            fIter = setTimeout(function () {
                gPlay(iNew);
            }, time2);
        }
    }

    $('input[name=compare]').live('click', function () {
        var val = $(this).val();
        var checked = 0;
        if ($(this).is(':checked')) {
            checked = 1;
        }

        $.post('/ajax/compare/', {id: val, checked: checked}, function (data) {
            if (data != '') {
                $('#catalog_compare_products').addClass('sidebar_products_block');
            }

            if (data == '') {
                $('#catalog_compare_products').removeClass('sidebar_products_block');
            }
            $('#catalog_compare_products').html(data);
        });
    });

    $('.delete_compare').live('click', function (ev) {
        ev.preventDefault();
    });
    $('.delete_compare').live('click', function () {
        var val = $(this).attr('xid');
        var checked = 0;

        $.post('/ajax/compare/', {id: val, checked: checked}, function (data) {
            if (data == '') {
                $('#catalog_compare_products').removeClass('sidebar_products_block');
            }
            $('#catalog_compare_products').html(data);
            $('input[name=compare][value=' + val + ']').removeAttr('checked');
        });
    });

    $("a.product_button.incard").click(function (ev) {
        ev.preventDefault();
    });
    $("a.product_button.incard").click(function (ev) {
        count = 1;
        xid = $(this).attr('xid');

        $.getJSON('/ajax/addcart/', {id: xid, count: count}, function (data) {
            $("#cart").html(data.html);
            by_popup(data.add_cart_message, ev);
        });
    });

    $("#order_button").click(function (ev) {
        ev.preventDefault();
    });
    $("#order_button").click(function (ev) {
        $(this).parents('form').submit();
    });

    $("a.button_link").click(function (ev) {
        ev.preventDefault();
    });
    $("a.button_link").click(function (ev) {
        var val = $('search_text').val();
        if (val != 'Поиск по сайту') {
            $(this).parents('form').submit();
        }
    });

    $("#forgot_form_link").click(function (ev) {
        ev.preventDefault();
    });
    $("#forgot_form_link").click(function (ev) {
        $('#login_form_box').find('a.close_icon').trigger('click');
        $('#forgot_form_box').show();
    });

    function by_popup(popup_text, evnt) {
        var winWidth = $("body").width();

        $(".close, .close-popup").live("click", function (event) {
            event.preventDefault();
        });
        $(".close, .close-popup").live("click", function () {
            $(".popupchik").remove();
        });

        $("*:not(.popupchik)").click(function (e) {
            kids = e.target;
            var _a = kids;
            while (true) {
                _atag = _a.tagName;
                if (_atag && ($(_a).hasClass('popupchik'))) {
                    break;
                }
                else {
                    if (!_atag) {
                        $(".popupchik").remove();
                        break;
                    }
                    _a = _a.parentNode;
                }
            }
        });
        //selector = $(this).parent();

        $("body").append('<div class="popupchik by_popup"><a href="#" class="close-popup">close</a></div>');
        $(".popupchik").css({
            right: winWidth - evnt.pageX,
            top: evnt.pageY
        });
        selector = $(".popupchik");
        $(selector).prepend(popup_text);
        setTimeout(function () {
            $(".by_popup").animate({"opacity": 0}, 200).remove;
        }, 3000)
    }

    $("#login_form").validate({
        errorLabelContainer: $("#login_form div.errhold"),
        submitHandler: function (form) {
            data = $(form).serialize();
            $.post('/ajax/sigin/', data, function (data) {
                if (data == 1) {
                    location.reload(true);
                }
                else {
                    $('#login_form div.errhold').show();
                    $('<label class="error">Ошибка авторизации</label>').appendTo('#login_form div.errhold');
                }
            });
        },
        rules: {
            login_email: {
                required: true,
                minlength: 2
            },
            login_password: {
                required: true,
                minlength: 2
            }
        },
        messages: {
            login_email: "Поле Логин пустое",
            login_password: "Поле Пароль пустое"
        },
        onkeyup: false
    });

    $("#call_form").validate({
        errorLabelContainer: $("#call_form div.errhold"),
        submitHandler: function (form) {
            data = $(form).serialize();
            $.post('/ajax/getcall/', data, function (data) {
                $('#call_form').prepend('<div class="success_message">' + data + '</div>');
                setTimeout("$('#call_form_box a.close_icon').trigger('click')", 1500);
                setTimeout("$('#call_form div.success_message').remove()", 1300);
                form.reset();
            });
        },
        rules: {
            call_name: {
                required: true,
                minlength: 2
            },
            call_phone: {
                required: true,
                minlength: 2
            }
        },
        messages: {
            call_name: "Поле Имя пустое",
            call_phone: "Поле Телефон пустое"
        },
        onkeyup: false
    });

    $("#forgot_form").validate({
        errorLabelContainer: $("#forgot_form div.errhold"),
        submitHandler: function (form) {
            data = $(form).serialize();
            $.post('/ajax/forgot/', data, function (data) {
                if (data == 1) {
                    $('#forgot_form').prepend('<div class="success_message">Ваш запрос принят</div>');

                    setTimeout("$('#forgot_form_box a.close_icon').trigger('click')", 1500);
                    setTimeout("$('#forgot_form div.success_message').remove()", 1300);

                    $('#forgot_form div.errhold').empty();
                }
                else {
                    $('#forgot_form div.errhold').show();
                    $('<label class="error">Указанный E-mail не найден</label>').appendTo('#forgot_form div.errhold');
                }
            });
            form.reset();
        },
        rules: {
            forgot_email: {
                required: true,
                minlength: 2
            }
        },
        messages: {
            forgot_email: "Поле E-mail пустое"
        },
        onkeyup: false
    });

    if ($(".popup").length > 0) {
        popup(".popup")
    }
    function popup(open_btn) {
        var winWidth = $("body").width();

        $(".warranty_popup .close_icon").live("click", function (event) {
            event.preventDefault();
        });

        $(".warranty_popup .close_icon").live("click", function () {
            $(".warranty_popup").remove();
        });

        $(open_btn).click(function (event) {
            event.preventDefault();
        });

        $(open_btn).click(function (e) {
            $(".warranty_popup").remove();

//            $("*:not(.warranty_popup)").click(function (e) {
//                kids = e.target;
//                var _a = kids;
//
//                elem = jQuery(kids);
//
//                while (true) {
//                    _atag = _a.tagName;
//                    if (_atag && ($(_a).hasClass('popupchik'))) {
//                        break;
//                    }
//                    else {
//                        if (!_atag) {
//                            $(".popupchik").remove();
//                            break;
//                        }
//                        _a = _a.parentNode;
//                    }
//                }
//            });
            //selector = $(this).parent();

            var clickElement = $(this);
            var positionElement = $(this).parent().find(".pseudo");

            ajaxlink = $(this).attr("href");
            $.get(ajaxlink, function (data) {
                $("body").append(
                    '<div class="warranty_popup"><div class="dialog_box warranty"><a href="#" class="close_icon">Закрыть</a><div class="dialog_content"/></div></div>'
                );
                $(".warranty_popup .dialog_box").css({
                    left: positionElement.offset().left - positionElement.attr("offset"),
                    top: positionElement.offset().top + positionElement.height()+ 3,
                    'background-position': '65% 0',
                    width: positionElement.attr("width")
                });
                $(".warranty_popup .dialog_box .dialog_content").html(data);
                $(".warranty_popup .dialog_box").show();
            });

        });
    }

    if ($(".noitem_reserve").length > 0) {
        $(".noitem_reserve").fancybox({
            'transitionIn': 'elastic',
            'transitionOut': 'elastic'
        });
    }

    $('#content_cart a.product_button').click(function (ev) {
        ev.preventDefault();
    });
    $("#content_cart a.product_button").click(function () {
        $("#cart_text_block").slideDown(1000, function () {
            $("#content_cart a.product_button").parent().hide();
        })
    });

    $('.phone_icon').click(function (ev) {
        ev.preventDefault();
    });
    $(".phone_icon").click(function () {
        $(this).next().trigger('click');
    });

    $("a.zoom").click(function (event) {
        event.preventDefault();
    });
    $("a.zoom").click(function (event) {
        $("a.gr_fancybox").trigger('click');
    });

    $(".map").fancybox({
        'titlePosition': 'inside',
        'transitionIn': 'none',
        'transitionOut': 'none'
    });
});