<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>


<xsl:template name="headers">
<script type="text/javascript" src="/js/jquery.jcarousel.js"/>
</xsl:template>

<xsl:template match="attributes">
	<xsl:choose>
		<xsl:when test="type='brand'">
			<dd>
				<xsl:value-of select="name"/>:&#160;<em><a href="{value}" target="_blank"><xsl:value-of select="value"/></a></em>
			</dd>
		</xsl:when>
		<xsl:otherwise>
			<dd>
				<xsl:value-of select="name"/>:<xsl:if  test="type!=6">&#160;</xsl:if><em><xsl:value-of select="value"/>&#160;<xsl:value-of select="unit_name"/></em>
			</dd>
		</xsl:otherwise>
	</xsl:choose>
	
</xsl:template>

<xsl:template match="view_attribut_group">
	<dt><xsl:value-of select="name"/></dt>
	<xsl:apply-templates select="attributes"/>
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

<xsl:template match="attr_shorts">    
    <li id="attr{@attribut_id}"><a href="/item/shortattribmode/itm/{//data/@id}/at/{@attribut_id}/val/{value}/"><xsl:value-of select="name"/><xsl:if  test="type!=6">:&#160;<xsl:choose><xsl:when test="range_name!=''"><xsl:value-of select="range_name"/></xsl:when><xsl:otherwise><xsl:value-of select="value_view"/> <xsl:value-of select="unit_name"/></xsl:otherwise></xsl:choose></xsl:if></a></li>
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
	<div class="tovar" id="pitm_{@item_id}">
		<h2><a href="/item/{@item_id}/{urname}/"><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/></a></h2>
		<div class="img"><a href="/item/{@item_id}/{urname}/"><img alt="" src="/images/it/{image/@src}"/></a>
			<xsl:if test="discount_image/@src!=''">
				<span class="sale"><img src="/images/disc/{discount_image/@src}" alt="" width="{discount_image/@w}" height="{discount_image/@h}" /></span> 
			</xsl:if>
		 </div>
		<p class="op"><xsl:value-of  select="description"  disable-output-escaping="yes"/></p>
		<div class="by">
			<xsl:choose>
				<xsl:when test="@price1 &gt; 0">
					<span class="price"><em><xsl:value-of select="@price1"/></em><span class="val"><xsl:value-of select="currency"/></span></span>
				</xsl:when>
				<xsl:otherwise>
					<span class="price"><em><xsl:value-of select="@price"/></em><span class="val"><xsl:value-of select="currency"/></span></span>
				</xsl:otherwise>					
			</xsl:choose>
			<a href="javascript:void(0);" id="{@item_id}" class="incard" title="добавить в корзину"><xsl:value-of select="currency"/></a>
<!--		<xsl:choose><xsl:when test="@in_cart=1"><a class="incard2" title="уже в корзине"></a></xsl:when><xsl:otherwise><a href="javascript:void(0);" id="{@item_id}" class="incard" title="добавить в корзину"><xsl:value-of select="currency"/></a></xsl:otherwise></xsl:choose>-->
			<xsl:choose><xsl:when test="@in_compare=1"><span id="citm_{@item_id}"><a title="Уже в сравнении" class="remove_from_compare" href="javascript:void(0);">Уже в сравнении</a></span></xsl:when><xsl:otherwise><span id="citm_{@item_id}"><a href="javascript:void(0);" xid="{@item_id}" class="add_to_compare" title="Добавить в сравнение">Добавить в сравнение</a></span></xsl:otherwise></xsl:choose>
		</div>
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
	<div class="comments">
		<div class="about">
			<span class="date"><xsl:value-of select="date"/></span>
			<strong class="name"><xsl:value-of select="name"/></strong>
		</div>
		<div class="comment-holder">
			<div class="comment">
				<p><xsl:value-of select="description"/></p>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="tabs">
	<li id="tab{pos}">
		<xsl:attribute name="section"><xsl:value-of select="name"/></xsl:attribute>
		<xsl:if test="//item/@section=name"><xsl:attribute name="class">active</xsl:attribute></xsl:if>
		<a>
			<xsl:choose>
				<xsl:when test="pos=1"><xsl:attribute name="href"><xsl:value-of select="//item/@item_url"/></xsl:attribute></xsl:when>			
				<xsl:otherwise><xsl:attribute name="href"><xsl:value-of select="//item/@item_url"/>#tab=<xsl:value-of select="name"/></xsl:attribute></xsl:otherwise>
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
			<p><xsl:apply-templates select="../txt"/></p>
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
		<div class="catalog">
			<xsl:apply-templates select="../item_item"/>
		</div>
	</div>
</xsl:template>

