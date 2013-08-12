<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('GOODS_GROUP');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;
$VIRTUAL_IMAGE_PATH="/it_link/";






if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';








if($cmf->Param('el1') == 'Удалить')
{
foreach ($_REQUEST['iid'] as $id)
 {


$cmf->execute('delete from GOODS_GROUP_ITEM_LINK where GOODS_GROUP_ID=? and ITEM_ID=?',$_REQUEST['id'],$id);


 }

$_REQUEST['e']='ED';
}






if($cmf->Param('el1') == 'Изменить')
{






		
				
    if(isset($_FILES['NOT_IMAGE']['tmp_name']) && $_FILES['NOT_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE']) && $_REQUEST['CLR_IMAGE']){$_REQUEST['IMAGE']=$cmf->UnlinkFile($_REQUEST['IMAGE'],$VIRTUAL_IMAGE_PATH);}
	
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;



$cmf->execute('update GOODS_GROUP_ITEM_LINK set CATALOGUE_ID=?,ITEM_ID=?,PARENT_ID=?,IMAGE=?,STATUS=? where GOODS_GROUP_ID=? and ITEM_ID=?',0,stripslashes($_REQUEST['ITEM_ID'])+0,0,stripslashes($_REQUEST['IMAGE']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id'],$_REQUEST['iid']);


        $parents = getParents($_REQUEST['CATALOGUE_ID']);
        $pid = 0; 
        if($parents) $pid = $parents[sizeof($parents)-1];
        $cmf->execute('update GOODS_GROUP_ITEM_LINK set PARENT_ID=?, CATALOGUE_ID=? where GOODS_GROUP_ID=? and ITEM_ID=?',$pid,$_REQUEST['CATALOGUE_ID'],$_REQUEST['id'],$_REQUEST['ITEM_ID']);

        
$_REQUEST['e']='ED';
};



if($cmf->Param('el1') == 'Добавить')
{


foreach ($_REQUEST['ITEM_ID'] as $id)
{






		
				
    if(isset($_FILES['NOT_IMAGE']['tmp_name']) && $_FILES['NOT_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE']) && $_REQUEST['CLR_IMAGE']){$_REQUEST['IMAGE']=$cmf->UnlinkFile($_REQUEST['IMAGE'],$VIRTUAL_IMAGE_PATH);}
	
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('insert into GOODS_GROUP_ITEM_LINK (GOODS_GROUP_ID,ITEM_ID,CATALOGUE_ID,PARENT_ID,IMAGE,STATUS) values (?,?,?,?,?,?)',$_REQUEST['id'],$id,0,0,stripslashes($_REQUEST['IMAGE']),stripslashes($_REQUEST['STATUS']));
//$_REQUEST['iid']=$_REQUEST['ITEM_ID'];

        $parents = getParents($_REQUEST['CATALOGUE_ID']);
        $pid = 0; 
        if($parents) $pid = $parents[sizeof($parents)-1];
        $cmf->execute('update GOODS_GROUP_ITEM_LINK set PARENT_ID=?, CATALOGUE_ID=? where GOODS_GROUP_ID=? and ITEM_ID=?',$pid,$_REQUEST['CATALOGUE_ID'],$_REQUEST['id'],$id);

        
} ///2

$_REQUEST['e']='ED';
$visible=0;
}


