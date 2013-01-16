<?xml version="1.0" encoding="windows-1251"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="text" encoding="windows-1251"/>

<xsl:template match="table|joined" mode="sequence">INSERT IGNORE INTO SEQUENCES (NAME,ID) VALUES ('<xsl:value-of select="@name"/>','0');
</xsl:template>

<xsl:template match="col" mode="name">
<xsl:apply-templates select="@name"/><xsl:text> </xsl:text>
<xsl:choose>
	<xsl:when test="ref/@type='text'">
	<xsl:apply-templates select="/config/coltypes_indirect/option[@value=1]"/>	
	</xsl:when>
	<xsl:otherwise>
	<xsl:apply-templates select="/config/coltypes_indirect/option[@value=current()/@type]"/>
	</xsl:otherwise>
</xsl:choose>

<xsl:if test="@primary='y'"> NOT NULL</xsl:if>
<xsl:if test="position() != last()">,</xsl:if>
</xsl:template> 

<xsl:template match="col" mode="primary">
<xsl:apply-templates select="@name"/><xsl:if test="position() != last()">,</xsl:if>
</xsl:template>



<xsl:template match="table" mode="create">
----------------- <xsl:apply-templates select="@name"/>: -----------------
drop table if exists <xsl:apply-templates select="@name"/>;
create table <xsl:apply-templates select="@name"/> (CMF_LANG_ID int(12) unsigned NOT NULL, <xsl:apply-templates select="col" mode="name"/><xsl:if test="col/@primary">,primary key (<xsl:apply-templates select="col[@primary]" mode="primary"/>,CMF_LANG_ID)</xsl:if>);
-- LANG_ID,<xsl:apply-templates select="col" mode="primary"/>
create  index idx_<xsl:apply-templates select="@name"/>_system  on <xsl:apply-templates select="@name"/> (CMF_LANG_ID,<xsl:apply-templates select="col[@primary]" mode="primary"/>);
---------
</xsl:template>

<xsl:template match="table/joined" mode="create">
----------------- joined <xsl:apply-templates select="@name"/>: -----------------
drop table if exists <xsl:apply-templates select="@name"/>;
create table <xsl:apply-templates select="@name"/> (CMF_LANG_ID int(12) unsigned NOT NULL,
<xsl:apply-templates select="../col[@primary]" mode="name"/>,
<xsl:apply-templates select="col" mode="name"/>,primary key(<xsl:apply-templates select="../col[@primary] | col[@primary]" mode="primary"/>,CMF_LANG_ID));
-- <xsl:apply-templates select="col" mode="primary"/>
create  index idx_<xsl:apply-templates select="@name"/>_system  on <xsl:apply-templates select="@name"/> (CMF_LANG_ID,<xsl:apply-templates select="../col[@primary]|col[@primary]" mode="primary"/>);
---------
</xsl:template>

<xsl:template match="col" mode="indexname">
<xsl:apply-templates select="@name"/><xsl:if test="position() != last()">,</xsl:if>
</xsl:template>

<!-- xsl:template match="index"><xsl:value-of select="."/><xsl:if test="position()!=last()">_</xsl:if></xsl:template -->
<xsl:key name="table_index" match="col" use="concat(../@name,index)"/>

<xsl:template match="table|joined" mode="index">
<xsl:for-each select="col[count(. | key('table_index', concat(../@name,index))[1])=1]">
<xsl:if test="index &gt; 0">create <xsl:choose><xsl:when test="index/@unique|following-sibling::col[index=current()/index]/index/@unique">UNIQUE</xsl:when><xsl:when test="index/@fulltext|following-sibling::col[index=current()/index]/index/@fulltext">FULLTEXT</xsl:when></xsl:choose> index idx_<xsl:apply-templates select="../@name"/>_<xsl:value-of select="index[.=current()/index]"/>  on <xsl:apply-templates select="../@name"/> (CMF_LANG_ID,<xsl:apply-templates select=".|following-sibling::col[index=current()/index]" mode="indexname"><xsl:sort select="index[.=current()/index]/@order"/></xsl:apply-templates>);
</xsl:if> 
</xsl:for-each>
</xsl:template> 

<xsl:template match="table" mode="type" xml:space="preserve"><xsl:if test="@type">INSERT into CMF_XMLS_ARTICLE (TYPE,ARTICLE,EDIT) VALUES (<xsl:value-of select="@type"/>,'<xsl:choose><xsl:when test="@article"><xsl:value-of select="@article"/></xsl:when><xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise></xsl:choose>','<xsl:value-of select="@name"/>.php?e=ED&amp;id=%id%<xsl:if test="@parentscript">&amp;pid=%pid%</xsl:if><xsl:if test="@pagesize">&amp;p=%p%</xsl:if><xsl:if test="@letter">&amp;l=%l%</xsl:if><xsl:if test="@treechild">&amp;r=%r%</xsl:if>');
-- <xsl:value-of select="@type"/> -- <xsl:value-of select="@name"/>
</xsl:if></xsl:template> 

<xsl:template match="config">
-- MultiLanguage tables (multilanguage='y') --
-- use <xsl:value-of select="/config/basename"/>;
<!-- create sequence SEQ_<xsl:value-of select="table/@name"/> start with 1 increment by 1 nominvalue nomaxvalue nocycle nocache; -->
<xsl:apply-templates select="table[@multilanguage='y']" mode="create"/>
<xsl:apply-templates select="table/joined[@multilanguage='y']" mode="create"/>

<xsl:apply-templates select="table[@multilanguage='y']" mode="index"/>
<xsl:apply-templates select="table/joined[@multilanguage='y']" mode="index"/>

<xsl:apply-templates select="table[@multilanguage='y']" mode="type"><xsl:sort select="@type"/></xsl:apply-templates>
<xsl:apply-templates select="table[@multilanguage='y'][not(@multilink)]|table/joined[@multilanguage='y']" mode="sequence"/>
-- /MultiLanguage tables (multilanguage='y') --
</xsl:template>
</xsl:stylesheet>