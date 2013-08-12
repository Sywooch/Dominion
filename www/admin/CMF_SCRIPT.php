<? require ('core.php');
if(file_exists('resize_img.php')){
include('resize_img.php');
$obj_img_resize = new Resize();
}
$cmf= new SCMF('CMF_SCRIPT');
session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}



$cmf->HeaderNoCache();
$cmf->makeCookieActions();



$cmf->MakeCommonHeader();

$visible=1;
if(!isset($_REQUEST['r']))$_REQUEST['r']=0;
if(!isset($_REQUEST['e']))$_REQUEST['e']='';
if(!isset($_REQUEST['event']))$_REQUEST['event']='';
if(!isset($_REQUEST['id']))$_REQUEST['id']='';
if(!isset($_REQUEST['pid']))$_REQUEST['pid']=0;
if(!isset($_REQUEST['f']))$_REQUEST['f']='';
$VIRTUAL_IMAGE_PATH="/adm/scr/";


$cmf->ENUM_TYPE=array(' в главное меню ',' в верхнее меню',' еще куда нибудь в меню');








if(!isset($_REQUEST['e1']))$_REQUEST['e1']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e1') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from CMF_SCRIPT_USER where CMF_SCRIPT_ID=? and CMF_USER_ID=?',$_REQUEST['id'],$id);

               if(is_array($_REQUEST['iid']))
               {
                  foreach ($_REQUEST['iid'] as $id)
                  {
                     $subtree = getSubTree($cmf,$_REQUEST['id']);
                     if($subtree)
                     {
                        for($i=0;$i<sizeof($subtree);$i++)
                        {
                            $cmf->execute('delete from CMF_SCRIPT_USER where CMF_SCRIPT_ID=? and CMF_USER_ID=?',$subtree[$i],$id);
                        }
                     }
                  }
               }
               
 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e1') == 'Изменить')
{


$_REQUEST['R']=isset($_REQUEST['R']) && $_REQUEST['R']?1:0;
$_REQUEST['W']=isset($_REQUEST['W']) && $_REQUEST['W']?1:0;
$_REQUEST['D']=isset($_REQUEST['D']) && $_REQUEST['D']?1:0;

$cmf->execute('update CMF_SCRIPT_USER set CMF_USER_ID=?,R=?,W=?,D=? where CMF_SCRIPT_ID=? and CMF_USER_ID=?',stripslashes($_REQUEST['CMF_USER_ID'])+0,stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']),$_REQUEST['id'],$_REQUEST['iid']);

               $subtree = getSubTree($cmf,$_REQUEST['id']);
               if($subtree)
               {
                  for($i=0;$i<sizeof($subtree);$i++)
                  {
                      $count = $cmf->selectrow_array("select COUNT(*) from CMF_SCRIPT_USER  where CMF_SCRIPT_ID=? and CMF_USER_ID=?",$subtree[$i],$_REQUEST['CMF_USER_ID']);
                      if($count == 0)
                      {
                         $cmf->execute('insert into CMF_SCRIPT_USER (CMF_SCRIPT_ID,CMF_USER_ID,R,W,D) values (?,?,?,?,?)',$subtree[$i],$_REQUEST['CMF_USER_ID'],stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']));
                      }
                      else
                      {
                         $cmf->execute('update CMF_SCRIPT_USER set R=?,W=?,D=? where CMF_SCRIPT_ID=? and CMF_USER_ID=?',stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']),$subtree[$i],$_REQUEST['CMF_USER_ID']);
                      }
                   }
               }
               
$_REQUEST['e']='ED';
};


if($cmf->Param('e1') == 'Добавить')
{


$_REQUEST['iid']=$_REQUEST['CMF_USER_ID'];



$_REQUEST['R']=isset($_REQUEST['R']) && $_REQUEST['R']?1:0;
$_REQUEST['W']=isset($_REQUEST['W']) && $_REQUEST['W']?1:0;
$_REQUEST['D']=isset($_REQUEST['D']) && $_REQUEST['D']?1:0;


$cmf->execute('insert into CMF_SCRIPT_USER (CMF_SCRIPT_ID,CMF_USER_ID,R,W,D) values (?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']));
$_REQUEST['e']='ED';

                if(!empty($_REQUEST['id']) && !empty($_REQUEST['CMF_USER_ID']))
                {
                 $subtree = getSubTree($cmf,$_REQUEST['id']);
                 if($subtree)
                 {
                    for($i=0;$i<sizeof($subtree);$i++)
                    {
                        $cmf->execute('insert into CMF_SCRIPT_USER (CMF_SCRIPT_ID,CMF_USER_ID,R,W,D) values (?,?,?,?,?)',$subtree[$i],$_REQUEST['CMF_USER_ID'],stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']));
                    }
                 }
               }
               
$visible=0;
}

if($cmf->Param('e1') == 'ED')
{
list ($V_CMF_USER_ID,$V_R,$V_W,$V_D)=$cmf->selectrow_arrayQ('select CMF_USER_ID,R,W,D from CMF_SCRIPT_USER where CMF_SCRIPT_ID=? and CMF_USER_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_CMF_USER_ID=$cmf->Spravotchnik($V_CMF_USER_ID,'select CMF_USER_ID,NAME from CMF_USER  order by NAME');        
					
$V_R=$V_R?'checked':'';
$V_W=$V_W?'checked':'';
$V_D=$V_D?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Права пользователей</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CMF_SCRIPT.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'" />';

@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Имя пользователя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CMF_USER_ID">
				
				
				
				
				
				
				$V_STR_CMF_USER_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>чтение?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='R' value='1' $V_R/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>запись?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='W' value='1' $V_W/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>удаление?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='D' value='1' $V_D/><br /></td></tr>
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
list($V_CMF_USER_ID,$V_R,$V_W,$V_D)=array('','','','');


$V_STR_CMF_USER_ID=$cmf->Spravotchnik($V_CMF_USER_ID,'select CMF_USER_ID,NAME from CMF_USER  order by NAME');
					
$V_R='';
$V_W='';
$V_D='';
@print <<<EOF
<h2 class="h2">Добавление - Права пользователей</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CMF_SCRIPT.php#f1" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Имя пользователя:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CMF_USER_ID">
				
				
				
				
				
				
				$V_STR_CMF_USER_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>чтение?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='R' value='1' $V_R/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>запись?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='W' value='1' $V_W/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>удаление?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='D' value='1' $V_D/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e1" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e1" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}

if(!isset($_REQUEST['e2']))$_REQUEST['e2']='';
if(!isset($_REQUEST['p']))$_REQUEST['p']='';

if(($cmf->Param('e2') == 'Удалить') and is_array($_REQUEST['iid']))
{
foreach ($_REQUEST['iid'] as $id)
 {

$cmf->execute('delete from CMF_SCRIPT_GROUP where CMF_SCRIPT_ID=? and CMF_GROUP_ID=?',$_REQUEST['id'],$id);

               if(is_array($_REQUEST['iid']))
               {
                  foreach ($_REQUEST['iid'] as $id)
                  {
                     $subtree = getSubTree($cmf,$_REQUEST['id']);
                     if($subtree)
                     {
                        for($i=0;$i<sizeof($subtree);$i++)
                        {
                            $cmf->execute('delete from CMF_SCRIPT_GROUP where CMF_SCRIPT_ID=? and CMF_GROUP_ID=?',$subtree[$i],$id);
                        }
                     }
                  }
               }
               
 }
$_REQUEST['e']='ED';
$visible=0;
}




if($cmf->Param('e2') == 'Изменить')
{


$_REQUEST['R']=isset($_REQUEST['R']) && $_REQUEST['R']?1:0;
$_REQUEST['W']=isset($_REQUEST['W']) && $_REQUEST['W']?1:0;
$_REQUEST['D']=isset($_REQUEST['D']) && $_REQUEST['D']?1:0;

$cmf->execute('update CMF_SCRIPT_GROUP set CMF_GROUP_ID=?,R=?,W=?,D=? where CMF_SCRIPT_ID=? and CMF_GROUP_ID=?',stripslashes($_REQUEST['CMF_GROUP_ID'])+0,stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']),$_REQUEST['id'],$_REQUEST['iid']);

               $subtree = getSubTree($cmf,$_REQUEST['id']);
               if($subtree)
               {
                  for($i=0;$i<sizeof($subtree);$i++)
                  {
                      $count = $cmf->selectrow_array("select COUNT(*) from CMF_SCRIPT_GROUP where CMF_SCRIPT_ID=? and CMF_GROUP_ID=?",$subtree[$i],$_REQUEST['CMF_GROUP_ID']);
                      if($count == 0)
                      {
                         $cmf->execute('insert into CMF_SCRIPT_GROUP (CMF_SCRIPT_ID,CMF_GROUP_ID,R,W,D) values (?,?,?,?,?)',$subtree[$i],$_REQUEST['CMF_GROUP_ID'],stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']));
                      }
                      else
                      {
                         $cmf->execute('update CMF_SCRIPT_GROUP set CMF_GROUP_ID=?,R=?,W=?,D=? where CMF_SCRIPT_ID=? and CMF_GROUP_ID=?',stripslashes($_REQUEST['CMF_GROUP_ID'])+0,stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']),$subtree[$i],$_REQUEST['CMF_GROUP_ID']);
                      }
                   }
               }
               
$_REQUEST['e']='ED';
};


if($cmf->Param('e2') == 'Добавить')
{


$_REQUEST['iid']=$_REQUEST['CMF_GROUP_ID'];



$_REQUEST['R']=isset($_REQUEST['R']) && $_REQUEST['R']?1:0;
$_REQUEST['W']=isset($_REQUEST['W']) && $_REQUEST['W']?1:0;
$_REQUEST['D']=isset($_REQUEST['D']) && $_REQUEST['D']?1:0;


$cmf->execute('insert into CMF_SCRIPT_GROUP (CMF_SCRIPT_ID,CMF_GROUP_ID,R,W,D) values (?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['iid'],stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']));
$_REQUEST['e']='ED';

                if(!empty($_REQUEST['id']) && !empty($_REQUEST['CMF_USER_ID']))
                {
                 $subtree = getSubTree($cmf,$_REQUEST['id']);
                 if($subtree)
                 {
                    for($i=0;$i<sizeof($subtree);$i++)
                    {
                        $cmf->execute('insert into CMF_SCRIPT_GROUP (CMF_SCRIPT_ID,CMF_GROUP_ID,R,W,D) values (?,?,?,?,?)',$subtree[$i],$_REQUEST['iid'],stripslashes($_REQUEST['R']),stripslashes($_REQUEST['W']),stripslashes($_REQUEST['D']));
                    }
                 }
               }
               
$visible=0;
}

if($cmf->Param('e2') == 'ED')
{
list ($V_CMF_GROUP_ID,$V_R,$V_W,$V_D)=$cmf->selectrow_arrayQ('select CMF_GROUP_ID,R,W,D from CMF_SCRIPT_GROUP where CMF_SCRIPT_ID=? and CMF_GROUP_ID=?',$_REQUEST['id'],$_REQUEST['iid']);


$V_STR_CMF_GROUP_ID=$cmf->Spravotchnik($V_CMF_GROUP_ID,'select CMF_GROUP_ID,NAME from CMF_GROUP  order by NAME');        
					
$V_R=$V_R?'checked':'';
$V_W=$V_W?'checked':'';
$V_D=$V_D?'checked':'';
@print <<<EOF
<h2 class="h2">Редактирование - Права групп</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CMF_SCRIPT.php#f2" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="iid" value="{$_REQUEST['iid']}" />


<input type="hidden" name="p" value="{$_REQUEST['p']}" />

EOF;
if(!empty($V_CMF_LANG_ID)) print '<input type="hidden" name="CMF_LANG_ID" value="'.$V_CMF_LANG_ID.'" />';

@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Группа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CMF_GROUP_ID">
				
				
				
				
				
				
				$V_STR_CMF_GROUP_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>чтение?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='R' value='1' $V_R/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>запись?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='W' value='1' $V_W/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>удаление?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='D' value='1' $V_D/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;





$visible=0;
}

if($cmf->Param('e2') == 'Новый')
{
list($V_CMF_GROUP_ID,$V_R,$V_W,$V_D)=array('','','','');


$V_STR_CMF_GROUP_ID=$cmf->Spravotchnik($V_CMF_GROUP_ID,'select CMF_GROUP_ID,NAME from CMF_GROUP  order by NAME');
					
$V_R='';
$V_W='';
$V_D='';
@print <<<EOF
<h2 class="h2">Добавление - Права групп</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form name="frm" method="POST" action="CMF_SCRIPT.php#f2" ENCTYPE="multipart/form-data" onsubmit="return true ;">
<input type="hidden" name="e" value="ED" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
<tr bgcolor="#FFFFFF"><th width="1%"><b>Группа:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

	
			<select name="CMF_GROUP_ID">
				
				
				
				
				
				
				$V_STR_CMF_GROUP_ID
			</select><br />
		
	

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>чтение?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='R' value='1' $V_R/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>запись?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='W' value='1' $V_W/><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>удаление?:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='D' value='1' $V_D/><br /></td></tr>
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="e2" value="Добавить" class="gbt badd" /> 
<input type="submit" name="e2" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}




if($_REQUEST['e'] == 'DL')
{
DelTree($cmf,$_REQUEST['id']);
$cmf->execute('delete from CMF_SCRIPT where CMF_SCRIPT_ID=?',$_REQUEST['id']);
}

if($_REQUEST['e'] == 'VS')
{
$STATUS=$cmf->selectrow_array('select STATUS from CMF_SCRIPT where CMF_SCRIPT_ID=?',$_REQUEST['id']);
$STATUS=1-$STATUS;
$cmf->execute('update CMF_SCRIPT set STATUS=? where CMF_SCRIPT_ID=?',$STATUS,$_REQUEST['id']);
if($STATUS)
{
$cmf->execute('update CMF_SCRIPT set REALSTATUS=1 where CMF_SCRIPT_ID=?',$_REQUEST['id']);
SetTreeRealStatus($cmf,$_REQUEST['id'],1);
}
else
{
$REALSTATUS=GetMyRealStatus($cmf,$_REQUEST['id']);
$cmf->execute('update CMF_SCRIPT set REALSTATUS=? where CMF_SCRIPT_ID=?',$REALSTATUS,$_REQUEST['id']);
SetTreeRealStatus($cmf,$_REQUEST['id'],$REALSTATUS);
}
}

if($_REQUEST['e'] == 'UP')
{
list($V_PARENT_ID,$V_ORDERING) =$cmf->selectrow_array('select PARENT_ID,ORDERING from CMF_SCRIPT where CMF_SCRIPT_ID=?',$_REQUEST['id']);
if($V_ORDERING > 1)
{

$sql="select CMF_SCRIPT_ID
           , ORDERING
      from CMF_SCRIPT
      where ORDERING < {$V_ORDERING}
            and PARENT_ID = {$V_PARENT_ID}
      order by ORDERING DESC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf->selectrow_array($sql);


$cmf->execute('update CMF_SCRIPT set ORDERING=? where CMF_SCRIPT_ID=?',$V_ORDERING,$V_OTHER_ID);
$cmf->execute('update CMF_SCRIPT set ORDERING=? where CMF_SCRIPT_ID=?',$V_OTHER_ORDERING, $_REQUEST['id']);

}
}

if($_REQUEST['e'] == 'DN')
{
list($V_PARENT_ID,$V_ORDERING) =$cmf->selectrow_array('select PARENT_ID,ORDERING from CMF_SCRIPT where CMF_SCRIPT_ID=?',$_REQUEST['id']);
$V_MAXORDERING=$cmf->selectrow_array('select max(ORDERING) from CMF_SCRIPT where PARENT_ID=?',$V_PARENT_ID);
if($V_ORDERING < $V_MAXORDERING)
{

$sql="select CMF_SCRIPT_ID
           , ORDERING
      from CMF_SCRIPT
      where ORDERING > {$V_ORDERING}
            and PARENT_ID = {$V_PARENT_ID}
      order by ORDERING ASC
      limit 1";
      
list($V_OTHER_ID,$V_OTHER_ORDERING)=$cmf->selectrow_array($sql);


$cmf->execute('update CMF_SCRIPT set ORDERING=? where CMF_SCRIPT_ID=?',$V_ORDERING,$V_OTHER_ID);
$cmf->execute('update CMF_SCRIPT set ORDERING=? where CMF_SCRIPT_ID=?',$V_OTHER_ORDERING, $_REQUEST['id']);
}
}

if($_REQUEST['event'] == 'Добавить')
{

if(!empty($_REQUEST['pid']))
{
  $_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from CMF_SCRIPT where PARENT_ID=?',$_REQUEST['pid']);
  $_REQUEST['ORDERING']++;
  $_REQUEST['id']=$cmf->GetSequence('CMF_SCRIPT');
  






		
				
    if(isset($_FILES['NOT_IMAGE']['tmp_name']) && $_FILES['NOT_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE']) && $_REQUEST['CLR_IMAGE']){$_REQUEST['IMAGE']=$cmf->UnlinkFile($_REQUEST['IMAGE'],$VIRTUAL_IMAGE_PATH);}
	


$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;
$_REQUEST['REALSTATUS']=isset($_REQUEST['REALSTATUS']) && $_REQUEST['REALSTATUS']?1:0;


  $cmf->execute('insert into CMF_SCRIPT (CMF_SCRIPT_ID,PARENT_ID,ARTICLE,NAME,URL,DESCRIPTION,IMAGE,BACKGROUND,TYPE,STATUS,REALSTATUS,ORDERING) values (?,?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['pid']+0,stripslashes($_REQUEST['ARTICLE']),stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IMAGE']),stripslashes($_REQUEST['BACKGROUND']),stripslashes($_REQUEST['TYPE']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['REALSTATUS']),stripslashes($_REQUEST['ORDERING']));
  
  
        $cmf->execute('update CMF_SCRIPT set REALSTATUS=? where CMF_SCRIPT_ID=?',GetMyRealStatus($cmf,$cmf->Param('id')),$cmf->Param('id'));
        $cmf->execute('insert into CMF_SCRIPT_GROUP (CMF_SCRIPT_ID,CMF_GROUP_ID,R,W,D) VALUES (?,1,1,1,1)',$cmf->Param('id'));
}
else
{
  $_REQUEST['ORDERING']=$cmf->selectrow_array('select max(ORDERING) from CMF_SCRIPT where PARENT_ID=?',0);
  $_REQUEST['ORDERING']++;
  $_REQUEST['id']=$cmf->GetSequence('CMF_SCRIPT');
  






		
				
    if(isset($_FILES['NOT_IMAGE']['tmp_name']) && $_FILES['NOT_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE']) && $_REQUEST['CLR_IMAGE']){$_REQUEST['IMAGE']=$cmf->UnlinkFile($_REQUEST['IMAGE'],$VIRTUAL_IMAGE_PATH);}
	


$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;
$_REQUEST['REALSTATUS']=isset($_REQUEST['REALSTATUS']) && $_REQUEST['REALSTATUS']?1:0;


  $_REQUEST['pid'] = (!empty($_REQUEST['PARENT_ID'])) ? $_REQUEST['PARENT_ID'] : 0;
  $cmf->execute('insert into CMF_SCRIPT (CMF_SCRIPT_ID,PARENT_ID,ARTICLE,NAME,URL,DESCRIPTION,IMAGE,BACKGROUND,TYPE,STATUS,REALSTATUS,ORDERING) values (?,?,?,?,?,?,?,?,?,?,?,?)',$_REQUEST['id'],$_REQUEST['pid']+0,stripslashes($_REQUEST['ARTICLE']),stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IMAGE']),stripslashes($_REQUEST['BACKGROUND']),stripslashes($_REQUEST['TYPE']),stripslashes($_REQUEST['STATUS']),stripslashes($_REQUEST['REALSTATUS']),stripslashes($_REQUEST['ORDERING']));
  
  
        $cmf->execute('update CMF_SCRIPT set REALSTATUS=? where CMF_SCRIPT_ID=?',GetMyRealStatus($cmf,$cmf->Param('id')),$cmf->Param('id'));
        $cmf->execute('insert into CMF_SCRIPT_GROUP (CMF_SCRIPT_ID,CMF_GROUP_ID,R,W,D) VALUES (?,1,1,1,1)',$cmf->Param('id'));

}
$_REQUEST['e']='ED';
}

if($_REQUEST['event'] == 'Изменить')
{







		
				
    if(isset($_FILES['NOT_IMAGE']['tmp_name']) && $_FILES['NOT_IMAGE']['tmp_name']){
		if(!empty($_REQUEST['iid'])){
		   if(!empty($_REQUEST['bid'])){ 
     		  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'_'.$_REQUEST['iid'].'_'.$_REQUEST['bid'].'',$VIRTUAL_IMAGE_PATH);
		   }
		   else{
			  $_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		   }
		}
		else{ 
			$_REQUEST['IMAGE']=$cmf->PicturePost('NOT_IMAGE',$_REQUEST['IMAGE'],''.$_REQUEST['id'].'',$VIRTUAL_IMAGE_PATH);		   
		}
	}

			
		if(isset($_REQUEST['CLR_IMAGE']) && $_REQUEST['CLR_IMAGE']){$_REQUEST['IMAGE']=$cmf->UnlinkFile($_REQUEST['IMAGE'],$VIRTUAL_IMAGE_PATH);}
	


$_REQUEST['STATUS']=isset($_REQUEST['STATUS']) && $_REQUEST['STATUS']?1:0;
$_REQUEST['REALSTATUS']=isset($_REQUEST['REALSTATUS']) && $_REQUEST['REALSTATUS']?1:0;


@$cmf->execute('update CMF_SCRIPT set ARTICLE=?,NAME=?,URL=?,DESCRIPTION=?,IMAGE=?,BACKGROUND=?,TYPE=? where CMF_SCRIPT_ID=?',stripslashes($_REQUEST['ARTICLE']),stripslashes($_REQUEST['NAME']),stripslashes($_REQUEST['URL']),stripslashes($_REQUEST['DESCRIPTION']),stripslashes($_REQUEST['IMAGE']),stripslashes($_REQUEST['BACKGROUND']),stripslashes($_REQUEST['TYPE']),$_REQUEST['id']);
$_REQUEST['e']='ED';

};

