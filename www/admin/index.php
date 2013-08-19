<? require ('core.php');
$cmf= new SCMF('INDEX');
  
if (!$cmf->GetRights()) {header('Location: login.php'); exit;}

$cmf->HeaderNoCache();
$id=$cmf->Param('id');
if(!$id)$id=2;

$cmf->MakeCommonHeader($id);

if (!defined('DEFAULT_URL'))
  define ('DEFAULT_URL', 'ANOTHER_PAGES.php');
  
$cmf->FITRST_SHEET_URL = DEFAULT_URL;

if($cmf->FITRST_SHEET_URL!='' && $cmf->FITRST_SHEET_URL !== 'index.php')
?>
<script>
window.location="<?=$cmf->FITRST_SHEET_URL?>";
</script>
<?

?>
<tr><td style="padding: 0px 0px 0px 15px;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td nowrap="nowrap" style="background: url(img/main_cat.gif) no-repeat left top;" class="main_title">Главная страница</td>
<td width="100%">&nbsp;</td>
</tr>
</table>
						
</td></tr>
<tr><td style="padding: 0px 19px 0px 21px;"><br>
<h3>Добро пожаловать!</h3>						
</td></tr><?
$cmf->MakeCommonFooter();
$cmf->Close();
unset ($cmf);
?>
