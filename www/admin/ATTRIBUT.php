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


$cmf->ENUM_TYPE=array('Целое число','Дробь','Строка','Список','Список. с карт.','чекбокс','чекбокс с тремя состояниями (да,нет,не знаю)','краткое описание(64 к)');





if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';

if($_REQUEST['e'] == 'RET')
{

$_REQUEST['pid']=$cmf->selectrow_array('select ATTRIBUT_GROUP_ID from ATTRIBUT where ATTRIBUT_ID=? ',$_REQUEST['id']);
}





if(!isset($_REQUEST['e1']))$_REQUEST['e1']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e1') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$ORDERING=$cmf->selectrow_array('select ORDERING from RANGE_LIST where ATTRIBUT_ID=? and RANGE_LIST_ID=?',$_REQUEST['id'],$id);
$cmf->execute('update RANGE_LIST set ORDERING=ORDERING-1 where ATTRIBUT_ID=? and ORDERING>?',$_REQUEST['id'],$ORDERING);
$cmf->execute('delete from RANGE_LIST where ATTRIBUT_ID=? and RANGE_LIST_ID=?',$_REQUEST['id'],$id);
$cmf->UpdateAllRanges();
 }
$_REQUEST['e']='ED';
$visible=0;
}


if($cmf->Param('e1') == 'UP')
{
$ORDERING=$cmf->selectrow_array('select ORDERING from RANGE_LIST where ATTRIBUT_ID=? and RANGE_LIST_ID=?',$_REQUEST['id'],$_REQUEST['iid']);
if($ORDERING>1)
{
$cmf->execute('update RANGE_LIST set ORDERING=ORDERING+1 where ATTRIBUT_ID=? and ORDERING=?',$_REQUEST['id'],$ORDERING-1);
$cmf->execute('update RANGE_LIST set ORDERING=ORDERING-1 where ATTRIBUT_ID=? and RANGE_LIST_ID=?',$_REQUEST['id'],$_REQUEST['iid']);
}
$_REQUEST['e']='ED';
}

if($cmf->Param('e1') == 'DN')
{
$ORDERING=$cmf->selectrow_array('select ORDERING from RANGE_LIST where ATTRIBUT_ID=? and RANGE_LIST_ID=?',$_REQUEST['id'],$_REQUEST['iid']);
$MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from RANGE_LIST');
if($ORDERING<$MAXORDERING)
{
$cmf->execute('update RANGE_LIST set ORDERING=ORDERING-1 where ATTRIBUT_ID=? and ORDERING=?',$_REQUEST['id'],$ORDERING+1);
$cmf->execute('update RANGE_LIST set ORDERING=ORDERING+1 where ATTRIBUT_ID=? and RANGE_LIST_ID=?',$_REQUEST['id'],$_REQUEST['iid']);
}
$_REQUEST['e']='ED';
}



