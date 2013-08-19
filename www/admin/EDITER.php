<?
ini_set("display_errors",1);
ini_set("display_startup_errors",1);

require ('core.php');
$cmf= new SCMF();
list ($E_article,$E_edit,$E_view)=$cmf->selectrow_array('select ARTICLE,EDIT,VIEW from CMF_XMLS_ARTICLE where TYPE=?',$_REQUEST['type']);
$cmf->setArticle($E_article);

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}
$cmf->HeaderNoCache();
$cmf->MakeCommonHeader();

$VIRTUAL_IMAGE_PATH='/docs/';
$VIRTUAL_FILE_PATH='/docs/';
list($pictypes,$flashtypes,$filetypes)=array('','','');



if(!isset($_REQUEST['event']))$_REQUEST['event']='';
if(empty($_REQUEST['CMF_LANG_ID'])) $_REQUEST['CMF_LANG_ID'] = 0;
if(empty($_REQUEST['XML'])) $_REQUEST['XML'] = '';

$_REQUEST['XML']= stripslashes($_REQUEST['XML']);
//$_REQUEST['XML']=preg_replace("/\<img/","<picture format=\"jpg\"",$_REQUEST['XML']);
//$_REQUEST['XML']=preg_replace("/\<a href/","<link href",$_REQUEST['XML']);
//$_REQUEST['XML']=preg_replace("/\<\/a>/","</link>",$_REQUEST['XML']);
//$_REQUEST['XML'] = str_replace("<p>&nbsp;</p>","",$_REQUEST['XML']);
$_REQUEST['XML'] = str_replace("&nbsp;"," ",$_REQUEST['XML']);




$param=$cmf->selectrow_array('select 1 from XMLS where XMLS_ID=? and TYPE=?',$_REQUEST['id'],$_REQUEST['type']);



if($param && $_REQUEST['event']=='Сохранить')
{
   if(!$_REQUEST['XML'])
   {
      //echo "Удяляем";         
      $cmf->execute('delete from XMLS where XMLS_ID=? and TYPE=?', $_REQUEST['id'],$_REQUEST['type']);
   }
   else
   {
      $cmf->execute('update XMLS set XML=? where XMLS_ID=? and TYPE=?', $_REQUEST['XML'], $_REQUEST['id'],$_REQUEST['type']);
   }
}
else
{
   if($_REQUEST['event']=='Сохранить' && $_REQUEST['XML']!='') $cmf->execute('insert into XMLS(XMLS_ID,TYPE,XML) values(?,?,?)', $_REQUEST['id'],$_REQUEST['type'],$_REQUEST['XML']); // echo mysql_error();
}



if($_REQUEST['id']>0)
 {
$_REQUEST['XML']=$cmf->selectrow_array('select XML from XMLS where XMLS_ID=? and TYPE=?',$_REQUEST['id'],$_REQUEST['type']);

//if(!$_REQUEST['XML']) { $_REQUEST['XML']="<story>\n</story>"; };
$Pictures='';
$Files='';
$Flashes='';
}


$_REQUEST['XML']=preg_replace("/\<picture format=\"jpg\"/","<img",$_REQUEST['XML']);
$_REQUEST['XML']=preg_replace("/\<link href/","<a href",$_REQUEST['XML']);
$_REQUEST['XML']=preg_replace("/\<\/link>/","</a>",$_REQUEST['XML']);
//$_REQUEST['XML']=htmlspecialchars($_REQUEST['XML']);

if(empty($_REQUEST['r'])) $_REQUEST['r'] = 0;
if(empty($_REQUEST['iid'])) $_REQUEST['iid'] = 0;


$E_edit=preg_replace("/\%(.+?)\%/e","\$_REQUEST['\\1']",$E_edit);
$E_view=@preg_replace("/\%(.+?)\%/e","\$_REQUEST['\\1']",$E_view);


$tmppp=time();
$editor_value = $_REQUEST['XML'];

@print <<<EOD
<table border="0" width="100%" cellspacing="0" cellpadding="3" height="100%">
<form name=MAIN_FORM action=EDITER.php method=POST ENCTYPE="multipart/form-data" onsubmit="return checkXML(XML);">
<input type=hidden name=id value="{$_REQUEST['id']}">
<input type=hidden name="CMF_LANG_ID" value="{$_REQUEST['CMF_LANG_ID']}">
<input type=hidden name=pid value="{$_REQUEST['pid']}">
<input type="hidden" name="sid" value="{$_REQUEST['sid']}">
<input type=hidden name=type value="{$_REQUEST['type']}">
<input type="hidden" name="r" value="{$_REQUEST['r']}">
<input type="hidden" name="p" value="{$_REQUEST['p']}">
<input type="hidden" name="s" value="{$_REQUEST['s']}">
<input type="hidden" name="l" value="{$_REQUEST['l']}">
<input type=hidden name="DO_PREVIEW" value="{$_REQUEST['DO_PREVIEW']}">
<tr><td>


</tr></table>
</td></tr>
<tr valign=top>
<td width=75% height=100%>
<textarea cols="80" id="XML" name="XML" rows="10">
EOD;
echo $editor_value;
@print <<<EOD
</textarea>
<script type="text/javascript">
//<![CDATA[

  // This call can be placed at any point after the
  // <textarea>, or inside a <head><script> in a
  // window.onload event handler.

  // Replace the <textarea id="editor"> with an CKEditor
  // instance, using default configurations.
  CKEDITOR.replace( 'XML', {
      width : 800,
      height : 400,  
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
      });
//]]>
</script>
</td>
<td>
        <nolayer><div style="overflow:auto;height:400px"></nolayer>
        <nolayer></div></nolayer>
    </td>
</tr><tr>
<td colspan=2><input id=save type=submit name=event value="Сохранить" class="gbt bsave"><button onclick="return doClose();" class="gbt bcancel">Закрыть</button>
    &nbsp;&nbsp;<span style='font:bold 8pt tahoma;color:red;'>$errormessage</span></td>
</tr>
</form>
</table>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
if(document.forms.MAIN_FORM.DO_PREVIEW.value=="1"){
    document.forms.MAIN_FORM.DO_PREVIEW.value="0";
    var win=window.open("{$E_view}&amp;tmpp=$tmppp");
    win.focus();
}
function doPreview(obj) {
        obj.DO_PREVIEW.value="1";
        obj.submit();
        return false;
}
function doClose(){
       if(confirm("Сохраните все изменения перед закрытием!"))window.location="{$E_edit}&amp;tmpp=$tmppp";
        return false;
}
//-->
</SCRIPT>

</body>
EOD;

$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);

?>
