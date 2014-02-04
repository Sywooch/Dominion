<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:import href="_main.xsl"/>

    <xsl:variable name="fancybox"><![CDATA[
<script type='text/javascript'>
	$(document).ready(function() {
		$("a.gr_fancybox, a.zoom").fancybox({
			'transitionIn': 'none',
			'transitionOut': 'none',
			'titlePosition': 'over',
			'titleFormat': function(title, currentArray, currentIndex, currentOpts) {
				return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &#160; ' + title : '') + '</span>';
			}
		});
	});
</script>]]>
    </xsl:variable>

    <xsl:variable name="validate"><![CDATA[
<script type="text/javascript">
$().ready(function() {
	$("#cart_order_form").validate({
		errorLabelContainer: $("#cart_order_form div.errhold"),
		rules: {
			email: {
			  minlength: 2,
			  email: true
			},
			name: {
				required: true,
				minlength: 2
			},
			address: {
				required: true,
				minlength: 2
			},
			telmob: {
				required: true,
				minlength: 2
			}
		},
		messages: {
			name: "Поле Имя пустое",
			address: "Поле Адрес пустое",
			email: "Поле E-mail пустое или не валидно",
			telmob: "Поле Телефон пустое"
		},
		onkeyup: false
	});
});
</script>]]>
    </xsl:variable>

    <!-- Группы товаров-->
    <xsl:template match="item">
        <div class="teaser clearfix" id="item_row{@id}">

            <div class="img_box_align">
                <div style="margin: 0 auto; width: {image_middle/@w}px;">
                    <a href="{href}">
                        <img alt="{brand_name} {name}" src="/images/it/{image_middle/@src}" width="{image_middle/@w}"
                             height="{image_middle/@h}"/>
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


            <h3>
                <a href="{href}"><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/>
                </a>
            </h3>
            <p>
                <xsl:value-of select="short_description" disable-output-escaping="yes"/>
            </p>
            <form action="/" method="post" class="quantity_form">
                <label for="quantity_input_1">Количество:&#160;</label>
                <input type="text" name="count[{@id}]" value="{@count}" class="count" id="count{@id}" xid="{@id}"/>
                &#160;шт
            </form>
            <xsl:choose>
                <xsl:when test="@price1 &gt; 0">
                    <div class="price_box">
                        <span class="price old"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of
                                select="sname"/>
                        </span>
                        <!--| <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of-->
                            <!--select="nat_sname"/>-->
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
            <a href="#" class="delete_icon" xid="{@id}">Delete</a>
        </div>

    </xsl:template>
    <!-- Группы товаров -->


    <xsl:template match="payment">
        <option value="{@id}">
            <xsl:if test="@id=/page/payment_err">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="name"/>
        </option>
    </xsl:template>

    <xsl:template match="data">
        <link rel="stylesheet" type="text/css" href="/css/fancybox.css"/>
        <script type='text/javascript' src='/js/fancybox.js'></script>
        <xsl:value-of select="$fancybox" disable-output-escaping="yes"/>

        <script type='text/javascript' src='/js/number_format.js'></script>
        <script type='text/javascript' src='/js/cart.js'></script>

        <h1>
            <xsl:apply-templates select="docinfo/name"/>
        </h1>
        <xsl:choose>
            <xsl:when test="count(item) &gt; 0">
                <div id="content_cart" class="chapter_products content_block">
                    <xsl:apply-templates select="item"/>

                    <div id="cart_total" class="clearfix">
                        <span>Всего к оплате
                            <span class="price">
                                <xsl:value-of select="format-number(total_summ, '### ##0', 'european')"/>
                            </span>
                            &#160;<xsl:value-of select="sname"/>
                        </span>
                        <div>
                            <a class="product_button" href="#">
                                <span>
                                    <span>Оформить заказ</span>
                                </span>
                            </a>
                            <!--<a class="button_link" href="#">Пересчитать</a>-->
                        </div>
                    </div>
                </div>
                <div id="cart_text_block" class="clearfix content_block">
                    <div class="left">
                        <xsl:apply-templates select="docinfo/txt"/>
                    </div>
                    <div class="right">
                        <form method="post" action="/cart/order/" id="cart_order_form" class="text_block_form">
                            <xsl:value-of select="$validate" disable-output-escaping="yes"/>
                            <div class="errhold"></div>
                            <p>
                                <i>Все поля, отмеченные<span class="asterisk">*</span>, обязательны для заполнения
                                </i>
                            </p>
                            <table>
                                <tbody>
                                    <!--<tr>
                                        <td>
                                            <label for="name">Способ оплаты<span class="asterisk">*</span></label>
                                            <select name="payment">
                                                <xsl:apply-templates select="payment"/>
                                            </select>
                                        </td>
                                    </tr>-->
                                    <tr>
                                        <td>
                                            <label for="name">Имя
                                                <span class="asterisk">*</span>
                                            </label>
                                            <input id="name" name="name" type="text" value="{//user_data/user_name}"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="telmob">Телефон:
                                                <span class="asterisk">*</span>
                                            </label>
                                            <input id="telmob" name="telmob" type="text"
                                                   value="{//user_data/user_phone}"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="email">E-mail:</label>
                                            <input id="email" name="email" type="email"
                                                   value="{//user_data/user_email}"/>
                                        </td>
                                    </tr>

                                    <!--<tr>
                                        <td>
                                            <label for="address">Адрес доставки:<span class="asterisk">*</span></label>
                                            <textarea id="address" name="address"></textarea>
                                        </td>
                                    </tr>-->
                                    <tr>
                                        <td>
                                            <label for="more_information">Дополнительная информация (адрес доставки,
                                                Ваши вопросы)
                                            </label>
                                            <textarea id="more_information" name="info"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <a class="product_button" href="#" id="order_button">
                                <span>
                                    <span>Подтвердить заказ</span>
                                </span>
                            </a>
                        </form>
                    </div>
                </div>
            </xsl:when>
            <xsl:otherwise>
                <div id="content_cart" class="empty">
                    <span>Ваша корзина пуста :(</span>
                </div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>