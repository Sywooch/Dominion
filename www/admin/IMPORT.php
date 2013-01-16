<?
require ('core.php');
require_once 'excel/reader.php';
require_once 'IMPORT.class.php';
//error_reporting(0);


set_time_limit(3600);
$cmf= new SCMF('IMPORT');

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}

if(isset($_REQUEST['del_file']) && $_REQUEST['del_file'])
{
   $fn = $_REQUEST['fl'];
   if(file_exists($fn)) unlink($fn);
   header('Location: IMPORT.php');
}


$cmf->HeaderNoCache();
$cmf->MakeCommonHeader();
?>
<h2 class='h2'>Обновление цен на товар</h2>
<form action="IMPORT.php" method="POST" enctype="multipart/form-data">
<b>Выберите файл:</b> <input type="file" name="file"> <br/><br/>
<input type="submit" name="update" value="Обновить">
<!-- <input type="submit" name="update_later" value="Обновить по расписанию"> -->
</form>
<br>

<?



if(!empty($_POST['update']))
{
  $importer = new IMPORTER($cmf);
  // print_r($importer->upl_file);
    if ($importer->upl_file){
        //$importer->readFile();         
        $importer->read_saveFile();//!!!!!!!!!!!!!!!!!!!!!!!!!!!     
    }
  
  
  
} 


$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);




?>

