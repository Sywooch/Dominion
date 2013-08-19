<?php
require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('ZAKAZ');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!isset($_SESSION['ZAKAZ']['ZAKAZSTATUS_ID']))
  $_SESSION['ZAKAZ']['ZAKAZSTATUS_ID']='all';
if (!isset($_SESSION['ZAKAZ']['SES_CMF_USER_ID']))
  $_SESSION['ZAKAZ']['SES_CMF_USER_ID']='all';
if (!isset($_SESSION['ZAKAZ']['DATE_FROM']))
  $_SESSION['ZAKAZ']['DATE_FROM']=date('Y-m-d H:i:s');
if (!isset($_SESSION['ZAKAZ']['DATE_TO']))
  $_SESSION['ZAKAZ']['DATE_TO']=date('Y-m-d H:i:s');
if (!isset($_SESSION['ZAKAZ']['SES_TELMOB']))
  $_SESSION['ZAKAZ']['SES_TELMOB']="";
if (!isset($_SESSION['ZAKAZ']['SES_NAME']))
  $_SESSION['ZAKAZ']['SES_NAME']="";
if (!isset($_SESSION['ZAKAZ']['SES_ORDER']))
  $_SESSION['ZAKAZ']['SES_ORDER']="";
if (!isset($_SESSION['ZAKAZ']['SUPPLIER_ID']))
  $_SESSION['ZAKAZ']['SUPPLIER_ID']="all";

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;







if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';



    if(empty($_REQUEST['DATE_FROM'])) $_REQUEST['DATE_FROM']='';
    if(empty($_REQUEST['DATE_TO'])) $_REQUEST['DATE_TO']='';
    $filtpath = "&amp;f={$_REQUEST['f']}&amp;DATE_FROM={$_REQUEST['DATE_FROM']}&amp;DATE_TO={$_REQUEST['DATE_TO']}";
    $sumPrice = 0;
     $cur = $cmf->selectrow_array("select SNAME
                          from CURRENCY
                          where PRICE = 1
                          and STATUS = 1");






if($cmf->Param('el1') == 'Удалить')
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from ZAKAZ_ITEM where ZAKAZ_ID=? and ZAKAZ_ITEM_ID=?',$_REQUEST['id'],$id);


 }

$_REQUEST['e']='ED';
}






if($cmf->Param('el1') == 'Изменить')
{
$IN_COST = $_REQUEST['PRICE'] * $_REQUEST['QUANTITY'];

$_REQUEST['PRICE'] = strip_tags($_REQUEST['PRICE']);
$_REQUEST['PRICE'] = addslashes($_REQUEST['PRICE']);

$_REQUEST['QUANTITY'] = strip_tags($_REQUEST['QUANTITY']);
$_REQUEST['QUANTITY'] = addslashes($_REQUEST['QUANTITY']);

$_REQUEST['PURCHASE_PRICE'] = strip_tags($_REQUEST['PURCHASE_PRICE']);
$_REQUEST['PURCHASE_PRICE'] = addslashes($_REQUEST['PURCHASE_PRICE']);

$IN_COST = $_REQUEST['PRICE'] * $_REQUEST['QUANTITY'];

$sql="update ZAKAZ_ITEM
      set  PRICE = {$_REQUEST['PRICE']}
          ,QUANTITY = {$_REQUEST['QUANTITY']}
          ,PURCHASE_PRICE = {$_REQUEST['PURCHASE_PRICE']}
          ,COST = {$IN_COST}
       where ZAKAZ_ITEM_ID={$_REQUEST['iid']}
         and ZAKAZ_ID={$_REQUEST['id']}";

$cmf->execute($sql);


//$cmf->execute('update ZAKAZ_ITEM set  where ZAKAZ_ITEM_ID=? and ITEM_ID=?',stripslashes($_REQUEST['ZAKAZ_ITEM_ID']),stripslashes($_REQUEST['NAME']),0,0,0,$_REQUEST['iid'],$_REQUEST['id']);


$_REQUEST['e']='ED';
};



