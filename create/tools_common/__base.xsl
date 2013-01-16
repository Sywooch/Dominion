<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml"  omit-xml-declaration="yes" encoding="UTF-8"/>

<xsl:strip-space elements="panel"/>

<xsl:template name="multilanguage_mark">[m]</xsl:template>
<xsl:template name="forcedtranslation_mark">[f]</xsl:template>

<xsl:template match="panel" mode="edit">
<tr bgcolor="#FFFFFF"><td></td><td>
</td></tr>
</xsl:template>

<xsl:template match="pseudocol" mode="edit" xml:space="preserve"><xsl:if test="ifsection">};
<xsl:value-of select="ifsection" disable-output-escaping="yes"/>{ 
print qq{</xsl:if><tr bgcolor="#FFFFFF"><td width="1%"><b><xsl:apply-templates select="name"/>:<br/><img src="img/hi.gif" width="125" height="1"/></b></td><td width="100%"><xsl:value-of select="data" disable-output-escaping="yes"/></td></tr><xsl:if test="ifsection">};
};
print qq{</xsl:if></xsl:template>

<xsl:template match="pseudocol[@fullrow]" mode="edit" xml:space="preserve"><xsl:if test="ifsection">};
<xsl:value-of select="ifsection" disable-output-escaping="yes"/>{ 
print qq{</xsl:if><xsl:value-of select="data" disable-output-escaping="yes"/><xsl:if test="ifsection">};
};
print qq{</xsl:if></xsl:template>

<xsl:template match="extrakey"><input type="submit" name="{name}" value="{comment}" class="gbut"><xsl:if test="@class"><xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute></xsl:if></input>
</xsl:template>

<xsl:template match="extrakey[@src]"><input type="image" name="{name}" src="{@src}" border="0" alt="{comment}" title="{comment}" hspace="4" class="gbut"/>
</xsl:template>

<xsl:template match="child_script"><a href="{@name}.php?pid=$V_{../col[@primary='y']/@name}"><img src="{@image}" border="0" title="{@title}"/></a>
</xsl:template>

<xsl:template match="col" mode="head"><th><xsl:apply-templates select="name"/></th></xsl:template>
<xsl:template match="calccol" mode="head"><th><xsl:apply-templates select="name"/></th></xsl:template>

<xsl:template match="col" mode="formvalid"><xsl:if test="ifsection">};<xsl:value-of select="ifsection" disable-output-escaping="yes"/>{ print qq{</xsl:if><xsl:if test="not(@internal) and not(@novalid='y')">
<xsl:choose>
<xsl:when test="@type=1"><xsl:text disable-output-escaping="yes"> &amp;&amp; </xsl:text>checkXML(<xsl:value-of select="@name"/>)</xsl:when>
<xsl:when test="@type=2"><xsl:text disable-output-escaping="yes"> &amp;&amp; </xsl:text>checkXML(<xsl:value-of select="@name"/>)</xsl:when>
<xsl:when test="@type=5"><xsl:text disable-output-escaping="yes"> &amp;&amp; </xsl:text>checkEmail(<xsl:value-of select="@name"/>)</xsl:when>
</xsl:choose></xsl:if><xsl:if test="ifsection">};};print qq{</xsl:if>
</xsl:template>

<xsl:template match="col" mode="name"><xsl:choose><xsl:when test="@type=12">DATE_FORMAT(<xsl:apply-templates select="@name"/>,"%Y-%m-%d %H:%i")</xsl:when><xsl:otherwise><xsl:apply-templates select="@name"/></xsl:otherwise></xsl:choose><xsl:if test="position() != last()">,</xsl:if></xsl:template>
<xsl:template match="col" mode="aname"><xsl:choose><xsl:when test="@visualityname"><xsl:value-of select="@visualityname" disable-output-escaping="yes"/></xsl:when><xsl:when test="@type=12">DATE_FORMAT(A.<xsl:apply-templates select="@name"/>,"%Y-%m-%d %H:%i")</xsl:when><xsl:otherwise>A.<xsl:apply-templates select="@name"/></xsl:otherwise></xsl:choose><xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="name_insert"><xsl:param name="tableName" /><xsl:if test="$tableName"><xsl:value-of select="$tableName" />.</xsl:if><xsl:apply-templates select="@name"/><xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="zero">0<xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="update"><xsl:apply-templates select="@name"/>=<xsl:choose><xsl:when test="@type=12">?</xsl:when><xsl:otherwise>?</xsl:otherwise></xsl:choose><xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="vars">$V_<xsl:apply-templates select="@name"/><xsl:if test="position() != last()">,</xsl:if></xsl:template>
<xsl:template match="col" mode="vars_init">''<xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="form">stripslashes($_REQUEST['<xsl:choose><xsl:when test="@parent='y'">pid</xsl:when><xsl:otherwise><xsl:apply-templates select="@name"/></xsl:otherwise></xsl:choose>'])<xsl:if test="@type=3 or @type=4 or @type=6">+0</xsl:if><xsl:if test="position() != last()">,</xsl:if></xsl:template>
<xsl:template match="col[@internal='y']" mode="form"><xsl:choose><xsl:when test="@type=3 or @type=4 or @type=6">0</xsl:when><xsl:otherwise>''</xsl:otherwise></xsl:choose><xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="insert">
$sth-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>bind_param(<xsl:value-of select="position()"/>,$_REQUEST['<xsl:apply-templates select="@name"/>']);</xsl:template>

<xsl:template match="col" mode="vopros"><xsl:choose><xsl:when test="@type=12">?</xsl:when><xsl:otherwise>?</xsl:otherwise></xsl:choose><xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="vopros2"><xsl:if test="not(@has_related)"><xsl:choose><xsl:when test="@type=12">?</xsl:when><xsl:otherwise>?</xsl:otherwise></xsl:choose><xsl:if test="position() != last()">,</xsl:if></xsl:if></xsl:template>

<xsl:template match="col" mode="number">:<xsl:value-of select="position()"/><xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="forminput">$_REQUEST['<xsl:choose><xsl:when test="@parent='y'"><xsl:choose><xsl:when test="@filt='y' and @filttype='select'"><xsl:apply-templates select="@name"/></xsl:when><xsl:otherwise>pid</xsl:otherwise></xsl:choose></xsl:when><xsl:otherwise><xsl:apply-templates select="@name"/></xsl:otherwise></xsl:choose>_'.$id]<xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col[@type='8']" mode="forminput">intval($_REQUEST['<xsl:choose><xsl:when test="@parent='y'">pid</xsl:when><xsl:otherwise><xsl:apply-templates select="@name"/></xsl:otherwise></xsl:choose>_'.$id])<xsl:if test="position() != last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="varsprint">
<td>
        <xsl:choose>
                <xsl:when test="@input='y' and not(@internal)">
                        <xsl:choose>
                                <xsl:when test="@type='6' or @type='10' or @type='13'">
                                        <select onchange="ch(this)" name="{@name}_$V_{../col[@primary='y' and not(@parent)]/@name}" class="i">
                                                $V_STR_<xsl:apply-templates select="@name"/>
                                        </select>
                                </xsl:when>
                                <xsl:when test="@type='8'">
                                        <xsl:text disable-output-escaping="yes">&lt;</xsl:text>input onclick="ch(this)" type="checkbox" name="<xsl:value-of select="@name" />_$V_<xsl:value-of select="../col[@primary='y' and not(@parent)]/@name" />" class="i" value="1" $V_STR_<xsl:apply-templates select="@name"/>/<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
                                </xsl:when>
                                <xsl:otherwise>
                                        <input onchange="ch(this)" type="text" name="{@name}_$V_{../col[@primary='y' and not(@parent)]/@name}" class="i">
                                                <xsl:attribute name="value">$V_<xsl:apply-templates select="@name"/></xsl:attribute>
                                                <xsl:attribute name="tabindex">$TABposition</xsl:attribute>
                                        </input>
                                </xsl:otherwise>
                        </xsl:choose>
                </xsl:when>
                <xsl:otherwise>$V_<xsl:apply-templates select="@name"/><xsl:if test="@primary='y' and (@type=6 or @type=13)">_STR</xsl:if></xsl:otherwise>
        </xsl:choose>
</td>
</xsl:template>

<!-- xsl:template match="col" mode="varsprint"><td>$V_<xsl:apply-templates select="@name"/><xsl:if test="@primary='y' and (@type=6 or @type=13)">_STR</xsl:if></td></xsl:template -->
<xsl:template match="col[@child]" mode="varsprint"><td>

<!-- <a href="{@child}.php?pid=$V_{../col[@primary='y']/@name}&amp;p=$_REQUEST['p']" class="b">$V_<xsl:apply-templates select="@name"/><xsl:if test="@primary='y' and (@type=6 or @type=13)">_STR</xsl:if></a>-->
 
 <a class="b">
	 <xsl:attribute name="href"><xsl:value-of select="@child"/>.php?pid=$V_<xsl:value-of select="../col[@primary='y']/@name"/>&amp;p={$_REQUEST['p']}</xsl:attribute>$V_<xsl:apply-templates select="@name"/><xsl:if test="@primary='y' and (@type=6 or @type=13)">_STR</xsl:if></a>
 
 </td></xsl:template>

<xsl:template match="calccol" mode="varsprint"><td><xsl:value-of select="data" disable-output-escaping="yes"/></td></xsl:template>

<xsl:template match="enum/option">'<xsl:apply-templates/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="enumcreate" xml:space="preserve">
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>=array(<xsl:if test="enum/@start &gt; 0">''<xsl:if test="count(enum/option) &gt; 0">,</xsl:if></xsl:if><xsl:apply-templates select="enum/option"/>);
</xsl:template>

<xsl:template match="col|calccol" mode="sortnames">'<xsl:value-of select="name"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:template>
<xsl:template match="col" mode="sortquerys">'order by <xsl:choose><xsl:when test="@sortasc"><xsl:value-of select="@sortasc"/></xsl:when><xsl:otherwise>A.<xsl:value-of select="@name"/></xsl:otherwise></xsl:choose> ','order by <xsl:choose><xsl:when test="@sortdesc"><xsl:value-of select="@sortdesc"/></xsl:when><xsl:otherwise>A.<xsl:value-of select="@name"/></xsl:otherwise></xsl:choose> desc '<xsl:if test="position()!=last()">,</xsl:if></xsl:template>
<xsl:template match="calccol" mode="sortquerys">'<xsl:value-of select="@sortasc" disable-output-escaping="yes"/>','<xsl:value-of select="@sortdesc" disable-output-escaping="yes"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:template>

<xsl:template match="col" mode="previsible">
	<xsl:choose>
        <xsl:when test="@type=6">
                <xsl:variable name="refTableName"><xsl:value-of select="ref/table" /></xsl:variable>
                <xsl:variable name="refTableIndex"><xsl:value-of select="//table[@name=$refTableName]/col[@primary='y']/@name" /></xsl:variable>
                <xsl:variable name="isMultiLanguage"><xsl:value-of select="//table[@name=$refTableName]/@multilanguage='y' or //joined[@name=$refTableName]/@multilanguage='y'" /></xsl:variable>
                <xsl:variable name="multi"><xsl:value-of select="ref/multi" /></xsl:variable>

                <xsl:choose>
					<xsl:when test="ref/table!=''">
					<!-- Если указана реф-таблица -->
						<xsl:choose>
							<xsl:when test="$isMultiLanguage='true'">
                                <xsl:choose>
                                        <xsl:when test="@primary='y'">
$V_<xsl:apply-templates select="@name"/>_STR=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> where <xsl:apply-templates select="ref/field"/>=? and CMF_LANG_ID=?',$V_<xsl:apply-templates select="@name"/>,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
                                        </xsl:when>
                                        <xsl:otherwise>
$V_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> where <xsl:apply-templates select="ref/field"/>=? and CMF_LANG_ID=?',$V_<xsl:apply-templates select="@name"/>,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
                                        </xsl:otherwise>
                                </xsl:choose>
							</xsl:when>
                        
							<xsl:when test="$multi='1'">
$V_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> A <xsl:value-of select="ref/where" disable-output-escaping="yes"/> where A.<xsl:apply-templates select="ref/field"/>=?',$V_<xsl:apply-templates select="@name"/>);
							</xsl:when>                        
							
							<xsl:otherwise>
                                <xsl:choose>
                                        <xsl:when test="@primary='y'">
$V_<xsl:apply-templates select="@name"/>_STR=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> where <xsl:apply-templates select="ref/field"/>=?',$V_<xsl:apply-templates select="@name"/>);
                                        </xsl:when>
                                        <xsl:when test="@input='y' and not(@internal)">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="@name"/>,'select <xsl:value-of select="$refTableIndex" />, <xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/>');
                                        </xsl:when>
                                        <xsl:otherwise>