if($cmf->Param('eventl1') == 'ED')
{
list ($V_GOODS_GROUP_ID,$V_CATALOGUE_ID,$V_ITEM_ID,$V_PARENT_ID,$V_IMAGE,$V_STATUS)=$cmf->selectrow_arrayQ('select GOODS_GROUP_ID,CATALOGUE_ID,ITEM_ID,PARENT_ID,IMAGE,STATUS from GOODS_GROUP_ITEM_LINK where GOODS_GROUP_ID=? and ITEM_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_GOODS_GROUP_ID=$cmf->Spravotchnik($V_GOODS_GROUP_ID,'select GOODS_GROUP_ID,NAME from GOODS_GROUP  order by NAME');        
					
$V_STR_CATALOGUE_ID=$cmf->Spravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE   order by NAME');        
					
$V_STR_ITEM_ID=$cmf->Spravotchnik($V_ITEM_ID,'select A.ITEM_ID,CONCAT(\' \',A.TYPENAME,B.NAME,A.NAME) from ITEM A   left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where 1  and A.STATUS=1  and A.CATALOGUE_ID='.$V_CATALOGUE_ID.'  order by CONCAT(\' \',A.TYPENAME,B.NAME,A.NAME)');
							
$V_STR_PARENT_ID=$cmf->Spravotchnik($V_PARENT_ID,'select CATALOGUE_ID,NAME from CATALOGUE  where PARENT_ID=0 and STATUS=1 order by NAME');        
					
if(isset($V_IMAGE))
{
   $IM_IMAGE=split('#',$V_IMAGE);
   if(isset($IM_5[1]) && $IM_IMAGE[1] > 150){$IM_IMAGE[2]=$IM_IMAGE[2]*150/$IM_IMAGE[1]; $IM_IMAGE[1]=150;}
}

$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
        <h2 class="h2">Редактирование - Связь "Группа - Товар"</h2>

<form method="POST" action="GOODS_GROUP.php#fl1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<br />
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<tr bgcolor="#F0F0F0" class="ftr">
        <td colspan="2">
        <input type="submit" name="el1" value="Изменить" class="gbt bsave" />
        <input type="submit" name="el1" value="Отменить" class="gbt bcancel" />
        </td>
</tr>
EOF;



        #список от child-таблицы
        
        
        #список от child-древовидной таблицы
        
        
        $VV_CATALOGUE_ID=$cmf->TreeSpravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE where PARENT_ID=?  and COUNT_ > 0',0);
@print <<<EOF
<tr bgcolor="#FFFFFF"><td><span class="title2">Группы товаров</span></td><td width="100%">
<table><tr><td>Каталог: <select name="CATALOGUE_ID" onchange="return chan(this.form,this.form.elements['ITEM_ID'],'select A.ITEM_ID,CONCAT(\' \',A.TYPENAME,B.NAME,A.NAME) from ITEM A  left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.CATALOGUE_ID=?  and A.STATUS=1  order by A.NAME',this.value);"><option value="">-- Не задан --</option>{$VV_CATALOGUE_ID}</select>
</td></tr>




<tr><td /></tr></table></td></tr>
EOF;
        
        

@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>товар:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="ITEM_ID">
				
				
				
				
				
				
				$V_STR_ITEM_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Альтернативная картинка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE" value="$V_IMAGE" />
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
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>



<tr bgcolor="#F0F0F0" class="ftr">
        <td colspan="2">
        <input type="submit" name="el1" value="Изменить" class="gbt bsave" />
        <input type="submit" name="el1" value="Отменить" class="gbt bcancel" />
        </td>
</tr>
</table>
</form>
EOF;
$_REQUEST['e']='';
$visible=0;


}

if($cmf->Param('el1') == 'Новый')
{
list($V_GOODS_GROUP_ID,$V_CATALOGUE_ID,$V_ITEM_ID,$V_PARENT_ID,$V_IMAGE,$V_STATUS)=array('','','','','','');


$V_STR_GOODS_GROUP_ID=$cmf->Spravotchnik($V_GOODS_GROUP_ID,'select GOODS_GROUP_ID,NAME from GOODS_GROUP  order by NAME');
					
$V_STR_CATALOGUE_ID=$cmf->Spravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE   order by NAME');
					
$V_STR_ITEM_ID=$cmf->Spravotchnik($V_ITEM_ID,'select A.ITEM_ID,CONCAT(\' \',A.TYPENAME,B.NAME,A.NAME) from ITEM A   left join BRAND B on (A.BRAND_ID=B.BRAND_ID) order by CONCAT(\' \',A.TYPENAME,B.NAME,A.NAME)');
					
$V_STR_PARENT_ID=$cmf->Spravotchnik($V_PARENT_ID,'select CATALOGUE_ID,NAME from CATALOGUE  where PARENT_ID=0 and STATUS=1 order by NAME');
					
$IM_IMAGE=array('','','');
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Связь "Группа - Товар"</h2>
<form method="POST" action="GOODS_GROUP.php#fl1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
        <tr bgcolor="#F0F0F0" class="ftr">
            <td colspan="2">
            <input type="submit" name="el1" value="Добавить" class="gbt badd" /><input type="submit" name="el1" value="Отменить" class="gbt bcancel" />
                </td>
        </tr>

<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;



        #список от child-таблицы
        

        
        #список от child-древовидной таблицы
        
        
        $VV_CATALOGUE_ID=$cmf->TreeSpravotchnik('','select CATALOGUE_ID,NAME from CATALOGUE   where PARENT_ID=?  and COUNT_ > 0',0);
@print <<<EOF
<tr bgcolor="#FFFFFF"><td><span class="title2">Группы товаров</span></td><td width="100%">
<table><tr><td>Каталог: <select name="CATALOGUE_ID" onchange="return chan(this.form,this.form.elements['ITEM_ID[]'],'select A.ITEM_ID,CONCAT(\' \',A.TYPENAME,B.NAME,A.NAME) from ITEM A  left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.CATALOGUE_ID=?  and A.STATUS=1  order by A.NAME',this.value);"><option value="">-- Не задан --</option>{$VV_CATALOGUE_ID}</select>
<br />Фильтр: <input type="text" name="q" onkeyup="return chan(this.form,this.form.elements['ITEM_ID[]'],'select A.ITEM_ID,A.NAME from ITEM A  left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.CATALOGUE_ID=? and A.NAME like ? order by A.NAME',CATALOGUE_ID.value+'\|%25'+this.value+'%25');" /></td></tr>
<tr><td><select name="ITEM_ID[]" multiple="" style="width:100%" size="8"></select></td></tr></table></td></tr>
EOF;
        
        
@print <<<EOF
<tr bgcolor="#FFFFFF"><th width="1%"><b>Альтернативная картинка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE" value="$V_IMAGE" />
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
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
<tr><td class="tbl_t2" colspan="2"></td></tr>

        <tr bgcolor="#F0F0F0" class="ftr">
                <td colspan="2">
                <input type="submit" name="el1" value="Добавить" class="gbt badd" /><input type="submit" name="el1" value="Отменить" class="gbt bcancel" />
                </td>
        </tr>
</table>
</form>
EOF;
$visible=0;
}








if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
list($ORDERING)=$cmf->selectrow_array('select ORDERING from GOODS_GROUP where GOODS_GROUP_ID=?',$id);
$cmf->execute('update GOODS_GROUP set ORDERING=ORDERING-1 where ORDERING>?',$ORDERING);
$cmf->execute('delete from GOODS_GROUP where GOODS_GROUP_ID=?',$id);

 }

}


