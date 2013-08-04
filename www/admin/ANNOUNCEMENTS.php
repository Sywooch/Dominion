<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('ANNOUNCEMENTS');
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

if($_REQUEST['e'] == 'RET')
{

$_REQUEST['pid']=$cmf->selectrow_array('select ITEM_ID from ANNOUNCEMENTS where ANN_ID=? ',$_REQUEST['id']);
}












if(($_REQUEST['e'] == 'Удалить') and is_array($_REQUEST['id']) and ($cmf->D))
{

foreach ($_REQUEST['id'] as $id)
 {

$cmf->execute('delete from ANNOUNCEMENTS where ANN_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{

if(!empty($_REQUEST['ITEM_ID'])) $_REQUEST['pid'] = $_REQUEST['ITEM_ID'];


$_REQUEST['id']=$cmf->GetSequence('ANNOUNCEMENTS');










$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into ANNOUNCEMENTS (ANN_ID,BRAND_ID,ITEM_ID,FIO,TITLE,PRICE,DESCRIPTION,PHONE,EMAIL,POSTED_AT,STATUS) values (?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['FIO']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['PRICE']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['PHONE']),stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['POSTED_AT']),stripslashes($_REQUEST['STATUS']));


$_REQUEST['e'] ='ED';

}

if($_REQUEST['e'] == 'Изменить')
{











$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

if(!empty($_REQUEST['pid'])) $cmf->execute('update ANNOUNCEMENTS set BRAND_ID=?,ITEM_ID=?,FIO=?,TITLE=?,PRICE=?,DESCRIPTION=?,PHONE=?,EMAIL=?,POSTED_AT=?,STATUS=? where ANN_ID=?',stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['FIO']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['PRICE']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['PHONE']),stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['POSTED_AT']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
else $cmf->execute('update ANNOUNCEMENTS set BRAND_ID=?,FIO=?,TITLE=?,PRICE=?,DESCRIPTION=?,PHONE=?,EMAIL=?,POSTED_AT=?,STATUS=? where ANN_ID=?',stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['FIO']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['PRICE']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['PHONE']),stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['POSTED_AT']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);

$_REQUEST['e'] ='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_ANN_ID,$V_BRAND_ID,$V_ITEM_ID,$V_FIO,$V_TITLE,$V_PRICE,$V_DESCRIPTION,$V_PHONE,$V_EMAIL,$V_POSTED_AT,$V_STATUS)=$cmf->selectrow_arrayQ('select ANN_ID,BRAND_ID,ITEM_ID,FIO,TITLE,PRICE,DESCRIPTION,PHONE,EMAIL,DATE_FORMAT(POSTED_AT,"%Y-%m-%d %H:%i"),STATUS from ANNOUNCEMENTS where ANN_ID=?',$_REQUEST['id']);


if(!empty($_REQUEST['ITEM_ID'])) $_REQUEST['pid'] = $_REQUEST['ITEM_ID'];


$V_STR_BRAND_ID=$cmf->Spravotchnik($V_BRAND_ID,'select A.BRAND_ID,A.NAME from BRAND A   INNER JOIN ITEM I on I.BRAND_ID=A.BRAND_ID  where 1  and A.STATUS=1 and I.STATUS=1 group by A.BRAND_ID order by A.NAME');
							
$V_STR_ITEM_ID=$cmf->Spravotchnik($V_ITEM_ID,'select A.ITEM_ID,CONCAT_WS(0xA0,B.TYPENAME,A.NAME) from ITEM A   left join CATALOGUE B on (A.CATALOGUE_ID=B.CATALOGUE_ID) where 1  and A.STATUS=1   and A.BRAND_ID='.$V_BRAND_ID.'  order by CONCAT_WS(0xA0,B.TYPENAME,A.NAME)');
							
$V_STATUS=$V_STATUS?'checked':'';
print @<<<EOF
<h2 class="h2">Редактирование - Объявления</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="ANNOUNCEMENTS.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(FIO) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(PRICE) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(PHONE) &amp;&amp; checkXML(EMAIL);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
EOF;





