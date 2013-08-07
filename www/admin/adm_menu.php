<? require ('core.php');
$cmf= new SCMF();
$cmf->Header();
?>
<html>
<head>
	<title>back-office</title>
	<style>
	td {font-family:tahoma,arial,helvetica,geneve,sans-serif; font-size:11px;}
	body {font-family:tahoma,arial,helvetica,geneve,sans-serif; font-size:11px;}
	input {border: 2px groove;font-family:tahoma,arial,helvetica,geneve,sans-serif; font-size:11px;}
	select {font-family:tahoma,arial,helvetica,geneve,sans-serif; font-size:11px;}
	.small {font-size:10px;color:#cccccc;}
	</style>
</script></head>

<body style="background: #a0a0a0" topmargin=0 leftmargin=0 rightmargin=0 marginwidth=0 marginheight=0 text=#efefef  link=#ff3300 alink=#bb0000 vlink=#ff3300>
<table width=100% border=0 cellpadding=0 cellspacing=0 bgcolor=#cccccc height="56">
<tr>
<form target=mainFrame onsubmit="top.mainFrame.location=P.options[P.selectedIndex].value;return false;">
<input type=hidden name=all_date value='1'>
<td width=1%><a href="/admin/" target="_top"><img src="img/logo.gif" alt="" width="216" height="41"  hspace=20 vspace="7" border=0></a><br></td>
      <td width=60% align=center> <SELECT style="WIDTH: 80%" name=P>
<?

$sth=$cmf->execute('select SCRIPTS_ID,NAME,DESCRIPTION,URL from SCRIPTS where STATUS=1 order by ORDER_');
while(list($V_SCRIPTS_ID,$V_NAME,$V_DESCRIPTION,$V_URL)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_URL){print "<option value='$V_URL'>$V_NAME</option>";}
else {print "<option value='' style='color:#eeeeee'>$V_NAME</option>";}
}
?></SELECT>
&nbsp;<input type=submit value=">>">
</td>
<td width=40%>
<input type=button  style="background:#0956A3;font-weight:bold; color:#ffffff;width:60%;" value="выйти" onClick="parent.location.href='session.cgi?page=logout'"><br>
</td>
<!-- td width=1% nowrap align=center><a href="mailto:asabay@gmail.com" target=_blank style="color:#000000"><b>programmed by<br/>Sabay</b></a></td -->
</form></tr>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 bgcolor=#808080>
<tr><td>
<img src="imgs/hi.gif" alt="" width=1 height=5 border=0><br>
</td></tr>
</table>

</body>
</html>
</body>
</html>
<?
$cmf->Close();
?>
