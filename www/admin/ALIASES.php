<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('CATALOGUE');
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

if($_REQUEST['e'] == 'RET')
{

$_REQUEST['pid']=$cmf->selectrow_array('select ITEM_ID from ALIASES where ALIASES_ID=? ',$_REQUEST['id']);
}





if($cmf->Param('move_x'))
{
$BRANDS=$cmf->Spravotchnik(0,'select distinct BRAND_ID,NAME from BRAND order by NAME');
?><table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="f">
<form action="ALIASES.php" method="POST">
<input type="hidden" name="pid" value="<?=$_REQUEST['pid']?>">
<input type="hidden" name="p" value="<?=$_REQUEST['p']?>">
<input type="hidden" name="s" value="<?=$_REQUEST['s']?>"><?
if(isset($_REQUEST['id']))
foreach ($_REQUEST['id'] as $id){ ?><input type="hidden" name="id[]" value="<?=$id?>"/><? }
?><tr bgcolor="#F0F0F0" class="ftr"><td><input type="submit" name="e" value="Прикрепить" class="gbt bsave"/><input type="submit" name="e" value="Отменить" class="gbt bcancel"/></td></tr>
<tr bgcolor="#FFFFFF"><td><select name="brandID" onchange="return chan(this.form,ITEM_ID,'select ITEM_ID,NAME from ITEM where BRAND_ID=? order by NAME',this.value);"><option>--------</option><? echo $BRANDS ?></select><input type="text" name="q" value="" onkeyup="return chan(this.form,ITEM_ID,'select ITEM_ID,NAME from ITEM where BRAND_ID=? and NAME like ? order by NAME',brandID.value+'\|%'+this.value+'%');"/>
<select name="ITEM_ID" style="width:100%" size="30"></select></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td><input type="submit" name="e" value="Прикрепить" class="gbt bsave"/><input type="submit" name="e" value="Отменить" class="gbt bcancel"/></td></tr>
</form></table><?
$visible=0;
}

if($_REQUEST['e'] == 'Прикрепить')
{
 if(isset($_REQUEST['id']))
 foreach ($_REQUEST['id'] as $id)
 {
        $cmf->execute('update ALIASES set ITEM_ID=? where ALIASES_ID=?',$cmf->Param('ITEM_ID'),$id);
 }
}

if($_REQUEST['e'] == 'Прикрепить похожие')
{
 if(isset($_REQUEST['id']))
 for($i=0;$i<sizeof($_REQUEST['id']);$i++)
 {
     $id = $_REQUEST['id'][$i];
     $ids = '';
     if(isset($_REQUEST['ids'][$id])) $ids = $_REQUEST['ids'][$id];
     if(isset($ids) && $ids)
     {
       $cmf->execute('update ALIASES set ITEM_ID=? where ALIASES_ID=?',$_REQUEST['ids'][$_REQUEST['id'][$i]],$_REQUEST['id'][$i]);
     }
 }
}

if($_REQUEST['e'] == 'Открепить выбранные')
{
 if(isset($_REQUEST['id']))
 for($i=0;$i<sizeof($_REQUEST['id']);$i++)
 {
     $id = $_REQUEST['id'][$i];
     if($id)
     {
       $cmf->execute('update ALIASES set ITEM_ID=0 where ALIASES_ID=?',$id);
     }
 }
}











if(($_REQUEST['e'] == 'Удалить') and is_array($_REQUEST['id']) and ($cmf->D))
{

foreach ($_REQUEST['id'] as $id)
 {

$cmf->execute('delete from ALIASES where ALIASES_ID=?',$id);

 }

}



if($_REQUEST['e'] == 'Добавить')
{


$_REQUEST['id']=$cmf->GetSequence('ALIASES');







$cmf->execute('insert into ALIASES (ALIASES_ID,ITEM_ID,TYPENAME,BRAND_ID,NAME) values (?,?,?,?,?)',$_REQUEST['id'],stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['NAME']));


$_REQUEST['e'] ='ED';

}

