<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('CATALOGUE');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();
$visible=1;
$VIRTUAL_IMAGE_PATH="/it/";






if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';

if($_REQUEST['e'] == 'RET')
{

$_REQUEST['pid']=$cmf->selectrow_array('select CATALOGUE_ID from ITEM where ITEM_ID=? ',$_REQUEST['id']);
}




if($_REQUEST['e'] == 'Диапазоны')
{
$cmf->UpdateAllRanges();
}

if($_REQUEST['e']=='Переместить')
{
?><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="f">
<form action="ITEM.php" method="POST">
<input type="hidden" name="pid" value="<?=$_REQUEST['pid']?>">
<input type="hidden" name="p" value="<?=$_REQUEST['p']?>">
<?
if(isset($_REQUEST['id']))
foreach ($_REQUEST['id'] as $vid){?><input type="hidden" name="id[]" value="<?=$vid?>"/><? } 
?>
<tr bgcolor="#F0F0F0" class="ftr"><td><input type="submit" name="e" value="Перенести" class="gbt bmv"/><input type="submit" name="event" value="Отменить" class="gbt bcancel"/></td></tr>
<tr bgcolor="#FFFFFF"><td><?
print GetTree($cmf,0);
?></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td><input type="submit" name="e" value="Перенести" class="gbt bmv"/><input type="submit" name="event" value="Отменить" class="gbt bcancel"/></td></tr>
</form></table><?
$visible=0;
}

if($_REQUEST['e']=='Переместить все')
{
?><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="f">
<form action="ITEM.php" method="POST">
<input type="hidden" name="pid" value="<?=$_REQUEST['pid']?>">
<input type="hidden" name="p" value="<?=$_REQUEST['p']?>">
<?
$children = getChildren($cmf,$_REQUEST['pid']);
if($children) $where = " and CATALOGUE_ID IN (".implode(',',$children).")";
else $where = " and CATALOGUE_ID = '".$_REQUEST['pid']."'";

$items = getItems($cmf,$where);
if($items)
{
   foreach ($items as $vid){?><input type="hidden" name="id[]" value="<?=$vid?>"/><? }
}
?>
<tr bgcolor="#F0F0F0" class="ftr"><td><input type="submit" name="e" value="Перенести" class="gbt bmv"/><input type="submit" name="event" value="Отменить" class="gbt bcancel"/></td></tr>
<tr bgcolor="#FFFFFF"><td><?
print GetTree($cmf,0);
?></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td><input type="submit" name="e" value="Перенести" class="gbt bmv"/><input type="submit" name="event" value="Отменить" class="gbt bcancel"/></td></tr>
</form></table><?
$visible=0;
}

if($_REQUEST['e'] == 'Скопировать')
{
if(isset($_REQUEST['id']))
foreach ($_REQUEST['id'] as $vid)
 {
        list($V_CATALOGUE_ID,$V_TYPENAME,$V_BRAND_ID,$V_NAME,$V_CURRENCY_ID,$V_ARTICLE,$V_IMAGE1,$V_IMAGE2,$V_IMAGE3,$V_DESCRIPTION,$V_PRICE,$V_PRICE1,$V_STATUS)=$cmf->selectrow_array('select CATALOGUE_ID,TYPENAME,BRAND_ID,NAME,CURRENCY_ID,ARTICLE,IMAGE1,IMAGE2,IMAGE3,DESCRIPTION,PRICE,PRICE1,STATUS from ITEM where ITEM_ID=?',$vid);
        //$V_ITEM_ID=$cmf->GetSequence('ITEM');
        $V_ITEM_ID=$cmf->selectrow_array('select MAX(ITEM_ID) from ITEM');
        $V_ITEM_ID=$V_ITEM_ID+1;

        if(!empty($V_IMAGE1) && strchr($V_IMAGE1,"#"))
        {
        list($SRC,$W,$H)=split('#',$V_IMAGE1);
        $FORMAT='';
        list($none,$FORMAT)=split('\.',$SRC);
        $cmf->FileCopy('/images/it/'.$SRC,"/images/it/$V_ITEM_ID.$FORMAT");
        $V_IMAGE1="$V_ITEM_ID.$FORMAT#$W#$H";
        }

        if(!empty($V_IMAGE2) && strchr($V_IMAGE2,"#"))
        {
        list($SRC,$W,$H)=split('#',$V_IMAGE2);
        list($none,$FORMAT)=split('\.',$SRC);
        $cmf->FileCopy('/images/it/'.$SRC,"/images/it/s_{$V_ITEM_ID}.{$FORMAT}");
        $V_IMAGE2="s_{$V_ITEM_ID}.{$FORMAT}#$W#$H";
        }

        if(!empty($V_IMAGE3) && strchr($V_IMAGE3,"#"))
        {
        list($SRC,$W,$H)=split('#',$V_IMAGE3);
        list($none,$FORMAT)=split('\.',$SRC);
        $cmf->FileCopy('/images/it/'.$SRC,"/images/it/b_{$V_ITEM_ID}.{$FORMAT}");
        $V_IMAGE3="b_{$V_ITEM_ID}.{$FORMAT}#$W#$H";
        }

        //echo $V_IMAGE1."=>".$V_IMAGE2."=>".$V_IMAGE3;


        $cmf->execute('insert into ITEM (ITEM_ID,CATALOGUE_ID,TYPENAME,BRAND_ID,NAME,CURRENCY_ID,ARTICLE,IMAGE1,IMAGE2,IMAGE3,DESCRIPTION,PRICE,PRICE1,STATUS) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$V_ITEM_ID,$V_CATALOGUE_ID,$V_TYPENAME,$V_BRAND_ID,$V_NAME,$V_CURRENCY_ID,$V_ARTICLE,$V_IMAGE1,$V_IMAGE2,$V_IMAGE3,$V_DESCRIPTION,$V_PRICE,$V_PRICE1,$V_STATUS);  echo mysql_error();
        $cmf->execute('insert into ITEM0 (ITEM_ID,ATTRIBUT_ID,VALUE) select ?,ATTRIBUT_ID,VALUE from ITEM0 where ITEM_ID=?',$V_ITEM_ID,$vid);
        $cmf->execute('insert into ITEM1 (ITEM_ID,ATTRIBUT_ID,VALUE) select ?,ATTRIBUT_ID,VALUE from ITEM1 where ITEM_ID=?',$V_ITEM_ID,$vid);
        $cmf->execute('insert into ITEM2 (ITEM_ID,ATTRIBUT_ID,VALUE) select ?,ATTRIBUT_ID,VALUE from ITEM2 where ITEM_ID=?',$V_ITEM_ID,$vid);
 }

$cmf->execute('delete from CAT_ITEM');
$cmf->Rebuild(array(0));
$cmf->CheckCount(0);
$cmf->execute('drop table if exists ttt');
$cmf->execute('create temporary table ttt select BRAND_ID,count(*) as c from ITEM where STATUS=1 group by BRAND_ID');
$cmf->execute('update BRAND set COUNT_=0');
$cmf->execute('update BRAND B inner join ttt on (B.BRAND_ID=ttt.BRAND_ID)  set B.COUNT_=ttt.c');
}

if($_REQUEST['e'] == 'Скопировать все')
{
$children = getChildren($cmf,$_REQUEST['pid']);
if($children) $where = " and CATALOGUE_ID IN (".implode(',',$children).")";
else $where = " and CATALOGUE_ID = '".$_REQUEST['pid']."'";

$items = getItems($cmf,$where);

if($items)
foreach ($items as $vid)
 {
        list($V_CATALOGUE_ID,$V_TYPENAME,$V_BRAND_ID,$V_NAME,$V_CURRENCY_ID,$V_ARTICLE,$V_IMAGE1,$V_IMAGE2,$V_IMAGE3,$V_DESCRIPTION,$V_PRICE,$V_PRICE1,$V_STATUS)=$cmf->selectrow_array('select CATALOGUE_ID,TYPENAME,BRAND_ID,NAME,CURRENCY_ID,ARTICLE,IMAGE1,IMAGE2,IMAGE3,DESCRIPTION,PRICE,PRICE1,STATUS from ITEM where ITEM_ID=?',$vid);
        $V_ITEM_ID=$cmf->GetSequence('ITEM');
        
        if(!empty($V_IMAGE1) && strchr($V_IMAGE1,"#"))
        {
        list($SRC,$W,$H)=split('#',$V_IMAGE1);
        $FORMAT='';
        list($none,$FORMAT)=split('\.',$SRC);
        $cmf->FileCopy('/images/it/'.$SRC,"/images/it/$V_ITEM_ID.$FORMAT");
        $V_IMAGE1="$V_ITEM_ID.$FORMAT#$W#$H";
        }

        if(!empty($V_IMAGE2) && strchr($V_IMAGE2,"#"))
        {
        list($SRC,$W,$H)=split('#',$V_IMAGE2);
        list($none,$FORMAT)=split('\.',$SRC);
        $cmf->FileCopy('/images/it/'.$SRC,"/images/it/s_{$V_ITEM_ID}.{$FORMAT}");
        $V_IMAGE2="s_{$V_ITEM_ID}.{$FORMAT}#$W#$H";
        }

        if(!empty($V_IMAGE3) && strchr($V_IMAGE3,"#"))
        {
        list($SRC,$W,$H)=split('#',$V_IMAGE3);
        list($none,$FORMAT)=split('\.',$SRC);
        $cmf->FileCopy('/images/it/'.$SRC,"/images/it/b_{$V_ITEM_ID}.{$FORMAT}");
        $V_IMAGE3="b_{$V_ITEM_ID}.{$FORMAT}#$W#$H";
        }

        $cmf->execute('insert into ITEM (ITEM_ID,CATALOGUE_ID,TYPENAME,BRAND_ID,NAME,CURRENCY_ID,ARTICLE,IMAGE1,IMAGE2,IMAGE3,DESCRIPTION,PRICE,PRICE1,STATUS) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',null,$V_CATALOGUE_ID,$V_TYPENAME,$V_BRAND_ID,$V_NAME,$V_CURRENCY_ID,$V_ARTICLE,$V_IMAGE1,$V_IMAGE2,$V_IMAGE3,$V_DESCRIPTION,$V_PRICE,$V_PRICE1,$V_STATUS);  echo mysql_error();
        $cmf->execute('insert into ITEM0 (ITEM_ID,ATTRIBUT_ID,VALUE) select ?,ATTRIBUT_ID,VALUE from ITEM0 where ITEM_ID=?',$V_ITEM_ID,$vid);
        $cmf->execute('insert into ITEM1 (ITEM_ID,ATTRIBUT_ID,VALUE) select ?,ATTRIBUT_ID,VALUE from ITEM1 where ITEM_ID=?',$V_ITEM_ID,$vid);
        $cmf->execute('insert into ITEM2 (ITEM_ID,ATTRIBUT_ID,VALUE) select ?,ATTRIBUT_ID,VALUE from ITEM2 where ITEM_ID=?',$V_ITEM_ID,$vid);
 }

$cmf->execute('delete from CAT_ITEM');
$cmf->Rebuild(array(0));
$cmf->CheckCount(0);
$cmf->execute('drop table if exists ttt');
$cmf->execute('create temporary table ttt select BRAND_ID,count(*) as c from ITEM where STATUS=1 group by BRAND_ID');
$cmf->execute('update BRAND set COUNT_=0');
$cmf->execute('update BRAND B inner join ttt on (B.BRAND_ID=ttt.BRAND_ID)  set B.COUNT_=ttt.c');
}

if($_REQUEST['e'] =='Перенести')
{
if(isset($_REQUEST['id']))
foreach ($_REQUEST['id'] as $vid)
 {
        $cmf->execute('update ITEM set CATALOGUE_ID=? where ITEM_ID=?',$_REQUEST['cid'],$vid);
 }
$cmf->execute('delete from CAT_ITEM');
$cmf->Rebuild(array(0));
$cmf->CheckCount(0);
$cmf->execute('drop table if exists ttt');
$cmf->execute('create temporary table ttt select BRAND_ID,count(*) as c from ITEM where STATUS=1 group by BRAND_ID');
$cmf->execute('update BRAND set COUNT_=0');
$cmf->execute('update BRAND B inner join ttt on (B.BRAND_ID=ttt.BRAND_ID)  set B.COUNT_=ttt.c');

$cmf->execute('delete from CAT_BRAND');
$sth = $cmf->execute('select  distinct CATALOGUE_ID,BRAND_ID,COUNT(*) as cnt from ITEM group by CATALOGUE_ID,BRAND_ID');
while($rw = mysql_fetch_array($sth))
{
  $cmf->execute('insert into CAT_BRAND set BRAND_ID=?, CATALOGUE_ID =?, COUNT_=?',$rw['BRAND_ID'],$rw['CATALOGUE_ID'],$rw['cnt']);

}
}

if (($_REQUEST['e'] == 'Добавить') || ($_REQUEST['e'] == 'Изменить')) {
    if ($cmf->Param('id')) {
        $cmf->execute('delete from ITEM0 where ITEM_ID=?', $_REQUEST['id']);
        $cmf->execute('delete from ITEM1 where ITEM_ID=?', $_REQUEST['id']);
        $cmf->execute('delete from ITEM2 where ITEM_ID=?', $_REQUEST['id']);
        $cmf->execute('delete from ITEM7 where ITEM_ID=?', $_REQUEST['id']);
    }
    foreach ($_REQUEST as $param => $val) {
        if (preg_match('/ATTR_(.)_(.+)/', $param, $m)) {
            list($tbl, $attr) = array($m[1], $m[2]);

            $val = trim($val);
            if ($val != '') {
                if ($tbl == 1) {
                    $val = preg_replace('/,/', '.', $val);
                }
                if ($tbl > 2 && $tbl != 7) {
                    $tbl = 0;
                }
                if ($tbl == 2 || $tbl == 7) {
                    $cmf->execute('insert into ITEM' . $tbl . ' (ITEM_ID,ATTRIBUT_ID,VALUE) values(?,?,?)', $_REQUEST['id'], $attr, $val);
                }
                elseif ($tbl == 7) {
                    $cmf->execute('insert into ITEM64K (ITEM_ID,ATTRIBUT_ID,VALUE) values(?,?,?)', $_REQUEST['id'], $attr, $val);
                }
                else {
                    $cmf->execute('insert into ITEM' . $tbl . '(ITEM_ID,ATTRIBUT_ID,VALUE) values(?,?,?)', $_REQUEST['id'], $attr, $val);
                }
            }
        }
    }
}


if(!isset($_REQUEST['e1']))$_REQUEST['e1']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e1') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from ITEM_PHOTO where ITEM_ID=? and ITEM_ITEM_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e1') == 'Изменить')
{




		
				

$path_to_watermark_IMAGE1='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_small_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_small_y'");
   if(isset($_REQUEST['GEN_IMAGE1']) && $_REQUEST['GEN_IMAGE1'] && isset($_FILES['NOT_IMAGE2']['tmp_name']) && $_FILES['NOT_IMAGE2']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_sm', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_img_sm', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE1'] = $obj_img_resize->new_image_name;
 
	  }
	  else{
			$_REQUEST['IMAGE1']=$cmf->PicturePostResize('NOT_',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_sm',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE1'],$_REQUEST['HEIGHT_IMAGE1']);
	  }
	}
	elseif(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE1',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_sm', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE1',''.$_REQUEST['id'].'_img_sm', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE1'] = $obj_img_resize->new_image_name;

	  }
	  else{
			$_REQUEST['IMAGE1']=$cmf->PicturePostResize('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_sm',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE1'],$_REQUEST['HEIGHT_IMAGE1']);
	  }
	}

			
	
	if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

		
				

