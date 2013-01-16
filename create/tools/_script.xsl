<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml"  omit-xml-declaration="yes" encoding="UTF-8"/>

<xsl:template match="config/table" xml:space="preserve">-----------------------|scripts/<xsl:value-of select="@name"/>.php|
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
<xsl:apply-templates select="col[@type=17]" mode="enumcreate"/>
<xsl:apply-templates select="joined/col[@type=10]" mode="enumcreate"/>
<xsl:apply-templates select="joined/col[@type=17]" mode="enumcreate"/>

if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';
<xsl:if test="@letter">if(!isset($_REQUEST['l']))$_REQUEST['l']='';</xsl:if>

<xsl:value-of select="extraevents" disable-output-escaping="yes"/>
<xsl:apply-templates select="joined" mode="events"/>
<xsl:apply-templates select="link" mode="events"/>

<xsl:apply-templates select="joined/joined" mode="events">

</xsl:apply-templates>


<xsl:if test="col[@input='y']">
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


if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>D)
{
<xsl:value-of select="predeletesevent" disable-output-escaping="yes"/>
foreach ($_REQUEST['id'] as $id)
 {<xsl:if test="col[@type=11]">
list($ORDERING)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$id);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="col[@type=11]/@name"/><xsl:text disable-output-escaping="yes">&gt;</xsl:text>?',$ORDERING);</xsl:if>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('delete from <xsl:apply-templates select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$id);
<xsl:value-of select="postdeleteevent" disable-output-escaping="yes"/>
 }
<xsl:value-of select="postdeletesevent" disable-output-escaping="yes"/>
}

<xsl:if test="col/@type=11">
if($_REQUEST['e'] == 'UP')
{
list($ORDERING)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
if($ORDERING<xsl:text disable-output-escaping="yes">&gt;</xsl:text>1)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>+1 where <xsl:value-of select="col[@type=11]/@name"/>=?',$ORDERING-1);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
}
}

if($_REQUEST['e'] == 'DN')
{
list($ORDERING)=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select <xsl:value-of select="col[@type=11]/@name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
$MAXORDERING=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:value-of select="@name"/>');
if($ORDERING<xsl:text disable-output-escaping="yes">&lt;</xsl:text>$MAXORDERING)
{
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>-1 where <xsl:value-of select="col[@type=11]/@name"/>=?',$ORDERING+1);
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:value-of select="col[@type=11]/@name"/>=<xsl:value-of select="col[@type=11]/@name"/>+1 where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
}
}
</xsl:if>

if($_REQUEST['e'] == 'Добавить')
{
<xsl:value-of select="preinsertevent" disable-output-escaping="yes"/>
<xsl:if test="col/@type=11">
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select max(<xsl:value-of select="col[@type=11]/@name"/>) from <xsl:apply-templates select="@name"/>');
$_REQUEST['<xsl:value-of select="col[@type=11]/@name"/>']++;
</xsl:if>

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

$_REQUEST['e']='ED';
<xsl:value-of select="postinsertevent" disable-output-escaping="yes"/>
}

if($_REQUEST['e'] == 'Изменить')
{
<xsl:value-of select="preupdateevent" disable-output-escaping="yes"/>
<xsl:apply-templates select="col" mode="preinsert"/>
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('update <xsl:value-of select="@name"/> set <xsl:apply-templates select="col[not(@primary) and @type!=11 and not(@internal)]" mode="update"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',<xsl:apply-templates select="col[not(@primary ) and @type!=11 and not(@internal)]" mode="form"/>,$_REQUEST['id']);
$_REQUEST['e']='ED';
<xsl:value-of select="postupdateevent" disable-output-escaping="yes"/>
};