if($_REQUEST['e'] == 'Изменить')
{







if(!empty($_REQUEST['pid'])) $cmf->execute('update ALIASES set ITEM_ID=?,TYPENAME=?,BRAND_ID=?,NAME=? where ALIASES_ID=?',stripslashes($_REQUEST['pid'])+0,stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['NAME']),$_REQUEST['id']);
else $cmf->execute('update ALIASES set TYPENAME=?,BRAND_ID=?,NAME=? where ALIASES_ID=?',stripslashes($_REQUEST['TYPENAME']),stripslashes($_REQUEST['BRAND_ID'])+0,stripslashes($_REQUEST['NAME']),$_REQUEST['id']);

$_REQUEST['e'] ='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_ALIASES_ID,$V_ITEM_ID,$V_TYPENAME,$V_BRAND_ID,$V_NAME)=$cmf->selectrow_arrayQ('select ALIASES_ID,ITEM_ID,TYPENAME,BRAND_ID,NAME from ALIASES where ALIASES_ID=?',$_REQUEST['id']);



$V_STR_BRAND_ID=$cmf->Spravotchnik($V_BRAND_ID,'select BRAND_ID,NAME from BRAND  order by NAME');        
					
print @<<<EOF
<h2 class="h2">Редактирование - Алиасы для моделей</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="ALIASES.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(TYPENAME) &amp;&amp; checkXML(NAME);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
EOF;



@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>Тип продукции:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TYPENAME" value="$V_TYPENAME" size="90" onfocus="_XDOC=this;" onkeydown="_etaKey(event)" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Производитель:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="BRAND_ID">
				
				
				
				
				<option value="0">не задан</option>
				
				$V_STR_BRAND_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Модель:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e" value="Назад" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;


$visible=0;
}

if($_REQUEST['e'] =='Новый')
{
list($V_ALIASES_ID,$V_ITEM_ID,$V_TYPENAME,$V_BRAND_ID,$V_NAME)=array('','','','','');


$V_STR_BRAND_ID=$cmf->Spravotchnik($V_BRAND_ID,'select BRAND_ID,NAME from BRAND  order by NAME');
					
@print <<<EOF
<h2 class="h2">Добавление - Алиасы для моделей</h2>
<a href="javascript:history.go(-1)">&#160;<b>вернуться</b></a><p />
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="ALIASES.php" ENCTYPE="multipart/form-data" name="frm" onsubmit="return true  &amp;&amp; checkXML(TYPENAME) &amp;&amp; checkXML(NAME);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e" value="Отменить" class="gbt bcancel" />
</td></tr>
EOF;



@print <<<EOF

<tr bgcolor="#FFFFFF"><th width="1%"><b>Тип продукции:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="TYPENAME" value="$V_TYPENAME" size="90" onfocus="_XDOC=this;" onkeydown="_etaKey(event)" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Производитель:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="BRAND_ID">
				
				
				
				
				<option value="0">не задан</option>
				
				$V_STR_BRAND_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Модель:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

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
$V_PARENTSCRIPTNAME=$cmf->selectrow_array('select NAME from ITEM where ITEM_ID=?',$_REQUEST['pid']);
if(!$V_PARENTSCRIPTNAME){$V_PARENTSCRIPTNAME='<span style="color:#FF0000">Нерубрицированные</span>';}


print <<<EOF
<h2 class="h2">$V_PARENTSCRIPTNAME / Алиасы для моделей</h2><form action="ALIASES.php" method="POST">

EOF;
if(!empty($_REQUEST['pid']))
{
   if($_REQUEST['pid']!='all')
   {
      ?><a href="ITEM.php?e=RET&amp;id=<?=$_REQUEST['pid']?>"><img src="i/back.gif" border="0" align="top" /> Назад</a><br /><?
   }
   else
   {
      ?><a href="ALIASES.php?pid=0"><img src="i/back.gif" border="0" align="top" /> Назад</a><br /><?
   }
}
else { ?><a href="CATALOGUE.php?e=RET&amp;id=<?=$_REQUEST['pid']?>"><img src="i/back.gif" border="0" align="top" /> Назад</a><br /><?}
$V_COUNT=$cmf->selectrow_array('select count(* ) from ALIASES A left join BRAND B  USING(BRAND_ID) where A.ITEM_ID=0');
echo "<b>Осталось не прикреплённых: $V_COUNT</b>";

print <<<EOF

EOF;




$_REQUEST['s']+=0;
$SORTNAMES=array('N','Тип продукции','Производитель','Модель');
$SORTQUERY=array('order by A.ALIASES_ID ','order by A.ALIASES_ID desc ','order by A.TYPENAME ','order by A.TYPENAME desc ','order by B.NAME,A.NAME ','order by B.NAME desc,A.NAME desc ','order by A.NAME ','order by A.NAME desc ');

//Ручные фильтры
$filters = '';
$filt_request = '';
foreach($_REQUEST as $key=>$val)
{
  if(preg_match('/^FILTER_(.+)$/',$key,$p))
  {
    if($val!='')
     {
        $filters.='&amp;'.$key.'='.$val;
     }
  }
}

list($HEADER,$i)=array('',0);

foreach ($SORTNAMES as $tmp)
{
        $tmps=$i*2;
        if(($_REQUEST['s']-$tmps)==0) 
        {
                $tmps+=1;
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ALIASES.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filters}">$tmp <img src="i/sdn.gif" border="0" /></a></th>
EOF;
        }
        elseif(($_REQUEST['s']-$tmps)==1)
        {
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ALIASES.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filters}">$tmp <img src="i/sup.gif" border="0" /></a></th>
EOF;
        } 
        else { 
$HEADER.=<<<EOF
<th nowrap=""><a class="b" href="ALIASES.php?pid={$_REQUEST['pid']}&amp;s=$tmps{$filters}">$tmp</a></th>
EOF;
        }
        $i++;
}


