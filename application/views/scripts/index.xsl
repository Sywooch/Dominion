<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>
<xsl:import href="_banner_right_slider.xsl"/>

<xsl:template match="cattree" mode="sub">	
	<li>
		<a href="{href}"><xsl:value-of select="name"/></a>
	</li>
</xsl:template>

<xsl:template match="cattree">	
	<div>
		<h2><a href="{href}" class="img" style="background: url(/images/cat/{image/@src}) no-repeat">
			<xsl:value-of select="name"/></a></h2>
		<xsl:if test="count(cattree) &gt; 0">
			<ul>
				<xsl:apply-templates select="cattree[@is_index=1]" mode="sub"/>
			</ul>
		</xsl:if>			
		<a href="{href}" class="more">Смотреть все</a>
	</div>
</xsl:template>


<xsl:template match="news_block">
	<div class="teaser clearfix">
		<a class="img_box" href="{href}"><img src="/images/news/{image/@src}" alt="{name}" /></a>
		<a href="{href}" class="more"><xsl:value-of select="name" /></a>
		<xsl:value-of select="short_description" />
	</div>
</xsl:template>

<xsl:template match="data">
    <div id="catalog_chapters" class="clearfix">
		<xsl:apply-templates select="cattree"/>		
	</div>    
</xsl:template>

</xsl:stylesheet>