if($cmf->Param('el1') == 'Добавить')
{

$sql="select I.NAME
           , I.CURRENCY_ID
           , C.SNAME
      from ITEM I
      left join CURRENCY C on (C.CURRENCY_ID = I.CURRENCY_ID)
      where I.ITEM_ID = ?";

list($IN_NAME, $IN_CURRENCY_ID, $IN_SNAME) = $cmf->selectrow_array($sql, $_REQUEST['ITEM_ID']);

$sth_currency = $cmf->execute("select * from CURRENCY where CURRENCY_ID = 1");
$default_currency = mysql_fetch_array($sth_currency, MYSQL_ASSOC);

if($default_currency['CURRENCY_ID'] != $IN_CURRENCY_ID){
   $rate = $cmf->selectrow_array("select PRICE from CURRENCY where CURRENCY_ID=?", $IN_CURRENCY_ID);
   $VV_PRICE = round($_REQUEST['PRICE'] * $rate);
 }

$IN_COST = $VV_PRICE * $_REQUEST['QUANTITY'];

$_REQUEST['PRICE'] = strip_tags($_REQUEST['PRICE']);
$_REQUEST['PRICE'] = addslashes($_REQUEST['PRICE']);

$_REQUEST['PURCHASE_PRICE'] = strip_tags($_REQUEST['PURCHASE_PRICE']);
$_REQUEST['PURCHASE_PRICE'] = addslashes($_REQUEST['PURCHASE_PRICE']);

$_REQUEST['QUANTITY'] = strip_tags($_REQUEST['QUANTITY']);
$_REQUEST['QUANTITY'] = addslashes($_REQUEST['QUANTITY']);

$sql="insert into ZAKAZ_ITEM
      set  ZAKAZ_ID = {$_REQUEST['id']}
          ,ITEM_ID = {$_REQUEST['ITEM_ID']}
          ,CATALOGUE_ID = {$_REQUEST['CATALOGUE_ID']}
          ,NAME = '{$IN_NAME}'
          ,PRICE = {$VV_PRICE}
          ,PURCHASE_PRICE = {$_REQUEST['PURCHASE_PRICE']}
          ,ITEM_PRICE = {$_REQUEST['PRICE']}
          ,ITEM_CURRENCY = '{$IN_SNAME}'
          ,QUANTITY = {$_REQUEST['QUANTITY']}
          ,COST = {$IN_COST}
          ,CURRENCY_ID = 1";

$cmf->execute($sql);

$_REQUEST['e']='ED';
$visible=0;
}


