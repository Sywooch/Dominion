<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>

<xsl:template match="socials">
	<li>
		<a target="_blank" href="{url}" title="{name}">
			<img width="20" height="20" src="/images/social/{image/@src}" alt="{name}"/>
		</a>
	</li>
</xsl:template>

<xsl:template match="banner_social_likes">
	<li>
		<!--<xsl:apply-templates select="banner_code"/>-->
		<xsl:variable name="banner_code" select="banner_code"/>
		<xsl:value-of select="$banner_code" disable-output-escaping="yes"/>
	</li>
</xsl:template>

<xsl:template match="attr_shorts">
	<label>
		<input type="checkbox" value="{value}" name="attr_shorts[]" xid="{@attribut_id}" id="attr{@attribut_id}" class="attr_shorts" />
		<xsl:value-of select="name"/><xsl:if  test="type!=6">:&#160;<xsl:choose><xsl:when test="range_name!=''"><xsl:value-of select="range_name"/></xsl:when><xsl:otherwise><xsl:value-of select="value_view"/> <xsl:value-of select="unit_name"/></xsl:otherwise></xsl:choose></xsl:if>
	</label>
</xsl:template>

<xsl:template name="podbor">
	<xsl:if test="count(attr_shorts) &gt; 0 and count(//page/similar_items) &gt; 0">
		<div id="same_products">
			<h2>Похожие товары</h2>
			<h3><a href="#" class="expanded">Быстрый подбор похожих товаров</a></h3>
			<form action="{//data/@category_path}" method="post" id="same_products_form">
				<input type="hidden" name="item_id" id="item_id" value="{//data/@id}"/>
				<input type="hidden" name="category_path" id="category_path" value="{//data/@category_path}"/>
				<xsl:apply-templates select="attr_shorts"/>
				<div class="applay_filters" style="clear: both;">
					<a title="Показать все" class="product_button" href="javascript:void(0);">
						<span>Показать все</span>
					</a>
				</div>
			</form>
			<div id="same_products_div">
			</div>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template name="headers">
<script type="text/javascript" src="/js/jquery.jcarousel.js"/>
<script type="text/javascript" src="/js/attributs.js"/>
<script type="text/javascript" src="http://js.testfreaks.com/badge/7560000.com.ua/head.js"/>
    <script>
        (function(d,t,p){
        var e = d.createElement(t); e.charset = "utf-8"; e.src = p;
        var s = d.getElementsByTagName(t)[0]; s.parentNode.insertBefore(e,s)
        })(document,"script","http://js.testfreaks.com/onpage/7560000.com.ua/prd.js")
    </script>
</xsl:template>

<xsl:template match="attributes">
	<xsl:choose>
		<xsl:when test="type='brand'">
			<dt class="odd"><xsl:value-of select="name"/></dt>
			<dd class="odd"><a href="{value}" target="_blank"><xsl:value-of select="value"/></a></dd>
		</xsl:when>
		<xsl:otherwise>
			<dt>
				<xsl:if test="position() mod 2=1">
					<xsl:attribute name="class">odd</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="name"/></dt>
			<dd>
				<xsl:if test="position() mod 2=1">
					<xsl:attribute name="class">odd</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="value"/>&#160;<xsl:value-of select="unit_name"/></dd>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="view_attribut_group">
	<h3><xsl:value-of select="name"/></h3>
	<dl class="product_features">
		<xsl:apply-templates select="attributes"/>
	</dl>
</xsl:template>

<xsl:template match="similar_items">
	<li>
		<div class="tovar" id="pitm_{@item_id}">
			<h2><a href="/item/{@item_id}/{urname}/"><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/></a></h2>
			<div class="img"><a href="/item/{@item_id}/{urname}/"><img src="/images/it/{image/@src}" alt="" width="{image/@w}" height="{image/@h}"/></a>
				<xsl:if test="discount_image/@src!=''">
					<span class="sale"><img src="/images/disc/{discount_image/@src}" alt="" width="{discount_image/@w}" height="{discount_image/@h}" /></span>
				</xsl:if>
			</div>
			<div class="by"> <span class="price"><xsl:value-of select="@price"/></span><xsl:choose><xsl:when test="@in_cart=1"><a id="{@item_id}" class="incard2" title="уже в корзине"><xsl:value-of select="currency"/></a></xsl:when><xsl:otherwise><a href="javascript:void(0);" id="{@item_id}" class="incard"><xsl:value-of select="currency"/></a></xsl:otherwise></xsl:choose><xsl:choose><xsl:when test="@in_compare=1"><span class="srv" title="уже в сравнении">CpaBH|/|Tb</span></xsl:when><xsl:otherwise><span id="citm_{@item_id}"><a href="javascript:void(0);" onclick="compare('{@item_id}',0,0)" class="srv">CpaBH|/|Tb</a></span></xsl:otherwise></xsl:choose></div>
		</div>
	</li>
