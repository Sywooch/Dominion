<?xml version="1.0" encoding="windows-1251"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml"  omit-xml-declaration="yes" encoding="windows-1251"/>

<xsl:template match="config/table[@parentscript]" xml:space="preserve">-----------------------|scripts/<xsl:value-of select="@name"/>.php|<xsl:variable name="parentTBL" select="/config/table[@name=current()/@parentscript]"/>
<xsl:text disable-output-escaping="yes">&lt;</xsl:text>? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('<xsl:choose><xsl:when test="@article"><xsl:value-of select="@article"/></xsl:when><xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise></xsl:choose>');
if (!$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetRights()) {header('Location: login.php'); exit;}
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>HeaderNoCache();

<xsl:if test="preheader">
	<xsl:value-of select="preheader" disable-output-escaping="yes"/>
</xsl:if>

$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>MakeCommonHeader();
$visible=1;
<xsl:if test="@imagepath">$VIRTUAL_IMAGE_PATH='<xsl:value-of select="@imagepath"/>';</xsl:if>

<xsl:apply-templates select="col[@type=10]" mode="enumcreate"/>
<xsl:apply-templates select="joined/col[@type=10]" mode="enumcreate"/>

if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';

if($_REQUEST['e'] == 'RET')
{
<xsl:variable name="parentName"><xsl:value-of select="@parentscript" /></xsl:variable>
$_REQUEST['pid']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@parent='y']/@name"/> from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=? <xsl:if test="../table[@name=$parentName]/@multilanguage"> and CMF_LANG_ID=?</xsl:if>',$_REQUEST['id']<xsl:if test="../table[@name=$parentName]/@multilanguage">,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID</xsl:if>);
}

<xsl:value-of select="extraevents" disable-output-escaping="yes"/>
<xsl:apply-templates select="joined" mode="events"/>
<xsl:apply-templates select="link" mode="events"/>

<xsl:if test="@move='y'">
if($_REQUEST['e'] == 'Перенести')
{
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Перемещение - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
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

<xsl:if test="col[@input='y']">
if($_REQUEST['e'] == 'Применить')
{
foreach ($_REQUEST['id'] as $id)
{
 $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal) and @input='y']" mode="update"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=?',<xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal) and @input='y']" mode="forminput"/>,$id);
}
};
</xsl:if>

if(($_REQUEST['e'] == 'Удалить') and is_array($_REQUEST['id']))
{
foreach ($_REQUEST['id'] as $id)
 {
<xsl:if test="col[@type=11]">
$ORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$id,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="col[@type=11]/@name"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>? and <xsl:value-of select="col[@parent='y']/@name"/>=? and CMF_LANG_ID=?',$ORDERING,$_REQUEST['pid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);</xsl:if>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('delete from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=? and CMF_LANG_ID=?',$id,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
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

if($_REQUEST['e'] == 'Добавить'){
11111
<xsl:if test="col[@type=7 or @type=15 or @type=16]/@watermark">
$path_to_watermark = $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array("select IMAGE from SETINGS where SYSTEM_NAME='<xsl:value-of select="@watermark"/>'");

if(!empty($path_to_watermark) <xsl:text disable-output-escaping="yes">&amp;&amp;</xsl:text> !empty($_REQUEST['IS_WATERMARK_b'])){
	$path_to_watermark = preg_replace('/\#.*/','',$path_to_watermark);
	$path_to_watermark = '../images/wm/'.$path_to_watermark;
}
else $path_to_watermark='';
</xsl:if>
<xsl:if test="col/@type=11">
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@parent='y']/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['pid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']++;</xsl:if>

$_REQUEST['id']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetSequence('<xsl:apply-templates select="@name"/>');
<xsl:apply-templates select="col" mode="preinsert"/>
<xsl:choose>
<xsl:when test="col[@type=7 or @type=14]">
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="col" mode="name_insert"/>,CMF_LANG_ID) values (<xsl:apply-templates select="col" mode="vopros"/>,?)',$_REQUEST['id'],<xsl:apply-templates select="col[not(@primary)]" mode="form"/>,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
</xsl:when>
<xsl:otherwise>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="col[not(@internal)]" mode="name_insert"/>,CMF_LANG_ID) values (?,<xsl:apply-templates select="col[not(@primary) and not(@internal)]" mode="vopros"/>,?)',$_REQUEST['id'],<xsl:apply-templates select="col[not(@primary) and not(@internal)]" mode="form"/>,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
</xsl:otherwise>
</xsl:choose>