if($_REQUEST['e'] == 'ED')
{
list(<xsl:apply-templates select="col[@type!=11 and not(@internal)]" mode="vars"/>)=
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_arrayQ('select <xsl:apply-templates select="col[@type!=11 and not(@internal)]" mode="name"/> from <xsl:value-of select="@name"/> where <xsl:value-of select="col[@primary='y']/@name"/>=?',$_REQUEST['id']);
<xsl:value-of select="preeditevent" disable-output-escaping="yes"/>

<xsl:apply-templates select="col[not(@primary) and not(@internal)]" mode="preedit"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Редактирование - <xsl:apply-templates select="name"/></h2>

<xsl:choose>
        <xsl:when test="edit!=''">
                <xsl:value-of select="edit" disable-output-escaping="yes"/>
</xsl:when>
<xsl:otherwise>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col" mode="formvalid"/>;</xsl:attribute>
<input type="hidden" name="id"><xsl:attribute name="value">{$_REQUEST['id']}</xsl:attribute></input><xsl:if test="@type">
<input type="hidden" name="type" value="{@type}"/></xsl:if>
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)"><input type="hidden" name="s"><xsl:attribute name="value">{$REQUEST['s']}</xsl:attribute></input></xsl:if>
<xsl:if test="@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e" value="Отменить" class="gbt bcancel"/>
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
<xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@internal)]|panel|pseudocol" mode="edit"/>
</xsl:otherwise>
</xsl:choose>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:if test="@type">
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml"/><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text></xsl:if>
<input type="submit" name="e" value="Отменить" class="gbt bcancel"/>
</td></tr>
</form>
</table><br/>
</xsl:otherwise>
</xsl:choose>
EOF;

<xsl:apply-templates select="joined"/>
<xsl:apply-templates select="link"/>
$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list(<xsl:apply-templates select="col" mode="vars"/>)=array(<xsl:apply-templates select="col" mode="vars_init"/>);
<xsl:apply-templates select="col[not(@primary) and not(@internal)]" mode="preadd"/>
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<h2 class="h2">Добавление - <xsl:apply-templates select="name"/></h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="{@name}.php" ENCTYPE="multipart/form-data"><xsl:attribute name="onsubmit">return true <xsl:apply-templates select="col[not(@internal)]" mode="formvalid"/>;</xsl:attribute>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd"/> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel"/>
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
<xsl:apply-templates select="col[not(@primary) and not(@parent) and @type!=11 and not(@internal)]|panel" mode="edit"/>
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
</xsl:if>

<xsl:if test="@letter">
$LETER='';
//if($_REQUEST['l']){$LETER=pack("C",$_REQUEST['l']);}
if($_REQUEST['l']){$LETER=$_REQUEST['l'];}
</xsl:if>
print '<h2 class="h2"><xsl:apply-templates select="name"/></h2><form action="{@name}.php" method="POST">';
<xsl:if test="@letter">
//$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select ascii(upper(substring(<xsl:value-of select="@letter"/>,1,1))) from <xsl:value-of select="@name"/><xsl:if test="where"> where '.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if> group by substring(<xsl:value-of select="@letter"/>,1,1)');
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select LEFT(<xsl:value-of select="@letter"/>,1) from <xsl:value-of select="@name"/><xsl:if test="where"> where '.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if> group by LEFT(<xsl:value-of select="@letter"/>,1)');
while(list ($id)=mysql_fetch_array($sth, MYSQL_NUM))
{
//$V_LETER=pack("C",$id);
$V_LETER=$id;
//if($id==32){$V_LETER='#';}
if($_REQUEST['l']==$id)
 { 
  print '<b class="red">'.$V_LETER.'</b> ';
 } 
 else 
 {
  print '<a href="{@name}.php?l='.urlencode($id).'" class="t">'.$V_LETER.'</a> ';
 }
}
$_REQUEST['l'] = urlencode($_REQUEST['l']);