if($_REQUEST['e'] == 'UP')
{
list($ORDERING)=$cmf->selectrow_array('select ORDERING from GOODS_GROUP where GOODS_GROUP_ID=?',$_REQUEST['id']);
if($ORDERING>1)
{
$cmf->execute('update GOODS_GROUP set ORDERING=ORDERING+1 where ORDERING=?',$ORDERING-1);
$cmf->execute('update GOODS_GROUP set ORDERING=ORDERING-1 where GOODS_GROUP_ID=?',$_REQUEST['id']);
}
}

if($_REQUEST['e'] == 'DN')
{
list($ORDERING)=$cmf->selectrow_array('select ORDERING from GOODS_GROUP where GOODS_GROUP_ID=?',$_REQUEST['id']);
$MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from GOODS_GROUP');
if($ORDERING<$MAXORDERING)
{
$cmf->execute('update GOODS_GROUP set ORDERING=ORDERING-1 where ORDERING=?',$ORDERING+1);
$cmf->execute('update GOODS_GROUP set ORDERING=ORDERING+1 where GOODS_GROUP_ID=?',$_REQUEST['id']);
}
}


if($_REQUEST['e'] == 'Добавить')
{


$_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from GOODS_GROUP');
$_REQUEST['ORDERING']++;


$_REQUEST['id']=$cmf->GetSequence('GOODS_GROUP');



$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$_REQUEST['IN_FRONT']=isset($_REQUEST['IN_FRONT']) && $_REQUEST['IN_FRONT']?1:0;
$_REQUEST['IN_CATALOG']=isset($_REQUEST['IN_CATALOG']) && $_REQUEST['IN_CATALOG']?1:0;



$cmf->execute('insert into GOODS_GROUP (GOODS_GROUP_ID,NAME,TITLE,STATUS,IMPORT_IDENT,IMPORT_IDENT_XML,IN_FRONT,IN_CATALOG,ORDERING) values (?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['IMPORT_IDENT']),stripslashes($_REQUEST['IMPORT_IDENT_XML']),stripslashes($_REQUEST['IN_FRONT']),stripslashes($_REQUEST['IN_CATALOG']),stripslashes($_REQUEST['ORDERING']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{




$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$_REQUEST['IN_FRONT']=isset($_REQUEST['IN_FRONT']) && $_REQUEST['IN_FRONT']?1:0;
$_REQUEST['IN_CATALOG']=isset($_REQUEST['IN_CATALOG']) && $_REQUEST['IN_CATALOG']?1:0;


$cmf->execute('update GOODS_GROUP set NAME=?,TITLE=?,STATUS=?,IMPORT_IDENT=?,IMPORT_IDENT_XML=?,IN_FRONT=?,IN_CATALOG=? where GOODS_GROUP_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['IMPORT_IDENT']),stripslashes($_REQUEST['IMPORT_IDENT_XML']),stripslashes($_REQUEST['IN_FRONT']),stripslashes($_REQUEST['IN_CATALOG']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_GOODS_GROUP_ID,$V_NAME,$V_TITLE,$V_STATUS,$V_IMPORT_IDENT,$V_IMPORT_IDENT_XML,$V_IN_FRONT,$V_IN_CATALOG)=
$cmf->selectrow_arrayQ('select GOODS_GROUP_ID,NAME,TITLE,STATUS,IMPORT_IDENT,IMPORT_IDENT_XML,IN_FRONT,IN_CATALOG from GOODS_GROUP where GOODS_GROUP_ID=?',$_REQUEST['id']);



$V_STATUS=$V_STATUS?'checked':'';
$V_IN_FRONT=$V_IN_FRONT?'checked':'';
$V_IN_CATALOG=$V_IN_CATALOG?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Группы товаров</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="GOODS_GROUP.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(IMPORT_IDENT) &amp;&amp; checkXML(IMPORT_IDENT_XML);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тайтл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TITLE" value="$V_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Идентификатор:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="IMPORT_IDENT" value="$V_IMPORT_IDENT" size="10" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Идентификатор в XML:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="IMPORT_IDENT_XML" value="$V_IMPORT_IDENT_XML" size="10" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Отображать на главной:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_FRONT' value='1' $V_IN_FRONT/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Отображать в каталоге:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_CATALOG' value='1' $V_IN_CATALOG/><br /></td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;








@print <<<EOF
        <h3 class="h3"><a name="fl1"></a>Связь "Группа - Товар"</h3>
        <table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
        <form action="GOODS_GROUP.php#fl1" method="POST">
        <tr bgcolor="#FFFFFF">
                <td colspan="3" class="main_tbl_title">
                <input type="submit" name="el1" value="Новый" class="gbt bnew" /><img src="i/0.gif" width="4" height="1" />
                <input type="submit" onclick="return dl();" name="el1" value="Удалить" class="gbt bdel" />
                <input type="hidden" name="id" value="{$_REQUEST['id']}" />
                
                <input type="hidden" name="p" value="{$_REQUEST['p']}" />
                
                </td>
        </tr>
EOF;



$sth=$cmf->execute('select ITEM_ID,STATUS from GOODS_GROUP_ITEM_LINK where GOODS_GROUP_ID=? ',$_REQUEST['id']);
?><tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'iid[]');" /></td><th>товар</th><td></td></tr><?
while(list($V_ITEM_ID,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_ITEM_ID_STR=$cmf->selectrow_arrayQ('select CONCAT(\' \',A.TYPENAME,B.NAME,A.NAME) from ITEM A  left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.ITEM_ID=?',$V_ITEM_ID);
        
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}
@print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="iid[]" value="{$V_ITEM_ID}" /></td>
<td><a href="ITEM.php?e=ED&amp;id=$V_ITEM_ID" target="_blank">$V_ITEM_ID_STR</a></td><td>

<a href="GOODS_GROUP.php?eventl1=ED&amp;iid=$V_ITEM_ID&amp;id={$_REQUEST['id']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>

EOF;
}

print '</form></table>';


$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_GOODS_GROUP_ID,$V_NAME,$V_TITLE,$V_STATUS,$V_IMPORT_IDENT,$V_IMPORT_IDENT_XML,$V_IN_FRONT,$V_IN_CATALOG,$V_ORDERING)=array('','','','','','','','','');

$V_STATUS='checked';
$V_IN_FRONT='';
$V_IN_CATALOG='';
@print <<<EOF
<h2 class="h2">Добавление - Группы товаров</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="GOODS_GROUP.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(IMPORT_IDENT) &amp;&amp; checkXML(IMPORT_IDENT_XML);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Название:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тайтл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TITLE" value="$V_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Идентификатор:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="IMPORT_IDENT" value="$V_IMPORT_IDENT" size="10" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Идентификатор в XML:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="IMPORT_IDENT_XML" value="$V_IMPORT_IDENT_XML" size="10" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Отображать на главной:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_FRONT' value='1' $V_IN_FRONT/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Отображать в каталоге:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_CATALOG' value='1' $V_IN_CATALOG/><br /></td></tr>

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


print '<h2 class="h2">Группы товаров</h2><form action="GOODS_GROUP.php" method="POST">';




$sth=$cmf->execute('select A.GOODS_GROUP_ID,A.NAME,A.TITLE,A.STATUS,A.IMPORT_IDENT,A.IMPORT_IDENT_XML,A.IN_FRONT,A.IN_CATALOG from GOODS_GROUP A where 1'.' order by A.ORDERING ');





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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Название</th><th>Тайтл</th><th>Идентификатор</th><th>Идентификатор в XML</th><td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_GOODS_GROUP_ID,$V_NAME,$V_TITLE,$V_STATUS,$V_IMPORT_IDENT,$V_IMPORT_IDENT_XML,$V_IN_FRONT,$V_IN_CATALOG)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="id[]" value="$V_GOODS_GROUP_ID" /></td>
<td>$V_GOODS_GROUP_ID</td><td>$V_NAME</td><td>$V_TITLE</td><td>$V_IMPORT_IDENT</td><td>$V_IMPORT_IDENT_XML</td><td nowrap="">
<a href="GOODS_GROUP.php?e=UP&amp;id=$V_GOODS_GROUP_ID"><img src="i/up.gif" border="0" /></a>
<a href="GOODS_GROUP.php?e=DN&amp;id=$V_GOODS_GROUP_ID"><img src="i/dn.gif" border="0" /></a>
EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="GOODS_GROUP.php?e=ED&amp;id=$V_GOODS_GROUP_ID"><img src="i/ed.gif" border="0" title="Изменить" /></a>


</td></tr>
EOF;
}
}
 
print '</table>';
}
print '</form>';
$cmf->MakeCommonFooter();
$cmf->Close();

         function getParents($id)
         {
          global $cmf;
          $path = array();
          $sth=$cmf->execute('select PARENT_ID,NAME,CATALOGUE_ID from CATALOGUE where CATALOGUE_ID='.$id.' and STATUS=1 order by NAME');
          if(mysql_num_rows($sth))
          {
             while(list($V_ID,$V_VAL,$V_CID)=mysql_fetch_array($sth))
             {
                if($V_ID>0)
                {
                   $path[] = $V_ID;
                   $path = array_merge($path,getParents($V_ID));
                }
             }
          }
          return $path;
         }
        
?>
