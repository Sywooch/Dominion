<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('SHOPUSER');
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




if(!isset($_REQUEST['e1']))$_REQUEST['e1']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e1') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from ZAKAZ where USER_ID=? and ZAKAZ_ID=?',$_REQUEST['id'],$id);

 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e1') == 'Изменить')
{




$cmf->execute('update ZAKAZ set ZAKAZ_ID=?,DATA=? where USER_ID=? and ZAKAZ_ID=?',stripslashes($_REQUEST['ZAKAZ_ID'])+0,stripslashes($_REQUEST['DATA']),$_REQUEST['id'],$_REQUEST['iid']);

$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{


$_REQUEST['iid']=$_REQUEST['ZAKAZ_ID'];






$cmf->execute('insert into ZAKAZ (USER_ID,ZAKAZ_ID,DATA) values (?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['DATA']));
$_REQUEST['e']='ED';

$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_ZAKAZ_ID,$V_DATA)=$cmf->selectrow_arrayQ('select ZAKAZ_ID,DATE_FORMAT(DATA,"%Y-%m-%d %H:%i") from ZAKAZ where USER_ID=? and ZAKAZ_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


@print <<<EOF
<h2 class="h2">Редактирование - Заказы этого пользователя</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="SHOPUSER.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'" />';

@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>ID Заказа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ZAKAZ_ID" value="$V_ZAKAZ_ID" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Информация о заказе:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATA" name="DATA" value="$V_DATA" />
EOF;

if($V_DATA) $V_DAT_ = substr($V_DATA,8,2).".".substr($V_DATA,5,2).".".substr($V_DATA,0,4)." ".substr($V_DATA,11,2).":".substr($V_DATA,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DATA">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DATA" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATA",
                       displayArea    :    "DATE_DATA",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATA",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;





$visible=0;
}

if($cmf->Param('e1') == 'Новый')
{
list($V_ZAKAZ_ID,$V_DATA)=array('','');


$V_DATA=$cmf->selectrow_array('select now()');
@print <<<EOF
<h2 class="h2">Добавление - Заказы этого пользователя</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="SHOPUSER.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>ID Заказа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ZAKAZ_ID" value="$V_ZAKAZ_ID" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Информация о заказе:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="DATA" name="DATA" value="$V_DATA" />
EOF;

if($V_DATA) $V_DAT_ = substr($V_DATA,8,2).".".substr($V_DATA,5,2).".".substr($V_DATA,0,4)." ".substr($V_DATA,11,2).":".substr($V_DATA,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_DATA">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_DATA" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "DATA",
                       displayArea    :    "DATE_DATA",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_DATA",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}









if(($_REQUEST['e']=='Удалить') and isset($_REQUEST['id']) and $cmf->D)
{

foreach ($_REQUEST['id'] as $id)
 {
$cmf->execute('delete from SHOPUSER where USER_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{



$_REQUEST['id']=$cmf->GetSequence('SHOPUSER');

$_REQUEST['IS_NEW']=isset($_REQUEST['IS_NEW']) && $_REQUEST['IS_NEW']?1:0;









$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;


$cmf->execute('insert into SHOPUSER (USER_ID,PASSWORD,SURNAME,NAME,BONUS,REGDATA,PRIVATEINFO,TELMOB,TELMOBT1,EMAIL,STATUS) values (?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['PASSWORD']),stripslashes($_REQUEST['SURNAME']),stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['BONUS']),stripslashes($_REQUEST['REGDATA']),stripslashes($_REQUEST['PRIVATEINFO']),stripslashes($_REQUEST['TELMOB']),stripslashes($_REQUEST['TELMOBT1']),stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['STATUS']));


$_REQUEST['e']='ED';

}

if($_REQUEST['e'] == 'Изменить')
{


$_REQUEST['IS_NEW']=isset($_REQUEST['IS_NEW']) && $_REQUEST['IS_NEW']?1:0;









$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;

$cmf->execute('update SHOPUSER set PASSWORD=?,SURNAME=?,NAME=?,BONUS=?,REGDATA=?,PRIVATEINFO=?,TELMOB=?,TELMOBT1=?,EMAIL=?,STATUS=? where USER_ID=?',stripslashes($_REQUEST['PASSWORD']),stripslashes($_REQUEST['SURNAME']),stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['BONUS']),stripslashes($_REQUEST['REGDATA']),stripslashes($_REQUEST['PRIVATEINFO']),stripslashes($_REQUEST['TELMOB']),stripslashes($_REQUEST['TELMOBT1']),stripslashes($_REQUEST['EMAIL']),stripslashes($_REQUEST['STATUS']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_USER_ID,$V_PASSWORD,$V_SURNAME,$V_NAME,$V_BONUS,$V_REGDATA,$V_PRIVATEINFO,$V_TELMOB,$V_TELMOBT1,$V_EMAIL,$V_STATUS)=
$cmf->selectrow_arrayQ('select USER_ID,PASSWORD,SURNAME,NAME,BONUS,DATE_FORMAT(REGDATA,"%Y-%m-%d %H:%i"),PRIVATEINFO,TELMOB,TELMOBT1,EMAIL,STATUS from SHOPUSER where USER_ID=?',$_REQUEST['id']);



$V_STATUS=$V_STATUS?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Пользователи</h2>


<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="SHOPUSER.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(PASSWORD) &amp;&amp; checkXML(SURNAME) &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(BONUS) &amp;&amp; checkXML(PRIVATEINFO) &amp;&amp; checkXML(TELMOB) &amp;&amp; checkXML(TELMOBT1) &amp;&amp; checkXML(EMAIL);">
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="type" value="5" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$REQUEST['s']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><td width="1%"><b> ID :<br /><img src="img/hi.gif" width="125" height="1" /></b></td><td width="100%">$V_USER_ID</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Пароль:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PASSWORD" value="$V_PASSWORD" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Фамилия:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SURNAME" value="$V_SURNAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Имя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Бонусы:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="BONUS" rows="5" cols="90">$V_BONUS</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата регистрации:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="REGDATA" name="REGDATA" value="$V_REGDATA" />
EOF;

if($V_REGDATA) $V_DAT_ = substr($V_REGDATA,8,2).".".substr($V_REGDATA,5,2).".".substr($V_REGDATA,0,4)." ".substr($V_REGDATA,11,2).":".substr($V_REGDATA,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_REGDATA">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_REGDATA" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "REGDATA",
                       displayArea    :    "DATE_REGDATA",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_REGDATA",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Доп. инфо (личное):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="PRIVATEINFO" rows="5" cols="90">$V_PRIVATEINFO</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Телефон:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TELMOB" value="$V_TELMOB" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Время звонка на моб. тел.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TELMOBT1" value="$V_TELMOBT1" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>E-mail:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" onclick="this.form.action='EDITER.php';" value="Xml" class="gbt bxml" />&#160;&#160;
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />

EOF;




print <<<EOF
<a name="f1"></a><h3 class="h3">Заказы этого пользователя</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="SHOPUSER.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="4">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />

<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select ZAKAZ_ID,DATE_FORMAT(DATA,"%Y-%m-%d %H:%i") from ZAKAZ where USER_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>ID Заказа</th><th>Информация о заказе</th><td></td></tr>
EOF;
while(list($V_ZAKAZ_ID,$V_DATA)=mysql_fetch_array($sth, MYSQL_NUM))
{


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_ZAKAZ_ID" /></td>
<td>$V_ZAKAZ_ID</td><td>$V_DATA</td><td nowrap="">

<a href="SHOPUSER.php?e1=ED&amp;iid=$V_ZAKAZ_ID&amp;id={$_REQUEST['id']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}


if($_REQUEST['e'] == 'Новый')
{
list($V_USER_ID,$V_IS_NEW,$V_PASSWORD,$V_SURNAME,$V_NAME,$V_BONUS,$V_REGDATA,$V_PRIVATEINFO,$V_TELMOB,$V_TELMOBT1,$V_EMAIL,$V_STATUS)=array('','','','','','','','','','','','');

$V_REGDATA=$cmf->selectrow_array('select now()');
$V_STATUS='checked';
@print <<<EOF
<h2 class="h2">Добавление - Пользователи</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="SHOPUSER.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(PASSWORD) &amp;&amp; checkXML(SURNAME) &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(BONUS) &amp;&amp; checkXML(PRIVATEINFO) &amp;&amp; checkXML(TELMOB) &amp;&amp; checkXML(TELMOBT1) &amp;&amp; checkXML(EMAIL);">
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>

<tr bgcolor="#FFFFFF"><th width="1%"><b>Пароль:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="PASSWORD" value="$V_PASSWORD" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Фамилия:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="SURNAME" value="$V_SURNAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Имя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Бонусы:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="BONUS" rows="5" cols="90">$V_BONUS</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Дата регистрации:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="hidden" id="REGDATA" name="REGDATA" value="$V_REGDATA" />
EOF;

if($V_REGDATA) $V_DAT_ = substr($V_REGDATA,8,2).".".substr($V_REGDATA,5,2).".".substr($V_REGDATA,0,4)." ".substr($V_REGDATA,11,2).":".substr($V_REGDATA,14,2);
else $V_DAT_ = '';


        
        @print <<<EOF
        <table>
        <tr><td><div id="DATE_REGDATA">$V_DAT_</div></td>
        <td><img src="img/img.gif" id="f_trigger_REGDATA" style="cursor: pointer; border: 1px solid red;" title="Show calendar" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
        </td>
        </tr>
        </table>

        
        
        <script type="text/javascript">
        Calendar.setup({
                       inputField     :    "REGDATA",
                       displayArea    :    "DATE_REGDATA",
                       ifFormat       :    "%Y-%m-%d %H:%M",
                       daFormat       :    "%d.%m.%Y %H:%M",
                       showsTime      :    "true",
                       timeFormat     :    "24",
                       button         :    "f_trigger_REGDATA",
                       align          :    "Tl",
                       singleClick    :    false
                       });
        </script>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Доп. инфо (личное):<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="PRIVATEINFO" rows="5" cols="90">$V_PRIVATEINFO</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Телефон:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TELMOB" value="$V_TELMOB" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Время звонка на моб. тел.:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TELMOBT1" value="$V_TELMOBT1" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>E-mail:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="EMAIL" value="$V_EMAIL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>

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


print '<h2 class="h2">Пользователи</h2><form action="SHOPUSER.php" method="POST">';


if($_REQUEST['s'] == ''){$_REQUEST['s']=1;}
$_REQUEST['s']+=0;
$SORTNAMES=array('ID','Фамилия','Имя','E-mail');
$SORTQUERY=array('order by A.USER_ID ','order by A.USER_ID desc ','order by A.SURNAME ','order by A.SURNAME desc ','order by A.NAME ','order by A.NAME desc ','order by A.EMAIL ','order by A.EMAIL desc ');
list ($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SHOPUSER.php?s=$tmps{$filtpath}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SHOPUSER.php?s=$tmps{$filtpath}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="SHOPUSER.php?s=$tmps{$filtpath}">$tmp</a></th>
EOF;
        }
        $i++;
}


$pagesize=120;
if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}
if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{

$_REQUEST['count']=$cmf->selectrow_array('select count(*) from SHOPUSER A where 1');

$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 for($i=1;$i<=$_REQUEST['pcount'];$i++)
 {
  if($i==$_REQUEST['p']) { print '- <b class="red">'.$i.'</b>'; } else { print <<<EOF
- <a class="t" href="SHOPUSER.php?count={$_REQUEST['count']}&amp;p=$i&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}&amp;s={$_REQUEST['s']}{$filtpath}">$i</a>
EOF;
}
 }
 print'<br />';
}


$sth=$cmf->execute('select A.USER_ID,A.IS_NEW,A.SURNAME,A.NAME,A.EMAIL,A.STATUS from SHOPUSER A where 1'.' '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);





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
while(list($V_USER_ID,$V_IS_NEW,$V_SURNAME,$V_NAME,$V_EMAIL,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
        if ( $V_IS_NEW ) {
          $V_NAME         = '<b>'.$V_NAME.'</b>';
          $V_SURNAME      = '<b>'.$V_SURNAME.'</b>'; 
          $V_EMAIL        = '<b>'.$V_EMAIL.'</b>';
        }
        
if($V_STATUS){$V_STATUS='#FFFFFF';} else {$V_STATUS='#a0a0a0';}

print <<<EOF
<tr bgcolor="$V_STATUS">
<td><input type="checkbox" name="id[]" value="$V_USER_ID" /></td>
<td>$V_USER_ID</td><td>$V_SURNAME</td><td>$V_NAME</td><td>$V_EMAIL</td><td nowrap="">

EOF;

if ($cmf->W)
{

@print <<<EOF
<a href="SHOPUSER.php?e=ED&amp;id=$V_USER_ID&amp;p={$_REQUEST['p']}&amp;s={$_REQUEST['s']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>


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
