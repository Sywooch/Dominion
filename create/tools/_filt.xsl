<?xml version="1.0" encoding="windows-1251"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml"  omit-xml-declaration="yes" encoding="windows-1251"/>

<xsl:template match="col" mode="hidden">}; if ($_REQUEST['FLT_<xsl:value-of select="@name"/>'] !== '') {print qq{<input type="hidden" name="FLT_{@name}" ><xsl:attribute name="value">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</xsl:attribute></input>";} print "</xsl:template>
<xsl:template match="col" mode="header">}; if ($_REQUEST['FLT_<xsl:value-of select="@name"/>'] ne '') {$HEADER.=qq{&amp;FLT_<xsl:value-of select="@name"/>=$_REQUEST['FLT_<xsl:value-of select="@name"/>'];} $HEADER.=qq{</xsl:template>
<xsl:template match="col" mode="url">}; if ($_REQUEST['FLT_<xsl:value-of select="@name"/>'] ne '') {print "&amp;FLT_<xsl:value-of select="@name"/>={$_REQUEST['FLT_<xsl:value-of select="@name"/>']}";} print "</xsl:template>

<xsl:template match="col" mode="filt">
.<xsl:choose>
<xsl:when test="@type=1">($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')?' and A.<xsl:value-of select="@name"/> like \''.mysql_escape_string($_REQUEST['FLT_<xsl:value-of select="@name"/>'].'%')."'":'')</xsl:when>
<xsl:when test="@type=2 and @filttype='null'">($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')==1?" and (A.<xsl:value-of select="@name"/>='' or A.<xsl:value-of select="@name"/> is null) ":'')</xsl:when>
<xsl:when test="@type=4 and @filttype='null'">($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')==1?" and (A.<xsl:value-of select="@name"/>=0 or A.<xsl:value-of select="@name"/> is null or A.<xsl:value-of select="@name"/>='') ":'')</xsl:when>
<xsl:when test="@type=4 and @filttype='checkbox'">($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')==1?" and (A.<xsl:value-of select="@name"/> <xsl:text disable-output-escaping="yes">&gt;</xsl:text> '0') ":'')</xsl:when>


<xsl:when test="@type=7">($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')==1?" and A.<xsl:value-of select="@name"/> is not null and A.<xsl:value-of select="@name"/>!='' ":'').($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')==2?" and (A.<xsl:value-of select="@name"/> is null or A.<xsl:value-of select="@name"/>='') ":'')</xsl:when>
<xsl:when test="@type=8">($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')!=''?' and A.<xsl:value-of select="@name"/>='.mysql_escape_string($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')):'')</xsl:when>

<xsl:otherwise>($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')?' and A.<xsl:value-of select="@name"/>='.mysql_escape_string($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')):'')</xsl:otherwise>
</xsl:choose>
</xsl:template>


