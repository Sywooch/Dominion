<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('TEXTES');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;
$VIRTUAL_IMAGE_PATH="/textes/";






if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';












if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
$cmf->execute('delete from TEXTES where TEXTES_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{

      $_REQUEST['DESCRIPTION'] = trim($_REQUEST['DESCRIPTION']);
      $pattern = '/^(?:<p>)?(.+?)(?:<\/p>)?$/is';
      preg_match($pattern, $_REQUEST['DESCRIPTION'], $out);
      $_REQUEST['DESCRIPTION'] = isset($out[1]) ? trim($out[1]):$_REQUEST['DESCRIPTION'];    
     


$_REQUEST['id']=$cmf->GetSequence('TEXTES');





		
				
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
	


$cmf->execute('insert into TEXTES (TEXTES_ID,NAME,SYS_NAME,DESCRIPTION,IMAGE) values (?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['SYS_NAME']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IMAGE']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{

      $_REQUEST['DESCRIPTION'] = trim($_REQUEST['DESCRIPTION']);
      $pattern = '/^(?:<p>)?(.+?)(?:<\/p>)?$/is';
      preg_match($pattern, $_REQUEST['DESCRIPTION'], $out);
      $_REQUEST['DESCRIPTION'] = isset($out[1]) ? trim($out[1]):$_REQUEST['DESCRIPTION'];
     





		
				
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
	

$cmf->execute('update TEXTES set NAME=?,SYS_NAME=?,DESCRIPTION=?,IMAGE=? where TEXTES_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['SYS_NAME']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IMAGE']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_TEXTES_ID,$V_NAME,$V_SYS_NAME,$V_DESCRIPTION,$V_IMAGE)=
$cmf->selectrow_arrayQ('select TEXTES_ID,NAME,SYS_NAME,DESCRIPTION,IMAGE from TEXTES where TEXTES_ID=?',$_REQUEST['id']);



if(isset($V_IMAGE))
{
   $IM_IMAGE=split('#',$V_IMAGE);
   if(isset($IM_4[1]) && $IM_IMAGE[1] > 150){$IM_IMAGE[2]=$IM_IMAGE[2]*150/$IM_IMAGE[1]; $IM_IMAGE[1]=150;}
}

@print <<<EOF
<h2 class="h2">Редактирование - Баннерные Места</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="TEXTES.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(SYS_NAME) &amp;&amp; checkXML(DESCRIPTION);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Системное имя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SYS_NAME" value="$V_SYS_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

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

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE" value="$V_IMAGE" />
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
$IM_IMAGE[0] = !empty($IM_IMAGE[0]) ? $IM_IMAGE[0]:0;
$IM_IMAGE[1] = !empty($IM_IMAGE[1]) ? $IM_IMAGE[1]:0;
$IM_IMAGE[2] = !empty($IM_IMAGE[2]) ? $IM_IMAGE[2]:0;
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
list($V_TEXTES_ID,$V_NAME,$V_SYS_NAME,$V_DESCRIPTION,$V_IMAGE)=array('','','','','');

$IM_IMAGE=array('','','');
@print <<<EOF
<h2 class="h2">Добавление - Баннерные Места</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="TEXTES.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(SYS_NAME) &amp;&amp; checkXML(DESCRIPTION);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Системное имя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SYS_NAME" value="$V_SYS_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

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

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE" value="$V_IMAGE" />
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
$IM_IMAGE[0] = !empty($IM_IMAGE[0]) ? $IM_IMAGE[0]:0;
$IM_IMAGE[1] = !empty($IM_IMAGE[1]) ? $IM_IMAGE[1]:0;
$IM_IMAGE[2] = !empty($IM_IMAGE[2]) ? $IM_IMAGE[2]:0;
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


print '<h2 class="h2">Баннерные Места</h2><form action="TEXTES.php" method="POST">';



$pagesize=120;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from TEXTES A where 1');

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="TEXTES.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.TEXTES_ID,A.NAME,A.SYS_NAME from TEXTES A where 1'.' order by A.NAME limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Название</th><th>Системное имя</th><td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_TEXTES_ID,$V_NAME,$V_SYS_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_TEXTES_ID" /></td>
<td>$V_TEXTES_ID</td><td>$V_NAME</td><td>$V_SYS_NAME</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="TEXTES.php?e=ED&amp;id=$V_TEXTES_ID&amp;p={$_REQUEST['p']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