$V_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> where <xsl:apply-templates select="ref/field"/>=?',$V_<xsl:apply-templates select="@name"/>);
                                        </xsl:otherwise>
                                </xsl:choose>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					
					<xsl:otherwise>$V_<xsl:apply-templates select="@name"/>_STR='';</xsl:otherwise>
				 </xsl:choose>
        </xsl:when>
        
<xsl:when test="@type=7">
if(isset($V_<xsl:apply-templates select="@name"/>))
{
   $IM_<xsl:value-of select="position()"/>=split('#',$V_<xsl:apply-templates select="@name"/>);
   if(strchr($IM_<xsl:value-of select="position()"/>[0],".swf"))
   {
       $V_<xsl:apply-templates select="@name"/>="<xsl:text disable-output-escaping='yes'>&lt;</xsl:text>object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"150\" height=\"100\"align=\"middle\"<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>param name=\"allowScriptAccess\" value=\"sameDomain\" /<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_<xsl:value-of select='position()'/>[0]\" /<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>param name=\"quality\" value=\"high\" /<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_<xsl:value-of select='position()'/>[0]\" quality=\"high\" width=\"150\" height=\"100\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>/object<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>";
   }
   else
   {
      if(isset($IM_<xsl:value-of select="position()"/>[1]) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $IM_<xsl:value-of select="position()"/>[1] <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 150){$IM_<xsl:value-of select="position()"/>[2]=$IM_<xsl:value-of select="position()"/>[2]*150/$IM_<xsl:value-of select="position()"/>[1]; $IM_<xsl:value-of select="position()"/>[1]=150;
      $V_<xsl:apply-templates select="@name"/>="<xsl:text disable-output-escaping='yes'>&lt;</xsl:text>img src=\"/images$VIRTUAL_IMAGE_PATH$IM_<xsl:value-of select='position()'/>[0]\" width=\"$IM_<xsl:value-of select='position()'/>[1]\" height=\"$IM_<xsl:value-of select='position()'/>[2]\"<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>";}
   }
}
</xsl:when>
<xsl:when test="@type=15 or @type=16">
if(isset($V_<xsl:apply-templates select="@name"/>))
{
   $IM_<xsl:value-of select="position()"/>=split('#',$V_<xsl:apply-templates select="@name"/>);
   if(isset($IM_<xsl:value-of select="position()"/>[1]) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $IM_<xsl:value-of select="position()"/>[1] <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 150){$IM_<xsl:value-of select="position()"/>[2]=$IM_<xsl:value-of select="position()"/>[2]*150/$IM_<xsl:value-of select="position()"/>[1]; $IM_<xsl:value-of select="position()"/>[1]=150;
   $V_<xsl:apply-templates select="@name"/>="<xsl:text disable-output-escaping='yes'>&lt;</xsl:text>img src=\"/images$VIRTUAL_IMAGE_PATH$IM_<xsl:value-of select='position()'/>[0]\" width=\"$IM_<xsl:value-of select='position()'/>[1]\" height=\"$IM_<xsl:value-of select='position()'/>[2]\"<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>";}
}
</xsl:when>

<xsl:when test="@type=13">
                <xsl:choose>
                        <xsl:when test="@primary='y'">
$V_<xsl:apply-templates select="@name"/>_STR=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetTreePath('select PARENT_ID,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> where <xsl:apply-templates select="ref/field"/>=?',$V_<xsl:apply-templates select="@name"/>);
                        </xsl:when>
                        <xsl:when test="@input='y' and not(@internal)">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>TreeSpravotchnik($V_<xsl:apply-templates select="@name"/>,'select <xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/><xsl:text> </xsl:text><xsl:value-of select="ref/where" disable-output-escaping="yes"/> where PARENT_ID=? <xsl:value-of select="ref/where2" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>',0);
                        </xsl:when>
                        <xsl:otherwise>
$V_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> where <xsl:apply-templates select="ref/field"/>=?',$V_<xsl:apply-templates select="@name"/>);
                        </xsl:otherwise>
                </xsl:choose>
        </xsl:when>
        <xsl:when test="@type=8">
                <xsl:choose>
                        <xsl:when test="@input='y' and not(@internal)">
$V_STR_<xsl:apply-templates select="@name"/>=$V_<xsl:apply-templates select="@name"/>?'checked':'';
                        </xsl:when>
                        <xsl:otherwise>
if(!$V_<xsl:apply-templates select="@name"/>) {$V_<xsl:apply-templates select="@name"/>='Нет';} else {$V_<xsl:apply-templates select="@name"/>='Да';}
                        </xsl:otherwise>
                </xsl:choose>
        </xsl:when>
        <xsl:when test="@type=10">
                <xsl:choose>
                        <xsl:when test="@input='y' and not(@internal)">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Enumerator($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>,$V_<xsl:apply-templates select="@name"/>);
                        </xsl:when>
                        <xsl:otherwise>
$V_<xsl:value-of select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>[$V_<xsl:value-of select="@name"/>];
                        </xsl:otherwise>
                </xsl:choose>
        </xsl:when>
        <xsl:when test="@type=17">
                <xsl:choose>
                        <xsl:when test="@input='y' and not(@internal)">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Enumerator2($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>,$V_<xsl:apply-templates select="@name"/>);
                        </xsl:when>
                        <xsl:otherwise>
$V_<xsl:value-of select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>[$V_<xsl:value-of select="@name"/>];
                        </xsl:otherwise>
                </xsl:choose>
        </xsl:when>
</xsl:choose>

</xsl:template>


<xsl:template match="col" mode="preedit">
<xsl:choose>
	<xsl:when test="@type=6">
        <xsl:variable name="refTableName"><xsl:value-of select="ref/table" /></xsl:variable>
        <xsl:variable name="multi"><xsl:value-of select="ref/multi" /></xsl:variable>
        <xsl:variable name="isMultiLanguage"><xsl:value-of select="//table[@name=$refTableName]/@multilanguage='y' or //joined[@name=$refTableName]/@multilanguage='y'" /></xsl:variable>

        <xsl:choose>
			<xsl:when test="ref/table!='' and not(@has_parent)">
				<!-- Если указана реф-таблица -->                
				<xsl:choose>
					<xsl:when test="$isMultiLanguage='true'">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="@name"/>,'select <xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/><xsl:text> </xsl:text> where CMF_LANG_ID=? <xsl:value-of select="ref/where" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>',$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);
					</xsl:when>

					<xsl:when test="$multi='1'">
						<xsl:choose>
							<xsl:when test="@has_related='1'">
 $V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="../col[@primary='y']/@name"/>,'select A.<xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> A <xsl:text> </xsl:text><xsl:value-of select="ref/where" disable-output-escaping="yes"/> where 1 <xsl:value-of select="ref/where2" disable-output-escaping="yes"/><xsl:value-of select="ref/cond" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>'<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);
							</xsl:when>
						
							<xsl:otherwise>
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="@name"/>,'select A.<xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> A <xsl:text> </xsl:text><xsl:value-of select="ref/where" disable-output-escaping="yes"/> where 1 <xsl:value-of select="ref/where2" disable-output-escaping="yes"/><xsl:value-of select="ref/cond" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>'<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
        
					<xsl:otherwise>
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="@name"/>,'select <xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/><xsl:text> </xsl:text><xsl:choose><xsl:when test="not(@filt)"><xsl:value-of select="ref/where" disable-output-escaping="yes"/></xsl:when><xsl:otherwise><xsl:value-of select="ref/cond" disable-output-escaping="yes"/></xsl:otherwise></xsl:choose> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>'<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);        
					</xsl:otherwise>
				</xsl:choose>        
			</xsl:when>
			<xsl:when test="ref/table!='' and @has_parent!=''">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="@name"/>,'select <xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/><xsl:text> </xsl:text>where <xsl:value-of select="@has_parent"/>='.$V_<xsl:apply-templates select="@has_parent"/>.' order by <xsl:apply-templates select="ref/visual"/>'<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);        			
			</xsl:when>
			<xsl:otherwise>$V_STR_<xsl:apply-templates select="@name"/>='';</xsl:otherwise>
        </xsl:choose>        
</xsl:when>
<xsl:when test="@type=13">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>TreeSpravotchnik($V_<xsl:apply-templates select="@name"/>,'select A.<xsl:apply-templates select="ref/field"/>,A.<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> A <xsl:text> </xsl:text><xsl:value-of select="ref/where" disable-output-escaping="yes"/> where A.PARENT_ID=?<xsl:text> </xsl:text><xsl:value-of select="ref/where2" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order">A.<xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise>A.<xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>',0);</xsl:when>

<xsl:when test="@type=7">
if(isset($V_<xsl:value-of select="@name"/>))
{
   $IM_<xsl:value-of select="@name"/>=split('#',$V_<xsl:value-of select="@name"/>);
   if(isset($IM_<xsl:value-of select="position()"/>[1]) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $IM_<xsl:value-of select="@name"/>[1] <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 150){$IM_<xsl:value-of select="@name"/>[2]=$IM_<xsl:value-of select="@name"/>[2]*150/$IM_<xsl:value-of select="@name"/>[1]; $IM_<xsl:value-of select="@name"/>[1]=150;}
}
</xsl:when>
<xsl:when test="@type=15 or @type=16">
if(isset($V_<xsl:value-of select="@name"/>))
{
  $IM_<xsl:value-of select="@name"/>=split('#',$V_<xsl:value-of select="@name"/>);
  if(isset($IM_<xsl:value-of select="position()"/>[1]) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $IM_<xsl:value-of select="@name"/>[1] <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 150){$IM_<xsl:value-of select="@name"/>[2]=$IM_<xsl:value-of select="@name"/>[2]*150/$IM_<xsl:value-of select="@name"/>[1]; $IM_<xsl:value-of select="@name"/>[1]=150;}
}
</xsl:when>

<xsl:when test="@type=8">
$V_<xsl:apply-templates select="@name"/>=$V_<xsl:apply-templates select="@name"/>?'checked':'';</xsl:when>
<xsl:when test="@type=10">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Enumerator($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>,$V_<xsl:apply-templates select="@name"/>);</xsl:when>
<xsl:when test="@type=17">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Enumerator2($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>,$V_<xsl:apply-templates select="@name"/>);</xsl:when>

<xsl:when test="@type=14">
$IM_<xsl:value-of select="@name"/>=split('#',$V_<xsl:value-of select="@name"/>);</xsl:when>
</xsl:choose>
</xsl:template>

<xsl:template match="col" mode="preadd">
<xsl:choose>
	<xsl:when test="@type=6">
        <xsl:variable name="refTableName"><xsl:value-of select="ref/table" /></xsl:variable>
        <xsl:variable name="multi"><xsl:value-of select="ref/multi" /></xsl:variable>
        <xsl:variable name="isMultiLanguage"><xsl:value-of select="//table[@name=$refTableName]/@multilanguage='y' or //joined[@name=$refTableName]/@multilanguage='y'" /></xsl:variable>

        <xsl:choose>
			<xsl:when test="ref/table!='' and not(@has_parent)">
				<!-- Если указана реф-таблица -->        
				<xsl:choose>
					<xsl:when test="$isMultiLanguage='true'">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="@name"/>,'select <xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/><xsl:text> where CMF_LANG_ID=? </xsl:text><xsl:if test="ref/where">and<xsl:text> </xsl:text></xsl:if><xsl:value-of select="ref/where" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>',$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);
					</xsl:when>
					<xsl:when test="$multi='1'">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="@name"/>,'select A.<xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> A <xsl:text> </xsl:text><xsl:value-of select="ref/where" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>'<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);
					</xsl:when>
					<xsl:otherwise>
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:apply-templates select="@name"/>,'select <xsl:apply-templates select="ref/field"/>,<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/><xsl:text> </xsl:text> <xsl:choose><xsl:when test="not(@filt)"><xsl:value-of select="ref/where" disable-output-escaping="yes"/></xsl:when><xsl:otherwise><xsl:value-of select="ref/cond" disable-output-escaping="yes"/></xsl:otherwise></xsl:choose> order by <xsl:choose><xsl:when test="ref/order"><xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise><xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>'<xsl:value-of select="ref/attributes" disable-output-escaping="yes"/>);
					</xsl:otherwise>
				</xsl:choose>        
			</xsl:when>
			<xsl:otherwise>$V_STR_<xsl:apply-templates select="@name"/>='';</xsl:otherwise>
        </xsl:choose>