#список от child-таблицы


#список от child-обычной таблицы


$VV_BRAND_ID=$cmf->Spravotchnik($V_BRAND_ID,'select A.BRAND_ID,A.NAME from BRAND A  INNER JOIN ITEM I on I.BRAND_ID=A.BRAND_ID  where 1  and A.STATUS=1 and I.STATUS=1 group by A.BRAND_ID');

@print <<<EOF
<tr><td class="tbl_t2"><span class="title2">Объявления</span></td><td class="tbl_e2" width="100%">
<table><tr><td>Производители: <select name="BRAND_ID" onchange="return chan(this.form,this.form.elements['ITEM_ID'],'select A.ITEM_ID,CONCAT_WS(0xA0,B.TYPENAME,A.NAME) from ITEM A  left join CATALOGUE B on (A.CATALOGUE_ID=B.CATALOGUE_ID) where A.BRAND_ID=?  and A.STATUS=1  order by A.NAME',this.value);"><option value="">-- Не задан --</option>$VV_BRAND_ID</select>�
Фильтр: <input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['ITEM_ID'],'select A.ITEM_ID,CONCAT_WS(0xA0,B.TYPENAME,A.NAME) from ITEM A  left join CATALOGUE B on (A.CATALOGUE_ID=B.CATALOGUE_ID) where A.BRAND_ID=? and A.NAME like ? and A.STATUS=1  order by A.NAME',BRAND_ID.value+'\|%25'+this.value+'%25');" /></td></tr>
<tr><td><select name="ITEM_ID" style="width:100%" size="8">$V_STR_ITEM_ID</select></td></tr></table></td></tr>
EOF;




