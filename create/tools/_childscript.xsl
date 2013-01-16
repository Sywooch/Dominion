<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml"  omit-xml-declaration="yes" encoding="UTF-8"/>

<xsl:template match="config/table[@parentscript]" xml:space="preserve">-----------------------|scripts/<xsl:value-of select="@name"/>.php|<xsl:variable name="parentTBL" select="/config/table[@name=current()/@parentscript]"/>
<xsl:text disable-output-escaping="yes">&lt;</xsl:text>? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('<xsl:choose><xsl:when test="@article"><xsl:value-of select="@article"/></xsl:when><xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise></xsl:choose>');
session_set_cookie_params($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetRights()) {header('Location: login.php'); exit;}

<xsl:value-of select="extraevents_top" disable-output-escaping="yes"/>

$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>HeaderNoCache();
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>makeCookieActions();

<xsl:if test="preheader">
	<xsl:value-of select="preheader" disable-output-escaping="yes"/>
</xsl:if>

$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>MakeCommonHeader();
$visible=1;
<xsl:if test="@imagepath">$VIRTUAL_IMAGE_PATH="<xsl:value-of select="@imagepath"/>";</xsl:if>

<xsl:apply-templates select="col[@type=10]" mode="enumcreate"/>
<xsl:apply-templates select="joined/col[@type=10]" mode="enumcreate"/>
<xsl:apply-templates select="col[@type=17]" mode="enumcreate"/>
<xsl:apply-templates select="joined/col[@type=17]" mode="enumcreate"/>

if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';

if($_REQUEST['e'] == 'RET')
{
<xsl:variable name="parentName"><xsl:value-of select="@parentscript" /></xsl:variable>
$_REQUEST['pid']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@parent='y']/@name"/> from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=? <xsl:if test="../table[@name=$parentName]/@multilanguage"> and CMF_LANG_ID=?</xsl:if>',$_REQUEST['id']<xsl:if test="../table[@name=$parentName]/@multilanguage">,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID</xsl:if>);
}



<xsl:value-of select="extraevents" disable-output-escaping="yes"/>
<xsl:apply-templates select="joined" mode="events"/>
<xsl:apply-templates select="link" mode="events"/>
<xsl:apply-templates select="joined/joined" mode="events">

</xsl:apply-templates>

<xsl:if test="@move='y'">
if($_REQUEST['e'] == 'Перенести')
{
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Перемещение - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data" name="frm"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<input type="hidden" name="s"><xsl:attribute name="value">{$_REQUEST['s']}</xsl:attribute></input><xsl:apply-templates select="col[@filt]" mode="filthidden"/>
EOF;
foreach ($_REQUEST['id'] as $id) print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<input type="hidden" name="id[]"><xsl:attribute name="value">{$id}</xsl:attribute></input>
EOF;

print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Перенос" class="gbt bmove"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><input type="submit" name="e" value="Отменить" class="gbt bcancel"/>
</td></tr>
<tr>
<td bgcolor="#FFFFFF"><br/>

<table width="100%" cellpadding="0" cellspacing="0">
<tr><td class="ulTree">
EOF;
<xsl:choose>
<xsl:when test="$parentTBL/@treechild">
print ___GetTree($cmf,0,$_REQUEST['pid']);
</xsl:when>
<xsl:otherwise>
print ___GetList($cmf,$_REQUEST['pid']);
</xsl:otherwise>
</xsl:choose>
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<br/>
Перейти в эту же папку <input type="checkbox" name="CMF_jump" value="1"/>
</td></tr>
</table>
</td>
</tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Перенос" class="gbt bmove"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><input type="submit" name="e" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table><br/>
EOF;
$visible=0;
}

if($_REQUEST['e'] == 'Перенос') 
{
foreach ($_REQUEST['id'] as $id)
 {
  <xsl:value-of select="moveevent" disable-output-escaping="yes"/>
  $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@parent='y']/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('cid'),$id);
 }
<xsl:value-of select="postmoveevent" disable-output-escaping="yes"/>
if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('CMF_jump')) {$_REQUEST['pid']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Param('cid');}
}
</xsl:if>

<xsl:if test="col[@input='y' and not(@internal)]">
if($_REQUEST['e'] == 'Применить' and is_array($_REQUEST['id']))
{
foreach ($_REQUEST['id'] as $id)
{
 $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal) and @input='y']" mode="update"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=?',<xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal) and @input='y']" mode="forminput"/>,$id);
<xsl:value-of select="postgroupupdateevent" disable-output-escaping="yes"/>
}
<xsl:value-of select="postgroupupdatesevent" disable-output-escaping="yes"/>
};
</xsl:if>

if(($_REQUEST['e'] == 'Удалить') and is_array($_REQUEST['id']) and ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>D))
{
<xsl:value-of select="predeletesevent" disable-output-escaping="yes"/>
foreach ($_REQUEST['id'] as $id)
 {
<xsl:if test="col[@type=11]">
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$id);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="col[@type=11]/@name"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>? and <xsl:value-of select="col[@parent='y']/@name"/>=?',$ORDERING,$_REQUEST['pid']);</xsl:if>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('delete from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=?',$id);
<xsl:value-of select="postdeleteevent" disable-output-escaping="yes"/>
 }
<xsl:value-of select="postdeletesevent" disable-output-escaping="yes"/>
}