<xsl:template match="tabs" mode="comments">
   <div>
	    <xsl:choose>
			<xsl:when test="@section='comments'"><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/> active</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="class"> tab_content tab<xsl:value-of select="pos"/></xsl:attribute></xsl:otherwise>
		</xsl:choose>
			<xsl:apply-templates select="../comments"/>
			<form action="/item/comments/itm/{//item/@item_id}/" method="post" id="ajaxlink">
				<div class="add-comment">
					<div class="clean">
					  <label for="name">имя</label>
					  <input type="text" id="name" name="name"/>
					</div>
					<div class="clean">
						<label for="text">мнение</label>
						<div class="text-comment-holder">
							<div class="text-comment-holder1"><textarea id="text" rows="10" cols="100" name="comment_name"></textarea></div>
						</div>
					</div>
				<table>
					<tr>
						<td><label for="capcha">впишите число</label></td>
						<td class="capcha"><span id="dle-captcha"><script type="text/javascript">reload();</script></span></td>
						<td><input type="text" id="capcha" maxlength="6" name="capcha"/></td>
						<td><input type="image" src="/i/send.png" id="addcomment"/></td>
					</tr>
					<tr>
						<td></td>
						<td class="reloadcapcha"><a onclick="reload(); return false;" href="javascript:void(0);">обновить</a></td>
						<td><span id="err"></span></td>
						<td></td>
					</tr>
				</table>
			  </div>
			</form>
	</div>
</xsl:template>

