<?xml version="1.0" encoding="windows-1251"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml"  omit-xml-declaration="yes" encoding="windows-1251"/>
<xsl:include href="../tools_common/__base.xsl"/>
<xsl:include href="_filt.xsl"/>
<xsl:include href="_script.xsl"/>
<xsl:include href="_childscript.xsl"/>
<xsl:include href="_treescript.xsl"/>
<xsl:include href="_multilink.xsl"/>

<xsl:template match="config"><xsl:apply-templates select="table[not(@nogen)][@multilanguage='y']"/></xsl:template>

</xsl:stylesheet>