$_REQUEST['e'] ='ED';
<xsl:if test="@forcedtranslation='y'">
#=========== насильный перевод
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('
insert into <xsl:apply-templates select="@name" /> (<xsl:apply-templates select="col[not(@isstate)]" mode="name_insert"/><xsl:if test="col[@isstate='y']">,<xsl:apply-templates select="col[@isstate='y']" mode="name_insert"/></xsl:if>,CMF_LANG_ID)
select <xsl:apply-templates select="col[not(@isstate)]" mode="name_insert"><xsl:with-param name="tableName">T</xsl:with-param></xsl:apply-templates><xsl:if test="col[@isstate='y']">,<xsl:apply-templates select="col[@isstate='y']" mode="zero"/></xsl:if>,CL.CMF_LANG_ID
from <xsl:apply-templates select="@name" /> T,CMF_LANG CL
where T.<xsl:value-of select="col[@primary='y']/@name"/>=? and T.CMF_LANG_ID=? and CL.CMF_LANG_ID!=?',$_REQUEST['id'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID,$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
#=========== /насильный перевод
</xsl:if>
<xsl:value-of select="postinsertevent" disable-output-escaping="yes"/>
}

if($_REQUEST['e'] == 'Изменить')
{
<xsl:apply-templates select="col" mode="preinsert"/>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal)]" mode="update"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=? and CMF_LANG_ID=?',<xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal)]" mode="form"/>,$_REQUEST['id'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$_REQUEST['e'] ='ED';
<xsl:value-of select="postupdateevent" disable-output-escaping="yes"/>
};


#=========== перевод
if($_REQUEST['e'] == 'Продублировать')
{
	if(is_array($_REQUEST['lang']))
	{
		foreach ($_REQUEST['lang'] as $langId=<xsl:text disable-output-escaping="yes">&gt;</xsl:text>$state)
		{
			$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('
				insert into <xsl:apply-templates select="@name" /> (CMF_LANG_ID,<xsl:apply-templates select="col[not(@isstate)]" mode="name_insert"/><xsl:if test="col[@isstate='y']">,<xsl:apply-templates select="col[@isstate='y']" mode="name_insert"/></xsl:if>)
				select ?,?,<xsl:apply-templates select="col[not(@primary)][not(@isstate)]" mode="name_insert"/><xsl:if test="col[@isstate='y']">,<xsl:apply-templates select="col[@isstate='y']" mode="zero"/></xsl:if>
				from <xsl:apply-templates select="@name" /> where <xsl:value-of select="col[@primary='y']/@name"/>=? and CMF_LANG_ID=?',$langId,$_REQUEST['id'],$_REQUEST['id'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
		}
<xsl:if test="@type">
			// а также перенос XML
			$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('
				insert into XMLS (CMF_LANG_ID,XMLS_ID,TYPE,XML)
				select ?,?,TYPE,XML
				from XMLS where XMLS_ID=? and CMF_LANG_ID=? and TYPE=<xsl:apply-templates select="@type" />',$langId,$_REQUEST['id'],$_REQUEST['id'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
</xsl:if>
	}
	
$_REQUEST['e']='ED';
};


if($_REQUEST['e'] == 'Языки')
{

	$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute("
		select L.CMF_LANG_ID,L.NAME,if(T.<xsl:value-of select="col[@primary='y']/@name" /><xsl:text disable-output-escaping="yes">&gt;</xsl:text>0,1,0) from CMF_LANG L
		left join <xsl:apply-templates select="@name" /> T on (L.CMF_LANG_ID=T.CMF_LANG_ID and T.<xsl:value-of select="col[@primary='y']/@name" />=?)
		where L.STATUS=1
		order by L.ORDERING asc
	",$_REQUEST['id']);

	@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data">
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
	<input type="submit" name="e" value="Продублировать" class="gbt bdublicate" /><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>
	<input type="button" value="Отменить" class="gbt bcancel" onclick="javascript:history.back();"/>
	<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
	<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
	<input type="hidden" name="s"><xsl:attribute name="value">{$REQUEST['s']}</xsl:attribute></input>
	<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input>
	</td>
	</tr>
</form>
</table>
EOF;
	
$visible=0;
}
#=========== /перевод


if($_REQUEST['e'] == 'ED')
{
list(<xsl:apply-templates select="col[@type!=11 and not(@internal)]" mode="vars"/>)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="col[@type!=11 and not(@internal)]" mode="name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>=? and CMF_LANG_ID=?',$_REQUEST['id'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
<xsl:value-of select="preeditevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col[not(@primary) and not(@parent) and not(@internal)]" mode="preedit"/>
print @<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Редактирование - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/><xsl:if test="checkevent"><xsl:value-of select="checkevent" disable-output-escaping="yes" /></xsl:if>;</xsl:attribute>
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<input type="hidden" name="s"><xsl:attribute name="value">{$_REQUEST['s']}</xsl:attribute></input>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input><xsl:apply-templates select="col[@filt]" mode="filthidden"/><xsl:if test="@type">
<input type="hidden" name="type" value="{@type}"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e" value="Языки" class="gbt blanguages" /><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>
<input type="submit" name="e" value="Назад" class="gbt bcancel"/>
</td></tr>
</xsl:if><xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@internal)]|panel|pseudocol" mode="edit"/>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e" value="Языки" class="gbt blanguages" /><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>
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
<xsl:apply-templates select="col[not(@primary) and not(@parent) and not(@internal)]" mode="preadd"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Добавление - <xsl:apply-templates select="name"/></h2>
<a href="javascript:history.go(-1)"><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><b>вернуться</b></a><p/>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd"/> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel"/>
</td></tr>
<xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@internal)]|panel" mode="edit"/>
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
    if($val<xsl:text disable-output-escaping="yes">&gt;</xsl:text>0)
     {
        $filtpath.='&amp;'.$key.'='.$val;
//	$filtwhere.=' and A.'.$p[1].
     }
  }
}
</xsl:if><xsl:choose>
<xsl:when test="scriptname">
<xsl:value-of select="scriptname" disable-output-escaping="yes"/>
</xsl:when>
<xsl:otherwise>
$V_PARENTSCRIPTNAME=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:apply-templates select="col[@parent='y']/ref/visual"/> from <xsl:apply-templates select="col[@parent='y']/ref/table"/> where <xsl:apply-templates select="col[@parent='y']/ref/field"/>=? and CMF_LANG_ID=?',$_REQUEST['pid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
</xsl:otherwise>
</xsl:choose>
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">$V_PARENTSCRIPTNAME / <xsl:apply-templates select="name"/></h2><form action="{@name}.php" method="POST">
<xsl:choose>
<xsl:when test="back"><xsl:value-of select="back" disable-output-escaping="yes"/></xsl:when>
<xsl:otherwise><a><xsl:attribute name="href"><xsl:value-of select="@parentscript"/>.php?e=RET&amp;id={$_REQUEST['pid']}</xsl:attribute>
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

