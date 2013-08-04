<? require ('core.php');
$cmf= new SCMF('MYSQLEDITOR');
if (!$cmf->GetRights()) {header('Location: login.php'); exit;}
$cmf->HeaderNoCache();
$cmf->MakeCommonHeader();

$TABLE=isset($_REQUEST['TABLE'])?stripslashes($_REQUEST['TABLE']):'';

$TABLE=preg_replace("/</", "&lt;", $TABLE);
$TABLE=preg_replace("/>/", "&gt;", $TABLE);
?>

<form action=sql_edit.php method=POST enctype="multipart/form-data">
<textarea name=TABLE rows=5 cols="90" style="width:100%"><?=$TABLE?></textarea>
<input type=submit name=OK value="select">
<input type=submit name=OK value="execute">

<input type=submit name=OK value="upload">
</form>
<br><hr><br><?
if(isset($_REQUEST['OK']))
{
if($_REQUEST['OK'] == 'upload')
{
   $temp_file = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';

   if($temp_file)
   {
      if(is_uploaded_file($temp_file))
      {
         move_uploaded_file($temp_file,"./temp.sql");
         
         $file = 'temp.sql';
         $fp = fopen($file,'r');
         $buffer = '';
         while(!feof($fp))
         {
            $string = fgets($fp);
            if(!preg_match("/^-{2,}/",$string) && strlen($string) > 2)
            {
               $buffer .=$string;
            }
         }
         fclose($fp);
         
         if($buffer)
         {
             $parts = explode(";",$buffer);
             for($i=0;$i<sizeof($parts);$i++)
             {
                 if($parts[$i])
                 {
                    $sth = $cmf->execute($parts[$i]);
                    if($cmf->sql_err()) print $cmf->sql_err()."<br>";
                 }
             }
         }
         //@unlink($file);
      }
   }
}

if($_REQUEST['OK'] == 'execute')
{
  $cmf->execute(stripslashes($_REQUEST['TABLE']));
  print $cmf->sql_err();
}

if($_REQUEST['OK'] == 'select')
{

 if(strchr($_REQUEST['TABLE'],";"))
 {
    $parts = explode(";",$_REQUEST['TABLE']);
    for($i=0;$i<sizeof($parts);$i++)
    {
       if($parts[$i])
       {
          $sth = $cmf->execute(stripslashes($parts[$i]));
          if($cmf->sql_err()) print $cmf->sql_err()."<br>";
          else
          {
             print_query($sth);
          }
       }
    }
 }
 else
 {
    $sth=$cmf->execute(stripslashes($_REQUEST['TABLE']));
    if($cmf->sql_err()) print $cmf->sql_err();
    else
    {
      print_query($sth);
    }
 }

}
}

 $cmf->MakeCommonFooter();
 $cmf->Close();
 
 function print_query($sth)
 {
    print "<p><table border=1 align=center><tr>";
     $fcount=mysql_num_fields($sth);
     for($i=0;$i<$fcount;$i++) {
       print '<td>'.mysql_field_name($sth,$i).'</td>';
     }

     while($r=mysql_fetch_array($sth))
     {
       print '<tr>';
       for($i=0;$i<$fcount;$i++)
       {
           print '<td valign=top>'.$r[$i].'</td>';
       }
       print"</tr>";
     }
     mysql_free_result($sth);
     print '</table></p>';
 }

?>