if($cmf->Param('e1') == 'Изменить')
{








$cmf->execute('update RANGE_LIST set NAME=?,MIN=?,MAX=?,COMMENT_=? where ATTRIBUT_ID=? and RANGE_LIST_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['MIN'])+0,stripslashes($_REQUEST['MAX'])+0,stripslashes($_REQUEST['COMMENT_']),$_REQUEST['id'],$_REQUEST['iid']);
$cmf->UpdateAllRanges();
$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{

$_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from RANGE_LIST where ATTRIBUT_ID=?',$_REQUEST['id']);
$_REQUEST['ORDERING']++;


$_REQUEST['iid']=$cmf->GetSequence('RANGE_LIST');










$cmf->execute('insert into RANGE_LIST (ATTRIBUT_ID,RANGE_LIST_ID,NAME,MIN,MAX,COMMENT_,ORDERING) values (?,?,?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['MIN'])+0,stripslashes($_REQUEST['MAX'])+0,stripslashes($_REQUEST['COMMENT_']),stripslashes($_REQUEST['ORDERING']));
$_REQUEST['e']='ED';
$cmf->UpdateAllRanges();
$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_RANGE_LIST_ID,$V_NAME,$V_MIN,$V_MAX,$V_COMMENT_)=$cmf->selectrow_arrayQ('select RANGE_LIST_ID,NAME,MIN,MAX,COMMENT_ from RANGE_LIST where ATTRIBUT_ID=? and RANGE_LIST_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


@print <<<EOF
<h2 class="h2">Редактирование - Список возможных диапазонов</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="ATTRIBUT.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(COMMENT_);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'" />';

@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Название атрибута:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Min значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MIN" value="$V_MIN" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Max значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MAX" value="$V_MAX" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Комментарий:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="COMMENT_" rows="5" cols="90">$V_COMMENT_</textarea><br />


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
list($V_RANGE_LIST_ID,$V_NAME,$V_MIN,$V_MAX,$V_COMMENT_,$V_ORDERING)=array('','','','','','');


@print <<<EOF
<h2 class="h2">Добавление - Список возможных диапазонов</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="ATTRIBUT.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(COMMENT_);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Название атрибута:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Min значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MIN" value="$V_MIN" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Max значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MAX" value="$V_MAX" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Комментарий:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="COMMENT_" rows="5" cols="90">$V_COMMENT_</textarea><br />


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







if($_REQUEST['e'] == 'Применить' and is_array($_REQUEST['id']))
{
foreach ($_REQUEST['id'] as $id)
{
 $cmf->execute('update ATTRIBUT set ATTRIBUT_GROUP_ID=? where ATTRIBUT_ID=?',$_REQUEST['ATTRIBUT_GROUP_ID_'.$id],$id);

}

};


if(($_REQUEST['e'] == 'Удалить') and is_array($_REQUEST['id']) and ($cmf->D))
{

foreach ($_REQUEST['id'] as $id)
 {

$ORDERING=$cmf->selectrow_array('select ORDERING from ATTRIBUT where ATTRIBUT_ID=?',$id);
$cmf->execute('update ATTRIBUT set ORDERING=ORDERING-1 where ORDERING>? and ATTRIBUT_GROUP_ID=?',$ORDERING,$_REQUEST['pid']);
$cmf->execute('delete from ATTRIBUT where ATTRIBUT_ID=?',$id);

 }

}


if($_REQUEST['e'] == 'UP')
{
list($V_ATTRIBUT_GROUP_ID,$V_ORDERING) =$cmf->selectrow_array('select ATTRIBUT_GROUP_ID,ORDERING from ATTRIBUT where ATTRIBUT_ID=?',$_REQUEST['id']);
if($V_ORDERING > 1)
{

$sql="select ATTRIBUT_ID
           , ORDERING
      from ATTRIBUT
      where ORDERING < {$V_ORDERING}
            and ATTRIBUT_GROUP_ID = {$V_ATTRIBUT_GROUP_ID}
      order by ORDERING DESC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf->selectrow_array($sql);


$cmf->execute('update ATTRIBUT set ORDERING=? where ATTRIBUT_ID=?',$V_ORDERING,$V_OTHER_ID);
$cmf->execute('update ATTRIBUT set ORDERING=? where ATTRIBUT_ID=?',$V_OTHER_ORDERING, $_REQUEST['id']);

}
}

if($_REQUEST['e'] == 'DN')
{
list($V_ATTRIBUT_GROUP_ID,$V_ORDERING) =$cmf->selectrow_array('select ATTRIBUT_GROUP_ID,ORDERING from ATTRIBUT where ATTRIBUT_ID=?',$_REQUEST['id']);
$V_MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from ATTRIBUT where ATTRIBUT_GROUP_ID=?',$V_ATTRIBUT_GROUP_ID);
if($V_ORDERING < $V_MAXORDERING)
{

$sql="select ATTRIBUT_ID
           , ORDERING
      from ATTRIBUT
      where ORDERING > {$V_ORDERING}
            and ATTRIBUT_GROUP_ID = {$V_ATTRIBUT_GROUP_ID}
      order by ORDERING ASC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf->selectrow_array($sql);


$cmf->execute('update ATTRIBUT set ORDERING=? where ATTRIBUT_ID=?',$V_ORDERING,$V_OTHER_ID);
$cmf->execute('update ATTRIBUT set ORDERING=? where ATTRIBUT_ID=?',$V_OTHER_ORDERING, $_REQUEST['id']);
}
}


if($_REQUEST['e'] == 'Добавить')
{

$_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from ATTRIBUT where ATTRIBUT_GROUP_ID=?',$_REQUEST['pid']);
$_REQUEST['ORDERING']++;

$_REQUEST['id']=$cmf->GetSequence('ATTRIBUT');






		
				
    if(isset($_FILES['NOT_ICON_IMAGE']['tmp_name']) && $_FILES['NOT_ICON_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['ICON_IMAGE']=$cmf->PicturePost('NOT_ICON_IMAGE',$_REQUEST['ICON_IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_icon',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['ICON_IMAGE']=$cmf->PicturePost('NOT_ICON_IMAGE',$_REQUEST['ICON_IMAGE'],''.$_REQUEST['id'].'_icon',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['ICON_IMAGE']=$cmf->PicturePost('NOT_ICON_IMAGE',$_REQUEST['ICON_IMAGE'],''.$_REQUEST['id'].'_icon',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_ICON_IMAGE']) && $_REQUEST['CLR_ICON_IMAGE']){$_REQUEST['ICON_IMAGE']=$cmf->UnlinkFile($_REQUEST['ICON_IMAGE'],$VIRTUAL_IMAGE_PATH);}
	




$_REQUEST['IS_RANGEABLE']=isset($_REQUEST['IS_RANGEABLE']) && $_REQUEST['IS_RANGEABLE']?1:0;
$_REQUEST['IS_RANGE_VIEW']=isset($_REQUEST['IS_RANGE_VIEW']) && $_REQUEST['IS_RANGE_VIEW']?1:0;
$_REQUEST['NOT_CARD']=isset($_REQUEST['NOT_CARD']) && $_REQUEST['NOT_CARD']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;
$_REQUEST['EXPAND']=isset($_REQUEST['EXPAND']) && $_REQUEST['EXPAND']?1:0;
$_REQUEST['MULTIPLE_']=isset($_REQUEST['MULTIPLE_']) && $_REQUEST['MULTIPLE_']?1:0;



$cmf->execute('insert into ATTRIBUT (ATTRIBUT_ID,ATTRIBUT_GROUP_ID,NAME,ID_FROM_VBD,TITLE,ICON_IMAGE,TYPE,UNIT_ID,VIEW_ATTRIBUT_GROUP_ID,ALTER_VALUE,IS_RANGEABLE,IS_RANGE_VIEW,NOT_CARD,STATUS,EXPAND,MULTIPLE_,ORDERING) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ID_FROM_VBD'])+0,stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['ICON_IMAGE']),stripslashes($_REQUEST['TYPE']),stripslashes($_REQUEST['UNIT_ID'])+0,stripslashes($_REQUEST['VIEW_ATTRIBUT_GROUP_ID'])+0,stripslashes($_REQUEST['ALTER_VALUE']),stripslashes($_REQUEST['IS_RANGEABLE']),stripslashes($_REQUEST['IS_RANGE_VIEW']),stripslashes($_REQUEST['NOT_CARD']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['EXPAND']),stripslashes($_REQUEST['MULTIPLE_']),stripslashes($_REQUEST['ORDERING']));


$_REQUEST['e'] ='ED';

}