<xsl:template match="col" mode="prefilt">
<xsl:choose>
<xsl:when test="@type=2 and @filttype='null'">
if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:apply-templates select="@name"/>') == '1') {$V_<xsl:apply-templates select="@name"/>='checked';}
</xsl:when>
<xsl:when test="@type=4 and @filttype='null'">
if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:apply-templates select="@name"/>') == '1') {$V_<xsl:apply-templates select="@name"/>='checked';}
</xsl:when>
<xsl:when test="@type=4 and @filttype='checkbox'">
if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:apply-templates select="@name"/>') == '1') {$V_<xsl:apply-templates select="@name"/>='checked';}
</xsl:when>
<xsl:when test="@type=6">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>'),'select <xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/><xsl:text> </xsl:text><xsl:value-of select="ref/where" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>'<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);</xsl:when>
<xsl:when test="@type=13">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>TreeSpravotchnik($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>'),'select <xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/><xsl:text> </xsl:text>where PARENT_ID=?<xsl:value-of select="ref/where" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>',0);</xsl:when>
<xsl:when test="@type=7">
$FLT_<xsl:apply-templates select="@name"/>_yes='';
$FLT_<xsl:apply-templates select="@name"/>_no='';
if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:apply-templates select="@name"/>') == '1'){$FLT_<xsl:apply-templates select="@name"/>_yes='checked';} 
elseif ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:apply-templates select="@name"/>') == '2'){ $FLT_<xsl:apply-templates select="@name"/>_no='checked';}</xsl:when>
<xsl:when test="@filttype='checkbox'">
<!--
switch($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:apply-templates select="@name"/>'))
{
        case '0':
                $V_<xsl:apply-templates select="@name"/>_0='checked';
                break;
        case '1':
                $V_<xsl:apply-templates select="@name"/>_1='checked';
                break;
        default:
                $V_<xsl:apply-templates select="@name"/>_NULL='checked';
                break;
}
-->
switch($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:apply-templates select="@name"/>'))
{
   case '1':
                $V_<xsl:apply-templates select="@name"/>='checked';
                break;
}
</xsl:when>
<xsl:when test="@type=10">
$V_ENUM_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Enumerator($ENUM_<xsl:value-of select="@name"/>,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')<xsl:if test="enum/@start &gt; 0">,<xsl:value-of select="enum/@start"/></xsl:if>);</xsl:when>
<xsl:when test="@type=17">
$V_ENUM_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Enumerator2($ENUM_<xsl:value-of select="@name"/>,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('FLT_<xsl:value-of select="@name"/>')<xsl:if test="enum/@start &gt; 0">,<xsl:value-of select="enum/@start"/></xsl:if>);</xsl:when>

<xsl:when test="@type=14"></xsl:when>
</xsl:choose>
</xsl:template>

<xsl:template match="col" mode="filthidden"><input type="hidden" name="FLT_{@name}"><xsl:attribute name="value">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</xsl:attribute></input></xsl:template>

<xsl:template match="col" mode="filtform">
<tr bgcolor="#FFFFFF"><th>
<xsl:choose>
<xsl:when test="@filttype='checkbox'"><xsl:apply-templates select="comment"/></xsl:when>
<xsl:otherwise><xsl:apply-templates select="name"/></xsl:otherwise>
</xsl:choose><br/><img src="i/0.gif" width="125" height="1"/></th><td><xsl:choose>
<xsl:when test="@type=0"><input  class="form_input_big"  type="text" name="FLT_{@name}" size="{@size}"><xsl:attribute name="value">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</xsl:attribute></input><br/></xsl:when>
<xsl:when test="@type=1 and @name!='YANDEX_ID'"><input  class="form_input_big"  type="text" name="FLT_{@name}" size="{@size}"><xsl:attribute name="value">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</xsl:attribute></input><br/></xsl:when>
<xsl:when test="@type=2"><xsl:choose>
<xsl:when test="@filttype='null'">
Нет <xsl:text disable-output-escaping="yes">&lt;</xsl:text>input  name='FLT_<xsl:value-of select="@name"/>' type="checkbox" value="1" $V_<xsl:apply-templates select="@name"/>/<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
</xsl:when>
<xsl:otherwise><textarea  name="FLT_{@name}" rows="{@rows}" cols="{@cols}">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</textarea></xsl:otherwise></xsl:choose><br/></xsl:when>
<xsl:when test="@type=3"><input type="text" name="FLT_{@name}" size="{@size}"><xsl:attribute name="value">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</xsl:attribute></input><br/></xsl:when>
<xsl:when test="@type=4"><xsl:choose>
<xsl:when test="@filttype='null'">
Нет <xsl:text disable-output-escaping="yes">&lt;</xsl:text>input  name='FLT_<xsl:value-of select="@name"/>' type="checkbox" value="1" $V_<xsl:apply-templates select="@name"/>/<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
</xsl:when>
<xsl:when test="@filttype='checkbox'">
 <xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='checkbox' name='FLT_<xsl:value-of select="@name"/>' value='1' $V_<xsl:value-of select="@name"/> /<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
</xsl:when>
<xsl:otherwise>
<input class="form_input_small" type="text" name="FLT_{@name}" size="{@size}"><xsl:attribute name="value">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</xsl:attribute></input></xsl:otherwise></xsl:choose><br/></xsl:when>
<xsl:when test="@type=5"><input class="form_input_small" type="text" name="FLT_{@name}" size="{@size}"><xsl:attribute name="value">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</xsl:attribute></input><br/></xsl:when>
<xsl:when test="@type=6"><select name="FLT_{@name}"><xsl:if test="styles"><xsl:attribute name="style"><xsl:value-of select="styles"/></xsl:attribute></xsl:if><xsl:if test="onchange"><xsl:attribute name="onchange"><xsl:value-of select="onchange"/></xsl:attribute></xsl:if><option value="0">--------</option>{$V_STR_<xsl:value-of select="@name"/>}</select><br/></xsl:when>
<xsl:when test="@type=13"><select name="FLT_{@name}"><xsl:if test="ref/none"><option value="0"><xsl:value-of select="ref/none"/></option></xsl:if>$V_STR_<xsl:value-of select="@name"/></select><br/></xsl:when>
<xsl:when test="@type=7">Есть:<xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='radio' id='FLT_<xsl:value-of select="@name"/>_yes' name='FLT_<xsl:value-of select="@name"/>' value='1' $FLT_<xsl:value-of select="@name"/>_yes/<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Нет:<xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='radio' id='FLT_<xsl:value-of select="@name"/>_no' name='FLT_<xsl:value-of select="@name"/>' value='2' $FLT_<xsl:value-of select="@name"/>_no/<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Сбросить:<xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='radio' name='FLT_<xsl:value-of select="@name"/>' value='0' /<xsl:text disable-output-escaping="yes">&gt;</xsl:text><br/></xsl:when>

<xsl:when test="@filttype='checkbox'">
<!--
<xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='radio' name='FLT_<xsl:value-of select="@name"/>' value='1' $V_<xsl:value-of select="@name"/>_1 /<xsl:text disable-output-escaping="yes">&gt;</xsl:text> да<xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='radio' name='FLT_<xsl:value-of select="@name"/>' value='0' $V_<xsl:value-of select="@name"/>_0 /<xsl:text disable-output-escaping="yes">&gt;</xsl:text> нет<xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='radio' name='FLT_<xsl:value-of select="@name"/>' value='' $V_<xsl:value-of select="@name"/>_NULL /<xsl:text disable-output-escaping="yes">&gt;</xsl:text> все<br/>
-->
<xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='checkbox' name='FLT_<xsl:value-of select="@name"/>' value='1' $V_<xsl:value-of select="@name"/> /<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
</xsl:when>

<xsl:when test="@type=10"><select name="FLT_{@name}">$V_ENUM_<xsl:value-of select="@name"/></select><br/></xsl:when>
<xsl:when test="@type=17"><select name="FLT_{@name}">$V_ENUM_<xsl:value-of select="@name"/></select><br/></xsl:when>

<xsl:when test="@type=12"><input type="text" name="FLT_{@name}"><xsl:attribute name="value">{$_REQUEST['FLT_<xsl:value-of select="@name"/>']}</xsl:attribute></input><xsl:if test="@calendar">
        <img id="c_anc_{@name}" src="imgs/hi.gif" width="1" height="1" />
        <input type="image" src="i/c/cal.gif" class="gbut" onClick="return showCalendar(this,'{@name}');" align="absMiddle"/>
        <div id="c_div_{@name}" style="position:absolute;"></div>
</xsl:if>(YYYY-MM-DD)<br/></xsl:when>
<xsl:when test="@type=14"></xsl:when>
</xsl:choose></td></tr>
</xsl:template>

</xsl:stylesheet>