list($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
	$tmps=$i*2;
	if(($_REQUEST['s']-$tmps)==0) 
	{
		$tmps+=1;
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?pid={$_REQUEST['pid']}&amp;s=$tmps<xsl:if test="@letter">&amp;l={$_REQUEST['l']}</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if></xsl:attribute>$tmp <img src='i/sdn.gif' border='0'/></a></th>
EOF;
	}
	elseif(($_REQUEST['s']-$tmps)==1)
	{
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?pid={$_REQUEST['pid']}&amp;s=$tmps<xsl:if test="@letter">&amp;l=$_REQUEST['l']</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if></xsl:attribute>$tmp <img src='i/sup.gif' border='0'/></a></th>
EOF;
	} 
	else { 
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?pid={$_REQUEST['pid']}&amp;s=$tmps<xsl:if test="@letter">&amp;l=$_REQUEST['l']</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if></xsl:attribute>$tmp</a></th>
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
$_REQUEST['count']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select count(*) from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/> where A.<xsl:value-of select="col[@parent='y']/@name"/>=? and A.CMF_LANG_ID=?'<xsl:if test="where">.<xsl:value-of select="where" disable-output-escaping="yes"/></xsl:if><xsl:apply-templates select="col[@filt='y']" mode="filt"/>,$_REQUEST['pid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
$_REQUEST['pcount']=round($_REQUEST['count']/$pagesize+0.9999);
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
- <a class="t"><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?pid={$_REQUEST['pid']}&amp;count={$_REQUEST['count']}&amp;p={$i}&amp;pcount={$_REQUEST['pcount']}<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">&amp;s={$_REQUEST['s']}</xsl:if><xsl:if test="col/@filt">{$filtpath}</xsl:if><xsl:if test="@pageresizeble='y'">&amp;ps={$_REQUEST['ps']}</xsl:if></xsl:attribute>$i</a>
EOF;
  }
 }
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>из <span class="red">({$_REQUEST['pcount']})</span><br/>
EOF;
}
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/><xsl:if test="col/@parent='y'"> where A.<xsl:value-of select="col[@parent='y']/@name"/>=? </xsl:if><xsl:if test="where">'.<xsl:value-of select="where" disable-output-escaping="yes"/>.' </xsl:if> and CMF_LANG_ID=?<xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose><xsl:if test="col/@filt">'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'</xsl:if> <xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">'.$SORTQUERY[$_REQUEST['s']].'</xsl:if>limit ?,?',$_REQUEST['pid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID,$pagesize*($_REQUEST['p']-1),$pagesize);
</xsl:when>
<xsl:otherwise>
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A<xsl:value-of select="@from" disable-output-escaping="yes"/><xsl:if test="col/@parent='y'"> where A.<xsl:value-of select="col[@parent='y']/@name"/>=? </xsl:if>and A.CMF_LANG_ID=? <xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> '<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">.$SORTQUERY[$_REQUEST['s']]</xsl:if>,$_REQUEST['pid'],$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>CMF_LANG_ID);
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
<tr bgcolor="#F0F0F0"><td colspan="{count(col[@type!=11][@visuality='y'])+2}"><xsl:if test="number(filter/@cols) &gt; 0"><xsl:attribute name="colspan"><xsl:value-of select="count(col[@type!=11][@visuality='y'])+2-number(filter/@cols)"/></xsl:attribute></xsl:if>
<input type="submit" name="e" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1"/><xsl:if test="col[@input='y']"><input type="submit" name="e" value="Применить" class="gbt bsave" /><img src="i/0.gif" width="4" height="1"/></xsl:if><xsl:if test="@move"><input type="submit" name="e" value="Перенести" class="gbt bmove" /><img src="i/0.gif" width="4" height="1"/></xsl:if><xsl:apply-templates select="extrakey"/>
<input type="submit" name="e" onclick="return dl();" value="Удалить" class="gbt bdel" />
<xsl:call-template name="multilanguage_mark" />
<xsl:if test="@forcedtranslation='y'"><xsl:call-template name="forcedtranslation_mark" /></xsl:if>
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<input type="hidden" name="s"><xsl:attribute name="value">{$_REQUEST['s']}</xsl:attribute></input>
</td><xsl:value-of select="filter" disable-output-escaping="yes"/></tr>
EOF;

