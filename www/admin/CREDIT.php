<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('CREDIT');
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
$cmf->execute('delete from CREDIT where CREDIT_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('CREDIT');










$cmf->execute('insert into CREDIT (CREDIT_ID,NAME,CODE,DESCRIPTION,LONG_TEXT,COEF,EMAIL,EMAIL_TEMPLATE) values (?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['CODE']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['LONG_TEXT']),stripslashes($_REQUEST['COEF']),stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['EMAIL_TEMPLATE']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{










$cmf->execute('update CREDIT set NAME=?,CODE=?,DESCRIPTION=?,LONG_TEXT=?,COEF=?,EMAIL=?,EMAIL_TEMPLATE=? where CREDIT_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['CODE']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['LONG_TEXT']),stripslashes($_REQUEST['COEF']),stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['EMAIL_TEMPLATE']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_CREDIT_ID,$V_NAME,$V_CODE,$V_DESCRIPTION,$V_LONG_TEXT,$V_COEF,$V_EMAIL,$V_EMAIL_TEMPLATE)=
$cmf->selectrow_arrayQ('select CREDIT_ID,NAME,CODE,DESCRIPTION,LONG_TEXT,COEF,EMAIL,EMAIL_TEMPLATE from CREDIT where CREDIT_ID=?',$_REQUEST['id']);



@print <<<EOF
<h2 class="h2">Редактирование - Кредит</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="CREDIT.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(CODE) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(LONG_TEXT) &amp;&amp; checkXML(COEF) &amp;&amp; checkXML(EMAIL) &amp;&amp; checkXML(EMAIL_TEMPLATE);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Код банка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="CODE" value="$V_CODE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

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

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="LONG_TEXT" name="LONG_TEXT" rows="20" cols="90">
EOF;
$V_LONG_TEXT = htmlspecialchars_decode($V_LONG_TEXT);
echo $V_LONG_TEXT;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'LONG_TEXT', {
      customConfig : 'ckeditor/light_config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Коэф. цены:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="COEF" value="$V_COEF" size="5" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Email менеджера банка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Шаблон письма:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="EMAIL_TEMPLATE" name="EMAIL_TEMPLATE" rows="20" cols="90">
EOF;
$V_EMAIL_TEMPLATE = htmlspecialchars_decode($V_EMAIL_TEMPLATE);
echo $V_EMAIL_TEMPLATE;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'EMAIL_TEMPLATE', {
      customConfig : 'ckeditor/light_config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

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
list($V_CREDIT_ID,$V_NAME,$V_CODE,$V_DESCRIPTION,$V_LONG_TEXT,$V_COEF,$V_EMAIL,$V_EMAIL_TEMPLATE)=array('','','','','','','','');

@print <<<EOF
<h2 class="h2">Добавление - Кредит</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="CREDIT.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(CODE) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(LONG_TEXT) &amp;&amp; checkXML(COEF) &amp;&amp; checkXML(EMAIL) &amp;&amp; checkXML(EMAIL_TEMPLATE);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Код банка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="CODE" value="$V_CODE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

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

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="LONG_TEXT" name="LONG_TEXT" rows="20" cols="90">
EOF;
$V_LONG_TEXT = htmlspecialchars_decode($V_LONG_TEXT);
echo $V_LONG_TEXT;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'LONG_TEXT', {
      customConfig : 'ckeditor/light_config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Коэф. цены:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="COEF" value="$V_COEF" size="5" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Email менеджера банка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Шаблон письма:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="EMAIL_TEMPLATE" name="EMAIL_TEMPLATE" rows="20" cols="90">
EOF;
$V_EMAIL_TEMPLATE = htmlspecialchars_decode($V_EMAIL_TEMPLATE);
echo $V_EMAIL_TEMPLATE;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'EMAIL_TEMPLATE', {
      customConfig : 'ckeditor/light_config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

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


print '<h2 class="h2">Кредит</h2><form action="CREDIT.php" method="POST">';



$_REQUEST['s']+=0;
$SORTNAMES=array('N','Название','Код банка','Коэф. цены','Email менеджера банка');
$SORTQUERY=array('order by A.CREDIT_ID ','order by A.CREDIT_ID desc ','order by A.NAME ','order by A.NAME desc ','order by A.CODE ','order by A.CODE desc ','order by A.COEF ','order by A.COEF desc ','order by A.EMAIL ','order by A.EMAIL desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CREDIT.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CREDIT.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="CREDIT.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}



$sth=$cmf->execute('select A.CREDIT_ID,A.NAME,A.CODE,A.COEF,A.EMAIL from CREDIT A where 1'.' '.$SORTQUERY[$_REQUEST['s']]);





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="7">
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
while(list($V_CREDIT_ID,$V_NAME,$V_CODE,$V_COEF,$V_EMAIL)=mysql_fetch_array($sth, MYSQL_NUM))
{


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_CREDIT_ID" /></td>
<td>$V_CREDIT_ID</td><td>$V_NAME</td><td>$V_CODE</td><td>$V_COEF</td><td>$V_EMAIL</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="CREDIT.php?e=ED&amp;id=$V_CREDIT_ID&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
