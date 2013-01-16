<?
require_once ('/home/kkb/public_html/admin/core.php');
//require_once ('../core.php');//for_site

//require_once ('E:/projects/tehnohata/www/admin/core.php');//for loc

class Parser
{
	private $db;
	const TYPE3 = 3;
	const PATH_FOR_SAVE_IMG = '/www/images/it';
	
	//define('PATH_FOR_GET_IMG',  'http://allcatalog.biz/images/it/'); 
	public $addAtr = true;

	function __construct()
	{

		$cmf = new SCMF;
		$this->db = $cmf;


	}

		function getFiles()
	{
		$ext = 'xml';
		$i = 1;
		while (!file_exists(PATH_FOR_GET_XML_FILE.'/'.$i.'_0.'.$ext))
		{
			$i++;
			$file_name=PATH_FOR_GET_XML_FILE.'/'.$i.'_0.'.$ext;
		}
		
		
		$buf=file_get_contents($file_name);
		$OUT=fopen(PATH_FOR_XML.'/'.$i.'_0.'.$ext,"w");
		fwrite($OUT,$buf);
		fclose($OUT);
		$notexist=0;
		for($j = 1;$notexist!=1;$j++)
		{
	
		if(file_exists(PATH_FOR_GET_XML_FILE.'/'.$i.'_'.$j.'.'.$ext))
		{
			$file_name=PATH_FOR_GET_XML_FILE.'/'.$i.'_'.$j.'.'.$ext;
			print_r($file_name);
			$buf=file_get_contents($file_name);
			$OUT=fopen(PATH_FOR_XML.'/'.$i.'_'.$j.'.'.$ext,"w");
			fwrite($OUT,$buf);
			fclose($OUT);
			
		}
	    else{$notexist=1;}
	}		


	}
	
	
	
function addBrands($xml)
	{
		$status=1;
		$i=0;
		foreach ($xml->Brands->Brand as $brand)
		{

			//$b=iconv("UTF-8", "Windows-1251",$brand);
			$b=$brand;
			
			$id=$xml->Brands->Brand[$i]->attributes(); 
			
			if($this->db->selectrow_array('select NAME from BRAND where NAME=?', $b)) // compare name of Bands
			{
			
			$brandid=$this->db->selectrow_array('select BRAND_ID from BRAND where NAME=?', $b); // old brand id
				
			$this->db->execute('update BRAND set BRAND_ID=? where NAME=?',$id,$b);
									
			$this->db->execute('update CAT_BRAND set BRAND_ID=? where BRAND_ID=?',$id,$brandid);
			
			$this->db->execute('update ITEM set BRAND_ID=? where BRAND_ID=?',$id,$brandid);
			
			}
			else
			{
			$this->db->execute("insert ignore into BRAND(BRAND_ID,NAME,STATUS) values ('$id', '$b','$status')");
			}
			$i++;
		}
	}

function addUnits($xml)
	{
		$i=0;
		foreach ($xml->Units->unit as $unit)
		{

			//$u=iconv("UTF-8", "Windows-1251",$unit);
			$u=$unit;
			$id=$xml->Units->unit[$i]->attributes();		
			if($this->db->selectrow_array('select NAME from UNIT where NAME=?', $unit)) // compare name of Bands
			{
			$unit_id=$this->db->selectrow_array('select UNIT_ID from UNIT where NAME=?', $unit);
			$this->db->execute('update UNIT set UNIT_ID=? where UNIT_ID=?',$id,$unit_id);
			}
			else
			{	
		
			$this->db->execute("insert ignore into UNIT(UNIT_ID,NAME) values ('$id', '$u')");
			
			}
			$i++;
		}
	}



