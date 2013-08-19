<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('BRAND');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;
$VIRTUAL_IMAGE_PATH="/br/";






if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';
if(!isset($_REQUEST['l']))$_REQUEST['l']='';











if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
$cmf->execute('delete from BRAND where BRAND_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('BRAND');




		
				
    if(isset($_FILES['NOT_IMAGE']['tmp_name']) && $_FILES['NOT_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE']) && $_REQUEST['CLR_IMAGE']){$_REQUEST['IMAGE']=$cmf->UnlinkFile($_REQUEST['IMAGE'],$VIRTUAL_IMAGE_PATH);}
	




$_REQUEST['IN_INDEX']=isset($_REQUEST['IN_INDEX']) && $_REQUEST['IN_INDEX']?1:0;
$_REQUEST['IN_ALL_PAGES']=isset($_REQUEST['IN_ALL_PAGES']) && $_REQUEST['IN_ALL_PAGES']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into BRAND (BRAND_ID,NAME,ID_FROM_VBD,IMAGE,URL,ALT_NAME,DESCRIPTION,COUNT_,IN_INDEX,IN_ALL_PAGES,STATUS) values (?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ID_FROM_VBD'])+0,stripslashes($_REQUEST['IMAGE']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['ALT_NAME']),stripslashes($_REQUEST['DESCRIPTION']),0,stripslashes($_REQUEST['IN_INDEX']),stripslashes($_REQUEST['IN_ALL_PAGES']),stripslashes($_REQUEST['STATUS']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{





		
				
    if(isset($_FILES['NOT_IMAGE']['tmp_name']) && $_FILES['NOT_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE']) && $_REQUEST['CLR_IMAGE']){$_REQUEST['IMAGE']=$cmf->UnlinkFile($_REQUEST['IMAGE'],$VIRTUAL_IMAGE_PATH);}
	