<xsl:if test="col/@type=11">
if($_REQUEST['e'] == 'UP')
{
list($V_<xsl:value-of select="col[@parent='y']/@name"/>,$V_ORDERING) =$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@parent='y']/@name"/>,<xsl:value-of select="col[@type=11]/@name"/> from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
if($V_ORDERING <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 1)
{

$sql="select <xsl:value-of select="col[@primary='y']/@name"/>
           , <xsl:value-of select="col[@type=11]/@name"/>
      from <xsl:apply-templates select="@name"/>
      where <xsl:value-of select="col[@type=11]/@name"/> <xsl:text disable-output-escaping="yes">&lt;</xsl:text> {$V_ORDERING}
            and <xsl:value-of select="col[@parent='y']/@name"/> = {$V_<xsl:value-of select="col[@parent='y']/@name"/>}
      order by <xsl:value-of select="col[@type=11]/@name"/> DESC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array($sql);


$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_ORDERING,$V_OTHER_ID);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_OTHER_ORDERING, $_REQUEST['id']);

}
}

if($_REQUEST['e'] == 'DN')
{
list($V_<xsl:value-of select="col[@parent='y']/@name"/>,$V_ORDERING) =$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@parent='y']/@name"/>,<xsl:value-of select="col[@type=11]/@name"/> from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
$V_MAXORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@parent='y']/@name"/>=?',$V_<xsl:value-of select="col[@parent='y']/@name"/>);
if($V_ORDERING <xsl:text disable-output-escaping="yes">&lt;</xsl:text> $V_MAXORDERING)
{

$sql="select <xsl:value-of select="col[@primary='y']/@name"/>
           , <xsl:value-of select="col[@type=11]/@name"/>
      from <xsl:apply-templates select="@name"/>
      where <xsl:value-of select="col[@type=11]/@name"/> <xsl:text disable-output-escaping="yes">&gt;</xsl:text> {$V_ORDERING}
            and <xsl:value-of select="col[@parent='y']/@name"/> = {$V_<xsl:value-of select="col[@parent='y']/@name"/>}
      order by <xsl:value-of select="col[@type=11]/@name"/> ASC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array($sql);


$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_ORDERING,$V_OTHER_ID);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_OTHER_ORDERING, $_REQUEST['id']);
}
}
</xsl:if>

if($_REQUEST['e'] == 'Добавить')
{
<xsl:value-of select="preinsertevent" disable-output-escaping="yes"/><xsl:if test="col/@type=11">
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@parent='y']/@name"/>=?',$_REQUEST['pid']);
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']++;</xsl:if>

$_REQUEST['id']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetSequence('<xsl:apply-templates select="@name"/>');
<xsl:apply-templates select="col" mode="preinsert"/>
<xsl:choose>
<xsl:when test="col[@type=7]">
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="col" mode="name_insert"/>) values (<xsl:apply-templates select="col" mode="vopros"/>)',<xsl:if test="col[@primary and @parent]">$_REQUEST['pid'],</xsl:if>$_REQUEST['id'],<xsl:apply-templates select="col[not(@primary)]" mode="form"/>);
</xsl:when>
<xsl:otherwise>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="col[not(@internal)]" mode="name_insert"/>) values (?,<xsl:apply-templates select="col[not(@primary) and not(@internal)]" mode="vopros"/>)',$_REQUEST['id'],<xsl:apply-templates select="col[not(@primary) and not(@internal)]" mode="form"/>);
</xsl:otherwise>
</xsl:choose>

$_REQUEST['e'] ='ED';
<xsl:value-of select="postinsertevent" disable-output-escaping="yes"/>
}

if($_REQUEST['e'] == 'Изменить')
{
<xsl:value-of select="preupdateevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preinsert"/>
if(!empty($_REQUEST['pid'])) $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal)]" mode="update"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=?',<xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal)]" mode="form"/>,$_REQUEST['id']);
else $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@internal)]" mode="update"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=?',<xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@internal)]" mode="form"/>,$_REQUEST['id']);

$_REQUEST['e'] ='ED';
<xsl:value-of select="postupdateevent" disable-output-escaping="yes"/>
};

