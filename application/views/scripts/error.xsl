<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "symbols.ent">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="_base.xsl" />

<!-- HEAD -->

<xsl:template name="title">
	<xsl:choose>
		<xsl:when test="//docinfo/title!=''"><xsl:apply-templates select="//docinfo/title"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="/page/default_title"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- Keywords -->
<xsl:template name="keywords">
<xsl:variable name="keywords">
<xsl:choose>
<xsl:when test="//docinfo/keywords!=''"><xsl:apply-templates select="//docinfo/keywords"/></xsl:when>
<xsl:otherwise><xsl:value-of select="/page/default_title"/></xsl:otherwise>
</xsl:choose>
</xsl:variable>

<xsl:call-template name="keyword">
  <xsl:with-param name="key"><xsl:value-of select="$keywords"/></xsl:with-param>
</xsl:call-template>
</xsl:template>

<xsl:template name="keyword">
<xsl:param name="key"/>
<xsl:variable name="key1"><![CDATA[<meta name="keywords" content="]]></xsl:variable>
<xsl:variable name="key2"><![CDATA["/>]]></xsl:variable>
<xsl:value-of select="$key1" disable-output-escaping="yes"/><xsl:value-of select="$key" disable-output-escaping="yes"/><xsl:value-of select="$key2" disable-output-escaping="yes"/>
</xsl:template>
<!-- Keywords -->

<!-- Description -->
<xsl:template name="description">
<xsl:variable name="description">
<xsl:choose>
<xsl:when test="//docinfo/description!=''"><xsl:apply-templates select="//docinfo/description"/></xsl:when>
<xsl:otherwise><xsl:value-of select="/page/default_title"/></xsl:otherwise>
</xsl:choose>
</xsl:variable>

<xsl:call-template name="descript">
  <xsl:with-param name="des"><xsl:value-of select="$description"/></xsl:with-param>
</xsl:call-template>
</xsl:template>

<xsl:template name="descript">
<xsl:param name="des"/>
<xsl:variable name="des1"><![CDATA[<meta name="description" content="]]></xsl:variable>
<xsl:variable name="des2"><![CDATA["/>]]></xsl:variable>
<xsl:value-of select="$des1" disable-output-escaping="yes"/><xsl:value-of select="$des" disable-output-escaping="yes"/><xsl:value-of select="$des2" disable-output-escaping="yes"/>
</xsl:template>
<!-- Description -->

<xsl:template name="headSection">
        <title><xsl:call-template name="title"/></title>
        <meta  http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <xsl:call-template name="keywords" />
        <xsl:call-template name="description" />
</xsl:template>

<xsl:template match="/page">
<xsl:variable name="doctype"><![CDATA[<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">]]></xsl:variable>
<!--<xsl:value-of select="$doctype" disable-output-escaping="yes"/>-->
<html>
 <head>
    <xsl:call-template name="headSection" />

    <link rel="stylesheet" type="text/css" href="/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/css/classes.css" />
    <link rel="stylesheet" type="text/css" href="/css/blocks.css" />
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/jsfunctions.js"></script>
 </head>
 <body class="page-404 clearfix">
	<a id="logo" href="/">интернет-магазин электроники и бытовой техники в Харькове</a>
    <div id="page_404">
    </div>
    <div id="page_404_message">
		<xsl:apply-templates select="error_message"/>
    </div>

</body>

</html>
</xsl:template>

</xsl:stylesheet>