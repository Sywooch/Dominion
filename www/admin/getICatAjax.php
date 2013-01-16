<?php

/**
 * Получить список каталогов для вывода в админке
 * для AJAX
 */
require ('core.php');
$cmf = new SCMF();

if (!$cmf->GetRights()) {
    header('Location: login.php');
    exit;
}

$cmf->Header();

if (isset($_REQUEST['catId'])) {
    $catId = (int) $_REQUEST['catId'];
} else {
    print "<option> нет доступного товара для катлога</option>";
    die();
}



$sql = "SELECT A.ITEM_ID
        , concat(B.NAME, ' ', A.NAME, ' (Артикул ', A.ARTICLE, ')')
        , C.SNAME
        , A.PRICE
        , A.PURCHASE_PRICE
        FROM
          ITEM A
        LEFT JOIN BRAND B
        ON (A.BRAND_ID = B.BRAND_ID)
        LEFT JOIN CURRENCY C
        USING (CURRENCY_ID)
        WHERE
          A.CATALOGUE_ID = ?
          AND A.STATUS = 1
          AND A.PRICE > 0
        ORDER BY
          A.NAME";

//$sth=call_user_func_array(array(&$cmf, 'execute'),$par);
$sth = $cmf->execute($sql, $catId);

if (is_resource($sth)) {
    while (list($id, $val, $currency, $resPrice, $purchasePrice) = mysql_fetch_array($sth, MYSQL_NUM)) {
        print "<option value='$id' price='$resPrice' purchase_price='$purchasePrice' currency_id='$currency'>" . $val . "</option>";
    }
}

$cmf->Close();
unset($cmf);