if($_REQUEST['e'] == 'ED')
{
list(<xsl:apply-templates select="col[@type!=11 and not(@internal)]" mode="vars"/>)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="col[@type!=11 and not(@internal)]" mode="name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=?',$_REQUEST['id']);

<xsl:value-of select="preeditevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col[not(@primary) and (not(@parent) or (@parent and @showselect)) and not(@internal)]" mode="preedit"/>
print @<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Редактирование - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data" name="frm"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<input type="hidden" name="s"><xsl:attribute name="value">{$_REQUEST['s']}</xsl:attribute></input>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input><xsl:apply-templates select="col[@filt]" mode="filthidden"/><xsl:if test="@type">
<input type="hidden" name="type" value="{@type}"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e" value="Назад" class="gbt bcancel"/>
</td></tr></xsl:if>
EOF;

<xsl:if test="col[@childfilt='y'][ref/table!=@name]!=''">

<xsl:variable name="ltbn" select="col[@childfilt='y'][ref/table!=@name]/ref/table"/>
<xsl:choose>
<xsl:when test="extraforminsert!=''"><xsl:value-of select="extraformevent" disable-output-escaping="yes"/></xsl:when>
<xsl:when test="not(/config/table[@name=$ltbn]/@parentscript)">
#обычный список
$VV_<xsl:value-of select="/config/table[@name=$ltbn]/col[@primary='y']/@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik('','select A.<xsl:value-of select="col[@parentfilt='y'][ref/table!=@name]/@name"/>,<xsl:value-of select="col[@link='y'][ref/table!=@name]/ref/visual"/> from <xsl:value-of select="/config/table[@name=$ltbn]/@name"/> A <xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text> <xsl:choose><xsl:when test="/config/table[@name=$ltbn]/@ordering"><xsl:value-of select="/config/table[@name=$ltbn]/@ordering"/></xsl:when><xsl:when test="not(/config/table[@name=$ltbn]/@ordering) and /config/table[@name=$ltbn]/col[@type=11]"><xsl:value-of select="/config/table[@name=$ltbn]/col[@type=11]/@name"/></xsl:when><xsl:otherwise><xsl:value-of select="col[@link='y'][ref/table!=@name]/ref/visual"/></xsl:otherwise></xsl:choose>');
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><span class="title2"><xsl:value-of select="/config/table[@name=$ltbn]/name"/></span><br /><img src="i/0.gif" width="125" height="1" /></td><td  width="100%">

<table><tr><td><input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}[]'],'select {col[@link='y'][ref/table!=@name]/@name},{col[@link='y'][ref/table!=@name]/ref/visual} from {/config/table[@name=$ltbn]/@name} where {col[@link='y'][ref/table!=@name]/ref/visual} like ? order by {col[@link='y'][ref/table!=@name]/ref/visual}',this.value+'%25');"/></td></tr>
<tr><td><select name="{col[@link='y'][ref/table!=@name]/@name}[]" multiple="" style="width:100%" size="8">{$VV_<xsl:value-of select="/config/table[@name=$ltbn]/col[@primary='y']/@name"/>}</select></td></tr>
</table>
</td>
</tr>
EOF;
</xsl:when>
<xsl:when test="/config/table[@name=$ltbn]/@parentscript">
#список от child-таблицы
<xsl:variable name="plbtn" select="col[@parentfilt='y'][ref/table!=@name]/ref/table"/>
<xsl:choose>
<xsl:when test="/config/table[@name=$plbtn]/@treechild">
#список от child-древовидной таблицы
<xsl:variable name="parentcol" select="col[ref/table=$plbtn]/ref/visual"/>
<xsl:value-of select="parentcol"/>
$VV_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>TreeSpravotchnik('','select <xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>,<xsl:value-of select="$parentcol"/> from <xsl:value-of select="$plbtn"/> <xsl:value-of select="col[ref/table!=@name]/ref/where"/> where PARENT_ID=? <xsl:value-of select="col[ref/table!=@name]/ref/where2" disable-output-escaping="yes"/>',0);
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><span class="title2"><xsl:value-of select="name"/></span></td><td width="100%">
<table><tr><td><xsl:value-of select="/config/table[@name=$plbtn]/name"/>: <select name="{/config/table[@name=$plbtn]/col[@primary='y']/@name}" onchange="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}'],'select A.{col[@link='y'][ref/table!=@name]/@name},{col[@link='y'][ref/table!=@name]/ref/visual} from {/config/table[@name=$ltbn]/@name} A {col[@link='y'][ref/table!=@name]/ref/where} where A.{/config/table[@name=$plbtn]/col[@primary='y']/@name}=? {col[@link='y'][ref/table!=@name]/ref/where2} order by A.NAME',this.value);"><option value="">-- Не задан --</option>{$VV_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>}</select>&#160;
Фильтр: <input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}'],'select A.{col[@link='y'][ref/table!=@name]/@name},A.NAME from {/config/table[@name=$ltbn]/@name} A {col[@link='y'][ref/table!=@name]/ref/where} where A.{/config/table[@name=$plbtn]/col[@primary='y']/@name}=? and A.NAME like ? order by A.NAME',{/config/table[@name=$plbtn]/col[@primary='y']/@name}.value+'\|%25'+this.value+'%25');"/></td></tr>
<tr><td><select name="{col[@link='y'][ref/table!=@name]/@name}[]" multiple="" style="width:100%" size="8"></select></td></tr></table></td></tr>
EOF;
</xsl:when>
<xsl:otherwise>
#список от child-обычной таблицы
<xsl:variable name="parentcol" select="col[ref/table=$plbtn]/ref/visual"/>
<xsl:value-of select="parentcol"/>
$VV_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik($V_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>,'select A.<xsl:value-of select="col[@parentfilt='y'][ref/table!=@name]/@name"/>,<xsl:value-of select="col[@parentfilt='y'][ref/table!=@name]/ref/visual"/> from <xsl:value-of select="$plbtn"/> A <xsl:value-of select="col[ref/table!=@name]/ref/where"/> where 1 <xsl:value-of select="col[ref/table!=@name]/ref/where2"/>');

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr><td class="tbl_t2"><span class="title2"><xsl:value-of select="name"/></span></td><td class="tbl_e2" width="100%">
<table><tr><td><xsl:value-of select="/config/table[@name=$plbtn]/name"/>: <select name="{/config/table[@name=$plbtn]/col[@primary='y']/@name}" onchange="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}'],'select A.{col[@link='y'][ref/table!=@name]/@name},{col[@link='y'][ref/table!=@name]/ref/visual} from {/config/table[@name=$ltbn]/@name} A {col[@link='y'][ref/table!=@name]/ref/where} where A.{/config/table[@name=$plbtn]/col[@primary='y']/@name}=? {col[@link='y'][ref/table!=@name]/ref/where2} order by A.NAME',this.value);"><option value="">-- Не задан --</option>$VV_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/></select>&#160;
Фильтр: <input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}'],'select A.{col[@link='y'][ref/table!=@name]/@name},{col[@link='y'][ref/table!=@name]/ref/visual} from {/config/table[@name=$ltbn]/@name} A {col[@link='y'][ref/table!=@name]/ref/where} where A.{/config/table[@name=$plbtn]/col[@primary='y']/@name}=? and A.NAME like ? and A.STATUS=1  order by A.NAME',{/config/table[@name=$plbtn]/col[@primary='y']/@name}.value+'\|%25'+this.value+'%25');"/></td></tr>
<tr><td><select name="{col[@link='y'][ref/table!=@name]/@name}"  style="width:100%" size="8">$V_STR_ITEM_ID</select></td></tr></table></td></tr>
EOF;
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:otherwise></xsl:otherwise>
</xsl:choose>
</xsl:if>

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<xsl:choose>
<xsl:when test="count(tabs) &gt; 0">
<xsl:variable name="tabscount" select="count(tabs/tab)"/>

