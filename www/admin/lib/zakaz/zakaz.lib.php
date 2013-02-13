<?php
$zakaz_profit = 0;
  
  $cmf->ARTICLE = 'ZAKAZ_PROFIT';

  if($cmf->GetRights()){
    include 'zakaz.model.php';
    include 'ProfitManager.php';
    $manager = new ProfitManager($cmf);
    $zakaz_profit = $manager->getProfit($_SESSION['ZAKAZ']);  
  }
  
  $cmf->ARTICLE = 'ZAKAZ';
  $cmf->GetRights();
  

  @print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="13">
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

</td></tr><tr>
                        <td colspan="13" class="main_tbl_title">
                        <table width="100%" border="0" cellspacing="5" cellpadding="0">
                          <tr>
                            <td width="100">Статус заказа</td>
                            <td align="left"><select name="ZAKAZSTATUS_ID">
EOF;
                        
                        $COUNT = $cmf->selectrow_array('select count(*) from ZAKAZ ');
                        $countNotViewed = $cmf->selectrow_array("select count(*) from ZAKAZ where STATUS = 0 or STATUS is NULL");
                        
                        
                        if ( $_REQUEST['ZAKAZSTATUS_ID'] == 'all') 
                        {
                                print '<option value="all">Все ('.$COUNT.')</option>';
                        }
                        else 
                        {
                                print '<option value="all" selected="selected">Все ('.$COUNT.')</option>';
                        }
                        
                        if ( $_REQUEST['ZAKAZSTATUS_ID'] == 0 ) 
                        {
                                print '<option value="0" class="red">Необработанные ('.$countNotViewed.')</option>';
                        }
                        else 
                        {
                                print '<option value="0" selected="selected" style="color:FF00FF;">Необработанные ('.$countNotViewed.')</option>';
                        }
                        
                        $vopr = $cmf->select('select ZAKAZSTATUS_ID,NAME,COLOR from ZAKAZSTATUS order by ORDERING');
                        $index = sizeof($vopr);
                        for($i=0; $i<$index; $i++)
                        {
                                $V_ZAKAZSTATUS_ID       = $vopr[$i]['ZAKAZSTATUS_ID'];
                                $V_NAME                         = $vopr[$i]['NAME'];
                                $V_COLOR                         = $vopr[$i]['COLOR'];
                                
                                $COUNT = $cmf->selectrow_array('select count(*) from ZAKAZ where STATUS=?',$V_ZAKAZSTATUS_ID);
                                
                                if($_REQUEST['ZAKAZSTATUS_ID'] == $V_ZAKAZSTATUS_ID)
                                {
                                        print '<option selected="selected" value="'.$V_ZAKAZSTATUS_ID.'" style="color:'.$V_COLOR.'; font-weight: bold;">'.$V_NAME.' ('.$COUNT.')</option>';
                                }
                                else 
                                {
                                     if($V_COLOR!='') print '<option value="'.$V_ZAKAZSTATUS_ID.'" style="color:'.$V_COLOR.'">'.$V_NAME.' ('.$COUNT.')</option>';
                                     else print '<option value="'.$V_ZAKAZSTATUS_ID.'">'.$V_NAME.' ('.$COUNT.')</option>';
                                }
                        }
print <<<EOF
</select>
</td>
<td rowspan="3">
<button type="submit" name="show" title="Показать">Показать</button>
</td>
</tr>
<tr>
<td width="100">Дата</td>
<td align="left">
<b>C:</b><input type="hidden" id="DATE_FROM" name="DATE_FROM" value="{$_REQUEST['DATE_FROM']}" />
EOF;

if($_REQUEST['DATE_FROM']) $V_DAT_FROM = substr($_REQUEST['DATE_FROM'],8,2).".".substr($_REQUEST['DATE_FROM'],5,2).".".substr($_REQUEST['DATE_FROM'],0,4);//." ".substr($_REQUEST['DATE_FROM'],11,2).":".substr($_REQUEST['DATE_FROM'],14,2);
else $V_DAT_FROM = $_SESSION['ZAKAZ']['DATE_FROM'];


        
        @print <<<EOF
        <span id="DATE_DATE_FROM">$V_DAT_FROM</span>
        <img src="img/img.gif" id="f_trigger_DATE_FROM" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATE_FROM",
                       displayArea    :    "DATE_DATE_FROM",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATE_FROM",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
&nbsp;<b>По:</b><input type="hidden" id="DATE_TO" name="DATE_TO" value="{$_REQUEST['DATE_TO']}" />
EOF;

if($_REQUEST['DATE_TO']) $V_DAT_TO = substr($_REQUEST['DATE_TO'],8,2).".".substr($_REQUEST['DATE_TO'],5,2).".".substr($_REQUEST['DATE_TO'],0,4);//." ".substr($V_DELIVERYDATA,11,2).":".substr($V_DELIVERYDATA,14,2);
else $V_DAT_TO = $_SESSION['ZAKAZ']['DATE_TO'];

$FILT_CMF_USER_ID=$cmf->Spravotchnik($_REQUEST['SES_CMF_USER_ID'],'select CMF_USER_ID,NAME from CMF_USER  order by NAME');
$FILT_SUPPLIER_ID=$cmf->Spravotchnik($_REQUEST['SES_SUPPLIER_ID'],'select SUPPLIER_ID,NAME from SUPPLIER  order by NAME');
        
        @print <<<EOF
        <span id="DATE_DATE_TO">$V_DAT_TO</span>
        <img src="img/img.gif" id="f_trigger_DATE_TO" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        
            
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATE_TO",
                       displayArea    :    "DATE_DATE_TO",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATE_TO",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>



</td></tr>
<tr>
<td width="100">Телефон</td>
<td align="left">
<input type="text" name="SES_TELMOB" value="{$_REQUEST["SES_TELMOB"]}" />
</td></tr>
<tr>
<td width="100">Имя</td>
<td align="left">
<input type="text" name="SES_NAME" value="{$_REQUEST["SES_NAME"]}" />
</td></tr>
<tr>
<td width="100">Номер заказа</td>
<td align="left">
<input type="text" name="SES_ORDER" value="{$_REQUEST["SES_ORDER"]}" />
</td></tr>
<td width="100">Менеджеры</td>
<td align="left">
<select name="SES_CMF_USER_ID">
<option value="all">-- все менеджеры --</option>
$FILT_CMF_USER_ID
</select>
</td>
</tr>
<tr>
<td width="100">Поставщики</td>
<td align="left">
<select name="SES_SUPPLIER_ID">
<option value="all">-- все поставщики --</option>
$FILT_SUPPLIER_ID
</select>
</td></tr>
<tr>
<td width="100"><b>Профит:</b></td>
<td align="left">
<b>$zakaz_profit</b>
</td></tr>

</table></td></tr>
EOF;

?>
