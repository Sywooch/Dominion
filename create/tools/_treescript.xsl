<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml"  omit-xml-declaration="yes" encoding="UTF-8"/>

<xsl:template match="col[@isfold]" mode="varsprint"><td><xsl:attribute name="style">padding-left:{$width}px</xsl:attribute>$V_<xsl:apply-templates select="@name"/><xsl:if test="@primary='y' and (@type=6 or @type=13)">_STR</xsl:if></td></xsl:template>

<xsl:template match="rowicon">
<xsl:value-of select="." disable-output-escaping="yes"/>
</xsl:template>

<xsl:template match="config/table[@treechild]" xml:space="preserve">-----------------------|scripts/<xsl:value-of select="@name"/>.php|
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
if(!isset($_REQUEST['r']))$_REQUEST['r']=0;
if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['event']))$_REQUEST['event']='';
if(!isset($_REQUEST['id']))$_REQUEST['id']='';
if(!isset($_REQUEST['pid']))$_REQUEST['pid']=0;
if(!isset($_REQUEST['f']))$_REQUEST['f']='';
<xsl:if test="@imagepath">$VIRTUAL_IMAGE_PATH="<xsl:value-of select="@imagepath"/>";</xsl:if>

<xsl:apply-templates select="col[@type=10]" mode="enumcreate"/>
<xsl:apply-templates select="joined/col[@type=10]" mode="enumcreate"/>
<xsl:apply-templates select="col[@type=17]" mode="enumcreate"/>
<xsl:apply-templates select="joined/col[@type=17]" mode="enumcreate"/>

<xsl:value-of select="extraevents" disable-output-escaping="yes"/>
<xsl:apply-templates select="link" mode="events"/>
<xsl:apply-templates select="joined" mode="events"/>

<xsl:apply-templates select="joined/joined" mode="events"/>

if($_REQUEST['e'] == 'DL')
{
DelTree($cmf,$_REQUEST['id']);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('delete from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
}

if($_REQUEST['e'] == 'VS')
{
$STATUS=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select STATUS from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
$STATUS=1-$STATUS;
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set STATUS=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$STATUS,$_REQUEST['id']);
if($STATUS)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set REALSTATUS=1 where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
SetTreeRealStatus($cmf,$_REQUEST['id'],1);
}
else
{
$REALSTATUS=GetMyRealStatus($cmf,$_REQUEST['id']);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set REALSTATUS=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$REALSTATUS,$_REQUEST['id']);
SetTreeRealStatus($cmf,$_REQUEST['id'],$REALSTATUS);
}
}

if($_REQUEST['e'] == 'UP')
{
list($V_<xsl:value-of select="col[@is_parent='y']/@name"/>,$V_ORDERING) =$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@is_parent='y']/@name"/>,<xsl:value-of select="col[@type=11]/@name"/> from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
if($V_ORDERING <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 1)
{

$sql="select <xsl:value-of select="col[@primary='y']/@name"/>
           , <xsl:value-of select="col[@type=11]/@name"/>
      from <xsl:apply-templates select="@name"/>
      where <xsl:value-of select="col[@type=11]/@name"/> <xsl:text disable-output-escaping="yes">&lt;</xsl:text> {$V_ORDERING}
            and <xsl:value-of select="col[@is_parent='y']/@name"/> = {$V_<xsl:value-of select="col[@is_parent='y']/@name"/>}
      order by <xsl:value-of select="col[@type=11]/@name"/> DESC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array($sql);


$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_ORDERING,$V_OTHER_ID);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_OTHER_ORDERING, $_REQUEST['id']);

}
}