<tr bgcolor="#FFFFFF" class="ftr"><td colspan="2">
<ul id="tabs" class="shadetabs">
<xsl:apply-templates select="tabs/tab" mode="list"/>
</ul>

<div style="border-top:1px solid gray; width:100%; margin-bottom: 1em; padding-top:10px;">
<xsl:apply-templates select="tabs/tab" mode="content_child"/>
</div>

<script type="text/javascript">
var tabs=new ddtabcontent("tabs");
tabs.setpersist(true);
tabs.setselectedClassTarget("link");
tabs.init();
</script>
</td></tr>
</xsl:when>
<xsl:otherwise>
<xsl:apply-templates select="col[not(@primary) and not(@parent) and not(@parentfilt) and not(@childfilt) and @type!=11 and not(@internal)]|panel|pseudocol" mode="edit"/>
</xsl:otherwise>
</xsl:choose>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e" value="Назад" class="gbt bcancel"/>
</td></tr>
</form>
</table><br/>
EOF;
<xsl:apply-templates select="joined"/>
<xsl:apply-templates select="link"/>
$visible=0;
}

if($_REQUEST['e'] =='Новый')
{
list(<xsl:apply-templates select="col" mode="vars"/>)=array(<xsl:apply-templates select="col" mode="vars_init"/>);
<xsl:value-of select="preaddevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col[not(@primary) and (not(@parent) or (@parent and @showselect)) and not(@internal) and not(@parentfilt)]" mode="preadd"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Добавление - <xsl:apply-templates select="name"/></h2>
<a href="javascript:history.go(-1)"><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><b>вернуться</b></a><p/>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data" name="frm"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd"/> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel"/>
</td></tr>
EOF;

<xsl:if test="col[@childfilt='y'][ref/table!=@name]">
<xsl:variable name="ltbn" select="col[@childfilt='y'][ref/table!=@name]/ref/table"/>
<xsl:choose>
<xsl:when test="extraforminsert!=''"><xsl:value-of select="extraformevent" disable-output-escaping="yes"/></xsl:when>
<xsl:when test="not(/config/table[@name=$ltbn]/@parentscript)">
#обычный список
$VV_<xsl:value-of select="/config/table[@name=$ltbn]/col[@primary='y']/@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik('','select A.<xsl:value-of select="col[@parentfilt='y'][ref/table!=@name]/@name"/>,<xsl:value-of select="col[@link='y'][ref/table!=@name]/ref/visual"/> from <xsl:value-of select="/config/table[@name=$ltbn]/@name"/> A <xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text> <xsl:choose><xsl:when test="/config/table[@name=$ltbn]/@ordering"><xsl:value-of select="/config/table[@name=$ltbn]/@ordering"/></xsl:when><xsl:when test="not(/config/table[@name=$ltbn]/@ordering) and /config/table[@name=$ltbn]/col[@type=11]"><xsl:value-of select="/config/table[@name=$ltbn]/col[@type=11]/@name"/></xsl:when><xsl:otherwise><xsl:value-of select="col[@link='y'][ref/table!=@name]/ref/visual"/></xsl:otherwise></xsl:choose>');
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><span class="title2"><xsl:value-of select="/config/table[@name=$ltbn]/name"/></span><br /><img src="i/0.gif" width="125" height="1" /></td><td  width="100%">

<table><tr><td><input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}[]'],'select {col[@link='y'][ref/table!=@name]/@name},{col[@link='y'][ref/table!=@name]/ref/visual} from {/config/table[@name=$ltbn]/@name} where {col[@link='y'][ref/table!=@name]/ref/visual} like ? order by {col[@link='y'][ref/table!=@name]/ref/visual}',this.value+'%25');"/></td></tr>
<tr><td><select name="{col[@link='y'][ref/table!=@name]/@name}[]" multiple="" style="width:100%" size="8">{$VV_<xsl:value-of select="/config/table[@name=$ltbn]/col[@primary='y']/@name"/>}</select></td></tr>
</table>
</td>
</tr>
EOF;
</xsl:when>
<xsl:when test="/config/table[@name=$ltbn]/@parentscript">
#список от child-таблицы
<xsl:variable name="plbtn" select="col[@parentfilt='y'][ref/table!=@name]/ref/table"/>
<xsl:choose>
<xsl:when test="/config/table[@name=$plbtn]/@treechild">
#список от child-древовидной таблицы
<xsl:variable name="parentcol" select="col[ref/table=$plbtn]/ref/visual"/>
<xsl:value-of select="parentcol"/>
$VV_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>TreeSpravotchnik('','select <xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>,<xsl:value-of select="$parentcol"/> from <xsl:value-of select="$plbtn"/> <xsl:value-of select="col[ref/table!=@name]/ref/where"/> where PARENT_ID=? <xsl:value-of select="col[ref/table!=@name]/ref/where2" disable-output-escaping="yes"/>',0);
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><span class="title2"><xsl:value-of select="name"/></span></td><td width="100%">
<table><tr><td><xsl:value-of select="/config/table[@name=$plbtn]/name"/>: <select name="{/config/table[@name=$plbtn]/col[@primary='y']/@name}" onchange="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}'],'select A.{col[@link='y'][ref/table!=@name]/@name},{col[@link='y'][ref/table!=@name]/ref/visual} from {/config/table[@name=$ltbn]/@name} A {col[@link='y'][ref/table!=@name]/ref/where} where A.{/config/table[@name=$plbtn]/col[@primary='y']/@name}=? {col[@link='y'][ref/table!=@name]/ref/where2} order by A.NAME',this.value);"><option value="">-- Не задан --</option>{$VV_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>}</select>&#160;
Фильтр: <input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}'],'select A.{col[@link='y'][ref/table!=@name]/@name},A.NAME from {/config/table[@name=$ltbn]/@name} A {col[@link='y'][ref/table!=@name]/ref/where} where A.{/config/table[@name=$plbtn]/col[@primary='y']/@name}=? and A.NAME like ? order by A.NAME',{/config/table[@name=$plbtn]/col[@primary='y']/@name}.value+'\|%25'+this.value+'%25');"/></td></tr>
<tr><td><select name="{col[@link='y'][ref/table!=@name]/@name}[]" multiple="" style="width:100%" size="8"></select></td></tr></table></td></tr>
EOF;
</xsl:when>
<xsl:otherwise>
#список от child-обычной таблицы
<xsl:variable name="parentcol" select="col[ref/table=$plbtn]/ref/visual"/>
<xsl:value-of select="parentcol"/>
$VV_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/>=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Spravotchnik('','select A.<xsl:value-of select="col[@parentfilt='y'][ref/table!=@name]/@name"/>,<xsl:value-of select="col[@parentfilt='y'][ref/table!=@name]/ref/visual"/> from <xsl:value-of select="$plbtn"/> A <xsl:value-of select="col[ref/table!=@name]/ref/where"/> where 1 <xsl:value-of select="col[ref/table!=@name]/ref/where2"/>');

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr><td class="tbl_t2"><span class="title2"><xsl:value-of select="name"/></span></td><td class="tbl_e2" width="100%">
<table><tr><td><xsl:value-of select="/config/table[@name=$plbtn]/name"/>: <select name="{/config/table[@name=$plbtn]/col[@primary='y']/@name}" onchange="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}'],'select A.{col[@link='y'][ref/table!=@name]/@name},{col[@link='y'][ref/table!=@name]/ref/visual} from {/config/table[@name=$ltbn]/@name} A {col[@link='y'][ref/table!=@name]/ref/where} where A.{/config/table[@name=$plbtn]/col[@primary='y']/@name}=? {col[@link='y'][ref/table!=@name]/ref/where2} order by A.NAME',this.value);"><option value="">-- Не задан --</option>$VV_<xsl:value-of select="/config/table[@name=$plbtn]/col[@primary='y']/@name"/></select>&#160;
Фильтр: <input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['{col[@link='y'][ref/table!=@name]/@name}'],'select A.{col[@link='y'][ref/table!=@name]/@name},{col[@link='y'][ref/table!=@name]/ref/visual} from {/config/table[@name=$ltbn]/@name} A {col[@link='y'][ref/table!=@name]/ref/where} where A.{/config/table[@name=$plbtn]/col[@primary='y']/@name}=? and A.NAME like ? and A.STATUS=1  order by A.NAME',{/config/table[@name=$plbtn]/col[@primary='y']/@name}.value+'\|%25'+this.value+'%25');"/></td></tr>
<tr><td><select name="{col[@link='y'][ref/table!=@name]/@name}"  style="width:100%" size="8"></select></td></tr></table></td></tr>
EOF;
</xsl:otherwise>
</xsl:choose>
</xsl:when>
<xsl:otherwise></xsl:otherwise>
</xsl:choose>
</xsl:if>

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<xsl:choose>
<xsl:when test="count(tabs) &gt; 0">
<xsl:variable name="tabscount" select="count(tabs/tab)"/>

