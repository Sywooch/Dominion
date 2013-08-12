<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('CMF_XMLS_ARTICLE');
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
$cmf->execute('delete from CMF_XMLS_ARTICLE where TYPE=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('CMF_XMLS_ARTICLE');






$cmf->execute('insert into CMF_XMLS_ARTICLE (TYPE,ARTICLE,EDIT,VIEW) values (?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['ARTICLE'])+0,stripslashes($_REQUEST['EDIT']),stripslashes($_REQUEST['VIEW']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{






$cmf->execute('update CMF_XMLS_ARTICLE set ARTICLE=?,EDIT=?,VIEW=? where TYPE=?',stripslashes($_REQUEST['ARTICLE'])+0,stripslashes($_REQUEST['EDIT']),stripslashes($_REQUEST['VIEW']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_TYPE,$V_ARTICLE,$V_EDIT,$V_VIEW)=
$cmf->selectrow_arrayQ('select TYPE,ARTICLE,EDIT,VIEW from CMF_XMLS_ARTICLE where TYPE=?',$_REQUEST['id']);



$V_STR_ARTICLE=$cmf->Spravotchnik($V_ARTICLE,'select ARTICLE,NAME from CMF_SCRIPT  order by NAME');        
					
@print <<<EOF
<h2 class="h2">Редактирование - Привязка типа к скрипту</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CMF_XMLS_ARTICLE.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(EDIT) &amp;&amp; checkXML(VIEW);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>ARTICLE:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="ARTICLE">
				
				
				
				
				
				
				$V_STR_ARTICLE
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Шаблон пути для редактирования:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EDIT" value="$V_EDIT" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Шаблон пути для просмотра:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="VIEW" value="$V_VIEW" size="90" /><br />

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
list($V_TYPE,$V_ARTICLE,$V_EDIT,$V_VIEW)=array('','','','');

$V_STR_ARTICLE=$cmf->Spravotchnik($V_ARTICLE,'select ARTICLE,NAME from CMF_SCRIPT  order by NAME');
					
@print <<<EOF
<h2 class="h2">Добавление - Привязка типа к скрипту</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CMF_XMLS_ARTICLE.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(EDIT) &amp;&amp; checkXML(VIEW);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>ARTICLE:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="ARTICLE">
				
				
				
				
				
				
				$V_STR_ARTICLE
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Шаблон пути для редактирования:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EDIT" value="$V_EDIT" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Шаблон пути для просмотра:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="VIEW" value="$V_VIEW" size="90" /><br />

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


print '<h2 class="h2">Привязка типа к скрипту</h2><form action="CMF_XMLS_ARTICLE.php" method="POST">';



$_REQUEST['s']+=0;
$SORTNAMES=array('Тип','ARTICLE','Шаблон пути для редактирования','Шаблон пути для просмотра');
$SORTQUERY=array('order by A.TYPE ','order by A.TYPE desc ','order by A.ARTICLE ','order by A.ARTICLE desc ','order by A.EDIT ','order by A.EDIT desc ','order by A.VIEW ','order by A.VIEW desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_XMLS_ARTICLE.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_XMLS_ARTICLE.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CMF_XMLS_ARTICLE.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}



$sth=$cmf->execute('select A.TYPE,A.ARTICLE,A.EDIT,A.VIEW from CMF_XMLS_ARTICLE A where 1'.' '.$SORTQUERY[$_REQUEST['s']]);





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="6">
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
while(list($V_TYPE,$V_ARTICLE,$V_EDIT,$V_VIEW)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_ARTICLE=$cmf->selectrow_arrayQ('select NAME from CMF_SCRIPT where ARTICLE=?',$V_ARTICLE);
                                        


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_TYPE" /></td>
<td>$V_TYPE</td><td>$V_ARTICLE</td><td>$V_EDIT</td><td>$V_VIEW</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="CMF_XMLS_ARTICLE.php?e=ED&amp;id=$V_TYPE&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
