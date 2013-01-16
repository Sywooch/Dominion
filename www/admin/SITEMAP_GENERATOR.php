<?php
require ('core.php');
$cmf = new SCMF('SITEMAP_GENERATOR');

session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}
$cmf->HeaderNoCache();
$cmf->makeCookieActions();

$cmf->MakeCommonHeader();

print <<<EOF
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="js/index.js"></script>

<h2 class='h2'>Генерация карту сайта</h2>
<input type="submit" name="sitemap_update" value="Сгенерировать">
EOF;

if(isset($_POST['sitemap_update']) && $_POST['sitemap_update'])
{
    echo "<p>Генерация закончена.</p>";
}

$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);
?>