if($_REQUEST['e'] == 'DN')
{
list($V_<xsl:value-of select="col[@is_parent='y']/@name"/>,$V_ORDERING) =$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@is_parent='y']/@name"/>,<xsl:value-of select="col[@type=11]/@name"/> from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
$V_MAXORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@is_parent='y']/@name"/>=?',$V_<xsl:value-of select="col[@is_parent='y']/@name"/>);
if($V_ORDERING <xsl:text disable-output-escaping="yes">&lt;</xsl:text> $V_MAXORDERING)
{

$sql="select <xsl:value-of select="col[@primary='y']/@name"/>
           , <xsl:value-of select="col[@type=11]/@name"/>
      from <xsl:apply-templates select="@name"/>
      where <xsl:value-of select="col[@type=11]/@name"/> <xsl:text disable-output-escaping="yes">&gt;</xsl:text> {$V_ORDERING}
            and <xsl:value-of select="col[@is_parent='y']/@name"/> = {$V_<xsl:value-of select="col[@is_parent='y']/@name"/>}
      order by <xsl:value-of select="col[@type=11]/@name"/> ASC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array($sql);


$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_ORDERING,$V_OTHER_ID);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_OTHER_ORDERING, $_REQUEST['id']);
}
}

if($_REQUEST['event'] == 'Добавить')
{

if(!empty($_REQUEST['pid']))
{
  $_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where PARENT_ID=?',$_REQUEST['pid']);
  $_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']++;
  $_REQUEST['id']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetSequence('<xsl:apply-templates select="@name"/>');
  <xsl:apply-templates select="col" mode="preinsert"/>
  $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="col" mode="name_insert"/>) values (<xsl:apply-templates select="col" mode="vopros"/>)',$_REQUEST['id'],$_REQUEST['pid']+0,<xsl:apply-templates select="col[not(@primary) and not(@parent) and not(@is_parent)]" mode="form"/>);
  <!-- $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set REALSTATUS=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',GetMyRealStatus($cmf,$_REQUEST['id']),$_REQUEST['id']); -->
  <xsl:value-of select="postinsertevent" disable-output-escaping="yes"/>
}
else
{
  $_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/> where PARENT_ID=?',0);
  $_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']++;
  $_REQUEST['id']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>GetSequence('<xsl:apply-templates select="@name"/>');
  <xsl:apply-templates select="col" mode="preinsert"/>
  $_REQUEST['pid'] = (!empty($_REQUEST['PARENT_ID'])) ? $_REQUEST['PARENT_ID'] : 0;
  $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('insert into <xsl:apply-templates select="@name"/> (<xsl:apply-templates select="col" mode="name_insert"/>) values (<xsl:apply-templates select="col" mode="vopros"/>)',$_REQUEST['id'],$_REQUEST['pid']+0,<xsl:apply-templates select="col[not(@primary) and not(@parent) and not(@is_parent)]" mode="form"/>);
  <!-- $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:apply-templates select="@name"/> set REALSTATUS=? where <xsl:value-of select="col[@primary='y']/@name"/>=?',GetMyRealStatus($cmf,$_REQUEST['id']),$_REQUEST['id']); -->
  <xsl:value-of select="postinsertevent" disable-output-escaping="yes"/>

}
$_REQUEST['e']='ED';
}

if($_REQUEST['event'] == 'Изменить')
{
<xsl:apply-templates select="col" mode="preinsert"/>
@$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@isrealstate) and not(@isstate) and not(@internal)]" mode="update"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',<xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@isrealstate) and not(@isstate) and not(@internal)]" mode="form"/>,$_REQUEST['id']);
$_REQUEST['e']='ED';
<xsl:value-of select="postupdateevent" disable-output-escaping="yes"/>
};