$path_to_watermark_IMAGE2='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_big_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_big_y'");
	
    if(isset($_FILES['NOT_IMAGE2']['tmp_name']) && $_FILES['NOT_IMAGE2']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
			  if(isset($obj_img_resize) && is_object($obj_img_resize)){
				
			$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_img_lrg', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;

			  }
			  else{
					$_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_img_lrg',$VIRTUAL_IMAGE_PATH);
			  }
		   }
		   else{
			  if(isset($obj_img_resize) && is_object($obj_img_resize)){
				
			$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_lrg', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;

			  }
			  else{
					 $_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_lrg',$VIRTUAL_IMAGE_PATH);		   
			  }
		   }
		}
		else{ 
			if(isset($obj_img_resize) && is_object($obj_img_resize)){
			   
			$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_img_lrg', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;

			}
			else{
				$_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_lrg',$VIRTUAL_IMAGE_PATH);		   
			}
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE2']) && $_REQUEST['CLR_IMAGE2']){$_REQUEST['IMAGE2']=$cmf->UnlinkFile($_REQUEST['IMAGE2'],$VIRTUAL_IMAGE_PATH);}
	
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('update ITEM_PHOTO set NAME=?,IMAGE1=?,IMAGE2=?,STATUS=? where ITEM_ID=? and ITEM_ITEM_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['IMAGE2']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{


$_REQUEST['iid']=$cmf->GetSequence('ITEM_PHOTO');





		
				

$path_to_watermark_IMAGE1='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_small_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_small_y'");
   if(isset($_REQUEST['GEN_IMAGE1']) && $_REQUEST['GEN_IMAGE1'] && isset($_FILES['NOT_IMAGE2']['tmp_name']) && $_FILES['NOT_IMAGE2']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_sm', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_img_sm', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE1'] = $obj_img_resize->new_image_name;
 
	  }
	  else{
			$_REQUEST['IMAGE1']=$cmf->PicturePostResize('NOT_',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_sm',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE1'],$_REQUEST['HEIGHT_IMAGE1']);
	  }
	}
	elseif(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE1',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_sm', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE1',''.$_REQUEST['id'].'_img_sm', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE1'] = $obj_img_resize->new_image_name;

	  }
	  else{
			$_REQUEST['IMAGE1']=$cmf->PicturePostResize('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_sm',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE1'],$_REQUEST['HEIGHT_IMAGE1']);
	  }
	}

			
	
	if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

		
				

$path_to_watermark_IMAGE2='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_big_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_big_y'");
	
    if(isset($_FILES['NOT_IMAGE2']['tmp_name']) && $_FILES['NOT_IMAGE2']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
			  if(isset($obj_img_resize) && is_object($obj_img_resize)){
				
			$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_img_lrg', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;

			  }
			  else{
					$_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_img_lrg',$VIRTUAL_IMAGE_PATH);
			  }
		   }
		   else{
			  if(isset($obj_img_resize) && is_object($obj_img_resize)){
				
			$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_lrg', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;

			  }
			  else{
					 $_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_lrg',$VIRTUAL_IMAGE_PATH);		   
			  }
		   }
		}
		else{ 
			if(isset($obj_img_resize) && is_object($obj_img_resize)){
			   
			$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_img_lrg', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;

			}
			else{
				$_REQUEST['IMAGE2']=$cmf->PicturePost('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_lrg',$VIRTUAL_IMAGE_PATH);		   
			}
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE2']) && $_REQUEST['CLR_IMAGE2']){$_REQUEST['IMAGE2']=$cmf->UnlinkFile($_REQUEST['IMAGE2'],$VIRTUAL_IMAGE_PATH);}
	
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into ITEM_PHOTO (ITEM_ID,ITEM_ITEM_ID,NAME,IMAGE1,IMAGE2,STATUS) values (?,?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['IMAGE2']),stripslashes($_REQUEST['STATUS']));
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_ITEM_ITEM_ID,$V_NAME,$V_IMAGE1,$V_IMAGE2,$V_STATUS)=$cmf->selectrow_arrayQ('select ITEM_ITEM_ID,NAME,IMAGE1,IMAGE2,STATUS from ITEM_PHOTO where ITEM_ID=? and ITEM_ITEM_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


if(isset($V_IMAGE1))
{
  $IM_IMAGE1=split('#',$V_IMAGE1);
  if(isset($IM_3[1]) && $IM_IMAGE1[1] > 150){$IM_IMAGE1[2]=$IM_IMAGE1[2]*150/$IM_IMAGE1[1]; $IM_IMAGE1[1]=150;}
}

if(isset($V_IMAGE2))
{
   $IM_IMAGE2=split('#',$V_IMAGE2);
   if(isset($IM_4[1]) && $IM_IMAGE2[1] > 150){$IM_IMAGE2[2]=$IM_IMAGE2[2]*150/$IM_IMAGE2[1]; $IM_IMAGE2[1]=150;}
}

$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Дополнительные фотографии</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="ITEM.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
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
<tr bgcolor="#FFFFFF"><th width="1%"><b>Наименование:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка мал.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
EOF;
if(!empty($IM_IMAGE1[1])) $width = $IM_IMAGE1[1];
else $width = '';

if(!empty($IM_IMAGE1[2])) $height = $IM_IMAGE1[2];
else $height = '';

$IM_IMAGE1[0] = !empty($IM_IMAGE1[0]) ? $IM_IMAGE1[0]:0;
$IM_IMAGE1[1] = !empty($IM_IMAGE1[1]) ? $IM_IMAGE1[1]:0;
$IM_IMAGE1[2] = !empty($IM_IMAGE1[2]) ? $IM_IMAGE1[2]:0;

print <<<EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]" width="$IM_IMAGE1[1]" height="$IM_IMAGE1[2]" /></td>
<td><input type="file" name="NOT_IMAGE1" size="1" disabled="1" /><br />
<input type="checkbox" name="GEN_IMAGE1" value="1" checked="1" onClick="if(document.frm.GEN_IMAGE1.checked == '1') document.frm.NOT_IMAGE1.disabled='1'; else document.frm.NOT_IMAGE1.disabled='';" />Сгенерить из большой<br />
<input type="checkbox" name="CLR_IMAGE1" value="1" />Сбросить карт. <br />
Ширина превью: <input type="text" name="WIDTH_IMAGE1" size="5" value="$width" /><br />
Высота превью: <input type="text" name="HEIGHT_IMAGE1" size="5" value="$height" /><br />

</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка бол.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE2" value="$V_IMAGE2" />
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
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
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
list($V_ITEM_ITEM_ID,$V_NAME,$V_IMAGE1,$V_IMAGE2,$V_STATUS)=array('','','','','');


$IM_IMAGE1=array('','','');
$IM_IMAGE2=array('','','');
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Дополнительные фотографии</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="ITEM.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Наименование:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка мал.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
EOF;
if(!empty($IM_IMAGE1[1])) $width = $IM_IMAGE1[1];
else $width = '';

if(!empty($IM_IMAGE1[2])) $height = $IM_IMAGE1[2];
else $height = '';

$IM_IMAGE1[0] = !empty($IM_IMAGE1[0]) ? $IM_IMAGE1[0]:0;
$IM_IMAGE1[1] = !empty($IM_IMAGE1[1]) ? $IM_IMAGE1[1]:0;
$IM_IMAGE1[2] = !empty($IM_IMAGE1[2]) ? $IM_IMAGE1[2]:0;

print <<<EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]" width="$IM_IMAGE1[1]" height="$IM_IMAGE1[2]" /></td>
<td><input type="file" name="NOT_IMAGE1" size="1" disabled="1" /><br />
<input type="checkbox" name="GEN_IMAGE1" value="1" checked="1" onClick="if(document.frm.GEN_IMAGE1.checked == '1') document.frm.NOT_IMAGE1.disabled='1'; else document.frm.NOT_IMAGE1.disabled='';" />Сгенерить из большой<br />
<input type="checkbox" name="CLR_IMAGE1" value="1" />Сбросить карт. <br />
Ширина превью: <input type="text" name="WIDTH_IMAGE1" size="5" value="$width" /><br />
Высота превью: <input type="text" name="HEIGHT_IMAGE1" size="5" value="$height" /><br />

</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка бол.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE2" value="$V_IMAGE2" />
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
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}