	function addAtrrGroups($xml)
	{
		$i=0;
		$gr_name=$xml->Attributs->group->name; //Атрибуты для рубрики Телефоны/Сотовые телефоны
		$pieces = explode("/", $gr_name);
		$n=$pieces[1];
		//print_r($n);
		if(!$this->db->selectrow_array('select ATTRIBUT_GROUP_ID from ATTRIBUT_GROUP where NAME=?',$n))
		{
		//$gr_id=$this->db->GetSequence('ATTRIBUT_GROUP');
		$this->db->execute("insert ignore into ATTRIBUT_GROUP(NAME)values('$n')");
		$gr_id=$this->db->selectrow_array('select ATTRIBUT_GROUP_ID from ATTRIBUT_GROUP where NAME=?',$n);
		}
		else 
		{
		$gr_id=$this->db->selectrow_array('select ATTRIBUT_GROUP_ID from ATTRIBUT_GROUP where NAME=?',$n);
		}
		
		
		foreach ($xml->Attributs->group->attributs_list->attribut as $atrr)
		{
			foreach($xml->Attributs->group->attributs_list->attribut[$i]->attributes() as $a => $b)
			{
					
			 if ($a == 'type')
			 {
			 	$type=$b;
			 //	print_r($type);
			 //	die();
			 }
			 	
			}
			$attr_name=$atrr[0];//iconv("UTF-8", "Windows-1251",$atrr);
			
			if($this->db->selectrow_array('select NAME from ATTRIBUT where NAME=?,ATTRIBUT_GROUP_ID=?', $attr_name,$gr_id)) // compare name 
			{
//			$id=$xml->Attributs->group->attributs_list->attribut[$i]->attributes();
//			
//			$a_id=$this->db->selectrow_array('select ATTRIBUT_ID from ATTRIBUT where NAME=?', $attr_name);
//				
//			$this->db->execute('update ATTRIBUT set ATTRIBUT_ID=? where ATTRIBUT_ID=?',$id,$a_id);
//			$this->db->execute('update ITEM0 set ATTRIBUT_ID=? where ATTRIBUT_ID=?',$id,$a_id);
//			$this->db->execute('update ITEM1 set ATTRIBUT_ID=? where ATTRIBUT_ID=?',$id,$a_id);
//			$this->db->execute('update ITEM2 set ATTRIBUT_ID=? where ATTRIBUT_ID=?',$id,$a_id);
//			$this->db->execute('update ITEM7 set ATTRIBUT_ID=? where ATTRIBUT_ID=?',$id,$a_id);
//			
//			$this->db->execute('update ATTRIBUT_LIST set ATTRIBUT_ID=? where ATTRIBUT_ID=?',$id,$attr_name);
			
			}
			
			
			//$id=$xml->Attributs->group->attributs_list->attribut[$i]->attributes();
			foreach($xml->Attributs->group->attributs_list->attribut[$i]->attributes() as $a => $b)
			{	 if ($a == 'id') {	$id=$b; }
			}
			//print_r($id);
			$status=1;
			//$id=$this->db->GetSequence('ATTRIBUT');
			$this->db->execute("insert into ATTRIBUT(ATTRIBUT_ID,ATTRIBUT_GROUP_ID,NAME,TYPE,STATUS) values('$id','$gr_id','$attr_name','$type','$status')");
			
			$i++;
		}
	}


