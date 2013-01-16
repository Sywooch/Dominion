<?
require("character_codings.php");
$in= fopen('tmp.tpl', 'r');

while (!feof($in))
{
  $buffer = fgets($in, 4096);
  if(preg_match("/^-----------------------\|(.+)\|/",$buffer,$matches)) $out= fopen($matches[1], 'w');
  elseif(preg_match("/^-----------------------/", $buffer)) fclose($out);
  else
  {
    $buffer=win1251_utf8($buffer);
    if(is_resource($out))fputs($out,$buffer);
  }
}
fclose($in);
?>
