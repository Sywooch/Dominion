<?
require ('core.php');

set_time_limit(0);
$cmf = new SCMF('REBUILD_INDEX');

if (!$cmf->GetRights()) {
//	header('Location: login.php'); exit;
}

$cmf->HeaderNoCache();
$cmf->MakeCommonHeader();



print <<<EOF
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/index.js"></script>

<h2 class='h2'>Обновление каталога товаров</h2>
<!--<form action="/search/update/" method="POST" enctype="multipart/form-data">-->
     <input type="submit" name="update" value="Перестроить индекс поиска">
<!--</form>-->
EOF;





if(isset($_POST['update']) && $_POST['update'])
{
//    require_once ('importLib/Import.class.php');
//    $ClassImportItems = new ImportItems();
//
//    if($ClassImportItems){
//    	$ClassImportItems->import();
//
//	    $cmf->execute("delete from CAT_ITEM");
//	    $cmf->Rebuild(array(0));
//	    $cmf->CheckCount(0);
//	    $cmf->UpdateAllRanges();
//    }
    
//    $resize = new Resize();
//    $resize->addImage();
	
    echo "<p>Обработка данных закончена.</p>";
}
//else echo '<font color="red">Выберите файл</font><br></br>';

$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);
?>