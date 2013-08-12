<?php
require ('core.php');
$cmf = new SCMF('CREATE_SEF_URL');

session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}
$cmf->HeaderNoCache();
$cmf->makeCookieActions();

$cmf->MakeCommonHeader();

print <<<EOF
<script type="text/javascript" src="js/index.js"></script>

<h2 class='h2'>Обновление ЧПУ-урлов</h2>
<input type="submit" name="create_sef_url" value="Перестроить урлы">
EOF;

if(isset($_POST['update']) && $_POST['update'])
{
    echo "<p>Обработка данных закончена.</p>";
}

$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);
?>