</xsl:when>
<xsl:when test="@type=13">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>TreeSpravotchnik($V_<xsl:apply-templates select="@name"/>,'select A.<xsl:apply-templates select="ref/field"/>,A.<xsl:apply-templates select="ref/visual"/> from <xsl:apply-templates select="ref/table"/> A <xsl:text> </xsl:text><xsl:value-of select="ref/where" disable-output-escaping="yes"/> where A.PARENT_ID=?<xsl:text> </xsl:text><xsl:value-of select="ref/where2" disable-output-escaping="yes"/> order by <xsl:choose><xsl:when test="ref/order">A.<xsl:apply-templates select="ref/order"/></xsl:when><xsl:otherwise>A.<xsl:apply-templates select="ref/visual"/></xsl:otherwise></xsl:choose>',0);</xsl:when>

<xsl:when test="@type=7">
$IM_<xsl:value-of select="@name"/>=array('','','');<!-- split('#',$V_<xsl:value-of select="@name"/>); --></xsl:when>
<xsl:when test="@type=15 or @type=16">
$IM_<xsl:value-of select="@name"/>=array('','','');<!-- split('#',$V_<xsl:value-of select="@name"/>); --></xsl:when>

<xsl:when test="@type=8">
$V_<xsl:apply-templates select="@name"/>='<xsl:if test="@default=1">checked</xsl:if>';</xsl:when>
<xsl:when test="@type=10">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Enumerator($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>,-1<!-- $V_<xsl:apply-templates select="@name"/> -->);</xsl:when>
<xsl:when test="@type=17">
$V_STR_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Enumerator2($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>ENUM_<xsl:value-of select="@name"/>,-1<!-- $V_<xsl:apply-templates select="@name"/> -->);</xsl:when>

<xsl:when test="@type=12">
$V_<xsl:apply-templates select="@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select now()');</xsl:when>
<xsl:when test="@type=14">
$IM_<xsl:value-of select="@name"/>=split('#',$V_<xsl:value-of select="@name"/>);</xsl:when>
</xsl:choose>
</xsl:template>

<xsl:template name="preinsert_swsh_bid">
	<xsl:param name="name"/>
	<xsl:param name="name2"/>
	<xsl:param name="prefix"/>
	<xsl:param name="postfix"/>
			$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addSettings('NOT_<xsl:value-of select="$name2"/>','<xsl:value-of select="$prefix"/>'.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'<xsl:value-of select="$postfix"/>', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_<xsl:value-of select="$name"/>, $width, $height);
			$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addImagePost();
			$_REQUEST['<xsl:value-of select="$name"/>'] = $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>new_image_name;
</xsl:template>

<xsl:template name="preinsert_swsh_iid">
	<xsl:param name="name"/>
	<xsl:param name="name2"/>
	<xsl:param name="prefix"/>
	<xsl:param name="postfix"/>
			$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addSettings('NOT_<xsl:value-of select="$name2"/>','<xsl:value-of select="$prefix"/>'.$_REQUEST['id'].'_'.$_REQUEST['iid'].'<xsl:value-of select="$postfix"/>', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_<xsl:value-of select="$name"/>, $width, $height);
			$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addImagePost();
			$_REQUEST['<xsl:value-of select="$name"/>'] = $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>new_image_name;
</xsl:template>

<xsl:template name="preinsert_swsh_small">
	<xsl:param name="name"/>
	<xsl:param name="name2"/>
	<xsl:param name="prefix"/>
	<xsl:param name="postfix"/>
			if(isset($_REQUEST['iid']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> !empty($_REQUEST['iid'])){
				$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addSettings('NOT_<xsl:value-of select="$name2"/>','<xsl:value-of select="$prefix"/>'.$_REQUEST['id'].'_'.$_REQUEST['iid'].'<xsl:value-of select="$postfix"/>', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_<xsl:value-of select="$name"/>, $width, $height);
			}
			else{
				$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addSettings('NOT_<xsl:value-of select="$name2"/>','<xsl:value-of select="$prefix"/>'.$_REQUEST['id'].'<xsl:value-of select="$postfix"/>', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_<xsl:value-of select="$name"/>, $width, $height);
			}
			
			$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addImagePost();
			$_REQUEST['<xsl:value-of select="$name"/>'] = $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>new_image_name;
</xsl:template>

<xsl:template name="preinsert_swsh">
	<xsl:param name="name"/>
	<xsl:param name="name2"/>
	<xsl:param name="prefix"/>
	<xsl:param name="postfix"/>
			$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addSettings('NOT_<xsl:value-of select="$name2"/>','<xsl:value-of select="$prefix"/>'.$_REQUEST['id'].'<xsl:value-of select="$postfix"/>', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_<xsl:value-of select="$name"/>, $width, $height);
			$obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addImagePost();
			$_REQUEST['<xsl:value-of select="$name"/>'] = $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>new_image_name;
</xsl:template>

<xsl:template match="col" mode="preinsert_wh" xml:space="preserve">
<xsl:choose>
	<xsl:when test="@watermark">
$path_to_watermark = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@watermark"/>'");

if(!empty($path_to_watermark) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> !empty($_REQUEST['IS_WATERMARK<xsl:apply-templates select="@postfix"/>'])){
	$path_to_watermark = preg_replace('/\#.*/','',$path_to_watermark);
	$path_to_watermark_<xsl:apply-templates select="@name"/>= $path_to_watermark;
}
else $path_to_watermark_<xsl:apply-templates select="@name"/>='';	
	</xsl:when>
	<xsl:otherwise>
$path_to_watermark_<xsl:apply-templates select="@name"/>='';
	</xsl:otherwise>
</xsl:choose>

	<xsl:if test="@width">
		$width = <xsl:value-of select="@width"/>;
	</xsl:if>
	<xsl:if test="@height">
		$height = <xsl:value-of select="@height"/>;
	</xsl:if>
    if(isset($_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
			  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
				<xsl:call-template name="preinsert_swsh_bid">
					<xsl:with-param name="name"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="name2"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="prefix"><xsl:value-of select="@prefix"/></xsl:with-param>
					<xsl:with-param name="postfix"><xsl:value-of select="@postfix"/></xsl:with-param>
				</xsl:call-template>
			  }
			  else{
					$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);
			  }
		   }
		   else{
			  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
				  <xsl:call-template name="preinsert_swsh_iid">
					<xsl:with-param name="name"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="name2"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="prefix"><xsl:value-of select="@prefix"/></xsl:with-param>
					<xsl:with-param name="postfix"><xsl:value-of select="@postfix"/></xsl:with-param>
				</xsl:call-template>
			  }
			  else{
					$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined' or @is_child='1'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);		   
			  }
		   }
		}
		else{ 
			if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
			    <xsl:call-template name="preinsert_swsh">
					<xsl:with-param name="name"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="name2"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="prefix"><xsl:value-of select="@prefix"/></xsl:with-param>
					<xsl:with-param name="postfix"><xsl:value-of select="@postfix"/></xsl:with-param>
				</xsl:call-template>
			}
			else{
					$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined' or @is_child='1'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);		   
			}
		}
	}
</xsl:template>

<xsl:template match="col" mode="preinsert_swsh" xml:space="preserve">
<xsl:choose>
	<xsl:when test="@watermark">
$path_to_watermark = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@watermark"/>'");

if(!empty($path_to_watermark) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> !empty($_REQUEST['IS_WATERMARK<xsl:apply-templates select="@postfix"/>'])){
	$path_to_watermark = preg_replace('/\#.*/','',$path_to_watermark);
	$path_to_watermark_<xsl:apply-templates select="@name"/>= $path_to_watermark;
}
else $path_to_watermark_<xsl:apply-templates select="@name"/>='';	
	</xsl:when>
	<xsl:otherwise>
$path_to_watermark_<xsl:apply-templates select="@name"/>='';
	</xsl:otherwise>
</xsl:choose>

	$width = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@set_width"/>'");
	$height = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@set_height"/>'");
	
    if(isset($_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
			  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
				<xsl:call-template name="preinsert_swsh_bid">
					<xsl:with-param name="name"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="name2"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="prefix"><xsl:value-of select="@prefix"/></xsl:with-param>
					<xsl:with-param name="postfix"><xsl:value-of select="@postfix"/></xsl:with-param>
				</xsl:call-template>
			  }
			  else{
					$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);
			  }
		   }
		   else{
			  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
				<xsl:call-template name="preinsert_swsh_iid">
					<xsl:with-param name="name"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="name2"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="prefix"><xsl:value-of select="@prefix"/></xsl:with-param>
					<xsl:with-param name="postfix"><xsl:value-of select="@postfix"/></xsl:with-param>
				</xsl:call-template>
			  }
			  else{
					 $_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined' or @is_child='1'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);		   
			  }
		   }
		}
		else{ 
			if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
			   <xsl:call-template name="preinsert_swsh">
					<xsl:with-param name="name"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="name2"><xsl:value-of select="@name"/></xsl:with-param>
					<xsl:with-param name="prefix"><xsl:value-of select="@prefix"/></xsl:with-param>
					<xsl:with-param name="postfix"><xsl:value-of select="@postfix"/></xsl:with-param>
				</xsl:call-template>
			}
			else{
				$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined' or @is_child='1'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);		   
			}
		}
	}
</xsl:template>

