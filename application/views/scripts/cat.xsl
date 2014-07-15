<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:import href="_main.xsl"/>
    <xsl:import href="inc/_catalog_menu.xsl"/>

    <xsl:template name="section_url">
        <xsl:value-of select="//data/@cat_real_url"/>
        <xsl:if test="//data/@br_page!=''">br/<xsl:value-of select="//data/@br_page"/>/</xsl:if>
        <xsl:if test="//data/@at_page!=''">at/<xsl:value-of select="//data/@at_page"/>/</xsl:if>
        <xsl:if test="//data/@ar_page!=''">ar/<xsl:value-of select="//data/@ar_page"/>/</xsl:if>
        <xsl:if test="//data/@show_price_min &gt; 0">pmin/<xsl:value-of select="//data/@show_price_min"/>/</xsl:if>
        <xsl:if test="//data/@show_price_max &gt; 0">pmax/<xsl:value-of select="//data/@show_price_max"/>/</xsl:if>
    </xsl:template>

    <xsl:template match="attr_brands">
        <label class="checkbox_label">
            <xsl:if test="@is_disabled = 1">
                <xsl:attribute name="class">checkbox_label noactive</xsl:attribute>
            </xsl:if>
            <input type="checkbox" name="attr_brand_id[]" value="{@id}" rel="attr_brand_id">
                <xsl:if test="@selected = 1">
                    <xsl:attribute name="checked">checked</xsl:attribute>
                </xsl:if>
            </input>
            <xsl:value-of select="name"/>
        </label>
        <script type="text/javascript">
            <xsl:if test="@selected = 1">
                objectValueSelection.brands_id = <xsl:value-of select="@id"/>;
            </xsl:if>
        </script>
    </xsl:template>

    <xsl:template match="attr_value">
        <label class="checkbox_label">
            <xsl:if test="@parent_id != //data/@attr_gr_id and @is_disabled = 1">
                <xsl:attribute name="class">checkbox_label noactive</xsl:attribute>
            </xsl:if>
            <input type="checkbox" name="attr_value[]" value="a{@parent_id}v{@id}" rel="attr_value" atg="{@parent_id}"
                   atid="{@id}">
                <xsl:if test="@selected = 1">
                    <xsl:attribute name="checked">checked</xsl:attribute>
                </xsl:if>
                <xsl:if test="@parent_id != //data/@attr_gr_id and @is_disabled = 1">
                    <xsl:attribute name="disabled">disabled</xsl:attribute>
                </xsl:if>
            </input>
            <xsl:value-of select="name"/>
        </label>
        <script type="text/javascript">
            <xsl:if test="@selected = 1">
                objectValueSelection.setAttributeArr(<xsl:value-of select="@parent_id"/>, 0, <xsl:value-of select="@id"/>);
            </xsl:if>
        </script>
    </xsl:template>

    <xsl:template match="attr_cat">
        <h3>
            <a href="#">
                <xsl:value-of select="name"/>
            </a>
        </h3>
        <div class="fieldgroup">
            <xsl:if test="count(attr_value[@selected = 1]) &gt; 0">
                <xsl:attribute name="style">display: block;</xsl:attribute>
            </xsl:if>
            <span class="unit">
                <xsl:apply-templates select="attr_value"/>
            </span>
        </div>
    </xsl:template>

    <xsl:template match="attr_cat[@is_range_view=1]">
        <xsl:variable name="atid" select="@id"/>
        <h3>
            <a href="#">
                <xsl:value-of select="name"/>
            </a>
        </h3>
        <div class="fieldgroup" xid="{@id}">
            <xsl:if test="count(//attr_range_mm[@id = $atid]) &gt; 0">
                <xsl:attribute name="style">display: block;</xsl:attribute>
            </xsl:if>
            <input type="hidden" name="attr_range_view_url_{@id}" id="attr_range_view_url_{@id}"
                   value="{//attr_range_view_url[@id = $atid]/@url}"/>
            <label for="input_min_{@id}">От&#160;</label>
            <input type="text" id="input_min_{@id}" name="attr_range_min[{@id}]" xid="{@id}"
                   value=""/>
            <label for="input_max_{@id}">&#160;до&#160;</label>
            <input type="text" id="input_max_{@id}" name="attr_range_max[{@id}]" xid="{@id}"
                   value=""/>
            &#160;<xsl:value-of select="attr_value[position()=1]/@unit_name"/>
            <div class='attr_range_view'></div>
        </div>
    </xsl:template>

    <xsl:template match="price_line">
        <li class="{style}">
            <xsl:value-of select="price"/>
        </li>
    </xsl:template>

    <xsl:template match="price_line" mode="java_script">
        slide_coord[<xsl:value-of select="position()"/>] = new Array();

        slide_coord[<xsl:value-of select="position()"/>]['min'] = <xsl:value-of select="price_from"/>;
        slide_coord[<xsl:value-of select="position()"/>]['max'] = <xsl:value-of select="price"/>;
        slide_coord[<xsl:value-of select="position()"/>]['step'] = <xsl:value-of select="@step"/>;
    </xsl:template>

    <xsl:template name="podbor">
        <h3>Подбор товара по параметрам</h3>
        <form id="catalog_compare_products_form" method="post">
            <input type="hidden" name="page_url" id="page_url" value="{//data/@cat_real_url}"/>
            <input type="hidden" name="catalogue_id" id="catalogue_id" value="{//data/@id}"/>

            <xsl:if test="count(//price_line) &gt; 2">
                <input type="hidden" name="min_slider_price" id="min_slider_price"
                       value="{//price_line[position()=1]/price}"/>
                <input type="hidden" name="max_slider_price" id="max_slider_price"
                       value="{//price_line[position()=last()]/price}"/>
                <h3>
                    <a href="#">Цена</a>
                </h3>
                <div class="fieldgroup" style="display: block;">
                    <label for="price_input_min">От&#160;</label>
                    <input type="text" id="price_input_min" name="price_min">
                        <xsl:if test="//data/@show_price_min &gt; 0">
                            <xsl:attribute name="value">
                                <xsl:value-of select="//data/@show_price_min"/>
                            </xsl:attribute>
                        </xsl:if>
                    </input>
                    <label for="price_input_max">&#160;до&#160;</label>
                    <input type="text" id="price_input_max" name="price_max">
                        <xsl:if test="//data/@show_price_max &gt; 0">
                            <xsl:attribute name="value">
                                <xsl:value-of select="//data/@show_price_max"/>
                            </xsl:attribute>
                        </xsl:if>
                    </input>
                    &#160;<xsl:value-of select="//data/@cname"/>
                    <ul class="range clearfix">
                        <!--<xsl:apply-templates select="//price_line[position() > 1 and position() &lt; 5]"/>-->
                    </ul>
                    <div class="jquery_slider"></div>
                </div>
            </xsl:if>
            <h3>
                <a href="#">Производитель</a>
            </h3>
            <div class="fieldgroup">
                <xsl:if test="count(attr_brands[@selected = 1]) &gt; 0">
                    <xsl:attribute name="style">display: block;</xsl:attribute>
                </xsl:if>
                <xsl:apply-templates select="attr_brands"/>
            </div>
            <xsl:apply-templates select="attr_cat"/>
            <div class="applay_filters">
                <xsl:if test="count(attr_brands[@selected = 1]) &gt; 0 or count(//attr_value[@selected = 1]) &gt; 0">
                    <xsl:attribute name="style">display: block;</xsl:attribute>
                </xsl:if>
                <a title="Применить фильтры" class="product_button" href="javascript:void(0);">
                    <span>Применить фильтры</span>
                </a>
            </div>
        </form>
    </xsl:template>

    <xsl:template match="item">
        <div class="teaser clearfix" id="pitm_{@item_id}">

            <div class="img_box_align">
                <div style="margin: 0 auto; width: {image_middle/@w}px;">
                    <a href="{href}">
                        <!--Если картник нет - выводим заглушку -->
                        <xsl:choose>
                            <xsl:when test="image_middle/@src">
                                <img alt="{brand_name} {name}" src="/images/it/{image_middle/@src}"
                                     width="{image_middle/@w}" height="{image_middle/@h}"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <img alt="no image" src="/i/no-photo.jpg" width="200" height="87"/>
                            </xsl:otherwise>
                        </xsl:choose>

                        <xsl:if test="@has_discount=1">
                            <span class="personal_product_status gold">
                                <xsl:attribute name="style">background: url("/images/usr_disc/<xsl:value-of
                                        select="sh_disc_img_small/@src"/>") no-repeat scroll 0 0 transparent;
                                </xsl:attribute>
                                User discount
                            </span>
                        </xsl:if>
                        <xsl:if test="discount_image/@src!=''">
                            <span class="sale">
                                <img src="/images/disc/{discount_image/@src}" alt="" width="{discount_image/@w}"
                                     height="{discount_image/@h}"/>
                            </span>
                        </xsl:if>
                    </a>
                </div>
            </div>
            <div class="main_content">
                <h3>
                    <a href="{href}"><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/>
                    </a>
                </h3>
                <div class="info_box">
                    <span class="articul">Артикул товара:<xsl:value-of select="article"/>
                    </span>
                </div>
                <p>
                    <xsl:value-of select="short_description" disable-output-escaping="yes"/>
                </p>
                <form action="/" method="get" class="to_compare_form">
                    <label>
                        <input type="checkbox" name="compare" value="{@item_id}">
                            <xsl:if test="@in_compare=1">
                                <xsl:attribute name="checked">checked</xsl:attribute>
                            </xsl:if>
                        </input>
                        Добавить в сравнение
                    </label>
                </form>
                <xsl:choose>
                    <xsl:when test="@price1 &gt; 0">
                        <div class="price_box">
                            <span class="price old">
                                <xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of
                                    select="sname"/>
                            </span>
                            <!--|-->
                            <!--<span class="price_usd old"><xsl:value-of-->
                            <!--select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of-->
                            <!--select="nat_sname"/>-->
                            <!--</span>-->
                        </div>
                        <div class="price_box">
                            <span class="price personal">
                                <xsl:if test="@has_discount=1">
                                    <xsl:attribute name="style">background: url("/images/usr_disc/<xsl:value-of
                                            select="sh_disc_img_big/@src"/>") no-repeat scroll 0 0 transparent;
                                    </xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="format-number(@price1, '### ##0', 'european')"/>&#160;<xsl:value-of
                                    select="sname"/>
                            </span>
                            <!--| <xsl:value-of select="format-number(@real_price1, '### ##0', 'european')"/>&#160;<xsl:value-of-->
                            <!--select="nat_sname"/>-->
                        </div>
                    </xsl:when>
                    <xsl:otherwise>
                        <div class="price_box">
                            <span class="price"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of
                                    select="sname"/>
                            </span>
                            <!--| <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of-->
                            <!--select="nat_sname"/>-->
                        </div>
                    </xsl:otherwise>
                </xsl:choose>
                <a href="javascript:void(0);" xid="{@item_id}" class="product_button incard" title="Купить сейчас">
                    <span>Купить сейчас</span>
                </a>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="data">
        <script type="text/javascript" src="/js/selection/buildUrl.js"></script>
        <script type="text/javascript" src="/js/selection/objectValueSelection.js"></script>
        <script type="text/javascript" src="/js/selection/servicesSelection.js"></script>
        <script type="text/javascript" src="/js/jquery.typewatch.js"></script>
        <script type="text/javascript" src="/js/jquery.cookie.js"></script>
        <script type="text/javascript" src="/js/selection.js"></script>
        <script type="text/javascript" src="/js/selection_range.js"></script>


        <script type="text/javascript">
            var slide_prev_value = 0;
            var slide_prev_min_value = 0;
            var slide_prev_max_value = 0;
            var slider_min = 0;
            var slider_max = 0;

            <xsl:if test="//price_line[position()= 1]/price &gt; 0">
                slider_min = <xsl:value-of select="//price_line[position()= 1]/price"/>;
                objectValueSelection.price_min = slider_min;
            </xsl:if>
            <xsl:if test="//price_line[position()= 5]/price &gt; 0">
                slider_max = <xsl:value-of select="//price_line[position()= 5]/price"/>;
                objectValueSelection.price_max = slider_max;
            </xsl:if>

            <xsl:choose>
                <xsl:when test="@show_price_max &gt; 0">
                    var slide_values_min = <xsl:value-of select="@show_price_min"/>;
                    var slide_values_max = <xsl:value-of select="@show_price_max"/>;
                    objectValueSelection.price_min = slide_values_min;
                    objectValueSelection.price_max = slide_values_max;
                </xsl:when>
                <xsl:otherwise>
                    var slide_values_min = <xsl:value-of select="@min_price"/>;
                    var slide_values_max = <xsl:value-of select="@max_price"/>;
                    objectValueSelection.price_min = slide_values_min;
                    objectValueSelection.price_max = slide_values_max;
                </xsl:otherwise>
            </xsl:choose>

            var attr_range_view = new Array();
            var attr_range_view_start = new Array();

            var slide_coord = new Array();
            <xsl:apply-templates select="//price_line" mode="java_script"/>
        </script>

        <h1>
            <xsl:value-of select="docinfo/name"/>
        </h1>
        <div class="chapter_products content_block">
            <xsl:apply-templates select="item"/>
            <ul class="pager">
                <xsl:apply-templates select="section"/>
            </ul>
        </div>
        <xsl:if test="section/@page=1">
            <div id="catalog_text_block">
                <xsl:apply-templates select="docinfo/long_text"/>
            </div>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>