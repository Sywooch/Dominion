<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('TRANSLIT_RULE');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;







if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';












if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
$cmf->execute('delete from TRANSLIT_RULE where TRANSLIT_RULE_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('TRANSLIT_RULE');





$cmf->execute('insert into TRANSLIT_RULE (TRANSLIT_RULE_ID,SRC,TRANSLIT) values (?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['SRC']),stripslashes($_REQUEST['TRANSLIT']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{





$cmf->execute('update TRANSLIT_RULE set SRC=?,TRANSLIT=? where TRANSLIT_RULE_ID=?',stripslashes($_REQUEST['SRC']),stripslashes($_REQUEST['TRANSLIT']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_TRANSLIT_RULE_ID,$V_SRC,$V_TRANSLIT)=
$cmf->selectrow_arrayQ('select TRANSLIT_RULE_ID,SRC,TRANSLIT from TRANSLIT_RULE where TRANSLIT_RULE_ID=?',$_REQUEST['id']);



@print <<<EOF
<h2 class="h2">Редактирование - Правила транслитерации</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="TRANSLIT_RULE.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(SRC) &amp;&amp; checkXML(TRANSLIT);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Исх.символ(ы):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SRC" value="$V_SRC" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Транслит:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TRANSLIT" value="$V_TRANSLIT" size="90" /><br />

</td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;



$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_TRANSLIT_RULE_ID,$V_SRC,$V_TRANSLIT)=array('','','');

@print <<<EOF
<h2 class="h2">Добавление - Правила транслитерации</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="TRANSLIT_RULE.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(SRC) &amp;&amp; checkXML(TRANSLIT);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Исх.символ(ы):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SRC" value="$V_SRC" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Транслит:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TRANSLIT" value="$V_TRANSLIT" size="90" /><br />

</td></tr>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;
$visible=0;
}

if($visible)
{


print '<h2 class="h2">Правила транслитерации</h2><form action="TRANSLIT_RULE.php" method="POST">';



$_REQUEST['s']+=0;
$SORTNAMES=array('N','Исх.символ(ы)','Транслит');
$SORTQUERY=array('order by A.TRANSLIT_RULE_ID ','order by A.TRANSLIT_RULE_ID desc ','order by A.SRC ','order by A.SRC desc ','order by A.TRANSLIT ','order by A.TRANSLIT desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="TRANSLIT_RULE.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="TRANSLIT_RULE.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="TRANSLIT_RULE.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}


$pagesize=50;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from TRANSLIT_RULE A where 1');

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="TRANSLIT_RULE.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.TRANSLIT_RULE_ID,A.SRC,A.TRANSLIT from TRANSLIT_RULE A where 1'.' '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="5">
EOF;

if ($cmf->W)
@print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" />
EOF;

@print <<<EOF
<img src="img/hi.gif" width="4" height="1" />
EOF;
if ($cmf->D)
  print '<input type="submit" name="e" onclick="return dl();" value="Удалить" class="gbt bdel" />';

@print <<<EOF
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;

print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td>$HEADER<td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_TRANSLIT_RULE_ID,$V_SRC,$V_TRANSLIT)=mysql_fetch_array($sth, MYSQL_NUM))
{


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_TRANSLIT_RULE_ID" /></td>
<td>$V_TRANSLIT_RULE_ID</td><td>$V_SRC</td><td>$V_TRANSLIT</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="TRANSLIT_RULE.php?e=ED&amp;id=$V_TRANSLIT_RULE_ID&amp;p={$_REQUEST['p']}&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


</td></tr>
EOF;
}
}
 
print '</table>';
}
print '</form>';
$cmf->MakeCommonFooter();
$cmf->Close();

?>