<xsl:template match="col" mode="preinsert_new" xml:space="preserve">
    if(isset($_FILES['NOT_<xsl:value-of select="@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['<xsl:value-of select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['<xsl:value-of select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined' or @is_child='1'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['<xsl:value-of select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined' or @is_child='1'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);		   
		}
	}
</xsl:template>

<xsl:template match="col" mode="preinsert15_wh" xml:space="preserve">
<xsl:choose>
	<xsl:when test="@watermark">
$path_to_watermark = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@watermark"/>'");

if(!empty($path_to_watermark) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> !empty($_REQUEST['IS_WATERMARK<xsl:apply-templates select="@postfix"/>'])){
	$path_to_watermark = preg_replace('/\#.*/','',$path_to_watermark);
	$path_to_watermark_<xsl:apply-templates select="@name"/>= $path_to_watermark;
}
else $path_to_watermark_<xsl:apply-templates select="@name"/>='';	
	</xsl:when>
	<xsl:otherwise>
$path_to_watermark_<xsl:apply-templates select="@name"/>='';
	</xsl:otherwise>
</xsl:choose>

	<xsl:if test="@width">
		$width = <xsl:value-of select="@width"/>;
	</xsl:if>
	<xsl:if test="@height">
		$height = <xsl:value-of select="@height"/>;
	</xsl:if>
   if(isset($_REQUEST['GEN_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['GEN_<xsl:apply-templates select="@name"/>'] <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> isset($_FILES['NOT_<xsl:apply-templates select="../col[@type=7]/@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="../col[@type=7]/@name"/>']['tmp_name']){
	  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
		  <xsl:call-template name="preinsert_swsh_small">
			<xsl:with-param name="name"><xsl:apply-templates select="@name"/></xsl:with-param>
			<xsl:with-param name="name2"><xsl:apply-templates select="../col[@type=7]/@name"/></xsl:with-param>
			<xsl:with-param name="prefix"><xsl:apply-templates select="@prefix"/></xsl:with-param>
			<xsl:with-param name="postfix"><xsl:apply-templates select="@postfix"/></xsl:with-param>
		  </xsl:call-template> 
	  }
	  else{
			$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePostResize('NOT_<xsl:apply-templates select="col[@type=7]/@name"/>',$_REQUEST['IMAGE2'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_<xsl:apply-templates select="@name"/>'],$_REQUEST['HEIGHT_<xsl:apply-templates select="@name"/>']);
	  }
	}
	elseif(isset($_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']){
	  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
		  <xsl:call-template name="preinsert_swsh_small">
			<xsl:with-param name="name"><xsl:apply-templates select="@name"/></xsl:with-param>
			<xsl:with-param name="name2"><xsl:apply-templates select="@name"/></xsl:with-param>
			<xsl:with-param name="prefix"><xsl:apply-templates select="@prefix"/></xsl:with-param>
			<xsl:with-param name="postfix"><xsl:apply-templates select="@postfix"/></xsl:with-param>
		  </xsl:call-template>
	  }
	  else{
			$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePostResize('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_<xsl:apply-templates select="@name"/>'],$_REQUEST['HEIGHT_<xsl:apply-templates select="@name"/>']);
	  }
	}
</xsl:template>

<xsl:template match="col" mode="preinsert15_swsh" xml:space="preserve">
<xsl:choose>
	<xsl:when test="@watermark">
$path_to_watermark = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@watermark"/>'");

if(!empty($path_to_watermark) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> !empty($_REQUEST['IS_WATERMARK<xsl:apply-templates select="@postfix"/>'])){
	$path_to_watermark = preg_replace('/\#.*/','',$path_to_watermark);
	$path_to_watermark_<xsl:apply-templates select="@name"/>= $path_to_watermark;
}
else $path_to_watermark_<xsl:apply-templates select="@name"/>='';	
	</xsl:when>
	<xsl:otherwise>
$path_to_watermark_<xsl:apply-templates select="@name"/>='';
	</xsl:otherwise>
</xsl:choose>

	$width = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@set_width"/>'");
	$height = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@set_height"/>'");
   if(isset($_REQUEST['GEN_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['GEN_<xsl:apply-templates select="@name"/>'] <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> isset($_FILES['NOT_<xsl:apply-templates select="../col[@type=7]/@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="../col[@type=7]/@name"/>']['tmp_name']){
	  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
		  <xsl:call-template name="preinsert_swsh_small">
			<xsl:with-param name="name"><xsl:apply-templates select="@name"/></xsl:with-param>
			<xsl:with-param name="name2"><xsl:apply-templates select="../col[@type=7]/@name"/></xsl:with-param>
			<xsl:with-param name="prefix"><xsl:apply-templates select="@prefix"/></xsl:with-param>
			<xsl:with-param name="postfix"><xsl:apply-templates select="@postfix"/></xsl:with-param>
		  </xsl:call-template> 
	  }
	  else{
			$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePostResize('NOT_<xsl:apply-templates select="col[@type=7]/@name"/>',$_REQUEST['IMAGE2'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_<xsl:apply-templates select="@name"/>'],$_REQUEST['HEIGHT_<xsl:apply-templates select="@name"/>']);
	  }
	}
	elseif(isset($_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']){
	  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
		  <xsl:call-template name="preinsert_swsh_small">
			<xsl:with-param name="name"><xsl:apply-templates select="@name"/></xsl:with-param>
			<xsl:with-param name="name2"><xsl:apply-templates select="@name"/></xsl:with-param>
			<xsl:with-param name="prefix"><xsl:apply-templates select="@prefix"/></xsl:with-param>
			<xsl:with-param name="postfix"><xsl:apply-templates select="@postfix"/></xsl:with-param>
		  </xsl:call-template>
	  }
	  else{
			$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePostResize('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_<xsl:apply-templates select="@name"/>'],$_REQUEST['HEIGHT_<xsl:apply-templates select="@name"/>']);
	  }
	}
</xsl:template>

<xsl:template match="col" mode="preinsert" xml:space="preserve"><xsl:choose>
	<xsl:when test="@type=7">
		<xsl:choose>
			<xsl:when test="@width!='' and @height!=''">
				<xsl:apply-templates select="self::node()" mode="preinsert_wh"/>
			</xsl:when>
			<xsl:when test="@set_width!='' and @set_height!=''">
				<xsl:apply-templates select="self::node()" mode="preinsert_swsh"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="self::node()" mode="preinsert_new"/>
			</xsl:otherwise>
		</xsl:choose>
		if(isset($_REQUEST['CLR_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['CLR_<xsl:apply-templates select="@name"/>']){$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>UnlinkFile($_REQUEST['<xsl:apply-templates select="@name"/>'],$VIRTUAL_IMAGE_PATH);}
	</xsl:when>
	<xsl:when test="@type=15 or @type=16">
		<xsl:choose>
			<xsl:when test="@width!='' and @height!=''">
				<xsl:apply-templates select="self::node()" mode="preinsert15_wh"/>
			</xsl:when>
			<xsl:when test="@set_width!='' and @set_height!=''">
				<xsl:apply-templates select="self::node()" mode="preinsert15_swsh"/>
			</xsl:when>
		</xsl:choose>
	
	if(isset($_REQUEST['CLR_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['CLR_<xsl:apply-templates select="@name"/>']){$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>UnlinkFile($_REQUEST['<xsl:apply-templates select="@name"/>'],$VIRTUAL_IMAGE_PATH);}
	</xsl:when>
	<xsl:when test="@type=8">$_REQUEST['<xsl:apply-templates select="@name"/>']=isset($_REQUEST['<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['<xsl:apply-templates select="@name"/>']?1:0;</xsl:when>
	<xsl:when test="@type=14">$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>FilePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);
	if(isset($_REQUEST['CLR_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['CLR_<xsl:apply-templates select="@name"/>']){$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>UnlinkFile($_REQUEST['<xsl:apply-templates select="@name"/>'],$VIRTUAL_IMAGE_PATH);}
	</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="col[../@multilanguage='y']" mode="preinsert" xml:space="preserve"><xsl:choose>

<xsl:when test="@type=7">
if(isset($_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']){
	if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
	  $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addSettings('NOT_<xsl:apply-templates select="@name"/>','<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_big, <xsl:value-of select="@width"/>, <xsl:value-of select="@height"/>);
	  $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addImagePost();
	  $_REQUEST['<xsl:apply-templates select="@name"/>'] = $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>new_image_name;
	}
	else{
		$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);
	}
}

if(isset($_REQUEST['CLR_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['CLR_<xsl:apply-templates select="@name"/>']){$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>UnlinkFile($_REQUEST['<xsl:apply-templates select="@name"/>'],$VIRTUAL_IMAGE_PATH);}
</xsl:when>
<xsl:when test="@type=15 or @type=16">
if(isset($_REQUEST['GEN_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['GEN_<xsl:apply-templates select="@name"/>'] <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> isset($_FILES['NOT_<xsl:apply-templates select="../col[@type=7]/@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="../col[@type=7]/@name"/>']['tmp_name']){
  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
	  $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addSettings('NOT_<xsl:apply-templates select="../col[@type=7]/@name"/>','<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_small, <xsl:value-of select="@width"/>, <xsl:value-of select="@height"/>);
	  $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addImagePost();
	  $_REQUEST['<xsl:apply-templates select="@name"/>'] = $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>new_image_name;
  }
  else{
	  $_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePostResize('NOT_IMAGE2',$_REQUEST['IMAGE2'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_<xsl:apply-templates select="@name"/>'],$_REQUEST['HEIGHT_<xsl:apply-templates select="@name"/>']);
  }
}
elseif(isset($_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_FILES['NOT_<xsl:apply-templates select="@name"/>']['tmp_name']){
  if(isset($obj_img_resize) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> is_object($obj_img_resize)){
	  $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addSettings('NOT_<xsl:apply-templates select="../col[@type=7]/@name"/>','<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_small, <xsl:value-of select="@width"/>, <xsl:value-of select="@height"/>);
	  $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>addImagePost();
	  $_REQUEST['<xsl:apply-templates select="@name"/>'] = $obj_img_resize-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>new_image_name;
  }
  else{
	  $_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>PicturePostResize('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH$_REQUEST['WIDTH_<xsl:apply-templates select="@name"/>'],$_REQUEST['HEIGHT_<xsl:apply-templates select="@name"/>']);
   }
}
if(isset($_REQUEST['CLR_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['CLR_<xsl:apply-templates select="@name"/>']){$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>UnlinkFile($_REQUEST['<xsl:apply-templates select="@name"/>'],$VIRTUAL_IMAGE_PATH);}
</xsl:when>

<xsl:when test="@type=8">$_REQUEST['<xsl:apply-templates select="@name"/>']=isset($_REQUEST['<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['<xsl:apply-templates select="@name"/>']?1:0;</xsl:when>
<xsl:when test="@type=14">$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>FilePost('NOT_<xsl:apply-templates select="@name"/>',$_REQUEST['<xsl:apply-templates select="@name"/>'],'<xsl:value-of select="@prefix"/>'.$_REQUEST['id']<xsl:if test="name(..)='joined'">.'_'.$_REQUEST['iid']</xsl:if>.'_'.$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID.'<xsl:value-of select="@postfix"/>',$VIRTUAL_IMAGE_PATH);
if(isset($_REQUEST['CLR_<xsl:apply-templates select="@name"/>']) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> $_REQUEST['CLR_<xsl:apply-templates select="@name"/>']){$_REQUEST['<xsl:apply-templates select="@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>UnlinkFile($_REQUEST['<xsl:apply-templates select="@name"/>'],$VIRTUAL_IMAGE_PATH);}
</xsl:when>
</xsl:choose></xsl:template>

<xsl:template name="set_watermark">
<input type="checkbox" name="IS_WATERMARK{@postfix}"/> Прикрепить watermark
</xsl:template>
<xsl:template match="col" mode="noadd">
<input type="hidden" name="{@name}" value=""/>
<xsl:if test="@type=7 or @type=15 or @type=16">
<div style="display:none"><input type="file" name="NOT_{@name}"/></div>
</xsl:if>
</xsl:template>

<xsl:template match="col" mode="edit" xml:space="preserve"><xsl:if test="ifsection">};
<xsl:value-of select="ifsection" disable-output-escaping="yes"/>{
print qq{</xsl:if><tr bgcolor="#FFFFFF"><th width="1%"><b><xsl:apply-templates select="name"/>:<br/><img src="img/hi.gif" width="125" height="1"/></b></th><td width="100%"><xsl:choose>
<xsl:when test="@type=1">
<xsl:choose>
<xsl:when test="@readonly='y'"><input type="text" name="{@name}_R" value="$V_{@name}" size="{@size}" readonly="1"><xsl:if test="@panelize='y'"><xsl:attribute name="onfocus">_XDOC=this;</xsl:attribute><xsl:attribute name="onkeydown">_etaKey(event)</xsl:attribute></xsl:if></input><input type="hidden" name="{@name}" value="$V_{@name}"/><br/>
</xsl:when>
<xsl:otherwise>
<input type="text" name="{@name}" value="$V_{@name}" size="{@size}"><xsl:if test="@panelize='y'"><xsl:attribute name="onfocus">_XDOC=this;</xsl:attribute><xsl:attribute name="onkeydown">_etaKey(event)</xsl:attribute></xsl:if></input><br/>
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:when test="@type=2">
<xsl:choose>
<xsl:when test="@panelize='y'">
<textarea id="{@name}" name="{@name}" rows="{@rows}" cols="{@cols}">
EOF;
$V_<xsl:value-of select="@name"/> = htmlspecialchars_decode($V_<xsl:value-of select="@name"/>);
echo $V_<xsl:value-of select="@name"/>;
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( '<xsl:value-of select="@name"/>', {
      <xsl:choose>
		  <xsl:when test="@econfig">customConfig : 'ckeditor/<xsl:value-of select="@econfig"/>',</xsl:when>
		  <xsl:otherwise>customConfig : 'ckeditor/config.js',</xsl:otherwise>
	  </xsl:choose>
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>
</xsl:when>
<xsl:otherwise>
<xsl:choose>
<xsl:when test="@readonly='y'"><textarea name="{@name}_R" rows="{@rows}" cols="{@cols}" readonly="1">$V_<xsl:value-of select="@name"/></textarea><input type="hidden" name="{@name}" value="$V_{@name}"/><br/></xsl:when>
<xsl:otherwise>
<textarea name="{@name}" rows="{@rows}" cols="{@cols}"><xsl:if test="styles"><xsl:attribute name="style"><xsl:value-of select="styles"/></xsl:attribute></xsl:if>$V_<xsl:value-of select="@name"/></textarea><br/>
</xsl:otherwise>
</xsl:choose>
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:when test="@type=3">
<xsl:choose>
<xsl:when test="@readonly='y'"><input type="text" name="{@name}_R" value="$V_{@name}" size="{@size}" readonly="1"></input><input type="hidden" name="{@name}" value="$V_{@name}"/><br/>
</xsl:when>
<xsl:otherwise>
<input type="text" name="{@name}" value="$V_{@name}" size="{@size}"/><br/>
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:when test="@type=4">
<xsl:choose>
<xsl:when test="@readonly='y'"><input type="text" name="{@name}_R" value="$V_{@name}" size="{@size}" readonly="1"></input><input type="hidden" name="{@name}" value="$V_{@name}"/><br/>
</xsl:when>
<xsl:otherwise>
<input type="text" name="{@name}" value="$V_{@name}" size="{@size}"/><br/>
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:when test="@type=5">
<xsl:choose>
<xsl:when test="@readonly='y'"><input type="text" name="{@name}_R" value="$V_{@name}" size="{@size}" readonly="1"></input><input type="hidden" name="{@name}" value="$V_{@name}"/><br/>
</xsl:when>
<xsl:otherwise>
<input type="text" name="{@name}" value="$V_{@name}" size="{@size}"><xsl:if test="@panelize='y'"><xsl:attribute name="onfocus">_XDOC=this;</xsl:attribute><xsl:attribute name="onkeydown">_etaKey(event)</xsl:attribute></xsl:if></input><br/>
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:when test="@type=6">
<xsl:choose>
<xsl:when test="@multiple='y'"><select name="{@name}[]" multiple="1" size="10"><xsl:if test="styles"><xsl:attribute name="style"><xsl:value-of select="styles"/></xsl:attribute></xsl:if><xsl:if test="onchange"><xsl:attribute name="onchange"><xsl:value-of select="onchange"/></xsl:attribute></xsl:if><xsl:if test="ref/none"><option value="0"><xsl:value-of select="ref/none"/></option></xsl:if>$V_STR_<xsl:value-of select="@name"/></select><br/></xsl:when>
<xsl:otherwise>
	<xsl:choose>
		<xsl:when test="child/table">
			<select name="{@name}" onchange="return chan(this.form,this.form.elements['{child/field}'],'select {child/field},{child/visual} from {child/table}  where {@name}=? order by {child/visual}',this.value);">
				<xsl:if test="ref/none"><option value="0"><xsl:value-of select="ref/none"/></option></xsl:if>				
				$V_STR_<xsl:value-of select="@name"/>
			</select><br/>
		</xsl:when>
		<xsl:otherwise>
			<select name="{@name}">
				<xsl:if test="child/table"><xsl:attribute name="onchange">return chan(this.form,this.form.elements['{child/table/field}'],'select {child/table/field},{child/table/visual} from {child/table/table}  where {@name}=? order by {child/table/visual}',this.value);</xsl:attribute></xsl:if>
				<xsl:if test="event!=''"><xsl:attribute name="{event}"><xsl:value-of select="action"/></xsl:attribute></xsl:if>
				<xsl:if test="styles"><xsl:attribute name="style"><xsl:value-of select="styles"/></xsl:attribute></xsl:if>
				<xsl:if test="onchange"><xsl:attribute name="onchange"><xsl:value-of select="onchange"/></xsl:attribute></xsl:if>
				<xsl:if test="ref/none"><option value="0"><xsl:value-of select="ref/none"/></option></xsl:if>
				
				$V_STR_<xsl:value-of select="@name"/>
			</select><br/>
		</xsl:otherwise>
	</xsl:choose>
	
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:when test="@type=13">
<xsl:choose>
<xsl:when test="@multiple='y'"><select name="{@name}[]" multiple="1" size="10"><xsl:if test="ref/none"><option value="0"><xsl:value-of select="ref/none"/></option></xsl:if>$V_STR_<xsl:value-of select="@name"/></select><br/></xsl:when>
<xsl:otherwise>
<select name="{@name}"><xsl:if test="event!=''"><xsl:attribute name="{event}"><xsl:value-of select="action"/></xsl:attribute></xsl:if><xsl:if test="ref/none"><option value="0"><xsl:value-of select="ref/none"/></option></xsl:if>$V_STR_<xsl:value-of select="@name"/></select><br/></xsl:otherwise>
</xsl:choose>
</xsl:when>

<xsl:when test="@type=7"><input type="hidden" name="{@name}" value="$V_{@name}"/>
<table><tr><td>
EOF;
if(!empty($IM_<xsl:value-of select="@name"/>[0]))
{
if(strchr($IM_<xsl:value-of select="@name"/>[0],".swf"))
{
   print "<xsl:text disable-output-escaping='yes'>&lt;</xsl:text>div style=\"width:600px\"<xsl:text disable-output-escaping='yes'>&gt;</xsl:text><xsl:text disable-output-escaping='yes'>&lt;</xsl:text>object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\"<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>param name=\"allowScriptAccess\" value=\"sameDomain\" /<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_<xsl:value-of select="@name"/>[0]\" /<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>param name=\"quality\" value=\"high\" /<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_<xsl:value-of select="@name"/>[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>
                                                 <xsl:text disable-output-escaping='yes'>&lt;</xsl:text>/object<xsl:text disable-output-escaping='yes'>&gt;</xsl:text><xsl:text disable-output-escaping='yes'>&lt;</xsl:text>/div<xsl:text disable-output-escaping='yes'>&gt;</xsl:text>";
}
else
{
$IM_<xsl:value-of select="@name"/>[0] = !empty($IM_<xsl:value-of select="@name"/>[0]) ? $IM_<xsl:value-of select="@name"/>[0]:0;
$IM_<xsl:value-of select="@name"/>[1] = !empty($IM_<xsl:value-of select="@name"/>[1]) ? $IM_<xsl:value-of select="@name"/>[1]:0;
$IM_<xsl:value-of select="@name"/>[2] = !empty($IM_<xsl:value-of select="@name"/>[2]) ? $IM_<xsl:value-of select="@name"/>[2]:0;
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_{@name}[0]" width="$IM_{@name}[1]" height="$IM_{@name}[2]"/>
EOF;
}
}

print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
</td>
<td><input type="file" name="NOT_{@name}" size="1"/><br/>
<input type="checkbox" name="CLR_{@name}" value="1"/>Сбросить карт.
<xsl:if test="@is_watermark"><br/><xsl:call-template name="set_watermark"/></xsl:if>
</td></tr></table>
</xsl:when>

<xsl:when test="@type=15"><input type="hidden" name="{@name}" value="$V_{@name}"/>
EOF;
if(!empty($IM_<xsl:value-of select="@name"/>[1])) $width = $IM_<xsl:value-of select="@name"/>[1];
else $width = '<xsl:value-of select="@width"/>';

if(!empty($IM_<xsl:value-of select="@name"/>[2])) $height = $IM_<xsl:value-of select="@name"/>[2];
else $height = '<xsl:value-of select="@height"/>';

$IM_<xsl:value-of select="@name"/>[0] = !empty($IM_<xsl:value-of select="@name"/>[0]) ? $IM_<xsl:value-of select="@name"/>[0]:0;
$IM_<xsl:value-of select="@name"/>[1] = !empty($IM_<xsl:value-of select="@name"/>[1]) ? $IM_<xsl:value-of select="@name"/>[1]:0;
$IM_<xsl:value-of select="@name"/>[2] = !empty($IM_<xsl:value-of select="@name"/>[2]) ? $IM_<xsl:value-of select="@name"/>[2]:0;

print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_{@name}[0]" width="$IM_{@name}[1]" height="$IM_{@name}[2]"/></td>
<td><input type="file" name="NOT_{@name}" size="1" disabled="1" /><br/>
<input type="checkbox" name="GEN_{@name}" value="1" checked="1" onClick="if(document.frm.GEN_{@name}.checked == '1') document.frm.NOT_{@name}.disabled='1'; else document.frm.NOT_{@name}.disabled='';" />Сгенерить из большой<br/>
<input type="checkbox" name="CLR_{@name}" value="1"/>Сбросить карт. <br/>
Ширина превью: <input type="text" name="WIDTH_{@name}" size="5" value="$width"/><br/>
Высота превью: <input type="text" name="HEIGHT_{@name}" size="5" value="$height"/><br/>
<xsl:if test="@is_watermark"><br/><xsl:call-template name="set_watermark"/></xsl:if>
</td></tr></table></xsl:when>
<xsl:when test="@type=8"><xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='checkbox' name='<xsl:value-of select="@name"/>' value='1' $V_<xsl:value-of select="@name"/>/<xsl:text disable-output-escaping="yes">&gt;</xsl:text><br/></xsl:when>
<xsl:when test="@type=10"><select name="{@name}">$V_STR_<xsl:value-of select="@name"/></select><br/></xsl:when>
<xsl:when test="@type=17"><select name="{@name}">$V_STR_<xsl:value-of select="@name"/></select><br/></xsl:when>
<xsl:when test="@type=12">
<!--<input type="text" name="{@name}" value="$V_{@name}"/>-->
<input type="hidden" id="{@name}" name="{@name}" value="$V_{@name}"/>
EOF;

if($V_<xsl:value-of select="@name"/>) $V_DAT_ = substr($V_<xsl:value-of select="@name"/>,8,2).".".substr($V_<xsl:value-of select="@name"/>,5,2).".".substr($V_<xsl:value-of select="@name"/>,0,4)." ".substr($V_<xsl:value-of select="@name"/>,11,2).":".substr($V_<xsl:value-of select="@name"/>,14,2);
else $V_DAT_ = '';

<xsl:if test="@calendar">
        <!--
        <img id="c_anc_{@name}" src="imgs/hi.gif" width="1" height="1" />
        <input type="image" src="i/c/cal.gif" width="34" class="button" onClick="return showCalendar(this,'{@name}');"/>
        <div id="c_div_{@name}" style="position:absolute;"></div>
        -->
        @print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
        <table>
        <tr><td><div id="DATE_{@name}">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_{@name}" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        <xsl:variable name="calsetup"><![CDATA[
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "]]><xsl:value-of select='@name'/><![CDATA[",
                       displayArea    :    "DATE_]]><xsl:value-of select='@name'/><![CDATA[",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_]]><xsl:value-of select='@name'/><![CDATA[",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>]]></xsl:variable>
        <xsl:value-of select="$calsetup" disable-output-escaping="yes"/>
</xsl:if><!--(YYYY-MM-DD)<br/>--></xsl:when>

<xsl:when test="@type=16"><input type="hidden" name="{@name}" value="$V_{@name}"/>
EOF;
$IM_<xsl:value-of select="@name"/>[0] = isset($IM_<xsl:value-of select="@name"/>[0]) ? $IM_<xsl:value-of select="@name"/>[0]:0;
$IM_<xsl:value-of select="@name"/>[1] = isset($IM_<xsl:value-of select="@name"/>[1]) ? $IM_<xsl:value-of select="@name"/>[1]:0;
$IM_<xsl:value-of select="@name"/>[2] = isset($IM_<xsl:value-of select="@name"/>[2]) ? $IM_<xsl:value-of select="@name"/>[2]:0;
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_{@name}[0]" width="$IM_{@name}[1]" height="$IM_{@name}[2]"/></td>
<td><input type="file" name="NOT_{@name}" size="1" /><br/>
<input type="checkbox" name="CLR_{@name}" value="1"/>Сбросить карт.
<xsl:if test="@is_watermark"><br/><xsl:call-template name="set_watermark"/></xsl:if>
</td></tr></table></xsl:when>
<xsl:when test="@type=8"><xsl:text disable-output-escaping="yes">&lt;</xsl:text>input type='checkbox' name='<xsl:value-of select="@name"/>' value='1' $V_<xsl:value-of select="@name"/>/<xsl:text disable-output-escaping="yes">&gt;</xsl:text><br/></xsl:when>
<xsl:when test="@type=10"><select name="{@name}">$V_STR_<xsl:value-of select="@name"/></select><br/></xsl:when>
<xsl:when test="@type=17"><select name="{@name}">$V_STR_<xsl:value-of select="@name"/></select><br/></xsl:when>
<xsl:when test="@type=12">
<!--<input type="text" name="{@name}" value="$V_{@name}"/>-->
<input type="hidden" id="{@name}" name="{@name}" value="$V_{@name}"/>
<xsl:if test="@calendar">
        <!--
        <img id="c_anc_{@name}" src="imgs/hi.gif" width="1" height="1" />
        <input type="image" src="i/c/cal.gif" width="34" class="button" onClick="return showCalendar(this,'{@name}');"/>
        <div id="c_div_{@name}" style="position:absolute;"></div>
        -->
        <table>
        <tr><td><div id="DATE_{@name}"></div></td>
        <td><img src="img/img.gif" id="f_trigger_{@name}" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        <xsl:variable name="calsetup"><![CDATA[
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "]]><xsl:value-of select='@name'/><![CDATA[",
                       displayArea    :    "DATE_]]><xsl:value-of select='@name'/><![CDATA[",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_]]><xsl:value-of select='@name'/><![CDATA[",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>]]></xsl:variable>
        <xsl:value-of select="$calsetup" disable-output-escaping="yes"/>
</xsl:if><!--(YYYY-MM-DD)<br/>--></xsl:when>

<xsl:when test="@type=14"><input type="hidden" name="{@name}" value="$V_{@name}"/>
<table><tr><td><input type="file" name="NOT_{@name}" size="1"/><br/><input type="checkbox" name="CLR_{@name}" value="1"/>Сбросить файл.</td>
<td><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></td><td>size=$IM_<xsl:value-of select="@name"/>[1]<br/>/images/$VIRTUAL_IMAGE_PATH$IM_<xsl:value-of select="@name"/>[0]
</td></tr></table></xsl:when>
</xsl:choose></td></tr><xsl:if test="ifsection">};
};
print qq{</xsl:if></xsl:template>


<!--   Joined -->
<xsl:template match="table/joined" mode="events" xml:space="preserve">
if(!isset($_REQUEST['e<xsl:value-of select="position()"/>']))$_REQUEST['e<xsl:value-of select="position()"/>']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {
<xsl:if test="col[@type=11]">
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id'],$id);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>?',$_REQUEST['id'],$ORDERING);</xsl:if>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('delete from <xsl:apply-templates select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id'],$id);
<xsl:value-of select="postdeleteevent" disable-output-escaping="yes"/>
 }
$_REQUEST['e']='ED';
$visible=0;
}

<xsl:if test="col/@type=11">
if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'UP')
{
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id'],$_REQUEST['iid']);
if($ORDERING<xsl:text disable-output-escaping="yes">&gt;</xsl:text>1)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>+1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/>=?',$_REQUEST['id'],$ORDERING-1);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id'],$_REQUEST['iid']);
}
$_REQUEST['e']='ED';
}

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'DN')
{
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id'],$_REQUEST['iid']);
$MAXORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:value-of select="@name"/>');
if($ORDERING<xsl:text disable-output-escaping="yes">&lt;</xsl:text>$MAXORDERING)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/>=?',$_REQUEST['id'],$ORDERING+1);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>+1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id'],$_REQUEST['iid']);
}
$_REQUEST['e']='ED';
}
</xsl:if>


if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'Изменить')
{
<xsl:value-of select="preupdateevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preinsert"/>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]" mode="update"><xsl:with-param name="off">2</xsl:with-param></xsl:apply-templates> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]" mode="form"/>,$_REQUEST['id'],$_REQUEST['iid']);
<xsl:value-of select="postupdateevent" disable-output-escaping="yes"/>
$_REQUEST['e']='ED';
};


if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'Добавить')
{
<xsl:if test="col/@type=11">
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where <xsl:apply-templates select="../col[@type=0]/@name"/>=?',$_REQUEST['id']);
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']++;
</xsl:if>
<xsl:choose>
<xsl:when test="col[@primary='y' and @editable='y']">
$_REQUEST['iid']=$_REQUEST['<xsl:value-of select="col[@primary='y' and @editable='y']/@name"/>'];
</xsl:when>
<xsl:otherwise>
$_REQUEST['iid']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetSequence('<xsl:apply-templates select="@name"/>');
</xsl:otherwise>
</xsl:choose>
<xsl:value-of select="preinsertevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preinsert"/>
<!--
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="../col[@type=0]" mode="name_insert"/>,<xsl:apply-templates select="col" mode="name_insert"/>) values (<xsl:apply-templates select="../col[@type=0]|col" mode="vopros"/>)',$_REQUEST['id'],$_REQUEST['iid']<xsl:if test="col[not(@primary)]">,<xsl:apply-templates select="col[not(@primary)]" mode="form"/></xsl:if>);
-->
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="../col[@primary='y']" mode="name_insert"/>,<xsl:apply-templates select="col" mode="name_insert"/>) values (<xsl:apply-templates select="../col[@primary='y']|col" mode="vopros"/>)',$_REQUEST['id'],$_REQUEST['iid']<xsl:if test="col[not(@primary)]">,<xsl:apply-templates select="col[not(@primary)]" mode="form"/></xsl:if>);
$_REQUEST['e']='ED';
<xsl:value-of select="postinsertevent" disable-output-escaping="yes"/>
$visible=0;
}

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'ED')
{
list (<xsl:apply-templates select="col[@type!=11]" mode="vars"/>)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="col[@type!=11]" mode="name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id'],$_REQUEST['iid']);
<xsl:value-of select="preeditevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preedit"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Редактирование - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="{../@name}.php#f{position()}" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="e" value="ED"/>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="iid"><xsl:attribute name="value">{$_REQUEST['iid']}</xsl:attribute></input>
<xsl:if test="@type"><input type="hidden" name="type" value="{@type}"/></xsl:if>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'"/>';

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e{position()}" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e{position()}" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e{position()}" value="Отменить" class="gbt bcancel"/>
</td></tr>
<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]|panel|pseudocol" mode="edit"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e{position()}" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e{position()}" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e{position()}" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table><br/>
EOF;

<xsl:variable name="position" select="position()+1"/>

<xsl:apply-templates select="joined">
<xsl:with-param name="position" select="$position"/>
</xsl:apply-templates>

$visible=0;
}

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'Новый')
{
list(<xsl:apply-templates select="col" mode="vars"/>)=array(<xsl:apply-templates select="col" mode="vars_init"/>);
<xsl:value-of select="preaddevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preadd"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Добавление - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="{../@name}.php#f{position()}" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="e" value="ED"/>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e{position()}" value="Добавить" class="gbt badd"/> 
<input type="submit" name="e{position()}" value="Отменить" class="gbt bcancel"/>
</td></tr>
<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]|panel|pseudocol" mode="edit"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e{position()}" value="Добавить" class="gbt badd"/> 
<input type="submit" name="e{position()}" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table>
EOF;
$visible=0;
}
</xsl:template>


<!--New for joined -->
<xsl:template match="joined/joined" mode="events" xml:space="preserve">
<xsl:variable name="position" select="@position"/>
<!--<xsl:param name="position"/>-->
if(!isset($_REQUEST['ell<xsl:value-of select="$position"/>']))$_REQUEST['ell<xsl:value-of select="$position"/>']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('ell<xsl:value-of select="$position"/>') == 'Удалить') and is_array($_REQUEST['bid']))
{
foreach ($_REQUEST['bid'] as $id)
 {
<xsl:if test="col[@type=11]">
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['iid'],$id);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>?',$_REQUEST['iid'],$ORDERING);</xsl:if>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('delete from <xsl:apply-templates select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['iid'],$id);
<xsl:value-of select="postdeleteevent" disable-output-escaping="yes"/>
 }


$pos = <xsl:value-of select="$position"/>;
$pos = $pos -1;
//$_REQUEST['e<xsl:value-of select="$position"/>']='ED';
echo '<xsl:text disable-output-escaping="yes">&lt;</xsl:text>meta http-equiv="Refresh" content="1; url=<xsl:value-of select="../../@name"/>.php?e'.$pos.'=ED&amp;id='.$_REQUEST['id'].'&amp;iid='.$_REQUEST['iid'].'"<xsl:text disable-output-escaping="yes">&gt;</xsl:text>';

$visible=0;
}

<xsl:if test="col/@type=11">
if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('ell<xsl:value-of select="$position"/>') == 'UP')
{
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['iid'],$_REQUEST['bid']);
if($ORDERING<xsl:text disable-output-escaping="yes">&gt;</xsl:text>1)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>+1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/>=?',$_REQUEST['iid'],$ORDERING-1);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['iid'],$_REQUEST['bid']);
}
$pos = <xsl:value-of select="$position"/>;
$pos = $pos -1;

//$_REQUEST['e']='ED';
echo '<xsl:text disable-output-escaping="yes">&lt;</xsl:text>meta http-equiv="Refresh" content="1; url=<xsl:value-of select="../../@name"/>.php?e'.$pos.'=ED&amp;id='.$_REQUEST['id'].'&amp;iid='.$_REQUEST['iid'].'"<xsl:text disable-output-escaping="yes">&gt;</xsl:text>';

$visible=0;
}

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('ell<xsl:value-of select="$position"/>') == 'DN')
{
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['iid'],$_REQUEST['bid']);
$MAXORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:value-of select="@name"/>');
if($ORDERING<xsl:text disable-output-escaping="yes">&lt;</xsl:text>$MAXORDERING)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/>=?',$_REQUEST['iid'],$ORDERING+1);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>+1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['iid'],$_REQUEST['bid']);
}
//$_REQUEST['e']='ED';
$pos = <xsl:value-of select="$position"/>;
$pos = $pos -1;
echo '<xsl:text disable-output-escaping="yes">&lt;</xsl:text>meta http-equiv="Refresh" content="1; url=<xsl:value-of select="../../@name"/>.php?e'.$pos.'=ED&amp;id='.$_REQUEST['id'].'&amp;iid='.$_REQUEST['iid'].'"<xsl:text disable-output-escaping="yes">&gt;</xsl:text>';

$visible=0;
}
</xsl:if>


if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('ell<xsl:value-of select="$position"/>') == 'Изменить')
{
<xsl:apply-templates select="col" mode="preinsert"/>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]" mode="update"><xsl:with-param name="off">2</xsl:with-param></xsl:apply-templates> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]" mode="form"/>,$_REQUEST['iid'],$_REQUEST['bid']);
<xsl:value-of select="postupdateevent" disable-output-escaping="yes"/>

