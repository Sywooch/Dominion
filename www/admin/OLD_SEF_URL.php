<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('OLD_SEF_URL');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;







if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['s']))$_REQUEST['s']='';
if(!isset($_REQUEST['f']))$_REQUEST['f']='';












if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
$cmf->execute('delete from OLD_SEF_URL where OLD_SEF_URL_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{








$cmf->execute('insert into OLD_SEF_URL (OLD_SEF_URL_ID,NAME,SEF_SITE_URL_ID,DATE) values (null,?,?,?)',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['SEF_SITE_URL_ID'])+0,stripslashes($_REQUEST['DATE']));
$_REQUEST['id']=mysql_insert_id($cmf->dbh);

$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{






$cmf->execute('update OLD_SEF_URL set NAME=?,SEF_SITE_URL_ID=?,DATE=? where OLD_SEF_URL_ID=?',stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['SEF_SITE_URL_ID'])+0,stripslashes($_REQUEST['DATE']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_OLD_SEF_URL_ID,$V_NAME,$V_SEF_SITE_URL_ID,$V_DATE)=
$cmf->selectrow_arrayQ('select OLD_SEF_URL_ID,NAME,SEF_SITE_URL_ID,DATE_FORMAT(DATE,"%Y-%m-%d %H:%i") from OLD_SEF_URL where OLD_SEF_URL_ID=?',$_REQUEST['id']);



        $V_STR_SEF_SITE_URL_ID=$cmf->Spravotchnik($V_SEF_SITE_URL_ID,'select SEF_SITE_URL_ID,concat(SEF_URL," :: ",SITE_URL) from SEF_SITE_URL  order by concat(SEF_URL," :: ",SITE_URL)');
        
        
@print <<<EOF
<h2 class="h2">Редактирование - Таблица соответствия старых урлов урлам сайта</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="OLD_SEF_URL.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Старый урл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>ЧПУ-урл :: Урл сайта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<select name="SEF_SITE_URL_ID">$V_STR_SEF_SITE_URL_ID</select><br />
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата создания записи:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATE" name="DATE" value="$V_DATE" />
EOF;

if($V_DATE) $V_DAT_ = substr($V_DATE,8,2).".".substr($V_DATE,5,2).".".substr($V_DATE,0,4)." ".substr($V_DATE,11,2).":".substr($V_DATE,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DATE">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DATE" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATE",
                       displayArea    :    "DATE_DATE",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATE",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;



$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_OLD_SEF_URL_ID,$V_NAME,$V_SEF_SITE_URL_ID,$V_DATE)=array('','','','');

$V_STR_SEF_SITE_URL_ID=$cmf->Spravotchnik($V_SEF_SITE_URL_ID,'select SEF_SITE_URL_ID,concat(SEF_URL," :: ",SITE_URL) from SEF_SITE_URL  order by concat(SEF_URL," :: ",SITE_URL)');     
$V_DATE=$cmf->selectrow_array('select now()');
@print <<<EOF
<h2 class="h2">Добавление - Таблица соответствия старых урлов урлам сайта</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="OLD_SEF_URL.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(NAME);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Старый урл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>ЧПУ-урл :: Урл сайта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">
<select name="SEF_SITE_URL_ID">$V_STR_SEF_SITE_URL_ID</select><br />
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата создания записи:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATE" name="DATE" value="$V_DATE" />
EOF;

if($V_DATE) $V_DAT_ = substr($V_DATE,8,2).".".substr($V_DATE,5,2).".".substr($V_DATE,0,4)." ".substr($V_DATE,11,2).":".substr($V_DATE,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DATE">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DATE" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATE",
                       displayArea    :    "DATE_DATE",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATE",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;
$visible=0;
}

if($visible)
{


print '<h2 class="h2">Таблица соответствия старых урлов урлам сайта</h2><form action="OLD_SEF_URL.php" method="POST">';



$_REQUEST['s']+=0;
$SORTNAMES=array('N','Старый урл','ЧПУ-урл :: Урл сайта','Дата создания записи');
$SORTQUERY=array('order by A.OLD_SEF_URL_ID ','order by A.OLD_SEF_URL_ID desc ','order by A.NAME ','order by A.NAME desc ','order by A.SEF_SITE_URL_ID ','order by A.SEF_SITE_URL_ID desc ','order by A.DATE ','order by A.DATE desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="OLD_SEF_URL.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="OLD_SEF_URL.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="OLD_SEF_URL.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}


$pagesize=50;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from OLD_SEF_URL A where 1');

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="OLD_SEF_URL.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.OLD_SEF_URL_ID,A.NAME,A.SEF_SITE_URL_ID,DATE_FORMAT(A.DATE,"%Y-%m-%d %H:%i") from OLD_SEF_URL A where 1'.' '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="6">
EOF;

if ($cmf->W)
@print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" />
EOF;

@print <<<EOF
<img src="img/hi.gif" width="4" height="1" />
EOF;
if ($cmf->D)
  print '<input type="submit" name="e" onclick="return dl();" value="Удалить" class="gbt bdel" />';

@print <<<EOF
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;

print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td>$HEADER<td></td></tr>
 
EOF;

if(is_resource($sth))
while(list($V_OLD_SEF_URL_ID,$V_NAME,$V_SEF_SITE_URL_ID,$V_DATE)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_SEF_SITE_URL_ID=$cmf->selectrow_arrayQ('select concat(SEF_URL," :: ",SITE_URL) from SEF_SITE_URL where SEF_SITE_URL_ID=?',$V_SEF_SITE_URL_ID);
                                        


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_OLD_SEF_URL_ID" /></td>
<td>$V_OLD_SEF_URL_ID</td><td>$V_NAME</td><td>$V_SEF_SITE_URL_ID</td><td>$V_DATE</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="OLD_SEF_URL.php?e=ED&amp;id=$V_OLD_SEF_URL_ID&amp;p={$_REQUEST['p']}&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


</td></tr>
EOF;
}
}
 
print '</table>';
}
print '</form>';
$cmf->MakeCommonFooter();
$cmf->Close();

?>