$pagesize=120;

if(!isset($_REQUEST['p']) || !($_REQUEST['p']) ){$_REQUEST['p']=1;}

if(!isset($_REQUEST['count']) || !$_REQUEST['count'])
{
if(!empty($_REQUEST['pid']) and $_REQUEST['pid']=='all')
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ALIASES A left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.ITEM_ID > 0'.($cmf->Param('FILTER_brandID')?' and (A.BRAND_ID='.mysql_escape_string($cmf->Param('FILTER_brandID')).' or A.BRAND_ID=0)':''),$_REQUEST['pid']);
}
else
{
$_REQUEST['count']=$cmf->selectrow_array('select count(*) from ALIASES A left join BRAND B on (A.BRAND_ID=B.BRAND_ID) where A.ITEM_ID=?'.($cmf->Param('FILTER_brandID')?' and (A.BRAND_ID='.mysql_escape_string($cmf->Param('FILTER_brandID')).' or A.BRAND_ID=0)':''),$_REQUEST['pid']);

}
$_REQUEST['pcount']=floor($_REQUEST['count']/$pagesize+0.9999);
if($_REQUEST['p'] > $_REQUEST['pcount']){$_REQUEST['p']=$_REQUEST['pcount'];}
}

if($_REQUEST['pcount'] > 1)
{
 $start=1;
if($_REQUEST['p']>15){$start=$_REQUEST['p']-15;}
 
 for($i=$start;$i<=$_REQUEST['pcount'] && ($i-$start)<31;$i++)
 {
  if($i==$_REQUEST['p']) { print <<<EOF
- <b class="red">$i</b>
EOF;
 } else { print <<<EOF
- <a class="t" href="ALIASES.php?pid={$_REQUEST['pid']}&amp;count={$_REQUEST['count']}&amp;p={$i}&amp;pcount={$_REQUEST['pcount']}&amp;s={$_REQUEST['s']}{$filters}">$i</a>
EOF;
  }
 }
print <<<EOF
&#160;из <span class="red">({$_REQUEST['pcount']})</span><br />
EOF;
}