<tr bgcolor="#FFFFFF" class="ftr"><td colspan="2">
<ul id="tabs" class="shadetabs">
<xsl:apply-templates select="tabs/tab" mode="list"/>
</ul>

<div style="border-top:1px solid gray; width:100%; margin-bottom: 1em; padding-top:10px;">
<xsl:apply-templates select="tabs/tab" mode="content_child_add"/>
</div>

<script type="text/javascript">
var tabs=new ddtabcontent("tabs");
tabs.setpersist(true);
tabs.setselectedClassTarget("link");
tabs.init();
</script>
</td></tr>
</xsl:when>
<xsl:otherwise>
<xsl:apply-templates select="col[not(@primary) and not(@parent) and not(@parentfilt) and not(@childfilt) and @type!=11 and not(@internal)]|panel" mode="edit"/>
</xsl:otherwise>
</xsl:choose>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd"/> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table><br/>
EOF;
$visible=0;
}

if($visible)
{<xsl:if test="col/@filt">
list($filtpath,$filtwhere)=array('','');
foreach($_REQUEST as $key=<xsl:text disable-output-escaping="yes">&gt;</xsl:text>$val)
{
  if(preg_match('/^FLT_(.+)$/',$key,$p))
  {
    if($val!='')
     {
        $filtpath.='&amp;'.$key.'='.$val;
     }
  }
}
</xsl:if><xsl:choose>
<xsl:when test="scriptname">
<xsl:value-of select="scriptname" disable-output-escaping="yes"/>
</xsl:when>
<xsl:otherwise>
if(empty($_REQUEST['pid'])) $_REQUEST['pid'] = 0;

if(!empty($_REQUEST['pid']) and $_REQUEST['pid']!='all') $V_PARENTSCRIPTNAME=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:apply-templates select="col[@parent='y']/ref/visual"/> from <xsl:apply-templates select="col[@parent='y']/ref/table"/> where <xsl:apply-templates select="col[@parent='y']/ref/field"/>=?',$_REQUEST['pid']);
else $V_PARENTSCRIPTNAME='';
</xsl:otherwise>
</xsl:choose>
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">$V_PARENTSCRIPTNAME / <xsl:apply-templates select="name"/></h2><form action="{@name}.php" method="POST">
<xsl:choose>
<xsl:when test="back"><xsl:value-of select="back" disable-output-escaping="yes"/></xsl:when>
<xsl:otherwise><a><xsl:attribute name="href"><xsl:value-of select="@parentscript"/>.php?e=RET&amp;id={$_REQUEST['pid']}&amp;p={$_REQUEST['p']}</xsl:attribute>
<img src="i/back.gif" border="0" align="top"/> Назад</a><br/></xsl:otherwise>
</xsl:choose>
EOF;
<xsl:variable name="p"><xsl:if test="@pagesize">&amp;p={$_REQUEST['p']}</xsl:if><xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">&amp;s={$_REQUEST['s']}</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if></xsl:variable>
<xsl:variable name="pid">$_REQUEST['pid']</xsl:variable>
<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">
<xsl:if test="@defsort">if($_REQUEST['s'] == ''){$_REQUEST['s']=<xsl:value-of select="@defsort"/>;}</xsl:if>
$_REQUEST['s']+=0;
$SORTNAMES=array(<xsl:apply-templates select="col[@visuality='y']|calccol" mode="sortnames"/>);
$SORTQUERY=array(<xsl:apply-templates select="col[@visuality='y']|calccol" mode="sortquerys"/>);

//Ручные фильтры
$filters = '';
$filt_request = '';
foreach($_REQUEST as $key=<xsl:text disable-output-escaping="yes">&gt;</xsl:text>$val)
{
  if(preg_match('/^FILTER_(.+)$/',$key,$p))
  {
    if($val!='')
     {
        $filters.='&amp;'.$key.'='.$val;
     }
  }
}

list($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?pid={$_REQUEST['pid']}&amp;s=$tmps<xsl:if test="@letter">&amp;l={$_REQUEST['l']}</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if>{$filters}</xsl:attribute>$tmp <img src='i/sdn.gif' border='0'/></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?pid={$_REQUEST['pid']}&amp;s=$tmps<xsl:if test="@letter">&amp;l=$_REQUEST['l']</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if>{$filters}</xsl:attribute>$tmp <img src='i/sup.gif' border='0'/></a></th>
EOF;
        } 
        else { 
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?pid={$_REQUEST['pid']}&amp;s=$tmps<xsl:if test="@letter">&amp;l=$_REQUEST['l']</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if>{$filters}</xsl:attribute>$tmp</a></th>
EOF;
        }
        $i++;
}
</xsl:if>
<xsl:choose>
<xsl:when test="@pagesize">
$pagesize=<xsl:if test="@pageresizeble='y'">$_REQUEST['ps'] || </xsl:if><xsl:value-of select="@pagesize"/>;

