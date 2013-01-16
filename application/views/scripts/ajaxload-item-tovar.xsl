<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_base.xsl"/>
	
	<xsl:template match="similar_items">
		<div class="teaser">
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
					<div class="price_box">
						<span class="price"><xsl:value-of select="format-number(@price1, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span> | <xsl:value-of select="format-number(@real_price1, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<div class="price_box">
						<span class="price"><xsl:value-of select="format-number(@price, '### ##0', 'european')"/>&#160;<xsl:value-of select="sname"/></span> | <xsl:value-of select="format-number(@real_price, '### ##0', 'european')"/>&#160;<xsl:value-of select="nat_sname"/>
					</div>			
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	
	<xsl:template match="page">
		<xsl:if test="count(similar_items) &gt; 0">
			 <xsl:apply-templates select="similar_items"/>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
