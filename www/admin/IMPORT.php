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
<h2 class='h2'>РћР±РЅРѕРІР»РµРЅРёРµ С†РµРЅ РЅР° С‚РѕРІР°СЂ</h2>
<form action="IMPORT.php" method="POST" enctype="multipart/form-data">
<b>Р’С‹Р±РµСЂРёС‚Рµ С„Р°Р№Р»:</b> <input type="file" name="file"> <br/><br/>
<input type="submit" name="update" value="РћР±РЅРѕРІРёС‚СЊ">
<!-- <input type="submit" name="update_later" value="РћР±РЅРѕРІРёС‚СЊ РїРѕ СЂР°СЃРїРёСЃР°РЅРёСЋ"> -->
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