if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}

if(!isset($_REQUEST['count']) || !$_REQUEST['count']<xsl:if test="@pageresizeble='y'"> || $_REQUEST['resize']</xsl:if>)
{
if(!empty($_REQUEST['pid']) and $_REQUEST['pid']=='all')
{
$_REQUEST['count']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select count(*) from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/> where A.<xsl:value-of select="col[@parent='y']/@name"/> <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 0'<xsl:if test="where">.<xsl:value-of select="where" disable-output-escaping="yes"/></xsl:if><xsl:apply-templates select="col[@filt='y']" mode="filt"/>,$_REQUEST['pid']);
}
else
{
$_REQUEST['count']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select count(*) from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/> where A.<xsl:value-of select="col[@parent='y']/@name"/>=?'<xsl:if test="where">.<xsl:value-of select="where" disable-output-escaping="yes"/></xsl:if><xsl:apply-templates select="col[@filt='y']" mode="filt"/>,$_REQUEST['pid']);

}
$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] <xsl:text disable-output-escaping="yes">&gt;</xsl:text> $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 1)
{
 $start=1;
<xsl:text disable-output-escaping="yes">if($_REQUEST['p']>15){$start=$_REQUEST['p']-15;}</xsl:text>
 
 for($i=$start;$i<xsl:text disable-output-escaping="yes">&lt;=$_REQUEST['pcount'] &amp;&amp; ($i-$start)&lt;31</xsl:text>;$i++)
 {
  if($i==$_REQUEST['p']) { print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
- <b class="red">$i</b>
EOF;
 } else { print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
- <a class="t"><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?pid={$_REQUEST['pid']}&amp;count={$_REQUEST['count']}&amp;p={$i}&amp;pcount={$_REQUEST['pcount']}<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">&amp;s={$_REQUEST['s']}</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if><xsl:if test="@pageresizeble='y'">&amp;ps={$_REQUEST['ps']}</xsl:if>{$filters}</xsl:attribute>$i</a>
EOF;
  }
 }
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>из <span class="red">({$_REQUEST['pcount']})</span><br/>
EOF;
}

