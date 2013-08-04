<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('SEF_SITE_URL');
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
$cmf->execute('delete from SEF_SITE_URL where SEF_SITE_URL_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('SEF_SITE_URL');






$cmf->execute('insert into SEF_SITE_URL (SEF_SITE_URL_ID,SEF_URL,SITE_URL,DATE) values (?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['SEF_URL']),stripslashes($_REQUEST['SITE_URL']),stripslashes($_REQUEST['DATE']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{






$cmf->execute('update SEF_SITE_URL set SEF_URL=?,SITE_URL=?,DATE=? where SEF_SITE_URL_ID=?',stripslashes($_REQUEST['SEF_URL']),stripslashes($_REQUEST['SITE_URL']),stripslashes($_REQUEST['DATE']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_SEF_SITE_URL_ID,$V_SEF_URL,$V_SITE_URL,$V_DATE)=
$cmf->selectrow_arrayQ('select SEF_SITE_URL_ID,SEF_URL,SITE_URL,DATE_FORMAT(DATE,"%Y-%m-%d %H:%i") from SEF_SITE_URL where SEF_SITE_URL_ID=?',$_REQUEST['id']);



@print <<<EOF
<h2 class="h2">Редактирование - Таблица соответствия ЧПУ-урлов урлам сайта</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="SEF_SITE_URL.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(SEF_URL) &amp;&amp; checkXML(SITE_URL);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>ЧПУ-урл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SEF_URL" value="$V_SEF_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Урл сайта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SITE_URL" value="$V_SITE_URL" size="90" /><br />

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
list($V_SEF_SITE_URL_ID,$V_SEF_URL,$V_SITE_URL,$V_DATE)=array('','','','');

$V_DATE=$cmf->selectrow_array('select now()');
@print <<<EOF
<h2 class="h2">Добавление - Таблица соответствия ЧПУ-урлов урлам сайта</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 500px" class="f">
<form method="POST" action="SEF_SITE_URL.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(SEF_URL) &amp;&amp; checkXML(SITE_URL);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>ЧПУ-урл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SEF_URL" value="$V_SEF_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Урл сайта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SITE_URL" value="$V_SITE_URL" size="90" /><br />

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


print '<h2 class="h2">Таблица соответствия ЧПУ-урлов урлам сайта</h2><form action="SEF_SITE_URL.php" method="POST">';



$_REQUEST['s']+=0;
$SORTNAMES=array('N','ЧПУ-урл','Урл сайта','Дата создания записи');
$SORTQUERY=array('order by A.SEF_SITE_URL_ID ','order by A.SEF_SITE_URL_ID desc ','order by A.SEF_URL ','order by A.SEF_URL desc ','order by A.SITE_URL ','order by A.SITE_URL desc ','order by A.DATE ','order by A.DATE desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SEF_SITE_URL.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SEF_SITE_URL.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SEF_SITE_URL.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}


$pagesize=50;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from SEF_SITE_URL A where 1');

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="SEF_SITE_URL.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.SEF_SITE_URL_ID,A.SEF_URL,A.SITE_URL,DATE_FORMAT(A.DATE,"%Y-%m-%d %H:%i") from SEF_SITE_URL A where 1'.' '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





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
while(list($V_SEF_SITE_URL_ID,$V_SEF_URL,$V_SITE_URL,$V_DATE)=mysql_fetch_array($sth, MYSQL_NUM))
{


print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_SEF_SITE_URL_ID" /></td>
<td>$V_SEF_SITE_URL_ID</td><td>$V_SEF_URL</td><td>$V_SITE_URL</td><td>$V_DATE</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="SEF_SITE_URL.php?e=ED&amp;id=$V_SEF_SITE_URL_ID&amp;p={$_REQUEST['p']}&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