if(!empty($_REQUEST['pid']) and $_REQUEST['pid'] == 'all')
{
$sth=$cmf->execute('select A.ALIASES_ID,A.TYPENAME,B.NAME,A.NAME from ALIASES A left join BRAND B on (A.BRAND_ID=B.BRAND_ID)
where A.ITEM_ID > 0 '.($cmf->Param('FILTER_brandID')?' and (A.BRAND_ID='.mysql_escape_string($cmf->Param('FILTER_brandID')).' or A.BRAND_ID=0)':'').'  '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$pagesize*($_REQUEST['p']-1),$pagesize);
}
else
{
$sth=$cmf->execute('select A.ALIASES_ID,A.TYPENAME,B.NAME,A.NAME from ALIASES A left join BRAND B on (A.BRAND_ID=B.BRAND_ID)
where A.ITEM_ID=? '.($cmf->Param('FILTER_brandID')?' and (A.BRAND_ID='.mysql_escape_string($cmf->Param('FILTER_brandID')).' or A.BRAND_ID=0)':'').'  '.$SORTQUERY[$_REQUEST['s']].'limit ?,?',$_REQUEST['pid'],$pagesize*($_REQUEST['p']-1),$pagesize);

}





@print <<<EOF
<img src="img/hi.gif" width="1" height="3" /><table bgcolor="#C0C0C0" border="0" cellpadding="4" cellspacing="1" class="l">
<tr bgcolor="#F0F0F0"><td colspan="0">
EOF;

if ($cmf->W)
@print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" /><input type="image" name="move" src="i/mv.gif" border="0" alt="Прикрепить к товару" title="Прикрепить к товару" hspace="4" class="gbut" />
EOF;

if ($cmf->D)
  print '<input type="submit" name="e" onclick="return dl();" value="Удалить" class="gbt bdel" />';
  
@print <<<EOF
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />
<input type="hidden" name="s" value="{$_REQUEST['s']}" />
</td><td colspan="6">
EOF;
$BRANDS=$cmf->Spravotchnik($cmf->Param('FILTER_brandID'),'select BRAND_ID,NAME from BRAND GROUP by NAME order by NAME');
print <<<EOF
<table bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="1" width="100%">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}">
<input type="hidden" name="p" value="{$_REQUEST['p']}">
<input type="hidden" name="s" value="{$_REQUEST['s']}">
<tr bgcolor="#CCCCCC"><td><select name="FILTER_brandID" onchange="return chan(this.form,ITEM_ID,'select ITEM_ID,NAME from ITEM where BRAND_ID=? order by NAME',this.value);"><option value="0">--------</option>$BRANDS</select><input type="text" name="q" value="" size="25" onkeyup="return chan(this.form,ITEM_ID,'select ITEM_ID,NAME from ITEM where BRAND_ID=? and NAME like ? order by NAME',FILTER_brandID.value+'\|%25'+this.value+'%25');"/>
EOF;
if($_REQUEST['pid'] != 'all') { ?>&#160;&#160;&#160;<input type="submit" name="e" value="Прикрепить"/>&#160;&#160;&#160;<input type="submit" name="e" value="filt"/>&#160;&#160;&#160;<input type="submit" name="e" value="Прикрепить похожие"/><?
} else { ?>&#160;&#160;&#160;<input type="submit" name="e" value="filt"/><?
}

if($_REQUEST['pid'] != 'all') { ?>&#160;&#160;&#160;<input type="button" value="Показать прикрепленные" onClick="location.href='ALIASES.php?pid=all'"/> <?
} else { ?>&#160;&#160;&#160;<input type="submit" name="e" value="Открепить выбранные"/><?
}
print <<<EOF
<br><select name="ITEM_ID" style="width:100%" size="7"></select>

EOF;
if($cmf->Param('FILTER_brandID'))
{
   ?>
   <script language="javascript">
   chan(document.forms[0],document.forms[0].ITEM_ID,'select ITEM_ID,NAME from ITEM where BRAND_ID=? order by NAME',<? echo $cmf->Param('FILTER_brandID'); ?>);
   </script>
   <?
}
print <<<EOF

</td></tr>
<tr><td bgcolor="#CCCCCC"></td></tr>
</table></td> </tr>
EOF;

print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'id[]');" /></td>$HEADER<td></td></tr>
EOF;