<xsl:template match="item">
	<h1><xsl:if test="typename!=''"><xsl:value-of select="typename"/>&#160;</xsl:if><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/></h1>
	
	<div class="imghold">
		<link rel="stylesheet" type="text/css" href="/css/fancybox.css" />
		<script type='text/javascript' src='/js/fancybox.js'></script>
		<xsl:value-of select="$fancybox" disable-output-escaping="yes"/>
		<div class="imgs">
			<xsl:choose>
				<xsl:when test="image_big/@src!=''"><a href="/images/it/{image_big/@src}" class="gr_fancybox main" rel="gr_fancybox" title="{typename} {brand_name} {name}"><img src="/images/it/{image_middle/@src}" alt="{typename} {brand_name} {name}" /></a><a href="#" class="zoom" title = "Увеличить">Увеличить</a></xsl:when>
				<xsl:otherwise><img src="/images/it/{image_middle/@src}" alt="" /></xsl:otherwise>
			</xsl:choose>				
			<xsl:if test="discount_image/@src!=''"><span class="sale"><img src="/images/disc/{discount_image/@src}" alt="" width="{discount_image/@w}" height="{discount_image/@h}" /></span> </xsl:if>
		</div>
		<xsl:choose>
			<xsl:when test="count(item_photo) &gt; 3">
				<xsl:value-of select="$fancybox_jcarousel" disable-output-escaping="yes"/>
				<div class="gallery  jcarousel-skin">
					<ul>
						<xsl:apply-templates select="item_photo"/>							
					</ul>
				</div>
			</xsl:when>
			<xsl:when test="count(item_photo) &lt; 4">						
				<div class="gallery  jcarousel-skin">
					<div class="jcarousel-container jcarousel-container-horizontal">
						<div class="jcarousel-clip jcarousel-clip-horizontal">
							<ul class="jcarousel-list jcarousel-list-horizontal">
								<xsl:apply-templates select="item_photo" mode="withoutcar"/>
							</ul>
						</div>
					</div>
				</div>
			</xsl:when>
		</xsl:choose>
	</div>
	<div class="descr-holder" id="pitm_{@item_id}">
		<div class="descr">
			<div class="by_block">
			
				<xsl:choose>
					<xsl:when test="@price=0 or @status=0">
						<div class="noitem">нет в наличии</div>
						<a href="/item/reserve/id/{@item_id}/" id="{@item_id}" class="noitem_reserve">Оставить заявку</a>				
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="@price1 &gt; 0">
								<span class="price" id="item_price"><em><xsl:value-of select="@price1"/></em><span class="val"><xsl:value-of select="currency"/></span></span>
								<span class="old_price" id="item_old_price"><em><xsl:value-of select="@price"/></em><span class="val"><xsl:value-of select="currency"/></span></span>
							</xsl:when>
							<xsl:otherwise>
								<span class="price" id="item_price"><em><xsl:value-of select="@price"/></em><span class="val"><xsl:value-of select="currency"/></span></span>
							</xsl:otherwise>					
						</xsl:choose>				
						<a href="javascript:void(0);" id="{@item_id}" class="incard itemcart">Добавить в корзину</a>				
						<p class="after_by"><xsl:if test="@in_cart_count &gt; 0">В корзину добавлено <xsl:value-of select="@in_cart_count"/> товаров<br /><a href="/cart/all/">Оформить заказ</a></xsl:if> </p>
						<xsl:if test="credit_description!=''"><a class="by_credit popup" href="/ajax/popup/mode/credit/id/{@credit_id}/">Купить в кредит</a></xsl:if>
						<xsl:if test="//banner_item_pay/description!=''">
							<div class="payvar">
								<xsl:apply-templates select="//banner_item_pay/description"/>
							</div>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
								
			</div>
			<p class="article">Артикул товара: <b><xsl:value-of  select="article"/></b></p>
			<p class="abouttov"><xsl:value-of  select="description"  disable-output-escaping="yes"/></p>
			<div class="buttonhold">
				<ul class="techinfo">
					<li>
						<p class="head" style="background:url(/i/ico1.png) no-repeat">
							<xsl:choose><xsl:when test="@in_compare=1"><span id="citm_{@item_id}"><a href="javascript:void(0);" class="remove_from_compare" title="Добавить в сравнение">Уже в сравнение</a></span></xsl:when><xsl:otherwise><span id="citm_{@item_id}"><a href="javascript:void(0);" xid="{@item_id}" class="add_to_compare" title="Добавить в сравнение">Добавить в сравнение</a></span></xsl:otherwise></xsl:choose>
						</p>
					</li>
					<!--<li>
						<p class="head" style="background:url(/i/ico3.png) no-repeat">
							<a href="#">Инструкции</a>
						</p>
					</li>-->
					<xsl:if test="//banner_item_live_help/description!=''">
						<li>
							<xsl:apply-templates select="//banner_item_live_help/description"/>
						</li>
					</xsl:if>					
				</ul>
				<xsl:if test="warranty_description!='' or delivery_description!='' or credit_description!=''">
					<ul class="byinfo">
						<xsl:if test="warranty_description!=''">
							<li>
								<p class="head" style="background:url(/i/ico5.png) no-repeat">
									<a class="popup" href="/ajax/popup/mode/warranty/id/{@warranty_id}/">Гарантия</a>
								</p>
								<xsl:apply-templates select="warranty_description"/>
							</li>
						</xsl:if>
						<xsl:if test="delivery_description!=''">
							<li>
								<p class="head" style="background:url(/i/ico6.png) no-repeat">
									<a class="popup" href="/ajax/popup/mode/delivery/id/{@delivery_id}/">Доставка</a>
								</p>
								<xsl:apply-templates select="delivery_description"/>
							</li>
						</xsl:if>
						<xsl:if test="credit_description!=''">
							<li>
								<p class="head" style="background:url(/i/ico7.png) no-repeat">
									<a class="popup" href="/ajax/popup/mode/credit/id/{@credit_id}/">Кредит</a>
								</p>
								<xsl:apply-templates select="credit_description"/>
							</li>
						</xsl:if>
					</ul>
				</xsl:if>
			</div>
		</div>
	</div>
	<div class="tabsholder">
		<ul id="tabs">
			<xsl:apply-templates select="tabs"/>
		</ul>
		<div id="tab_content">
			<xsl:apply-templates select="tabs[name='description']" mode="description"/>
			<xsl:apply-templates select="tabs[name='characteristics']" mode="charcter"/>
			<xsl:apply-templates select="tabs[name='video']" mode="video"/>
			<xsl:apply-templates select="tabs[name='items']" mode="items"/>
			<xsl:apply-templates select="tabs[name='comments']" mode="comments"/>
		</div>
	</div>
	<div class="tabs_hold">
		<div class="block-news">
			<ul class="tabs">
				<li ajaxlink="/item/shortattrib/itm/{/page/data/@id}/" class="active"><span href="news.html">Похожие товары</span><em>[0]</em></li>
			</ul>
			<span class="load"></span>
			<div class="content">					
				<!--<xsl:if test="count(similar_items) &gt; 0">-->
					<div class="carusel_hover">
						<xsl:if test="count(attr_shorts) &gt; 0">
							<ul class="attriboots" ajaxlink="/item/shortattribmode/itm/{/page/data/@id}/">
								<xsl:apply-templates select="attr_shorts"/>
							</ul>
						</xsl:if>
						
	<!--						<div id="carusel-holder">
							<ul class="jcarousel-skin" id="mycarousel">
								<xsl:apply-templates select="similar_items"/>
							</ul>
						</div>-->
						
						<div id="carusel-holder">
							<div class="jcarousel-skin" id="mycarousel">
								<ul ajaxlink="/item/getsimilaritem/itm/{/page/data/@id}/">
								</ul>
							</div>
						</div>
						<div id="carusel_hover"><div>&#160;</div></div>
					</div>						
				<!--</xsl:if>-->
			</div>
		</div>
	</div>
	<xsl:if test="//page/banner_cart_item/description!=''">
		<div class="seotext2">
			<p><xsl:apply-templates select="//page/banner_cart_item/description"/></p>
		</div>
	</xsl:if>
		
	<xsl:if test="seo_bottom!=''">
		<div class="seotext2">
			<p><xsl:apply-templates select="seo_bottom"/></p>
		</div>
	</xsl:if>
	
	
</xsl:template>

<xsl:template match="data">
	<xsl:apply-templates select="item"/>
</xsl:template>

</xsl:stylesheet>