$_REQUEST['IN_INDEX']=isset($_REQUEST['IN_INDEX']) && $_REQUEST['IN_INDEX']?1:0;
$_REQUEST['IN_ALL_PAGES']=isset($_REQUEST['IN_ALL_PAGES']) && $_REQUEST['IN_ALL_PAGES']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('update BRAND set NAME=?,ID_FROM_VBD=?,IMAGE=?,URL=?,ALT_NAME=?,DESCRIPTION=?,IN_INDEX=?,IN_ALL_PAGES=?,STATUS=? where BRAND_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ID_FROM_VBD'])+0,stripslashes($_REQUEST['IMAGE']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['ALT_NAME']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IN_INDEX']),stripslashes($_REQUEST['IN_ALL_PAGES']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_BRAND_ID,$V_NAME,$V_ID_FROM_VBD,$V_IMAGE,$V_URL,$V_ALT_NAME,$V_DESCRIPTION,$V_IN_INDEX,$V_IN_ALL_PAGES,$V_STATUS)=
$cmf->selectrow_arrayQ('select BRAND_ID,NAME,ID_FROM_VBD,IMAGE,URL,ALT_NAME,DESCRIPTION,IN_INDEX,IN_ALL_PAGES,STATUS from BRAND where BRAND_ID=?',$_REQUEST['id']);



if(isset($V_IMAGE))
{
   $IM_IMAGE=split('#',$V_IMAGE);
   if(isset($IM_3[1]) && $IM_IMAGE[1] > 150){$IM_IMAGE[2]=$IM_IMAGE[2]*150/$IM_IMAGE[1]; $IM_IMAGE[1]=150;}
}

$V_IN_INDEX=$V_IN_INDEX?'checked':'';
$V_IN_ALL_PAGES=$V_IN_ALL_PAGES?'checked':'';
$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Производители</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="BRAND.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(URL) &amp;&amp; checkXML(ALT_NAME) &amp;&amp; checkXML(DESCRIPTION);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="type" value="6" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<input type="hidden" name="l" value="{$_REQUEST['l']}" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>ID внешей БД:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ID_FROM_VBD" value="$V_ID_FROM_VBD" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Иконка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE" value="$V_IMAGE" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE[0]))
{
if(strchr($IM_IMAGE[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE[0] = isset($IM_IMAGE[0]) ? $IM_IMAGE[0]:0;
$IM_IMAGE[1] = isset($IM_IMAGE[1]) ? $IM_IMAGE[1]:0;
$IM_IMAGE[2] = isset($IM_IMAGE[2]) ? $IM_IMAGE[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]" width="$IM_IMAGE[1]" height="$IM_IMAGE[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Альтернативное название для URL в категории(только латиница):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ALT_NAME" value="$V_ALT_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><td></td><td /></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="7" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Выводить на главной странице:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_INDEX' value='1' $V_IN_INDEX/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Участвовать на всех страницах сайта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_ALL_PAGES' value='1' $V_IN_ALL_PAGES/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;



$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_BRAND_ID,$V_NAME,$V_ID_FROM_VBD,$V_IMAGE,$V_URL,$V_ALT_NAME,$V_DESCRIPTION,$V_COUNT_,$V_IN_INDEX,$V_IN_ALL_PAGES,$V_STATUS)=array('','','','','','','','','','','');

$IM_IMAGE=array('','','');
$V_IN_INDEX='';
$V_IN_ALL_PAGES='';
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Производители</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="BRAND.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(URL) &amp;&amp; checkXML(ALT_NAME) &amp;&amp; checkXML(DESCRIPTION);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>ID внешей БД:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ID_FROM_VBD" value="$V_ID_FROM_VBD" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Иконка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE" value="$V_IMAGE" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE[0]))
{
if(strchr($IM_IMAGE[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE[0] = isset($IM_IMAGE[0]) ? $IM_IMAGE[0]:0;
$IM_IMAGE[1] = isset($IM_IMAGE[1]) ? $IM_IMAGE[1]:0;
$IM_IMAGE[2] = isset($IM_IMAGE[2]) ? $IM_IMAGE[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]" width="$IM_IMAGE[1]" height="$IM_IMAGE[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Альтернативное название для URL в категории(только латиница):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ALT_NAME" value="$V_ALT_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><td></td><td /></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="7" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Выводить на главной странице:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_INDEX' value='1' $V_IN_INDEX/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Участвовать на всех страницах сайта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_ALL_PAGES' value='1' $V_IN_ALL_PAGES/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>

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


$LETER='';
//if($_REQUEST['l']){$LETER=pack("C",$_REQUEST['l']);}
if($_REQUEST['l']){$LETER=$_REQUEST['l'];}

print '<h2 class="h2">Производители</h2><form action="BRAND.php" method="POST">';

//$sth=$cmf->execute('select ascii(upper(substring(NAME,1,1))) from BRAND group by substring(NAME,1,1)');
$sth=$cmf->execute('select LEFT(NAME,1) from BRAND group by LEFT(NAME,1)');
while(list ($id)=mysql_fetch_array($sth, MYSQL_NUM))
{
//$V_LETER=pack("C",$id);
$V_LETER=$id;
//if($id==32){$V_LETER='#';}
if($_REQUEST['l']==$id)
 { 
  print '<b class="red">'.$V_LETER.'</b> ';
 } 
 else 
 {
  print '<a href="BRAND.php?l='.urlencode($id).'" class="t">'.$V_LETER.'</a> ';
 }
}
$_REQUEST['l'] = urlencode($_REQUEST['l']);

print '&#160;&#160;&#160;<a href="BRAND.php" class="t">все</a><br />';



 if($_REQUEST['l']!=''){
$sth=$cmf->execute('select A.BRAND_ID,A.NAME,A.IMAGE,A.URL,A.IN_INDEX,A.IN_ALL_PAGES,A.STATUS from BRAND A where 1 and LEFT(A.NAME,1)=?'.' order by A.NAME ',$LETER);
}
else
{
$sth=$cmf->execute('select A.BRAND_ID,A.NAME,A.IMAGE,A.URL,A.IN_INDEX,A.IN_ALL_PAGES,A.STATUS from BRAND A where 1'.' order by A.NAME ');
}




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
<input type="hidden" name="l" value="{$_REQUEST['l']}" />
</td></tr>
EOF;

print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Название</th><th>Иконка</th><th>URL</th><td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_BRAND_ID,$V_NAME,$V_IMAGE,$V_URL,$V_IN_INDEX,$V_IN_ALL_PAGES,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if(isset($V_IMAGE))
{
   $IM_3=split('#',$V_IMAGE);
   if(strchr($IM_3[0],".swf"))
   {
       $V_IMAGE="<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"150\" height=\"100\"align=\"middle\"><param name=\"allowScriptAccess\" value=\"sameDomain\" /><param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_3[0]\" /><param name=\"quality\" value=\"high\" /><embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_3[0]\" quality=\"high\" width=\"150\" height=\"100\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /></object>";
   }
   else
   {
      if(isset($IM_3[1]) && $IM_3[1] > 150){$IM_3[2]=$IM_3[2]*150/$IM_3[1]; $IM_3[1]=150;
      $V_IMAGE="<img src=\"/images$VIRTUAL_IMAGE_PATH$IM_3[0]\" width=\"$IM_3[1]\" height=\"$IM_3[2]\">";}
   }
}

if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="id[]" value="$V_BRAND_ID" /></td>
<td>$V_BRAND_ID</td><td>$V_NAME</td><td>$V_IMAGE</td><td>$V_URL</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="BRAND.php?e=ED&amp;id=$V_BRAND_ID&amp;l={$_REQUEST['l']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