if($_REQUEST['e'] == 'Изменить')
{







		
				
    if(isset($_FILES['NOT_ICON_IMAGE']['tmp_name']) && $_FILES['NOT_ICON_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['ICON_IMAGE']=$cmf->PicturePost('NOT_ICON_IMAGE',$_REQUEST['ICON_IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_icon',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['ICON_IMAGE']=$cmf->PicturePost('NOT_ICON_IMAGE',$_REQUEST['ICON_IMAGE'],''.$_REQUEST['id'].'_icon',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['ICON_IMAGE']=$cmf->PicturePost('NOT_ICON_IMAGE',$_REQUEST['ICON_IMAGE'],''.$_REQUEST['id'].'_icon',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_ICON_IMAGE']) && $_REQUEST['CLR_ICON_IMAGE']){$_REQUEST['ICON_IMAGE']=$cmf->UnlinkFile($_REQUEST['ICON_IMAGE'],$VIRTUAL_IMAGE_PATH);}
	




$_REQUEST['IS_RANGEABLE']=isset($_REQUEST['IS_RANGEABLE']) && $_REQUEST['IS_RANGEABLE']?1:0;
$_REQUEST['IS_RANGE_VIEW']=isset($_REQUEST['IS_RANGE_VIEW']) && $_REQUEST['IS_RANGE_VIEW']?1:0;
$_REQUEST['NOT_CARD']=isset($_REQUEST['NOT_CARD']) && $_REQUEST['NOT_CARD']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;
$_REQUEST['EXPAND']=isset($_REQUEST['EXPAND']) && $_REQUEST['EXPAND']?1:0;
$_REQUEST['MULTIPLE_']=isset($_REQUEST['MULTIPLE_']) && $_REQUEST['MULTIPLE_']?1:0;