if($_REQUEST['e'] == 'ED')
{
list(<xsl:apply-templates select="col[@type!=11]" mode="vars"/>)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="col[@type!=11]" mode="name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);


<xsl:value-of select="preeditevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col[not(@primary)]" mode="preedit"/>

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Редактирование - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="pid"><xsl:attribute name="value">{$_REQUEST['pid']}</xsl:attribute></input>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input><xsl:if test="@type">
<input type="hidden" name="type" value="{@type}"/>
</xsl:if>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="event" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="event" value="Отменить" class="gbt bcancel"/>
</td></tr>

<xsl:choose>
<xsl:when test="count(tabs) &gt; 0">
<xsl:variable name="tabscount" select="count(tabs/tab)"/>

<tr bgcolor="#FFFFFF" class="ftr"><td colspan="2">
<ul id="tabs" class="shadetabs">
<xsl:apply-templates select="tabs/tab" mode="list"/>
</ul>

<div style="border-top:1px solid gray; width:100%; margin-bottom: 1em; padding-top:10px;">
<xsl:apply-templates select="tabs/tab" mode="content_edit"/>
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
<xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@isrealstate) and not(@isstate) and not(@internal)]|panel|pseudocol" mode="edit"/>
</xsl:otherwise>
</xsl:choose>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="event" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="event" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table><br />
EOF;
<xsl:apply-templates select="joined"/>
<xsl:apply-templates select="link"/>
$visible=0;
}

if($_REQUEST['e'] == 'AD' ||  $_REQUEST['e'] =='Новый')
{
list(<xsl:apply-templates select="col" mode="vars"/>)=array(<xsl:apply-templates select="col" mode="vars_init"/>);
if(!empty($_REQUEST['pid'])) $V_<xsl:apply-templates select="col[@is_parent='y']/@name"/> = $_REQUEST['pid'];
else $V_<xsl:apply-templates select="col[@is_parent='y']/@name"/> = 0;

<xsl:value-of select="preaddevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col[not(@primary)]" mode="preadd"/>

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Добавление - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
EOF;
<xsl:choose>
<xsl:when test="col[@link='y'][@is_parent='y'][not(@primary) and not(@parent) and @type!=11]/@name">print '<input type="hidden" name="pid" value="'.$_REQUEST['pid'].'"/>';</xsl:when>
<xsl:otherwise>print '<input type="hidden" name="pid" value="'.$_REQUEST['pid'].'" />';</xsl:otherwise>
</xsl:choose>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Добавить" class="gbt badd"/> 
<input type="submit" name="event" value="Отменить" class="gbt bcancel"/>
</td></tr>

<xsl:choose>
<xsl:when test="count(tabs) &gt; 0">
<xsl:variable name="tabscount" select="count(tabs/tab)"/>

<tr bgcolor="#FFFFFF" class="ftr"><td colspan="2">
<ul id="tabs" class="shadetabs">
<xsl:apply-templates select="tabs/tab" mode="list"/>
</ul>

<div style="border-top:1px solid gray; width:100%; margin-bottom: 1em; padding-top:10px;">
<xsl:apply-templates select="tabs/tab" mode="content_add"/>
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
<xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@isrealstate) and not(@internal)]|panel" mode="edit"/>
</xsl:otherwise>
</xsl:choose>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Добавить" class="gbt badd"/> 
<input type="submit" name="event" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table>
EOF;
$visible=0;
}

if($visible)
{
$parhash=array('0'=<xsl:text disable-output-escaping="yes">&gt;</xsl:text>'1');
$<xsl:value-of select="col[@primary='y']/@name"/>=$_REQUEST['id'];
$O_<xsl:value-of select="col[@primary='y']/@name"/>=$<xsl:value-of select="col[@primary='y']/@name"/>;
do 
{
  $PARENTID=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select PARENT_ID from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$<xsl:value-of select="col[@primary='y']/@name"/>);
  $parhash[$<xsl:value-of select="col[@primary='y']/@name"/>]=1;
  $<xsl:value-of select="col[@primary='y']/@name"/>=$PARENTID;
}while(isset($PARENTID));
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2"><xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="{@name}.php" method="POST">
<input type="hidden" name="r"><xsl:attribute name="value">{$_REQUEST['r']}</xsl:attribute></input>
<tr bgcolor="#F0F0F0"><td colspan="{count(col[@type!=11][@visuality='y'])+2}">
EOF;

if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>W)
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<input type="submit" name="e" value="Новый" class="gbt badd" />
EOF;

