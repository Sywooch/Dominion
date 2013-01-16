<?xml version="1.0" encoding="windows-1251"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml"  omit-xml-declaration="yes" encoding="windows-1251"/>

<xsl:variable name="pathToImagesDir" >../../images</xsl:variable>

<xsl:template match="table" mode="makeDir">mkdir "<xsl:value-of select="$pathToImagesDir" /><xsl:apply-templates select="@imagepath" />"
</xsl:template>

<xsl:template match="config"><xsl:apply-templates select="table[@imagepath!='']"  mode="makeDir" /></xsl:template>

</xsl:stylesheet>
