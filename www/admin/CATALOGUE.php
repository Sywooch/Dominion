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
if(!isset($_REQUEST['r']))$_REQUEST['r']=0;
if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['event']))$_REQUEST['event']='';
if(!isset($_REQUEST['id']))$_REQUEST['id']='';
if(!isset($_REQUEST['pid']))$_REQUEST['pid']=0;
if(!isset($_REQUEST['f']))$_REQUEST['f']='';
$VIRTUAL_IMAGE_PATH="/cat/";







		
if($_REQUEST['e'] == 'Excel'){
  define("TMP_DIR", dirname(__FILE__) . "/tmp");
  require_once 'exelExportImport/excelExportData.php';
  $export = excelExportData::export($_REQUEST['id']);
  $export->getFile('ExportItems');
}		

if(!isset($V_PARENT_ID)) $V_PARENT_ID=0;

if($_REQUEST['e'] == 'Перестроить')
{
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

$sth2 = $cmf->execute("select distinct ITEM_ID from ALIASES where ITEM_ID>0");
while($rw2 = mysql_fetch_array($sth2))
{
   $count = $cmf->selectrow_array("select COUNT(*) from ITEM where ITEM_ID=?",$rw2['ITEM_ID']);
   if($count == 0) $cmf->execute('delete from ALIASES where ITEM_ID=?',$rw2['ITEM_ID']);
}

}

if($_REQUEST['e'] == 'Установить')
{
        $cmf->execute('delete from ATTR_CATALOG_LINK where CATALOGUE_ID=?',$cmf->Param('id'));
        if(isset($_REQUEST['ATR']))
        foreach ($_REQUEST['ATR'] as $id)
        {
            $inPodbor = isset($_REQUEST["n{$id}"])? $_REQUEST["n{$id}"]:0;
            $cmf->execute('insert into ATTR_CATALOG_LINK(CATALOGUE_ID,ATTRIBUT_ID,IN_PODBOR) values(?,?,?)',$_REQUEST['id'],$id,$inPodbor);
        }
}

if($_REQUEST['e'] == 'Применить')
{
$cmf->execute('delete from ATTR_CATALOG_VIS where CATALOGUE_ID=?',$_REQUEST['id']);
if(isset($_REQUEST['ATR']))
foreach ($_REQUEST['ATR'] as $id)
{
$cmf->execute('insert into ATTR_CATALOG_VIS(CATALOGUE_ID,ATTRIBUT_ID) values(?,?)',$_REQUEST['id'],$id);
}
}

if($_REQUEST['e'] == 'DL')
{
DelTree($cmf,$_REQUEST['id']);
$cmf->execute('delete from CATALOGUE where CATALOGUE_ID=?',$_REQUEST['id']);
updateOrdering($cmf,0);
}


