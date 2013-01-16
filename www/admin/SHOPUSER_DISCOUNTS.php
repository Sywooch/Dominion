<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('SHOPUSER_DISCOUNTS');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;
$VIRTUAL_IMAGE_PATH="/usr_disc/";






if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';




if(!isset($_REQUEST['e1']))$_REQUEST['e1']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e1') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from SHOPUSER_DISCOUNTS_RANGE_LIST where SHOPUSER_DISCOUNTS_ID=? and SHOPUSER_DISCOUNTS_RANGE_LIST_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e1') == 'Изменить')
{






$cmf->execute('update SHOPUSER_DISCOUNTS_RANGE_LIST set MIN=?,MAX=?,DISCOUNT_SUMM=? where SHOPUSER_DISCOUNTS_ID=? and SHOPUSER_DISCOUNTS_RANGE_LIST_ID=?',stripslashes($_REQUEST['MIN'])+0,stripslashes($_REQUEST['MAX'])+0,stripslashes($_REQUEST['DISCOUNT_SUMM']),$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{


$_REQUEST['iid']=$cmf->GetSequence('SHOPUSER_DISCOUNTS_RANGE_LIST');








$cmf->execute('insert into SHOPUSER_DISCOUNTS_RANGE_LIST (SHOPUSER_DISCOUNTS_ID,SHOPUSER_DISCOUNTS_RANGE_LIST_ID,MIN,MAX,DISCOUNT_SUMM) values (?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['MIN'])+0,stripslashes($_REQUEST['MAX'])+0,stripslashes($_REQUEST['DISCOUNT_SUMM']));
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_SHOPUSER_DISCOUNTS_RANGE_LIST_ID,$V_MIN,$V_MAX,$V_DISCOUNT_SUMM)=$cmf->selectrow_arrayQ('select SHOPUSER_DISCOUNTS_RANGE_LIST_ID,MIN,MAX,DISCOUNT_SUMM from SHOPUSER_DISCOUNTS_RANGE_LIST where SHOPUSER_DISCOUNTS_ID=? and SHOPUSER_DISCOUNTS_RANGE_LIST_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


@print <<<EOF
<h2 class="h2">Редактирование - Список возможных диапазонов</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="SHOPUSER_DISCOUNTS.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(DISCOUNT_SUMM);">
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
<tr bgcolor="#FFFFFF"><th width="1%"><b>Min значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MIN" value="$V_MIN" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Max значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MAX" value="$V_MAX" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Скидка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="DISCOUNT_SUMM" value="$V_DISCOUNT_SUMM" size="90" /><br />

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
list($V_SHOPUSER_DISCOUNTS_RANGE_LIST_ID,$V_MIN,$V_MAX,$V_DISCOUNT_SUMM)=array('','','','');


@print <<<EOF
<h2 class="h2">Добавление - Список возможных диапазонов</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="SHOPUSER_DISCOUNTS.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(DISCOUNT_SUMM);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Min значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MIN" value="$V_MIN" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Max значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MAX" value="$V_MAX" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Скидка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="DISCOUNT_SUMM" value="$V_DISCOUNT_SUMM" size="90" /><br />

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
list($ORDERING)=$cmf->selectrow_array('select ORDERING from SHOPUSER_DISCOUNTS where SHOPUSER_DISCOUNTS_ID=?',$id);
$cmf->execute('update SHOPUSER_DISCOUNTS set ORDERING=ORDERING-1 where ORDERING>?',$ORDERING);
$cmf->execute('delete from SHOPUSER_DISCOUNTS where SHOPUSER_DISCOUNTS_ID=?',$id);

 }

}


if($_REQUEST['e'] == 'UP')
{
list($ORDERING)=$cmf->selectrow_array('select ORDERING from SHOPUSER_DISCOUNTS where SHOPUSER_DISCOUNTS_ID=?',$_REQUEST['id']);
if($ORDERING>1)
{
$cmf->execute('update SHOPUSER_DISCOUNTS set ORDERING=ORDERING+1 where ORDERING=?',$ORDERING-1);
$cmf->execute('update SHOPUSER_DISCOUNTS set ORDERING=ORDERING-1 where SHOPUSER_DISCOUNTS_ID=?',$_REQUEST['id']);
}
}

if($_REQUEST['e'] == 'DN')
{
list($ORDERING)=$cmf->selectrow_array('select ORDERING from SHOPUSER_DISCOUNTS where SHOPUSER_DISCOUNTS_ID=?',$_REQUEST['id']);
$MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from SHOPUSER_DISCOUNTS');
if($ORDERING<$MAXORDERING)
{
$cmf->execute('update SHOPUSER_DISCOUNTS set ORDERING=ORDERING-1 where ORDERING=?',$ORDERING+1);
$cmf->execute('update SHOPUSER_DISCOUNTS set ORDERING=ORDERING+1 where SHOPUSER_DISCOUNTS_ID=?',$_REQUEST['id']);
}
}


if($_REQUEST['e'] == 'Добавить')
{


$_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from SHOPUSER_DISCOUNTS');
$_REQUEST['ORDERING']++;


$_REQUEST['id']=$cmf->GetSequence('SHOPUSER_DISCOUNTS');






		
				
    if(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_usr_disc_small',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_usr_disc_small',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_usr_disc_small',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

		
				
    if(isset($_FILES['NOT_IMAGE2']['tmp_name']) && $_FILES['NOT_IMAGE2']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_usr_disc_big',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_usr_disc_big',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_usr_disc_big',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE2']) && $_REQUEST['CLR_IMAGE2']){$_REQUEST['IMAGE2']=$cmf->UnlinkFile($_REQUEST['IMAGE2'],$VIRTUAL_IMAGE_PATH);}
	
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;



$cmf->execute('insert into SHOPUSER_DISCOUNTS (SHOPUSER_DISCOUNTS_ID,NAME,CLASS_NAME,MIN,MAX,IMAGE1,IMAGE2,STATUS,ORDERING) values (?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['CLASS_NAME']),stripslashes($_REQUEST['MIN'])+0,stripslashes($_REQUEST['MAX'])+0,stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['IMAGE2']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['ORDERING']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{







		
				
    if(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_usr_disc_small',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_usr_disc_small',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_usr_disc_small',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

		
				
    if(isset($_FILES['NOT_IMAGE2']['tmp_name']) && $_FILES['NOT_IMAGE2']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_usr_disc_big',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_usr_disc_big',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_usr_disc_big',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE2']) && $_REQUEST['CLR_IMAGE2']){$_REQUEST['IMAGE2']=$cmf->UnlinkFile($_REQUEST['IMAGE2'],$VIRTUAL_IMAGE_PATH);}
	
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('update SHOPUSER_DISCOUNTS set NAME=?,CLASS_NAME=?,MIN=?,MAX=?,IMAGE1=?,IMAGE2=?,STATUS=? where SHOPUSER_DISCOUNTS_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['CLASS_NAME']),stripslashes($_REQUEST['MIN'])+0,stripslashes($_REQUEST['MAX'])+0,stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['IMAGE2']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_SHOPUSER_DISCOUNTS_ID,$V_NAME,$V_CLASS_NAME,$V_MIN,$V_MAX,$V_IMAGE1,$V_IMAGE2,$V_STATUS)=
$cmf->selectrow_arrayQ('select SHOPUSER_DISCOUNTS_ID,NAME,CLASS_NAME,MIN,MAX,IMAGE1,IMAGE2,STATUS from SHOPUSER_DISCOUNTS where SHOPUSER_DISCOUNTS_ID=?',$_REQUEST['id']);



if(isset($V_IMAGE1))
{
   $IM_IMAGE1=split('#',$V_IMAGE1);
   if(isset($IM_5[1]) && $IM_IMAGE1[1] > 150){$IM_IMAGE1[2]=$IM_IMAGE1[2]*150/$IM_IMAGE1[1]; $IM_IMAGE1[1]=150;}
}

if(isset($V_IMAGE2))
{
   $IM_IMAGE2=split('#',$V_IMAGE2);
   if(isset($IM_6[1]) && $IM_IMAGE2[1] > 150){$IM_IMAGE2[2]=$IM_IMAGE2[2]*150/$IM_IMAGE2[1]; $IM_IMAGE2[1]=150;}
}

$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Скидка покупателя</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="SHOPUSER_DISCOUNTS.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(CLASS_NAME);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="type" value="9" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Название лкасса:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="CLASS_NAME" value="$V_CLASS_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Min значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MIN" value="$V_MIN" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Max значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MAX" value="$V_MAX" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка (маленькая):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE1[0]))
{
if(strchr($IM_IMAGE1[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE1[0] = !empty($IM_IMAGE1[0]) ? $IM_IMAGE1[0]:0;
$IM_IMAGE1[1] = !empty($IM_IMAGE1[1]) ? $IM_IMAGE1[1]:0;
$IM_IMAGE1[2] = !empty($IM_IMAGE1[2]) ? $IM_IMAGE1[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]" width="$IM_IMAGE1[1]" height="$IM_IMAGE1[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE1" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE1" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка (большая):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE2" value="$V_IMAGE2" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE2[0]))
{
if(strchr($IM_IMAGE2[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE2[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE2[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE2[0] = !empty($IM_IMAGE2[0]) ? $IM_IMAGE2[0]:0;
$IM_IMAGE2[1] = !empty($IM_IMAGE2[1]) ? $IM_IMAGE2[1]:0;
$IM_IMAGE2[2] = !empty($IM_IMAGE2[2]) ? $IM_IMAGE2[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE2[0]" width="$IM_IMAGE2[1]" height="$IM_IMAGE2[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE2" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE2" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Статус:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;




print <<<EOF
<a name="f1"></a><h3 class="h3">Список возможных диапазонов</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="SHOPUSER_DISCOUNTS.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="6">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select SHOPUSER_DISCOUNTS_RANGE_LIST_ID,MIN,MAX,DISCOUNT_SUMM from SHOPUSER_DISCOUNTS_RANGE_LIST where SHOPUSER_DISCOUNTS_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>Min значение</th><th>Max значение</th><th>Скидка</th><td></td></tr>
EOF;
while(list($V_SHOPUSER_DISCOUNTS_RANGE_LIST_ID,$V_MIN,$V_MAX,$V_DISCOUNT_SUMM)=mysql_fetch_array($sth, MYSQL_NUM))
{


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_SHOPUSER_DISCOUNTS_RANGE_LIST_ID" /></td>
<td>$V_SHOPUSER_DISCOUNTS_RANGE_LIST_ID</td><td>$V_MIN</td><td>$V_MAX</td><td>$V_DISCOUNT_SUMM</td><td nowrap="">

<a href="SHOPUSER_DISCOUNTS.php?e1=ED&amp;iid=$V_SHOPUSER_DISCOUNTS_RANGE_LIST_ID&amp;id={$_REQUEST['id']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_SHOPUSER_DISCOUNTS_ID,$V_NAME,$V_CLASS_NAME,$V_MIN,$V_MAX,$V_IMAGE1,$V_IMAGE2,$V_STATUS,$V_ORDERING)=array('','','','','','','','','');

$IM_IMAGE1=array('','','');
$IM_IMAGE2=array('','','');
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Скидка покупателя</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="SHOPUSER_DISCOUNTS.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(CLASS_NAME);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Название лкасса:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="CLASS_NAME" value="$V_CLASS_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Min значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MIN" value="$V_MIN" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Max значение:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="MAX" value="$V_MAX" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка (маленькая):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE1[0]))
{
if(strchr($IM_IMAGE1[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE1[0] = !empty($IM_IMAGE1[0]) ? $IM_IMAGE1[0]:0;
$IM_IMAGE1[1] = !empty($IM_IMAGE1[1]) ? $IM_IMAGE1[1]:0;
$IM_IMAGE1[2] = !empty($IM_IMAGE1[2]) ? $IM_IMAGE1[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]" width="$IM_IMAGE1[1]" height="$IM_IMAGE1[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE1" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE1" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка (большая):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE2" value="$V_IMAGE2" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE2[0]))
{
if(strchr($IM_IMAGE2[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE2[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE2[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE2[0] = !empty($IM_IMAGE2[0]) ? $IM_IMAGE2[0]:0;
$IM_IMAGE2[1] = !empty($IM_IMAGE2[1]) ? $IM_IMAGE2[1]:0;
$IM_IMAGE2[2] = !empty($IM_IMAGE2[2]) ? $IM_IMAGE2[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE2[0]" width="$IM_IMAGE2[1]" height="$IM_IMAGE2[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE2" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE2" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Статус:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>

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


print '<h2 class="h2">Скидка покупателя</h2><form action="SHOPUSER_DISCOUNTS.php" method="POST">';



$pagesize=20;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from SHOPUSER_DISCOUNTS A where 1');

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="SHOPUSER_DISCOUNTS.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.SHOPUSER_DISCOUNTS_ID,A.NAME,A.MIN,A.MAX,A.STATUS from SHOPUSER_DISCOUNTS A where 1'.' order by A.ORDERING limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Название</th><th>Min значение</th><th>Max значение</th><td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_SHOPUSER_DISCOUNTS_ID,$V_NAME,$V_MIN,$V_MAX,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="id[]" value="$V_SHOPUSER_DISCOUNTS_ID" /></td>
<td>$V_SHOPUSER_DISCOUNTS_ID</td><td>$V_NAME</td><td>$V_MIN</td><td>$V_MAX</td><td nowrap="">
<a href="SHOPUSER_DISCOUNTS.php?e=UP&amp;id=$V_SHOPUSER_DISCOUNTS_ID"><img src="i/up.gif" border="0" /></a>
<a href="SHOPUSER_DISCOUNTS.php?e=DN&amp;id=$V_SHOPUSER_DISCOUNTS_ID"><img src="i/dn.gif" border="0" /></a>
EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="SHOPUSER_DISCOUNTS.php?e=ED&amp;id=$V_SHOPUSER_DISCOUNTS_ID&amp;p={$_REQUEST['p']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