if(!isset($_REQUEST['e2']))$_REQUEST['e2']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e2') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from ITEM_MEDIA where ITEM_ID=? and ITEM_MEDIA_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e2') == 'Изменить')
{



$_REQUEST['MEDIA_FILE']=$cmf->FilePost('NOT_MEDIA_FILE',$_REQUEST['MEDIA_FILE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_media',$VIRTUAL_IMAGE_PATH);
	if(isset($_REQUEST['CLR_MEDIA_FILE']) && $_REQUEST['CLR_MEDIA_FILE']){$_REQUEST['MEDIA_FILE']=$cmf->UnlinkFile($_REQUEST['MEDIA_FILE'],$VIRTUAL_IMAGE_PATH);}
	

$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('update ITEM_MEDIA set NAME=?,MEDIA_FILE=?,MEDIA_CODE=?,STATUS=? where ITEM_ID=? and ITEM_MEDIA_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['MEDIA_FILE']),stripslashes($_REQUEST['MEDIA_CODE']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e2') == 'Добавить')
{


$_REQUEST['iid']=$cmf->GetSequence('ITEM_MEDIA');




$_REQUEST['MEDIA_FILE']=$cmf->FilePost('NOT_MEDIA_FILE',$_REQUEST['MEDIA_FILE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_img_media',$VIRTUAL_IMAGE_PATH);
	if(isset($_REQUEST['CLR_MEDIA_FILE']) && $_REQUEST['CLR_MEDIA_FILE']){$_REQUEST['MEDIA_FILE']=$cmf->UnlinkFile($_REQUEST['MEDIA_FILE'],$VIRTUAL_IMAGE_PATH);}
	

$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into ITEM_MEDIA (ITEM_ID,ITEM_MEDIA_ID,NAME,MEDIA_FILE,MEDIA_CODE,STATUS) values (?,?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['MEDIA_FILE']),stripslashes($_REQUEST['MEDIA_CODE']),stripslashes($_REQUEST['STATUS']));
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e2') == 'ED')
{
list ($V_ITEM_MEDIA_ID,$V_NAME,$V_MEDIA_FILE,$V_MEDIA_CODE,$V_STATUS)=$cmf->selectrow_arrayQ('select ITEM_MEDIA_ID,NAME,MEDIA_FILE,MEDIA_CODE,STATUS from ITEM_MEDIA where ITEM_ID=? and ITEM_MEDIA_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$IM_MEDIA_FILE=split('#',$V_MEDIA_FILE);
$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Медиа файлы</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="ITEM.php#f2" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(MEDIA_CODE);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'" />';

@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Наименование:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Файл медиа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="MEDIA_FILE" value="$V_MEDIA_FILE" />
<table><tr><td><input type="file" name="NOT_MEDIA_FILE" size="1" /><br /><input type="checkbox" name="CLR_MEDIA_FILE" value="1" />Сбросить файл.</td>
<td>&#160;</td><td>size=$IM_MEDIA_FILE[1]<br />/images/$VIRTUAL_IMAGE_PATH$IM_MEDIA_FILE[0]
</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Код проигрователя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="MEDIA_CODE" rows="7" cols="90">$V_MEDIA_CODE</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;





$visible=0;
}

if($cmf->Param('e2') == 'Новый')
{
list($V_ITEM_MEDIA_ID,$V_NAME,$V_MEDIA_FILE,$V_MEDIA_CODE,$V_STATUS)=array('','','','','');


$IM_MEDIA_FILE=split('#',$V_MEDIA_FILE);
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Медиа файлы</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="ITEM.php#f2" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(MEDIA_CODE);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Наименование:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Файл медиа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="MEDIA_FILE" value="$V_MEDIA_FILE" />
<table><tr><td><input type="file" name="NOT_MEDIA_FILE" size="1" /><br /><input type="checkbox" name="CLR_MEDIA_FILE" value="1" />Сбросить файл.</td>
<td>&#160;</td><td>size=$IM_MEDIA_FILE[1]<br />/images/$VIRTUAL_IMAGE_PATH$IM_MEDIA_FILE[0]
</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Код проигрователя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="MEDIA_CODE" rows="7" cols="90">$V_MEDIA_CODE</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}

if(!isset($_REQUEST['e3']))$_REQUEST['e3']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e3') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from ITEM_REQUEST where ITEM_ID=? and ITEM_REQUEST_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e3') == 'Изменить')
{



$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('update ITEM_REQUEST set EMAIL=?,STATUS=? where ITEM_ID=? and ITEM_REQUEST_ID=?',stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e3') == 'Добавить')
{


$_REQUEST['iid']=$cmf->GetSequence('ITEM_REQUEST');




$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into ITEM_REQUEST (ITEM_ID,ITEM_REQUEST_ID,EMAIL,STATUS) values (?,?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['STATUS']));
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e3') == 'ED')
{
list ($V_ITEM_REQUEST_ID,$V_EMAIL,$V_STATUS)=$cmf->selectrow_arrayQ('select ITEM_REQUEST_ID,EMAIL,STATUS from ITEM_REQUEST where ITEM_ID=? and ITEM_REQUEST_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Заявки</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="ITEM.php#f3" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(EMAIL);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'" />';

@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e3" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e3" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>E-mail:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e3" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e3" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;





$visible=0;
}

if($cmf->Param('e3') == 'Новый')
{
list($V_ITEM_REQUEST_ID,$V_EMAIL,$V_STATUS)=array('','','');


$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Заявки</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="ITEM.php#f3" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(EMAIL);">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e3" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e3" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>E-mail:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e3" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e3" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}





if($cmf->Param('el1') == 'Удалить')
{
foreach ($_REQUEST['iid'] as $id)
 {


$cmf->execute('delete from ITEM_ITEM where ITEM_ID=? and ITEM_ITEM_ID=?',$_REQUEST['id'],$id);


 }

$_REQUEST['e']='ED';
}






if($cmf->Param('el1') == 'Изменить')
{




$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


        $_REQUEST['ITEM_ITEM_ID'] = $_REQUEST['ITEM_ID'];

        

$cmf->execute('update ITEM_ITEM set ITEM_ITEM_ID=?,STATUS=? where ITEM_ITEM_ID=? and ITEM_ID=?',stripslashes($_REQUEST['ITEM_ITEM_ID']),stripslashes($_REQUEST['STATUS']),$_REQUEST['iid'],$_REQUEST['id']);



        $parents = getParents($_REQUEST['CATALOGUE_ID']);
        if($parents) $pid = $parents[sizeof($parents)-1];
        if($pid == '') $pid = 0;
        $cmf->execute('update ITEM_ITEM set CATALOGUE_ID=? where ITEM_ITEM_ID=? and ITEM_ID=?',$_REQUEST['CATALOGUE_ID'],$_REQUEST['ITEM_ID'],$_REQUEST['id']);

        
$_REQUEST['e']='ED';
};



if($cmf->Param('el1') == 'Добавить')
{


foreach ($_REQUEST['ITEM_ID'] as $id)
{

        $_REQUEST['ITEM_ITEM_ID'] = $_REQUEST['id'];
        $count = $cmf->selectrow_array("select COUNT(*) from ITEM_ITEM where ITEM_ITEM_ID=? and ITEM_ID=?",$id,$_REQUEST['id']);
        if($count > 0)
        {
           echo '<b>Этот товар уже добавлен в сопутствующие</b><br/><br/><a href="ITEM.php?e=ED&amp;id='.$_REQUEST['id'].'">Вернуться</a>';
           exit;
        }
        



$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('insert into ITEM_ITEM (ITEM_ID,ITEM_ITEM_ID,STATUS) values (?,?,?)',$_REQUEST['id'],$id,stripslashes($_REQUEST['STATUS']));
//$_REQUEST['iid']=$_REQUEST['ITEM_ID'];

        $cmf->execute('update ITEM_ITEM set CATALOGUE_ID=? where ITEM_ITEM_ID=? and ITEM_ID=?',$_REQUEST['CATALOGUE_ID'],$id,$_REQUEST['id']);

        
} ///1

$_REQUEST['e']='ED';
$visible=0;
}


if($cmf->Param('eventl1') == 'ED')
{
list ($V_CATALOGUE_ID,$V_ITEM_ID,$V_ITEM_ITEM_ID,$V_STATUS)=$cmf->selectrow_arrayQ('select CATALOGUE_ID,ITEM_ID,ITEM_ITEM_ID,STATUS from ITEM_ITEM where ITEM_ID=? and ITEM_ITEM_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_CATALOGUE_ID=$cmf->Spravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE   order by NAME');        
					
 $V_STR_ITEM_ID=$cmf->Spravotchnik($V_ITEM_ITEM_ID,'select A.ITEM_ID,CONCAT(B.NAME,\' \',A.NAME, \' (Артикул \', A.ARTICLE,\')\') from ITEM A   left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where 1  and A.STATUS=1 and A.PRICE > 0  and A.CATALOGUE_ID='.$V_CATALOGUE_ID.'  order by CONCAT(B.NAME,\' \',A.NAME, \' (Артикул \', A.ARTICLE,\')\')');
							
$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
        <h2 class="h2">Редактирование - Сопутствующие товары</h2>

<form method="POST" action="ITEM.php#fl1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
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
        
        
        $VV_CATALOGUE_ID=$cmf->TreeSpravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE   where PARENT_ID=?  and COUNT_ > 0',0);
@print <<<EOF
<tr bgcolor="#FFFFFF"><td><span class="title2">Продукция</span></td><td width="100%">
<table><tr><td>Каталог: <select name="CATALOGUE_ID" onchange="return chan(this.form,this.form.elements['ITEM_ID'],'select A.ITEM_ID,CONCAT(B.NAME,\' \',A.NAME, \' (Артикул \', A.ARTICLE,\')\') from ITEM A  left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.CATALOGUE_ID=?  and A.STATUS=1 and A.PRICE &gt; 0  order by A.NAME',this.value);"><option value="">-- Не задан --</option>{$VV_CATALOGUE_ID}</select>
</td></tr>




<tr><td /></tr></table></td></tr>
EOF;
        
        

@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>товар:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="ITEM_ID">
				
				
				
				
				
				
				$V_STR_ITEM_ID
			</select><br />
		
	

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
list($V_CATALOGUE_ID,$V_ITEM_ID,$V_ITEM_ITEM_ID,$V_STATUS)=array('','','','');


$V_STR_CATALOGUE_ID=$cmf->Spravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE   order by NAME');
					
$V_STR_ITEM_ID=$cmf->Spravotchnik($V_ITEM_ID,'select A.ITEM_ID,CONCAT(B.NAME,\' \',A.NAME, \' (Артикул \', A.ARTICLE,\')\') from ITEM A   left join BRAND B on (A.BRAND_ID=B.BRAND_ID) order by CONCAT(B.NAME,\' \',A.NAME, \' (Артикул \', A.ARTICLE,\')\')');
					
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Сопутствующие товары</h2>
<form method="POST" action="ITEM.php#fl1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
        <tr bgcolor="#F0F0F0" class="ftr">
            <td colspan="2">
            <input type="submit" name="el1" value="Добавить" class="gbt badd" /><input type="submit" name="el1" value="Отменить" class="gbt bcancel" />
                </td>
        </tr>

<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;



        #список от child-таблицы
        

        
        #список от child-древовидной таблицы
        
        
        $VV_CATALOGUE_ID=$cmf->TreeSpravotchnik('','select CATALOGUE_ID,NAME from CATALOGUE   where PARENT_ID=?  and COUNT_ > 0',0);
@print <<<EOF
<tr bgcolor="#FFFFFF"><td><span class="title2">Продукция</span></td><td width="100%">
<table><tr><td>Каталог: <select name="CATALOGUE_ID" onchange="return chan(this.form,this.form.elements['ITEM_ID[]'],'select A.ITEM_ID,CONCAT(B.NAME,\' \',A.NAME, \' (Артикул \', A.ARTICLE,\')\') from ITEM A  left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.CATALOGUE_ID=?  and A.STATUS=1 and A.PRICE &gt; 0  order by A.NAME',this.value);"><option value="">-- Не задан --</option>{$VV_CATALOGUE_ID}</select>
</td></tr>

<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/sorting.js"></script>

<tr><td>поиск:<br /><input type="text" id="sorting" /></td></tr>

<tr><td><select name="ITEM_ID[]" id="filterfield" multiple="" style="width:100%" size="8"></select></td></tr></table></td></tr>
EOF;
        
        
@print <<<EOF
<tr bgcolor="#FFFFFF"><th width="1%"><b>Связанный товар:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%" /></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>
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






if($_REQUEST['e'] == 'Применить' and is_array($_REQUEST['id']))
{
foreach ($_REQUEST['id'] as $id)
{
 $cmf->execute('update ITEM set STATUS=? where ITEM_ID=?',intval($_REQUEST['STATUS_'.$id]),$id);

$cmf->execute('delete from CAT_ITEM');
$cmf->Rebuild(array(0));
$cmf->CheckCount(0);
$cmf->execute('update ITEM I inner join CURRENCY C on (C.CURRENCY_ID=I.CURRENCY_ID) set I.PRICE_VIRTUAL=I.PRICE*C.PRICE where I.ITEM_ID=?',$id);
$cmf->UpdateRange($id);
                
}

};


if(($_REQUEST['e'] == 'Удалить') and is_array($_REQUEST['id']) and ($cmf->D))
{

foreach ($_REQUEST['id'] as $id)
 {

$cmf->execute('delete from ITEM where ITEM_ID=?',$id);

$cmf->CheckCount(0);
$cmf->execute('delete from CAT_ITEM');
$cmf->Rebuild(array(0));
$cmf->execute('delete from ITEM0 where ITEM_ID=?',$id);
$cmf->execute('delete from ITEM1 where ITEM_ID=?',$id);
$cmf->execute('delete from ITEM2 where ITEM_ID=?',$id);
$cmf->execute('delete from ITEMR where ITEM_ID=?',$id);
$cmf->execute('delete from ITEM7 where ITEM_ID=?',$id);
$cmf->UpdateRange($id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{


$_REQUEST['id']=$cmf->GetSequence('ITEM');
















		
				

$path_to_watermark_IMAGE0='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_icon_x_small'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_icon_y_small'");
   if(isset($_REQUEST['GEN_IMAGE0']) && $_REQUEST['GEN_IMAGE0'] && isset($_FILES['NOT_IMAGE3']['tmp_name']) && $_FILES['NOT_IMAGE3']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_icon_s', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE0, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_icon_s', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE0, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE0'] = $obj_img_resize->new_image_name;
 
	  }
	  else{
			$_REQUEST['IMAGE0']=$cmf->PicturePostResize('NOT_',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_icon_s',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE0'],$_REQUEST['HEIGHT_IMAGE0']);
	  }
	}
	elseif(isset($_FILES['NOT_IMAGE0']['tmp_name']) && $_FILES['NOT_IMAGE0']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE0',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_icon_s', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE0, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE0',''.$_REQUEST['id'].'_icon_s', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE0, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE0'] = $obj_img_resize->new_image_name;

	  }
	  else{
			$_REQUEST['IMAGE0']=$cmf->PicturePostResize('NOT_IMAGE0',$_REQUEST['IMAGE0'],''.$_REQUEST['id'].'_icon_s',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE0'],$_REQUEST['HEIGHT_IMAGE0']);
	  }
	}

			
	
	if(isset($_REQUEST['CLR_IMAGE0']) && $_REQUEST['CLR_IMAGE0']){$_REQUEST['IMAGE0']=$cmf->UnlinkFile($_REQUEST['IMAGE0'],$VIRTUAL_IMAGE_PATH);}
	

		
				

$path_to_watermark_IMAGE1='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_icon_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_icon_y'");
   if(isset($_REQUEST['GEN_IMAGE1']) && $_REQUEST['GEN_IMAGE1'] && isset($_FILES['NOT_IMAGE3']['tmp_name']) && $_FILES['NOT_IMAGE3']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_icon', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_icon', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE1'] = $obj_img_resize->new_image_name;
 
	  }
	  else{
			$_REQUEST['IMAGE1']=$cmf->PicturePostResize('NOT_',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_icon',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE1'],$_REQUEST['HEIGHT_IMAGE1']);
	  }
	}
	elseif(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE1',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_icon', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE1',''.$_REQUEST['id'].'_icon', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE1'] = $obj_img_resize->new_image_name;

	  }
	  else{
			$_REQUEST['IMAGE1']=$cmf->PicturePostResize('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_icon',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE1'],$_REQUEST['HEIGHT_IMAGE1']);
	  }
	}

			
	
	if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

		
				

$path_to_watermark_IMAGE2='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_small_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_small_y'");
   if(isset($_REQUEST['GEN_IMAGE2']) && $_REQUEST['GEN_IMAGE2'] && isset($_FILES['NOT_IMAGE3']['tmp_name']) && $_FILES['NOT_IMAGE3']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_m', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_m', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;
 
	  }
	  else{
			$_REQUEST['IMAGE2']=$cmf->PicturePostResize('NOT_',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_m',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE2'],$_REQUEST['HEIGHT_IMAGE2']);
	  }
	}
	elseif(isset($_FILES['NOT_IMAGE2']['tmp_name']) && $_FILES['NOT_IMAGE2']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_m', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_m', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;

	  }
	  else{
			$_REQUEST['IMAGE2']=$cmf->PicturePostResize('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_m',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE2'],$_REQUEST['HEIGHT_IMAGE2']);
	  }
	}

			
	
	if(isset($_REQUEST['CLR_IMAGE2']) && $_REQUEST['CLR_IMAGE2']){$_REQUEST['IMAGE2']=$cmf->UnlinkFile($_REQUEST['IMAGE2'],$VIRTUAL_IMAGE_PATH);}
	

		
				

$path_to_watermark_IMAGE3='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_big_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_big_y'");
	
    if(isset($_FILES['NOT_IMAGE3']['tmp_name']) && $_FILES['NOT_IMAGE3']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
			  if(isset($obj_img_resize) && is_object($obj_img_resize)){
				
			$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_b', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE3, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE3'] = $obj_img_resize->new_image_name;

			  }
			  else{
					$_REQUEST['IMAGE3']=$cmf->PicturePost('NOT_IMAGE3',$_REQUEST['IMAGE3'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_b',$VIRTUAL_IMAGE_PATH);
			  }
		   }
		   else{
			  if(isset($obj_img_resize) && is_object($obj_img_resize)){
				
			$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_b', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE3, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE3'] = $obj_img_resize->new_image_name;

			  }
			  else{
					 $_REQUEST['IMAGE3']=$cmf->PicturePost('NOT_IMAGE3',$_REQUEST['IMAGE3'],''.$_REQUEST['id'].'_b',$VIRTUAL_IMAGE_PATH);		   
			  }
		   }
		}
		else{ 
			if(isset($obj_img_resize) && is_object($obj_img_resize)){
			   
			$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_b', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE3, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE3'] = $obj_img_resize->new_image_name;

			}
			else{
				$_REQUEST['IMAGE3']=$cmf->PicturePost('NOT_IMAGE3',$_REQUEST['IMAGE3'],''.$_REQUEST['id'].'_b',$VIRTUAL_IMAGE_PATH);		   
			}
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE3']) && $_REQUEST['CLR_IMAGE3']){$_REQUEST['IMAGE3']=$cmf->UnlinkFile($_REQUEST['IMAGE3'],$VIRTUAL_IMAGE_PATH);}
	
$_REQUEST['IS_ACTION']=isset($_REQUEST['IS_ACTION']) && $_REQUEST['IS_ACTION']?1:0;






$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into ITEM (ITEM_ID,CATALOGUE_ID,TYPENAME,CATNAME,BRAND_ID,DISCOUNT_ID,WARRANTY_ID,DELIVERY_ID,CREDIT_ID,NAME,ARTICLE,CURRENCY_ID,PRICE,PRICE1,PURCHASE_PRICE,IMAGE0,IMAGE1,IMAGE2,IMAGE3,IS_ACTION,DESCRIPTION,SEO_BOTTOM,TITLE,DESC_META,KEYWORD_META,DATE_INSERT,STATUS) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['CATNAME']),stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['DISCOUNT_ID'])+0,stripslashes($_REQUEST['WARRANTY_ID'])+0,stripslashes($_REQUEST['DELIVERY_ID'])+0,stripslashes($_REQUEST['CREDIT_ID'])+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ARTICLE']),stripslashes($_REQUEST['CURRENCY_ID'])+0,stripslashes($_REQUEST['PRICE'])+0,stripslashes($_REQUEST['PRICE1'])+0,stripslashes($_REQUEST['PURCHASE_PRICE'])+0,stripslashes($_REQUEST['IMAGE0']),stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['IMAGE2']),stripslashes($_REQUEST['IMAGE3']),stripslashes($_REQUEST['IS_ACTION']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['SEO_BOTTOM']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['DESC_META']),stripslashes($_REQUEST['KEYWORD_META']),stripslashes($_REQUEST['DATE_INSERT']),stripslashes($_REQUEST['STATUS']));


