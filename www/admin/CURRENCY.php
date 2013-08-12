<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('CURRENCY');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;



$cmf->ENUM_SNAME_TYPE=array('--',' постфикс ',' префикс');





if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';










if($_REQUEST['e'] == 'Применить' and is_array($_REQUEST['id']))
{
foreach ($_REQUEST['id'] as $id)
{
 $cmf->execute('update CURRENCY set STATUS=? where CURRENCY_ID=?',intval($_REQUEST['STATUS_'.$id]),$id);

}

};



if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
list($ORDERING)=$cmf->selectrow_array('select ORDERING from CURRENCY where CURRENCY_ID=?',$id);
$cmf->execute('update CURRENCY set ORDERING=ORDERING-1 where ORDERING>?',$ORDERING);
$cmf->execute('delete from CURRENCY where CURRENCY_ID=?',$id);

 }

}


if($_REQUEST['e'] == 'UP')
{
list($ORDERING)=$cmf->selectrow_array('select ORDERING from CURRENCY where CURRENCY_ID=?',$_REQUEST['id']);
if($ORDERING>1)
{
$cmf->execute('update CURRENCY set ORDERING=ORDERING+1 where ORDERING=?',$ORDERING-1);
$cmf->execute('update CURRENCY set ORDERING=ORDERING-1 where CURRENCY_ID=?',$_REQUEST['id']);
}
}

if($_REQUEST['e'] == 'DN')
{
list($ORDERING)=$cmf->selectrow_array('select ORDERING from CURRENCY where CURRENCY_ID=?',$_REQUEST['id']);
$MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from CURRENCY');
if($ORDERING<$MAXORDERING)
{
$cmf->execute('update CURRENCY set ORDERING=ORDERING-1 where ORDERING=?',$ORDERING+1);
$cmf->execute('update CURRENCY set ORDERING=ORDERING+1 where CURRENCY_ID=?',$_REQUEST['id']);
}
}


if($_REQUEST['e'] == 'Добавить')
{


$_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from CURRENCY');
$_REQUEST['ORDERING']++;


$_REQUEST['id']=$cmf->GetSequence('CURRENCY');






$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;



$cmf->execute('insert into CURRENCY (CURRENCY_ID,NAME,SNAME,SNAME_TYPE,SYSTEM_NAME,PRICE,STATUS,ORDERING) values (?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['SNAME']),stripslashes($_REQUEST['SNAME_TYPE']),stripslashes($_REQUEST['SYSTEM_NAME']),stripslashes($_REQUEST['PRICE'])+0,stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['ORDERING']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{







$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('update CURRENCY set NAME=?,SNAME=?,SNAME_TYPE=?,SYSTEM_NAME=?,PRICE=?,STATUS=? where CURRENCY_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['SNAME']),stripslashes($_REQUEST['SNAME_TYPE']),stripslashes($_REQUEST['SYSTEM_NAME']),stripslashes($_REQUEST['PRICE'])+0,stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_CURRENCY_ID,$V_NAME,$V_SNAME,$V_SNAME_TYPE,$V_SYSTEM_NAME,$V_PRICE,$V_STATUS)=
$cmf->selectrow_arrayQ('select CURRENCY_ID,NAME,SNAME,SNAME_TYPE,SYSTEM_NAME,PRICE,STATUS from CURRENCY where CURRENCY_ID=?',$_REQUEST['id']);



$V_STR_SNAME_TYPE=$cmf->Enumerator($cmf->ENUM_SNAME_TYPE,$V_SNAME_TYPE);
$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Валюта</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CURRENCY.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(SNAME) &amp;&amp; checkXML(SYSTEM_NAME);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SNAME" value="$V_SNAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое название - префикс/постфикс:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="SNAME_TYPE">$V_STR_SNAME_TYPE</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Уникальный id валюты:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SYSTEM_NAME" value="$V_SYSTEM_NAME" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Курс к вирт. единице:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PRICE" value="$V_PRICE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>


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
list($V_CURRENCY_ID,$V_NAME,$V_SNAME,$V_SNAME_TYPE,$V_SYSTEM_NAME,$V_PRICE,$V_STATUS,$V_ORDERING)=array('','','','','','','','');

$V_STR_SNAME_TYPE=$cmf->Enumerator($cmf->ENUM_SNAME_TYPE,-1);
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Валюта</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CURRENCY.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(SNAME) &amp;&amp; checkXML(SYSTEM_NAME);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SNAME" value="$V_SNAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое название - префикс/постфикс:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="SNAME_TYPE">$V_STR_SNAME_TYPE</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Уникальный id валюты:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SYSTEM_NAME" value="$V_SYSTEM_NAME" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Курс к вирт. единице:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PRICE" value="$V_PRICE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>

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
list($filtpath,$filtwhere)=array('','');
foreach($_REQUEST as $key=>$val)
{
  if(preg_match('/^FLT_(.+)$/',$key,$p))
  {
    if($val!='')
     {
        $filtpath.='&amp;'.$key.'='.$val;
     }
  }
}



print '<h2 class="h2">Валюта</h2><form action="CURRENCY.php" method="POST">';




$sth=$cmf->execute('select A.CURRENCY_ID,A.NAME,A.SNAME,A.SYSTEM_NAME,A.PRICE,A.STATUS from CURRENCY A where 1'
.($cmf->Param('FLT_NAME')?' and A.NAME like \''.mysql_escape_string($_REQUEST['FLT_NAME'].'%')."'":'').' order by A.ORDERING ');





@print <<<EOF
<table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />

<tr bgcolor="#F0F0F0"><td colspan="2"><input type="submit" name="e" value="Фильтр" class="gbt bflt" /></td></tr>
<tr bgcolor="#FFFFFF"><th>Название<br /><img src="i/0.gif" width="125" height="1" /></th><td><input class="form_input_big" type="text" name="FLT_NAME" size="90" value="{$_REQUEST['FLT_NAME']}" /><br /></td></tr>
</table>
EOF;


@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="8">
EOF;

if ($cmf->W)
@print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" />
EOF;

@print <<<EOF
<img src="img/hi.gif" width="4" height="1" /><input type="submit" name="e" value="Применить" class="gbt bsave" /><img src="i/0.gif" width="4" height="1" />
EOF;
if ($cmf->D)
  print '<input type="submit" name="e" onclick="return dl();" value="Удалить" class="gbt bdel" />';

@print <<<EOF
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;

print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Название</th><th>Краткое название</th><th>Уникальный id валюты</th><th>Курс к вирт. единице</th><th>Вкл</th><td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_CURRENCY_ID,$V_NAME,$V_SNAME,$V_SYSTEM_NAME,$V_PRICE,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_STR_STATUS=$V_STATUS?'checked':'';
                        
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="id[]" value="$V_CURRENCY_ID" /></td>
<td>$V_CURRENCY_ID</td><td>$V_NAME</td><td>$V_SNAME</td><td>$V_SYSTEM_NAME</td><td>$V_PRICE</td><td><input onclick="ch(this)" type="checkbox" name="STATUS_$V_CURRENCY_ID" class="i" value="1" $V_STR_STATUS/></td><td nowrap="">
<a href="CURRENCY.php?e=UP&amp;id=$V_CURRENCY_ID"><img src="i/up.gif" border="0" /></a>
<a href="CURRENCY.php?e=DN&amp;id=$V_CURRENCY_ID"><img src="i/dn.gif" border="0" /></a>
EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="CURRENCY.php?e=ED&amp;id=$V_CURRENCY_ID"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
