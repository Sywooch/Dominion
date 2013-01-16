<?php

require ('core.php');
$cmf = new SCMF();

if (!$cmf->GetRights()) {
    header('Location: login.php');
    exit;
}

$cmf->Header();

$par = split('\|', $_REQUEST['q']);

$_REQUEST['sel'] = stripslashes($_REQUEST['sel']);

array_unshift($par, $_REQUEST['sel']);

//$sth=call_user_func_array(array(&$cmf, 'execute'),$par);
$sth = $cmf->execute($par[0], $par[1]);
while (list($id, $val) = mysql_fetch_array($sth, MYSQL_NUM)) {
    $sql = "select I.PRICE
             , I.PRICE1
             , I.PURCHASE_PRICE
             , C.SNAME
        from ITEM I
        left join CURRENCY C on (C.CURRENCY_ID = I.CURRENCY_ID)
        where I.ITEM_ID = ?";

    $par2[0] = $sql;
    $par2[1] = $id;

    $sth2 = call_user_func_array(array(&$cmf, 'execute'), $par2);
    list($price, $price1, $purchasePrice, $currency) = mysql_fetch_array($sth2,
                                                                          MYSQL_NUM);

    $resPrice = !empty($price1) ? $price1 : $price;
    //print "add(name,'$id','".addslashes($val)."');";
    print "<option value='$id' price='$resPrice' purchase_price='$purchasePrice' currency_id='$currency'>" . $val . "</option>";
}

$cmf->Close();
unset($cmf);