$_REQUEST['e'] ='ED';

      if(empty($_REQUEST['CATNAME'])){
        $dbRules = $cmf->select("select * from TRANSLIT_RULE");
        $rules = array();
        if(!empty($dbRules)){
          foreach ($dbRules as $rule){
            $rules[$rule['SRC']] = $rule['TRANSLIT'];
          }
        }
        
        $_REQUEST['NAME'] = trim(mb_strtolower($_REQUEST['NAME'],'utf-8'));
        $_REQUEST['NAME'] = preg_replace("/\s+/s", "-", $_REQUEST['NAME']);
        
        $_REQUEST['CATNAME'] = strtr($_REQUEST['NAME'], $rules);
        
        $cmf->execute('update ITEM set CATNAME=? where ITEM_ID=?',$_REQUEST['CATNAME'] ,$_REQUEST['id']);
        
      }
      
      $cmf->CheckCount(0);
      $cmf->UpdateRange($_REQUEST['id']);
      $cmf->execute('delete from CAT_ITEM');
      $cmf->Rebuild(array(0));
      $cmf->execute('drop table if exists ttt');
      $cmf->execute('create temporary table ttt select BRAND_ID,count(*) as c from ITEM where STATUS=1 group by BRAND_ID');
      $cmf->execute('update BRAND set COUNT_=0');
      $cmf->execute('update BRAND B inner join ttt on (B.BRAND_ID=ttt.BRAND_ID)  set B.COUNT_=ttt.c');
      $cmf->UpdateRange($_REQUEST['id']);
      
      require $_SERVER['DOCUMENT_ROOT']."/lib/MetaGenerate.php";
      require $_SERVER['DOCUMENT_ROOT']."/lib/MetaGenerateModelStrategy.php";

      $model = MetaGenerateModelStrategy::getModel($cmf);
      $meta = new MetaGenerate($model);

      $meta->metaItemById($_REQUEST['id']);
     
}

if($_REQUEST['e'] == 'Изменить')
{

















		
				

$path_to_watermark_IMAGE0='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_icon_x_small'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_icon_y_small'");
   if(isset($_REQUEST['GEN_IMAGE0']) && $_REQUEST['GEN_IMAGE0'] && isset($_FILES['NOT_IMAGE3']['tmp_name']) && $_FILES['NOT_IMAGE3']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_icon_s', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE0, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_icon_s', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE0, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE0'] = $obj_img_resize->new_image_name;
 
	  }
	  else{
			$_REQUEST['IMAGE0']=$cmf->PicturePostResize('NOT_',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_icon_s',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE0'],$_REQUEST['HEIGHT_IMAGE0']);
	  }
	}
	elseif(isset($_FILES['NOT_IMAGE0']['tmp_name']) && $_FILES['NOT_IMAGE0']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE0',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_icon_s', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE0, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE0',''.$_REQUEST['id'].'_icon_s', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE0, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE0'] = $obj_img_resize->new_image_name;

	  }
	  else{
			$_REQUEST['IMAGE0']=$cmf->PicturePostResize('NOT_IMAGE0',$_REQUEST['IMAGE0'],''.$_REQUEST['id'].'_icon_s',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE0'],$_REQUEST['HEIGHT_IMAGE0']);
	  }
	}

			
	
	if(isset($_REQUEST['CLR_IMAGE0']) && $_REQUEST['CLR_IMAGE0']){$_REQUEST['IMAGE0']=$cmf->UnlinkFile($_REQUEST['IMAGE0'],$VIRTUAL_IMAGE_PATH);}
	

		
				

$path_to_watermark_IMAGE1='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_icon_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_icon_y'");
   if(isset($_REQUEST['GEN_IMAGE1']) && $_REQUEST['GEN_IMAGE1'] && isset($_FILES['NOT_IMAGE3']['tmp_name']) && $_FILES['NOT_IMAGE3']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_icon', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_icon', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE1'] = $obj_img_resize->new_image_name;
 
	  }
	  else{
			$_REQUEST['IMAGE1']=$cmf->PicturePostResize('NOT_',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_icon',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE1'],$_REQUEST['HEIGHT_IMAGE1']);
	  }
	}
	elseif(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE1',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_icon', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE1',''.$_REQUEST['id'].'_icon', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE1, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE1'] = $obj_img_resize->new_image_name;

	  }
	  else{
			$_REQUEST['IMAGE1']=$cmf->PicturePostResize('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_icon',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE1'],$_REQUEST['HEIGHT_IMAGE1']);
	  }
	}

			
	
	if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

		
				