if(!empty($_REQUEST['pid']) and $_REQUEST['pid'] == 'all')
{
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/><xsl:if test="col/@parent='y'">
where A.<xsl:value-of select="col[@parent='y']/@name"/> <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 0 </xsl:if><xsl:if test="where">'.<xsl:value-of select="where" disable-output-escaping="yes"/>.' </xsl:if><xsl:if test="col/@filt">'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'</xsl:if><xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> <xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">'.$SORTQUERY[$_REQUEST['s']].'</xsl:if>limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);
}
else
{
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/><xsl:if test="col/@parent='y'">
where A.<xsl:value-of select="col[@parent='y']/@name"/>=? </xsl:if><xsl:if test="where">'.<xsl:value-of select="where" disable-output-escaping="yes"/>.' </xsl:if><xsl:if test="col/@filt">'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'</xsl:if><xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> <xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">'.$SORTQUERY[$_REQUEST['s']].'</xsl:if>limit ?,?',$_REQUEST['pid'],$pagesize*($_REQUEST['p']-1),$pagesize);

}
</xsl:when>
<xsl:otherwise>
if(!empty($_REQUEST['pid']) and $_REQUEST['pid']!='all')
{
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/><xsl:if test="col/@parent='y'"> where A.<xsl:value-of select="col[@parent='y']/@name"/>=? </xsl:if><xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> '<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">.$SORTQUERY[$_REQUEST['s']]</xsl:if>,$_REQUEST['pid']);
}
else
{
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/><xsl:if test="col/@parent='y'">
where A.<xsl:value-of select="col[@parent='y']/@name"/> <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 0 </xsl:if><xsl:if test="where">'.<xsl:value-of select="where" disable-output-escaping="yes"/>.' </xsl:if><xsl:if test="col/@filt">'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'</xsl:if><xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> <xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">'.$SORTQUERY[$_REQUEST['s']].'</xsl:if>limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);

}
</xsl:otherwise>
</xsl:choose>

<xsl:if test="col/@filt">
<xsl:apply-templates select="col[@filt]" mode="prefilt"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<input type="hidden" name="s"><xsl:attribute name="value">{$_REQUEST['s']}</xsl:attribute></input>
<xsl:if test="@type"><input type="hidden" name="type" value="{@type}"/></xsl:if>
<tr bgcolor="#F0F0F0"><td colspan="2"><input type="submit" name="e" value="Фильтр" class="gbt bflt"/></td></tr>
<xsl:apply-templates select="col[@filt]" mode="filtform"/>
</table>
EOF;
</xsl:if>


@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<img src="img/hi.gif" width="1" height="3"/><table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="{count(col[@type!=11][@visuality='y'])+count(calccol)+2}"><xsl:if test="number(filter/@cols) &gt; 0"><xsl:attribute name="colspan"><xsl:value-of select="count(col[@type!=11][@visuality='y'])+count(calccol)+2-number(filter/@cols)"/></xsl:attribute></xsl:if>
EOF;

if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>W)
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<input type="submit" name="e" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1"/><xsl:if test="col[@input='y']"><input type="submit" name="e" value="Применить" class="gbt bsave" /><img src="i/0.gif" width="4" height="1"/></xsl:if><xsl:if test="@move"><input type="submit" name="e" value="Перенести" class="gbt bmove" /><img src="i/0.gif" width="4" height="1"/></xsl:if><xsl:apply-templates select="extrakey"/>
EOF;

if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>D)
  print '<input type="submit" name="e" onclick="return dl();" value="Удалить" class="gbt bdel" />';
  
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<input type="hidden" name="s"><xsl:attribute name="value">{$_REQUEST['s']}</xsl:attribute></input>
</td><xsl:value-of select="filter" disable-output-escaping="yes"/></tr>
EOF;

