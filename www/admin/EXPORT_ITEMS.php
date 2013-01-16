<?php
set_time_limit(0);
require ('core.php');
require ('class.export_items.php');

require_once "exel_writer/Writer.php";

$cmf= new SCMF('EXPORT_ITEMS');
if (!$cmf->GetRights()) {header('Location: login.php'); exit;}

if(isset($_POST['export'])){  
  $export_meta = new export_items($cmf);
  $export_meta->run();
  $export_meta->getFile();
}

$cmf->HeaderNoCache();
$cmf->MakeCommonHeader();
?>
<h2 class='h2'>Экспорт товаров</h2>
<form action="EXPORT_ITEMS.php" method="POST" name="export_form">
<table cellspacing="0" cellpadding="3" border="0">
<tr>
  <td></td>
  <td><input type="submit" name="export" value="Экспорт"></td>
</tr>
</table>
</form>
<?
$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);

?>