</xsl:template>

<xsl:template match="item_photo">
    <li>
		<a href="/images/it/{img_big/@src}" class="gr_fancybox" rel="gr_fancybox" title="{//item/typename} {//item/brand_name} {//item/name}"><img src="/images/it/{img_small/@src}" width="{img_small/@w}" height="{img_small/@h}" alt="{//item/typename} {//item/brand_name} {//item/name}" /></a>
	</li>
</xsl:template>

<xsl:template match="item_photo" mode="withoutcar">
    <li class="jcarousel-item jcarousel-item-horizontal">
		<a href="/images/it/{img_big/@src}" class="gr_fancybox" rel="gr_fancybox" title="{//item/typename} {//item/brand_name} {//item/name}"><img src="/images/it/{img_small/@src}" width="{img_small/@w}" height="{img_small/@h}" alt="{//item/typename} {//item/brand_name} {//item/name}" /></a>
	</li>
</xsl:template>

<xsl:template match="item_item">
	<div class="teaser" id="pitm_{@item_id}">
		<a href="{href}">
			<img alt="{brand_name} {name}" src="/images/it/{image_small/@src}" width="{image_small/@w}" height="{image_small/@h}"/>
		</a>
		<h3><a href="{href}"><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/></a></h3>
		<form action="/" method="get" class="to_compare_form">
			<label>
				<input type="checkbox" name="compare" value="{@item_id}">
					<xsl:if test="@in_compare=1">
						<xsl:attribute name="checked">checked</xsl:attribute>
					</xsl:if>
				</input>
				Добавить в сравнение</label>
		</form>
		<xsl:choose>
			<xsl:when test="@price1 &gt; 0">
				<span class="price"><xsl:value-of select="format-number(@price1, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span> | <xsl:value-of select="format-number(@real_price1, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
				<xsl:value-of select="format-number(@price1, '### ##0', 'european')"/>
			</xsl:when>
			<xsl:otherwise>
				<span class="price"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span> | <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
			</xsl:otherwise>
		</xsl:choose>
		<a href="javascript:void(0);" xid="{@item_id}" class="product_button incard" title="Купить сейчас"><span>Купить сейчас</span></a>
	</div>
</xsl:template>

<xsl:template match="catalog_path">
    <li><a href="/cat/{@id}/"><xsl:value-of select="name"/></a></li>
</xsl:template>

<xsl:variable name="fancybox"><![CDATA[
<script type='text/javascript'>
	$(document).ready(function() {	
		$("a.gr_fancybox").fancybox();
	});
</script>]]>
</xsl:variable>

<xsl:variable name="fancybox_jcarousel"><![CDATA[
<script type='text/javascript'>
	$(document).ready(function() {	
		$('.gallery ul').jcarousel({
			scroll: 3
		});
	});
</script>]]>
</xsl:variable>

<xsl:template match="item_media">
	<xsl:choose>
		<xsl:when test="media_file/@src!=''">
			<object type="application/x-shockwave-flash" data="/i/uppod.swf" width="500" height="375">
				<param name="bgcolor" value="#ffffff" />
				<param name="allowFullScreen" value="true" />
				<param name="allowScriptAccess" value="always" />
				<param name="wmode" value="transparent" />
				<param name="movie" value="http://site.ru/uppod.swf" />
				<param name="flashvars" value="st=/css/video.txt&amp;file=/images/it/{media_file/@src}" />
			</object>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="media_code" disable-output-escaping="yes"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="comments">
	<div class="comment_block">
		<div class="comment_header">
			<h3><xsl:value-of select="name"/></h3>
			<span class="date"><xsl:value-of select="date"/></span>
		</div>
		<p><xsl:value-of select="description"/></p>
	</div>
</xsl:template>

<xsl:template match="tabs">
	<li id="tab{pos}">
		<xsl:attribute name="section"><xsl:value-of select="name"/></xsl:attribute>
		<xsl:if test="sel=1">
			<xsl:attribute name="class">active</xsl:attribute>
		</xsl:if>
		<a>
			<xsl:choose>
				<xsl:when test="pos=1"><xsl:attribute name="href"><xsl:value-of select="//item/href"/></xsl:attribute></xsl:when>
				<xsl:otherwise><xsl:attribute name="href"><xsl:value-of select="//item/href"/><xsl:value-of select="name"/>/</xsl:attribute></xsl:otherwise>
			</xsl:choose>
			<xsl:value-of select="value"/>
		</a>
	</li>
</xsl:template>

<xsl:template match="tabs" mode="description">
   <div>
	   <xsl:choose>
			<xsl:when test="@section='description'"><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/> active</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/></xsl:attribute></xsl:otherwise>
		</xsl:choose>
		<div class="text">
			<p><xsl:apply-templates select="../long_text"/></p>
		</div>
	</div>
</xsl:template>

<xsl:template match="tabs" mode="charcter">
   <div>
	   <xsl:choose>
			<xsl:when test="@section='characteristics'"><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/> active</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/></xsl:attribute></xsl:otherwise>
		</xsl:choose>
		<!--<xsl:if test="count(view_attribut_group) &gt; 0">-->
			<xsl:variable name="view_attribut_group_count" select="count(//view_attribut_group) div 2"/>
			<dl class="hka">
				<xsl:apply-templates select="//view_attribut_group[position() &lt;= $view_attribut_group_count]"/>
			</dl>
			<dl class="hka">
				<xsl:apply-templates select="//item/view_attribut_group[position() &gt; $view_attribut_group_count]"/>
			</dl>
		<!--</xsl:if>-->
	</div>
</xsl:template>

<xsl:template match="tabs" mode="video">
   <div>
	   <xsl:choose>
			<xsl:when test="@section='video'"><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/> active</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/></xsl:attribute></xsl:otherwise>
		</xsl:choose>
		<xsl:apply-templates select="../item_media"/>
	</div>
</xsl:template>

<xsl:template match="tabs" mode="items">
   <div>
		<xsl:choose>
			<xsl:when test="@section='items'"><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/> active</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/></xsl:attribute></xsl:otherwise>
		</xsl:choose>
		<div id="same_products_content" class="clearfix">
			<xsl:apply-templates select="../item_item"/>
		</div>
	</div>
</xsl:template>

<xsl:variable name="validate"><![CDATA[
<script type="text/javascript">
$().ready(function() { 
	$("#comment_form").validate({
		errorLabelContainer: $("#comment_form div.errhold"),
		submitHandler: function(form) {
			data = $(form).serialize();
			var utl = $(form).attr('action');
			$.getJSON(utl, data, function(data){
				if(data.res==1){
					$('#comment_view').prepend(data.html_code);
					$('span.response_count').text(data.count);
					$('li[section=comments]').find('a').text('Отзывы ('+data.count+')');					
				}
			});
			
			form.reset();
		},
		rules: {
			captcha: {
				required: true,
				remote: "/ajax/caphainp/"
			},
			comment_name: {
				required: true,
				minlength: 2
			},
			comment_comment: {
				required: true,
				minlength: 2
			}
		},
		messages: {
			comment_name: "Поле Имя пустое",
			comment_comment: "Поле Сообщение пустое",
			captcha: "Укажиет правильные символы на картинке."
		},
		onkeyup: false
	});
});
</script>]]>
</xsl:variable>

<xsl:template match="tabs" mode="comments">
   <div>
	    <xsl:choose>
			<xsl:when test="@section='comments'"><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/> active</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/></xsl:attribute></xsl:otherwise>
		</xsl:choose>
			<xsl:value-of select="$validate" disable-output-escaping="yes"/>
			<form method="get" action="/ajax/comments/item_id/{//item/@item_id}/" class="text_block_form" id="comment_form">
				<div class="errhold"></div>
				<div>
					<label for="comment_name">Ваше имя:<span class="asterisk">*</span></label>
					<input id="comment_name" name="comment_name" type="text"/>
				</div>
				<div>
					<label for="comment_comment">Ваш отзыв:<span class="asterisk">*</span></label>
					<textarea id="comment_comment" name="comment_comment"></textarea>
				</div>
				<div class="captcha">
					<div class="clearfix">
						<label for="captcha_input">Впишите число:</label>
						<span id="capcha"></span>
						<input id="captcha" name="captcha" type="text"/>
						<script>reload();</script>
					</div>
					<a href="#" class="captcha_refresh_button" onclick="reload(); return false;">Обновить</a>
				</div>
				<input type="submit" value="Отправить"/>
			</form>

			<div class="content_block comments">
				<h3>Комментарии <span class="quantity">(<span class="response_count"><xsl:value-of select="count(../comments)"/></span>)</span></h3>
				<div id="comment_view">
					<xsl:apply-templates select="../comments"/>
				</div>
			</div>
	</div>
</xsl:template>

<xsl:template match="item">
	<xsl:value-of select="$fancybox" disable-output-escaping="yes"/>

	<h1><xsl:if test="typename!=''"><xsl:value-of select="typename"/>&#160;</xsl:if><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/></h1>

	<div id="product_block" class="clearfix">
		<div id="product_photos">
			<div id="product_main_photo">
				<xsl:choose>
					<xsl:when test="image_big/@src!=''">
						<a href="/images/it/{image_big/@src}" class="gr_fancybox main" rel="gr_fancybox" title="{typename} {brand_name} {name}">


                            <!--Если картник нет - выводим заглушку -->
                            <xsl:choose>
                                <xsl:when test="image_middle/@src">
                                    <img alt="{brand_name} {name}" src="/images/it/{image_middle/@src}" width="{image_middle/@w}" height="{image_middle/@h}"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <img alt="no image" src="/i/b_no-photo.jpg" width="200" height="200"/>
                                </xsl:otherwise>
                            </xsl:choose>




							<xsl:if test="@has_discount=1">
								<span class="personal_product_status gold">
									<xsl:attribute name="style">background: url("/images/usr_disc/<xsl:value-of select="sh_disc_img_small/@src"/>") no-repeat scroll 0 0 transparent;</xsl:attribute>
									User discount</span>
							</xsl:if>
							<xsl:if test="discount_image/@src!=''">
								<span class="sale"><img src="/images/disc/{discount_image/@src}" alt="" width="{discount_image/@w}" height="{discount_image/@h}" /></span>
							</xsl:if>
						</a>
						<a href="#" class="zoom" title = "Увеличить">Увеличить</a>
					</xsl:when>
					<xsl:otherwise>


                        <!--Если картник нет - выводим заглушку -->
                        <xsl:choose>
                            <xsl:when test="image_middle/@src">
                                <img alt="{brand_name} {name}" src="/images/it/{image_middle/@src}" width="{image_middle/@w}" height="{image_middle/@h}"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <img alt="no image" src="/i/b_no-photo.jpg" width="200" height="200"/>
                            </xsl:otherwise>
                        </xsl:choose>


                        <!--<img src="/images/it/{image_middle/@src}" alt="{typename} {brand_name} {name}" />-->
                    </xsl:otherwise>
				</xsl:choose>
			</div>
			<div id="product_photos_slider">
				<xsl:choose>
					<xsl:when test="count(item_photo) &gt; 3">
						<xsl:value-of select="$fancybox_jcarousel" disable-output-escaping="yes"/>
						<div class="gallery  jcarousel-skin">
							<ul class="menu clearfix">
								<xsl:apply-templates select="item_photo"/>
							</ul>
						</div>
					</xsl:when>
					<xsl:when test="count(item_photo) &lt; 4">
						<div class="gallery  jcarousel-skin">
							<div class="jcarousel-container jcarousel-container-horizontal">
								<div class="jcarousel-clip jcarousel-clip-horizontal">
									<ul class="menu clearfix jcarousel-list jcarousel-list-horizontal">
										<xsl:apply-templates select="item_photo" mode="withoutcar"/>
									</ul>
								</div>
							</div>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
		</div>

		<div id="product_info_left_column">
			<div id="poduct_price_block" class="clearfix">

				<xsl:choose>
					<xsl:when test="@price &gt; 0 and @active=1">
						<xsl:choose>
							<xsl:when test="@price1 &gt; 0">
								<span class="price old"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/> </span> | <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
								<div id="product_personal_price">
									Ваша цена:
									<br/>
									<span class="price personal">
										<xsl:if test="@has_discount=1">
											<xsl:attribute name="style">background: url("/images/usr_disc/<xsl:value-of select="sh_disc_img_big/@src"/>") no-repeat scroll 0 0 transparent;</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="format-number(@price1, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span> | <xsl:value-of select="format-number(@real_price1, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
								</div>
							</xsl:when>
							<xsl:otherwise>
								<div class="price_box">
									<span class="price"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span> | <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
								</div>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
						<span class="price">нет в наличии</span>
					</xsl:otherwise>
				</xsl:choose>

			</div>
			<div class="info_box">
				<span class="articul">Артикул товара:<xsl:value-of select="article"/></span>
			</div>
			<div class="info_box">
				<form action="/" method="get" class="to_compare_form">
					<label>
						<input type="checkbox" name="compare" value="{@item_id}">
							<xsl:if test="@in_compare=1">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
						</input>
						Добавить в сравнение</label>
				</form>
			</div>
			<div class="info_box online">
				<xsl:if test="//banner_item_live_help/description!=''">
					<xsl:apply-templates select="//banner_item_live_help/description"/>
				</xsl:if>
			</div>
		</div>

		<div id="product_button_block" class="clearfix">
			<xsl:choose>
				<xsl:when test="@price=0 or @active=0">
					<a href="/item/reserve/id/{@item_id}/" id="{@item_id}" class="product_button noitem_reserve"><span>Сообщить о наличии</span></a>
				</xsl:when>
				<xsl:otherwise>
					<a href="javascript:void(0);" xid="{@item_id}" class="product_button incard" title="Купить сейчас"><span>Купить сейчас</span></a>
					<xsl:if test="//banner_item_pay/description!=''">
						<div class="payvar">
							<xsl:apply-templates select="//banner_item_pay/description"/>
						</div>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="@price &gt; 0 and @active=1">
				<xsl:if test="credit_description!=''">
				  <a class="popup pseudo orange" href="/calculator/get/item_id/{@item_id}/" width="528px" offset="250"><span>Купить в кредит</span></a>
				  </xsl:if>
			</xsl:if>
		</div>
		<div id="product_info_right_column">
			<xsl:if test="warranty_description!=''">
				<div class="info_box">
					<a href="/ajax/popup/mode/warranty/id/{@warranty_id}/" class="product_block_icon warranty popup">Гарантия</a>
					<a href="/ajax/popup/mode/warranty/id/{@warranty_id}/" class="pseudo popup" width="370px" offset="200"><span>Гарантия</span></a>
					<a href="/ajax/popup/mode/warranty/id/{@warranty_id}/" class="what_is_it popup">What is it?</a>
					<div class="info">
						<xsl:apply-templates select="warranty_description"/>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="delivery_description!=''">
				<div class="info_box">
					<a href="/ajax/popup/mode/delivery/id/{@delivery_id}/" class="product_block_icon delivery popup">Доставка</a>
					<a href="/ajax/popup/mode/delivery/id/{@delivery_id}/" class="pseudo popup" width="370px" offset="200"><span>Доставка</span></a>
					<a href="/ajax/popup/mode/delivery/id/{@delivery_id}/" class="what_is_it popup">What is it?</a>
					<div class="info">
						<xsl:apply-templates select="delivery_description"/>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="credit_description!=''">
				<div class="info_box">
					<a href="/ajax/popup/mode/credit/id/{@credit_id}/" class="product_block_icon credit popup">Кредит</a>
					<a href="/ajax/popup/mode/credit/id/{@credit_id}/" class="pseudo popup" width="370px" offset="200"><span>Кредит</span></a>
					<a href="/ajax/popup/mode/credit/id/{@credit_id}/" class="what_is_it popup">What is it?</a>
					<div class="info">
						<xsl:apply-templates select="credit_description"/>
					</div>
				</div>
			</xsl:if>
		</div>

		<div class="social_block">
			<xsl:if test="count(//data/socials) &gt; 0">
				<ul class="socset">
					<li>Рассказать друзьям:&#160;</li>
					<xsl:apply-templates select="//data/socials"/>
				</ul>
			</xsl:if>
			<xsl:if test="count(//banner_social_likes) &gt; 0">
				<ul class="socset">
					<xsl:apply-templates select="//banner_social_likes"/>
				</ul>

			</xsl:if>

		</div>
        <div style="float:right">
            <a id="tfw-badge" href="http://www.testfreaks.com.ua">TestFreaks</a>
        </div>

	</div>
	<div class="content_block">
		<ul class="tabs content">
			<xsl:apply-templates select="tabs"/>
		</ul>
		<div id="tab_content">
			<xsl:apply-templates select="tabs[name='description']" mode="description"/>
			<xsl:apply-templates select="tabs[name='characteristics']" mode="charcter"/>
			<xsl:apply-templates select="tabs[name='video']" mode="video"/>
			<xsl:apply-templates select="tabs[name='items']" mode="items"/>
			<!--<xsl:apply-templates select="tabs[name='comments']" mode="comments"/>-->
		</div>
	</div>
</xsl:template>

<xsl:template match="data">
	<xsl:apply-templates select="item"/>
</xsl:template>

</xsl:stylesheet>