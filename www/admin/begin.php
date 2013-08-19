<? require ('core.php');
$cmf= new SCMF();
$cmf->Header();
?><html>
<head>
<title>back-office</title>
<link href="admin.css" rel="stylesheet"/>
<style type="text/css">
.small {font-size: 10px; color: #000000}
a {font-weight: bold; font-size: 12px; text-decoration: none; color:#f20303}
a:hover {color: #000000; text-decoration: underline}
.ye {color: #0956a3;}
</style>
</head>
<body style="BACKGROUND: #5D5D5D" text="#efefef" vlink="#ffcc00" alink="#33cc00" link="#ffffff" leftmargin="0" topmargin="10" rightmargin="0" marginheight="10" marginwidth="0">
<table height="100%" cellspacing="0" cellpadding="0" width="100%" border="0">

<tr>
<td valign="top" align="middle">
<table cellspacing="1" cellpadding="10" width="95%" bgcolor="#cccccc" border="0">

<tr bgcolor="#666666">
<td bgcolor="#CCCCCC" class="ye"><?

$sth=$cmf->execute('select SCRIPTS_ID,NAME,DESCRIPTION,URL from SCRIPTS where STATUS=1 order by ORDER_');
while(list($V_SCRIPTS_ID,$V_NAME,$V_DESCRIPTION,$V_URL)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_URL)
{
?><img height="5" alt="" src="" width="1" border="0" /><br />
 <img height="7" alt="" src="" width="5" border="0" /><img height="1" alt="" src="" width="10" border="0" /><a href="<?=$V_URL?>" class="ye" target="mainFrame"><?=$V_NAME?></a><br />
 <img height="2" alt="" src="" width="1" border="0" /><br /><?

if($V_DESCRIPTION)
{
?><table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
<td width="1%"><img height="1" alt="" src="" width="30" border="0" /></td>
<td width="99%"><span class="small"><?=$V_DESCRIPTION?><br />
</span></td>
</tr>
</table><?
}
}
else
{
?><img height=5  alt="" src="" width=1 border=0>
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#333333">
<tr><td><img height=7 alt="" src="" width=5 border=0><img height=1 alt="" src="" width=10 border=0><b><?=$V_NAME?></b></td></tr>
</table><?

}
}

?>
<br />
</td></tr>
</table>
</td>
</tr>

</table>
</body>
</html>
<?
$cmf->Close();
?>