$visible=0;
$pos = <xsl:value-of select="$position"/>;
$pos = $pos -1;
//$_REQUEST['e<xsl:value-of select="$position"/>']='ED';
echo '<xsl:text disable-output-escaping="yes">&lt;</xsl:text>meta http-equiv="Refresh" content="1; url=<xsl:value-of select="../../@name"/>.php?e'.$pos.'=ED&amp;id='.$_REQUEST['id'].'&amp;iid='.$_REQUEST['iid'].'"<xsl:text disable-output-escaping="yes">&gt;</xsl:text>';
};


if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('ell<xsl:value-of select="$position"/>') == 'Добавить')
{
<xsl:if test="col/@type=11">
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where <xsl:apply-templates select="../col[@type=0]/@name"/>=?',$_REQUEST['iid']);
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']++;
</xsl:if>
<xsl:choose>
<xsl:when test="col[@primary='y' and @editable='y']">
$_REQUEST['bid']=$_REQUEST['<xsl:value-of select="col[@primary='y' and @editable='y']/@name"/>'];
</xsl:when>
<xsl:otherwise>
$_REQUEST['bid']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetSequence('<xsl:apply-templates select="@name"/>');
</xsl:otherwise>
</xsl:choose>
<xsl:value-of select="preinsertevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preinsert"/>
<!--
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="../col[@type=0]" mode="name_insert"/>,<xsl:apply-templates select="col" mode="name_insert"/>) values (<xsl:apply-templates select="../col[@type=0]|col" mode="vopros"/>)',$_REQUEST['id'],$_REQUEST['iid']<xsl:if test="col[not(@primary)]">,<xsl:apply-templates select="col[not(@primary)]" mode="form"/></xsl:if>);
-->
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="../col[@primary='y']" mode="name_insert"/>,<xsl:apply-templates select="col" mode="name_insert"/>) values (<xsl:apply-templates select="../col[@primary='y']|col" mode="vopros"/>)',$_REQUEST['iid'],$_REQUEST['bid']<xsl:if test="col[not(@primary)]">,<xsl:apply-templates select="col[not(@primary)]" mode="form"/></xsl:if>);

