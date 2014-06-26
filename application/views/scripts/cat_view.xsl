<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>

    <xsl:template name="headers">
        <script type="text/javascript" src="/js/menu.js"/>
    </xsl:template>

<xsl:template match="sub_cattree">
	<div>
		<h2><a href="{href}" class="img" style="background: url(/images/cat/{image/@src}) no-repeat">
			<xsl:value-of select="name"/></a></h2>
		<xsl:if test="count(brand_view) &gt; 0">
			<ul>
				<xsl:apply-templates select="brand_view"/>
			</ul>
		</xsl:if>		
		<a href="{href}" class="more">Смотреть все</a>
	</div>
</xsl:template>

<xsl:template match="brand_view">
	<li>
		<a href="{href}"><xsl:value-of select="name"/></a>
	</li>
</xsl:template>

<xsl:template match="data">
	<h1><xsl:value-of select="docinfo/name"/></h1>
	<div id="catalog_chapters" class="clearfix">
		<xsl:apply-templates select="sub_cattree"/>
	</div>
	<div id="catalog_text_block">
		<xsl:apply-templates select="docinfo/long_text"/>
	</div>
</xsl:template>

</xsl:stylesheet>