if($cmf->Param('eventl1') == 'ED')
{
list ($V_CATALOGUE_ID
     ,$V_ITEM_ID
     ,$V_ZAKAZ_ITEM_ID
     ,$V_NAME
     ,$V_PRICE
     ,$V_QUANTITY
     ,$V_COST
     ,$V_CURRENCY_ID
     ,$V_PURCHASE_PRICE)=$cmf->selectrow_arrayQ('select CATALOGUE_ID
                                                    ,ITEM_ID
                                                    ,ZAKAZ_ITEM_ID
                                                    ,NAME
                                                    ,PRICE
                                                    ,QUANTITY
                                                    ,COST
                                                    ,CURRENCY_ID
                                                    ,PURCHASE_PRICE
                                              from ZAKAZ_ITEM
                                              where ZAKAZ_ID=? and ZAKAZ_ITEM_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


//$V_STR_CATALOGUE_ID=$cmf->Spravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE   order by NAME');

 $V_STR_ITEM_ID=$cmf->Spravotchnik($V_ITEM_ID,'select A.ITEM_ID,A.NAME from ITEM A   left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where 1  and A.STATUS=1  and A.CATALOGUE_ID='.$V_CATALOGUE_ID.'  order by A.NAME');

@print <<<EOF
        <h2 class="h2">Редактирование - Товары</h2>

<form method="POST" action="ZAKAZ.php#fl1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
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


        $VV_CATALOGUE_ID=$cmf->TreeSpravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE   where PARENT_ID=?  and COUNT_ > 0',0);
@print <<<EOF
<tr bgcolor="#FFFFFF"><td><span class="title2">Товар заказа</span></td><td width="100%">
<table>
<tr><td /></tr></table></td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Наименование:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
$V_NAME
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Цена продажи (грн.):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<input type="text" name="PRICE" value="$V_PRICE" size="90" /><br />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Количество:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<input type="text" name="QUANTITY" value="$V_QUANTITY" size="90" /><br />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Цена закупки (грн.):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<input type="text" name="PURCHASE_PRICE" value="$V_PURCHASE_PRICE" size="90" /><br />
</td></tr>



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
list($V_CATALOGUE_ID,$V_ITEM_ID,$V_ZAKAZ_ITEM_ID,$V_NAME,$V_PRICE,$V_QUANTITY,$V_COST)=array('','','','','','','');


$V_STR_CATALOGUE_ID=$cmf->Spravotchnik($V_CATALOGUE_ID,'select CATALOGUE_ID,NAME from CATALOGUE   order by NAME');

$V_STR_ITEM_ID=$cmf->Spravotchnik($V_ITEM_ID,'select A.ITEM_ID,A.NAME from ITEM A   left join BRAND B on (A.BRAND_ID=B.BRAND_ID) order by A.NAME');

@print <<<EOF
<h2 class="h2">Добавление - Товары</h2>
<form method="POST" action="ZAKAZ.php#fl1" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
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

 /*
  *     <td>Каталог: <select name="CATALOGUE_ID" onchange="return getOptions({$VV_CATALOGUE_ID})"></select>
  */
$VV_CATALOGUE_ID=$cmf->TreeSpravotchnik('','select CATALOGUE_ID,NAME from CATALOGUE   where PARENT_ID=?  and COUNT_ > 0',0);
$V_STR_CURRENCY_ID=$cmf->Spravotchnik('','select CURRENCY_ID,NAME from CURRENCY  order by NAME');
@print <<<EOF
<tr bgcolor="#FFFFFF"><td><span class="title2">Заказы</span></td><td width="100%">
<table>
 <tr>
   <!--<td>Каталог: <select name="CATALOGUE_ID" onchange="return chan(this.form,this.form.elements['ITEM_ID'],'select A.ITEM_ID,CONCAT(B.NAME,\' \',A.NAME, \' (Артикул \', A.ARTICLE,\')\') from ITEM A  left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.CATALOGUE_ID=?  and A.STATUS=1 and A.PRICE &gt; 0  order by A.NAME',this.value);"><option value="">-- Не задан --</option>{$VV_CATALOGUE_ID}</select>-->
   <td>Каталог: <select name="CATALOGUE_ID" onchange="return getOptions(this.form.elements['ITEM_ID'], this.value)"><option value="">-- Не задан --</option>{$VV_CATALOGUE_ID}</select>
  </td>
</tr>

<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/sorting.js"></script>

<tr><td>поиск:<br /><input type="text" id="sorting" /></td></tr>

<tr><td><select name="ITEM_ID" id="filterfield" style="width:100%"></select></td></tr></table></td></tr>
<tr bgcolor="#FFFFFF">
<th width="1%"><b>Валюта товара:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<span id="CURRENCY_ID"></span>
</td></tr>
<tr bgcolor="#FFFFFF">
<th width="1%"><b>Цена продажи:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<input type="text" name="PRICE" value="" id="PRICE" size="90" /><br />
</td></tr>
<tr bgcolor="#FFFFFF">
<th width="1%"><b>Цена закупки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<input type="text" name="PURCHASE_PRICE" value="" id="PURCHASE_PRICE" size="90" /><br />
</td></tr>
<tr bgcolor="#FFFFFF">
<th width="1%"><b>Количество:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<input type="text" name="QUANTITY" value="1" size="90" /><br />
</td></tr>

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
$cmf->execute('delete from ZAKAZ where ZAKAZ_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('ZAKAZ');















$cmf->execute('insert into ZAKAZ (ZAKAZ_ID,DATA,DELIVERYDATA,NAME,CMF_USER_ID,SUPPLIER_ID,EMAIL,TELMOB,ADDRESS,INFO,PAYMENT,STATUS) values (?,?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['DATA']),stripslashes($_REQUEST['DELIVERYDATA']),stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['CMF_USER_ID'])+0,stripslashes($_REQUEST['SUPPLIER_ID'])+0,stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['TELMOB']),stripslashes($_REQUEST['ADDRESS']),stripslashes($_REQUEST['INFO']),stripslashes($_REQUEST['PAYMENT'])+0,stripslashes($_REQUEST['STATUS'])+0);


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{















$cmf->execute('update ZAKAZ set DATA=?,DELIVERYDATA=?,NAME=?,CMF_USER_ID=?,SUPPLIER_ID=?,EMAIL=?,TELMOB=?,ADDRESS=?,INFO=?,PAYMENT=?,STATUS=? where ZAKAZ_ID=?',stripslashes($_REQUEST['DATA']),stripslashes($_REQUEST['DELIVERYDATA']),stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['CMF_USER_ID'])+0,stripslashes($_REQUEST['SUPPLIER_ID'])+0,stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['TELMOB']),stripslashes($_REQUEST['ADDRESS']),stripslashes($_REQUEST['INFO']),stripslashes($_REQUEST['PAYMENT'])+0,stripslashes($_REQUEST['STATUS'])+0,$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_ZAKAZ_ID,$V_DATA,$V_DELIVERYDATA,$V_NAME,$V_CMF_USER_ID,$V_SUPPLIER_ID,$V_EMAIL,$V_TELMOB,$V_ADDRESS,$V_INFO,$V_PAYMENT,$V_STATUS)=
$cmf->selectrow_arrayQ('select ZAKAZ_ID,DATE_FORMAT(DATA,"%Y-%m-%d %H:%i"),DATE_FORMAT(DELIVERYDATA,"%Y-%m-%d %H:%i"),NAME,CMF_USER_ID,SUPPLIER_ID,EMAIL,TELMOB,ADDRESS,INFO,PAYMENT,STATUS from ZAKAZ where ZAKAZ_ID=?',$_REQUEST['id']);



$V_STR_CMF_USER_ID=$cmf->Spravotchnik($V_CMF_USER_ID,'select CMF_USER_ID,NAME from CMF_USER  order by NAME');

$V_STR_SUPPLIER_ID=$cmf->Spravotchnik($V_SUPPLIER_ID,'select SUPPLIER_ID,NAME from SUPPLIER  order by NAME');

$V_STR_PAYMENT=$cmf->Spravotchnik($V_PAYMENT,'select PAYMENT_ID,NAME from PAYMENT  order by NAME');

$V_STR_STATUS=$cmf->Spravotchnik($V_STATUS,'select ZAKAZSTATUS_ID,NAME from ZAKAZSTATUS  order by NAME');

@print <<<EOF
<h2 class="h2">Редактирование - Заказы</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="ZAKAZ.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(EMAIL) &amp;&amp; checkXML(TELMOB) &amp;&amp; checkXML(ADDRESS) &amp;&amp; checkXML(INFO);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="type" value="7" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Дата заказа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATA" name="DATA" value="$V_DATA" />
EOF;

if($V_DATA) $V_DAT_ = substr($V_DATA,8,2).".".substr($V_DATA,5,2).".".substr($V_DATA,0,4)." ".substr($V_DATA,11,2).":".substr($V_DATA,14,2);
else $V_DAT_ = '';



        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DATA">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DATA" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>



        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATA",
                       displayArea    :    "DATE_DATA",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATA",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата доставки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DELIVERYDATA" name="DELIVERYDATA" value="$V_DELIVERYDATA" />
EOF;

if($V_DELIVERYDATA) $V_DAT_ = substr($V_DELIVERYDATA,8,2).".".substr($V_DELIVERYDATA,5,2).".".substr($V_DELIVERYDATA,0,4)." ".substr($V_DELIVERYDATA,11,2).":".substr($V_DELIVERYDATA,14,2);
else $V_DAT_ = '';



        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DELIVERYDATA">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DELIVERYDATA" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>



        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DELIVERYDATA",
                       displayArea    :    "DATE_DELIVERYDATA",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DELIVERYDATA",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Ф.И.О.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Менеджер:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


			<select name="CMF_USER_ID">






				$V_STR_CMF_USER_ID
			</select><br />



</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Поставщики:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


			<select name="SUPPLIER_ID">






				$V_STR_SUPPLIER_ID
			</select><br />



</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>E-mail:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Телефон:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TELMOB" value="$V_TELMOB" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Адрес:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ADDRESS" value="$V_ADDRESS" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Заметки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="INFO" rows="5" cols="90">$V_INFO</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Способ оплаты:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


			<select name="PAYMENT">






				$V_STR_PAYMENT
			</select><br />



</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Статус:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


			<select name="STATUS">




				<option value="0">- не задан -</option>

				$V_STR_STATUS
			</select><br />



</td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;








@print <<<EOF
        <h3 class="h3"><a name="fl1"></a>Товары</h3>
        <table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
        <form action="ZAKAZ.php#fl1" method="POST">
        <tr bgcolor="#FFFFFF">
                <td colspan="8" class="main_tbl_title">
                <input type="submit" name="el1" value="Новый" class="gbt bnew" /><img src="i/0.gif" width="4" height="1" />
                <input type="submit" onclick="return dl();" name="el1" value="Удалить" class="gbt bdel" />
                <input type="hidden" name="id" value="{$_REQUEST['id']}" />

                <input type="hidden" name="p" value="{$_REQUEST['p']}" />

                </td>
        </tr>
EOF;

$sth=$cmf->execute('select CATALOGUE_ID, ITEM_ID,ZAKAZ_ITEM_ID,NAME,PRICE, PURCHASE_PRICE, QUANTITY,COST, ITEM_PRICE from ZAKAZ_ITEM where ZAKAZ_ID=? ',$_REQUEST['id']);
?>
<tr bgcolor="#FFFFFF">
  <td><input type="checkbox" onclick="return SelectAll(this.form,checked,'iid[]');" /></td>
  <th>ИД рубрики</th>
  <th>товар</th>
  <th>ID сопутствующего товара</th>
  <th>Наименование</th>
  <th>Цена</th>
  <th>Цена (USD)</th>
  <th>Цена закупки</th>
  <th>Кол-во</th>
  <th>Сумма</th><td></td></tr><?php
while(list($V_CATALOGUE_ID,$V_ITEM_ID,$V_ZAKAZ_ITEM_ID,$V_NAME,$V_PRICE, $V_PURCHASE_PRICE, $V_QUANTITY,$V_COST, $V_ITEM_PRICE)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_CATALOGUE_ID_STR=$cmf->selectrow_arrayQ('select NAME from CATALOGUE A   where A.CATALOGUE_ID=?',$V_CATALOGUE_ID);

$V_ITEM_ID_STR=$cmf->selectrow_arrayQ('select A.NAME from ITEM A  left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.ITEM_ID=?',$V_ITEM_ID);


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="{$V_ZAKAZ_ITEM_ID}" /></td>
<td>$V_CATALOGUE_ID_STR</td><td><a href="ITEM.php?e=ED&amp;id=$V_ITEM_ID" target="_blank">$V_ITEM_ID_STR</a></td><td><a href="" target="_blank">$V_ZAKAZ_ITEM_ID</a></td><td>$V_NAME</td><td>$V_PRICE</td><td>$V_ITEM_PRICE</td><td>$V_PURCHASE_PRICE</td><td>$V_QUANTITY</td><td>$V_COST</td><td>

<a href="ZAKAZ.php?eventl1=ED&amp;iid=$V_ZAKAZ_ITEM_ID&amp;id={$_REQUEST['id']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>

EOF;
}

print '</form></table>';


$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_ZAKAZ_ID,$V_DATA,$V_DELIVERYDATA,$V_NAME,$V_CMF_USER_ID,$V_SUPPLIER_ID,$V_EMAIL,$V_TELMOB,$V_ADDRESS,$V_INFO,$V_USER_ID,$V_PAYMENT,$V_STATUS)=array('','','','','','','','','','','','','');

//$V_DATA=$cmf->selectrow_array('select now()');
//$V_DELIVERYDATA=$cmf->selectrow_array('select now()');
$V_DATA=date('Y-m-d H:i:s');
$V_DELIVERYDATA=date('Y-m-d H:i:s');

$V_STR_CMF_USER_ID=$cmf->Spravotchnik($V_CMF_USER_ID,'select CMF_USER_ID,NAME from CMF_USER  order by NAME');

$V_STR_SUPPLIER_ID=$cmf->Spravotchnik($V_SUPPLIER_ID,'select SUPPLIER_ID,NAME from SUPPLIER  order by NAME');

$V_STR_PAYMENT=$cmf->Spravotchnik($V_PAYMENT,'select PAYMENT_ID,NAME from PAYMENT  order by NAME');

$V_STR_STATUS=$cmf->Spravotchnik($V_STATUS,'select ZAKAZSTATUS_ID,NAME from ZAKAZSTATUS  order by NAME');

@print <<<EOF
<h2 class="h2">Добавление - Заказы</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="ZAKAZ.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(EMAIL) &amp;&amp; checkXML(TELMOB) &amp;&amp; checkXML(ADDRESS) &amp;&amp; checkXML(INFO);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" />
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Дата заказа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATA" name="DATA" value="$V_DATA" />
EOF;

if($V_DATA) $V_DAT_ = substr($V_DATA,8,2).".".substr($V_DATA,5,2).".".substr($V_DATA,0,4)." ".substr($V_DATA,11,2).":".substr($V_DATA,14,2);
else $V_DAT_ = '';

        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DATA">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DATA" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>



        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATA",
                       displayArea    :    "DATE_DATA",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATA",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата доставки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DELIVERYDATA" name="DELIVERYDATA" value="$V_DELIVERYDATA" />
EOF;

if($V_DELIVERYDATA) $V_DAT_ = substr($V_DELIVERYDATA,8,2).".".substr($V_DELIVERYDATA,5,2).".".substr($V_DELIVERYDATA,0,4)." ".substr($V_DELIVERYDATA,11,2).":".substr($V_DELIVERYDATA,14,2);
else $V_DAT_ = '';



        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DELIVERYDATA">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DELIVERYDATA" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>



        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DELIVERYDATA",
                       displayArea    :    "DATE_DELIVERYDATA",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DELIVERYDATA",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Ф.И.О.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Менеджер:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


			<select name="CMF_USER_ID">






				$V_STR_CMF_USER_ID
			</select><br />



</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Поставщики:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


			<select name="SUPPLIER_ID">






				$V_STR_SUPPLIER_ID
			</select><br />



</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>E-mail:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Телефон:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TELMOB" value="$V_TELMOB" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Адрес:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ADDRESS" value="$V_ADDRESS" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Заметки:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="INFO" rows="5" cols="90">$V_INFO</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Способ оплаты:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


			<select name="PAYMENT">






				$V_STR_PAYMENT
			</select><br />



</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Статус:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


			<select name="STATUS">




				<option value="0">- не задан -</option>

				$V_STR_STATUS
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


print '<h2 class="h2">Заказы</h2><form action="ZAKAZ.php" method="POST">';

if(isset($_REQUEST['SES_TELMOB']) && empty($_REQUEST['SES_TELMOB']) && !empty($_SESSION['ZAKAZ']['SES_TELMOB']))
    $_SESSION['ZAKAZ']['SES_TELMOB'] = $_REQUEST['SES_TELMOB'];
if(isset($_REQUEST['SES_NAME']) && empty($_REQUEST['SES_NAME']) && !empty($_SESSION['ZAKAZ']['SES_NAME']))
    $_SESSION['ZAKAZ']['SES_NAME'] = $_REQUEST['SES_NAME'];
if(isset($_REQUEST['SES_ORDER']) && empty($_REQUEST['SES_ORDER']) && !empty($_SESSION['ZAKAZ']['SES_ORDER']))
    $_SESSION['ZAKAZ']['SES_ORDER'] = $_REQUEST['SES_ORDER'];

$_REQUEST['ZAKAZSTATUS_ID'] = empty($_REQUEST['ZAKAZSTATUS_ID']) ? $_SESSION['ZAKAZ']['ZAKAZSTATUS_ID']:$_REQUEST['ZAKAZSTATUS_ID'];
$_REQUEST['SES_SUPPLIER_ID'] = empty($_REQUEST['SES_SUPPLIER_ID']) ? $_SESSION['ZAKAZ']['SUPPLIER_ID']:$_REQUEST['SES_SUPPLIER_ID'];
$_REQUEST['SES_CMF_USER_ID'] = empty($_REQUEST['SES_CMF_USER_ID']) ? $_SESSION['ZAKAZ']['SES_CMF_USER_ID']:$_REQUEST['SES_CMF_USER_ID'];
$_REQUEST['DATE_FROM'] = empty($_REQUEST['DATE_FROM']) ? $_SESSION['ZAKAZ']['DATE_FROM']:$_REQUEST['DATE_FROM'];
$_REQUEST['DATE_TO'] = empty($_REQUEST['DATE_TO']) ? $_SESSION['ZAKAZ']['DATE_TO']:$_REQUEST['DATE_TO'];
$_REQUEST['SES_TELMOB'] = empty($_REQUEST['SES_TELMOB']) ? $_SESSION['ZAKAZ']['SES_TELMOB']:$_REQUEST['SES_TELMOB'];
$_REQUEST['SES_NAME'] = empty($_REQUEST['SES_NAME']) ? $_SESSION['ZAKAZ']['SES_NAME']:$_REQUEST['SES_NAME'];
$_REQUEST['SES_ORDER'] = empty($_REQUEST['SES_ORDER']) ? $_SESSION['ZAKAZ']['SES_ORDER']:$_REQUEST['SES_ORDER'];

$patterns[0] = "/\D/";
$replacements[2] = "";
$_REQUEST['SES_TELMOB'] = preg_replace($patterns, $replacements, $_REQUEST['SES_TELMOB']);

$_SESSION['ZAKAZ']['ZAKAZSTATUS_ID'] = $_REQUEST['ZAKAZSTATUS_ID'];
$_SESSION['ZAKAZ']['SUPPLIER_ID'] = $_REQUEST['SES_SUPPLIER_ID'];
$_SESSION['ZAKAZ']['SES_CMF_USER_ID'] = $_REQUEST['SES_CMF_USER_ID'];
$_SESSION['ZAKAZ']['DATE_FROM'] = $_REQUEST['DATE_FROM'];
$_SESSION['ZAKAZ']['DATE_TO'] = $_REQUEST['DATE_TO'];
$_SESSION['ZAKAZ']['SES_TELMOB'] = $_REQUEST['SES_TELMOB'];
$_SESSION['ZAKAZ']['SES_NAME'] = $_REQUEST['SES_NAME'];
$_SESSION['ZAKAZ']['SES_ORDER'] = $_REQUEST['SES_ORDER'];


$pagesize=20;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ZAKAZ A where 1'.
(($_REQUEST['SES_TELMOB'] != '') ? ' and REPLACE(REPLACE(TELMOB, \' \', \'\'), \'-\', \'\') like "%'.$_REQUEST['SES_TELMOB'].'%"' : '').
(($_REQUEST['SES_NAME'] != '') ? ' and NAME like "%'.$_REQUEST['SES_NAME'].'%"' : '').
(($_REQUEST['SES_ORDER'] != '') ? ' and ZAKAZ_ID = '.$_REQUEST['SES_ORDER'].'' : '').
(($_REQUEST['ZAKAZSTATUS_ID'] != 'all') ? ' and STATUS='.$_REQUEST['ZAKAZSTATUS_ID'] : '').
(($_REQUEST['SES_SUPPLIER_ID'] != 'all') ? ' and SUPPLIER_ID='.$_REQUEST['SES_SUPPLIER_ID'] : '').
(($_REQUEST['SES_CMF_USER_ID'] != 'all') ? ' and CMF_USER_ID='.$_REQUEST['SES_CMF_USER_ID'] : '').
(($_REQUEST['DATE_FROM'] != '')? " and DATE(A.DATA) >= DATE(STR_TO_DATE('{$_REQUEST['DATE_FROM']}', '%Y-%m-%d %H:%i'))":'').
(($_REQUEST['DATE_TO'] != '')? " and DATE(A.DATA) <= DATE(STR_TO_DATE('{$_REQUEST['DATE_TO']}', '%Y-%m-%d %H:%i'))":''));

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="ZAKAZ.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}

$sth=$cmf->execute('select A.ZAKAZ_ID,DATE_FORMAT(A.DATA,"%Y-%m-%d %H:%i"),A.NAME,A.CMF_USER_ID,A.SUPPLIER_ID,A.EMAIL,A.TELMOB,A.INFO,A.PAYMENT,A.STATUS from ZAKAZ A where 1'.
(($_REQUEST['SES_TELMOB'] != '') ? ' and REPLACE(REPLACE(TELMOB, \' \', \'\'), \'-\', \'\') like "%'.$_REQUEST['SES_TELMOB'].'%"' : '').
(($_REQUEST['SES_NAME'] != '') ? ' and NAME like "%'.$_REQUEST['SES_NAME'].'%"' : '').
(($_REQUEST['SES_ORDER'] != '') ? ' and ZAKAZ_ID = '.$_REQUEST['SES_ORDER'].'' : '').
(($_REQUEST['ZAKAZSTATUS_ID'] != 'all') ? ' and STATUS='.$_REQUEST['ZAKAZSTATUS_ID'] : '').
(($_REQUEST['SES_SUPPLIER_ID'] != 'all') ? ' and SUPPLIER_ID='.$_REQUEST['SES_SUPPLIER_ID'] : '').
(($_REQUEST['SES_CMF_USER_ID'] != 'all') ? ' and CMF_USER_ID='.$_REQUEST['SES_CMF_USER_ID'] : '').
(($_REQUEST['DATE_FROM'] != '')? " and DATE(A.DATA) >= DATE(STR_TO_DATE('{$_REQUEST['DATE_FROM']}', '%Y-%m-%d %H:%i'))":'').
(($_REQUEST['DATE_TO'] != '')? " and DATE(A.DATA) <= DATE(STR_TO_DATE('{$_REQUEST['DATE_TO']}', '%Y-%m-%d %H:%i'))":'').''.' order by A.DATA desc limit ?,?',(int)$pagesize*((int)$_REQUEST['p']-1),(int)$pagesize);

include "lib/zakaz/zakaz.lib.php";

print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td><th>N</th><th>Дата заказа</th><th>Ф.И.О.</th><th>Менеджер</th><th>Поставщики</th><th>E-mail</th><th>Телефон</th><th>Заметки</th><th>Способ оплаты</th><th>Статус</th><td></td><td></td></tr>

EOF;

$sth_currency = $cmf->execute("select * from CURRENCY where CURRENCY_ID = 1");
$default_currency = mysql_fetch_array($sth_currency, MYSQL_ASSOC);

if(is_resource($sth))
while(list($V_ZAKAZ_ID,$V_DATA,$V_NAME,$V_CMF_USER_ID,$V_SUPPLIER_ID,$V_EMAIL,$V_TELMOB,$V_INFO,$V_PAYMENT,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
print <<<EOF
<input type="hidden" name="f" value="{$_REQUEST['f']}"/>
EOF;

$V_CMF_USER_ID=$cmf->selectrow_arrayQ('select NAME from CMF_USER where CMF_USER_ID=?',$V_CMF_USER_ID);

$V_SUPPLIER_ID=$cmf->selectrow_arrayQ('select NAME from SUPPLIER where SUPPLIER_ID=?',$V_SUPPLIER_ID);

$V_PAYMENT=$cmf->selectrow_arrayQ('select NAME from PAYMENT where PAYMENT_ID=?',$V_PAYMENT);

$V_STATUS=$cmf->selectrow_arrayQ('select NAME from ZAKAZSTATUS where ZAKAZSTATUS_ID=?',$V_STATUS);



    $V_COLOR = $cmf->selectrow_arrayQ("select COLOR from ZAKAZSTATUS where NAME='".$V_STATUS."'");
    if(empty($V_COLOR) && $_REQUEST['f'] == 0) $V_COLOR = '#FF00FF';

    $V_STATUS = '<table width="100%" cellspacing="0"><tr><td style="background-color: '.$V_COLOR.'">&nbsp;</td></tr></table>';

print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_ZAKAZ_ID" /></td>
<td>$V_ZAKAZ_ID</td><td>$V_DATA</td><td>$V_NAME</td><td>$V_CMF_USER_ID</td><td>$V_SUPPLIER_ID</td><td>$V_EMAIL</td><td>$V_TELMOB</td><td>$V_INFO</td><td>$V_PAYMENT</td><td>$V_STATUS</td><td>
EOF;


 $sth5=$cmf->execute(' select I.NAME
                            , I.ITEM_ID
                            , I.TYPENAME
                            , I.ARTICLE
                            , C.CATALOGUE_ID
                            , B.NAME
                            , CR.SNAME
                            , Z.PRICE
                            , Z.CURRENCY_ID
                            , Z.ITEM_PRICE
                            , Z.ITEM_CURRENCY
                            , Z.PURCHASE_PRICE
                     from ZAKAZ_ITEM  Z
                     join ITEM I using (ITEM_ID)
                     join CATALOGUE C on  (C.CATALOGUE_ID = I.CATALOGUE_ID)
                     join BRAND B on  (B.BRAND_ID = I.BRAND_ID)
                     join CURRENCY CR on  (CR.CURRENCY_ID = I.CURRENCY_ID)
                     where Z.ZAKAZ_ID = ?',$V_ZAKAZ_ID);

 while(list($VV_NAME
          , $VV_ITEM_ID
          , $VV_TYPENAME
          , $VV_ARTICLE
          , $VV_CATALOGUE_ID
          , $VV_BRAND_NAME
          , $VV_SNAME
          , $VV_PRICE
          , $VV_CURRENCY_ID
          , $VV_ITEM_PRICE
          , $VV_ITEM_CURRENCY
          , $VV_PURCHASE_PRICE
          )=mysql_fetch_array($sth5, MYSQL_NUM))
 {

   if($default_currency['CURRENCY_ID'] != $VV_CURRENCY_ID){
     $rate = $cmf->selectrow_array("select PRICE from CURRENCY where CURRENCY_ID=?", $VV_CURRENCY_ID);
     $VV_PRICE = round($VV_PRICE * $rate);
   }

   echo "<a href = '/admin/ITEM.php?e=ED&id=$VV_ITEM_ID&pid=$VV_CATALOGUE_ID' target='_brank'>$VV_TYPENAME $VV_BRAND_NAME $VV_NAME</a>";
   echo "<br/><b>Артикул:</b> $VV_ARTICLE<br/><b style='color:#003399'>Цена:</b> $VV_PRICE {$default_currency['SNAME']}";
   if(!empty($VV_ITEM_PRICE)) {
    echo "<br/><b style='color:#003399'>Цена $VV_ITEM_CURRENCY:</b> $VV_ITEM_PRICE {$VV_ITEM_CURRENCY}";
   }
   
   if(!empty($VV_PURCHASE_PRICE)) {
       echo "<br/><b style='color:#336600'>Цена закупки {$default_currency['SNAME']}:</b> {$VV_PURCHASE_PRICE} {$default_currency['SNAME']}";
   }
   echo "<hr />";
 }
print <<<EOF
</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="ZAKAZ.php?e=ED&amp;id=$V_ZAKAZ_ID&amp;p={$_REQUEST['p']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>&nbsp;
<a href="tovar_check.php?zakaz_id=$V_ZAKAZ_ID"><img src="i/flt.gif" border="0" title="Товарынй чек" /></a>&nbsp;
<a href="rashodnaya.php?zakaz_id=$V_ZAKAZ_ID"><img src="i/flt.gif" border="0" title="Расходная" /></a>


</td></tr>
EOF;
}
}

print '</table>';
}
print '</form>';
$cmf->MakeCommonFooter();
$cmf->Close();