$path_to_watermark_IMAGE2='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_small_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_small_y'");
   if(isset($_REQUEST['GEN_IMAGE2']) && $_REQUEST['GEN_IMAGE2'] && isset($_FILES['NOT_IMAGE3']['tmp_name']) && $_FILES['NOT_IMAGE3']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_m', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_m', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;
 
	  }
	  else{
			$_REQUEST['IMAGE2']=$cmf->PicturePostResize('NOT_',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_m',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE2'],$_REQUEST['HEIGHT_IMAGE2']);
	  }
	}
	elseif(isset($_FILES['NOT_IMAGE2']['tmp_name']) && $_FILES['NOT_IMAGE2']['tmp_name']){
	  if(isset($obj_img_resize) && is_object($obj_img_resize)){
		  
			if(isset($_REQUEST['iid']) && !empty($_REQUEST['iid'])){
				$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_m', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			}
			else{
				$obj_img_resize->addSettings('NOT_IMAGE2',''.$_REQUEST['id'].'_m', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE2, $width, $height);
			}
			
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE2'] = $obj_img_resize->new_image_name;

	  }
	  else{
			$_REQUEST['IMAGE2']=$cmf->PicturePostResize('NOT_IMAGE2',$_REQUEST['IMAGE2'],''.$_REQUEST['id'].'_m',$VIRTUAL_IMAGE_PATH,$_REQUEST['WIDTH_IMAGE2'],$_REQUEST['HEIGHT_IMAGE2']);
	  }
	}

			
	
	if(isset($_REQUEST['CLR_IMAGE2']) && $_REQUEST['CLR_IMAGE2']){$_REQUEST['IMAGE2']=$cmf->UnlinkFile($_REQUEST['IMAGE2'],$VIRTUAL_IMAGE_PATH);}
	

		
				

$path_to_watermark_IMAGE3='';
	

	$width = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_big_x'");
	$height = $cmf->selectrow_array("select VALUE from SETINGS where SYSTEM_NAME='item_main_big_y'");
	
    if(isset($_FILES['NOT_IMAGE3']['tmp_name']) && $_FILES['NOT_IMAGE3']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
			  if(isset($obj_img_resize) && is_object($obj_img_resize)){
				
			$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_b', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE3, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE3'] = $obj_img_resize->new_image_name;

			  }
			  else{
					$_REQUEST['IMAGE3']=$cmf->PicturePost('NOT_IMAGE3',$_REQUEST['IMAGE3'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_b',$VIRTUAL_IMAGE_PATH);
			  }
		   }
		   else{
			  if(isset($obj_img_resize) && is_object($obj_img_resize)){
				
			$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_b', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE3, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE3'] = $obj_img_resize->new_image_name;

			  }
			  else{
					 $_REQUEST['IMAGE3']=$cmf->PicturePost('NOT_IMAGE3',$_REQUEST['IMAGE3'],''.$_REQUEST['id'].'_b',$VIRTUAL_IMAGE_PATH);		   
			  }
		   }
		}
		else{ 
			if(isset($obj_img_resize) && is_object($obj_img_resize)){
			   
			$obj_img_resize->addSettings('NOT_IMAGE3',''.$_REQUEST['id'].'_b', '../images'.$VIRTUAL_IMAGE_PATH, $path_to_watermark_IMAGE3, $width, $height);
			$obj_img_resize->addImagePost();
			$_REQUEST['IMAGE3'] = $obj_img_resize->new_image_name;

			}
			else{
				$_REQUEST['IMAGE3']=$cmf->PicturePost('NOT_IMAGE3',$_REQUEST['IMAGE3'],''.$_REQUEST['id'].'_b',$VIRTUAL_IMAGE_PATH);		   
			}
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE3']) && $_REQUEST['CLR_IMAGE3']){$_REQUEST['IMAGE3']=$cmf->UnlinkFile($_REQUEST['IMAGE3'],$VIRTUAL_IMAGE_PATH);}
	
$_REQUEST['IS_ACTION']=isset($_REQUEST['IS_ACTION']) && $_REQUEST['IS_ACTION']?1:0;






$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

if(!empty($_REQUEST['pid'])) $cmf->execute('update ITEM set CATALOGUE_ID=?,TYPENAME=?,CATNAME=?,BRAND_ID=?,DISCOUNT_ID=?,WARRANTY_ID=?,DELIVERY_ID=?,CREDIT_ID=?,NAME=?,ARTICLE=?,CURRENCY_ID=?,PRICE=?,PRICE1=?,PURCHASE_PRICE=?,IMAGE0=?,IMAGE1=?,IMAGE2=?,IMAGE3=?,IS_ACTION=?,DESCRIPTION=?,SEO_BOTTOM=?,TITLE=?,DESC_META=?,KEYWORD_META=?,DATE_INSERT=?,STATUS=? where ITEM_ID=?',stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['CATNAME']),stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['DISCOUNT_ID'])+0,stripslashes($_REQUEST['WARRANTY_ID'])+0,stripslashes($_REQUEST['DELIVERY_ID'])+0,stripslashes($_REQUEST['CREDIT_ID'])+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ARTICLE']),stripslashes($_REQUEST['CURRENCY_ID'])+0,stripslashes($_REQUEST['PRICE'])+0,stripslashes($_REQUEST['PRICE1'])+0,stripslashes($_REQUEST['PURCHASE_PRICE'])+0,stripslashes($_REQUEST['IMAGE0']),stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['IMAGE2']),stripslashes($_REQUEST['IMAGE3']),stripslashes($_REQUEST['IS_ACTION']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['SEO_BOTTOM']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['DESC_META']),stripslashes($_REQUEST['KEYWORD_META']),stripslashes($_REQUEST['DATE_INSERT']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
else $cmf->execute('update ITEM set TYPENAME=?,CATNAME=?,BRAND_ID=?,DISCOUNT_ID=?,WARRANTY_ID=?,DELIVERY_ID=?,CREDIT_ID=?,NAME=?,ARTICLE=?,CURRENCY_ID=?,PRICE=?,PRICE1=?,PURCHASE_PRICE=?,IMAGE0=?,IMAGE1=?,IMAGE2=?,IMAGE3=?,IS_ACTION=?,DESCRIPTION=?,SEO_BOTTOM=?,TITLE=?,DESC_META=?,KEYWORD_META=?,DATE_INSERT=?,STATUS=? where ITEM_ID=?',stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['CATNAME']),stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['DISCOUNT_ID'])+0,stripslashes($_REQUEST['WARRANTY_ID'])+0,stripslashes($_REQUEST['DELIVERY_ID'])+0,stripslashes($_REQUEST['CREDIT_ID'])+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ARTICLE']),stripslashes($_REQUEST['CURRENCY_ID'])+0,stripslashes($_REQUEST['PRICE'])+0,stripslashes($_REQUEST['PRICE1'])+0,stripslashes($_REQUEST['PURCHASE_PRICE'])+0,stripslashes($_REQUEST['IMAGE0']),stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['IMAGE2']),stripslashes($_REQUEST['IMAGE3']),stripslashes($_REQUEST['IS_ACTION']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['SEO_BOTTOM']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['DESC_META']),stripslashes($_REQUEST['KEYWORD_META']),stripslashes($_REQUEST['DATE_INSERT']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);

$_REQUEST['e'] ='ED';

      if(empty($_REQUEST['CATNAME'])){
        $dbRules = $cmf->select("select * from TRANSLIT_RULE");
        $rules = array();
        if(!empty($dbRules)){
          foreach ($dbRules as $rule){
            $rules[$rule['SRC']] = $rule['TRANSLIT'];
          }
        }
        
        $_REQUEST['NAME'] = trim(mb_strtolower($_REQUEST['NAME'],'utf-8'));
        $_REQUEST['NAME'] = preg_replace("/\s+/s", "-", $_REQUEST['NAME']);
        
        $_REQUEST['CATNAME'] = strtr($_REQUEST['NAME'], $rules);
        
        $cmf->execute('update ITEM set CATNAME=? where ITEM_ID=?',$_REQUEST['CATNAME'] ,$_REQUEST['id']);
        
      }
      
      $cmf->execute('delete from CAT_ITEM');
      $cmf->Rebuild(array(0));
      $cmf->CheckCount(0);      
      $cmf->UpdateRange($_REQUEST['id']);
      
      require ROOT_PATH."/lib/MetaGenerate.php";
      require ROOT_PATH."/lib/MetaGenerateModelStrategy.php";

      $model = MetaGenerateModelStrategy::getModel($cmf);
      $meta = new MetaGenerate($model);

      $meta->metaItemById($_REQUEST['id']);
       
};

if($_REQUEST['e'] == 'ED')
{
list($V_ITEM_ID,$V_CATALOGUE_ID,$V_TYPENAME,$V_CATNAME,$V_BRAND_ID,$V_DISCOUNT_ID,$V_WARRANTY_ID,$V_DELIVERY_ID,$V_CREDIT_ID,$V_NAME,$V_ARTICLE,$V_CURRENCY_ID,$V_PRICE,$V_PRICE1,$V_PURCHASE_PRICE,$V_IMAGE0,$V_IMAGE1,$V_IMAGE2,$V_IMAGE3,$V_IS_ACTION,$V_DESCRIPTION,$V_SEO_BOTTOM,$V_TITLE,$V_DESC_META,$V_KEYWORD_META,$V_DATE_INSERT,$V_STATUS)=$cmf->selectrow_arrayQ('select ITEM_ID,CATALOGUE_ID,TYPENAME,CATNAME,BRAND_ID,DISCOUNT_ID,WARRANTY_ID,DELIVERY_ID,CREDIT_ID,NAME,ARTICLE,CURRENCY_ID,PRICE,PRICE1,PURCHASE_PRICE,IMAGE0,IMAGE1,IMAGE2,IMAGE3,IS_ACTION,DESCRIPTION,SEO_BOTTOM,TITLE,DESC_META,KEYWORD_META,DATE_FORMAT(DATE_INSERT,"%Y-%m-%d %H:%i"),STATUS from ITEM where ITEM_ID=?',$_REQUEST['id']);


if(empty($_REQUEST['pid'])) $_REQUEST['pid']=$cmf->selectrow_array('select CATALOGUE_ID from ITEM where ITEM_ID=? ',$_REQUEST['id']);
$checkString='';
$sth=$cmf->execute('select ACL.ATTRIBUT_ID,A.TYPE from ATTR_CATALOG_LINK ACL, ATTRIBUT A, ATTRIBUT_GROUP AG where ACL.CATALOGUE_ID=? and ACL.ATTRIBUT_ID=A.ATTRIBUT_ID and A.ATTRIBUT_GROUP_ID=AG.ATTRIBUT_GROUP_ID and A.TYPE=7 order by AG.ORDERING,A.ORDERING',$_REQUEST['pid']);
while(list($V_ATTRIBUT_ID,$V_TYPE)=mysql_fetch_array($sth, MYSQL_NUM))$checkString.="&amp;&amp; checkXML(ATTR_".$V_TYPE."_".$V_ATTRIBUT_ID.")";


$V_STR_BRAND_ID=$cmf->Spravotchnik($V_BRAND_ID,'select B.BRAND_ID,B.NAME from BRAND  B  order by B.NAME');        
					
$V_STR_DISCOUNT_ID=$cmf->Spravotchnik($V_DISCOUNT_ID,'select DISCOUNT_ID,NAME from DISCOUNTS  order by NAME');        
					
$V_STR_WARRANTY_ID=$cmf->Spravotchnik($V_WARRANTY_ID,'select WARRANTY_ID,NAME from WARRANTY  order by NAME');        
					
$V_STR_DELIVERY_ID=$cmf->Spravotchnik($V_DELIVERY_ID,'select DELIVERY_ID,NAME from DELIVERY  order by NAME');        
					
$V_STR_CREDIT_ID=$cmf->Spravotchnik($V_CREDIT_ID,'select CREDIT_ID,NAME from CREDIT  order by NAME');        
					
$V_STR_CURRENCY_ID=$cmf->Spravotchnik($V_CURRENCY_ID,'select CURRENCY_ID,NAME from CURRENCY  order by NAME');        
					
if(isset($V_IMAGE0))
{
  $IM_IMAGE0=split('#',$V_IMAGE0);
  if(isset($IM_14[1]) && $IM_IMAGE0[1] > 150){$IM_IMAGE0[2]=$IM_IMAGE0[2]*150/$IM_IMAGE0[1]; $IM_IMAGE0[1]=150;}
}

if(isset($V_IMAGE1))
{
  $IM_IMAGE1=split('#',$V_IMAGE1);
  if(isset($IM_15[1]) && $IM_IMAGE1[1] > 150){$IM_IMAGE1[2]=$IM_IMAGE1[2]*150/$IM_IMAGE1[1]; $IM_IMAGE1[1]=150;}
}

if(isset($V_IMAGE2))
{
  $IM_IMAGE2=split('#',$V_IMAGE2);
  if(isset($IM_16[1]) && $IM_IMAGE2[1] > 150){$IM_IMAGE2[2]=$IM_IMAGE2[2]*150/$IM_IMAGE2[1]; $IM_IMAGE2[1]=150;}
}

if(isset($V_IMAGE3))
{
   $IM_IMAGE3=split('#',$V_IMAGE3);
   if(isset($IM_17[1]) && $IM_IMAGE3[1] > 150){$IM_IMAGE3[2]=$IM_IMAGE3[2]*150/$IM_IMAGE3[1]; $IM_IMAGE3[1]=150;}
}

$V_IS_ACTION=$V_IS_ACTION?'checked':'';
$V_STATUS=$V_STATUS?'checked':'';
print @<<<EOF
<h2 class="h2">Редактирование - Продукция</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="ITEM.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(TYPENAME) &amp;&amp; checkXML(CATNAME) &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(ARTICLE) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(SEO_BOTTOM) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(DESC_META) &amp;&amp; checkXML(KEYWORD_META);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" /><input type="hidden" name="FLT_BRAND_ID" value="{$_REQUEST['FLT_BRAND_ID']}" /><input type="hidden" name="FLT_NAME" value="{$_REQUEST['FLT_NAME']}" /><input type="hidden" name="FLT_ARTICLE" value="{$_REQUEST['FLT_ARTICLE']}" /><input type="hidden" name="FLT_PRICE" value="{$_REQUEST['FLT_PRICE']}" /><input type="hidden" name="FLT_STATUS" value="{$_REQUEST['FLT_STATUS']}" />
<input type="hidden" name="type" value="3" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Назад" class="gbt bcancel" />
</td></tr>
EOF;



@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>Тип продукции:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TYPENAME" value="$V_TYPENAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Путь до каталога:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="CATNAME" value="$V_CATNAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Производитель:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="BRAND_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_BRAND_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Скидка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="DISCOUNT_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_DISCOUNT_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Гарантия:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="WARRANTY_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_WARRANTY_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Доставка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="DELIVERY_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_DELIVERY_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Кредит:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CREDIT_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_CREDIT_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Наименование продукта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Артикул:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ARTICLE" value="$V_ARTICLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Валюта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CURRENCY_ID">
				
				
				
				
				
				
				$V_STR_CURRENCY_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цена:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PRICE" value="$V_PRICE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цена со скидкой:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PRICE1" value="$V_PRICE1" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цена закупки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PURCHASE_PRICE" value="$V_PURCHASE_PRICE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка мал. для поиска:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE0" value="$V_IMAGE0" />
EOF;
if(!empty($IM_IMAGE0[1])) $width = $IM_IMAGE0[1];
else $width = '';

if(!empty($IM_IMAGE0[2])) $height = $IM_IMAGE0[2];
else $height = '';

$IM_IMAGE0[0] = !empty($IM_IMAGE0[0]) ? $IM_IMAGE0[0]:0;
$IM_IMAGE0[1] = !empty($IM_IMAGE0[1]) ? $IM_IMAGE0[1]:0;
$IM_IMAGE0[2] = !empty($IM_IMAGE0[2]) ? $IM_IMAGE0[2]:0;

print <<<EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE0[0]" width="$IM_IMAGE0[1]" height="$IM_IMAGE0[2]" /></td>
<td><input type="file" name="NOT_IMAGE0" size="1" disabled="1" /><br />
<input type="checkbox" name="GEN_IMAGE0" value="1" checked="1" onClick="if(document.frm.GEN_IMAGE0.checked == '1') document.frm.NOT_IMAGE0.disabled='1'; else document.frm.NOT_IMAGE0.disabled='';" />Сгенерить из большой<br />
<input type="checkbox" name="CLR_IMAGE0" value="1" />Сбросить карт. <br />
Ширина превью: <input type="text" name="WIDTH_IMAGE0" size="5" value="$width" /><br />
Высота превью: <input type="text" name="HEIGHT_IMAGE0" size="5" value="$height" /><br />

</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка мал.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
EOF;
if(!empty($IM_IMAGE1[1])) $width = $IM_IMAGE1[1];
else $width = '';

if(!empty($IM_IMAGE1[2])) $height = $IM_IMAGE1[2];
else $height = '';

$IM_IMAGE1[0] = !empty($IM_IMAGE1[0]) ? $IM_IMAGE1[0]:0;
$IM_IMAGE1[1] = !empty($IM_IMAGE1[1]) ? $IM_IMAGE1[1]:0;
$IM_IMAGE1[2] = !empty($IM_IMAGE1[2]) ? $IM_IMAGE1[2]:0;

print <<<EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]" width="$IM_IMAGE1[1]" height="$IM_IMAGE1[2]" /></td>
<td><input type="file" name="NOT_IMAGE1" size="1" disabled="1" /><br />
<input type="checkbox" name="GEN_IMAGE1" value="1" checked="1" onClick="if(document.frm.GEN_IMAGE1.checked == '1') document.frm.NOT_IMAGE1.disabled='1'; else document.frm.NOT_IMAGE1.disabled='';" />Сгенерить из большой<br />
<input type="checkbox" name="CLR_IMAGE1" value="1" />Сбросить карт. <br />
Ширина превью: <input type="text" name="WIDTH_IMAGE1" size="5" value="$width" /><br />
Высота превью: <input type="text" name="HEIGHT_IMAGE1" size="5" value="$height" /><br />