print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');"/></td><xsl:choose><xsl:when test="not(@ordering) and not(col/@type=11) and not(@nosort)">$HEADER</xsl:when><xsl:otherwise><xsl:apply-templates select="calccol|col[@type!=11][@visuality='y']" mode="head"/></xsl:otherwise></xsl:choose><td></td></tr>
EOF;
<xsl:if test="col[@input='y']">$TABposition=1;</xsl:if>

if($sth)
while(list(<xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="vars"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{<xsl:if test="col[@input='y']">$TABposition++;</xsl:if>

<xsl:value-of select="previsible" disable-output-escaping="yes"/>
<xsl:apply-templates select="col[@visuality='y' and not(@visualityname)]" mode="previsible"/>
<xsl:if test="col[@isstate='y']">
if($V_<xsl:value-of select="col[@isstate='y']/@name"/> == 1){$V_COLOR='#FFFFFF';} else {$V_COLOR='#a0a0a0';}
</xsl:if>
<xsl:value-of select="postvisible" disable-output-escaping="yes"/>

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><xsl:if test="col[@isstate='y']"><xsl:attribute name="bgcolor">$V_COLOR</xsl:attribute></xsl:if>
<td><input type="checkbox" name="id[]" value="$V_{col[@primary='y' and not(@parent)]/@name}"/></td>
<xsl:apply-templates select="col[@type!=11][@visuality='y']|calccol" mode="varsprint"/><td nowrap=""><xsl:if test="col/@type=11">
<a><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?e=UP&amp;id=$V_<xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>&amp;pid={$_REQUEST['pid']}{$filters}</xsl:attribute><img src="i/up.gif" border="0"/></a>
<a><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?e=DN&amp;id=$V_<xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>&amp;pid={$_REQUEST['pid']}{$filters}</xsl:attribute><img src="i/dn.gif" border="0"/></a></xsl:if>
EOF;

if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>W)
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?e=ED&amp;id=$V_<xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>&amp;pid={$_REQUEST['pid']}<xsl:value-of select="$p"/>{$filters}</xsl:attribute><img src="i/ed.gif" border="0" title="Изменить"/></a>

<xsl:apply-templates select="child_script"/><xsl:value-of select="rowicon" disable-output-escaping="yes"/></td></tr>
EOF;
}
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
        <xsl:value-of select="pseudorow" disable-output-escaping="yes"/></table>
EOF;
}
print '</form>';
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>MakeCommonFooter();
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Close();
<xsl:value-of select="extrasubs" disable-output-escaping="yes"/>
<xsl:choose>
<xsl:when test="$parentTBL/@treechild">
function ___GetTree($cmf,$pid,$id)
{
$id+=0;
$ret='';
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:value-of select="$parentTBL/col[@primary='y']/@name"/>,<xsl:value-of select="$parentTBL/col[@isfold='y']/@name"/> from <xsl:value-of select="$parentTBL/@name"/> where <xsl:value-of select="$parentTBL/col[@parent='y']/@name"/>=? order by <xsl:value-of select="$parentTBL/col[@type=11]/@name"/>',$pid);
while(list($V_<xsl:value-of select="$parentTBL/col[@primary='y']/@name"/>,$V_<xsl:value-of select="$parentTBL/col[@isfold='y']/@name"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.='<li>'.($id==$V_<xsl:value-of select="$parentTBL/col[@primary='y']/@name"/>?'<input type="radio" name="cid" value="'.$V_{$parentTBL/col[@primary='y']/@name}.'" disabled="yes"/>':'<input type="radio" name="cid" value="'.$V_{$parentTBL/col[@primary='y']/@name}.'"/>')."<xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>$V_<xsl:value-of select="$parentTBL/col[@isfold='y']/@name"/></li>".___GetTree($cmf,$V_<xsl:value-of select="$parentTBL/col[@primary='y']/@name"/>,$id);
}
if ($ret) {$ret="<ul>${ret}</ul>";}
return $ret;
}
</xsl:when>
<xsl:otherwise>
function ___GetList($cmf,$id)
{
$ret='';
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:value-of select="$parentTBL/col[@primary='y']/@name"/>,<xsl:value-of select="$parentTBL/col[@child]/@name"/> from <xsl:value-of select="$parentTBL/@name"/>  order by <xsl:choose><xsl:when test="$parentTBL/col[@type=11]"><xsl:value-of select="$parentTBL/col[@type=11]/@name"/></xsl:when><xsl:otherwise><xsl:value-of select="$parentTBL/col[@child]/@name"/></xsl:otherwise></xsl:choose>');
while(list($V_<xsl:value-of select="$parentTBL/col[@primary='y']/@name"/>,$V_<xsl:value-of select="$parentTBL/col[@child]/@name"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.='<li>'.($id==$V_<xsl:value-of select="$parentTBL/col[@primary='y']/@name"/>?'<input type="radio" name="cid" value="'.$V_{$parentTBL/col[@primary='y']/@name}.'" disabled="yes"/>':'<input type="radio" name="cid" value="'.$V_{$parentTBL/col[@primary='y']/@name}.'"/>')."<xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>$V_<xsl:value-of select="$parentTBL/col[@child]/@name"/></li>";
}
if ($ret) {$ret="<ul>${ret}</ul>";}
return $ret;
}
</xsl:otherwise>
</xsl:choose>
?<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
-----------------------
</xsl:template>

<xsl:template match="config">
<xsl:apply-templates select="table"/>
</xsl:template>
</xsl:stylesheet>
