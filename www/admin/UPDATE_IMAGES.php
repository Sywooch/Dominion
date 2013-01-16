<?php
require ('core.php');

set_time_limit(0);
$cmf = new SCMF('UPDATE_IMAGES');

if (!$cmf->GetRights()) {
//    header('Location: login.php'); exit;
}

$cmf->HeaderNoCache();
$cmf->MakeCommonHeader();



print <<<EOF
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/index.js"></script>

<h2 class='h2'>Обновление фотографий</h2>
<!--<form action="/search/update/" method="POST" enctype="multipart/form-data">-->
     <input type="submit" name="update_images" value="Обновить фотографии">
<!--</form>-->
EOF;





if(isset($_POST['update']) && $_POST['update'])
{
    echo "<p>Обработка данных закончена.</p>";
}
//else echo '<font color="red">Выберите файл</font><br></br>';

$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);
?>