if($_REQUEST['e'] == 'ED')
{
list($V_CMF_SCRIPT_ID,$V_PARENT_ID,$V_ARTICLE,$V_NAME,$V_URL,$V_DESCRIPTION,$V_IMAGE,$V_BACKGROUND,$V_TYPE,$V_STATUS,$V_REALSTATUS)=$cmf->selectrow_arrayQ('select CMF_SCRIPT_ID,PARENT_ID,ARTICLE,NAME,URL,DESCRIPTION,IMAGE,BACKGROUND,TYPE,STATUS,REALSTATUS from CMF_SCRIPT where CMF_SCRIPT_ID=?',$_REQUEST['id']);




if(isset($V_IMAGE))
{
   $IM_IMAGE=split('#',$V_IMAGE);
   if(isset($IM_6[1]) && $IM_IMAGE[1] > 150){$IM_IMAGE[2]=$IM_IMAGE[2]*150/$IM_IMAGE[1]; $IM_IMAGE[1]=150;}
}

$V_STR_TYPE=$cmf->Enumerator($cmf->ENUM_TYPE,$V_TYPE);
$V_STATUS=$V_STATUS?'checked':'';
$V_REALSTATUS=$V_REALSTATUS?'checked':'';

@print <<<EOF
<h2 class="h2">Редактирование - Скрипты</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CMF_SCRIPT.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(ARTICLE) &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(URL) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(BACKGROUND);">
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="event" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Псевдоним скрипта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ARTICLE" value="$V_ARTICLE" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Название cкрипта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Путь к скрипту:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="7" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE" value="$V_IMAGE" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE[0]))
{
if(strchr($IM_IMAGE[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE[0] = isset($IM_IMAGE[0]) ? $IM_IMAGE[0]:0;
$IM_IMAGE[1] = isset($IM_IMAGE[1]) ? $IM_IMAGE[1]:0;
$IM_IMAGE[2] = isset($IM_IMAGE[2]) ? $IM_IMAGE[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]" width="$IM_IMAGE[1]" height="$IM_IMAGE[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цвет фона:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="BACKGROUND" value="$V_BACKGROUND" size="7" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тип:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="TYPE">$V_STR_TYPE</select><br /></td></tr>


<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Изменить" class="gbt bsave" />&#160;&#160;
<input type="submit" name="event" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table><br />
EOF;



print <<<EOF
<a name="f1"></a><h3 class="h3">Права пользователей</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="CMF_SCRIPT.php#f1" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="6">
<input type="submit" name="e1" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e1" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select CMF_USER_ID,R,W,D from CMF_SCRIPT_USER where CMF_SCRIPT_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>Имя пользователя</th><th>чтение?</th><th>запись?</th><th>удаление?</th><td></td></tr>
EOF;
while(list($V_CMF_USER_ID,$V_R,$V_W,$V_D)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_CMF_USER_ID_STR=$cmf->selectrow_arrayQ('select NAME from CMF_USER where CMF_USER_ID=?',$V_CMF_USER_ID);
                                        
if(!$V_R) {$V_R='Нет';} else {$V_R='Да';}
                        
if(!$V_W) {$V_W='Нет';} else {$V_W='Да';}
                        
if(!$V_D) {$V_D='Нет';} else {$V_D='Да';}
                        


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_CMF_USER_ID" /></td>
<td>$V_CMF_USER_ID_STR</td><td>$V_R</td><td>$V_W</td><td>$V_D</td><td nowrap="">

<a href="CMF_SCRIPT.php?e1=ED&amp;iid=$V_CMF_USER_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';



print <<<EOF
<a name="f2"></a><h3 class="h3">Права групп</h3>
EOF;

@print <<<EOF
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="CMF_SCRIPT.php#f2" method="POST">
<tr bgcolor="#F0F0F0"><td colspan="6">
<input type="submit" name="e2" value="Новый" class="gbt badd" /><img src="img/hi.gif" width="4" height="1" />
<input type="submit" name="e2" onclick="return dl();" value="Удалить" class="gbt bdel" />
<input type="hidden" name="id" value="{$_REQUEST['id']}" />
<input type="hidden" name="pid" value="{$_REQUEST['pid']}" />
<input type="hidden" name="p" value="{$_REQUEST['p']}" />

</td></tr>
EOF;
$sth=$cmf->execute('select CMF_GROUP_ID,R,W,D from CMF_SCRIPT_GROUP where CMF_SCRIPT_ID=? ',$_REQUEST['id']);
print <<<EOF
<tr bgcolor="#FFFFFF"><td><input type="checkbox" onclick="return SelectAll(this.form,checked,'[iid]');" /></td><th>Группа</th><th>чтение?</th><th>запись?</th><th>удаление?</th><td></td></tr>
EOF;
while(list($V_CMF_GROUP_ID,$V_R,$V_W,$V_D)=mysql_fetch_array($sth, MYSQL_NUM))
{
$V_CMF_GROUP_ID_STR=$cmf->selectrow_arrayQ('select NAME from CMF_GROUP where CMF_GROUP_ID=?',$V_CMF_GROUP_ID);
                                        
if(!$V_R) {$V_R='Нет';} else {$V_R='Да';}
                        
if(!$V_W) {$V_W='Нет';} else {$V_W='Да';}
                        
if(!$V_D) {$V_D='Нет';} else {$V_D='Да';}
                        


@print <<<EOF
<tr bgcolor="#FFFFFF">
<td><input type="checkbox" name="iid[]" value="$V_CMF_GROUP_ID" /></td>
<td>$V_CMF_GROUP_ID_STR</td><td>$V_R</td><td>$V_W</td><td>$V_D</td><td nowrap="">

<a href="CMF_SCRIPT.php?e2=ED&amp;iid=$V_CMF_GROUP_ID&amp;id={$_REQUEST['id']}&amp;pid={$_REQUEST['pid']}"><img src="i/ed.gif" border="0" title="Изменить" /></a>
</td></tr>
EOF;
$visible=0;
}
print '</form></table>';


$visible=0;
}

if($_REQUEST['e'] == 'AD' ||  $_REQUEST['e'] =='Новый')
{
list($V_CMF_SCRIPT_ID,$V_PARENT_ID,$V_ARTICLE,$V_NAME,$V_URL,$V_DESCRIPTION,$V_IMAGE,$V_BACKGROUND,$V_TYPE,$V_STATUS,$V_REALSTATUS,$V_ORDERING)=array('','','','','','','','','','','','');
if(!empty($_REQUEST['pid'])) $V_ = $_REQUEST['pid'];
else $V_ = 0;



$IM_IMAGE=array('','','');
$V_STR_TYPE=$cmf->Enumerator($cmf->ENUM_TYPE,-1);
$V_STATUS='checked';
$V_REALSTATUS='';

@print <<<EOF
<h2 class="h2">Добавление - Скрипты</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" style="width: 100%" class="f">
<form method="POST" action="CMF_SCRIPT.php" ENCTYPE="multipart/form-data" onsubmit="return true  &amp;&amp; checkXML(ARTICLE) &amp;&amp; checkXML(NAME) &amp;&amp; checkXML(URL) &amp;&amp; checkXML(DESCRIPTION) &amp;&amp; checkXML(BACKGROUND);">
EOF;
print '<input type="hidden" name="pid" value="'.$_REQUEST['pid'].'" />';
@print <<<EOF
<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Добавить" class="gbt badd" /> 
<input type="submit" name="event" value="Отменить" class="gbt bcancel" />
</td></tr>


<tr bgcolor="#FFFFFF"><th width="1%"><b>Псевдоним скрипта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="ARTICLE" value="$V_ARTICLE" size="" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Название cкрипта:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="NAME" value="$V_NAME" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Путь к скрипту:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="URL" value="$V_URL" size="90" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Краткое описание:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">


<textarea name="DESCRIPTION" rows="7" cols="90">$V_DESCRIPTION</textarea><br />


</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Картинка:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type="hidden" name="IMAGE" value="$V_IMAGE" />
<table><tr><td>
EOF;
if(!empty($IM_IMAGE[0]))
{
if(strchr($IM_IMAGE[0],".swf"))
{
   print "<div style=\"width:600px\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"100%\" align=\"middle\">
                                                 <param name=\"allowScriptAccess\" value=\"sameDomain\" />
                                                 <param name=\"movie\" value=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]\" />
                                                 <param name=\"quality\" value=\"high\" />
                                                 <embed src=\"/images$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]\" quality=\"high\" width=\"100%\"  align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
                                                 </object></div>";
}
else
{
$IM_IMAGE[0] = isset($IM_IMAGE[0]) ? $IM_IMAGE[0]:0;
$IM_IMAGE[1] = isset($IM_IMAGE[1]) ? $IM_IMAGE[1]:0;
$IM_IMAGE[2] = isset($IM_IMAGE[2]) ? $IM_IMAGE[2]:0;
print <<<EOF
<img src="/images/$VIRTUAL_IMAGE_PATH$IM_IMAGE[0]" width="$IM_IMAGE[1]" height="$IM_IMAGE[2]" />
EOF;
}
}

print <<<EOF
</td>
<td><input type="file" name="NOT_IMAGE" size="1" /><br />
<input type="checkbox" name="CLR_IMAGE" value="1" />Сбросить карт.

</td></tr></table>
</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Цвет фона:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%">

<input type="text" name="BACKGROUND" value="$V_BACKGROUND" size="7" /><br />

</td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Тип:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><select name="TYPE">$V_STR_TYPE</select><br /></td></tr><tr bgcolor="#FFFFFF"><th width="1%"><b>Вкл:<br /><img src="img/hi.gif" width="125" height="1" /></b></th><td width="100%"><input type='checkbox' name='STATUS' value='1' $V_STATUS/><br /></td></tr>

<tr bgcolor="#F0F0F0" class="ftr"><td colspan="2">
<input type="submit" name="event" value="Добавить" class="gbt badd" /> 
<input type="submit" name="event" value="Отменить" class="gbt bcancel" />
</td></tr>
</form>
</table>
EOF;
$visible=0;
}

if($visible)
{
$parhash=array('0'=>'1');
$CMF_SCRIPT_ID=$_REQUEST['id'];
$O_CMF_SCRIPT_ID=$CMF_SCRIPT_ID;
do 
{
  $PARENTID=$cmf->selectrow_array('select PARENT_ID from CMF_SCRIPT where CMF_SCRIPT_ID=?',$CMF_SCRIPT_ID);
  $parhash[$CMF_SCRIPT_ID]=1;
  $CMF_SCRIPT_ID=$PARENTID;
}while(isset($PARENTID));
print <<<EOF
<h2 class="h2">Скрипты</h2>
<table bgcolor="#CCCCCC" border="0" cellpadding="5" cellspacing="1" class="l">
<form action="CMF_SCRIPT.php" method="POST">
<input type="hidden" name="r" value="{$_REQUEST['r']}" />
<tr bgcolor="#F0F0F0"><td colspan="6">
EOF;

if ($cmf->W)
print <<<EOF
<input type="submit" name="e" value="Новый" class="gbt badd" />
EOF;

print <<<EOF
</td></tr>
EOF;
print <<<EOF
<tr bgcolor="#FFFFFF"><th>N</th><th>Название cкрипта</th><th>Путь к скрипту</th><th>Тип</th><form action="CMF_SCRIPT.php" method="POST"><th>

</th></form></tr>
EOF;
print visibleTree($cmf,$_REQUEST['r'],0,$_REQUEST['r'],$parhash);
print '</form></table>';
}

function visibleTree($cmf,$parent,$level,$root,$parhash)
{
$width=$level*15+10;
$ret='';
$sth=$cmf->execute('select CMF_SCRIPT_ID,NAME,URL,TYPE,STATUS,REALSTATUS from CMF_SCRIPT where PARENT_ID=? order by ORDERING',$parent);
while ( list($V_CMF_SCRIPT_ID,$V_NAME,$V_URL,$V_TYPE,$V_STATUS,$V_REALSTATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{

$V_TYPE=$cmf->ENUM_TYPE[$V_TYPE];
                        



  $ICONS=<<<EOF
  
EOF;
  $V_REALSTATUS=$V_REALSTATUS?'b':'d';
  $V_STATUS=$V_STATUS?0:1;
  $CO_=$cmf->selectrow_array('select count(*) from CMF_SCRIPT where PARENT_ID=?',$V_CMF_SCRIPT_ID);
if(!$CO_)
 {

$folder=<<<EOF
<img src="i/f1.gif" class="fld" /><a href="CMF_SCRIPT.php?e=ED&amp;id=$V_CMF_SCRIPT_ID&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF;

 }
else
 {

$folder=isset($parhash[$V_CMF_SCRIPT_ID])?$folder=<<<EOF
<a href="CMF_SCRIPT.php?id=$V_CMF_SCRIPT_ID&amp;r=$root" class="$V_REALSTATUS"><img src="i/f1.gif" class="fld" /></a><a href="CMF_SCRIPT.php?id=$V_CMF_SCRIPT_ID&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF
:
$folder=<<<EOF
<a href="CMF_SCRIPT.php?id=$V_CMF_SCRIPT_ID&amp;r=$root" class="$V_REALSTATUS"><img src="i/f0.gif" class="fld" /></a><a href="CMF_SCRIPT.php?id=$V_CMF_SCRIPT_ID&amp;r=$root" class="$V_REALSTATUS">$V_NAME</a>
EOF;

 }

 $V_NAME=<<<EOF
$folder 
EOF;
 
  $ret.=<<<EOF
<tr bgcolor="#ffffff">
<td>$V_CMF_SCRIPT_ID</td><td style="padding-left:{$width}px">$V_NAME</td><td>$V_URL</td><td>$V_TYPE</td><td nowrap="">
EOF;

if ($cmf->W)
$ret.=<<<EOF
<a href="CMF_SCRIPT.php?e=AD&amp;pid=$V_CMF_SCRIPT_ID&amp;r=$root"><img src="i/add.gif" border="0" title="Добавить" hspace="5" /></a>
<a href="CMF_SCRIPT.php?e=UP&amp;id=$V_CMF_SCRIPT_ID&amp;r=$root"><img src="i/up.gif" border="0" title="Вверх" hspace="5" /></a>
<a href="CMF_SCRIPT.php?e=DN&amp;id=$V_CMF_SCRIPT_ID&amp;r=$root"><img src="i/dn.gif" border="0" title="Вниз" hspace="5" /></a>
<a href="CMF_SCRIPT.php?e=ED&amp;id=$V_CMF_SCRIPT_ID&amp;r=$root"><img src="i/ed.gif" border="0" title="Изменить" hspace="5" /></a>
<a href="CMF_SCRIPT.php?e=VS&amp;id=$V_CMF_SCRIPT_ID&amp;o=$V_CMF_SCRIPT_ID"><img src="i/v$V_STATUS.gif" border="0" /></a>&#160;
$ICONS
EOF;
if ($cmf->D)
{
$ret .=<<<EOF
<a href="CMF_SCRIPT.php?e=DL&amp;id=$V_CMF_SCRIPT_ID&amp;r=$root" onclick="return dl();"><img src="i/del.gif" border="0" title="Удалить" hspace="5" /></a>
EOF;
}

  $ret.= '</td></tr>';

  if(isset($parhash[$V_CMF_SCRIPT_ID])){$ret.=visibleTree($cmf,$V_CMF_SCRIPT_ID,$level+1,$root,$parhash);}
}
return $ret;
}

function DelTree($cmf,$id)
{
$sth=$cmf->execute('select CMF_SCRIPT_ID from CMF_SCRIPT where PARENT_ID=?',$id);
while(list($V_CMF_SCRIPT_ID)=mysql_fetch_array($sth, MYSQL_NUM))
{
DelTree($cmf,$V_CMF_SCRIPT_ID);
$cmf->execute('delete from CMF_SCRIPT where CMF_SCRIPT_ID=?',$V_CMF_SCRIPT_ID);
#### del items
}
}

function SetTreeRealStatus($cmf,$id,$state)
{
$sth=$cmf->execute('select CMF_SCRIPT_ID,STATUS from CMF_SCRIPT where PARENT_ID=?',$id);
while(list($V_CMF_SCRIPT_ID,$V_STATUS)=mysql_fetch_array($sth, MYSQL_NUM))
{
if($V_STATUS){SetTreeRealStatus($cmf,$V_CMF_SCRIPT_ID,$state);}
if($state) {$cmf->execute('update CMF_SCRIPT set REALSTATUS=STATUS where CMF_SCRIPT_ID=?',$V_CMF_SCRIPT_ID);}
else {$cmf->execute('update CMF_SCRIPT set REALSTATUS=0 where CMF_SCRIPT_ID=?',$V_CMF_SCRIPT_ID);}
}
}

function GetMyRealStatus($cmf,$id)
{
$V_PARENT_ID=$id;
$V_FULLSTATUS=0;
while ($V_PARENT_ID>0)
{
list ($V_PARENT_ID,$V_STATUS)=$cmf->selectrow_array('select PARENT_ID,STATUS from CMF_SCRIPT where CMF_SCRIPT_ID=?',$V_PARENT_ID);
$V_FULLSTATUS+=1-$V_STATUS;
}
if($V_FULLSTATUS){$V_FULLSTATUS=0;} else {$V_FULLSTATUS=1;}
return $V_FULLSTATUS;
}

$cmf->MakeCommonFooter();
$cmf->Close();

         function getSubTree($cmf,$parent)
         {
           $subtree = array();
           $sth = $cmf->execute($query = "select CMF_SCRIPT_ID from CMF_SCRIPT where PARENT_ID=?",$parent);

           while($row = mysql_fetch_array($sth))
           {
             if($row['CMF_SCRIPT_ID'])
             {
                $subtree[] = $row['CMF_SCRIPT_ID'];
                $subtree = array_merge($subtree,getSubTree($cmf,$row['CMF_SCRIPT_ID']));
             }
           }
           return $subtree;
         }
        
?>