@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>ФИО:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="FIO" value="$V_FIO" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Заголовок:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TITLE" value="$V_TITLE" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цена:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PRICE" value="$V_PRICE" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="DESCRIPTION" name="DESCRIPTION" rows="7" cols="90">
EOF;
$V_DESCRIPTION = htmlspecialchars_decode($V_DESCRIPTION);
echo $V_DESCRIPTION;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'DESCRIPTION', {
      customConfig : 'ckeditor/config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Телефон:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PHONE" value="$V_PHONE" size="" onfocus="_XDOC=this;" onkeydown="_etaKey(event)" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>E-mail:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="" onfocus="_XDOC=this;" onkeydown="_etaKey(event)" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата добавления:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="POSTED_AT" name="POSTED_AT" value="$V_POSTED_AT" />
EOF;

if($V_POSTED_AT) $V_DAT_ = substr($V_POSTED_AT,8,2).".".substr($V_POSTED_AT,5,2).".".substr($V_POSTED_AT,0,4)." ".substr($V_POSTED_AT,11,2).":".substr($V_POSTED_AT,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_POSTED_AT">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_POSTED_AT" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "POSTED_AT",
                       displayArea    :    "DATE_POSTED_AT",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_POSTED_AT",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>

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
list($V_ANN_ID,$V_BRAND_ID,$V_ITEM_ID,$V_FIO,$V_TITLE,$V_PRICE,$V_DESCRIPTION,$V_PHONE,$V_EMAIL,$V_POSTED_AT,$V_STATUS)=array('','','','','','','','','','','');


$V_STR_ITEM_ID=$cmf->Spravotchnik($V_ITEM_ID,'select A.ITEM_ID,CONCAT_WS(0xA0,B.TYPENAME,A.NAME) from ITEM A   left join CATALOGUE B on (A.CATALOGUE_ID=B.CATALOGUE_ID) order by CONCAT_WS(0xA0,B.TYPENAME,A.NAME)');
					
$V_POSTED_AT=$cmf->selectrow_array('select now()');
$V_STATUS='';
@print <<<EOF
<h2 class="h2">Добавление - Объявления</h2>
<a href="javascript:history.go(-1)">&#160;<b>вернуться</b></a><p />
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="ANNOUNCEMENTS.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(FIO) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(PRICE) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(PHONE) &amp;&amp; checkXML(EMAIL);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
EOF;




#список от child-таблицы


#список от child-обычной таблицы


$VV_BRAND_ID=$cmf->Spravotchnik('','select A.BRAND_ID,A.NAME from BRAND A  INNER JOIN ITEM I on I.BRAND_ID=A.BRAND_ID  where 1  and A.STATUS=1 and I.STATUS=1 group by A.BRAND_ID');

@print <<<EOF
<tr><td class="tbl_t2"><span class="title2">Объявления</span></td><td class="tbl_e2" width="100%">
<table><tr><td>Производители: <select name="BRAND_ID" onchange="return chan(this.form,this.form.elements['ITEM_ID'],'select A.ITEM_ID,CONCAT_WS(0xA0,B.TYPENAME,A.NAME) from ITEM A  left join CATALOGUE B on (A.CATALOGUE_ID=B.CATALOGUE_ID) where A.BRAND_ID=?  and A.STATUS=1  order by A.NAME',this.value);"><option value="">-- Не задан --</option>$VV_BRAND_ID</select>�
Фильтр: <input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['ITEM_ID'],'select A.ITEM_ID,CONCAT_WS(0xA0,B.TYPENAME,A.NAME) from ITEM A  left join CATALOGUE B on (A.CATALOGUE_ID=B.CATALOGUE_ID) where A.BRAND_ID=? and A.NAME like ? and A.STATUS=1  order by A.NAME',BRAND_ID.value+'\|%25'+this.value+'%25');" /></td></tr>
<tr><td><select name="ITEM_ID" style="width:100%" size="8"></select></td></tr></table></td></tr>
EOF;




@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>ФИО:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="FIO" value="$V_FIO" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Заголовок:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TITLE" value="$V_TITLE" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цена:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PRICE" value="$V_PRICE" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="DESCRIPTION" name="DESCRIPTION" rows="7" cols="90">
EOF;
$V_DESCRIPTION = htmlspecialchars_decode($V_DESCRIPTION);
echo $V_DESCRIPTION;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'DESCRIPTION', {
      customConfig : 'ckeditor/config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Телефон:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PHONE" value="$V_PHONE" size="" onfocus="_XDOC=this;" onkeydown="_etaKey(event)" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>E-mail:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="" onfocus="_XDOC=this;" onkeydown="_etaKey(event)" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата добавления:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="POSTED_AT" name="POSTED_AT" value="$V_POSTED_AT" />
EOF;

if($V_POSTED_AT) $V_DAT_ = substr($V_POSTED_AT,8,2).".".substr($V_POSTED_AT,5,2).".".substr($V_POSTED_AT,0,4)." ".substr($V_POSTED_AT,11,2).":".substr($V_POSTED_AT,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_POSTED_AT">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_POSTED_AT" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "POSTED_AT",
                       displayArea    :    "DATE_POSTED_AT",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_POSTED_AT",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>

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

if(!empty($_REQUEST['pid']) and $_REQUEST['pid']!='all') $V_PARENTSCRIPTNAME=$cmf->selectrow_array('select CONCAT_WS(0xA0,B.TYPENAME,A.NAME) from ITEM where ITEM_ID=?',$_REQUEST['pid']);
else $V_PARENTSCRIPTNAME='';

print <<<EOF
<h2 class="h2">$V_PARENTSCRIPTNAME / Объявления</h2><form action="ANNOUNCEMENTS.php" method="POST">

EOF;
if(!empty($_REQUEST['pid']))
{
   ?><a href="ITEM.php?e=RET&amp;id=<?=$_REQUEST['pid']?>"><img src="i/back.gif" border="0" align="top" /> Назад</a><br /><?
}
print <<<EOF

EOF;




$pagesize=35;

if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}

if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{
if(!empty($_REQUEST['pid']) and $_REQUEST['pid']=='all')
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ANNOUNCEMENTS A where A.ITEM_ID > 0',$_REQUEST['pid']);
}
else
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ANNOUNCEMENTS A where A.ITEM_ID=?',$_REQUEST['pid']);

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
- <a class="t" href="ANNOUNCEMENTS.php?pid={$_REQUEST['pid']}&amp;count={$_REQUEST['count']}&amp;p={$i}&amp;pcount={$_REQUEST['pcount']}{$filters}">$i</a>
EOF;
  }
 }
