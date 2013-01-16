<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_main.xsl"/>
<xsl:import href="_banner_right_slider.xsl"/>

<xsl:template match="data">	
	<h1><xsl:apply-templates select="docinfo/name" /></h1>
	<div class="content_block">
		<xsl:apply-templates select="docinfo/txt" />
	</div>
</xsl:template>
</xsl:stylesheet>