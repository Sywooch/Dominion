<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('CMF_BUG');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;



$cmf->ENUM_STATUS=array(' Новый ',' В процессе',' Обработан',' Отказ');





if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';












if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
$cmf->execute('delete from CMF_BUG where CMF_BUG_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('CMF_BUG');








$cmf->execute('insert into CMF_BUG (CMF_BUG_ID,CMF_USER_ID,DATA,URL,DESCRIPTION,STATUS) values (?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['CMF_USER_ID'])+0,stripslashes($_REQUEST['DATA']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['STATUS']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{








$cmf->execute('update CMF_BUG set CMF_USER_ID=?,DATA=?,URL=?,DESCRIPTION=?,STATUS=? where CMF_BUG_ID=?',stripslashes($_REQUEST['CMF_USER_ID'])+0,stripslashes($_REQUEST['DATA']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_CMF_BUG_ID,$V_CMF_USER_ID,$V_DATA,$V_URL,$V_DESCRIPTION,$V_STATUS)=
$cmf->selectrow_arrayQ('select CMF_BUG_ID,CMF_USER_ID,DATE_FORMAT(DATA,"%Y-%m-%d %H:%i"),URL,DESCRIPTION,STATUS from CMF_BUG where CMF_BUG_ID=?',$_REQUEST['id']);



$V_STR_CMF_USER_ID=$cmf->Spravotchnik($V_CMF_USER_ID,'select CMF_USER_ID,concat(LOGIN,":",CMF_USER_ID) from CMF_USER  order by concat(LOGIN,":",CMF_USER_ID)');        
					
$V_STR_STATUS=$cmf->Enumerator($cmf->ENUM_STATUS,$V_STATUS);
@print <<<EOF
<h2 class="h2">Редактирование - Баги системы</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CMF_BUG.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(URL) &amp;&amp; checkXML(DESCRIPTION);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Пользователь:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CMF_USER_ID">
				
				
				
				
				
				
				$V_STR_CMF_USER_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATA" name="DATA" value="$V_DATA" />
EOF;

if($V_DATA) $V_DAT_ = substr($V_DATA,8,2).".".substr($V_DATA,5,2).".".substr($V_DATA,0,4)." ".substr($V_DATA,11,2).":".substr($V_DATA,14,2);
else $V_DAT_ = '';

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL ошибки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="5" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Статус:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="STATUS">$V_STR_STATUS</select><br /></td></tr>


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
list($V_CMF_BUG_ID,$V_CMF_USER_ID,$V_DATA,$V_URL,$V_DESCRIPTION,$V_STATUS)=array('','','','','','');

$V_STR_CMF_USER_ID=$cmf->Spravotchnik($V_CMF_USER_ID,'select CMF_USER_ID,concat(LOGIN,":",CMF_USER_ID) from CMF_USER  order by concat(LOGIN,":",CMF_USER_ID)');
					
$V_DATA=$cmf->selectrow_array('select now()');
$V_STR_STATUS=$cmf->Enumerator($cmf->ENUM_STATUS,-1);
@print <<<EOF
<h2 class="h2">Добавление - Баги системы</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CMF_BUG.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(URL) &amp;&amp; checkXML(DESCRIPTION);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Пользователь:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CMF_USER_ID">
				
				
				
				
				
				
				$V_STR_CMF_USER_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATA" name="DATA" value="$V_DATA" />
EOF;

if($V_DATA) $V_DAT_ = substr($V_DATA,8,2).".".substr($V_DATA,5,2).".".substr($V_DATA,0,4)." ".substr($V_DATA,11,2).":".substr($V_DATA,14,2);
else $V_DAT_ = '';

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL ошибки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="5" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Статус:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="STATUS">$V_STR_STATUS</select><br /></td></tr>

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


print '<h2 class="h2">Баги системы</h2><form action="CMF_BUG.php" method="POST">';


if($_REQUEST['s'] == ''){$_REQUEST['s']=1;}
$_REQUEST['s']+=0;
$SORTNAMES=array('N','Пользователь','Дата','URL ошибки');
$SORTQUERY=array('order by A.CMF_BUG_ID ','order by A.CMF_BUG_ID desc ','order by A.CMF_USER_ID ','order by A.CMF_USER_ID desc ','order by A.DATA ','order by A.DATA desc ','order by A.URL ','order by A.URL desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_BUG.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_BUG.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_BUG.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}


$pagesize=100;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from CMF_BUG A where 1'.' and'.(isset($_REQUEST['f'])?"STATUS={$_REQUEST['f']}":'1=1'));

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="CMF_BUG.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.CMF_BUG_ID,A.CMF_USER_ID,DATE_FORMAT(A.DATA,"%Y-%m-%d %H:%i"),A.URL from CMF_BUG A where 1 and '.(isset($_REQUEST['f'])?"STATUS={$_REQUEST['f']}":'1=1').''.' '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="3">
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

</td><th colspan="3" class="main_tbl_title">
EOF;
foreach ($cmf->ENUM_STATUS as $key=>$val)
{
$COUNT=$cmf->selectrow_array('select count(*) from CMF_BUG where STATUS=?',$key);
if($cmf->Param('f') == $key){ ?><a href="CMF_BUG.php?f=<?=$key?>" class="red"><?=$val?> (<?=$COUNT?>)</a>&#160;&#160;&#160;<?}
else {?><a href="CMF_BUG.php?f=<?=$key?>"><?=$val?> (<?=$COUNT?>)</a>&#160;&#160;&#160;<? }
}
print <<<EOF
</th></tr>
EOF;

print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td>$HEADER<td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_CMF_BUG_ID,$V_CMF_USER_ID,$V_DATA,$V_URL)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_CMF_USER_ID=$cmf->selectrow_arrayQ('select concat(LOGIN,":",CMF_USER_ID) from CMF_USER where CMF_USER_ID=?',$V_CMF_USER_ID);
                                        


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_CMF_BUG_ID" /></td>
<td>$V_CMF_BUG_ID</td><td>$V_CMF_USER_ID</td><td>$V_DATA</td><td>$V_URL</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="CMF_BUG.php?e=ED&amp;id=$V_CMF_BUG_ID&amp;p={$_REQUEST['p']}&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