print <<<EOF
&#160;из <span class="red">({$_REQUEST['pcount']})</span><br />
EOF;
}

if(!empty($_REQUEST['pid']) and $_REQUEST['pid'] == 'all')
{
$sth=$cmf->execute('select A.ANN_ID,A.BRAND_ID,A.ITEM_ID,A.FIO,A.TITLE,A.PRICE,A.DESCRIPTION,A.PHONE,A.EMAIL,DATE_FORMAT(A.POSTED_AT,"%Y-%m-%d %H:%i"),A.STATUS from ANNOUNCEMENTS A
where A.ITEM_ID > 0  order by A.POSTED_AT desc limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);
}
else
{
$sth=$cmf->execute('select A.ANN_ID,A.BRAND_ID,A.ITEM_ID,A.FIO,A.TITLE,A.PRICE,A.DESCRIPTION,A.PHONE,A.EMAIL,DATE_FORMAT(A.POSTED_AT,"%Y-%m-%d %H:%i"),A.STATUS from ANNOUNCEMENTS A
where A.ITEM_ID=?  order by A.POSTED_AT desc limit ?,?',$_REQUEST['pid'],$pagesize*($_REQUEST['p']-1),$pagesize);

}





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="12">
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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Производитель</th><th>Товар</th><th>ФИО</th><th>Заголовок</th><th>Цена</th><th>Описание</th><th>Телефон</th><th>E-mail</th><th>Дата добавления</th><td></td></tr>
EOF;


if($sth)
while(list($V_ANN_ID,$V_BRAND_ID,$V_ITEM_ID,$V_FIO,$V_TITLE,$V_PRICE,$V_DESCRIPTION,$V_PHONE,$V_EMAIL,$V_POSTED_AT,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{



$V_BRAND_ID=$cmf->selectrow_arrayQ('select A.NAME from BRAND A  INNER JOIN ITEM I on I.BRAND_ID=A.BRAND_ID  where A.BRAND_ID=?',$V_BRAND_ID);
							
$V_ITEM_ID=$cmf->selectrow_arrayQ('select CONCAT_WS(0xA0,B.TYPENAME,A.NAME) from ITEM A  left join CATALOGUE B on (A.CATALOGUE_ID=B.CATALOGUE_ID) where A.ITEM_ID=?',$V_ITEM_ID);
							

if($V_STATUS == 1){$V_COLOR='#FFFFFF';} else {$V_COLOR='#a0a0a0';}



@print <<<EOF
<tr bgcolor="$V_COLOR">
<td><input type="checkbox" name="id[]" value="$V_ANN_ID" /></td>
<td>$V_ANN_ID</td><td>$V_BRAND_ID</td><td>$V_ITEM_ID</td><td>$V_FIO</td><td>$V_TITLE</td><td>$V_PRICE</td><td>$V_DESCRIPTION</td><td>$V_PHONE</td><td>$V_EMAIL</td><td>$V_POSTED_AT</td><td nowrap="">
EOF;

if ($cmf->W)
@print <<<EOF
<a href="ANNOUNCEMENTS.php?e=ED&amp;id=$V_ANN_ID&amp;pid={$_REQUEST['pid']}&amp;p={$_REQUEST['p']}{$filters}"><img src="i/ed.gif" border="0" title="Изменить" /></a>

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
$sth=$cmf->execute('select ITEM_ID,NAME from ITEM  order by NAME');
while(list($V_ITEM_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.='<li>'.($id==$V_ITEM_ID?'<input type="radio" name="cid" value="'.$V_ITEM_ID.'" disabled="yes" />':'<input type="radio" name="cid" value="'.$V_ITEM_ID.'" />')."&#160;$V_NAME</li>";
}
if ($ret) {$ret="<ul>${ret}</ul>";}
return $ret;
}

?>