print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
</td></tr>
EOF;
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="head"/><form action="{@treechild}.php" method="POST"><th>
<!-- input type="text" name="q" value="">
<input type="image" src="img/filt.gif" name="find" alt="Поиск" title="Поиск" border="0" class="gbut" / -->
</th></form></tr><xsl:value-of select="pseudorow" disable-output-escaping="yes"/>
EOF;
print visibleTree($cmf,$_REQUEST['r'],0,$_REQUEST['r'],$parhash);
print '</form></table>';
}

function visibleTree($cmf,$parent,$level,$root,$parhash)
{
$width=$level*15+10;
$ret='';
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="name"/> from <xsl:apply-templates select="@name"/> where PARENT_ID=? order by <xsl:value-of select="col[@type=11]/@name"/>',$parent);
while ( list(<xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="vars"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{
<xsl:apply-templates select="col[@visuality='y']" mode="previsible"/>

<!-- if($level <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 1) 
        {
        $COLOR='#dddddd';
        }
else 
        {
        $COLOR='#CCCCCC'; 
        }; -->
<xsl:value-of select="postvisible" disable-output-escaping="yes"/>
  $ICONS=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
  <xsl:apply-templates select="rowicon"/>
EOF;
  $V_<xsl:value-of select="col[@isrealstate='y']/@name"/>=$V_<xsl:value-of select="col[@isrealstate='y']/@name"/>?'b':'d';
  $V_<xsl:value-of select="col[@isstate='y']/@name"/>=$V_<xsl:value-of select="col[@isstate='y']/@name"/>?0:1;
  $CO_=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select count(*) from <xsl:apply-templates select="@name"/> where PARENT_ID=?',$V_<xsl:value-of select="col[@primary='y']/@name"/>);
if(!$CO_)
 {
<xsl:choose>
<xsl:when test="@treechild=@name">
$folder=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<img src="i/f1.gif" class="fld"/><a href="{@name}.php?e=ED&amp;id=$V_{col[@primary='y']/@name}&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF;
</xsl:when>
<xsl:otherwise>
$folder=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<img src="i/f1.gif" class="fld"/><a href="{@treechild}.php?pid=$V_{col[@primary='y']/@name}" class="$V_REALSTATUS">$V_NAME</a>
EOF;
</xsl:otherwise>
</xsl:choose>
 }
else
 {
<xsl:choose>
<xsl:when test="@treechild=@name">
$folder=isset($parhash[$V_<xsl:value-of select="col[@primary='y']/@name"/>])?$folder=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a href="{@name}.php?id=$V_{col[@primary='y']/@name}&amp;r=$root" class="$V_REALSTATUS"><img src="i/f1.gif" class="fld"/></a><a href="{@name}.php?id=$V_{col[@primary='y']/@name}&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF
:
$folder=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a href="{@name}.php?id=$V_{col[@primary='y']/@name}&amp;r=$root" class="$V_REALSTATUS"><img src="i/f0.gif" class="fld"/></a><a href="{@name}.php?id=$V_{col[@primary='y']/@name}&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF;
</xsl:when>
<xsl:otherwise>
$folder=isset($parhash[$V_<xsl:value-of select="col[@primary='y']/@name"/>])?$folder=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a href="{@treechild}.php?pid=$V_{col[@primary='y']/@name}" class="$V_REALSTATUS"><img src="i/f1.gif" class="fld"/></a><a href="{@name}.php?id=$V_{col[@primary='y']/@name}&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF
:
$folder=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a href="{@treechild}.php?pid=$V_{col[@primary='y']/@name}" class="$V_REALSTATUS"><img src="i/f0.gif" class="fld"/></a><a href="{@name}.php?id=$V_{col[@primary='y']/@name}&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF;
</xsl:otherwise>
</xsl:choose>
 }

 $V_<xsl:value-of select="col[@isfold='y']/@name"/>=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
$folder <xsl:value-of select="nameadd" disable-output-escaping="yes"/>
EOF;
 
  $ret.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#ffffff">
<xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="varsprint"/><td nowrap="">
EOF;

if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>W)
$ret.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a href="{@name}.php?e=AD&amp;pid=$V_{col[@primary='y']/@name}&amp;r=$root"><img src="i/add.gif" border="0" title="Добавить" hspace="5"/></a>
<a href="{@name}.php?e=UP&amp;id=$V_{col[@primary='y']/@name}&amp;r=$root"><img src="i/up.gif" border="0" title="Вверх" hspace="5"/></a>
<a href="{@name}.php?e=DN&amp;id=$V_{col[@primary='y']/@name}&amp;r=$root"><img src="i/dn.gif" border="0" title="Вниз" hspace="5"/></a>
<a href="{@name}.php?e=ED&amp;id=$V_{col[@primary='y']/@name}&amp;r=$root"><img src="i/ed.gif" border="0" title="Изменить" hspace="5"/></a>
<a href="{@name}.php?e=VS&amp;id=$V_{col[@primary='y']/@name}&amp;o=$V_{col[@primary='y']/@name}"><img src="i/v$V_STATUS.gif" border="0"/></a><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text>
$ICONS
EOF;
if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>D)
{
$ret .=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a href="{@name}.php?e=DL&amp;id=$V_{col[@primary='y']/@name}&amp;r=$root" onclick="return dl();"><img src="i/del.gif" border="0" title="Удалить" hspace="5"/></a>
EOF;
}

  $ret.= '</td></tr>';

  if(isset($parhash[$V_<xsl:value-of select="col[@primary='y']/@name"/>])){$ret.=visibleTree($cmf,$V_<xsl:value-of select="col[@primary='y']/@name"/>,$level+1,$root,$parhash);}
}
return $ret;
}