</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка сред.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE2" value="$V_IMAGE2" />
EOF;
if(!empty($IM_IMAGE2[1])) $width = $IM_IMAGE2[1];
else $width = '';

if(!empty($IM_IMAGE2[2])) $height = $IM_IMAGE2[2];
else $height = '';

$IM_IMAGE2[0] = !empty($IM_IMAGE2[0]) ? $IM_IMAGE2[0]:0;
$IM_IMAGE2[1] = !empty($IM_IMAGE2[1]) ? $IM_IMAGE2[1]:0;
$IM_IMAGE2[2] = !empty($IM_IMAGE2[2]) ? $IM_IMAGE2[2]:0;

print <<<EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE2[0]" width="$IM_IMAGE2[1]" height="$IM_IMAGE2[2]" /></td>
<td><input type="file" name="NOT_IMAGE2" size="1" disabled="1" /><br />
<input type="checkbox" name="GEN_IMAGE2" value="1" checked="1" onClick="if(document.frm.GEN_IMAGE2.checked == '1') document.frm.NOT_IMAGE2.disabled='1'; else document.frm.NOT_IMAGE2.disabled='';" />Сгенерить из большой<br />
<input type="checkbox" name="CLR_IMAGE2" value="1" />Сбросить карт. <br />
Ширина превью: <input type="text" name="WIDTH_IMAGE2" size="5" value="$width" /><br />
Высота превью: <input type="text" name="HEIGHT_IMAGE2" size="5" value="$height" /><br />

