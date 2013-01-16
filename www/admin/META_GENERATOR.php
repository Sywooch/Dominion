<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
require ('core.php');
$cmf = new SCMF('META_GENERATOR');

session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}
$cmf->HeaderNoCache();
$cmf->makeCookieActions();

$cmf->MakeCommonHeader();

print <<<EOF
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="js/index.js"></script>

<h2 class='h2'>Генерация мета-описания</h2>
<form>
Сгенерировать для:<br />
<select name="gen_type" id="gen_type">
<option value="0">всего</option>
<option value="1">всего каталога</option>
<option value="2">каталога 1го уровня</option>
<option value="3">каталога 2го уровня и выше</option>
<option value="4">указанного каталога</option>
<option value="5">всех товаров</option>
<option value="6">новых товаров</option>
</select><br /><br />
Укажите каталог:<br />
<select name="gen_catalog_id" id="gen_catalog_id" disabled="disabled">
EOF;

$sth=$cmf->execute('select CATALOGUE_ID, NAME from CATALOGUE order by PARENT_ID, NAME');

if(is_resource($sth)) 
while(list($V_CATALOGUE_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{
print <<<EOF
  <option value="$V_CATALOGUE_ID">$V_NAME</option>
EOF;
}
print <<<EOF
</select>
 </form>
<input type="submit" name="meta_generate" value="Сгенерировать">
EOF;

if(isset($_POST['update']) && $_POST['update'])
{
    echo "<p>Генерация закончена.</p>";
}

$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);
?>
