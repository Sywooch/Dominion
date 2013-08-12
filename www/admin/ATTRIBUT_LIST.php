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
$VIRTUAL_IMAGE_PATH="/attr/";






if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';

if($_REQUEST['e'] == 'RET')
{

$_REQUEST['pid']=$cmf->selectrow_array('select ATTRIBUT_ID from ATTRIBUT_LIST where ATTRIBUT_LIST_ID=? ',$_REQUEST['id']);
}












if(($_REQUEST['e'] == 'Удалить') and is_array($_REQUEST['id']) and ($cmf->D))
{

foreach ($_REQUEST['id'] as $id)
 {

$cmf->execute('delete from ATTRIBUT_LIST where ATTRIBUT_LIST_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{


$_REQUEST['id']=$cmf->GetSequence('ATTRIBUT_LIST');






$cmf->execute('insert into ATTRIBUT_LIST (ATTRIBUT_LIST_ID,ATTRIBUT_ID,NAME,COMMENT_) values (?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['COMMENT_']));


$_REQUEST['e'] ='ED';

}

if($_REQUEST['e'] == 'Изменить')
{






if(!empty($_REQUEST['pid'])) $cmf->execute('update ATTRIBUT_LIST set ATTRIBUT_ID=?,NAME=?,COMMENT_=? where ATTRIBUT_LIST_ID=?',stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['COMMENT_']),$_REQUEST['id']);
else $cmf->execute('update ATTRIBUT_LIST set NAME=?,COMMENT_=? where ATTRIBUT_LIST_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['COMMENT_']),$_REQUEST['id']);

$_REQUEST['e'] ='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_ATTRIBUT_LIST_ID,$V_ATTRIBUT_ID,$V_NAME,$V_COMMENT_)=$cmf->selectrow_arrayQ('select ATTRIBUT_LIST_ID,ATTRIBUT_ID,NAME,COMMENT_ from ATTRIBUT_LIST where ATTRIBUT_LIST_ID=?',$_REQUEST['id']);



print @<<<EOF
<h2 class="h2">Редактирование - Список возможных значений</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="ATTRIBUT_LIST.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(COMMENT_);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
EOF;



@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>Значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Комментарий:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="COMMENT_" rows="5" cols="90">$V_COMMENT_</textarea><br />


</td></tr>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Назад" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;


$visible=0;
}

if($_REQUEST['e'] =='Новый')
{
list($V_ATTRIBUT_LIST_ID,$V_ATTRIBUT_ID,$V_NAME,$V_COMMENT_)=array('','','','');


@print <<<EOF
<h2 class="h2">Добавление - Список возможных значений</h2>
<a href="javascript:history.go(-1)">&#160;<b>вернуться</b></a><p />
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="ATTRIBUT_LIST.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(COMMENT_);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
EOF;



@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>Значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Комментарий:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="COMMENT_" rows="5" cols="90">$V_COMMENT_</textarea><br />


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
if(empty($_REQUEST['pid'])) $_REQUEST['pid'] = 0;

if(!empty($_REQUEST['pid']) and $_REQUEST['pid']!='all') $V_PARENTSCRIPTNAME=$cmf->selectrow_array('select NAME from ATTRIBUT where ATTRIBUT_ID=?',$_REQUEST['pid']);
else $V_PARENTSCRIPTNAME='';

print <<<EOF
<h2 class="h2">$V_PARENTSCRIPTNAME / Список возможных значений</h2><form action="ATTRIBUT_LIST.php" method="POST">
<a href="ATTRIBUT.php?e=RET&amp;id={$_REQUEST['pid']}">
<img src="i/back.gif" border="0" align="top" /> Назад</a><br />
EOF;




$_REQUEST['s']+=0;
$SORTNAMES=array('N','Значение');
$SORTQUERY=array('order by A.ATTRIBUT_LIST_ID ','order by A.ATTRIBUT_LIST_ID desc ','order by A.NAME ','order by A.NAME desc ');

//Ручные фильтры
$filters = '';
$filt_request = '';
foreach($_REQUEST as $key=>$val)
{
  if(preg_match('/^FILTER_(.+)$/',$key,$p))
  {
    if($val!='')
     {
        $filters.='&amp;'.$key.'='.$val;
     }
  }
}

list($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ATTRIBUT_LIST.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filters}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ATTRIBUT_LIST.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filters}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ATTRIBUT_LIST.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filters}">$tmp</a></th>
EOF;
        }
        $i++;
}


if(!empty($_REQUEST['pid']) and $_REQUEST['pid']!='all')
{
$sth=$cmf->execute('select A.ATTRIBUT_LIST_ID,A.NAME from ATTRIBUT_LIST A where A.ATTRIBUT_ID=?  '.$SORTQUERY[$_REQUEST['s']],$_REQUEST['pid']);
}
else
{
$sth=$cmf->execute('select A.ATTRIBUT_LIST_ID,A.NAME from ATTRIBUT_LIST A
where A.ATTRIBUT_ID > 0  '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);

}





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="4">
EOF;

if ($cmf->W)
@print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
EOF;

if ($cmf->D)
  print '<input type="submit" name="e" onclick="return dl();" value="Удалить" class="gbt bdel" />';
  
@print <<<EOF
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
</td></tr>
EOF;

print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td>$HEADER<td></td></tr>
EOF;


if($sth)
while(list($V_ATTRIBUT_LIST_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{






@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_ATTRIBUT_LIST_ID" /></td>
<td>$V_ATTRIBUT_LIST_ID</td><td>$V_NAME</td><td nowrap="">
EOF;

if ($cmf->W)
@print <<<EOF
<a href="ATTRIBUT_LIST.php?e=ED&amp;id=$V_ATTRIBUT_LIST_ID&amp;pid={$_REQUEST['pid']}&amp;s={$_REQUEST['s']}{$filters}"><img src="i/ed.gif" border="0" title="Изменить" /></a>

</td></tr>
EOF;
}
@print <<<EOF
        </table>
EOF;
}
print '</form>';
$cmf->MakeCommonFooter();
$cmf->Close();


function ___GetList($cmf,$id)
{
$ret='';
$sth=$cmf->execute('select ATTRIBUT_ID,NAME from ATTRIBUT  order by ORDERING');
while(list($V_ATTRIBUT_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.='<li>'.($id==$V_ATTRIBUT_ID?'<input type="radio" name="cid" value="'.$V_ATTRIBUT_ID.'" disabled="yes" />':'<input type="radio" name="cid" value="'.$V_ATTRIBUT_ID.'" />')."&#160;$V_NAME</li>";
}
if ($ret) {$ret="<ul>${ret}</ul>";}
return $ret;
}

?>