</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка бол.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE3" value="$V_IMAGE3" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE3[0]))
{
if(strchr($IM_IMAGE3[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE3[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE3[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE3[0] = !empty($IM_IMAGE3[0]) ? $IM_IMAGE3[0]:0;
$IM_IMAGE3[1] = !empty($IM_IMAGE3[1]) ? $IM_IMAGE3[1]:0;
$IM_IMAGE3[2] = !empty($IM_IMAGE3[2]) ? $IM_IMAGE3[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE3[0]" width="$IM_IMAGE3[1]" height="$IM_IMAGE3[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE3" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE3" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Участвует в акции:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_ACTION' value='1' $V_IS_ACTION/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="7" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>SEO текст внизу страницы:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="SEO_BOTTOM" name="SEO_BOTTOM" rows="7" cols="90">
EOF;
$V_SEO_BOTTOM = htmlspecialchars_decode($V_SEO_BOTTOM);
echo $V_SEO_BOTTOM;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'SEO_BOTTOM', {
      customConfig : 'ckeditor/config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тайтл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TITLE" value="$V_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Description:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESC_META" rows="7" cols="90">$V_DESC_META</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Ключевые слова:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="KEYWORD_META" rows="7" cols="90">$V_KEYWORD_META</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><td></td><td /></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата вставки в БД:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATE_INSERT" name="DATE_INSERT" value="$V_DATE_INSERT" />
EOF;

if($V_DATE_INSERT) $V_DAT_ = substr($V_DATE_INSERT,8,2).".".substr($V_DATE_INSERT,5,2).".".substr($V_DATE_INSERT,0,4)." ".substr($V_DATE_INSERT,11,2).":".substr($V_DATE_INSERT,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DATE_INSERT">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DATE_INSERT" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATE_INSERT",
                       displayArea    :    "DATE_DATE_INSERT",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATE_INSERT",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr><tr bgcolor="#FFFFFF"><td width="1%"><b>Дополнительные атрибуты:<br /><img src="img/hi.gif" width="125" height="1" /></b></td><td width="100%">

EOF;
/*$sth=$cmf->execute('select ACL.ATTRIBUT_ID,A.NAME,A.TYPE,A.UNIT_ID from ATTR_CATALOG_LINK ACL, ATTRIBUT A, ATTRIBUT_GROUP AG where ACL.CATALOGUE_ID=? and ACL.ATTRIBUT_ID=A.ATTRIBUT_ID and A.ATTRIBUT_GROUP_ID=AG.ATTRIBUT_GROUP_ID order by AG.ORDERING,A.ORDERING',$_REQUEST['pid']);*/
$sth=$cmf->execute('select ACL.ATTRIBUT_ID,A.NAME,A.TYPE,A.UNIT_ID from ATTR_CATALOG_LINK ACL, ATTRIBUT A, ATTRIBUT_GROUP AG where ACL.CATALOGUE_ID=? and ACL.ATTRIBUT_ID=A.ATTRIBUT_ID and A.ATTRIBUT_GROUP_ID=AG.ATTRIBUT_GROUP_ID order by A.ORDERING',$_REQUEST['pid']);

while(list($V_ATTRIBUT_ID,$V_NAME,$V_TYPE,$V_UNIT_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_UNIT_ID=$cmf->selectrow_array('select concat("(",NAME,")") from UNIT where UNIT_ID=?',$V_UNIT_ID);
if($V_TYPE==5)
{
  $VALUE=$cmf->selectrow_array('select VALUE from ITEM0 where ITEM_ID=? and ATTRIBUT_ID=?',$_REQUEST['id'],$V_ATTRIBUT_ID);
  if($VALUE == '1'){$VALUE='<option value="1" selected="">Да</option><option value="0">Нет</option>';} else {$VALUE='<option value="1">Да</option><option value="0" selected="">Нет</option>';}
?><b><?=$V_NAME?></b><br><select style="width:450px" name="ATTR_<?=$V_TYPE?>_<?=$V_ATTRIBUT_ID?>"><?=$VALUE?></select><br><?
}
elseif($V_TYPE==6)
{
  $VALUE=$cmf->selectrow_array('select VALUE from ITEM0 where ITEM_ID=? and ATTRIBUT_ID=?',$_REQUEST['id'],$V_ATTRIBUT_ID);
  if($VALUE == '1'){$VALUE='<option value="2">Не знаю</option><option value="1" selected="">Да</option><option value="0">Нет</option>';} elseif ($VALUE == '0'){$VALUE='<option value="2">Не знаю</option><option value="1">Да</option><option value="0" selected="">Нет</option>';} else {$VALUE='<option value="2">Не знаю</option><option value="1">Да</option><option value="0">Нет</option>';}
?><b><?=$V_NAME?></b><br><select style="width:450px" name="ATTR_<?=$V_TYPE?>_<?=$V_ATTRIBUT_ID?>"><?=$VALUE?></select><br><?
}
elseif($V_TYPE==7)
{
  $VALUE=$cmf->selectrow_array('select VALUE from ITEM7 where ITEM_ID=? and ATTRIBUT_ID=?',$_REQUEST['id'],$V_ATTRIBUT_ID);
?><b><?=$V_NAME?> <?=$V_UNIT_ID?></b><br><textarea name="ATTR_<?=$V_TYPE?>_<?=$V_ATTRIBUT_ID?>" cols="90" rows="3" onfocus="_XDOC=this;" onkeydown="_etaKey(event)"><?=$VALUE?></textarea><br><?
}
elseif($V_TYPE>2)
 {
  $VALUE=$cmf->selectrow_array('select VALUE from ITEM0 where ITEM_ID=? and ATTRIBUT_ID=?',$_REQUEST['id'],$V_ATTRIBUT_ID);
?><b><?=$V_NAME?> <?=$V_UNIT_ID?></b><br><select style="width:450px" name="ATTR_<?=$V_TYPE?>_<?=$V_ATTRIBUT_ID?>"><option value="">-- none --</option><?print $cmf->Spravotchnik($VALUE,'select ATTRIBUT_LIST_ID,NAME from ATTRIBUT_LIST where ATTRIBUT_ID=? order by NAME',$V_ATTRIBUT_ID); ?></select><br><?
 }
elseif($V_TYPE==2)
 {
  $VALUE=$cmf->selectrow_array("select VALUE from ITEM2 where ITEM_ID=? and ATTRIBUT_ID=?",$_REQUEST['id'],$V_ATTRIBUT_ID);
?><b><?=$V_NAME?> <?=$V_UNIT_ID?></b><br><input type="text" value="<?=$VALUE?>" name="ATTR_<?=$V_TYPE?>_<?=$V_ATTRIBUT_ID?>" size="90"><br><?
 }
else
 {
  $VALUE=$cmf->selectrow_array("select VALUE from ITEM$V_TYPE where ITEM_ID=? and ATTRIBUT_ID=?",$_REQUEST['id'],$V_ATTRIBUT_ID);
?><b><?=$V_NAME?> <?=$V_UNIT_ID?></b><br><input type="text" value="<?=$VALUE?>" name="ATTR_<?=$V_TYPE?>_<?=$V_ATTRIBUT_ID?>" size="90"><br><?
 }
}
print <<<EOF
</td></tr>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Назад" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;



print <<<EOF
<a name="f1"></a><h3 class="h3">Дополнительные фотографии</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="ITEM.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="4">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select ITEM_ITEM_ID,NAME,STATUS from ITEM_PHOTO where ITEM_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>Наименование</th><td></td></tr>
EOF;
while(list($V_ITEM_ITEM_ID,$V_NAME,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

@print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="iid[]" value="$V_ITEM_ITEM_ID" /></td>
<td>$V_ITEM_ITEM_ID</td><td>$V_NAME</td><td nowrap="">

<a href="ITEM.php?e1=ED&amp;iid=$V_ITEM_ITEM_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';



print <<<EOF
<a name="f2"></a><h3 class="h3">Медиа файлы</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="ITEM.php#f2" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="4">
<input type="submit" name="e2" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e2" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select ITEM_MEDIA_ID,NAME,STATUS from ITEM_MEDIA where ITEM_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>Наименование</th><td></td></tr>
EOF;
while(list($V_ITEM_MEDIA_ID,$V_NAME,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

@print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="iid[]" value="$V_ITEM_MEDIA_ID" /></td>
<td>$V_ITEM_MEDIA_ID</td><td>$V_NAME</td><td nowrap="">

<a href="ITEM.php?e2=ED&amp;iid=$V_ITEM_MEDIA_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';



print <<<EOF
<a name="f3"></a><h3 class="h3">Заявки</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="ITEM.php#f3" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="4">
<input type="submit" name="e3" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e3" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select ITEM_REQUEST_ID,EMAIL,STATUS from ITEM_REQUEST where ITEM_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>E-mail</th><td></td></tr>
EOF;
while(list($V_ITEM_REQUEST_ID,$V_EMAIL,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

@print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="iid[]" value="$V_ITEM_REQUEST_ID" /></td>
<td>$V_ITEM_REQUEST_ID</td><td>$V_EMAIL</td><td nowrap="">

<a href="ITEM.php?e3=ED&amp;iid=$V_ITEM_REQUEST_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';







@print <<<EOF
        <h3 class="h3"><a name="fl1"></a>Сопутствующие товары</h3>
        <table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
        <form action="ITEM.php#fl1" method="POST">
        <tr bgcolor="#FFFFFF">
                <td colspan="4" class="main_tbl_title">
                <input type="submit" name="el1" value="Новый" class="gbt bnew" /><img src="i/0.gif" width="4" height="1" />
                <input type="submit" onclick="return dl();" name="el1" value="Удалить" class="gbt bdel" />
                <input type="hidden" name="id" value="{$_REQUEST['id']}" />
                <input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
                <input type="hidden" name="p" value="{$_REQUEST['p']}" />
                
                </td>
        </tr>
EOF;



$sth=$cmf->execute('select CATALOGUE_ID,ITEM_ITEM_ID,STATUS from ITEM_ITEM where ITEM_ID=? ',$_REQUEST['id']);
?><tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'iid[]');" /></td><th>ИД рубрики</th><th>Связанный товар</th><td></td></tr><?
while(list($V_CATALOGUE_ID,$V_ITEM_ITEM_ID,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
        //$V_ITEM_ITEM_ID = $cmf->selectrow_arrayQ('select NAME from ITEM   where ITEM_ID=?',$V_ITEM_ITEM_ID);
        
$V_CATALOGUE_ID_STR=$cmf->selectrow_arrayQ('select NAME from CATALOGUE A   where A.CATALOGUE_ID=?',$V_CATALOGUE_ID);
        
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}
@print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="iid[]" value="{$V_ITEM_ITEM_ID}" /></td>
<td>$V_CATALOGUE_ID_STR</td><td><a href="ITEM.php?e=ED&amp;id=$V_ITEM_ITEM_ID" target="_blank">$V_ITEM_ITEM_ID</a></td><td>

<a href="ITEM.php?eventl1=ED&amp;iid=$V_ITEM_ITEM_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>

EOF;
}

print '</form></table>';


$visible=0;
}

if($_REQUEST['e'] =='Новый')
{
list($V_ITEM_ID,$V_CATALOGUE_ID,$V_TYPENAME,$V_CATNAME,$V_BRAND_ID,$V_DISCOUNT_ID,$V_WARRANTY_ID,$V_DELIVERY_ID,$V_CREDIT_ID,$V_NAME,$V_ARTICLE,$V_CURRENCY_ID,$V_PRICE,$V_PRICE1,$V_PURCHASE_PRICE,$V_IMAGE0,$V_IMAGE1,$V_IMAGE2,$V_IMAGE3,$V_IS_ACTION,$V_DESCRIPTION,$V_SEO_BOTTOM,$V_TITLE,$V_DESC_META,$V_KEYWORD_META,$V_DATE_INSERT,$V_STATUS)=array('','','','','','','','','','','','','','','','','','','','','','','','','','','');


$V_STR_BRAND_ID=$cmf->Spravotchnik($V_BRAND_ID,'select B.BRAND_ID,B.NAME from BRAND  B  order by B.NAME');
					
$V_STR_DISCOUNT_ID=$cmf->Spravotchnik($V_DISCOUNT_ID,'select DISCOUNT_ID,NAME from DISCOUNTS  order by NAME');
					
$V_STR_WARRANTY_ID=$cmf->Spravotchnik($V_WARRANTY_ID,'select WARRANTY_ID,NAME from WARRANTY  order by NAME');
					
$V_STR_DELIVERY_ID=$cmf->Spravotchnik($V_DELIVERY_ID,'select DELIVERY_ID,NAME from DELIVERY  order by NAME');
					
$V_STR_CREDIT_ID=$cmf->Spravotchnik($V_CREDIT_ID,'select CREDIT_ID,NAME from CREDIT  order by NAME');
					
$V_STR_CURRENCY_ID=$cmf->Spravotchnik($V_CURRENCY_ID,'select CURRENCY_ID,NAME from CURRENCY  order by NAME');
					
$IM_IMAGE0=array('','','');
$IM_IMAGE1=array('','','');
$IM_IMAGE2=array('','','');
$IM_IMAGE3=array('','','');
$V_IS_ACTION='checked';
$V_DATE_INSERT=$cmf->selectrow_array('select now()');
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Продукция</h2>
<a href="javascript:history.go(-1)">&#160;<b>вернуться</b></a><p />
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="ITEM.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(TYPENAME) &amp;&amp; checkXML(CATNAME) &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(ARTICLE) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(SEO_BOTTOM) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(DESC_META) &amp;&amp; checkXML(KEYWORD_META);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
EOF;



@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>Тип продукции:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TYPENAME" value="$V_TYPENAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Путь до каталога:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="CATNAME" value="$V_CATNAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Производитель:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="BRAND_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_BRAND_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Скидка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="DISCOUNT_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_DISCOUNT_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Гарантия:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="WARRANTY_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_WARRANTY_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Доставка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="DELIVERY_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_DELIVERY_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Кредит:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CREDIT_ID">
				
				
				
				
				<option value="0">- не задан -</option>
				
				$V_STR_CREDIT_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Наименование продукта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Артикул:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ARTICLE" value="$V_ARTICLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Валюта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CURRENCY_ID">
				
				
				
				
				
				
				$V_STR_CURRENCY_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цена:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PRICE" value="$V_PRICE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цена со скидкой:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PRICE1" value="$V_PRICE1" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цена закупки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PURCHASE_PRICE" value="$V_PURCHASE_PRICE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка мал. для поиска:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE0" value="$V_IMAGE0" />
EOF;
if(!empty($IM_IMAGE0[1])) $width = $IM_IMAGE0[1];
else $width = '';

if(!empty($IM_IMAGE0[2])) $height = $IM_IMAGE0[2];
else $height = '';

$IM_IMAGE0[0] = !empty($IM_IMAGE0[0]) ? $IM_IMAGE0[0]:0;
$IM_IMAGE0[1] = !empty($IM_IMAGE0[1]) ? $IM_IMAGE0[1]:0;
$IM_IMAGE0[2] = !empty($IM_IMAGE0[2]) ? $IM_IMAGE0[2]:0;

print <<<EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE0[0]" width="$IM_IMAGE0[1]" height="$IM_IMAGE0[2]" /></td>
<td><input type="file" name="NOT_IMAGE0" size="1" disabled="1" /><br />
<input type="checkbox" name="GEN_IMAGE0" value="1" checked="1" onClick="if(document.frm.GEN_IMAGE0.checked == '1') document.frm.NOT_IMAGE0.disabled='1'; else document.frm.NOT_IMAGE0.disabled='';" />Сгенерить из большой<br />
<input type="checkbox" name="CLR_IMAGE0" value="1" />Сбросить карт. <br />
Ширина превью: <input type="text" name="WIDTH_IMAGE0" size="5" value="$width" /><br />
Высота превью: <input type="text" name="HEIGHT_IMAGE0" size="5" value="$height" /><br />

</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка мал.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
EOF;
if(!empty($IM_IMAGE1[1])) $width = $IM_IMAGE1[1];
else $width = '';

if(!empty($IM_IMAGE1[2])) $height = $IM_IMAGE1[2];
else $height = '';

$IM_IMAGE1[0] = !empty($IM_IMAGE1[0]) ? $IM_IMAGE1[0]:0;
$IM_IMAGE1[1] = !empty($IM_IMAGE1[1]) ? $IM_IMAGE1[1]:0;
$IM_IMAGE1[2] = !empty($IM_IMAGE1[2]) ? $IM_IMAGE1[2]:0;

print <<<EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE1[0]" width="$IM_IMAGE1[1]" height="$IM_IMAGE1[2]" /></td>
<td><input type="file" name="NOT_IMAGE1" size="1" disabled="1" /><br />
<input type="checkbox" name="GEN_IMAGE1" value="1" checked="1" onClick="if(document.frm.GEN_IMAGE1.checked == '1') document.frm.NOT_IMAGE1.disabled='1'; else document.frm.NOT_IMAGE1.disabled='';" />Сгенерить из большой<br />
<input type="checkbox" name="CLR_IMAGE1" value="1" />Сбросить карт. <br />
Ширина превью: <input type="text" name="WIDTH_IMAGE1" size="5" value="$width" /><br />
Высота превью: <input type="text" name="HEIGHT_IMAGE1" size="5" value="$height" /><br />

</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка сред.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE2" value="$V_IMAGE2" />
EOF;
if(!empty($IM_IMAGE2[1])) $width = $IM_IMAGE2[1];
else $width = '';

if(!empty($IM_IMAGE2[2])) $height = $IM_IMAGE2[2];
else $height = '';

$IM_IMAGE2[0] = !empty($IM_IMAGE2[0]) ? $IM_IMAGE2[0]:0;
$IM_IMAGE2[1] = !empty($IM_IMAGE2[1]) ? $IM_IMAGE2[1]:0;
$IM_IMAGE2[2] = !empty($IM_IMAGE2[2]) ? $IM_IMAGE2[2]:0;

print <<<EOF
<table><tr><td>
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE2[0]" width="$IM_IMAGE2[1]" height="$IM_IMAGE2[2]" /></td>
<td><input type="file" name="NOT_IMAGE2" size="1" disabled="1" /><br />
<input type="checkbox" name="GEN_IMAGE2" value="1" checked="1" onClick="if(document.frm.GEN_IMAGE2.checked == '1') document.frm.NOT_IMAGE2.disabled='1'; else document.frm.NOT_IMAGE2.disabled='';" />Сгенерить из большой<br />
<input type="checkbox" name="CLR_IMAGE2" value="1" />Сбросить карт. <br />
Ширина превью: <input type="text" name="WIDTH_IMAGE2" size="5" value="$width" /><br />
Высота превью: <input type="text" name="HEIGHT_IMAGE2" size="5" value="$height" /><br />

</td></tr></table></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка бол.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE3" value="$V_IMAGE3" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE3[0]))
{
if(strchr($IM_IMAGE3[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE3[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE3[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE3[0] = !empty($IM_IMAGE3[0]) ? $IM_IMAGE3[0]:0;
$IM_IMAGE3[1] = !empty($IM_IMAGE3[1]) ? $IM_IMAGE3[1]:0;
$IM_IMAGE3[2] = !empty($IM_IMAGE3[2]) ? $IM_IMAGE3[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE3[0]" width="$IM_IMAGE3[1]" height="$IM_IMAGE3[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE3" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE3" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Участвует в акции:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_ACTION' value='1' $V_IS_ACTION/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="7" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>SEO текст внизу страницы:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<textarea id="SEO_BOTTOM" name="SEO_BOTTOM" rows="7" cols="90">
EOF;
$V_SEO_BOTTOM = htmlspecialchars_decode($V_SEO_BOTTOM);
echo $V_SEO_BOTTOM;
@print <<<EOF
</textarea>

<script type="text/javascript">
  CKEDITOR.replace( 'SEO_BOTTOM', {
      customConfig : 'ckeditor/config.js',
      filebrowserBrowseUrl : 'ckeditor/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Images',
      filebrowserFlashBrowseUrl : 'ckeditor/ckfinder/ckfinder.html?Type=Flash',
      filebrowserUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
      filebrowserImageUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
      filebrowserFlashUploadUrl : 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
      });
</script>

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тайтл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TITLE" value="$V_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Description:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESC_META" rows="7" cols="90">$V_DESC_META</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Ключевые слова:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="KEYWORD_META" rows="7" cols="90">$V_KEYWORD_META</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><td></td><td /></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата вставки в БД:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATE_INSERT" name="DATE_INSERT" value="$V_DATE_INSERT" />
EOF;

if($V_DATE_INSERT) $V_DAT_ = substr($V_DATE_INSERT,8,2).".".substr($V_DATE_INSERT,5,2).".".substr($V_DATE_INSERT,0,4)." ".substr($V_DATE_INSERT,11,2).":".substr($V_DATE_INSERT,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DATE_INSERT">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DATE_INSERT" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATE_INSERT",
                       displayArea    :    "DATE_DATE_INSERT",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATE_INSERT",
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

if(!empty($_REQUEST['pid']) and $_REQUEST['pid']!='all') $V_PARENTSCRIPTNAME=$cmf->selectrow_array('select NAME from CATALOGUE where CATALOGUE_ID=?',$_REQUEST['pid']);
else $V_PARENTSCRIPTNAME='';

print <<<EOF
<h2 class="h2">$V_PARENTSCRIPTNAME / Продукция</h2><form action="ITEM.php" method="POST">
<a href="CATALOGUE.php?e=RET&amp;id={$_REQUEST['pid']}&amp;p={$_REQUEST['p']}">
<img src="i/back.gif" border="0" align="top" /> Назад</a><br />
EOF;



if($_REQUEST['s'] == ''){$_REQUEST['s']=1;}
$_REQUEST['s']+=0;
$SORTNAMES=array('N','Тип продукции','Производитель','Наименование продукта','Артикул','Валюта','Цена','Цена со скидкой','Дата вставки в БД','Вкл');
$SORTQUERY=array('order by A.ITEM_ID ','order by A.ITEM_ID desc ','order by A.TYPENAME ','order by A.TYPENAME desc ','order by B.NAME,A.NAME ','order by B.NAME desc,A.NAME desc ','order by A.NAME ','order by A.NAME desc ','order by A.ARTICLE ','order by A.ARTICLE desc ','order by A.CURRENCY_ID ','order by A.CURRENCY_ID desc ','order by A.PRICE ','order by A.PRICE desc ','order by A.PRICE1 ','order by A.PRICE1 desc ','order by A.DATE_INSERT ','order by A.DATE_INSERT desc ','order by A.STATUS ','order by A.STATUS desc ');

//Ручные фильтры
$filters = '';
$filt_request = '';
foreach($_REQUEST as $key=>$val)
{
  if(preg_match('/^FILTER_(.+)$/',$key,$p))
  {
    if($val!='')
     {
        $filters.='&amp;'.$key.'='.$val;
     }
  }
}

list($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ITEM.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filtpath}{$filters}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ITEM.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filtpath}{$filters}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ITEM.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filtpath}{$filters}">$tmp</a></th>
EOF;
        }
        $i++;
}


$pagesize=35;

if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}

if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{
if(!empty($_REQUEST['pid']) and $_REQUEST['pid']=='all')
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ITEM A left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.CATALOGUE_ID > 0'
.($cmf->Param('FLT_BRAND_ID')?' and A.BRAND_ID='.mysql_escape_string($cmf->Param('FLT_BRAND_ID')):'')
.($cmf->Param('FLT_NAME')?' and A.NAME like \''.mysql_escape_string($_REQUEST['FLT_NAME'].'%')."'":'')
.($cmf->Param('FLT_ARTICLE')?' and A.ARTICLE like \''.mysql_escape_string($_REQUEST['FLT_ARTICLE'].'%')."'":'')
.($cmf->Param('FLT_PRICE')==1?" and (A.PRICE> '0') ":'')
.($cmf->Param('FLT_STATUS')!=''?' and A.STATUS='.mysql_escape_string($cmf->Param('FLT_STATUS')):''),$_REQUEST['pid']);
}
else
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ITEM A left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.CATALOGUE_ID=?'
.($cmf->Param('FLT_BRAND_ID')?' and A.BRAND_ID='.mysql_escape_string($cmf->Param('FLT_BRAND_ID')):'')
.($cmf->Param('FLT_NAME')?' and A.NAME like \''.mysql_escape_string($_REQUEST['FLT_NAME'].'%')."'":'')
.($cmf->Param('FLT_ARTICLE')?' and A.ARTICLE like \''.mysql_escape_string($_REQUEST['FLT_ARTICLE'].'%')."'":'')
.($cmf->Param('FLT_PRICE')==1?" and (A.PRICE> '0') ":'')
.($cmf->Param('FLT_STATUS')!=''?' and A.STATUS='.mysql_escape_string($cmf->Param('FLT_STATUS')):''),$_REQUEST['pid']);

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
- <a class="t" href="ITEM.php?pid={$_REQUEST['pid']}&amp;count={$_REQUEST['count']}&amp;p={$i}&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}{$filtpath}{$filters}">$i</a>
EOF;
  }
 }
print <<<EOF
&#160;из <span class="red">({$_REQUEST['pcount']})</span><br />
EOF;
}

if(!empty($_REQUEST['pid']) and $_REQUEST['pid'] == 'all')
{
$sth=$cmf->execute('select A.ITEM_ID,A.TYPENAME,B.NAME,A.NAME,A.ARTICLE,A.CURRENCY_ID,A.PRICE,A.PRICE1,A.IS_ACTION,DATE_FORMAT(A.DATE_INSERT,"%Y-%m-%d %H:%i"),A.STATUS from ITEM A left join BRAND B on (A.BRAND_ID=B.BRAND_ID)
where A.CATALOGUE_ID > 0 '
.($cmf->Param('FLT_BRAND_ID')?' and A.BRAND_ID='.mysql_escape_string($cmf->Param('FLT_BRAND_ID')):'')
.($cmf->Param('FLT_NAME')?' and A.NAME like \''.mysql_escape_string($_REQUEST['FLT_NAME'].'%')."'":'')
.($cmf->Param('FLT_ARTICLE')?' and A.ARTICLE like \''.mysql_escape_string($_REQUEST['FLT_ARTICLE'].'%')."'":'')
.($cmf->Param('FLT_PRICE')==1?" and (A.PRICE> '0') ":'')
.($cmf->Param('FLT_STATUS')!=''?' and A.STATUS='.mysql_escape_string($cmf->Param('FLT_STATUS')):'').' '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);
}
else
{
$sth=$cmf->execute('select A.ITEM_ID,A.TYPENAME,B.NAME,A.NAME,A.ARTICLE,A.CURRENCY_ID,A.PRICE,A.PRICE1,A.IS_ACTION,DATE_FORMAT(A.DATE_INSERT,"%Y-%m-%d %H:%i"),A.STATUS from ITEM A left join BRAND B on (A.BRAND_ID=B.BRAND_ID)
where A.CATALOGUE_ID=? '
.($cmf->Param('FLT_BRAND_ID')?' and A.BRAND_ID='.mysql_escape_string($cmf->Param('FLT_BRAND_ID')):'')
.($cmf->Param('FLT_NAME')?' and A.NAME like \''.mysql_escape_string($_REQUEST['FLT_NAME'].'%')."'":'')
.($cmf->Param('FLT_ARTICLE')?' and A.ARTICLE like \''.mysql_escape_string($_REQUEST['FLT_ARTICLE'].'%')."'":'')
.($cmf->Param('FLT_PRICE')==1?" and (A.PRICE> '0') ":'')
.($cmf->Param('FLT_STATUS')!=''?' and A.STATUS='.mysql_escape_string($cmf->Param('FLT_STATUS')):'').' '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$_REQUEST['pid'],$pagesize*($_REQUEST['p']-1),$pagesize);

}




$V_STR_BRAND_ID=$cmf->Spravotchnik($cmf->Param('FLT_BRAND_ID'),'select B.BRAND_ID,B.NAME from BRAND  B INNER JOIN ITEM I on I.BRAND_ID=B.BRAND_ID where B.STATUS=1 and I.CATALOGUE_ID = '.$_REQUEST['pid'].' group by B.NAME order by B.NAME');
if($cmf->Param('FLT_PRICE') == '1') {$V_PRICE='checked';}

switch($cmf->Param('FLT_STATUS'))
{
   case '1':
                $V_STATUS='checked';
                break;
}

@print <<<EOF
<table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
<input type="hidden" name="type" value="3" />
<tr bgcolor="#F0F0F0"><td colspan="2"><input type="submit" name="e" value="Фильтр" class="gbt bflt" /></td></tr>
<tr bgcolor="#FFFFFF"><th>Производитель<br /><img src="i/0.gif" width="125" height="1" /></th><td><select name="FLT_BRAND_ID"><option value="0">--------</option>{$V_STR_BRAND_ID}</select><br /></td></tr><tr bgcolor="#FFFFFF"><th>Наименование продукта<br /><img src="i/0.gif" width="125" height="1" /></th><td><input class="form_input_big" type="text" name="FLT_NAME" size="90" value="{$_REQUEST['FLT_NAME']}" /><br /></td></tr><tr bgcolor="#FFFFFF"><th>Артикул<br /><img src="i/0.gif" width="125" height="1" /></th><td><input class="form_input_big" type="text" name="FLT_ARTICLE" size="90" value="{$_REQUEST['FLT_ARTICLE']}" /><br /></td></tr><tr bgcolor="#FFFFFF"><th>Ненулевая цена<br /><img src="i/0.gif" width="125" height="1" /></th><td><input type='checkbox' name='FLT_PRICE' value='1' $V_PRICE /><br /></td></tr><tr bgcolor="#FFFFFF"><th>Выводить активные позиции<br /><img src="i/0.gif" width="125" height="1" /></th><td><input type='checkbox' name='FLT_STATUS' value='1' $V_STATUS /></td></tr>
</table>
EOF;



@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="12">
EOF;

if ($cmf->W)
@print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" /><input type="submit" name="e" value="Применить" class="gbt bsave" /><img src="i/0.gif" width="4" height="1" /><input type="submit" name="e" value="Переместить" class="gbt bmv" /><input type="submit" name="e" value="Скопировать" class="gbt bcopy" /><input type="submit" name="e" value="Переместить все" class="gbt bmv" /><input type="submit" name="e" value="Скопировать все" class="gbt bcopy" /><input type="submit" name="e" value="Диапазоны" class="gbt branges" />
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
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td>$HEADER<td></td></tr>
EOF;
$TABposition=1;

if($sth)
while(list($V_ITEM_ID,$V_TYPENAME,$V_BRAND_ID,$V_NAME,$V_ARTICLE,$V_CURRENCY_ID,$V_PRICE,$V_PRICE1,$V_IS_ACTION,$V_DATE_INSERT,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{$TABposition++;



$V_CURRENCY_ID=$cmf->selectrow_arrayQ('select NAME from CURRENCY where CURRENCY_ID=?',$V_CURRENCY_ID);
                                        
$V_STR_STATUS=$V_STATUS?'checked':'';
                        

if($V_STATUS == 1){$V_COLOR='#FFFFFF';} else {$V_COLOR='#a0a0a0';}



@print <<<EOF
<tr bgcolor="$V_COLOR">
<td><input type="checkbox" name="id[]" value="$V_ITEM_ID" /></td>
<td>$V_ITEM_ID</td><td>$V_TYPENAME</td><td>$V_BRAND_ID</td><td><a class="b" href="RESPONSES.php?pid=$V_ITEM_ID&amp;p={$_REQUEST['p']}">$V_NAME</a></td><td>$V_ARTICLE</td><td>$V_CURRENCY_ID</td><td>$V_PRICE</td><td>$V_PRICE1</td><td>$V_DATE_INSERT</td><td><input onclick="ch(this)" type="checkbox" name="STATUS_$V_ITEM_ID" class="i" value="1" $V_STR_STATUS/></td><td nowrap="">
EOF;

if ($cmf->W)
@print <<<EOF
<a href="ITEM.php?e=ED&amp;id=$V_ITEM_ID&amp;pid={$_REQUEST['pid']}&amp;p={$_REQUEST['p']}&amp;s={$_REQUEST['s']}{$filtpath}{$filters}"><img src="i/ed.gif" border="0" title="Изменить" /></a>

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

function GetTree($cmf,$id)
{
$sth=$cmf->execute('select CATALOGUE_ID,NAME from CATALOGUE where PARENT_ID=? order by ORDERING',$id);
if($sth)
{
$ret='<ul>';
while(list($V_CATALOGUE_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.=<<<EOF
<dl><input type="radio" name="cid" value="$V_CATALOGUE_ID">&#160;$V_NAME</dl>
EOF
.GetTree($cmf,$V_CATALOGUE_ID);
}
$ret.='</ul>';
}
return $ret;
}

function getChildren($cmf,$id)
{
  $path = array();
  $sth=$cmf->execute('select CATALOGUE_ID from CATALOGUE where PARENT_ID=? and STATUS=1 order by CATALOGUE_ID',$id);
  if(mysql_num_rows($sth))
  {
     while($row=mysql_fetch_array($sth))
     {
       if($row['CATALOGUE_ID']>0)
       {
          $path[] = $row['CATALOGUE_ID'];
          $path = array_merge($path,getChildren($row['CATALOGUE_ID']));
       }
     }
  }
  return $path;
}

function getItems($cmf,$where)
{
   $items = array();
   $sth = $cmf->execute("select ITEM_ID from ITEM where 1 ".$where);
   if(mysql_num_rows($sth))
   {
      while($row=mysql_fetch_array($sth))
      {
         $items[] = $row['ITEM_ID'];
      }
   }
   return $items;
}

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



function ___GetTree($cmf,$pid,$id)
{
$id+=0;
$ret='';
$sth=$cmf->execute('select CATALOGUE_ID,NAME from CATALOGUE where CATALOGUE_ID=? order by ORDERING',$pid);
while(list($V_CATALOGUE_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.='<li>'.($id==$V_CATALOGUE_ID?'<input type="radio" name="cid" value="'.$V_CATALOGUE_ID.'" disabled="yes" />':'<input type="radio" name="cid" value="'.$V_CATALOGUE_ID.'" />')."&#160;$V_NAME</li>".___GetTree($cmf,$V_CATALOGUE_ID,$id);
}
if ($ret) {$ret="<ul>${ret}</ul>";}
return $ret;
}

?>
