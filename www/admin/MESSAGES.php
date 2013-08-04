<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('MESSAGES');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;



$cmf->ENUM_ACTION=array('Отправить','Сохранить в архиве');

$cmf->ENUM_STATE_=array('Отправляется','Отправлен');





if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';












if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
$cmf->execute('delete from MESSAGES where MESSAGES_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('MESSAGES');









$cmf->execute('insert into MESSAGES (MESSAGES_ID,NAME,TEXT,ACTION,SUBSCRIBE_CLIENT_GROUP_ID) values (?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['TEXT']),stripslashes($_REQUEST['ACTION']),stripslashes($_REQUEST['SUBSCRIBE_CLIENT_GROUP_ID'])+0);


$_REQUEST['e']='ED';


      switch($_REQUEST['ACTION'])
      {
         case 0:
         {
            $cmf->execute('update MESSAGES set STATE_= 0 where MESSAGES_ID=?',$_REQUEST['id']);
            break;
         }

         case 1:
         {
            $cmf->execute('update MESSAGES set STATE_= 1 where MESSAGES_ID=?',$_REQUEST['id']);
            break;
         }
      }

      //Админ
      if(!empty($_COOKIE['CMF_UID']))
      {
         $admin_id = $cmf->selectrow_array("select CMF_USER_ID from CMF_USER where MD5_=?", $_COOKIE['CMF_UID']);
         $cmf->execute('update MESSAGES set ADMIN=? where MESSAGES_ID=?',$admin_id,$_REQUEST['id']);
      }
      
      if(!empty($_POST['SUBSCRIBE_CLIENT_GROUP_ID'])){
        $sql="delete from MESSAGES_SHOPUSER
              where MESSAGES_ID = ?";
              
        $cmf->execute($sql,$_REQUEST['id']);
        
        $sql="select USER_ID
              from SHOPUSER_SUBSCRIBE_CLIENT_GROUP
              where SUBSCRIBE_CLIENT_GROUP_ID = ?";
              
        $sth = $cmf->execute($sql, $_REQUEST['SUBSCRIBE_CLIENT_GROUP_ID']);
        
        while($row=mysql_fetch_array($sth, MYSQL_ASSOC)){
          
          $sql="insert into MESSAGES_SHOPUSER
                set MESSAGES_ID = {$_REQUEST['id']},
                    USER_ID = {$row['USER_ID']},
                    WAS_SEND = 0";

          $cmf->execute($sql);
        }
      }
  
}

if($_REQUEST['e'] == 'Изменить')
{









$cmf->execute('update MESSAGES set NAME=?,TEXT=?,ACTION=?,SUBSCRIBE_CLIENT_GROUP_ID=? where MESSAGES_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['TEXT']),stripslashes($_REQUEST['ACTION']),stripslashes($_REQUEST['SUBSCRIBE_CLIENT_GROUP_ID'])+0,$_REQUEST['id']);
$_REQUEST['e']='ED';


      switch($_REQUEST['ACTION'])
      {
         case 0:
         {
            $cmf->execute('update MESSAGES set STATE_= 0 where MESSAGES_ID=?',$_REQUEST['id']);
            break;
         }

         case 1:
         {
            $cmf->execute('update MESSAGES set STATE_= 1 where MESSAGES_ID=?',$_REQUEST['id']);
            break;
         }
      }

      //Админ
      if(!empty($_COOKIE['CMF_UID']))
      {
         $admin_id = $cmf->selectrow_array("select CMF_USER_ID from CMF_USER where MD5_=?", $_COOKIE['CMF_UID']);
         $cmf->execute('update MESSAGES set ADMIN=? where MESSAGES_ID=?',$admin_id,$_REQUEST['id']);
      }
      
      if(!empty($_POST['SUBSCRIBE_CLIENT_GROUP_ID'])){
        $sql="delete from MESSAGES_SHOPUSER
              where MESSAGES_ID = ?";
              
        $cmf->execute($sql,$_REQUEST['id']);
        
        $sql="select USER_ID
              from SHOPUSER_SUBSCRIBE_CLIENT_GROUP
              where SUBSCRIBE_CLIENT_GROUP_ID = ?";
              
        $sth = $cmf->execute($sql, $_REQUEST['SUBSCRIBE_CLIENT_GROUP_ID']);
        
        while($row=mysql_fetch_array($sth, MYSQL_ASSOC)){
          
          $sql="insert into MESSAGES_SHOPUSER
                set MESSAGES_ID = {$_REQUEST['id']},
                    USER_ID = {$row['USER_ID']},
                    WAS_SEND = 0";

          $cmf->execute($sql);
        }
      }
  
};

if($_REQUEST['e'] == 'ED')
{
list($V_MESSAGES_ID,$V_NAME,$V_TEXT,$V_ACTION,$V_SUBSCRIBE_CLIENT_GROUP_ID)=
$cmf->selectrow_arrayQ('select MESSAGES_ID,NAME,TEXT,ACTION,SUBSCRIBE_CLIENT_GROUP_ID from MESSAGES where MESSAGES_ID=?',$_REQUEST['id']);



$V_STR_ACTION=$cmf->Enumerator($cmf->ENUM_ACTION,$V_ACTION);
$V_STR_SUBSCRIBE_CLIENT_GROUP_ID=$cmf->Spravotchnik($V_SUBSCRIBE_CLIENT_GROUP_ID,'select SUBSCRIBE_CLIENT_GROUP_ID,NAME from SUBSCRIBE_CLIENT_GROUP  order by NAME');        
					
@print <<<EOF
<h2 class="h2">Редактирование - Рассылка</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="MESSAGES.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(TEXT);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Тема:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Текст сообщения:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="TEXT" name="TEXT" rows="7" cols="90">
EOF;
$V_TEXT = htmlspecialchars_decode($V_TEXT);
echo $V_TEXT;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'TEXT', {
      customConfig : 'ckeditor/config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><td></td><td /></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Действия:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="ACTION">$V_STR_ACTION</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Группа адресов рассылки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="SUBSCRIBE_CLIENT_GROUP_ID">
				
				
				
				
				<option value="0"></option>
				
				$V_STR_SUBSCRIBE_CLIENT_GROUP_ID
			</select><br />
		
	

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
list($V_MESSAGES_ID,$V_NAME,$V_TEXT,$V_ACTION,$V_STATE_,$V_ADMIN,$V_SUBSCRIBE_CLIENT_GROUP_ID)=array('','','','','','','');

$V_STR_ACTION=$cmf->Enumerator($cmf->ENUM_ACTION,-1);
$V_STR_SUBSCRIBE_CLIENT_GROUP_ID=$cmf->Spravotchnik($V_SUBSCRIBE_CLIENT_GROUP_ID,'select SUBSCRIBE_CLIENT_GROUP_ID,NAME from SUBSCRIBE_CLIENT_GROUP  order by NAME');
					
@print <<<EOF
<h2 class="h2">Добавление - Рассылка</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="MESSAGES.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(TEXT);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Тема:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Текст сообщения:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="TEXT" name="TEXT" rows="7" cols="90">
EOF;
$V_TEXT = htmlspecialchars_decode($V_TEXT);
echo $V_TEXT;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'TEXT', {
      customConfig : 'ckeditor/config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><td></td><td /></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Действия:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="ACTION">$V_STR_ACTION</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Группа адресов рассылки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="SUBSCRIBE_CLIENT_GROUP_ID">
				
				
				
				
				<option value="0"></option>
				
				$V_STR_SUBSCRIBE_CLIENT_GROUP_ID
			</select><br />
		
	

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


print '<h2 class="h2">Рассылка</h2><form action="MESSAGES.php" method="POST">';



$_REQUEST['s']+=0;
$SORTNAMES=array('N','Тема','Состояние','Группа адресов рассылки');
$SORTQUERY=array('order by A.MESSAGES_ID ','order by A.MESSAGES_ID desc ','order by A.NAME ','order by A.NAME desc ','order by A.STATE_ ','order by A.STATE_ desc ','order by A.SUBSCRIBE_CLIENT_GROUP_ID ','order by A.SUBSCRIBE_CLIENT_GROUP_ID desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="MESSAGES.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="MESSAGES.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="MESSAGES.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}



$sth=$cmf->execute('select A.MESSAGES_ID,A.NAME,A.STATE_,A.SUBSCRIBE_CLIENT_GROUP_ID from MESSAGES A where 1'.' '.$SORTQUERY[$_REQUEST['s']]);





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
while(list($V_MESSAGES_ID,$V_NAME,$V_STATE_,$V_SUBSCRIBE_CLIENT_GROUP_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_STATE_=$cmf->ENUM_STATE_[$V_STATE_];
                        
$V_SUBSCRIBE_CLIENT_GROUP_ID=$cmf->selectrow_arrayQ('select NAME from SUBSCRIBE_CLIENT_GROUP where SUBSCRIBE_CLIENT_GROUP_ID=?',$V_SUBSCRIBE_CLIENT_GROUP_ID);
                                        


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_MESSAGES_ID" /></td>
<td>$V_MESSAGES_ID</td><td>$V_NAME</td><td>$V_STATE_</td><td>$V_SUBSCRIBE_CLIENT_GROUP_ID</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="MESSAGES.php?e=ED&amp;id=$V_MESSAGES_ID&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
