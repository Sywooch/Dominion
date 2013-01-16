<? require ('core.php');
$cmf= new SCMF('ANOTHER_PAGES'); //REPORT_BUG
if (!$cmf->GetRights()) {header('Location: login.php'); exit;}
$cmf->HeaderNoCache();
$cmf->MakeCommonHeader();
$TABLE=mysql_escape_string($cmf->Param('TABLE'));
?>
<tr><td style="padding: 0px 19px 0px 21px;">
<h2 class='h2'>Сообщить об ошибке</h2>
<?
if (!$TABLE) {
?>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form action="report_bug.php" method=POST>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2"><input type=submit name=OK value="Отправить" class="gbt bsave"></td></tr>
<tr bgcolor="#FFFFFF"><th nowrap="nowrap">Адрес, где произошла ошибка</td><td width="90%"><input type="text" name="URL" size="90"></td></tr>
<tr bgcolor="#FFFFFF"><th nowrap="nowrap">Описание ошибки</td><td><textarea name=TABLE rows=5 cols="90" style="width:100%"></textarea></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2"><input type=submit name=OK value="Отправить" class="gbt bsave"></td></tr>
</form>
</table>
<br><?
}
else {
$cmf->execute('insert into CMF_BUG (CMF_BUG_ID,CMF_USER_ID,DATA,URL,DESCRIPTION,STATUS) values (null,?,now(),?,?,0)',$cmf->USER_ID,$cmf->Param('URL'),$TABLE);
?><h3>Письмо об ошибке было отправлено администратору системы. Спасибо за сотрудничество!</h3><?
}

?></td></tr></table></td></tr><?

$cmf->MakeCommonFooter();
$cmf->Close();
unset ($cmf);
?>