<xsl:value-of select="postinsertevent" disable-output-escaping="yes"/>
$visible=0;

$pos = <xsl:value-of select="$position"/>;
$pos = $pos -1;
//$_REQUEST['e<xsl:value-of select="$position"/>']='ED';
echo '<xsl:text disable-output-escaping="yes">&lt;</xsl:text>meta http-equiv="Refresh" content="1; url=<xsl:value-of select="../../@name"/>.php?e'.$pos.'=ED&amp;id='.$_REQUEST['id'].'&amp;iid='.$_REQUEST['iid'].'"<xsl:text disable-output-escaping="yes">&gt;</xsl:text>';

}

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('ell<xsl:value-of select="$position"/>') == 'ED')
{
list (<xsl:apply-templates select="col[@type!=11]" mode="vars"/>)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="col[@type!=11]" mode="name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['iid'],$_REQUEST['bid']);
<xsl:value-of select="preeditevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preedit"/>
$pos = <xsl:value-of select="$position"/>;
$pos = $pos-1;

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Редактирование - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="{../../@name}.php#f{$position}" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
EOF;
echo '<input type="hidden" name="ell'.$pos.'" value="ED"/>';
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="iid"><xsl:attribute name="value">{$_REQUEST['iid']}</xsl:attribute></input>
<input type="hidden" name="bid"><xsl:attribute name="value">{$_REQUEST['bid']}</xsl:attribute></input>
<xsl:if test="@type"><input type="hidden" name="type" value="{@type}"/></xsl:if>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'"/>';

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="ell{$position}" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="ell{$position}" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="ell{$position}" value="Отменить" class="gbt bcancel"/>
</td></tr>
<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]|panel|pseudocol" mode="edit"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="ell{$position}" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="ell{$position}" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="ell{$position}" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table><br/>
EOF;
$visible=0;
}

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('ell<xsl:value-of select="$position"/>') == 'Новый')
{
list(<xsl:apply-templates select="col" mode="vars"/>)=array(<xsl:apply-templates select="col" mode="vars_init"/>);
<xsl:value-of select="preaddevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preadd"/>
$pos = <xsl:value-of select="$position"/>;
$pos = $pos-1;

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Добавление - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="{../../@name}.php#f{$position}" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
EOF;
echo '<input type="hidden" name="ell'.$pos.'" value="ED"/>';
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<input type="hidden" name="iid"><xsl:attribute name="value">{$_REQUEST['iid']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="ell{$position}" value="Добавить" class="gbt badd"/>
<input type="submit" name="ell{$position}" value="Отменить" class="gbt bcancel"/>
</td></tr>
<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]|panel|pseudocol" mode="edit"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="ell{$position}" value="Добавить" class="gbt badd"/>
<input type="submit" name="ell{$position}" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table>
EOF;
$visible=0;
}
</xsl:template>
<!--New for joined -->



