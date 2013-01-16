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
create table <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="col" mode="name"/><xsl:if test="col/@primary">,primary key(<xsl:apply-templates select="col[@primary]" mode="primary"/>)</xsl:if>);
-- <xsl:apply-templates select="col" mode="primary"/>
---------
</xsl:template> 

<xsl:template match="table/joined" mode="create">
----------------- joined <xsl:apply-templates select="@name"/>: -----------------
drop table if exists <xsl:apply-templates select="@name"/>;
create table <xsl:apply-templates select="@name"/> (
<xsl:apply-templates select="../col[@primary]" mode="name"/>,
<xsl:apply-templates select="col" mode="name"/>,primary key(<xsl:apply-templates select="../col[@primary] | col[@primary]" mode="primary"/>));
-- <xsl:apply-templates select="col" mode="primary"/>
---------
</xsl:template> 

<xsl:template match="table/joined/joined" mode="create">
----------------- joined <xsl:apply-templates select="@name"/>: -----------------
drop table if exists <xsl:apply-templates select="@name"/>;
create table <xsl:apply-templates select="@name"/> (
<xsl:apply-templates select="../col[@primary]" mode="name"/>,
<xsl:apply-templates select="col" mode="name"/>,primary key(<xsl:apply-templates select="../col[@primary] | col[@primary]" mode="primary"/>));
-- <xsl:apply-templates select="col" mode="primary"/>
---------
</xsl:template>

<xsl:template match="col" mode="indexname">
<xsl:apply-templates select="@name"/><xsl:if test="position() != last()">,</xsl:if>
</xsl:template>

<!-- xsl:template match="index"><xsl:value-of select="."/><xsl:if test="position()!=last()">_</xsl:if></xsl:template -->
<xsl:key name="table_index" match="col" use="concat(../@name,index)"/>

<xsl:template match="table|joined" mode="index">
<xsl:for-each select="col[count(. | key('table_index', concat(../@name,index))[1])=1]">
<xsl:if test="index &gt; 0">create <xsl:choose><xsl:when test="index/@unique|following-sibling::col[index=current()/index]/index/@unique">UNIQUE</xsl:when><xsl:when test="index/@fulltext|following-sibling::col[index=current()/index]/index/@fulltext">FULLTEXT</xsl:when></xsl:choose> index idx_<xsl:apply-templates select="../@name"/>_<xsl:value-of select="index[.=current()/index]"/>  on <xsl:apply-templates select="../@name"/> (<xsl:apply-templates select=".|following-sibling::col[index=current()/index]" mode="indexname"><xsl:sort select="index[.=current()/index]/@order"/></xsl:apply-templates>);
</xsl:if> 
</xsl:for-each>
</xsl:template> 

<xsl:template match="table" mode="type" xml:space="preserve"><xsl:if test="@type">INSERT into CMF_XMLS_ARTICLE (TYPE,ARTICLE,EDIT) VALUES (<xsl:value-of select="@type"/>,'<xsl:choose><xsl:when test="@article"><xsl:value-of select="@article"/></xsl:when><xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise></xsl:choose>','<xsl:value-of select="@name"/>.php?e=ED&amp;id=%id%<xsl:if test="@parentscript">&amp;pid=%pid%</xsl:if><xsl:if test="@pagesize">&amp;p=%p%</xsl:if><xsl:if test="@letter">&amp;l=%l%</xsl:if><xsl:if test="@treechild">&amp;r=%r%</xsl:if>');
-- <xsl:value-of select="@type"/> -- <xsl:value-of select="@name"/>
</xsl:if></xsl:template> 

<xsl:template match="config">
use <xsl:value-of select="/config/basename"/>;
<!-- create sequence SEQ_<xsl:value-of select="table/@name"/> start with 1 increment by 1 nominvalue nomaxvalue nocycle nocache; -->
<xsl:apply-templates select="table[not(@multilanguage)]" mode="create"/>
<xsl:apply-templates select="table/joined[not(@multilanguage)]" mode="create"/>
<xsl:apply-templates select="table/joined[not(@multilanguage)]/joined[not(@multilanguage)]" mode="create"/>

<xsl:apply-templates select="table[not(@multilanguage)]" mode="index"/>
<xsl:apply-templates select="table/joined[not(@multilanguage)]" mode="index"/>