if($_REQUEST['e'] == 'FT')
{
?><h2 class="h2">Полный набор атрибутов</h2>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="admin_attr.js"></script>
<table class="attr_table" bgcolor="#cccccc" border="0" cellpadding="3" cellspacing="1">
<form action=CATALOGUE.php method=POST>
<input type="hidden" name="id" value="<?=$_REQUEST['id']?>">
<tr bgcolor="#F0F0F0" class="ftr"><td><input type="submit" name="e" value="Установить" class="gbt bsave"/><input type="submit" name="event" value="Отменить" class="gbt bcancel"/></td></tr>
<tr bgcolor="#FFFFFF"><td>
        <div id="all_attr">

<?
  $sth=$cmf->execute('select ATTRIBUT_GROUP_ID,NAME from ATTRIBUT_GROUP order by ORDERING');
  while(list($V_ATTRIBUT_GROUP_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
  {
      
   $i=0;
        list($ATTRS,$FLAG)=array('','');
        $sth1=$cmf->execute('select A.ATTRIBUT_ID,A.NAME,U.NAME from ATTRIBUT A left join UNIT U on (A.UNIT_ID=U.UNIT_ID) where ATTRIBUT_GROUP_ID=? order by A.ORDERING',$V_ATTRIBUT_GROUP_ID);
        while(list($VV_ATTRIBUT_ID,$VV_NAME,$VV_UNIT)=mysql_fetch_array($sth1, MYSQL_NUM))
        {
                if($i==0){$ATTRS.='<tr>';}
                list($VV_STATE,$IN_PODBOR)=$cmf->selectrow_array("select 'checked',IN_PODBOR from ATTR_CATALOG_LINK where CATALOGUE_ID=? and ATTRIBUT_ID=?",$_REQUEST['id'],$VV_ATTRIBUT_ID);

                if($VV_STATE)$FLAG='active';
                $active1 = $IN_PODBOR == '3'? 'class="active"':'';    // Название + значение
                $active1 = $IN_PODBOR == '2'? 'class="active"':'';    // Значени атрибута
                $active2 = $IN_PODBOR == '1'? 'class="active"':'';    // Название атрибута
                $active3 = ($IN_PODBOR == '0')? 'class="active"':'';    // для 'не участвует'

                $checked1 = $IN_PODBOR == '3'? 'checked="checked"':'';    // Название + значение
                $checked1 = $IN_PODBOR == '2'? 'checked="checked"':'';    // Значени атрибута
                $checked2 = $IN_PODBOR == '1'? 'checked="checked"':'';    // Название атрибута
                $checked3 = ($IN_PODBOR == '0' || (empty($checked1) && empty($checked2)))? 'checked="checked"':'';    // для 'не участвует'

                $activeOne = $active1=='' && $active2=='' && $active3==''? '':'class="active"';
                $disable = $active1=='' && $active2=='' && $active3==''? 'disabled="disabled"':'';
                $ATTRS.="<td><input type=checkbox name='ATR[]' id='id_chk$VV_ATTRIBUT_ID' value='$VV_ATTRIBUT_ID' $VV_STATE/>
                        <label for='id_chk$VV_ATTRIBUT_ID'>$VV_NAME, $VV_UNIT</label>
                        <fieldset title='Параметры активности атрибута' $activeOne>
                            <p class='d3'>
                              <input type='radio' $disable id='n{$VV_ATTRIBUT_ID}_3' value='0' $checked3 name='n$VV_ATTRIBUT_ID'>
                              <label for='n{$VV_ATTRIBUT_ID}_3' $active3>Не участвует</label>
                            </p>
                            <p class='d1'>
                              <input type='radio' $disable id='n{$VV_ATTRIBUT_ID}_1' $checked1 value='1' name='n$VV_ATTRIBUT_ID'>
                              <label for='n{$VV_ATTRIBUT_ID}_1' $active1>Название атрибута</label>
                            </p>
                            <p class='d2'>
                              <input type='radio' $disable id='n{$VV_ATTRIBUT_ID}_2' $checked2 value='2' name='n$VV_ATTRIBUT_ID'>
                              <label for='n{$VV_ATTRIBUT_ID}_2' $active2>Значени атрибута</label>
                            </p>
                            <p class='d4'>
                              <input type='radio' $disable id='n{$VV_ATTRIBUT_ID}_4' $checked2 value='3' name='n$VV_ATTRIBUT_ID'>
                              <label for='n{$VV_ATTRIBUT_ID}_4' $active2>Название + значение</label>
                            </p>
                            
                        </fieldset>
                </td>";
                $i++;
                if($i>2){$i=0;$ATTRS.='</tr>';}
        }

        ?>
            <div class="attr">
                <a class="attr_btn <?=$FLAG?>"><span><?=$V_NAME?></span></a>
                <div class="attr_hold">
                    <table>
                        <?=$ATTRS?>
                    </table>
                </div>
            </div>
            <?
  }
?></div></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td><input type="submit" name="e" value="Установить" class="gbt bsave"/><input type="submit" name="event" value="Отменить" class="gbt bcancel"/></td></tr>
</form></table><?
  $visible=0;
}


if($_REQUEST['e'] == 'FV')
{
$i=0;
?><h2 class="h2">Укороченный набор атрибутов</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="3" cellspacing="1" class="f">
<form action=CATALOGUE.php method=POST>
<input type="hidden" name="id" value="<?=$_REQUEST['id']?>">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="3"><input type="submit" name="e" value="Применить" class="gbt bsave"/><input type="submit" name="event" value="Отменить" class="gbt bcancel"/></td></tr>
<?

$sth=$cmf->execute('select ACL.ATTRIBUT_ID,A.NAME,U.NAME from ATTR_CATALOG_LINK ACL,ATTRIBUT A left join UNIT U on (A.UNIT_ID=U.UNIT_ID) where ACL.CATALOGUE_ID=? and ACL.ATTRIBUT_ID=A.ATTRIBUT_ID order by A.ORDERING',$_REQUEST['id']);
while(list ($V_ATTRIBUT_ID,$V_NAME,$V_UNIT)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($i==0){print '<tr bgcolor="#FFFFFF">';}
$state=$cmf->selectrow_array("select 'checked' from ATTR_CATALOG_VIS where CATALOGUE_ID=? and ATTRIBUT_ID=?",$_REQUEST['id'],$V_ATTRIBUT_ID);
print "<td><input type=checkbox name='ATR[]' value='$V_ATTRIBUT_ID' $state>&#160;$V_NAME,$V_UNIT</td>";
$i++;
if($i>2){$i=0;print '</tr>';}
}
if($i!=0){$i=3-$i;print "<td colspan='$i'></td></tr>";}
?><tr bgcolor="#F0F0F0" class="ftr"><td colspan="3"><input type="submit" name="e" value="Применить" class="gbt bsave"/><input type="submit" name="event" value="Отменить" class="gbt bcancel"/></td></tr>
</form></table><?
$visible=0;
}



if(!isset($_REQUEST['e1']))$_REQUEST['e1']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e1') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from CATALOGUE_PRICE_EXPORT where CATALOGUE_ID=? and CATALOGUE_PRICE_EXPORT_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e1') == 'Изменить')
{




$cmf->execute('update CATALOGUE_PRICE_EXPORT set PRICE_EXPORT_ID=? where CATALOGUE_ID=? and CATALOGUE_PRICE_EXPORT_ID=?',stripslashes($_REQUEST['PRICE_EXPORT_ID'])+0,$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{


$_REQUEST['iid']=$cmf->GetSequence('CATALOGUE_PRICE_EXPORT');






$cmf->execute('insert into CATALOGUE_PRICE_EXPORT (CATALOGUE_ID,CATALOGUE_PRICE_EXPORT_ID,PRICE_EXPORT_ID) values (?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['PRICE_EXPORT_ID'])+0);
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_CATALOGUE_PRICE_EXPORT_ID,$V_PRICE_EXPORT_ID)=$cmf->selectrow_arrayQ('select CATALOGUE_PRICE_EXPORT_ID,PRICE_EXPORT_ID from CATALOGUE_PRICE_EXPORT where CATALOGUE_ID=? and CATALOGUE_PRICE_EXPORT_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_PRICE_EXPORT_ID=$cmf->Spravotchnik($V_PRICE_EXPORT_ID,'select PRICE_EXPORT_ID,NAME from PRICE_EXPORT  order by NAME');        
					
@print <<<EOF
<h2 class="h2">Редактирование - Связь Каталог-Экспортная группа</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CATALOGUE.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
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
<tr bgcolor="#FFFFFF"><th width="1%"><b>Преобразование в:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="PRICE_EXPORT_ID">
				
				
				
				
				
				
				$V_STR_PRICE_EXPORT_ID
			</select><br />
		
	

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
list($V_CATALOGUE_PRICE_EXPORT_ID,$V_PRICE_EXPORT_ID)=array('','');


$V_STR_PRICE_EXPORT_ID=$cmf->Spravotchnik($V_PRICE_EXPORT_ID,'select PRICE_EXPORT_ID,NAME from PRICE_EXPORT  order by NAME');
					
@print <<<EOF
<h2 class="h2">Добавление - Связь Каталог-Экспортная группа</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CATALOGUE.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Преобразование в:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="PRICE_EXPORT_ID">
				
				
				
				
				
				
				$V_STR_PRICE_EXPORT_ID
			</select><br />
		
	

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

if(!isset($_REQUEST['e2']))$_REQUEST['e2']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e2') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from CATALOGUE_BRAND_VIEW where CATALOGUE_ID=? and CATALOGUE_BRAND_VIEW_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e2') == 'Изменить')
{




$cmf->execute('update CATALOGUE_BRAND_VIEW set BRAND_ID=? where CATALOGUE_ID=? and CATALOGUE_BRAND_VIEW_ID=?',stripslashes($_REQUEST['BRAND_ID'])+0,$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e2') == 'Добавить')
{


$_REQUEST['iid']=$cmf->GetSequence('CATALOGUE_BRAND_VIEW');






$cmf->execute('insert into CATALOGUE_BRAND_VIEW (CATALOGUE_ID,CATALOGUE_BRAND_VIEW_ID,BRAND_ID) values (?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['BRAND_ID'])+0);
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e2') == 'ED')
{
list ($V_CATALOGUE_BRAND_VIEW_ID,$V_BRAND_ID)=$cmf->selectrow_arrayQ('select CATALOGUE_BRAND_VIEW_ID,BRAND_ID from CATALOGUE_BRAND_VIEW where CATALOGUE_ID=? and CATALOGUE_BRAND_VIEW_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_BRAND_ID=$cmf->Spravotchnik($V_BRAND_ID,'select A.BRAND_ID,A.NAME from BRAND A  join CAT_BRAND CB on (CB.BRAND_ID = A.BRAND_ID) where CB.COUNT_ > 0 and CB.CATALOGUE_ID = '.$_REQUEST['id'].' order by A.NAME');        
					
@print <<<EOF
<h2 class="h2">Редактирование - Связь "Категория товаров - Бренды"</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CATALOGUE.php#f2" ENCTYPE="multipart/form-data" onsubmit="return true ;">
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
<tr bgcolor="#FFFFFF"><th width="1%"><b>Рубрика каталога:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="BRAND_ID">
				
				
				
				
				
				
				$V_STR_BRAND_ID
			</select><br />
		
	

</td></tr>
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
list($V_CATALOGUE_BRAND_VIEW_ID,$V_BRAND_ID)=array('','');


$V_STR_BRAND_ID=$cmf->Spravotchnik($V_BRAND_ID,'select A.BRAND_ID,A.NAME from BRAND A  join CAT_BRAND CB on (CB.BRAND_ID = A.BRAND_ID) where CB.COUNT_ > 0 and CB.CATALOGUE_ID = '.$_REQUEST['id'].' order by A.NAME');
					
@print <<<EOF
<h2 class="h2">Добавление - Связь "Категория товаров - Бренды"</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CATALOGUE.php#f2" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Рубрика каталога:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="BRAND_ID">
				
				
				
				
				
				
				$V_STR_BRAND_ID
			</select><br />
		
	

</td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}




if($_REQUEST['e'] == 'DL')
{
DelTree($cmf,$_REQUEST['id']);
$cmf->execute('delete from CATALOGUE where CATALOGUE_ID=?',$_REQUEST['id']);
}

if($_REQUEST['e'] == 'VS')
{
$STATUS=$cmf->selectrow_array('select STATUS from CATALOGUE where CATALOGUE_ID=?',$_REQUEST['id']);
$STATUS=1-$STATUS;
$cmf->execute('update CATALOGUE set STATUS=? where CATALOGUE_ID=?',$STATUS,$_REQUEST['id']);
if($STATUS)
{
$cmf->execute('update CATALOGUE set REALSTATUS=1 where CATALOGUE_ID=?',$_REQUEST['id']);
SetTreeRealStatus($cmf,$_REQUEST['id'],1);
}
else
{
$REALSTATUS=GetMyRealStatus($cmf,$_REQUEST['id']);
$cmf->execute('update CATALOGUE set REALSTATUS=? where CATALOGUE_ID=?',$REALSTATUS,$_REQUEST['id']);
SetTreeRealStatus($cmf,$_REQUEST['id'],$REALSTATUS);
}
}

if($_REQUEST['e'] == 'UP')
{
list($V_PARENT_ID,$V_ORDERING) =$cmf->selectrow_array('select PARENT_ID,ORDERING from CATALOGUE where CATALOGUE_ID=?',$_REQUEST['id']);
if($V_ORDERING > 1)
{

$sql="select CATALOGUE_ID
           , ORDERING
      from CATALOGUE
      where ORDERING < {$V_ORDERING}
            and PARENT_ID = {$V_PARENT_ID}
      order by ORDERING DESC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf->selectrow_array($sql);


$cmf->execute('update CATALOGUE set ORDERING=? where CATALOGUE_ID=?',$V_ORDERING,$V_OTHER_ID);
$cmf->execute('update CATALOGUE set ORDERING=? where CATALOGUE_ID=?',$V_OTHER_ORDERING, $_REQUEST['id']);

}
}

if($_REQUEST['e'] == 'DN')
{
list($V_PARENT_ID,$V_ORDERING) =$cmf->selectrow_array('select PARENT_ID,ORDERING from CATALOGUE where CATALOGUE_ID=?',$_REQUEST['id']);
$V_MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from CATALOGUE where PARENT_ID=?',$V_PARENT_ID);
if($V_ORDERING < $V_MAXORDERING)
{

$sql="select CATALOGUE_ID
           , ORDERING
      from CATALOGUE
      where ORDERING > {$V_ORDERING}
            and PARENT_ID = {$V_PARENT_ID}
      order by ORDERING ASC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf->selectrow_array($sql);


$cmf->execute('update CATALOGUE set ORDERING=? where CATALOGUE_ID=?',$V_ORDERING,$V_OTHER_ID);
$cmf->execute('update CATALOGUE set ORDERING=? where CATALOGUE_ID=?',$V_OTHER_ORDERING, $_REQUEST['id']);
}
}

if($_REQUEST['event'] == 'Добавить')
{

if(!empty($_REQUEST['pid']))
{
  $_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from CATALOGUE where PARENT_ID=?',$_REQUEST['pid']);
  $_REQUEST['ORDERING']++;
  $_REQUEST['id']=$cmf->GetSequence('CATALOGUE');
  














		
				
    if(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_img1',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_img1',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_img1',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

$_REQUEST['IN_ADV']=isset($_REQUEST['IN_ADV']) && $_REQUEST['IN_ADV']?1:0;
$_REQUEST['IS_INDEX']=isset($_REQUEST['IS_INDEX']) && $_REQUEST['IS_INDEX']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;
$_REQUEST['REALSTATUS']=isset($_REQUEST['REALSTATUS']) && $_REQUEST['REALSTATUS']?1:0;


  $cmf->execute('insert into CATALOGUE (CATALOGUE_ID,PARENT_ID,NAME,ID_FROM_VBD,TYPENAME,CATNAME,REALCATNAME,URL,SUB_ITEM_TITLE,TITLE,SUB_TITLE,DESC_META,KEYWORD_META,DESCRIPTION,IMAGE1,COUNT_,IN_ADV,IS_INDEX,STATUS,REALSTATUS,ORDERING) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['pid']+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ID_FROM_VBD'])+0,stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['CATNAME']),'',stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['SUB_ITEM_TITLE']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['SUB_TITLE']),stripslashes($_REQUEST['DESC_META']),stripslashes($_REQUEST['KEYWORD_META']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IMAGE1']),0,stripslashes($_REQUEST['IN_ADV']),stripslashes($_REQUEST['IS_INDEX']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['REALSTATUS']),stripslashes($_REQUEST['ORDERING']));
  
  
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
        
        $cmf->execute('update CATALOGUE set CATNAME=? where CATALOGUE_ID=?',$_REQUEST['CATNAME'] ,$_REQUEST['id']);
        
      }
  
      $cmf->execute('update CATALOGUE set REALSTATUS=?,REALCATNAME=? where CATALOGUE_ID=?',GetMyRealStatus($cmf,$_REQUEST['id']),GetPath($cmf,$_REQUEST['id']),$_REQUEST['id']);
      $cmf->CheckCount(0);
      
      require $_SERVER['DOCUMENT_ROOT']."/lib/CreateSEFU.class.php";
      $sefu = new CreateSEFU();
      $sefu->_applySEFUCatalogue($_REQUEST['id']);
      $sefu->_applySEFUCatalogueBrand($_REQUEST['id']);
      
      require $_SERVER['DOCUMENT_ROOT']."/lib/MetaGenerate.php";
      require $_SERVER['DOCUMENT_ROOT']."/lib/MetaGenerateModelStrategy.php";
      
      $model = MetaGenerateModelStrategy::getModel($cmf);
      $meta = new MetaGenerate($model);

      $meta->metaCatalogueById($_REQUEST['id']);
      
    
}
else
{
  $_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from CATALOGUE where PARENT_ID=?',0);
  $_REQUEST['ORDERING']++;
  $_REQUEST['id']=$cmf->GetSequence('CATALOGUE');
  














		
				
    if(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_img1',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_img1',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_img1',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

$_REQUEST['IN_ADV']=isset($_REQUEST['IN_ADV']) && $_REQUEST['IN_ADV']?1:0;
$_REQUEST['IS_INDEX']=isset($_REQUEST['IS_INDEX']) && $_REQUEST['IS_INDEX']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;
$_REQUEST['REALSTATUS']=isset($_REQUEST['REALSTATUS']) && $_REQUEST['REALSTATUS']?1:0;


  $_REQUEST['pid'] = (!empty($_REQUEST['PARENT_ID'])) ? $_REQUEST['PARENT_ID'] : 0;
  $cmf->execute('insert into CATALOGUE (CATALOGUE_ID,PARENT_ID,NAME,ID_FROM_VBD,TYPENAME,CATNAME,REALCATNAME,URL,SUB_ITEM_TITLE,TITLE,SUB_TITLE,DESC_META,KEYWORD_META,DESCRIPTION,IMAGE1,COUNT_,IN_ADV,IS_INDEX,STATUS,REALSTATUS,ORDERING) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['pid']+0,stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ID_FROM_VBD'])+0,stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['CATNAME']),'',stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['SUB_ITEM_TITLE']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['SUB_TITLE']),stripslashes($_REQUEST['DESC_META']),stripslashes($_REQUEST['KEYWORD_META']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IMAGE1']),0,stripslashes($_REQUEST['IN_ADV']),stripslashes($_REQUEST['IS_INDEX']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['REALSTATUS']),stripslashes($_REQUEST['ORDERING']));
  
  
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
        
        $cmf->execute('update CATALOGUE set CATNAME=? where CATALOGUE_ID=?',$_REQUEST['CATNAME'] ,$_REQUEST['id']);
        
      }
  
      $cmf->execute('update CATALOGUE set REALSTATUS=?,REALCATNAME=? where CATALOGUE_ID=?',GetMyRealStatus($cmf,$_REQUEST['id']),GetPath($cmf,$_REQUEST['id']),$_REQUEST['id']);
      $cmf->CheckCount(0);
      
      require $_SERVER['DOCUMENT_ROOT']."/lib/CreateSEFU.class.php";
      $sefu = new CreateSEFU();
      $sefu->_applySEFUCatalogue($_REQUEST['id']);
      $sefu->_applySEFUCatalogueBrand($_REQUEST['id']);
      
      require $_SERVER['DOCUMENT_ROOT']."/lib/MetaGenerate.php";
      require $_SERVER['DOCUMENT_ROOT']."/lib/MetaGenerateModelStrategy.php";
      
      $model = MetaGenerateModelStrategy::getModel($cmf);
      $meta = new MetaGenerate($model);

      $meta->metaCatalogueById($_REQUEST['id']);
      
    

}
$_REQUEST['e']='ED';
}

if($_REQUEST['event'] == 'Изменить')
{















		
				
    if(isset($_FILES['NOT_IMAGE1']['tmp_name']) && $_FILES['NOT_IMAGE1']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'_img1',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_img1',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE1']=$cmf->PicturePost('NOT_IMAGE1',$_REQUEST['IMAGE1'],''.$_REQUEST['id'].'_img1',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE1']) && $_REQUEST['CLR_IMAGE1']){$_REQUEST['IMAGE1']=$cmf->UnlinkFile($_REQUEST['IMAGE1'],$VIRTUAL_IMAGE_PATH);}
	

$_REQUEST['IN_ADV']=isset($_REQUEST['IN_ADV']) && $_REQUEST['IN_ADV']?1:0;
$_REQUEST['IS_INDEX']=isset($_REQUEST['IS_INDEX']) && $_REQUEST['IS_INDEX']?1:0;
$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;
$_REQUEST['REALSTATUS']=isset($_REQUEST['REALSTATUS']) && $_REQUEST['REALSTATUS']?1:0;


@$cmf->execute('update CATALOGUE set PARENT_ID=?,NAME=?,ID_FROM_VBD=?,TYPENAME=?,CATNAME=?,URL=?,SUB_ITEM_TITLE=?,TITLE=?,SUB_TITLE=?,DESC_META=?,KEYWORD_META=?,DESCRIPTION=?,IMAGE1=?,IN_ADV=?,IS_INDEX=? where CATALOGUE_ID=?',stripslashes($_REQUEST['PARENT_ID']),stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['ID_FROM_VBD'])+0,stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['CATNAME']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['SUB_ITEM_TITLE']),stripslashes($_REQUEST['TITLE']),stripslashes($_REQUEST['SUB_TITLE']),stripslashes($_REQUEST['DESC_META']),stripslashes($_REQUEST['KEYWORD_META']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IMAGE1']),stripslashes($_REQUEST['IN_ADV']),stripslashes($_REQUEST['IS_INDEX']),$_REQUEST['id']);
$_REQUEST['e']='ED';

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
        
        $cmf->execute('update CATALOGUE set CATNAME=? where CATALOGUE_ID=?',$_REQUEST['CATNAME'] ,$_REQUEST['id']);              
      }
      
      $cmf->execute('update CATALOGUE set REALCATNAME=? where CATALOGUE_ID=?',GetPath($cmf,$_REQUEST['id']),$_REQUEST['id']);        
      
      require ROOT_PATH."/lib/CreateSEFU.class.php";
      $sefu = new CreateSEFU();      
      UpdatePath($cmf,$_REQUEST['id'],'', $sefu);
      
      $sefu->_applySEFUCatalogue($_REQUEST['id']);
      $sefu->_applySEFUCatalogueBrand($_REQUEST['id']);
      
      require ROOT_PATH."/lib/MetaGenerate.php";
      require ROOT_PATH."/lib/MetaGenerateModelStrategy.php";
      
      $model = MetaGenerateModelStrategy::getModel($cmf);
      $meta = new MetaGenerate($model);

      $meta->metaCatalogueById($_REQUEST['id']);
    
};

if($_REQUEST['e'] == 'ED')
{
list($V_CATALOGUE_ID,$V_PARENT_ID,$V_NAME,$V_ID_FROM_VBD,$V_TYPENAME,$V_CATNAME,$V_REALCATNAME,$V_URL,$V_SUB_ITEM_TITLE,$V_TITLE,$V_SUB_TITLE,$V_DESC_META,$V_KEYWORD_META,$V_DESCRIPTION,$V_IMAGE1,$V_COUNT_,$V_IN_ADV,$V_IS_INDEX,$V_STATUS,$V_REALSTATUS)=$cmf->selectrow_arrayQ('select CATALOGUE_ID,PARENT_ID,NAME,ID_FROM_VBD,TYPENAME,CATNAME,REALCATNAME,URL,SUB_ITEM_TITLE,TITLE,SUB_TITLE,DESC_META,KEYWORD_META,DESCRIPTION,IMAGE1,COUNT_,IN_ADV,IS_INDEX,STATUS,REALSTATUS from CATALOGUE where CATALOGUE_ID=?',$_REQUEST['id']);




$V_STR_PARENT_ID=$cmf->TreeSpravotchnik($V_PARENT_ID,'select A.CATALOGUE_ID,A.NAME from CATALOGUE A   where A.PARENT_ID=?  order by A.NAME',0);
if(isset($V_IMAGE1))
{
   $IM_IMAGE1=split('#',$V_IMAGE1);
   if(isset($IM_14[1]) && $IM_IMAGE1[1] > 150){$IM_IMAGE1[2]=$IM_IMAGE1[2]*150/$IM_IMAGE1[1]; $IM_IMAGE1[1]=150;}
}

$V_IN_ADV=$V_IN_ADV?'checked':'';
$V_IS_INDEX=$V_IS_INDEX?'checked':'';
$V_STATUS=$V_STATUS?'checked':'';
$V_REALSTATUS=$V_REALSTATUS?'checked':'';

@print <<<EOF
<h2 class="h2">Редактирование - Каталог</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CATALOGUE.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(TYPENAME) &amp;&amp; checkXML(CATNAME) &amp;&amp; checkXML(URL) &amp;&amp; checkXML(SUB_ITEM_TITLE) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(SUB_TITLE) &amp;&amp; checkXML(DESC_META) &amp;&amp; checkXML(KEYWORD_META) &amp;&amp; checkXML(DESCRIPTION);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="type" value="2" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="event" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="event" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Родительский каталог:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<select name="PARENT_ID"><option value="0">Корневой</option>$V_STR_PARENT_ID</select><br />
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Название пункта меню:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>ID внешей БД:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ID_FROM_VBD" value="$V_ID_FROM_VBD" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Префикс для типа товара:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TYPENAME" value="$V_TYPENAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Путь до каталога:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="CATNAME" value="$V_CATNAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Добавка к тайтлу продукции:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SUB_ITEM_TITLE" value="$V_SUB_ITEM_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тайтл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TITLE" value="$V_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Добавка к тайтлу:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SUB_TITLE" value="$V_SUB_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Description:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESC_META" rows="7" cols="90">$V_DESC_META</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Ключевые слова:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="KEYWORD_META" rows="7" cols="90">$V_KEYWORD_META</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткий текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="7" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
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
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Участвует в рекламе:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_ADV' value='1' $V_IN_ADV/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Выводить на главной:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_INDEX' value='1' $V_IS_INDEX/><br /></td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="event" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="event" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;



print <<<EOF
<a name="f1"></a><h3 class="h3">Связь Каталог-Экспортная группа</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="CATALOGUE.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="4">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select CATALOGUE_PRICE_EXPORT_ID,PRICE_EXPORT_ID from CATALOGUE_PRICE_EXPORT where CATALOGUE_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>Преобразование в</th><td></td></tr>
EOF;
while(list($V_CATALOGUE_PRICE_EXPORT_ID,$V_PRICE_EXPORT_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_PRICE_EXPORT_ID=$cmf->selectrow_arrayQ('select NAME from PRICE_EXPORT where PRICE_EXPORT_ID=?',$V_PRICE_EXPORT_ID);
                                        


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_CATALOGUE_PRICE_EXPORT_ID" /></td>
<td>$V_CATALOGUE_PRICE_EXPORT_ID</td><td>$V_PRICE_EXPORT_ID</td><td nowrap="">

<a href="CATALOGUE.php?e1=ED&amp;iid=$V_CATALOGUE_PRICE_EXPORT_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';



print <<<EOF
<a name="f2"></a><h3 class="h3">Связь "Категория товаров - Бренды"</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="CATALOGUE.php#f2" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="4">
<input type="submit" name="e2" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e2" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select CATALOGUE_BRAND_VIEW_ID,BRAND_ID from CATALOGUE_BRAND_VIEW where CATALOGUE_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>N</th><th>Рубрика каталога</th><td></td></tr>
EOF;
while(list($V_CATALOGUE_BRAND_VIEW_ID,$V_BRAND_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_BRAND_ID=$cmf->selectrow_arrayQ('select A.NAME from BRAND A where A.BRAND_ID=?',$V_BRAND_ID);
                                        


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_CATALOGUE_BRAND_VIEW_ID" /></td>
<td>$V_CATALOGUE_BRAND_VIEW_ID</td><td>$V_BRAND_ID</td><td nowrap="">

<a href="CATALOGUE.php?e2=ED&amp;iid=$V_CATALOGUE_BRAND_VIEW_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}

if($_REQUEST['e'] == 'AD' ||  $_REQUEST['e'] =='Новый')
{
list($V_CATALOGUE_ID,$V_PARENT_ID,$V_NAME,$V_ID_FROM_VBD,$V_TYPENAME,$V_CATNAME,$V_REALCATNAME,$V_URL,$V_SUB_ITEM_TITLE,$V_TITLE,$V_SUB_TITLE,$V_DESC_META,$V_KEYWORD_META,$V_DESCRIPTION,$V_IMAGE1,$V_COUNT_,$V_IN_ADV,$V_IS_INDEX,$V_STATUS,$V_REALSTATUS,$V_ORDERING)=array('','','','','','','','','','','','','','','','','','','','','');
if(!empty($_REQUEST['pid'])) $V_PARENT_ID = $_REQUEST['pid'];
else $V_PARENT_ID = 0;



$V_STR_PARENT_ID=$cmf->TreeSpravotchnik($V_PARENT_ID,'select A.CATALOGUE_ID,A.NAME from CATALOGUE A   where A.PARENT_ID=?  order by A.NAME',0);
$IM_IMAGE1=array('','','');
$V_IN_ADV='';
$V_IS_INDEX='';
$V_STATUS='checked';
$V_REALSTATUS='';

@print <<<EOF
<h2 class="h2">Добавление - Каталог</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CATALOGUE.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(TYPENAME) &amp;&amp; checkXML(CATNAME) &amp;&amp; checkXML(URL) &amp;&amp; checkXML(SUB_ITEM_TITLE) &amp;&amp; checkXML(TITLE) &amp;&amp; checkXML(SUB_TITLE) &amp;&amp; checkXML(DESC_META) &amp;&amp; checkXML(KEYWORD_META) &amp;&amp; checkXML(DESCRIPTION);">
EOF;
print '<input type="hidden" name="pid" value="'.$_REQUEST['pid'].'" />';
@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Добавить" class="gbt badd" /> 
<input type="submit" name="event" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Родительский каталог:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<select name="PARENT_ID"><option value="0">Корневой</option>$V_STR_PARENT_ID</select><br />
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Название пункта меню:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>ID внешей БД:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ID_FROM_VBD" value="$V_ID_FROM_VBD" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Префикс для типа товара:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TYPENAME" value="$V_TYPENAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Путь до каталога:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="CATNAME" value="$V_CATNAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>URL:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Добавка к тайтлу продукции:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SUB_ITEM_TITLE" value="$V_SUB_ITEM_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тайтл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TITLE" value="$V_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Добавка к тайтлу:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SUB_TITLE" value="$V_SUB_TITLE" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Description:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESC_META" rows="7" cols="90">$V_DESC_META</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Ключевые слова:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="KEYWORD_META" rows="7" cols="90">$V_KEYWORD_META</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткий текст:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="7" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE1" value="$V_IMAGE1" />
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
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Участвует в рекламе:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IN_ADV' value='1' $V_IN_ADV/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Выводить на главной:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='IS_INDEX' value='1' $V_IS_INDEX/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Добавить" class="gbt badd" /> 
<input type="submit" name="event" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}

if($visible)
{
$parhash=array('0'=>'1');
$CATALOGUE_ID=$_REQUEST['id'];
$O_CATALOGUE_ID=$CATALOGUE_ID;
do 
{
  $PARENTID=$cmf->selectrow_array('select PARENT_ID from CATALOGUE where CATALOGUE_ID=?',$CATALOGUE_ID);
  $parhash[$CATALOGUE_ID]=1;
  $CATALOGUE_ID=$PARENTID;
}while(isset($PARENTID));
print <<<EOF
<h2 class="h2">Каталог</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="CATALOGUE.php" method="POST">
<input type="hidden" name="r" value="{$_REQUEST['r']}" />
<tr bgcolor="#F0F0F0"><td colspan="5">
EOF;

if ($cmf->W)
print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" />
EOF;

print <<<EOF
</td></tr>
EOF;
print <<<EOF
<tr bgcolor="#FFFFFF"><th>N</th><th>Название пункта меню</th><th>Путь</th><form action="ITEM.php" method="POST"><th>

</th></form></tr><tr bgcolor="#F0F0F0"><td>-</td><td colspan="7"><a href="ALIASES.php?pid=0" class="red">Нерубрицированные</a></td></tr>
        <tr bgcolor="#F0F0F0"><td>-</td><td colspan="7"><a href="?e=Перестроить" class="red">Перестроить</a></td></tr>
        
        
EOF;
print visibleTree($cmf,$_REQUEST['r'],0,$_REQUEST['r'],$parhash);
print '</form></table>';
}

function visibleTree($cmf,$parent,$level,$root,$parhash)
{
$width=$level*15+10;
$ret='';
$sth=$cmf->execute('select CATALOGUE_ID,NAME,REALCATNAME,COUNT_,IN_ADV,IS_INDEX,STATUS,REALSTATUS from CATALOGUE where PARENT_ID=? order by ORDERING',$parent);
while ( list($V_CATALOGUE_ID,$V_NAME,$V_REALCATNAME,$V_COUNT_,$V_IN_ADV,$V_IS_INDEX,$V_STATUS,$V_REALSTATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{




  $ICONS=<<<EOF
  <a href="CATALOGUE.php?id=$V_CATALOGUE_ID&amp;e=FT&amp;r=$root"><img src="i/flt.gif" border="0" title="Фильтры" hspace="5"/></a><a href="CATALOGUE.php?id=$V_CATALOGUE_ID&amp;e=FV&amp;r=$root"><img src="i/flt.gif" border="0" title="Кор. фильт." hspace="5"/></a><a href="CATALOGUE.php?id=$V_CATALOGUE_ID&amp;e=Excel&amp;r=$root"><img src="i/xls_file.gif" border="0" title="Загрузить товары каталога" hspace="5"/></a>
EOF;
  $V_REALSTATUS=$V_REALSTATUS?'b':'d';
  $V_STATUS=$V_STATUS?0:1;
  $CO_=$cmf->selectrow_array('select count(*) from CATALOGUE where PARENT_ID=?',$V_CATALOGUE_ID);
if(!$CO_)
 {

$folder=<<<EOF
<img src="i/f1.gif" class="fld" /><a href="ITEM.php?pid=$V_CATALOGUE_ID" class="$V_REALSTATUS">$V_NAME</a>
EOF;

 }
else
 {

$folder=isset($parhash[$V_CATALOGUE_ID])?$folder=<<<EOF
<a href="ITEM.php?pid=$V_CATALOGUE_ID" class="$V_REALSTATUS"><img src="i/f1.gif" class="fld" /></a><a href="CATALOGUE.php?id=$V_CATALOGUE_ID&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF
:
$folder=<<<EOF
<a href="ITEM.php?pid=$V_CATALOGUE_ID" class="$V_REALSTATUS"><img src="i/f0.gif" class="fld" /></a><a href="CATALOGUE.php?id=$V_CATALOGUE_ID&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF;

 }

 $V_NAME=<<<EOF
$folder ($V_COUNT_)
EOF;
 
  $ret.=<<<EOF
<tr bgcolor="#ffffff">
<td>$V_CATALOGUE_ID</td><td style="padding-left:{$width}px">$V_NAME</td><td>$V_REALCATNAME</td><td nowrap="">
EOF;

if ($cmf->W)
$ret.=<<<EOF
<a href="CATALOGUE.php?e=AD&amp;pid=$V_CATALOGUE_ID&amp;r=$root"><img src="i/add.gif" border="0" title="Добавить" hspace="5" /></a>
<a href="CATALOGUE.php?e=UP&amp;id=$V_CATALOGUE_ID&amp;r=$root"><img src="i/up.gif" border="0" title="Вверх" hspace="5" /></a>
<a href="CATALOGUE.php?e=DN&amp;id=$V_CATALOGUE_ID&amp;r=$root"><img src="i/dn.gif" border="0" title="Вниз" hspace="5" /></a>
<a href="CATALOGUE.php?e=ED&amp;id=$V_CATALOGUE_ID&amp;r=$root"><img src="i/ed.gif" border="0" title="Изменить" hspace="5" /></a>
<a href="CATALOGUE.php?e=VS&amp;id=$V_CATALOGUE_ID&amp;o=$V_CATALOGUE_ID"><img src="i/v$V_STATUS.gif" border="0" /></a>&#160;
$ICONS
EOF;
if ($cmf->D)
{
$ret .=<<<EOF
<a href="CATALOGUE.php?e=DL&amp;id=$V_CATALOGUE_ID&amp;r=$root" onclick="return dl();"><img src="i/del.gif" border="0" title="Удалить" hspace="5" /></a>
EOF;
}

  $ret.= '</td></tr>';

  if(isset($parhash[$V_CATALOGUE_ID])){$ret.=visibleTree($cmf,$V_CATALOGUE_ID,$level+1,$root,$parhash);}
}
return $ret;
}

function DelTree($cmf,$id)
{
$sth=$cmf->execute('select CATALOGUE_ID from CATALOGUE where PARENT_ID=?',$id);
while(list($V_CATALOGUE_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
DelTree($cmf,$V_CATALOGUE_ID);
$cmf->execute('delete from CATALOGUE where CATALOGUE_ID=?',$V_CATALOGUE_ID);
#### del items
}
}

function SetTreeRealStatus($cmf,$id,$state)
{
$sth=$cmf->execute('select CATALOGUE_ID,STATUS from CATALOGUE where PARENT_ID=?',$id);
while(list($V_CATALOGUE_ID,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){SetTreeRealStatus($cmf,$V_CATALOGUE_ID,$state);}
if($state) {$cmf->execute('update CATALOGUE set REALSTATUS=STATUS where CATALOGUE_ID=?',$V_CATALOGUE_ID);}
else {$cmf->execute('update CATALOGUE set REALSTATUS=0 where CATALOGUE_ID=?',$V_CATALOGUE_ID);}
}
}

function GetMyRealStatus($cmf,$id)
{
$V_PARENT_ID=$id;
$V_FULLSTATUS=0;
while ($V_PARENT_ID>0)
{
list ($V_PARENT_ID,$V_STATUS)=$cmf->selectrow_array('select PARENT_ID,STATUS from CATALOGUE where CATALOGUE_ID=?',$V_PARENT_ID);
$V_FULLSTATUS+=1-$V_STATUS;
}
if($V_FULLSTATUS){$V_FULLSTATUS=0;} else {$V_FULLSTATUS=1;}
return $V_FULLSTATUS;
}

$cmf->MakeCommonFooter();
$cmf->Close();


function GetPath($cmf,$id)
{
list ($PATH,$PARENTID,$NAME)=array('','','');
$i=0;
while(list($PARENTID,$NAME)=$cmf->selectrow_array('select PARENT_ID,CATNAME from CATALOGUE where CATALOGUE_ID=?',$id))
{
$i++;
if($i==1 && $NAME=='') break;
$id=$PARENTID;
if($NAME){ $PATH="/$NAME$PATH"; }
};

if('/' != substr($PATH,-1)){$PATH=$PATH."/";}
$PATH = preg_replace("/(\/){1,}/","/",$PATH);
return $PATH;
}

function UpdatePath($cmf,$id,$path, $sefu)
{
   
  $sth=$cmf->execute('select CATALOGUE_ID,CATNAME from CATALOGUE where PARENT_ID=?',$id);
  while(list ($V_CATALOGUE_ID,$V_CATNAME)=mysql_fetch_array($sth, MYSQL_NUM))
  {        
    if($V_CATNAME){$V_CATNAME="$path/$V_CATNAME";} else {$V_CATNAME='';};
    $V_CATNAME = preg_replace("/\/\//","/",$V_CATNAME);
    if('/' != substr($V_CATNAME,-1)){$V_CATNAME=$V_CATNAME."/";}
    $cmf->execute('update CATALOGUE set REALCATNAME=? where CATALOGUE_ID=?',$V_CATNAME,$V_CATALOGUE_ID);
    
        
    UpdatePath($cmf,$V_CATALOGUE_ID,$V_CATNAME, $sefu);
    
    $sefu->_applySEFUCatalogue($V_CATALOGUE_ID);
    $sefu->_applySEFUCatalogueBrand($V_CATALOGUE_ID);
  }
}

function updateOrdering($cmf,$id)
{
   $sth = $cmf->execute("select CATALOGUE_ID from CATALOGUE where PARENT_ID=? order by ORDERING",$id);
   $order=1;
   while($row = mysql_fetch_array($sth))
   {
       $sql = "update CATALOGUE set ORDERING='".$order."' where CATALOGUE_ID = '".$row['CATALOGUE_ID']."'";
       $cmf->execute($sql);
       $order++;
       updateOrdering($cmf,$row['CATALOGUE_ID']);
   }
}

?>