if(!empty($_REQUEST['pid'])) $cmf->execute('update ATTRIBUT set ATTRIBUT_GROUP_ID=?,NAME=?,ID_FROM_VBD=?,TITLE=?,ICON_IMAGE=?,TYPE=?,UNIT_ID=?,VIEW_ATTRIBUT_GROUP_ID=?,ALTER_VALUE=?,IS_RANGEABLE=?,IS_RANGE_VIEW=?,NOT_CARD=?,STATUS=?,EXPAND=?,MULTIPLE_=? where ATTRIBUT_ID=?',stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ID_FROM_VBD'])+0,stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['ICON_IMAGE']),stripslashes($_REQUEST['TYPE']),stripslashes($_REQUEST['UNIT_ID'])+0,stripslashes($_REQUEST['VIEW_ATTRIBUT_GROUP_ID'])+0,stripslashes($_REQUEST['ALTER_VALUE']),stripslashes($_REQUEST['IS_RANGEABLE']),stripslashes($_REQUEST['IS_RANGE_VIEW']),stripslashes($_REQUEST['NOT_CARD']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['EXPAND']),stripslashes($_REQUEST['MULTIPLE_']),$_REQUEST['id']);
else $cmf->execute('update ATTRIBUT set NAME=?,ID_FROM_VBD=?,TITLE=?,ICON_IMAGE=?,TYPE=?,UNIT_ID=?,VIEW_ATTRIBUT_GROUP_ID=?,ALTER_VALUE=?,IS_RANGEABLE=?,IS_RANGE_VIEW=?,NOT_CARD=?,STATUS=?,EXPAND=?,MULTIPLE_=? where ATTRIBUT_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ID_FROM_VBD'])+0,stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['ICON_IMAGE']),stripslashes($_REQUEST['TYPE']),stripslashes($_REQUEST['UNIT_ID'])+0,stripslashes($_REQUEST['VIEW_ATTRIBUT_GROUP_ID'])+0,stripslashes($_REQUEST['ALTER_VALUE']),stripslashes($_REQUEST['IS_RANGEABLE']),stripslashes($_REQUEST['IS_RANGE_VIEW']),stripslashes($_REQUEST['NOT_CARD']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['EXPAND']),stripslashes($_REQUEST['MULTIPLE_']),$_REQUEST['id']);

$_REQUEST['e'] ='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_ATTRIBUT_ID,$V_ATTRIBUT_GROUP_ID,$V_NAME,$V_ID_FROM_VBD,$V_TITLE,$V_ICON_IMAGE,$V_TYPE,$V_UNIT_ID,$V_VIEW_ATTRIBUT_GROUP_ID,$V_ALTER_VALUE,$V_IS_RANGEABLE,$V_IS_RANGE_VIEW,$V_NOT_CARD,$V_STATUS,$V_EXPAND,$V_MULTIPLE_)=$cmf->selectrow_arrayQ('select ATTRIBUT_ID,ATTRIBUT_GROUP_ID,NAME,ID_FROM_VBD,TITLE,ICON_IMAGE,TYPE,UNIT_ID,VIEW_ATTRIBUT_GROUP_ID,ALTER_VALUE,IS_RANGEABLE,IS_RANGE_VIEW,NOT_CARD,STATUS,EXPAND,MULTIPLE_ from ATTRIBUT where ATTRIBUT_ID=?',$_REQUEST['id']);



if(isset($V_ICON_IMAGE))
{
   $IM_ICON_IMAGE=split('#',$V_ICON_IMAGE);
   if(isset($IM_4[1]) && $IM_ICON_IMAGE[1] > 150){$IM_ICON_IMAGE[2]=$IM_ICON_IMAGE[2]*150/$IM_ICON_IMAGE[1]; $IM_ICON_IMAGE[1]=150;}
}

$V_STR_TYPE=$cmf->Enumerator($cmf->ENUM_TYPE,$V_TYPE);
$V_STR_UNIT_ID=$cmf->Spravotchnik($V_UNIT_ID,'select UNIT_ID,NAME from UNIT  order by NAME');        
					
$V_STR_VIEW_ATTRIBUT_GROUP_ID=$cmf->Spravotchnik($V_VIEW_ATTRIBUT_GROUP_ID,'select VIEW_ATTRIBUT_GROUP_ID,NAME from VIEW_ATTRIBUT_GROUP  order by NAME');        
					
