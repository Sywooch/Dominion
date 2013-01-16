<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>

<xsl:template name="section_url">
/register/orders/
</xsl:template>

<xsl:template match="item">
	<li><xsl:value-of select="name"/> | <xsl:value-of select="format-number(@total, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/> | <span class="quantity"><xsl:value-of select="@count"/> шт.</span></li>
</xsl:template>

<xsl:template match="myorders">
	<div class="kabinet_order">
		<h3>
			Заказ №<span class="number"><xsl:value-of select="@order"/></span> |
			<span class="date"><xsl:value-of select="posted_at"/></span>
		</h3>
		<ul class="order_products">
			<xsl:apply-templates select="item"/>
		</ul>
		Итого: <span class="sum"><xsl:value-of select="format-number(total_sum, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span>
		<xsl:if test="os_name != ''">
			| <span class="order_state" style="color: {os_color}"><xsl:value-of select="os_name"/></span>
		</xsl:if>
		
	</div>	
</xsl:template>

<xsl:template match="data">
	<h1><xsl:apply-templates select="docinfo/name" /></h1>
	<div class="content_block">
		<ul class="tabs">
			<li class="active">Заказы</li>
			<li><a href="/register/account/">Редактировать данные</a></li>
			<li><a href="/register/mybonus/">Бонусная программа</a></li>
		</ul>
		<div class="kabinet_tab">
			<xsl:apply-templates select="myorders"/>
		</div>
	</div>

</xsl:template>
</xsl:stylesheet>