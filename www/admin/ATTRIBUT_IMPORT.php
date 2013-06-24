<?php
require('core.php');
require_once 'lib/AttributImport/AttributImportModel.php';
require_once 'lib/AttributImport/AttributImport.php';

$cmf = new SCMF('ATTRIBUT_IMPORT');

if ($_POST) {
    $mode = !empty($_POST['mode']) ? $_POST['mode'] : '';

    $atModel = new AttributImportModel($cmf);
    $atImp = new AttributImport($atModel);

    switch ($mode) {
        case 'import':
            $atImp->run($_POST);
            break;
        case 'create':
            $atImp->create($_POST);
            break;
        case 'look_attr_val':
            $atImp->lookAttrVal($_POST);
            break;
    }
    exit;
}

session_set_cookie_params($cmf->sessionCookieLifeTime, '/admin/');
session_start();

if (!$cmf->GetRights()) {
    header('Location: login.php');
    exit;
}
$cmf->HeaderNoCache();
$cmf->makeCookieActions();

$cmf->MakeCommonHeader();
$selHtml = '';
function selectHtml($parentId = 0, $level = 1)
{
    global $cmf, $selHtml;

    $sth = $cmf->execute(
        'select CATALOGUE_ID
                                     , CONCAT(REPEAT(\'-\', ' . $level . '*2-1), NAME) as NAME
                        from CATALOGUE where PARENT_ID = ' . $parentId . ' order by ORDERING'
    );
    if (is_resource($sth)) {
        while (list($V_CATALOGUE_ID, $V_NAME) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $selHtml .= "<option value='{$V_CATALOGUE_ID}'>|{$V_NAME}</option>";
            $level++;
            selectHtml($V_CATALOGUE_ID, $level);
            $level--;
        }
    }
}

selectHtml(0, 1);
print <<<EOF
<style>
.error{
    background: #fceced;
    padding: 7px 10px 9px;
    margin: 0 0 5px;
}
.error p{
    color: #CC0000;
    font-weight: bold;
    font-size: 12px;
}

.error ul{
    margin: 5px;
    list-style: disc;
    font-weight: bold;
}

.error ul li{
    margin-left: 5px;
}

.success {
    background-color: #EBF8A4;
    border-color: #A2D246;
    padding: 10px 10px 10px 25px;
}
</style>
<link rel="stylesheet" type="text/css" href="../css/fancybox.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox.js"></script>
<script type="text/javascript" src="js/index.js"></script>

<h2 class='h2'>Импорт атрибутов</h2>
<div id="error" class="error"></div>
<table cellspacing="1" cellpadding="5" border="0" class="l">
<tr><td>
<select name="catalog_id" id="catalog_id">
<option value='0'>-- все категории --</option>
$selHtml
</select>
</td></tr>
<tr><td>
<input type="button" name="run_attr_imp" value="Импортировать">
</td></tr>
</table>

<form method="POST" action="">
<table cellspacing="1" cellpadding="5" border="0" bgcolor="#CCCCCC" class="l" id="content">
</table>
</form>
EOF;
$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);
?>
