<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('PRICE_EXPORT');
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
$cmf->execute('delete from PRICE_EXPORT where PRICE_EXPORT_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('PRICE_EXPORT');





$cmf->execute('insert into PRICE_EXPORT (PRICE_EXPORT_ID,NAME,INDENT) values (?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['INDENT']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{





$cmf->execute('update PRICE_EXPORT set NAME=?,INDENT=? where PRICE_EXPORT_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['INDENT']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_PRICE_EXPORT_ID,$V_NAME,$V_INDENT)=
$cmf->selectrow_arrayQ('select PRICE_EXPORT_ID,NAME,INDENT from PRICE_EXPORT where PRICE_EXPORT_ID=?',$_REQUEST['id']);



@print <<<EOF
<h2 class="h2">Редактирование - Экспорт прайсов</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="PRICE_EXPORT.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(INDENT);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Идентификатор:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="INDENT" value="$V_INDENT" size="90" /><br />

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
list($V_PRICE_EXPORT_ID,$V_NAME,$V_INDENT)=array('','','');

@print <<<EOF
<h2 class="h2">Добавление - Экспорт прайсов</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="PRICE_EXPORT.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(INDENT);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Идентификатор:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="INDENT" value="$V_INDENT" size="90" /><br />

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


print '<h2 class="h2">Экспорт прайсов</h2><form action="PRICE_EXPORT.php" method="POST">';



$_REQUEST['s']+=0;
$SORTNAMES=array('N','Название','Идентификатор');
$SORTQUERY=array('order by A.PRICE_EXPORT_ID ','order by A.PRICE_EXPORT_ID desc ','order by A.NAME ','order by A.NAME desc ','order by A.INDENT ','order by A.INDENT desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="PRICE_EXPORT.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="PRICE_EXPORT.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="PRICE_EXPORT.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}



$sth=$cmf->execute('select A.PRICE_EXPORT_ID,A.NAME,A.INDENT from PRICE_EXPORT A where 1'.' '.$SORTQUERY[$_REQUEST['s']]);





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
while(list($V_PRICE_EXPORT_ID,$V_NAME,$V_INDENT)=mysql_fetch_array($sth, MYSQL_NUM))
{


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_PRICE_EXPORT_ID" /></td>
<td>$V_PRICE_EXPORT_ID</td><td>$V_NAME</td><td>$V_INDENT</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="PRICE_EXPORT.php?e=ED&amp;id=$V_PRICE_EXPORT_ID&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
