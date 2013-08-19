<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('CMF_USER');
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

$cmf->execute('delete from CMF_USER_GROUP where CMF_USER_ID=? and CMF_GROUP_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e1') == 'Изменить')
{



$cmf->execute('update CMF_USER_GROUP set CMF_GROUP_ID=? where CMF_USER_ID=? and CMF_GROUP_ID=?',stripslashes($_REQUEST['CMF_GROUP_ID'])+0,$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{


$_REQUEST['iid']=$_REQUEST['CMF_GROUP_ID'];





$cmf->execute('insert into CMF_USER_GROUP (CMF_USER_ID,CMF_GROUP_ID) values (?,?)',$_REQUEST['id'],$_REQUEST['iid']);
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_CMF_GROUP_ID)=$cmf->selectrow_arrayQ('select CMF_GROUP_ID from CMF_USER_GROUP where CMF_USER_ID=? and CMF_GROUP_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_CMF_GROUP_ID=$cmf->Spravotchnik($V_CMF_GROUP_ID,'select CMF_GROUP_ID,NAME from CMF_GROUP  order by NAME');        
					
@print <<<EOF
<h2 class="h2">Редактирование - Принадлежность пользователя к группе</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CMF_USER.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
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
<tr bgcolor="#FFFFFF"><th width="1%"><b>Группа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CMF_GROUP_ID">
				
				
				
				
				
				
				$V_STR_CMF_GROUP_ID
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
list($V_CMF_GROUP_ID)=array('');


$V_STR_CMF_GROUP_ID=$cmf->Spravotchnik($V_CMF_GROUP_ID,'select CMF_GROUP_ID,NAME from CMF_GROUP  order by NAME');
					
@print <<<EOF
<h2 class="h2">Добавление - Принадлежность пользователя к группе</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CMF_USER.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Группа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CMF_GROUP_ID">
				
				
				
				
				
				
				$V_STR_CMF_GROUP_ID
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
$cmf->execute('delete from CMF_USER where CMF_USER_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('CMF_USER');






$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into CMF_USER (CMF_USER_ID,NAME,LOGIN,PASS_,URL,STATUS) values (?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['LOGIN']),stripslashes($_REQUEST['PASS_']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['STATUS']));


$_REQUEST['e']='ED';
$cmf->execute('update CMF_USER set MD5_=md5(now()) where CMF_USER_ID=?',$_REQUEST['id']);
}

if($_REQUEST['e'] == 'Изменить')
{







$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('update CMF_USER set NAME=?,LOGIN=?,PASS_=?,URL=?,STATUS=? where CMF_USER_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['LOGIN']),stripslashes($_REQUEST['PASS_']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
$_REQUEST['e']='ED';
$cmf->execute('update CMF_USER set MD5_=md5(now()) where CMF_USER_ID=?',$_REQUEST['id']);
};

if($_REQUEST['e'] == 'ED')
{
list($V_CMF_USER_ID,$V_NAME,$V_LOGIN,$V_PASS_,$V_URL,$V_STATUS)=
$cmf->selectrow_arrayQ('select CMF_USER_ID,NAME,LOGIN,PASS_,URL,STATUS from CMF_USER where CMF_USER_ID=?',$_REQUEST['id']);



$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Пользователи системы</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CMF_USER.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(LOGIN) &amp;&amp; checkXML(PASS_) &amp;&amp; checkXML(URL);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Имя пользователя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Login:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="LOGIN" value="$V_LOGIN" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Пароль:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PASS_" value="$V_PASS_" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL точки входа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;




print <<<EOF
<a name="f1"></a><h3 class="h3">Принадлежность пользователя к группе</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="CMF_USER.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="3">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select CMF_GROUP_ID from CMF_USER_GROUP where CMF_USER_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>Группа</th><td></td></tr>
EOF;
while(list($V_CMF_GROUP_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_CMF_GROUP_ID_STR=$cmf->selectrow_arrayQ('select NAME from CMF_GROUP where CMF_GROUP_ID=?',$V_CMF_GROUP_ID);
                                        


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_CMF_GROUP_ID" /></td>
<td>$V_CMF_GROUP_ID_STR</td><td nowrap="">

<a href="CMF_USER.php?e1=ED&amp;iid=$V_CMF_GROUP_ID&amp;id={$_REQUEST['id']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_CMF_USER_ID,$V_MD5_,$V_NAME,$V_LOGIN,$V_PASS_,$V_URL,$V_STATUS)=array('','','','','','','');

$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Пользователи системы</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CMF_USER.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(LOGIN) &amp;&amp; checkXML(PASS_) &amp;&amp; checkXML(URL);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Имя пользователя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Login:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="LOGIN" value="$V_LOGIN" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Пароль:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PASS_" value="$V_PASS_" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL точки входа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

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


print '<h2 class="h2">Пользователи системы</h2><form action="CMF_USER.php" method="POST">';


if($_REQUEST['s'] == ''){$_REQUEST['s']=1;}
$_REQUEST['s']+=0;
$SORTNAMES=array('N','Имя пользователя','Login');
$SORTQUERY=array('order by A.CMF_USER_ID ','order by A.CMF_USER_ID desc ','order by A.NAME ','order by A.NAME desc ','order by A.LOGIN ','order by A.LOGIN desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_USER.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_USER.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_USER.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}


$pagesize=100;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from CMF_USER A where 1');

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="CMF_USER.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.CMF_USER_ID,A.NAME,A.LOGIN,A.STATUS from CMF_USER A where 1'.' '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





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
while(list($V_CMF_USER_ID,$V_NAME,$V_LOGIN,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="id[]" value="$V_CMF_USER_ID" /></td>
<td>$V_CMF_USER_ID</td><td>$V_NAME</td><td>$V_LOGIN</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="CMF_USER.php?e=ED&amp;id=$V_CMF_USER_ID&amp;p={$_REQUEST['p']}&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