print '<xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><xsl:text disable-output-escaping="yes">&amp;#160;</xsl:text><a href="{@name}.php" class="t">все</a><br/>';
</xsl:if><xsl:variable name="p"><xsl:if test="@pagesize">&amp;p={$_REQUEST['p']}</xsl:if><xsl:if test="@letter">&amp;l={$_REQUEST['l']}</xsl:if><xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">&amp;s={$_REQUEST['s']}</xsl:if></xsl:variable>
<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">
<xsl:if test="@defsort">if($_REQUEST['s'] == ''){$_REQUEST['s']=<xsl:value-of select="@defsort"/>;}</xsl:if>
$_REQUEST['s']+=0;
$SORTNAMES=array(<xsl:apply-templates select="col[@visuality='y']|calccol" mode="sortnames"/>);
$SORTQUERY=array(<xsl:apply-templates select="col[@visuality='y']|calccol" mode="sortquerys"/>);
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?s=$tmps<xsl:if test="@letter">&amp;l={$_REQUEST['l']}</xsl:if>{$filtpath}</xsl:attribute>$tmp <img src='i/sdn.gif' border='0'/></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?s=$tmps<xsl:if test="@letter">&amp;l={$_REQUEST['l']}</xsl:if>{$filtpath}</xsl:attribute>$tmp <img src='i/sup.gif' border='0'/></a></th>
EOF;
        } 
        else { 
$HEADER.=<xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<th nowrap=''><a class='b'><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?s=$tmps<xsl:if test="@letter">&amp;l={$_REQUEST['l']}</xsl:if>{$filtpath}</xsl:attribute>$tmp</a></th>
EOF;
        }
        $i++;
}
</xsl:if>
<xsl:choose>
<xsl:when test="@pagesize">
$pagesize=<xsl:value-of select="@pagesize"/>;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{
<xsl:choose>
<xsl:when test="@letter">
if($_REQUEST['l']!=''){ $_REQUEST['count']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select count(*) from <xsl:apply-templates select="@name"/> A<xsl:text> </xsl:text>where LEFT(<xsl:value-of select="@letter"/>,1)=?<xsl:if test="where"> and '.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if><xsl:apply-templates select="col[@filt='y']" mode="filt"/>',$LETER);}
else {$_REQUEST['count']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select count(*) from <xsl:apply-templates select="@name"/> A where 1'<xsl:if test="where">.' and'.<xsl:value-of select="where" disable-output-escaping="yes"/></xsl:if><xsl:apply-templates select="col[@filt='y']" mode="filt"/>);}
</xsl:when>
<xsl:otherwise>
$_REQUEST['count']=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>selectrow_array('select count(*) from <xsl:apply-templates select="@name"/> A where 1'<xsl:if test="where">.' and'.<xsl:value-of select="where" disable-output-escaping="yes"/></xsl:if><xsl:apply-templates select="col[@filt='y']" mode="filt"/>);
</xsl:otherwise>
</xsl:choose>
$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] <xsl:text disable-output-escaping="yes">&gt;</xsl:text> $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] <xsl:text disable-output-escaping="yes">&gt;</xsl:text> 1)
{
 for($i=1;$i<xsl:text disable-output-escaping="yes">&lt;</xsl:text>=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
- <a class="t"><xsl:attribute name="href"><xsl:value-of select="@name"/>.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}<xsl:if test="@letter">&amp;l={$_REQUEST['l']}</xsl:if><xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">&amp;s={$_REQUEST['s']}</xsl:if>{$filtpath}</xsl:attribute>$i</a>
EOF;
}
 }
 print'<br/>';
}
<xsl:if test="@letter"> if($_REQUEST['l']!=''){</xsl:if>

$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A where 1<xsl:choose><xsl:when test="@letter"><xsl:text> </xsl:text>and LEFT(A.<xsl:value-of select="@letter"/>,1)=?<xsl:if test="where"> and'.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if></xsl:when><xsl:otherwise><xsl:if test="where"> and '.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if>'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'</xsl:otherwise></xsl:choose><xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> <xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">'.$SORTQUERY[$_REQUEST['s']].'</xsl:if>limit ?,?',<xsl:if test="@letter">$LETER,</xsl:if>$pagesize*($_REQUEST['p']-1),$pagesize);
<xsl:if test="@letter">}
else
{
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A where 1<xsl:if test="where"> and '.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if>'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'<xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);
}</xsl:if>
</xsl:when>
<xsl:otherwise>
<xsl:if test="@letter"> if($_REQUEST['l']!=''){</xsl:if>
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A where 1<xsl:choose><xsl:when test="@letter"><xsl:text> </xsl:text>and LEFT(A.<xsl:value-of select="@letter"/>,1)=?<xsl:if test="where"> and '.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if>'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'</xsl:when><xsl:otherwise><xsl:if test="where"> and '.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if>'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'</xsl:otherwise></xsl:choose><xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> '<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">.$SORTQUERY[$_REQUEST['s']]</xsl:if><xsl:if test="@letter">,$LETER</xsl:if>);
<xsl:if test="@letter">}
else
{
$sth=$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>execute('select <xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="aname"/> from <xsl:apply-templates select="@name"/> A where 1<xsl:if test="where"> and '.<xsl:value-of select="where" disable-output-escaping="yes"/>.'</xsl:if>'<xsl:apply-templates select="col[@filt='y']" mode="filt"/>.'<xsl:choose><xsl:when test="@ordering"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="@ordering"/></xsl:when><xsl:otherwise><xsl:if test="col/@type=11"><xsl:text> </xsl:text>order<xsl:text> </xsl:text>by<xsl:text> </xsl:text>A.<xsl:value-of select="col[@type=11]/@name"/></xsl:if></xsl:otherwise></xsl:choose> '<xsl:if test="not(@ordering) and not(col/@type=11) and not(@nosort)">.$SORTQUERY[$_REQUEST['s']]</xsl:if>);
}</xsl:if>
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
<img src="img/hi.gif" width="1" height="3"/><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="{count(col[@type!=11][@visuality='y'])+2}"><xsl:if test="number(filter/@cols) &gt; 0"><xsl:attribute name="colspan"><xsl:value-of select="count(col[@type!=11][@visuality='y'])+2-number(filter/@cols)"/></xsl:attribute></xsl:if>
EOF;