	function addItems($xml)
	{
		
		$status=1;
		$i=0;
		$group=$xml->Attributs->group->attributes(); //85
		$gr_name=$xml->Attributs->group->name; //Атрибуты для рубрики Телефоны/Сотовые телефоны
		$pieces = explode("/", $gr_name);
		$n=$pieces[1];
		//print_r($n);
		if(!$this->db->selectrow_array('select CATALOGUE_ID from CATALOGUE where NAME=?', $n)) //нет такого Каталога
		$gr_id=0;
		else  $gr_id=$this->db->selectrow_array('select CATALOGUE_ID from CATALOGUE where NAME=?',$n); //Сотовые телефоны
		foreach ($xml->Items->item as $item)
		{
			foreach($xml->Items->item[$i]->attributes() as $a => $b)
			{	 if ($a == 'brandId') {	$brand=$b; }
			}
			$id=iconv("UTF-8", "Windows-1251",$xml->Items->item[$i]->attributes());
			
			if($this->db->selectrow_array('select NAME from ITEM where ITEM_ID=?', $id)) {	}
			else
			{
			//$id=$this->db->GetSequence("ITEM");	
			$name=trim($item->name);//iconv("UTF-8", "Windows-1251", $item->name);
			$article=$item->article;//iconv("UTF-8", "Windows-1251", $item->article);
			$des=$item->description;//iconv("UTF-8", "Windows-1251",$item->description);
			$image1=$item->image1;//iconv("UTF-8", "Windows-1251",$item->image1);
			//$this->getIMAGES($image1,$id);
			$image2=$item->image2;//iconv("UTF-8", "Windows-1251",$item->image2);
			//$this->getIMAGES($image2,$id);
			$image3=$item->image3;//iconv("UTF-8", "Windows-1251",$item->image3);
			//if (empty($image3)) $image3 = null; else $this->getIMAGES($image3,$id);
			$this->db->execute("insert ignore into ITEM(ITEM_ID,CATALOGUE_ID,BRAND_ID,DESCRIPTION,NAME,ARTICLE,STATUS,TYPENAME,IMAGE1,IMAGE2,IMAGE3) values('$id','$gr_id','$brand','$des','$name','$article','$status','$n','$image1','$image2','$image3')");
			//print_r($i);
			
			}
			$this->addAttr($xml,$i,$id);
				
			$i++;

		}
	  
	
	//return $addAtr;
	}
	function addAttr($xml,$i,$id)
	{
//        if($xml->Items->item[$i]== Null)exit;
//		foreach($xml->Items->item[$c]->attr->attributes() as $a => $b)
//		{	if ($a == 'itemId')		{	$itemId=$b;		}
//		}
		
		$this->delAtr($id);//!!!!!!!!!!!!!!!!!!!!!!!!!!!
		
		foreach ($xml->Items->item[$i]->attr as $item)
		{

			$ii=$item->attributes();//iconv("UTF-8", "Windows-1251",$item->attributes());
							
			$attr=$item;//iconv("UTF-8", "Windows-1251", $item);
				
			$type=$this->db->selectrow_array('select TYPE from ATTRIBUT where ATTRIBUT_ID=?', $ii); //select CATALOGUE_ID from CATALOGUE where PARENT_ID=?',$id

		   if($type == self::TYPE3){
				$atr_name=$this->db->selectrow_array('select NAME from ATTRIBUT where ATTRIBUT_ID=?', $ii);
				$atr_list_id = $this->db->selectrow_array('select ATTRIBUT_LIST_ID from ATTRIBUT_LIST where NAME=? and ATTRIBUT_ID = ?', $attr, $ii);
					if(!$atr_list_id){// нет в таблице ATTRIBUT_LIST такого значения атрибутта
						$atr_list_id=$this->db->GetSequence('ATTRIBUT_LIST'); //Sequence
						//$atr_id=$this->db->selectrow_array('select ATTRIBUT_ID from ATTRIBUT where NAME=?', $atr_name);
						$this->db->execute("insert ATTRIBUT_LIST(ATTRIBUT_LIST_ID,ATTRIBUT_ID,NAME) values('$atr_list_id','$ii','$attr')");
					}
				$ii = $atr_list_id;
				
			}
			$table=$this->getTable($type); //сохранить в нужной таблице
			
			$this->db->execute("insert ignore into $table(ITEM_ID,ATTRIBUT_ID,VALUE) values('$id','$ii','$attr')");
			
	
		}

	

	}

function getTable($t)
		{
		switch ($t)
						{
						    case 0:
						        $table='ITEM0';
						      
						        break;
						    case 1:
						       	$table='ITEM1';
						        
						        break;
						    case 2:
						        $table='ITEM2';
						        
						        break;
					         case 3:
						        $table='ITEM0';
						       
						        break;
					         case 4:
						        $table='ITEM0';
						        
						        break;
					         case 5:
						        $table='ITEM0';
						       
						        break;
					         case 6:
						        $table='ITEM0';
						       
						        break;
					         case 6:
						        $table='ITEM0';
						        
						        break;
							 case 7:
						        $table='ITEM7';
						        
						        break;						
						
						}//switch
						
					return $table;	
		}
function getIMAGES($image,$id)
	{
		
		$pieces=explode("#",$image);
		$name=$pieces[0];
		//print_r($name);
		$pref = substr($name, 0, 2); 
		$newname=$id;
		if($pref=='s_') $newname='s_'.$id;
		if($pref=='b_') $newname='b_'.$id;
		
		$file_name=PATH_FOR_GET_IMG.'/'.$name;
		$buf=file_get_contents($file_name);
		$OUT=fopen(PATH_FOR_SAVE_IMG.'/'.$newname,"w");
		fwrite($OUT,$buf);
		fclose($OUT);
		
	}

    function delAtr($id)
    {
    // ATTRIBUTES
						
				$this->db->execute("delete from ITEM0 where ITEM_ID=?",$id);
				$this->db->execute("delete from ITEM1 where ITEM_ID=?",$id);
				$this->db->execute("delete from ITEM2 where ITEM_ID=?",$id);
				$this->db->execute("delete from ITEM7 where ITEM_ID=?",$id);		
    
    }
	
}