if($sth)
while(list($V_ALIASES_ID,$V_TYPENAME,$V_BRAND_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{



$parts= preg_split('/[\s,\+\|\-\(\)&@]/i', $V_NAME);

$i=0; $k=0;
$hhh =array();

 foreach ($parts as $value){
    if (!empty($value))
     {

       if (preg_match('/^([^0-9]*)(\d+)([^0-9]*)$/i', $value, $match))
            {
               for ($j=1; $j<count($match); $j++)
                 if (!empty($match[$j]))
                 {
                   $hhh[$k] = $match[$j];
                   $k++;
                 }
            } else
            {
                $part[$i]=trim($value);
            $i++;
        }
     }
 }


$parts="";
if (!empty($part) && count($part)>0)
 {
    $parts = implode('|',$part);
    //$parts .="*";

 } elseif (!empty($part) && count($part)==0) $parts = $part[0]."*";

if (!empty($parts)){
        $parts = "($parts)?([[:blank:]]|\\\\+|\\\\-|\\\\|)*";
}

$i=1;$add="";
foreach ($hhh as $value){
        if ($i==1) $add.="($value)([[:blank:]]|\\\\+|\\\\-|\\\\|)*";
        elseif ($i==2) $add.="($value)+";
        else $add.="($value)*";
    $i++;
}

unset($part, $hhh);

$parts .= $add;

$query="select I.ITEM_ID,I.NAME from ITEM as I INNER JOIN BRAND as B ON B.BRAND_ID = I.BRAND_ID where I.NAME
REGEXP '".$parts."' and B.NAME='".$V_BRAND_ID."' order by I.NAME";

$st = mysql_query($query);

$COIN = '';
if(mysql_num_rows($st))
{
   $number = mysql_num_rows($st);
   if($number>1)
   {
      $COIN .= '<select name="ids['.$V_ALIASES_ID.']" style="width:100px;color:red;">';
      while($row = mysql_fetch_array($st))
      {
         $COIN .='<option value="'.$row['ITEM_ID'].'">'.$row['NAME'].'</option>';
      }
      $COIN .= '</select>';
   }
   else
   {
      $row = mysql_fetch_array($st);
      $COIN .='<input type="hidden" name="ids['.$V_ALIASES_ID.']" value="'.$row['ITEM_ID'].'"> <b><font color="red">'.$row['NAME'].'</font></b>';
   }
}
if($_REQUEST['pid']!='all') $V_NAME .="<br/>".$COIN;
else
{
   $ITEM_NAME = $cmf->selectrow_array("select I.NAME from ITEM I inner join ALIASES A on A.ITEM_ID=I.ITEM_ID where A.ALIASES_ID=?",$V_ALIASES_ID);
   $BRAND_NAME = $cmf->selectrow_array("select B.NAME from BRAND B inner join ALIASES A on A.BRAND_ID=B.BRAND_ID where A.ALIASES_ID=?",$V_ALIASES_ID);
   $V_NAME .='��<font color="blue"><b>'.$BRAND_NAME.' '.$ITEM_NAME.'</b></font>';
}






@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="id[]" value="$V_ALIASES_ID" /></td>
<td>$V_ALIASES_ID</td><td>$V_TYPENAME</td><td>$V_BRAND_ID</td><td>$V_NAME</td><td nowrap="">
EOF;

if ($cmf->W)
@print <<<EOF
<a href="ALIASES.php?e=ED&amp;id=$V_ALIASES_ID&amp;pid={$_REQUEST['pid']}&amp;p={$_REQUEST['p']}&amp;s={$_REQUEST['s']}{$filters}"><img src="i/ed.gif" border="0" title="Изменить" /></a>

</td></tr>
EOF;
}
@print <<<EOF
        </table>
EOF;
}
print '</form>';
$cmf->MakeCommonFooter();
$cmf->Close();

function GetTree($cmf,$id)
{
$sth=$cmf->execute('select CATALOGUE_ID,NAME from CATALOGUE where PARENT_ID=? order by ORDERING',$id);
$ret='<ul>';
while(list ($V_CATALOGUE_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.=<<<EOF
<dl><input type="radio" name="cid" value="$V_CATALOGUE_ID">&#160;$V_NAME</dl>
EOF
.GetTree($cmf,$V_CATALOGUE_ID);
}
$ret.='</ul>';
return $ret;
}


function ___GetList($cmf,$id)
{
$ret='';
$sth=$cmf->execute('select ITEM_ID,NAME from ITEM  order by NAME');
while(list($V_ITEM_ID,$V_NAME)=mysql_fetch_array($sth, MYSQL_NUM))
{
$ret.='<li>'.($id==$V_ITEM_ID?'<input type="radio" name="cid" value="'.$V_ITEM_ID.'" disabled="yes" />':'<input type="radio" name="cid" value="'.$V_ITEM_ID.'" />')."&#160;$V_NAME</li>";
}
if ($ret) {$ret="<ul>${ret}</ul>";}
return $ret;
}

?>