if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>W)
@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<input type="submit" name="e" value="Новый" class="gbt badd" />
EOF;

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<img src="img/hi.gif" width="4" height="1"/><xsl:if test="col[@input='y']"><input type="submit" name="e" value="Применить" class="gbt bsave" /><img src="i/0.gif" width="4" height="1"/></xsl:if><xsl:apply-templates select="extrakey"/>
EOF;
if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>D)
  print '<input type="submit" name="e" onclick="return dl();" value="Удалить" class="gbt bdel" />';

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<input type="hidden" name="p"><xsl:attribute name="value">{$_REQUEST['p']}</xsl:attribute></input>
<xsl:if test="@letter"><input type="hidden" name="l"><xsl:attribute name="value">{$_REQUEST['l']}</xsl:attribute></input></xsl:if>
</td><xsl:value-of select="filter" disable-output-escaping="yes"/></tr>
EOF;

print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');"/></td><xsl:choose><xsl:when test="not(@ordering) and not(col/@type=11) and not(@nosort)">$HEADER</xsl:when><xsl:otherwise><xsl:apply-templates select="col[@type!=11][@visuality='y']" mode="head"/></xsl:otherwise></xsl:choose><td></td></tr>
<xsl:value-of disable-output-escaping="yes" select="pseudorow[not(@last)]" /> 
EOF;

if(is_resource($sth))
while(list(<xsl:apply-templates select="col[@type!=11][@visuality='y' or @selectible='y']" mode="vars"/>)=mysql_fetch_array($sth, MYSQL_NUM))
{<xsl:value-of select="previsible" disable-output-escaping="yes"/><xsl:apply-templates select="col[@visuality='y']" mode="previsible"/>
<xsl:if test="col[@isstate='y']">if($V_<xsl:value-of select="col[@isstate='y']/@name"/>){$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#FFFFFF';} else {$V_<xsl:value-of select="col[@isstate='y']/@name"/>='#a0a0a0';}</xsl:if>
<xsl:value-of select="postvisible" disable-output-escaping="yes"/>
print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<tr bgcolor="#FFFFFF"><xsl:if test="col[@isstate='y']"><xsl:attribute name="bgcolor">$V_<xsl:value-of select="col[@isstate='y']/@name"/></xsl:attribute></xsl:if>
<td><input type="checkbox" name="id[]" value="$V_{col[@primary='y']/@name}"/></td>
<xsl:apply-templates select="col[@type!=11][@visuality='y']|calccol" mode="varsprint"/><td nowrap="">
<xsl:if test="col/@type=11"><a href="{@name}.php?e=UP&amp;id=$V_{col[@primary='y']/@name}"><img src="i/up.gif" border="0"/></a>
<a href="{@name}.php?e=DN&amp;id=$V_{col[@primary='y']/@name}"><img src="i/dn.gif" border="0"/></a></xsl:if>
EOF;

if ($cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>W)
{

@print <xsl:text disable-output-escaping="yes">&lt;&lt;&lt;</xsl:text>EOF
<a href="{@name}.php?e=ED&amp;id=$V_{col[@primary='y']/@name}{$p}"><img src="i/ed.gif" border="0" title="Изменить"/></a>


<xsl:apply-templates select="child_script"/><xsl:apply-templates select="rowicon"/></td></tr>
EOF;
}
}
<xsl:value-of disable-output-escaping="yes" select="pseudorow[@last = 'y']" /> 
print '</table>';
}
print '</form>';
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>MakeCommonFooter();
$cmf-<xsl:text disable-output-escaping="yes">&gt;</xsl:text>Close();
<xsl:value-of select="extrasubs" disable-output-escaping="yes"/>
?<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
-----------------------
</xsl:template> 
</xsl:stylesheet>
