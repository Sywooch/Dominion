<? require ('core.php');
$cmf= new SCMF();

$error='';
list ($CMF_UID,$ADVERTIZER_ID,$CMF_URL)=array(0,0,'');

if($cmf->Param('e') == 'out')
{
  $CMF_UID='';
}
elseif($cmf->Param('e') == 'in')
{
  $REURI='index.php';
  if(preg_match("/\S/",$cmf->Param('login'))){ list($CMF_USER_ID,$CMF_UID,$CMF_URL)=$cmf->selectrow_array('select CMF_USER_ID,MD5_,URL from CMF_USER where LOGIN=? and PASS_=? and STATUS=1',$cmf->Param('login'),$cmf->Param('pass')); }

  if($CMF_UID)
  {
  if(!$CMF_URL)
    $CMF_URL=$cmf->selectrow_array("select CG.URL from CMF_GROUP CG inner join CMF_USER_GROUP CUG on (CG.CMF_GROUP_ID=CUG.CMF_GROUP_ID) where CG.URL is not null and CG.URL!='' and CUG.CMF_USER_ID=? limit 1",$CMF_USER_ID);

  if(!$CMF_URL) $CMF_URL=$REURI;
  setcookie('CMF_UID', $CMF_UID);
  header('Location: '.$CMF_URL);
  exit(0);
  
  }
  else 
  {
    $error='<b>Неверный логин или пароль</b><br>';
    $CMF_UID='';
  }
} 
  setcookie('CMF_UID', $CMF_UID);
  $cmf->HeaderNoCache();
?>   
<html>
<head>
        <title>Система управления сайтом</title>        
        <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

<div id="reg" style="text-align: center; position: relative; padding-top: 20%;">
<!-- img src="i/logo.gif" border="0" alt="" -->
<table class="selhead" cellspacing="0" cellpadding="0" style="width: 250px; margin-top: 15px;" align="center">
<tr>
<td id="first" style="vertical-align: bottom"><img src="img/redbox.gif" style="width:16px; height:15px;" /></td>
<td id="last" style="text-align: left"><img src="img/txt_admin_logon.gif" width="113" height="9" border="0" align="absmiddle" alt="ВХОД ДЛЯ АДМИНИСТРАТОРА" /></td>                     
</tr>
</table>

<table style="width: 250px; border: solid 1px #CCCCCC; border-top: none;" align="center">
<form action="login.php" method="POST">
<input type="hidden" name="e" value="in">
<tr>
<td><td align="right"><img src="i/icon_admin_part.gif" width="60" height="60" border="0" align="absmiddle" alt=""></td></td>
<td>
<table cellspacing="0" cellpadding="5" >
<tr>

<td colspan="2"><img src="i/0.gif" width="1" height="5" border="0" alt=""><?=$error?></td>
</tr>
<tr>
<td style="padding-left: 10px;">логин:</td>
<td><input type="text" name="login" class="i"/></td>
</tr>
<tr>
<td style="padding-left: 10px;">пароль:</td>
<td><input type="password" name="pass" class="i"/></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="image" name="ev" src="i/btn_login.gif" width="70" height="19" border="0" alt="Войти"></td>
</tr>
</table>
</td>
</tr>
</form>
</table>
</div>
</body>
</html>

<?
$cmf->Close();
unset ($cmf);
?>
