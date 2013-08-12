<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('SUBSCRIBE_CLIENT_GROUP');
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




if(!isset($_REQUEST['e1']))$_REQUEST['e1']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e1') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from SHOPUSER_SUBSCRIBE_CLIENT_GROUP where SUBSCRIBE_CLIENT_GROUP_ID=? and SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e1') == 'Изменить')
{




$cmf->execute('update SHOPUSER_SUBSCRIBE_CLIENT_GROUP set USER_ID=? where SUBSCRIBE_CLIENT_GROUP_ID=? and SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID=?',stripslashes($_REQUEST['USER_ID'])+0,$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{


$_REQUEST['iid']=$cmf->GetSequence('SHOPUSER_SUBSCRIBE_CLIENT_GROUP');






$cmf->execute('insert into SHOPUSER_SUBSCRIBE_CLIENT_GROUP (SUBSCRIBE_CLIENT_GROUP_ID,SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID,USER_ID) values (?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['USER_ID'])+0);
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID,$V_USER_ID)=$cmf->selectrow_arrayQ('select SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID,USER_ID from SHOPUSER_SUBSCRIBE_CLIENT_GROUP where SUBSCRIBE_CLIENT_GROUP_ID=? and SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_USER_ID=$cmf->Spravotchnik($V_USER_ID,'select USER_ID,if(SURNAME is null, NAME, concat(SURNAME,\' \',NAME)) from SHOPUSER  order by if(SURNAME is null, NAME, concat(SURNAME,\' \',NAME))');        
					
@print <<<EOF
<h2 class="h2">Редактирование - Связь группы с участиниками</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="SUBSCRIBE_CLIENT_GROUP.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'" />';

@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Участник:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="USER_ID">
				
				
				
				
				
				
				$V_STR_USER_ID
			</select><br />
		
	

</td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;





$visible=0;
}

if($cmf->Param('e1') == 'Новый')
{
list($V_SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID,$V_USER_ID)=array('','');


$V_STR_USER_ID=$cmf->Spravotchnik($V_USER_ID,'select USER_ID,if(SURNAME is null, NAME, concat(SURNAME,\' \',NAME)) from SHOPUSER  order by if(SURNAME is null, NAME, concat(SURNAME,\' \',NAME))');
					
@print <<<EOF
<h2 class="h2">Добавление - Связь группы с участиниками</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="SUBSCRIBE_CLIENT_GROUP.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Участник:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="USER_ID">
				
				
				
				
				
				
				$V_STR_USER_ID
			</select><br />
		
	

</td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}









if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
$cmf->execute('delete from SUBSCRIBE_CLIENT_GROUP where SUBSCRIBE_CLIENT_GROUP_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('SUBSCRIBE_CLIENT_GROUP');


$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into SUBSCRIBE_CLIENT_GROUP (SUBSCRIBE_CLIENT_GROUP_ID,NAME,STATUS) values (?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['STATUS']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{



$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('update SUBSCRIBE_CLIENT_GROUP set NAME=?,STATUS=? where SUBSCRIBE_CLIENT_GROUP_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_SUBSCRIBE_CLIENT_GROUP_ID,$V_NAME,$V_STATUS)=
$cmf->selectrow_arrayQ('select SUBSCRIBE_CLIENT_GROUP_ID,NAME,STATUS from SUBSCRIBE_CLIENT_GROUP where SUBSCRIBE_CLIENT_GROUP_ID=?',$_REQUEST['id']);



$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Группа адресов рассылки</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="SUBSCRIBE_CLIENT_GROUP.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Имя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;




print <<<EOF
<a name="f1"></a><h3 class="h3">Связь группы с участиниками</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="SUBSCRIBE_CLIENT_GROUP.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="4">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID,USER_ID from SHOPUSER_SUBSCRIBE_CLIENT_GROUP where SUBSCRIBE_CLIENT_GROUP_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>Участник</th><td></td></tr>
EOF;
while(list($V_SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID,$V_USER_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_USER_ID=$cmf->selectrow_arrayQ('select if(SURNAME is null, NAME, concat(SURNAME,\' \',NAME)) from SHOPUSER where USER_ID=?',$V_USER_ID);
                                        


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID" /></td>
<td>$V_SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID</td><td>$V_USER_ID</td><td nowrap="">

<a href="SUBSCRIBE_CLIENT_GROUP.php?e1=ED&amp;iid=$V_SHOPUSER_SUBSCRIBE_CLIENT_GROUP_ID&amp;id={$_REQUEST['id']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_SUBSCRIBE_CLIENT_GROUP_ID,$V_NAME,$V_STATUS)=array('','','');

$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Группа адресов рассылки</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="SUBSCRIBE_CLIENT_GROUP.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Имя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

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


print '<h2 class="h2">Группа адресов рассылки</h2><form action="SUBSCRIBE_CLIENT_GROUP.php" method="POST">';



$_REQUEST['s']+=0;
$SORTNAMES=array('N','Имя');
$SORTQUERY=array('order by A.SUBSCRIBE_CLIENT_GROUP_ID ','order by A.SUBSCRIBE_CLIENT_GROUP_ID desc ','order by A.NAME ','order by A.NAME desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SUBSCRIBE_CLIENT_GROUP.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SUBSCRIBE_CLIENT_GROUP.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SUBSCRIBE_CLIENT_GROUP.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}



$sth=$cmf->execute('select A.SUBSCRIBE_CLIENT_GROUP_ID,A.NAME,A.STATUS from SUBSCRIBE_CLIENT_GROUP A where 1'.' '.$SORTQUERY[$_REQUEST['s']]);





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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td>$HEADER<td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_SUBSCRIBE_CLIENT_GROUP_ID,$V_NAME,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="id[]" value="$V_SUBSCRIBE_CLIENT_GROUP_ID" /></td>
<td>$V_SUBSCRIBE_CLIENT_GROUP_ID</td><td>$V_NAME</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="SUBSCRIBE_CLIENT_GROUP.php?e=ED&amp;id=$V_SUBSCRIBE_CLIENT_GROUP_ID&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