$V_IS_RANGEABLE=$V_IS_RANGEABLE?'checked':'';
$V_IS_RANGE_VIEW=$V_IS_RANGE_VIEW?'checked':'';
$V_NOT_CARD=$V_NOT_CARD?'checked':'';
$V_STATUS=$V_STATUS?'checked':'';
$V_EXPAND=$V_EXPAND?'checked':'';
$V_MULTIPLE_=$V_MULTIPLE_?'checked':'';
print @<<<EOF
<h2 class="h2">Редактирование - Аттрибуты товара</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="ATTRIBUT.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(ALTER_VALUE);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" /><input type="hidden" name="FLT_ATTRIBUT_GROUP_ID" value="{$_REQUEST['FLT_ATTRIBUT_GROUP_ID']}" />
<input type="hidden" name="type" value="4" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Назад" class="gbt bcancel" />
</td></tr>
EOF;



@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название атрибута:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>ID внешей БД:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ID_FROM_VBD" value="$V_ID_FROM_VBD" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>TITLE:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="TITLE" rows="2" cols="90">$V_TITLE</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Иконка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="ICON_IMAGE" value="$V_ICON_IMAGE" />
<table><tr><td>
EOF;
if(!empty($IM_ICON_IMAGE[0]))
{
if(strchr($IM_ICON_IMAGE[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_ICON_IMAGE[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_ICON_IMAGE[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_ICON_IMAGE[0] = !empty($IM_ICON_IMAGE[0]) ? $IM_ICON_IMAGE[0]:0;
$IM_ICON_IMAGE[1] = !empty($IM_ICON_IMAGE[1]) ? $IM_ICON_IMAGE[1]:0;
$IM_ICON_IMAGE[2] = !empty($IM_ICON_IMAGE[2]) ? $IM_ICON_IMAGE[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_ICON_IMAGE[0]" width="$IM_ICON_IMAGE[1]" height="$IM_ICON_IMAGE[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_ICON_IMAGE" size="1" /><br />
<input type="checkbox" name="CLR_ICON_IMAGE" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тип поля:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="TYPE">$V_STR_TYPE</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Единица измерения:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="UNIT_ID">
				
				
				
				
				<option value="0">-- не задана --</option>
				
				$V_STR_UNIT_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Название группировки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="VIEW_ATTRIBUT_GROUP_ID">
				
				
				
				
				<option value="0">-- не задана --</option>
				
				$V_STR_VIEW_ATTRIBUT_GROUP_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Альтернативное значение для вывода:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ALTER_VALUE" value="$V_ALTER_VALUE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Явл-ся диапазоном?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_RANGEABLE' value='1' $V_IS_RANGEABLE/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Выводить как диапозон:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_RANGE_VIEW' value='1' $V_IS_RANGE_VIEW/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Не выводить в карточке товара:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='NOT_CARD' value='1' $V_NOT_CARD/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Развернуть?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='EXPAND' value='1' $V_EXPAND/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Множественный выбор(для списков) :<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='MULTIPLE_' value='1' $V_MULTIPLE_/><br /></td></tr>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Назад" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;



print <<<EOF
<a name="f1"></a><h3 class="h3">Список возможных диапазонов</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="ATTRIBUT.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="6">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select RANGE_LIST_ID,NAME,MIN,MAX from RANGE_LIST where ATTRIBUT_ID=?  order by MIN,MAX',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>Название атрибута</th><th>Min значение</th><th>Max значение</th><td></td></tr>
EOF;
while(list($V_RANGE_LIST_ID,$V_NAME,$V_MIN,$V_MAX)=mysql_fetch_array($sth, MYSQL_NUM))
{


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_RANGE_LIST_ID" /></td>
<td>$V_RANGE_LIST_ID</td><td>$V_NAME</td><td>$V_MIN</td><td>$V_MAX</td><td nowrap="">
<a href="ATTRIBUT.php?e1=UP&amp;iid=$V_RANGE_LIST_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}#f1"><img src="i/up.gif" border="0" /></a>
<a href="ATTRIBUT.php?e1=DN&amp;iid=$V_RANGE_LIST_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}#f1"><img src="i/dn.gif" border="0" /></a>
<a href="ATTRIBUT.php?e1=ED&amp;iid=$V_RANGE_LIST_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}

if($_REQUEST['e'] =='Новый')
{
list($V_ATTRIBUT_ID,$V_ATTRIBUT_GROUP_ID,$V_NAME,$V_ID_FROM_VBD,$V_TITLE,$V_ICON_IMAGE,$V_TYPE,$V_UNIT_ID,$V_VIEW_ATTRIBUT_GROUP_ID,$V_ALTER_VALUE,$V_IS_RANGEABLE,$V_IS_RANGE_VIEW,$V_NOT_CARD,$V_STATUS,$V_EXPAND,$V_MULTIPLE_,$V_ORDERING)=array('','','','','','','','','','','','','','','','','');


$IM_ICON_IMAGE=array('','','');
$V_STR_TYPE=$cmf->Enumerator($cmf->ENUM_TYPE,-1);
$V_STR_UNIT_ID=$cmf->Spravotchnik($V_UNIT_ID,'select UNIT_ID,NAME from UNIT  order by NAME');
					
$V_STR_VIEW_ATTRIBUT_GROUP_ID=$cmf->Spravotchnik($V_VIEW_ATTRIBUT_GROUP_ID,'select VIEW_ATTRIBUT_GROUP_ID,NAME from VIEW_ATTRIBUT_GROUP  order by NAME');
					
$V_IS_RANGEABLE='';
$V_IS_RANGE_VIEW='';
$V_NOT_CARD='';
$V_STATUS='checked';
$V_EXPAND='checked';
$V_MULTIPLE_='';
@print <<<EOF
<h2 class="h2">Добавление - Аттрибуты товара</h2>
<a href="javascript:history.go(-1)">&#160;<b>вернуться</b></a><p />
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="ATTRIBUT.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(ALTER_VALUE);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
EOF;



@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название атрибута:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>ID внешей БД:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ID_FROM_VBD" value="$V_ID_FROM_VBD" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>TITLE:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="TITLE" rows="2" cols="90">$V_TITLE</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Иконка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="ICON_IMAGE" value="$V_ICON_IMAGE" />
<table><tr><td>
EOF;
if(!empty($IM_ICON_IMAGE[0]))
{
if(strchr($IM_ICON_IMAGE[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_ICON_IMAGE[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_ICON_IMAGE[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_ICON_IMAGE[0] = !empty($IM_ICON_IMAGE[0]) ? $IM_ICON_IMAGE[0]:0;
$IM_ICON_IMAGE[1] = !empty($IM_ICON_IMAGE[1]) ? $IM_ICON_IMAGE[1]:0;
$IM_ICON_IMAGE[2] = !empty($IM_ICON_IMAGE[2]) ? $IM_ICON_IMAGE[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_ICON_IMAGE[0]" width="$IM_ICON_IMAGE[1]" height="$IM_ICON_IMAGE[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_ICON_IMAGE" size="1" /><br />
<input type="checkbox" name="CLR_ICON_IMAGE" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тип поля:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="TYPE">$V_STR_TYPE</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Единица измерения:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="UNIT_ID">
				
				
				
				
				<option value="0">-- не задана --</option>
				
				$V_STR_UNIT_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Название группировки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="VIEW_ATTRIBUT_GROUP_ID">
				
				
				
				
				<option value="0">-- не задана --</option>
				
				$V_STR_VIEW_ATTRIBUT_GROUP_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Альтернативное значение для вывода:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ALTER_VALUE" value="$V_ALTER_VALUE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Явл-ся диапазоном?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_RANGEABLE' value='1' $V_IS_RANGEABLE/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Выводить как диапозон:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_RANGE_VIEW' value='1' $V_IS_RANGE_VIEW/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Не выводить в карточке товара:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='NOT_CARD' value='1' $V_NOT_CARD/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Развернуть?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='EXPAND' value='1' $V_EXPAND/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Множественный выбор(для списков) :<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='MULTIPLE_' value='1' $V_MULTIPLE_/><br /></td></tr>

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

if(empty($_REQUEST['pid'])) $_REQUEST['pid'] = 0;

if(!empty($_REQUEST['pid']) and $_REQUEST['pid']!='all') $V_PARENTSCRIPTNAME=$cmf->selectrow_array('select NAME from ATTRIBUT_GROUP where ATTRIBUT_GROUP_ID=?',$_REQUEST['pid']);
else $V_PARENTSCRIPTNAME='';

print <<<EOF
<h2 class="h2">$V_PARENTSCRIPTNAME / Аттрибуты товара</h2><form action="ATTRIBUT.php" method="POST">
<a href="ATTRIBUT_GROUP.php?e=RET&amp;id={$_REQUEST['pid']}&amp;p={$_REQUEST['p']}">
<img src="i/back.gif" border="0" align="top" /> Назад</a><br />
EOF;




$pagesize=20;

if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}

if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{
if(!empty($_REQUEST['pid']) and $_REQUEST['pid']=='all')
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ATTRIBUT A where A.ATTRIBUT_GROUP_ID > 0'
.($cmf->Param('FLT_ATTRIBUT_GROUP_ID')?' and A.ATTRIBUT_GROUP_ID='.mysql_escape_string($cmf->Param('FLT_ATTRIBUT_GROUP_ID')):''),$_REQUEST['pid']);
}
else
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ATTRIBUT A where A.ATTRIBUT_GROUP_ID=?'
.($cmf->Param('FLT_ATTRIBUT_GROUP_ID')?' and A.ATTRIBUT_GROUP_ID='.mysql_escape_string($cmf->Param('FLT_ATTRIBUT_GROUP_ID')):''),$_REQUEST['pid']);

}
$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 $start=1;
if($_REQUEST['p']>15){$start=$_REQUEST['p']-15;}
 
 for($i=$start;$i<=$_REQUEST['pcount'] && ($i-$start)<31;$i++)
 {
  if($i==$_REQUEST['p']) { print <<<EOF
- <b class="red">$i</b>
EOF;
 } else { print <<<EOF
- <a class="t" href="ATTRIBUT.php?pid={$_REQUEST['pid']}&amp;count={$_REQUEST['count']}&amp;p={$i}&amp;pcount={$_REQUEST['pcount']}{$filtpath}{$filters}">$i</a>
EOF;
  }
 }
print <<<EOF
&#160;из <span class="red">({$_REQUEST['pcount']})</span><br />
EOF;
}

if(!empty($_REQUEST['pid']) and $_REQUEST['pid'] == 'all')
{
$sth=$cmf->execute('select A.ATTRIBUT_ID,A.ATTRIBUT_GROUP_ID,A.NAME,A.TYPE,A.UNIT_ID,A.VIEW_ATTRIBUT_GROUP_ID,A.ALTER_VALUE,A.IS_RANGEABLE,A.IS_RANGE_VIEW,A.NOT_CARD,A.STATUS,A.EXPAND,A.MULTIPLE_ from ATTRIBUT A
where A.ATTRIBUT_GROUP_ID > 0 '
.($cmf->Param('FLT_ATTRIBUT_GROUP_ID')?' and A.ATTRIBUT_GROUP_ID='.mysql_escape_string($cmf->Param('FLT_ATTRIBUT_GROUP_ID')):'').' order by A.ORDERING limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);
}
else
{
$sth=$cmf->execute('select A.ATTRIBUT_ID,A.ATTRIBUT_GROUP_ID,A.NAME,A.TYPE,A.UNIT_ID,A.VIEW_ATTRIBUT_GROUP_ID,A.ALTER_VALUE,A.IS_RANGEABLE,A.IS_RANGE_VIEW,A.NOT_CARD,A.STATUS,A.EXPAND,A.MULTIPLE_ from ATTRIBUT A
where A.ATTRIBUT_GROUP_ID=? '
.($cmf->Param('FLT_ATTRIBUT_GROUP_ID')?' and A.ATTRIBUT_GROUP_ID='.mysql_escape_string($cmf->Param('FLT_ATTRIBUT_GROUP_ID')):'').' order by A.ORDERING limit ?,?',$_REQUEST['pid'],$pagesize*($_REQUEST['p']-1),$pagesize);

}




$V_STR_ATTRIBUT_GROUP_ID=$cmf->Spravotchnik($cmf->Param('FLT_ATTRIBUT_GROUP_ID'),'select ATTRIBUT_GROUP_ID,NAME from ATTRIBUT_GROUP  order by NAME');
@print <<<EOF
<table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
<input type="hidden" name="type" value="4" />
<tr bgcolor="#F0F0F0"><td colspan="2"><input type="submit" name="e" value="Фильтр" class="gbt bflt" /></td></tr>
<tr bgcolor="#FFFFFF"><th>Группа<br /><img src="i/0.gif" width="125" height="1" /></th><td><select name="FLT_ATTRIBUT_GROUP_ID"><option value="0">--------</option>{$V_STR_ATTRIBUT_GROUP_ID}</select><br /></td></tr>
</table>
EOF;



@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="10">
EOF;

if ($cmf->W)
@print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" /><input type="submit" name="e" value="Применить" class="gbt bsave" /><img src="i/0.gif" width="4" height="1" />
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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Группа</th><th>Название атрибута</th><th>Тип поля</th><th>Единица измерения</th><th>Название группировки</th><th>Альтернативное значение для вывода</th><th>Множественный выбор(для списков) </th><td></td></tr>
EOF;
$TABposition=1;

if($sth)
while(list($V_ATTRIBUT_ID,$V_ATTRIBUT_GROUP_ID,$V_NAME,$V_TYPE,$V_UNIT_ID,$V_VIEW_ATTRIBUT_GROUP_ID,$V_ALTER_VALUE,$V_IS_RANGEABLE,$V_IS_RANGE_VIEW,$V_NOT_CARD,$V_STATUS,$V_EXPAND,$V_MULTIPLE_)=mysql_fetch_array($sth, MYSQL_NUM))
{$TABposition++;



$V_STR_ATTRIBUT_GROUP_ID=$cmf->Spravotchnik($V_ATTRIBUT_GROUP_ID,'select ATTRIBUT_GROUP_ID, NAME from ATTRIBUT_GROUP');
                                        
$V_TYPE=$cmf->ENUM_TYPE[$V_TYPE];
                        
$V_UNIT_ID=$cmf->selectrow_arrayQ('select NAME from UNIT where UNIT_ID=?',$V_UNIT_ID);
                                        
$V_VIEW_ATTRIBUT_GROUP_ID=$cmf->selectrow_arrayQ('select NAME from VIEW_ATTRIBUT_GROUP where VIEW_ATTRIBUT_GROUP_ID=?',$V_VIEW_ATTRIBUT_GROUP_ID);
                                        
if(!$V_MULTIPLE_) {$V_MULTIPLE_='Нет';} else {$V_MULTIPLE_='Да';}
                        

if($V_STATUS == 1){$V_COLOR='#FFFFFF';} else {$V_COLOR='#a0a0a0';}



@print <<<EOF
<tr bgcolor="$V_COLOR">
<td><input type="checkbox" name="id[]" value="$V_ATTRIBUT_ID" /></td>
<td>$V_ATTRIBUT_ID</td><td><select onchange="ch(this)" name="ATTRIBUT_GROUP_ID_$V_ATTRIBUT_ID" class="i">
                                                $V_STR_ATTRIBUT_GROUP_ID</select></td><td><a class="b" href="ATTRIBUT_LIST.php?pid=$V_ATTRIBUT_ID&amp;p={$_REQUEST['p']}">$V_NAME</a></td><td>$V_TYPE</td><td>$V_UNIT_ID</td><td>$V_VIEW_ATTRIBUT_GROUP_ID</td><td>$V_ALTER_VALUE</td><td>$V_MULTIPLE_</td><td nowrap="">
<a href="ATTRIBUT.php?e=UP&amp;id=$V_ATTRIBUT_ID&amp;pid={$_REQUEST['pid']}{$filters}"><img src="i/up.gif" border="0" /></a>
<a href="ATTRIBUT.php?e=DN&amp;id=$V_ATTRIBUT_ID&amp;pid={$_REQUEST['pid']}{$filters}"><img src="i/dn.gif" border="0" /></a>
EOF;

if ($cmf->W)
@print <<<EOF
<a href="ATTRIBUT.php?e=ED&amp;id=$V_ATTRIBUT_ID&amp;pid={$_REQUEST['pid']}&amp;p={$_REQUEST['p']}{$filtpath}{$filters}"><img src="i/ed.gif" border="0" title="Изменить" /></a>

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
$sth=$cmf->execute('select ATTRIBUT_GROUP_ID,NAME from ATTRIBUT_GROUP  order by ORDERING');
while(list($V_ATTRIBUT_GROUP_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.='<li>'.($id==$V_ATTRIBUT_GROUP_ID?'<input type="radio" name="cid" value="'.$V_ATTRIBUT_GROUP_ID.'" disabled="yes" />':'<input type="radio" name="cid" value="'.$V_ATTRIBUT_GROUP_ID.'" />')."&#160;$V_NAME</li>";
}
if ($ret) {$ret="<ul>${ret}</ul>";}
return $ret;
}

?>