function DelTree($cmf,$id)
{
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:value-of select="col[@primary='y']/@name"/> from <xsl:apply-templates select="@name"/> where PARENT_ID=?',$id);
while(list($V_<xsl:value-of select="col[@primary='y']/@name"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{
DelTree($cmf,$V_<xsl:value-of select="col[@primary='y']/@name"/>);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('delete from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_<xsl:value-of select="col[@primary='y']/@name"/>);
#### del items
}
}

function SetTreeRealStatus($cmf,$id,$state)
{
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:value-of select="col[@primary='y']/@name"/>,<xsl:value-of select="col[@isstate='y']/@name"/> from <xsl:apply-templates select="@name"/> where PARENT_ID=?',$id);
while(list($V_<xsl:value-of select="col[@primary='y']/@name"/>,$V_<xsl:value-of select="col[@isstate='y']/@name"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_<xsl:value-of select="col[@isstate='y']/@name"/>){SetTreeRealStatus($cmf,$V_<xsl:value-of select="col[@primary='y']/@name"/>,$state);}
if($state) {$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@isrealstate='y']/@name"/>=<xsl:value-of select="col[@isstate='y']/@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_<xsl:value-of select="col[@primary='y']/@name"/>);}
else {$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@isrealstate='y']/@name"/>=0 where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_<xsl:value-of select="col[@primary='y']/@name"/>);}
}
}

function GetMyRealStatus($cmf,$id)
{
$V_PARENT_ID=$id;
$V_FULLSTATUS=0;
while ($V_PARENT_ID<xsl:text disable-output-escaping="yes">&gt;</xsl:text>0)
{
list ($V_PARENT_ID,$V_STATUS)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select PARENT_ID,<xsl:value-of select="col[@isstate='y']/@name"/> from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$V_PARENT_ID);
$V_FULLSTATUS+=1-$V_STATUS;
}
if($V_FULLSTATUS){$V_FULLSTATUS=0;} else {$V_FULLSTATUS=1;}
return $V_FULLSTATUS;
}

$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>MakeCommonFooter();
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Close();
<xsl:value-of select="extrasubs" disable-output-escaping="yes"/>
?<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
-----------------------
</xsl:template>


<xsl:template match="config">
<xsl:apply-templates select="table"/>
</xsl:template>
</xsl:stylesheet>
