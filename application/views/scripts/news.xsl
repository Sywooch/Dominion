<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>
<xsl:import href="_banner_right_slider.xsl"/>

<xsl:template name="section_url">
<xsl:choose>
<xsl:when test="/page/data/@id &gt; 0">/news/<xsl:apply-templates select="/page/data/@id"/>/</xsl:when>
<xsl:otherwise>/news/</xsl:otherwise>
</xsl:choose>
</xsl:template>


<xsl:template match="news_list">
	<div class="teaser clearfix">
		<a href="{href}" class="img_box"><img src="/images/news/{image/@src}" alt="{name}" /></a>
		<h3><a href="{href}"><xsl:value-of select="name" /></a></h3>
		<p>
			<xsl:value-of select="short_description" />
		</p>
		<a href="{href}" class="more">Читать полностью</a>
	</div>	
</xsl:template>

<xsl:template match="data">
	<xsl:choose>
	   <xsl:when test="count(news_list) &gt; 0">
		   <h1><xsl:apply-templates select="docinfo/name" /></h1>
			<div class="chapter_products content_block">
				<xsl:apply-templates select="news_list"/>
				<ul class="pager">
					<xsl:apply-templates select="/page/data/section"/>
				</ul>
			</div>
	   </xsl:when>
	   <xsl:otherwise>
			<div class="textpage">
				<h1><xsl:value-of select="news_single/name"  disable-output-escaping="yes"/></h1>
				<xsl:apply-templates select="news_single/txt" />
			</div>
	  </xsl:otherwise>
	  </xsl:choose> 
</xsl:template>

</xsl:stylesheet>	