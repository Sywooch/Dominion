<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>

<xsl:template match="view_compare_list" mode="item">
	<td>
		<div class="teaser">
			<xsl:choose>
				<xsl:when test="position()=last()"><xsl:attribute name="class">teaser last</xsl:attribute></xsl:when>
				<xsl:when test="position()=1"><xsl:attribute name="class">teaser first</xsl:attribute></xsl:when>
			</xsl:choose>
			<a href="{href}"> <img alt="{brand_name} {name}" src="/images/it/{image_small/@src}" width="{image_small/@w}" height="{image_small/@h}"/></a>
			<h3><a href="{href}"><xsl:value-of select="brand_name"/>&#160;<xsl:value-of select="name"/></a></h3>
			<xsl:choose>
				<xsl:when test="@price1 &gt; 0">
					<div class="price_box">
						<span class="price old"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/> </span> | <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
					</div>
					<div class="price_box">
						<span class="price personal gold"><xsl:value-of select="format-number(@price1, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span> | <xsl:value-of select="format-number(@real_price1, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
					</div>
					<xsl:value-of select="format-number(@price1, '### ##0', 'european')"/>
				</xsl:when>
				<xsl:otherwise>
					<div class="price_box">
						<span class="price"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span> | <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
					</div>			
				</xsl:otherwise>
			</xsl:choose>
			<a href="javascript:void(0);" xid="{@item_id}" class="product_button incard" title="Купить сейчас"><span>Купить сейчас</span></a>
		</div>
	</td>
</xsl:template>

<xsl:template match="view_compare_list" mode="brands">
	<td><xsl:value-of select="brand_name"/></td>
</xsl:template>

<xsl:template match="attributes">
	<xsl:value-of select="value"/>
</xsl:template>

<xsl:template match="view_compare_list" mode="value_atr">
	<xsl:param name="cur"/>
	<td>
		<xsl:apply-templates select="attributes[name=$cur]"/>
   </td>
</xsl:template>

<xsl:template match="attributes" mode="attr">
    <tr>
		<xsl:attribute name="class">
			<xsl:if test="(position() mod 2) = 0">odd</xsl:if>
		</xsl:attribute>
		<td class="feature">
			<span><xsl:value-of select="name"/>&#160;<xsl:if test="unit_name!=''">,<xsl:apply-templates select="unit_name"/></xsl:if></span>						
			<xsl:apply-templates select="../../view_compare_list" mode="value_atr">
                <xsl:with-param name="cur">
                    <xsl:value-of select="name"/>
                </xsl:with-param>
            </xsl:apply-templates>
		</td>
	</tr>
</xsl:template>

<xsl:template name="compare">
	<div id="compare_block">
<!--		<div id="same_products_content" class="box clearfix">
			
		</div>-->
		<table class="box">
			<tr>
				<td class="feature">&#160;</td>
				<xsl:apply-templates select="view_compare_list" mode="item"/>
			</tr>
			<tr class="odd">
				<td class="feature">Производитель</td>
				<xsl:apply-templates select="view_compare_list" mode="brands"/>
			</tr>
			<xsl:apply-templates select="view_compare_list[1]/attributes" mode="attr"/>						
		</table>
	</div>
</xsl:template>

<xsl:template match="data">
	<h1><xsl:apply-templates select="docinfo/name" /></h1>
</xsl:template>

</xsl:stylesheet>	