<xsl:apply-templates select="table[not(@multilanguage)]" mode="type"><xsl:sort select="@type"/></xsl:apply-templates>
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (1,0,'INDEX','Главный скрипт','index.php','',NULL,NULL,0,1,1,1);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (2,1,'ADMIN','Админ','','',NULL,NULL,0,1,1,1);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (3,2,'SYSTEM','Системные функции','','','3.gif#28#26','#336699',0,1,1,1);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (4,2,'RIGHTS','Управление правами','','','4.gif#28#26','#70B1E4',0,1,1,1);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (5,3,'CMF_SCRIPT','Скрипты','CMF_SCRIPT.php','Управление Админским меню',NULL,NULL,0,1,1,2);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (6,3,'SEQUENCES','Последовательности','SEQUENCES.php','Аналог сиквенсов в Оракле',NULL,NULL,0,1,1,3);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (7,3,'MYSQLEDITOR','Mysql Editor','sql_edit.php','Редактор MySQL для ручного управления базой',NULL,NULL,0,1,1,4);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (20,3,'CMF_XMLS_ARTICLE','Типы для XML редактора','CMF_XMLS_ARTICLE.php','','','',0,1,1,5);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (24,3,'CMF_BUG','Отчет о багах','CMF_BUG.php','',NULL,NULL,0,1,1,6);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (25,3,'CMF_LANG','Языки CMF','CMF_LANG.php','',NULL,NULL,0,1,1,7);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (11,1,'fdg','РЕДАКТОР','','','','',0,1,1,3);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (12,11,'for','Сайт','','','12.gif#30#26','#339900',0,1,1,1);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (23,11,'for','Каталог','','','','#339900',0,1,1,1);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (13,12,'ANOTHER_PAGES','Страницы сайта','ANOTHER_PAGES.php','','','',0,1,1,1);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (14,4,'CMF_USER','Пользователи системы','CMF_USER.php','Удаление и добавление пользователей системы','','',0,1,1,1);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (15,4,'CMF_GROUP','Группы системы','CMF_GROUP.php','Удаление и добавление системных групп','','',0,1,1,2);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (16,23,'ATTRIBUT_GROUP','Группы атрибутов','ATTRIBUT_GROUP.php','Управление группами атрибутов каталога','','',0,1,1,3);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (19,12,'EDITER','Редактор XML','EDITER.php','','','',0,0,0,4);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (21,12,'NEWS','Новости','NEWS.php','Редактирование новостей','','',0,1,1,2);
INSERT INTO `CMF_SCRIPT` (`CMF_SCRIPT_ID`,`PARENT_ID`,`ARTICLE`,`NAME`,`URL`,`DESCRIPTION`,`IMAGE`,`BACKGROUND`,`TYPE`,`STATUS`,`REALSTATUS`,`ORDERING`) VALUES (22,4,'CMF_USER_RIGHTS','Ваши права','user_rights.php','Матрица ваших прав','','',0,1,1,3);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (1,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (1,2,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (2,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (3,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (4,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (5,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (6,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (7,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (11,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (11,2,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (12,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (12,2,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (13,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (13,2,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (14,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (15,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (16,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (16,2,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (19,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (20,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (21,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (21,2,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (22,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (22,2,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (23,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (23,2,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (24,1,1,1,1);
INSERT INTO `CMF_SCRIPT_GROUP` (`CMF_SCRIPT_ID`,`CMF_GROUP_ID`,`R`,`W`,`D`) VALUES (25,1,1,1,1);

INSERT INTO `CMF_USER` (`CMF_USER_ID`,`MD5_`,`NAME`,`LOGIN`,`PASS_`,`STATUS`,`URL`) VALUES (1,'246fee6c6775fbe7057e2fa971f90e6d','Главный администратор системы','admin','adm',1,'ANOTHER_PAGES.php');
INSERT INTO `CMF_USER` (`CMF_USER_ID`,`MD5_`,`NAME`,`LOGIN`,`PASS_`,`STATUS`,`URL`) VALUES (2,'E1CD90B205DFBA3B6508ABBD163468218140E980','Редактор','test','test',1,'ANOTHER_PAGES.php');
INSERT INTO `CMF_USER_GROUP` (`CMF_USER_ID`,`CMF_GROUP_ID`) VALUES (1,1);
INSERT INTO `CMF_USER_GROUP` (`CMF_USER_ID`,`CMF_GROUP_ID`) VALUES (2,2);
INSERT INTO `CMF_GROUP` (`CMF_GROUP_ID`,`NAME`) VALUES (1,'Админская группа');
INSERT INTO `CMF_GROUP` (`CMF_GROUP_ID`,`NAME`) VALUES (2,'Редакторы');
INSERT INTO `SEQUENCES` (`SEQUENCES_ID`,`NAME`,`ID`) VALUES (6,'CMF_SCRIPT',100);
INSERT INTO `CMF_LANG` (`CMF_LANG_ID`, `NAME`, `ORDERING`, `STATUS`) VALUES (1,'Russian',1,1);
--INSERT INTO `CMF_LANG` (`CMF_LANG_ID`, `NAME`, `ORDERING`, `STATUS`) VALUES (2,'English',2,1);
<xsl:apply-templates select="table[not(@multilanguage)][not(@multilink)]|table/joined[not(@multilanguage)]" mode="sequence"/>
</xsl:template>


</xsl:stylesheet>