<xsl:template match="table/joined[@multilanguage='y']" mode="events" xml:space="preserve">
if(!isset($_REQUEST['e<xsl:value-of select="position()"/>']))$_REQUEST['e<xsl:value-of select="position()"/>']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {
<xsl:if test="col[@type=11]">
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$id,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>? and CMF_LANG_ID=?',$_REQUEST['id'],$ORDERING,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);</xsl:if>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('delete from <xsl:apply-templates select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$id,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
<xsl:value-of select="postdeleteevent" disable-output-escaping="yes"/>
 }
$_REQUEST['e']='ED';
$visible=0;
}

<xsl:if test="col/@type=11">
if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'UP')
{
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$_REQUEST['iid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
if($ORDERING<xsl:text disable-output-escaping="yes">&gt;</xsl:text>1)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>+1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$ORDERING-1,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$_REQUEST['iid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
}
$_REQUEST['e']='ED';
}

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'DN')
{
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$_REQUEST['iid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$MAXORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:value-of select="@name"/> where CMF_LANG_ID=?',$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
if($ORDERING<xsl:text disable-output-escaping="yes">&lt;</xsl:text>$MAXORDERING)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@type=11]/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$ORDERING+1,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>+1 where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$_REQUEST['iid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
}
$_REQUEST['e']='ED';
}
</xsl:if>


if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'Изменить')
{
<xsl:apply-templates select="col" mode="preinsert"/>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]" mode="update"><xsl:with-param name="off">2</xsl:with-param></xsl:apply-templates> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]" mode="form"/>,$_REQUEST['id'],$_REQUEST['iid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
<xsl:value-of select="postupdateevent" disable-output-escaping="yes"/>
$_REQUEST['e']='ED';
};


if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'Добавить')
{
<xsl:if test="col/@type=11">
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where <xsl:apply-templates select="../col[@type=0]/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']++;
</xsl:if>
<xsl:choose>
<xsl:when test="col[@primary='y' and @editable='y']">
$_REQUEST['iid']=$_REQUEST['<xsl:value-of select="col[@primary='y' and @editable='y']/@name"/>'];
</xsl:when>
<xsl:otherwise>
$_REQUEST['iid']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetSequence('<xsl:apply-templates select="@name"/>');
</xsl:otherwise>
</xsl:choose>
<xsl:value-of select="preinsertevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preinsert"/>
<!--
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="../col[@type=0]" mode="name_insert"/>,<xsl:apply-templates select="col" mode="name_insert"/>,CMF_LANG_ID) values (<xsl:apply-templates select="../col[@type=0]|col" mode="vopros"/>,?)',$_REQUEST['id'],$_REQUEST['iid']<xsl:if test="col[not(@primary)]">,<xsl:apply-templates select="col[not(@primary)]" mode="form"/></xsl:if>,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
-->
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="../col[@primary='y']" mode="name_insert"/>,<xsl:apply-templates select="col" mode="name_insert"/>,CMF_LANG_ID) values (<xsl:apply-templates select="../col[@primary='y']|col" mode="vopros"/>,?)',$_REQUEST['id'],$_REQUEST['iid']<xsl:if test="col[not(@primary)]">,<xsl:apply-templates select="col[not(@primary)]" mode="form"/></xsl:if>,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$_REQUEST['e']='ED';
<xsl:if test="@forcedtranslation='y'">
#=========== насильный перевод
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('
insert into <xsl:apply-templates select="@name" /> (<xsl:apply-templates select="../col[@primary='y']" mode="name_insert"/>,<xsl:apply-templates select="col[not(@isstate)]" mode="name_insert"/><xsl:if test="col[@isstate='y']">,<xsl:apply-templates select="col[@isstate='y']" mode="name_insert"/></xsl:if>,CMF_LANG_ID)
select <xsl:apply-templates select="../col[@primary='y']" mode="name_insert"><xsl:with-param name="tableName">T</xsl:with-param></xsl:apply-templates>,<xsl:apply-templates select="col[not(@isstate)]" mode="name_insert"><xsl:with-param name="tableName">T</xsl:with-param></xsl:apply-templates><xsl:if test="col[@isstate='y']">,<xsl:apply-templates select="col[@isstate='y']" mode="zero" /></xsl:if>,CL.CMF_LANG_ID
from <xsl:apply-templates select="@name" /> T,CMF_LANG CL
where T.<xsl:apply-templates select="col[@primary='y']/@name"/>=? and T.CMF_LANG_ID=? and CL.CMF_LANG_ID!=?',$_REQUEST['iid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
#=========== /насильный перевод
</xsl:if>
<xsl:value-of select="postinsertevent" disable-output-escaping="yes"/>
$visible=0;
}

#=========== перевод
if($_REQUEST['e<xsl:value-of select="position()"/>'] == 'Продублировать')
{
        if(is_array($_REQUEST['lang']))
        {
                foreach ($_REQUEST['lang'] as $langId=<xsl:text disable-output-escaping="yes">&gt;</xsl:text>$state)
                {
                        $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('
                                insert into <xsl:apply-templates select="@name" /> (CMF_LANG_ID,<xsl:apply-templates select="../col[@primary='y']/@name" mode="name_insert"/>,<xsl:apply-templates select="col[not(@isstate)]" mode="name_insert"/><xsl:if test="col[@isstate='y']">,<xsl:apply-templates select="col[@isstate='y']" mode="name_insert"/></xsl:if>)
                                select ?,<xsl:apply-templates select="../col[@primary='y']/@name" mode="name_insert"/>,?,<xsl:apply-templates select="col[not(@primary)][not(@isstate)]" mode="name_insert"/><xsl:if test="col[@isstate='y']">,<xsl:apply-templates select="col[@isstate='y']" mode="zero"/></xsl:if>
                                from <xsl:apply-templates select="@name" /> where <xsl:apply-templates select="col[@primary='y']/@name" />=? and CMF_LANG_ID=?',$langId,$_REQUEST['iid'],$_REQUEST['iid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
                }
        }
$_REQUEST['e<xsl:value-of select="position()"/>']='ED';
};

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e1') == 'Языки')
{
        $sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute("
                select L.CMF_LANG_ID,L.NAME,if(T.<xsl:apply-templates select="col[@primary='y']/@name" /><xsl:text disable-output-escaping="yes">&gt;</xsl:text>0,1,0) from CMF_LANG L
                left join <xsl:apply-templates select="@name" /> T on (L.CMF_LANG_ID=T.CMF_LANG_ID and T.<xsl:apply-templates select="col[@primary='y']/@name" />=?)
                where L.STATUS=1
                order by L.ORDERING asc
        ",$_REQUEST['iid']);
        
        @print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="{../@name}.php" ENCTYPE="multipart/form-data">
EOF;

        while(list($V_CMF_LANG_ID,$V_NAME,$V_CHECKED)=mysql_fetch_array($sth, MYSQL_NUM))
        {
                $inputTag=($V_CHECKED==1 ? '<input type="checkbox" checked="checked" disabled="disabled" />' : '<input type="checkbox" name="lang['.$V_CMF_LANG_ID.']" />');
                @print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
                <tr bgcolor="#FFFFFF"><th width="1%">
                $inputTag
                </th>
                <td>
                $V_NAME
                </td>
                </tr>
EOF;
        }

        
        @print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
        <tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
        <input type="submit" name="e{position()}" value="Продублировать" class="gbt bdublicate" /><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>
        <input type="button" value="Отменить" class="gbt bcancel" onclick="javascript:history.back();"/>
        <input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
        <input type="hidden" name="s"><xsl:attribute name="value">{$REQUEST['s']}</xsl:attribute></input>
        <input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
        <input type="hidden" name="iid"><xsl:attribute name="value">{$_REQUEST['iid']}</xsl:attribute></input>
        </td>
        </tr>
</form>
</table>
EOF;

$_REQUEST['e']='';
$visible=0;
}
#=========== /перевод

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'ED')
{
list (<xsl:apply-templates select="col[@type!=11]" mode="vars"/>)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="col[@type!=11]" mode="name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="../col[@primary='y']/@name"/>=? and <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$_REQUEST['iid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
<xsl:value-of select="preeditevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preedit"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Редактирование - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="{../@name}.php#f{position()}" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="e" value="ED"/>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="iid"><xsl:attribute name="value">{$_REQUEST['iid']}</xsl:attribute></input>
<xsl:if test="@type"><input type="hidden" name="type" value="{@type}"/></xsl:if>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e{position()}" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e{position()}" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e{position()}" value="Языки" class="gbt blanguages" /><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>
<input type="submit" name="e{position()}" value="Отменить" class="gbt bcancel"/>
</td></tr>
<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]|panel|pseudocol" mode="edit"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e{position()}" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e{position()}" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e{position()}" value="Языки" class="gbt blanguages" /><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>
<input type="submit" name="e{position()}" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table><br/>
EOF;
$visible=0;
}

