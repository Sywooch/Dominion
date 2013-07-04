<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:import href="_base.xsl"/>
    <xsl:import href="inc/_top_banners.xsl"/>
    <!-- HEAD -->
    <xsl:template name="title">
        <xsl:choose>
            <xsl:when test="//docinfo/title!=''">
                <xsl:value-of select="//docinfo/title" disable-output-escaping="yes"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="/page/default_title"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <!-- Keywords -->
    <xsl:template name="keywords">
        <xsl:variable name="keywords">
            <xsl:choose>
                <xsl:when test="//docinfo/keywords!=''">
                    <xsl:value-of select="//docinfo/keywords"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="/page/default_title"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:call-template name="keyword">
            <xsl:with-param name="key">
                <xsl:value-of select="$keywords"/>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>
    <xsl:template name="keyword">
        <xsl:param name="key"/>
        <xsl:variable name="key1"><![CDATA[<meta name="keywords" content="]]></xsl:variable>
        <xsl:variable name="key2"><![CDATA["/>]]></xsl:variable>
        <xsl:value-of select="$key1" disable-output-escaping="yes"/>
        <xsl:value-of select="$key" disable-output-escaping="yes"/>
        <xsl:value-of select="$key2" disable-output-escaping="yes"/>
    </xsl:template>
    <!-- Keywords -->
    <!-- Description -->
    <xsl:template name="description">
        <xsl:variable name="description">
            <xsl:choose>
                <xsl:when test="//docinfo/description!=''">
                    <xsl:apply-templates select="//docinfo/description"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="/page/default_title"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:call-template name="descript">
            <xsl:with-param name="des">
                <xsl:value-of select="$description"/>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>
    <xsl:template name="descript">
        <xsl:param name="des"/>
        <xsl:variable name="des1"><![CDATA[<meta name="description" content="]]></xsl:variable>
        <xsl:variable name="des2"><![CDATA["/>]]></xsl:variable>
        <xsl:value-of select="$des1" disable-output-escaping="yes"/>
        <xsl:value-of select="$des" disable-output-escaping="yes"/>
        <xsl:value-of select="$des2" disable-output-escaping="yes"/>
    </xsl:template>
    <!-- Description -->
    <xsl:template name="headSection">
        <title>
            <xsl:call-template name="title"/>
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <xsl:call-template name="keywords"/>
        <xsl:call-template name="description"/>
    </xsl:template>

    <!-- Top menu -->
    <xsl:template match="main_menu">
        <xsl:variable name="pid">
            <xsl:apply-templates select="@another_pages_id"/>
        </xsl:variable>
        <xsl:variable name="target">
            <xsl:choose>
                <xsl:when test="@is_new_win='1'">_blank</xsl:when>
                <xsl:otherwise>_self</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:choose>
            <xsl:when test="@on_path='1'">
                <li class="active">
                    <xsl:apply-templates select="name"/>
                </li>
            </xsl:when>
            <xsl:otherwise>
                <li>
                    <a href="{href}" target="{$target}">
                        <xsl:apply-templates select="name"/>
                    </a>
                </li>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <!-- Top menu -->

    <!--Сравнение-->
    <xsl:template match="comp_item">
        <li>
            <a class="del" href="javascript:void(0);" onclick="compare('{@item_id}',1,0)" title="Удалить">&#160;</a>
            <a href="/item/{@item_id}/{urname}/">
                <strong>
                    <xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/>
                </strong>
            </a>
        </li>
    </xsl:template>
    <xsl:template match="comp_cat">
        <li>
            <h3>
                <xsl:value-of select="name"/>
            </h3>
        </li>
        <xsl:apply-templates select="comp_item"/>
        <xsl:if test="count(comp_item) &gt; 1">
            <li>
                <a href="/compare/{@catalogue_id}/" class="crvlink" target="_blank">Сравнить товарные позиции</a>
            </li>
            <br/>
        </xsl:if>
    </xsl:template>

    <xsl:template match="breadcrumbs">
        <xsl:if test="position()!=last()">
            <li>
                <a class="pseudo" href="javascript:void(0);">
                    <xsl:if test="count(breadcrumbs) = 0">
                        <xsl:attribute name="href">
                            <xsl:value-of select="url"/>
                        </xsl:attribute>
                    </xsl:if>
                    <span>
                        <xsl:value-of select="name"/>
                    </span>
                </a>
                <xsl:if test="count(breadcrumbs) &gt; 0">
                    <div class="dialog_box breadcrumb">
                        <a href="#" class="close_icon">Close</a>
                        <ul class="menu dialog_content">
                            <xsl:apply-templates select="breadcrumbs" mode="sub"/>
                        </ul>
                    </div>
                </xsl:if>
            </li>
        </xsl:if>
    </xsl:template>

    <xsl:template match="breadcrumbs" mode="sub">
        <li>
            <a href="{url}">
                <xsl:if test="@id='brands'">
                    <xsl:attribute name="style">color: #ED1C24</xsl:attribute>
                </xsl:if>
                <xsl:value-of select="name"/>
            </a>
        </li>
    </xsl:template>

    <xsl:template match="socials">
        <li>
            <a href="{url}" target="_blank">
                <img src="/images/social/{image/@src}" width="{image/@w}" height="{image/@h}"/>
            </a>
        </li>
    </xsl:template>

    <!--Сравнение-->

    <!-- кредитный баннер -->
    <xsl:template match="banner_credit_images">
        <a href="{url}">
            <img src="/images/bn/{image/@src}" alt="{alt}"/>
        </a>
    </xsl:template>

    <xsl:template match="banner_timetable">
        <xsl:apply-templates select="description"/>
    </xsl:template>

    <xsl:template match="banner_phone">
        <li>
            <xsl:apply-templates select="description"/>
        </li>
    </xsl:template>

    <xsl:template match="catalog_banner_left|banner_bottom">
        <xsl:variable name="target">
            <xsl:choose>
                <xsl:when test="newwin='1'">_blank</xsl:when>
                <xsl:otherwise>_self</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="type='0'">
                <!-- Картинка -->
                <xsl:choose>
                    <xsl:when test="url!=''">
                        <a href="{url}" target="{$target}">
                            <img src="/images/bn/{image/@src}" alt=""/>
                        </a>
                    </xsl:when>
                    <xsl:otherwise>
                        <img src="/images/bn/{image/@src}" alt=""/>
                    </xsl:otherwise>
                </xsl:choose>
                <!-- Картинка -->
            </xsl:when>
            <xsl:when test="type='1'">
                <!-- Флеш -->
                <xsl:choose>
                    <xsl:when test="url!=''">
                        <a href="{url}" target="{$target}" title="{alt}">
                            <object type="application/x-shockwave-flash" data="/images/bn/{image/@src}"
                                    width="{image/@w}" height="{image/@h}">
                                <param name="movie" value="/images/bn/{image/@src}"/>
                                <param value="transparent" name="wmode"/>
                            </object>
                        </a>
                    </xsl:when>
                    <xsl:otherwise>
                        <div style="z-index:1">
                            <object type="application/x-shockwave-flash" data="/images/bn/{image/@src}"
                                    width="{image/@w}" height="{image/@h}">
                                <param name="movie" value="/images/bn/{image/@src}"/>
                                <param value="transparent" name="wmode"/>
                            </object>
                        </div>
                    </xsl:otherwise>
                </xsl:choose>
                <!-- Флеш -->
            </xsl:when>
            <xsl:otherwise>
                <xsl:choose>
                    <xsl:when test="description!=''">
                        <xsl:apply-templates select="description"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="alt"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="through-currencies">
        <ul class="valuta">
            <xsl:apply-templates select="//currencies"/>
        </ul>
    </xsl:template>

    <xsl:template name="through-brands">
        <xsl:choose>
            <xsl:when test="not(//data/@section)"></xsl:when>
            <xsl:otherwise>
                <h3>Популярные производители</h3>
            </xsl:otherwise>
        </xsl:choose>

        <xsl:call-template name="through-currencies"/>

        <xsl:choose>
            <xsl:when test="not(//data/@section)"></xsl:when>
            <xsl:otherwise>
                <ul class="brands"></ul>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="banner_java_scripts">
        <!--<xsl:apply-templates select="banner_code"/>-->
        <xsl:value-of select="banner_code" disable-output-escaping="yes"/>
    </xsl:template>

    <xsl:template match="banner_counters">
        <div class="footer_small_box">
            <!--<xsl:apply-templates select="banner_code"/>-->
            <xsl:value-of select="banner_code" disable-output-escaping="yes"/>
        </div>
    </xsl:template>


    <xsl:template match="banner_right">
        <div class="sidebar_block">
            <xsl:variable name="target">
                <xsl:choose>
                    <xsl:when test="@newwin='1'">_blank</xsl:when>
                    <xsl:otherwise>_self</xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="@type='0'">
                    <!-- Картинка -->
                    <xsl:choose>
                        <xsl:when test="url!=''">
                            <a href="{url}" target="{$target}">
                                <img src="/images/bn/{image/@src}" alt=""/>
                            </a>
                        </xsl:when>
                        <xsl:otherwise>
                            <img src="/images/bn/{image/@src}" alt=""/>
                        </xsl:otherwise>
                    </xsl:choose>
                    <!-- Картинка -->
                </xsl:when>
                <xsl:when test="@type='1'">
                    <!-- Флеш -->
                    <xsl:choose>
                        <xsl:when test="url!=''">
                            <a href="{url}" target="{$target}" title="{alt}">
                                <object type="application/x-shockwave-flash" data="/images/bn/{image/@src}"
                                        width="{image/@w}" height="{image/@h}">
                                    <param name="movie" value="/images/bn/{image/@src}"/>
                                    <param value="transparent" name="wmode"/>
                                </object>
                            </a>
                        </xsl:when>
                        <xsl:otherwise>
                            <div style="z-index:1">
                                <object type="application/x-shockwave-flash" data="/images/bn/{image/@src}"
                                        width="{image/@w}" height="{image/@h}">
                                    <param name="movie" value="/images/bn/{image/@src}"/>
                                    <param value="transparent" name="wmode"/>
                                </object>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                    <!-- Флеш -->
                </xsl:when>
                <xsl:otherwise>
                    <xsl:choose>
                        <xsl:when test="description!=''">
                            <xsl:apply-templates select="description"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:apply-templates select="alt"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:otherwise>
            </xsl:choose>
        </div>
    </xsl:template>

    <xsl:template match="banner_socnets">
        <li>
            <xsl:variable name="target">
                <xsl:choose>
                    <xsl:when test="@newwin='1'">_blank</xsl:when>
                    <xsl:otherwise>_self</xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <a href="{url}" target="{$target}" style="background: url('/images/bn/{image/@src}') no-repeat"></a>
        </li>
    </xsl:template>

    <xsl:template name="cart">
        <xsl:choose>
            <xsl:when test="//cart/total_count &gt; 0">
                <a href="/cart/">Корзина
                    <br/>
                    <span>Товаров
                        <xsl:value-of select="//cart/total_count"/> &mdash;
                        <xsl:value-of select="//cart/total_summ"/> грн
                    </span>
                </a>
            </xsl:when>
            <xsl:otherwise>
                <a>Корзина
                    <br/>
                    <span>Товаров 0</span>
                </a>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="compare_list">
        <li>
            <a href="#" xid="{@id}" class="delete_compare">Close</a>
            <h3>
                <a href="{href}"><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/>
                </a>
            </h3>
        </li>
    </xsl:template>

    <xsl:template name="login_form">
        <a href="#" class="pseudo">
            <span>Вход</span>
        </a>
        <div id="login_form_box" class="dialog_box login">
            <a href="#" class="close_icon">Close</a>
            <form id="login_form" action="/" method="get" class="dialog_content">
                <div class="errhold"></div>
                <div>
                    <label for="login_email">E-mail</label>
                    <input id="login_email" name="login_email" type="email"/>
                </div>
                <div>
                    <label for="login_password">Пароль</label>
                    <input id="login_password" name="login_password" type="password"/>
                </div>
                <input type="submit" value="Войти"/>
                <a href="#" class="more" id="forgot_form_link">Забыли пароль?</a>
                <a href="/register/" class="more">Регистрация</a>
            </form>
        </div>
        <div id="forgot_form_box" class="dialog_box_forgot">
            <a href="#" class="close_icon">Close</a>
            <form id="forgot_form" action="/ajax/forgot/" method="post" class="dialog_content">
                <div class="errhold"></div>
                <div>
                    <label for="forgot_email">E-mail</label>
                    <input id="forgot_email" name="forgot_email" type="email"/>
                </div>
                <input type="submit" value="Напомнить"/>
            </form>
        </div>
        <a href="/register/" id="reg_link">Регистрация</a>
    </xsl:template>

    <xsl:template name="user_logout">
        <a href="/register/logout/" class="pseudo">Выход</a>
        <a href="/register/account/" id="reg_link">Кабинет</a>
    </xsl:template>

    <xsl:template name="podbor"/>
    <xsl:template name="headers"/>
    <xsl:template name="compare"/>

    <!-- кредитный баннер -->
    <xsl:template match="/page">
        <xsl:variable name="doctype">
            <![CDATA[<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">]]></xsl:variable>
        <!--<xsl:value-of select="$doctype" disable-output-escaping="yes"/>-->
        <html>
            <head>
                <xsl:call-template name="headSection"/>
                <xsl:variable name="style">
                    <![CDATA[<!--[if IE 6.0]><link rel="stylesheet" type="text/css" href="/css/ie.css" media="screen"/><![endif]-->]]></xsl:variable>
                <link id="favicon" href="/favicon.ico" rel="icon" type="image/x-icon"/>
                <link rel="stylesheet" type="text/css" href="/css/ui-lightness/jquery-ui-1.8.18.custom.css"/>
                <!--<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />-->
                <link rel="stylesheet" type="text/css" href="/css/style.css"/>
                <link rel="stylesheet" type="text/css" href="/css/classes.css"/>
                <link rel="stylesheet" type="text/css" href="/css/blocks.css"/>
                <link rel="stylesheet" type="text/css" href="/css/popup.css"/>
                <link rel="stylesheet" type="text/css" href="/css/fancybox.css"/>
                <link rel="stylesheet" type="text/css" href="/css/elastic.css"/>

                <!--<script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script>-->

                <!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>-->

                <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
                <script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>

                <script type="text/javascript" src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>

                <script type='text/javascript' src='/js/fancybox.js'></script>
                <!--<script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script>-->
                <script type="text/javascript" src="/js/jsfunctions.js"></script>
                <script type="text/javascript" src="/js/jquery.validate.js"></script>
                <script type="text/javascript" src="/js/swfobject.js"></script>
                <script type="text/javascript" src="/js/elastic_search.js"></script>
                <!--<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom.js"></script>-->
                <script type="text/javascript">
                    swfobject.registerObject("flash_banner", "9.0.0");
                </script>
                <script type="text/javascript" src="/js/main.js"></script>
                <script type="text/javascript" src="/js/tabs.js"></script>
                <xsl:apply-templates select="banner_java_scripts"/>
                <xsl:call-template name="headers"/>
            </head>
            <body>
                <div id="top_left_bg"></div>
                <div id="main">
                    <xsl:if test="//data/@is_compare=1">
                        <xsl:attribute name="style">width:100%;</xsl:attribute>
                    </xsl:if>
                    <div id="header">
                        <a id="logo">
                            <xsl:if test="not(//data/@is_start)">
                                <xsl:attribute name="href">/</xsl:attribute>
                            </xsl:if>
                            интернет-магазин электроники и бытовой техники в Харькове
                        </a>
                        <ul id="main_menu" class="menu">
                            <xsl:apply-templates select="main_menu"/>
                        </ul>

                        <!-- Вызыввем шаблон из inc/_top_banners.xsl -->
                        <xsl:call-template name="_top_banners_show_banners"/>


                        <form id="search_form" method="get" action="/search/">
                            <input id="search_text" name="search_text" type="text" value="Поиск по сайту">
                                <xsl:if test="data/query != '' ">
                                    <xsl:attribute name="value">
                                        <xsl:value-of select="data/query"/>
                                    </xsl:attribute>
                                </xsl:if>
                            </input>
                            <a href="#" class="button_link">Искать</a>
                        </form>
                        <div id="header_nav">
                            <xsl:choose>
                                <xsl:when test="user_data/user_id &gt; 0">
                                    <xsl:call-template name="user_logout"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:call-template name="login_form"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </div>
                        <ul id="networks">
                            <xsl:apply-templates select="banner_socnets"/>
                        </ul>
                        <div id="header_phones">
                            <div class="phone">
                                <a href="#" class="phone_icon">Phone number</a>
                                <a href="#" class="pseudo">
                                    <span>
                                        <xsl:apply-templates select="//right_side_mobile"/>
                                    </span>
                                </a>
                                <div id="phones_box_dialog" class="dialog_box phone">
                                    <a href="#" class="close_icon">Close</a>
                                    <div class="dialog_content">
                                        <xsl:apply-templates select="//right_side_mobile_content"/>
                                    </div>
                                </div>
                            </div>
                            <div class="phone">
                                <span class="phone_icon">Phone number</span>
                                <strong>
                                    <xsl:apply-templates select="//right_side_phone"/>
                                </strong>
                            </div>
                            <div class="last">
                                <a href="#" class="phone_icon">Phone number</a>
                                <a href="#" class="pseudo">
                                    <span>Заказать обратный звонок</span>
                                </a>
                                <div id="call_form_box" class="dialog_box call">
                                    <a href="#" class="close_icon">Close</a>
                                    <form id="call_form" action="/ajax/getcall/" method="post" class="dialog_content">
                                        <div class="errhold"></div>
                                        <div>
                                            <label for="call_name">ФИО<span class="asterisk">*</span>:
                                            </label>
                                            <input id="call_name" name="call_name" type="text"/>
                                        </div>
                                        <div>
                                            <label for="call_phone">Телефон для связи<span class="asterisk">*</span>:
                                            </label>
                                            <input id="call_phone" name="call_phone" type="text"/>
                                        </div>
                                        <div>
                                            <label for="call_time">Лучшее время для звонка:</label>
                                            <input id="call_time" name="call_time" type="text"/>
                                        </div>
                                        <input type="submit" value="Отправить"/>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="all" class="clearfix">
                        <xsl:if test="//data/@is_compare=1">
                            <xsl:attribute name="style">overflow:inherit;</xsl:attribute>
                        </xsl:if>
                        <xsl:apply-templates select="data" mode="body"/>
                    </div>
                </div>
                <div id="footer_carrier">
                    <div id="footer">
                        <a id="footer_logo">
                            <xsl:if test="not(//data/@is_start)">
                                <xsl:attribute name="href">/</xsl:attribute>
                            </xsl:if>
                        </a>
                        <div id="copy">
                            <xsl:apply-templates select="footer_page_left"/>
                            <span class="develop">
                                <p>
                                    <a href="/ajax/go/?url=http://adlabs.com.ua">Разработка сайта &mdash; AD|LABS</a>
                                </p>
                            </span>
                        </div>
                        <div class="footer_big_box">
                            <xsl:apply-templates select="banner_bottom"/>
                        </div>
                        <div id="small_boxes_container">
                            <xsl:apply-templates select="banner_counters"/>
                        </div>
                        <div id="footer_phones">
                            <xsl:apply-templates select="footer_phones"/>
                        </div>
                        <div id="footer_operational_mode">
                            <xsl:apply-templates select="footer_work"/>
                            <span class="develop">
                                <p>
                                    <a href="/ajax/map/" class="map">Схема проезда</a>
                                </p>
                            </span>
                        </div>
                    </div>
                    <div id="bottom_left_bg"></div>
                </div>
                <div class="litext">
                    <script type='text/javascript'>/* build:::7 */
                        var liveTex = true,
                        liveTexID = 26194,
                        liveTex_object = true;
                        (function() {
                        var lt = document.createElement('script');
                        lt.type ='text/javascript';
                        lt.async = true;
                        lt.src = 'http://cs15.livetex.ru/js/client.js';
                        var sc = document.getElementsByTagName('script')[0];
                        if ( sc ) sc.parentNode.insertBefore(lt, sc);
                        else document.documentElement.firstChild.appendChild(lt);
                        })();
                    </script>
                </div>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="data[@is_start]" mode="body">
        <div id="content">
            <xsl:apply-templates select="."/>
        </div>
        <div id="sidebar">
            <div id="cart">
                <xsl:call-template name="cart"/>
            </div>
            <div id="sidebar_slider">
                <ul class="slider_buttons">
                    <xsl:apply-templates select="banner_right" mode="slider_buttons"/>
                </ul>
                <ul class="slider_holder">
                    <xsl:apply-templates select="banner_right"/>
                </ul>
            </div>
        </div>
        <div id="front_text_block" class="clearfix">
            <xsl:if test="count(news_block) &gt; 0">
                <div id="news_block">
                    <h2>
                        <a href="/news/">Новости и акции</a>
                    </h2>
                    <xsl:apply-templates select="news_block"/>
                    <p>
                        <a href="/news/">Все новости</a>
                    </p>
                </div>
            </xsl:if>
            <xsl:if test="docinfo/txt != ''">
                <div id="about">
                    <xsl:apply-templates select="docinfo/txt"/>
                </div>
            </xsl:if>
        </div>
    </xsl:template>

    <xsl:template match="data[not(@is_start)]" mode="body">
        <div id="content">
            <xsl:if test="count(breadcrumbs) &gt; 0">
                <ul class="breadcrumb">
                    <li>
                        <a href="/" class="pseudo">
                            <span>Главная</span>
                        </a>
                    </li>
                    <xsl:apply-templates select="breadcrumbs"/>
                </ul>
            </xsl:if>
            <xsl:apply-templates select="."/>
        </div>
        <div id="sidebar">
            <xsl:if test="not(//data/@is_compare)">
                <div id="cart">
                    <xsl:call-template name="cart"/>
                </div>
            </xsl:if>
            <div id="catalog_compare_products">
                <xsl:if test="count(compare_list) &gt; 0">
                    <xsl:attribute name="class">sidebar_products_block</xsl:attribute>
                    <h3>Товары в сравнении</h3>
                    <ul class="menu">
                        <xsl:apply-templates select="compare_list"/>
                    </ul>
                    <xsl:if test="count(compare_list) &gt; 1">
                        <a href="/compare/{@id}/" class="button_link">Сравнить</a>
                    </xsl:if>
                </xsl:if>
            </div>
            <xsl:call-template name="podbor"/>
            <xsl:choose>
                <xsl:when test="@is_slider">
                    <div id="sidebar_slider">
                        <ul class="slider_buttons">
                            <xsl:apply-templates select="banner_right" mode="slider_buttons"/>
                        </ul>
                        <ul class="slider_holder">
                            <xsl:apply-templates select="banner_right"/>
                        </ul>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates select="banner_right"/>
                </xsl:otherwise>
            </xsl:choose>

        </div>
        <xsl:call-template name="compare"/>
    </xsl:template>
</xsl:stylesheet>

