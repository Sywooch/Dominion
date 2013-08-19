<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('BANN_SECTION');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;
$VIRTUAL_IMAGE_PATH="/bn/";




$cmf->ENUM_TYPE=array(' картинка ',' флеш баннер',' текст');



if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';




if(!isset($_REQUEST['e1']))$_REQUEST['e1']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e1') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$ORDERING=$cmf->selectrow_array('select ORDERING from SECTION_ALIGN where BANN_SECTION_ID=? and SECTION_ALIGN_ID=?',$_REQUEST['id'],$id);
$cmf->execute('update SECTION_ALIGN set ORDERING=ORDERING-1 where BANN_SECTION_ID=? and ORDERING>?',$_REQUEST['id'],$ORDERING);
$cmf->execute('delete from SECTION_ALIGN where BANN_SECTION_ID=? and SECTION_ALIGN_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}


if($cmf->Param('e1') == 'UP')
{
$ORDERING=$cmf->selectrow_array('select ORDERING from SECTION_ALIGN where BANN_SECTION_ID=? and SECTION_ALIGN_ID=?',$_REQUEST['id'],$_REQUEST['iid']);
if($ORDERING>1)
{
$cmf->execute('update SECTION_ALIGN set ORDERING=ORDERING+1 where BANN_SECTION_ID=? and ORDERING=?',$_REQUEST['id'],$ORDERING-1);
$cmf->execute('update SECTION_ALIGN set ORDERING=ORDERING-1 where BANN_SECTION_ID=? and SECTION_ALIGN_ID=?',$_REQUEST['id'],$_REQUEST['iid']);
}
$_REQUEST['e']='ED';
}

if($cmf->Param('e1') == 'DN')
{
$ORDERING=$cmf->selectrow_array('select ORDERING from SECTION_ALIGN where BANN_SECTION_ID=? and SECTION_ALIGN_ID=?',$_REQUEST['id'],$_REQUEST['iid']);
$MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from SECTION_ALIGN');
if($ORDERING<$MAXORDERING)
{
$cmf->execute('update SECTION_ALIGN set ORDERING=ORDERING-1 where BANN_SECTION_ID=? and ORDERING=?',$_REQUEST['id'],$ORDERING+1);
$cmf->execute('update SECTION_ALIGN set ORDERING=ORDERING+1 where BANN_SECTION_ID=? and SECTION_ALIGN_ID=?',$_REQUEST['id'],$_REQUEST['iid']);
}
$_REQUEST['e']='ED';
}



if($cmf->Param('e1') == 'Изменить')
{




		
				
    if(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	





$_REQUEST['NEWWIN']=isset($_REQUEST['NEWWIN']) && $_REQUEST['NEWWIN']?1:0;
$_REQUEST['IS_ADV']=isset($_REQUEST['IS_ADV']) && $_REQUEST['IS_ADV']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('update SECTION_ALIGN set ALIGN_ID=?,IMAGE1=?,ALT=?,DESCRIPTION=?,BANNER_CODE=?,TYPE=?,URL=?,NEWWIN=?,IS_ADV=?,STATUS=? where BANN_SECTION_ID=? and SECTION_ALIGN_ID=?',stripslashes($_REQUEST['ALIGN_ID'])+0,stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['ALT']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['BANNER_CODE']),stripslashes($_REQUEST['TYPE']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['NEWWIN']),stripslashes($_REQUEST['IS_ADV']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{

$_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from SECTION_ALIGN where =?',$_REQUEST['id']);
$_REQUEST['ORDERING']++;


$_REQUEST['iid']=$cmf->GetSequence('SECTION_ALIGN');





		
				
    if(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	





$_REQUEST['NEWWIN']=isset($_REQUEST['NEWWIN']) && $_REQUEST['NEWWIN']?1:0;
$_REQUEST['IS_ADV']=isset($_REQUEST['IS_ADV']) && $_REQUEST['IS_ADV']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;



$cmf->execute('insert into SECTION_ALIGN (BANN_SECTION_ID,SECTION_ALIGN_ID,ALIGN_ID,IMAGE1,ALT,DESCRIPTION,BANNER_CODE,TYPE,URL,NEWWIN,IS_ADV,STATUS,ORDERING) values (?,?,?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['ALIGN_ID'])+0,stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['ALT']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['BANNER_CODE']),stripslashes($_REQUEST['TYPE']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['NEWWIN']),stripslashes($_REQUEST['IS_ADV']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['ORDERING']));
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_SECTION_ALIGN_ID,$V_ALIGN_ID,$V_IMAGE1,$V_ALT,$V_DESCRIPTION,$V_BANNER_CODE,$V_TYPE,$V_URL,$V_NEWWIN,$V_IS_ADV,$V_STATUS)=$cmf->selectrow_arrayQ('select SECTION_ALIGN_ID,ALIGN_ID,IMAGE1,ALT,DESCRIPTION,BANNER_CODE,TYPE,URL,NEWWIN,IS_ADV,STATUS from SECTION_ALIGN where BANN_SECTION_ID=? and SECTION_ALIGN_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_ALIGN_ID=$cmf->Spravotchnik($V_ALIGN_ID,'select ALIGN_ID,NAME from ALIGN  order by NAME');        
					
if(isset($V_IMAGE1))
{
   $IM_IMAGE1=split('#',$V_IMAGE1);
   if(isset($IM_3[1]) && $IM_IMAGE1[1] > 150){$IM_IMAGE1[2]=$IM_IMAGE1[2]*150/$IM_IMAGE1[1]; $IM_IMAGE1[1]=150;}
}

$V_STR_TYPE=$cmf->Enumerator($cmf->ENUM_TYPE,$V_TYPE);
$V_NEWWIN=$V_NEWWIN?'checked':'';
$V_IS_ADV=$V_IS_ADV?'checked':'';
$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Баннеры</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="BANN_SECTION.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(ALT) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(BANNER_CODE) &amp;&amp; checkXML(URL);">
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
<tr bgcolor="#FFFFFF"><th width="1%"><b>место:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="ALIGN_ID">
				
				
				
				
				
				
				$V_STR_ALIGN_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
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
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Альт текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ALT" value="$V_ALT" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткий текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="DESCRIPTION" name="DESCRIPTION" rows="7" cols="90">
EOF;
$V_DESCRIPTION = htmlspecialchars_decode($V_DESCRIPTION);
echo $V_DESCRIPTION;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'DESCRIPTION', {
      customConfig : 'ckeditor/light_config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Код банера:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="BANNER_CODE" rows="7" cols="90">$V_BANNER_CODE</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тип:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="TYPE">$V_STR_TYPE</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>В новом окне:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='NEWWIN' value='1' $V_NEWWIN/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Рекламный:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_ADV' value='1' $V_IS_ADV/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;







$pos = 2;
$pos = $pos -1;


print <<<EOF
<a name="f2"></a><h3 class="h3">Связь "Категория товаров - Банер"</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="BANN_SECTION.php#f2" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="4">
<input type="submit" name="ell2" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="ell2" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select CATALOGUE_SECTION_ALIGN_ID,CATALOGUE_ID from CATALOGUE_SECTION_ALIGN where SECTION_ALIGN_ID=? ',$_REQUEST['iid']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[bid]');" /></td><th>N</th><th>Рубрика каталога</th><td></td></tr>
EOF;
while(list($V_CATALOGUE_SECTION_ALIGN_ID,$V_CATALOGUE_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_CATALOGUE_ID=$cmf->selectrow_arrayQ('select NAME from CATALOGUE where CATALOGUE_ID=?',$V_CATALOGUE_ID);
                                        

@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="bid[]" value="$V_CATALOGUE_SECTION_ALIGN_ID" /></td>
<td>$V_CATALOGUE_SECTION_ALIGN_ID</td><td>$V_CATALOGUE_ID</td><td nowrap="">

<a href="BANN_SECTION.php?ell2=ED&amp;bid=$V_CATALOGUE_SECTION_ALIGN_ID&amp;id={$_REQUEST['id']}&amp;iid={$_REQUEST['iid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}

if($cmf->Param('e1') == 'Новый')
{
list($V_SECTION_ALIGN_ID,$V_ALIGN_ID,$V_IMAGE1,$V_ALT,$V_DESCRIPTION,$V_BANNER_CODE,$V_TYPE,$V_URL,$V_NEWWIN,$V_IS_ADV,$V_STATUS,$V_ORDERING)=array('','','','','','','','','','','','');


$V_STR_ALIGN_ID=$cmf->Spravotchnik($V_ALIGN_ID,'select ALIGN_ID,NAME from ALIGN  order by NAME');
					
$IM_IMAGE1=array('','','');
$V_STR_TYPE=$cmf->Enumerator($cmf->ENUM_TYPE,-1);
$V_NEWWIN='checked';
$V_IS_ADV='';
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Баннеры</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="BANN_SECTION.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(ALT) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(BANNER_CODE) &amp;&amp; checkXML(URL);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>место:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="ALIGN_ID">
				
				
				
				
				
				
				$V_STR_ALIGN_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
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
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Альт текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ALT" value="$V_ALT" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткий текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="DESCRIPTION" name="DESCRIPTION" rows="7" cols="90">
EOF;
$V_DESCRIPTION = htmlspecialchars_decode($V_DESCRIPTION);
echo $V_DESCRIPTION;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'DESCRIPTION', {
      customConfig : 'ckeditor/light_config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Код банера:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="BANNER_CODE" rows="7" cols="90">$V_BANNER_CODE</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тип:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="TYPE">$V_STR_TYPE</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>В новом окне:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='NEWWIN' value='1' $V_NEWWIN/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Рекламный:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_ADV' value='1' $V_IS_ADV/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}






if(!isset($_REQUEST['ell2']))$_REQUEST['ell2']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('ell2') == 'Удалить') and is_array($_REQUEST['bid']))
{
foreach ($_REQUEST['bid'] as $id)
 {

$cmf->execute('delete from CATALOGUE_SECTION_ALIGN where SECTION_ALIGN_ID=? and CATALOGUE_SECTION_ALIGN_ID=?',$_REQUEST['iid'],$id);

 }


$pos = 2;
$pos = $pos -1;
//$_REQUEST['e2']='ED';
echo '<meta http-equiv="Refresh" content="1; url=BANN_SECTION.php?e'.$pos.'=ED&amp;id='.$_REQUEST['id'].'&amp;iid='.$_REQUEST['iid'].'">';

$visible=0;
}




if($cmf->Param('ell2') == 'Изменить')
{



$cmf->execute('update CATALOGUE_SECTION_ALIGN set CATALOGUE_ID=? where SECTION_ALIGN_ID=? and CATALOGUE_SECTION_ALIGN_ID=?',stripslashes($_REQUEST['CATALOGUE_ID'])+0,$_REQUEST['iid'],$_REQUEST['bid']);


$visible=0;
$pos = 2;
$pos = $pos -1;
//$_REQUEST['e2']='ED';
echo '<meta http-equiv="Refresh" content="1; url=BANN_SECTION.php?e'.$pos.'=ED&amp;id='.$_REQUEST['id'].'&amp;iid='.$_REQUEST['iid'].'">';
};


if($cmf->Param('ell2') == 'Добавить')
{


$_REQUEST['bid']=$cmf->GetSequence('CATALOGUE_SECTION_ALIGN');






$cmf->execute('insert into CATALOGUE_SECTION_ALIGN (SECTION_ALIGN_ID,CATALOGUE_SECTION_ALIGN_ID,CATALOGUE_ID) values (?,?,?)',$_REQUEST['iid'],$_REQUEST['bid'],stripslashes($_REQUEST['CATALOGUE_ID'])+0);


$visible=0;

$pos = 2;
$pos = $pos -1;
//$_REQUEST['e2']='ED';
echo '<meta http-equiv="Refresh" content="1; url=BANN_SECTION.php?e'.$pos.'=ED&amp;id='.$_REQUEST['id'].'&amp;iid='.$_REQUEST['iid'].'">';

}

if($cmf->Param('ell2') == 'ED')
{
list ($V_CATALOGUE_SECTION_ALIGN_ID,$V_CATALOGUE_ID)=$cmf->selectrow_arrayQ('select CATALOGUE_SECTION_ALIGN_ID,CATALOGUE_ID from CATALOGUE_SECTION_ALIGN where SECTION_ALIGN_ID=? and CATALOGUE_SECTION_ALIGN_ID=?',$_REQUEST['iid'],$_REQUEST['bid']);


$V_STR_CATALOGUE_ID=$cmf->Spravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE  order by NAME');        
					
$pos = 2;
$pos = $pos-1;

@print <<<EOF
<h2 class="h2">Редактирование - Связь "Категория товаров - Банер"</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="BANN_SECTION.php#f2" ENCTYPE="multipart/form-data" onsubmit="return true ;">
EOF;
echo '<input type="hidden" name="ell'.$pos.'" value="ED" />';
@print <<<EOF
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />
<input type="hidden" name="bid" value="{$_REQUEST['bid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'" />';

@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="ell2" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="ell2" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Рубрика каталога:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CATALOGUE_ID">
				
				
				
				
				
				
				$V_STR_CATALOGUE_ID
			</select><br />
		
	

</td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="ell2" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="ell2" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;
$visible=0;
}

if($cmf->Param('ell2') == 'Новый')
{
list($V_CATALOGUE_SECTION_ALIGN_ID,$V_CATALOGUE_ID)=array('','');


$V_STR_CATALOGUE_ID=$cmf->Spravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE  order by NAME');
					
$pos = 2;
$pos = $pos-1;

@print <<<EOF
<h2 class="h2">Добавление - Связь "Категория товаров - Банер"</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="BANN_SECTION.php#f2" ENCTYPE="multipart/form-data" onsubmit="return true ;">
EOF;
echo '<input type="hidden" name="ell'.$pos.'" value="ED" />';
@print <<<EOF
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="ell2" value="Добавить" class="gbt badd" />
<input type="submit" name="ell2" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Рубрика каталога:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CATALOGUE_ID">
				
				
				
				
				
				
				$V_STR_CATALOGUE_ID
			</select><br />
		
	

</td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="ell2" value="Добавить" class="gbt badd" />
<input type="submit" name="ell2" value="Отменить" class="gbt bcancel" />
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
$cmf->execute('delete from BANN_SECTION where BANN_SECTION_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('BANN_SECTION');




$cmf->execute('insert into BANN_SECTION (BANN_SECTION_ID,NAME) values (?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{




$cmf->execute('update BANN_SECTION set NAME=? where BANN_SECTION_ID=?',stripslashes($_REQUEST['NAME']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_BANN_SECTION_ID,$V_NAME)=
$cmf->selectrow_arrayQ('select BANN_SECTION_ID,NAME from BANN_SECTION where BANN_SECTION_ID=?',$_REQUEST['id']);



@print <<<EOF
<h2 class="h2">Редактирование - Баннерные разделы</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="BANN_SECTION.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;




print <<<EOF
<a name="f1"></a><h3 class="h3">Баннеры</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="BANN_SECTION.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="8">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select SECTION_ALIGN_ID,ALIGN_ID,IMAGE1,ALT,TYPE,URL,STATUS from SECTION_ALIGN where BANN_SECTION_ID=?  order by ORDERING',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>место</th><th>Картинка.</th><th>Альт текст</th><th>Тип</th><th>URL</th><td></td></tr>
EOF;
while(list($V_SECTION_ALIGN_ID,$V_ALIGN_ID,$V_IMAGE1,$V_ALT,$V_TYPE,$V_URL,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_ALIGN_ID=$cmf->selectrow_arrayQ('select NAME from ALIGN where ALIGN_ID=?',$V_ALIGN_ID);
                                        
if(isset($V_IMAGE1))
{
   $IM_3=split('#',$V_IMAGE1);
   if(strchr($IM_3[0],".swf"))
   {
       $V_IMAGE1="<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"150\" height=\"100\"align=\"middle\"><param name=\"allowScriptAccess\" value=\"sameDomain\" /><param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_3[0]\" /><param name=\"quality\" value=\"high\" /><embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_3[0]\" quality=\"high\" width=\"150\" height=\"100\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /></object>";
   }
   else
   {
      if(isset($IM_3[1]) && $IM_3[1] > 150){$IM_3[2]=$IM_3[2]*150/$IM_3[1]; $IM_3[1]=150;
      $V_IMAGE1="<img src=\"/images$VIRTUAL_IMAGE_PATH$IM_3[0]\" width=\"$IM_3[1]\" height=\"$IM_3[2]\">";}
   }
}

$V_TYPE=$cmf->ENUM_TYPE[$V_TYPE];
                        
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

@print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="iid[]" value="$V_SECTION_ALIGN_ID" /></td>
<td>$V_SECTION_ALIGN_ID</td><td>$V_ALIGN_ID</td><td>$V_IMAGE1</td><td>$V_ALT</td><td>$V_TYPE</td><td>$V_URL</td><td nowrap="">
<a href="BANN_SECTION.php?e1=UP&amp;iid=$V_SECTION_ALIGN_ID&amp;id={$_REQUEST['id']}#f1"><img src="i/up.gif" border="0" /></a>
<a href="BANN_SECTION.php?e1=DN&amp;iid=$V_SECTION_ALIGN_ID&amp;id={$_REQUEST['id']}#f1"><img src="i/dn.gif" border="0" /></a>
<a href="BANN_SECTION.php?e1=ED&amp;iid=$V_SECTION_ALIGN_ID&amp;id={$_REQUEST['id']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_BANN_SECTION_ID,$V_NAME)=array('','');

@print <<<EOF
<h2 class="h2">Добавление - Баннерные разделы</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="BANN_SECTION.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

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


print '<h2 class="h2">Баннерные разделы</h2><form action="BANN_SECTION.php" method="POST">';



$pagesize=120;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from BANN_SECTION A where 1');

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="BANN_SECTION.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.BANN_SECTION_ID,A.NAME from BANN_SECTION A where 1'.' order by A.NAME limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Название</th><td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_BANN_SECTION_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_BANN_SECTION_ID" /></td>
<td>$V_BANN_SECTION_ID</td><td>$V_NAME</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="BANN_SECTION.php?e=ED&amp;id=$V_BANN_SECTION_ID&amp;p={$_REQUEST['p']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