if($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('e<xsl:value-of select="position()"/>') == 'Новый')
{
list(<xsl:apply-templates select="col" mode="vars"/>)=array(<xsl:apply-templates select="col" mode="vars_init"/>);
<xsl:value-of select="preaddevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preadd"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Добавление - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="{../@name}.php#f{position()}" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="e" value="ED"/>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e{position()}" value="Добавить" class="gbt badd"/> 
<input type="submit" name="e{position()}" value="Отменить" class="gbt bcancel"/>
</td></tr>
<xsl:apply-templates select="col[@type!=11 and (not(@primary) or @editable='y')]|panel|pseudocol" mode="edit"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e{position()}" value="Добавить" class="gbt badd"/> 
<input type="submit" name="e{position()}" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table>
EOF;
$visible=0;
}
</xsl:template>

<xsl:template match="table/joined" xml:space="preserve">
<xsl:variable name="p">&amp;id={$_REQUEST['id']}<xsl:if test="../@letter">&amp;l={$_REQUEST['l']}</xsl:if><xsl:if test="../@parentscript">&amp;pid={$_REQUEST['pid']}</xsl:if></xsl:variable>
<xsl:if test="ifsection"><xsl:value-of select="ifsection" disable-output-escaping="yes"/># Секция появляется только в случае выполнения условия
{</xsl:if>
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a name="f{position()}"></a><h3 class="h3"><xsl:apply-templates select="name"/></h3>
EOF;

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="{../@name}.php#f{position()}" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="{count(col[@type!=11][@visuality='y'])+2}">
<input type="submit" name="e{position()}" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1"/><xsl:apply-templates select="extrakey"/>
<input type="submit" name="e{position()}" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
</td></tr>
EOF;
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="name"/> from <xsl:apply-templates select="@name"/> where <xsl:apply-templates select="../col[@primary]/@name"/>=? <xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text><xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text><xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose>',$_REQUEST['id']);
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');"/></td><xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="head"/><td></td></tr>
EOF;
while(list(<xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="vars"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{<xsl:value-of select="extrainlist" disable-output-escaping="yes"/><xsl:apply-templates select="col[@visuality='y']" mode="previsible"/>
<xsl:if test="col[@isstate='y']">if($V_<xsl:value-of select="col[@isstate='y']/@name"/>){$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#FFFFFF';} else {$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#a0a0a0';}</xsl:if>
<xsl:value-of select="postvisible" disable-output-escaping="yes"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><xsl:if test="col[@isstate='y']"><xsl:attribute name="bgcolor">$V_<xsl:value-of select="col[@isstate='y']/@name"/></xsl:attribute></xsl:if>
<td><input type="checkbox" name="iid[]" value="$V_{col[@primary='y']/@name}"/></td>
<xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="varsprint"/><td nowrap="">
<xsl:if test="col/@type=11"><a href="{../@name}.php?e{position()}=UP&amp;iid=$V_{col[@primary='y']/@name}{$p}#f{position()}"><img src="i/up.gif" border="0"/></a>
<a href="{../@name}.php?e{position()}=DN&amp;iid=$V_{col[@primary='y']/@name}{$p}#f{position()}"><img src="i/dn.gif" border="0"/></a></xsl:if>
<a href="{../@name}.php?e{position()}=ED&amp;iid=$V_{col[@primary='y']/@name}{$p}"><img src="i/ed.gif" border="0" title="Изменить"/></a>
<xsl:apply-templates select="child_script"/></td></tr>
EOF;
$visible=0;
}
print '</form></table>';<xsl:if test="ifsection">
}</xsl:if>
</xsl:template>

<!-- New for joined -->
<xsl:template match="joined/joined" xml:space="preserve">
<!--<xsl:param name="position"/>-->
<xsl:variable name="position" select="@position"/>

$pos = <xsl:value-of select="$position"/>;
$pos = $pos -1;
<xsl:variable name="p">&amp;id={$_REQUEST['id']}&amp;iid={$_REQUEST['iid']}<xsl:if test="../@letter">&amp;l={$_REQUEST['l']}</xsl:if><xsl:if test="../@parentscript">&amp;pid={$_REQUEST['pid']}</xsl:if></xsl:variable>
<xsl:if test="ifsection"><xsl:value-of select="ifsection" disable-output-escaping="yes"/># Секция появляется только в случае выполнения условия
{</xsl:if>
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a name="f{$position}"></a><h3 class="h3"><xsl:apply-templates select="name"/></h3>
EOF;

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="{../../@name}.php#f{$position}" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="{count(col[@type!=11][@visuality='y'])+2}">
<input type="submit" name="ell{$position}" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1"/><xsl:apply-templates select="extrakey"/>
<input type="submit" name="ell{$position}" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<input type="hidden" name="iid"><xsl:attribute name="value">{$_REQUEST['iid']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
</td></tr>
EOF;
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="name"/> from <xsl:apply-templates select="@name"/> where <xsl:apply-templates select="../col[@primary]/@name"/>=? <xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text><xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text><xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose>',$_REQUEST['iid']);
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[bid]');"/></td><xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="head"/><td></td></tr>
EOF;
while(list(<xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="vars"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{<xsl:value-of select="extrainlist" disable-output-escaping="yes"/><xsl:apply-templates select="col[@visuality='y']" mode="previsible"/>
<xsl:if test="col[@isstate='y']">if($V_<xsl:value-of select="col[@isstate='y']/@name"/>){$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#FFFFFF';} else {$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#a0a0a0';}</xsl:if>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><xsl:if test="col[@isstate='y']"><xsl:attribute name="bgcolor">$V_<xsl:value-of select="col[@isstate='y']/@name"/></xsl:attribute></xsl:if>
<td><input type="checkbox" name="bid[]" value="$V_{col[@primary='y']/@name}"/></td>
<xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="varsprint"/><td nowrap="">
<xsl:if test="col/@type=11"><a href="{../../@name}.php?ell{$position}=UP&amp;bid=$V_{col[@primary='y']/@name}{$p}#f{$position}"><img src="i/up.gif" border="0"/></a>
<a href="{../../@name}.php?ell{$position}=DN&amp;bid=$V_{col[@primary='y']/@name}{$p}#f{$position}"><img src="i/dn.gif" border="0"/></a></xsl:if>
<a href="{../../@name}.php?ell{$position}=ED&amp;bid=$V_{col[@primary='y']/@name}{$p}"><img src="i/ed.gif" border="0" title="Изменить"/></a>
<xsl:apply-templates select="child_script"/></td></tr>
EOF;
$visible=0;
}
print '</form></table>';<xsl:if test="ifsection">
}</xsl:if>
</xsl:template>
<!-- New for joined -->


<xsl:template match="table/joined[@multilanguage='y']" xml:space="preserve">
<xsl:variable name="p">&amp;id={$_REQUEST['id']}<xsl:if test="../@letter">&amp;l={$_REQUEST['l']}</xsl:if><xsl:if test="../@parentscript">&amp;pid={$_REQUEST['pid']}</xsl:if></xsl:variable>
<xsl:if test="ifsection"><xsl:value-of select="ifsection" disable-output-escaping="yes"/># Секция появляется только в случае выполнения условия
{</xsl:if>
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a name="f{position()}"></a><h3 class="h3"><xsl:apply-templates select="name"/></h3>
EOF;

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="{../@name}.php#f{position()}" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="{count(col[@type!=11][@visuality='y'])+2}">

<input type="submit" name="e{position()}" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1"/><xsl:apply-templates select="extrakey"/>

<input type="submit" name="e{position()}" onclick="return dl();" value="Удалить" class="gbt bdel" />

<xsl:call-template name="multilanguage_mark" />
<xsl:if test="@forcedtranslation='y'"><xsl:call-template name="forcedtranslation_mark" /></xsl:if>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
<xsl:if test="../@parentscript"><input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="../@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
</td></tr>
EOF;
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="name"/> from <xsl:apply-templates select="@name"/> where <xsl:apply-templates select="../col[@primary]/@name"/>=? and CMF_LANG_ID=?<xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text><xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text><xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose>',$_REQUEST['id'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');"/></td><xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="head"/><td></td></tr>
EOF;
while(list(<xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="vars"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{<xsl:value-of select="extrainlist" disable-output-escaping="yes"/><xsl:apply-templates select="col[@visuality='y']" mode="previsible"/>
<xsl:if test="col[@isstate='y']">if($V_<xsl:value-of select="col[@isstate='y']/@name"/>){$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#FFFFFF';} else {$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#a0a0a0';}</xsl:if>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><xsl:if test="col[@isstate='y']"><xsl:attribute name="bgcolor">$V_<xsl:value-of select="col[@isstate='y']/@name"/></xsl:attribute></xsl:if>
<td><input type="checkbox" name="iid[]" value="$V_{col[@primary='y']/@name}"/></td>
<xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="varsprint"/><td nowrap="">
<xsl:if test="col/@type=11"><a href="{../@name}.php?e{position()}=UP&amp;iid=$V_{col[@primary='y']/@name}{$p}#f{position()}"><img src="i/up.gif" border="0"/></a>
<a href="{../@name}.php?e{position()}=DN&amp;iid=$V_{col[@primary='y']/@name}{$p}#f{position()}"><img src="i/dn.gif" border="0"/></a></xsl:if>
<a href="{../@name}.php?e{position()}=ED&amp;iid=$V_{col[@primary='y']/@name}{$p}"><img src="i/ed.gif" border="0" title="Изменить"/></a>
<xsl:apply-templates select="child_script"/></td></tr>
EOF;
}
print '</form></table>';<xsl:if test="ifsection">
}</xsl:if>
</xsl:template>

<!-- Табы -->
<xsl:template match="tabs/tab" mode="list">
<xsl:choose>
<xsl:when test="@id='1'"><li><a href="#" rel="tab{@id}" class="selected"><xsl:value-of select="name"/></a></li></xsl:when>
<xsl:otherwise><li><a href="#" rel="tab{@id}"><xsl:value-of select="name"/></a></li></xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="tabs/tab" mode="content_add">
<xsl:variable name="id" select="@id"/>
<div id="tab{$id}" class="tabcontent">
<table cellspacing="1" cellpadding="5" border="0" bgcolor="#cccccc" class="f">
<xsl:apply-templates select="../../col[@tabid=$id][not(@primary) and not(@parent) and @type!=11 and not(@isrealstate) and not(@internal)]|../../panel[@tabid=$id]" mode="edit"/>
</table>
</div>
</xsl:template>

<xsl:template match="tabs/tab" mode="content_child_add">
<xsl:variable name="id" select="@id"/>
<div id="tab{$id}" class="tabcontent">
<table cellspacing="1" cellpadding="5" border="0" bgcolor="#cccccc" class="f">
<xsl:apply-templates select="../../col[@tabid=$id][not(@primary) and not(@parent) and not(@parentfilt) and not(@childfilt) and @type!=11 and not(@internal)]|../../panel[@tabid=$id]" mode="edit"/>
</table>
</div>
</xsl:template>

<xsl:template match="tabs/tab" mode="content_child">
<xsl:variable name="id" select="@id"/>
<div id="tab{$id}" class="tabcontent">
<table cellspacing="1" cellpadding="5" border="0" bgcolor="#cccccc" class="f">
<xsl:apply-templates select="../../col[@tabid=$id][not(@primary) and not(@parent) and not(@parentfilt) and not(@childfilt) and @type!=11 and not(@internal)]|../../panel[@tabid=$id]|../../pseudocol[@tabid=$id]" mode="edit"/>
</table>
</div>
</xsl:template>

<xsl:template match="tabs/tab" mode="content_edit">
<xsl:variable name="id" select="@id"/>
<div id="tab{$id}" class="tabcontent">
<table cellspacing="1" cellpadding="5" border="0" bgcolor="#cccccc" class="f">
<xsl:apply-templates select="../../col[@tabid=$id][not(@primary) and not(@parent) and @type!=11 and not(@isrealstate) and not(@isstate) and not(@internal)]|../../panel[@tabid=$id]|../../pseudocol[@tabid=$id]" mode="edit"/>
</table>
</div>
</xsl:template>
<!-- Табы -->

</xsl:stylesheet>