print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');"/></td><xsl:choose><xsl:when test="not(@ordering) and not(col/@type=11) and not(@nosort)">$HEADER</xsl:when><xsl:otherwise><xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="head"/></xsl:otherwise></xsl:choose><td></td></tr>
EOF;
<xsl:if test="col[@input='y']">$TABposition=1;</xsl:if>
if($sth)
while(list(<xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="vars"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{<xsl:if test="col[@input='y']">$TABposition++;</xsl:if><xsl:value-of select="previsible" disable-output-escaping="yes"/><xsl:apply-templates select="col[@visuality='y' and not(@visualityname)]" mode="previsible"/><xsl:if test="col[@isstate='y']">if($V_<xsl:value-of select="col[@isstate='y']/@name"/>){$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#FFFFFF';} else {$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#a0a0a0';}</xsl:if>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><xsl:if test="col[@isstate='y']"><xsl:attribute name="bgcolor">$V_<xsl:value-of select="col[@isstate='y']/@name"/></xsl:attribute></xsl:if>
<td><input type="checkbox" name="id[]" value="$V_{col[@primary='y' and not(@parent)]/@name}"/></td>
<xsl:apply-templates select="col[@type!=11][@visuality='y']|calccol" mode="varsprint"/><td nowrap=""><xsl:if test="col/@type=11">
<a><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?e=UP&amp;id=$V_<xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>&amp;pid={$_REQUEST['pid']}</xsl:attribute><img src="i/up.gif" border="0"/></a>
<a><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?e=DN&amp;id=$V_<xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>&amp;pid={$_REQUEST['pid']}</xsl:attribute><img src="i/dn.gif" border="0"/></a></xsl:if>
<a><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?e=ED&amp;id=$V_<xsl:value-of select="col[@primary='y' and not(@parent)]/@name"/>&amp;pid={$_REQUEST['pid']}<xsl:value-of select="$p"/></xsl:attribute><img src="i/ed.gif" border="0" title="Изменить"/></a>
<xsl:apply-templates select="child_script"/><xsl:value-of select="rowicon" disable-output-escaping="yes"/></td></tr>
EOF;
}
print '</table>';
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
