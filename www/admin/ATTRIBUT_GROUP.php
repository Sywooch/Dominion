<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('ATTRIBUT_GROUP');
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

      foreach ($_REQUEST['id'] as $id){
        $cmf->execute('delete from ATTRIBUT where ATTRIBUT_GROUP_ID=?',$id);
      }
    
foreach ($_REQUEST['id'] as $id)
 {
list($ORDERING)=$cmf->selectrow_array('select ORDERING from ATTRIBUT_GROUP where ATTRIBUT_GROUP_ID=?',$id);
$cmf->execute('update ATTRIBUT_GROUP set ORDERING=ORDERING-1 where ORDERING>?',$ORDERING);
$cmf->execute('delete from ATTRIBUT_GROUP where ATTRIBUT_GROUP_ID=?',$id);

 }

}


if($_REQUEST['e'] == 'UP')
{
list($ORDERING)=$cmf->selectrow_array('select ORDERING from ATTRIBUT_GROUP where ATTRIBUT_GROUP_ID=?',$_REQUEST['id']);
if($ORDERING>1)
{
$cmf->execute('update ATTRIBUT_GROUP set ORDERING=ORDERING+1 where ORDERING=?',$ORDERING-1);
$cmf->execute('update ATTRIBUT_GROUP set ORDERING=ORDERING-1 where ATTRIBUT_GROUP_ID=?',$_REQUEST['id']);
}
}

if($_REQUEST['e'] == 'DN')
{
list($ORDERING)=$cmf->selectrow_array('select ORDERING from ATTRIBUT_GROUP where ATTRIBUT_GROUP_ID=?',$_REQUEST['id']);
$MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from ATTRIBUT_GROUP');
if($ORDERING<$MAXORDERING)
{
$cmf->execute('update ATTRIBUT_GROUP set ORDERING=ORDERING-1 where ORDERING=?',$ORDERING+1);
$cmf->execute('update ATTRIBUT_GROUP set ORDERING=ORDERING+1 where ATTRIBUT_GROUP_ID=?',$_REQUEST['id']);
}
}


if($_REQUEST['e'] == 'Добавить')
{


$_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from ATTRIBUT_GROUP');
$_REQUEST['ORDERING']++;


$_REQUEST['id']=$cmf->GetSequence('ATTRIBUT_GROUP');





$cmf->execute('insert into ATTRIBUT_GROUP (ATTRIBUT_GROUP_ID,NAME,ORDERING) values (?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ORDERING']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{





$cmf->execute('update ATTRIBUT_GROUP set NAME=? where ATTRIBUT_GROUP_ID=?',stripslashes($_REQUEST['NAME']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_ATTRIBUT_GROUP_ID,$V_NAME)=
$cmf->selectrow_arrayQ('select ATTRIBUT_GROUP_ID,NAME from ATTRIBUT_GROUP where ATTRIBUT_GROUP_ID=?',$_REQUEST['id']);



@print <<<EOF
<h2 class="h2">Редактирование - Группы атрибутов товара</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="ATTRIBUT_GROUP.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название группы:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

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
list($V_ATTRIBUT_GROUP_ID,$V_NAME,$V_ORDERING)=array('','','');

@print <<<EOF
<h2 class="h2">Добавление - Группы атрибутов товара</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="ATTRIBUT_GROUP.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название группы:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

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


print '<h2 class="h2">Группы атрибутов товара</h2><form action="ATTRIBUT_GROUP.php" method="POST">';




$sth=$cmf->execute('select A.ATTRIBUT_GROUP_ID,A.NAME from ATTRIBUT_GROUP A where 1'.' order by A.ORDERING ');





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="4">
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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Название группы</th><td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_ATTRIBUT_GROUP_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_ATTRIBUT_GROUP_ID" /></td>
<td>$V_ATTRIBUT_GROUP_ID</td><td><a href="ATTRIBUT.php?pid=$V_ATTRIBUT_GROUP_ID" class="b">$V_NAME</a></td><td nowrap="">
<a href="ATTRIBUT_GROUP.php?e=UP&amp;id=$V_ATTRIBUT_GROUP_ID"><img src="i/up.gif" border="0" /></a>
<a href="ATTRIBUT_GROUP.php?e=DN&amp;id=$V_ATTRIBUT_GROUP_ID"><img src="i/dn.gif" border="0" /></a>
EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="ATTRIBUT_GROUP.php?e=ED&amp;id=$V_ATTRIBUT_GROUP_ID"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
