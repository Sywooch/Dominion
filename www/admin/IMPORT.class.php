<?php
class phpException extends exception {
    public function __construct($errno, $errstr, $errfile, $errline) {
        parent::__construct();
        $this->code = $errno;
        $this->message = $errstr;
        $this->file = $errfile;
        $this->line = $errline;
    }
}

function err2exc($errno, $errstr, $errfile, $errline) {
    throw new phpException($errno, $errstr, $errfile, $errline);
}

set_error_handler('err2exc', ~E_ALL & ~E_NOTICE &~ E_USER_NOTICE | E_STRICT);
error_reporting(E_ALL | E_STRICT );



$attribs = array('2','3','5','6','7','8','9','10','11','12','13');
$attribs2 = array('10','11','13');

define('ARTIKUL', 1);
define('TYPE', 2);
define('BRAND', 2);
define('COUNTRY', 3);
define('PRICE', 5);
define('PRICE1', 6);


define('ATTR_STARTS', 6);

define('LIST_START', 0);


class IMPORTER {
var $upl_file;
var $data;
var $count;
var $rows;
var $cmf;
var $list;
var $v_brand;
protected  $v_articul;
protected  $v_country;
protected  $v_price;
protected  $v_price1;
protected  $v_articul_group;
protected  $v_type;

protected  $v_catalogue;

const LIST_PARAM = 1;


//=========================
const ID = 1;
const ARTICLE = 2;
const NAME = 3;
const PRICE = 4;
const PRICE1 = 5;
const CURRENCY = 6;


//===========================


protected $v_param= array('ID_CATALOG_PARAM' => 1, 'ID_ATRIBUT_GROUP_PARAM' => 2);
	
  function  __construct($cmf){
    $this->list = LIST_START; // С какого листа считываем
    $this->v_type = TYPE;
    $this->v_articul = ARTIKUL; // Артикул
    $this->v_brand = BRAND;
    $this->v_country = COUNTRY;
    $this->v_price = PRICE; // Цена
    $this->v_price1 = PRICE1; // Цена опт

    $this->v_attr_start = ATTR_STARTS;

    $this->upl_file=isset($_FILES['file']['tmp_name'])?$_FILES['file']['tmp_name']:false;

    if ($this->upl_file){		 
      $upl_file_name = $_FILES['file']['name'];
      $parts = explode(".",$upl_file_name);
      $ext = array_pop($parts);
      if($ext!='xls') {echo '<font color="red">Файл должен быть в формате XLS</font><br></br>';
        $this->upl_file = false;
        return false;
      }
    } else return false;

    $path = 'tmp/'.$upl_file_name;
    if(is_uploaded_file($this->upl_file))
    {
    move_uploaded_file($this->upl_file,$path);
    }

    //Чтение файла
    $this->data = new Spreadsheet_Excel_Reader();
    $this->data->setOutputEncoding('CP1251');
    $this->data->setUTFEncoder('mb');
    $this->data->read($path);  

    //print_r($this->data->sheets[$this->list]);

    $this->count = count($this->data->sheets); //Кол-во закладок в файле

    //print_r($this->count);
    // die();

    //$this->v_catalogue = $this->data->sheets[self::LIST_PARAM]['cells'][2][$this->v_param['ID_CATALOG_PARAM']];
    //$this->v_articul_group = $this->data->sheets[self::LIST_PARAM]['cells'][2][$this->v_param['ID_ATRIBUT_GROUP_PARAM']];
    $this->rows = $this->data->sheets[$this->list]['numRows']-1; //Количество товарных позиций

    $this->cmf =& $cmf;

  }
//=================================================================================================

	function read_saveFile()
	{
	//global $attribs, $attribs2;
    //   $new = 0;
    //   $updated = 0;
	//   $c= $this->list;
	   
//	   print_r($this->data->sheets[$this->list]['cells'][2][1]);
//	   die();

             //Цикл по строкам
             for($i = 1; $i <= $this->data->sheets[$this->list]['numRows']; $i++)
             {
                 /***************  Цикл по ячейкам  **********************/

             //if ($i == 1) $this->SetAttributes($i);

             if ($i!=1 && !empty($this->data->sheets[$this->list]['cells'][$i][self::ID]) )
            // && !empty($this->data->sheets[$this->list]['cells'][$i][self::ARTICLE]) 
            // && !empty($this->data->sheets[$this->list]['cells'][$i][self::NAME])
            // && !empty($this->data->sheets[$this->list]['cells'][$i][self::PRICE])
           //  && !empty($this->data->sheets[$this->list]['cells'][$i][self::PRICE1])) 
             
             $this->addData($i);
                          //$this->SetData($i);
                 /******************  Конец цикла по ячейкам  *****************************/
                 //echo "<hr>";
             }
             print_r("Обновление сделано!!!");
		
	}
	function addData($i)
	{
		$id=$this->data->sheets[$this->list]['cells'][$i][self::ID]; 
        $article=$this->data->sheets[$this->list]['cells'][$i][self::ARTICLE]; 
        $name=$this->data->sheets[$this->list]['cells'][$i][self::NAME];
        $price=$this->data->sheets[$this->list]['cells'][$i][self::PRICE];
        $price1=$this->data->sheets[$this->list]['cells'][$i][self::PRICE1];
        $cur=$this->data->sheets[$this->list]['cells'][$i][self::CURRENCY];
        
        $curid=$this->cmf->selectrow_array('select CURRENCY_ID from CURRENCY where SYSTEM_NAME=?', $cur);
        
        
//        $l=strlen($name);
//        $pieces = explode(" ",$name);
//        if(count($pieces)>=3)
//        {
//        $typename=$pieces[0];
//        $l1=strlen($typename);
//        $brand=$pieces[1];
//        $l2=strlen($barand);
//        $l_sum=$l1+$l2;
//        $item_name=substr($name,$l_sum-1,$l-$l_sum);//$pieces[2];
//        $brandid=$this->cmf->selectrow_array('select BRAND_ID from BRAND where NAME=?', $brand);
//        }
        
		if($price > 0)
		{
        $this->cmf->execute('update ITEM
       		set PRICE=?,PRICE1=?,STATUS=?,ARTICLE=?,CURRENCY_ID=? where ITEM_ID=?', 
             $price,$price1,1,$article,$curid,$id);
		}
		else{ $this->cmf->execute('update ITEM
       		set PRICE=?,PRICE1=?,STATUS=?,ARTICLE=?,CURRENCY_ID=? where ITEM_ID=?', 
             $price,$price1,0,$article,$curid,$id);
		}
	}
	
function addEmit($i)
	{
		 $name=$this->data->sheets[$this->list]['cells'][$i][self::EMIT];
		 //print_r("EMIT=".$name);
		 
	     if($this->cmf->selectrow_array('select NAME from EMIT where NAME=?', $name))
			{			}
			else
			{
				
				$this->cmf->execute("insert ignore into EMIT (NAME) values (?)",$name);
			//if (mysql_error()){	echo mysql_error();	die();}
			
			
			}
	}
function addRoad($i)
	{
		 $name=trim($this->data->sheets[$this->list]['cells'][$i][self::ROAD]);
		 //print_r("KOD=".iconv_get_encoding($name)); 
		 //$name=iconv("UTF-8", "Windows-1251",$name);
		 //$name = mb_convert_encoding($name, "UTF-8", "Windows-1251");
		 // print_r("ROAD CODE=".substr("$name",2,1));
		 
//		 print_r("NAME=".$name);
//		 print_r("ROAD CODE=".substr("$name",1,1));
//		 die();
	 
		 if(substr("$name",1,1) == '/') $code='';
		 else
		 {
		
		 	//print_r("NAME=".$name);
		 	preg_match('/^(.+)\s/Uis', $name, $matches);
		 	$code=$matches[0];
		 	$code = mb_convert_encoding($code, "UTF-8", "Windows-1251");
		 	
		   	 	
		 }
		 
		 $name = mb_convert_encoding($name, "UTF-8", "Windows-1251");//convert name
		 
	     if($this->cmf->selectrow_array('select NAME from ROAD where NAME=?', $name))
			{			}
			else
			{
				
				
				$this->cmf->execute("insert ignore into ROAD (NAME,CODE) values (?,?)",$name,$code);
			   if (mysql_error()){	echo mysql_error();	die();}
			}

		 // ADD IN	REF_ROAD_REGION
		 $region=trim($this->data->sheets[$this->list]['cells'][$i][self::REGION]);
		 $region= mb_convert_encoding($region, "UTF-8", "Windows-1251");
	     $road_id=$this->cmf->selectrow_array('select ROAD_ID from ROAD where NAME=?', $name);
		 $region_id=$this->cmf->selectrow_array('select REGION_ID from REGION where NAME=?', $region);	
		 $this->cmf->execute("insert ignore into REF_ROAD_REGION (ROAD_ID,REGION_ID) values ('$road_id','$region_id')");
		 
			 
	}
function addCity($i)
	{
		 $name=trim($this->data->sheets[$this->list]['cells'][$i][self::CITY]);
		  $name = mb_convert_encoding($name, "UTF-8", "Windows-1251");
		// print_r("CITY=".$name);
	     if($this->cmf->selectrow_array('select NAME from CITY where NAME=?', $name))
			{			}
			else
			{
				 $region=trim($this->data->sheets[$this->list]['cells'][$i][self::REGION]);
				 $region= mb_convert_encoding($region, "UTF-8", "Windows-1251");
				 
				 $region_id=$this->cmf->selectrow_array('select REGION_ID from REGION where NAME=?', $region);	
				
				$this->cmf->execute("insert ignore into CITY (NAME,REGION_ID) values (?,?)",$name,$region_id);
			}
	}
  function addRegion($i)
	{
		 $name=trim($this->data->sheets[$this->list]['cells'][$i][self::REGION]);
		  $name = mb_convert_encoding($name, "UTF-8", "Windows-1251");
		 //print_r("REGION=".$name);
	     if($this->cmf->selectrow_array('select NAME from REGION where NAME=?', $name))
			{			}
			else
			{
				
				$this->cmf->execute("insert ignore into REGION (NAME) values ('$name')");
			}
	}
    function addAzs($i)
	{
		 $emit=$this->data->sheets[$this->list]['cells'][$i][self::EMIT];
		 $term=$this->data->sheets[$this->list]['cells'][$i][self::TERM];
		 $name_to=$this->data->sheets[$this->list]['cells'][$i][self::NAME_TO];
		  $name_to = mb_convert_encoding($name_to, "UTF-8", "Windows-1251");
		 $km=$this->data->sheets[$this->list]['cells'][$i][self::KM];
		 $road=$this->data->sheets[$this->list]['cells'][$i][self::ROAD];
		  $road = mb_convert_encoding($road, "UTF-8", "Windows-1251");
		 $region=$this->data->sheets[$this->list]['cells'][$i][self::REGION];
		  $region = mb_convert_encoding($region, "UTF-8", "Windows-1251");
		 $city=$this->data->sheets[$this->list]['cells'][$i][self::CITY];
		  $city = mb_convert_encoding($city, "UTF-8", "Windows-1251");
		 $address=$this->data->sheets[$this->list]['cells'][$i][self::ADDRESS];
		  $address = mb_convert_encoding($address, "UTF-8", "Windows-1251");
		 
		 
		 $emit_id=$this->cmf->selectrow_array('select EMIT_ID from EMIT where NAME=?', $emit);
		 $road_id=$this->cmf->selectrow_array('select ROAD_ID from ROAD where NAME=?', $road);
		 $city_id=$this->cmf->selectrow_array('select CITY_ID from CITY where NAME=?', $city);
		 
	    
				$id=$this->cmf->GetSequence('AZS');
				$this->cmf->execute("insert ignore into AZS 
							(AZS_ID,EMIT_ID,ROAD_ID,CITY_ID,
							ADDRESS,KILOMETRE,NUM_TERMINAL,NAME_TO) 
							values (?,?,?,?,?,?,?,?)",
							$id,$emit_id,$road_id,$city_id,
							$address,$km,$term,$name_to);
			
	}
//=================================================================================================	
	
	
    protected  function SetFirstRow($cell){
	
 //Первая строка со списком колонок

	//Это атрибут


    echo "атрибуты";
                              if(strchr($cell,",")) list($name,$unit) = explode(",",$cell);
                              else
                              {
                                $name = $cell;
                                $unit='';
                              }


                              if($unit)
                              {
                                 $unit_id = $this->cmf->selectrow_array("select UNIT_ID from UNIT where NAME=?",$unit);
                                 if($unit_id == '')
                                 {
                                    //Новая единица измерения
                                    $this->cmf->execute("insert into UNIT set UNIT_ID=?,NAME=?",null,$unit);
                                    $unit_id = mysql_insert_id();
                                 }
                              } else $unit_id = null;

                              $attribut_id = $this->cmf->selectrow_array("select ATTRIBUT_ID from ATTRIBUT where NAME=? and ATTRIBUT_GROUP_ID=?",$name, $this->v_articul_group);

                              echo "select ATTRIBUT_ID from ATTRIBUT where NAME=$name and ATTRIBUT_GROUP_ID=$this->v_articul_group  <br>";
                              if($attribut_id=='')
                              {
                                  //Добавляем новый
                                  echo "Добавляем новый атрибут <br>";
                                  $order = $this->cmf->selectrow_array("select MAX(ORDERING) from ATTRIBUT where ATTRIBUT_GROUP_ID=?" , $this->v_articul_group);
                                  //echo "$order";
                                  $this->cmf->execute("insert into ATTRIBUT set ATTRIBUT_ID='',ATTRIBUT_GROUP_ID=?,NAME=?,TYPE=3,UNIT_ID=?,ORDERING=?,STATUS=1",$this->v_articul_group,$name,$unit_id,$order+1);

                                  $attribut_id = mysql_insert_id();
                                  $this->cmf->execute("insert ignore into ATTR_CATALOG_LINK set CATALOGUE_ID=1,ATTRIBUT_ID=?",$attribut_id);
                              }
                              else
                              {
                                 //Обновляем существующий
                                 
                              	//print_r("update ATTRIBUT set NAME=?,UNIT_ID=? where ATTRIBUT_ID= $name,$unit_id,$attribut_id");
                                 $this->cmf->execute("update ATTRIBUT set NAME=?,UNIT_ID=? where ATTRIBUT_ID=?",$name,$unit_id,$attribut_id);
                              }
                              echo mysql_error();
                           
	
}	
	
    protected  function SetData($i){
	  $new = 0;
	//Список товаров   
	                       	
                           $brand_id = $this->cmf->selectrow_array("select BRAND_ID from BRAND where NAME=?",$this->data->sheets[$this->list]['cells'][$i][BRAND]);
                           if($brand_id == '')
                           {
                              //Добавляем нового
                              $this->cmf->execute("insert into BRAND set BRAND_ID=?,NAME=?,STATUS=1",null,$this->data->sheets[$this->list]['cells'][$i][BRAND]);
                              $brand_id = mysql_insert_id();
                           }

                           /*
                           $visual_text = '';
                           $visual_id = $this->data->sheets[$this->list]['cells'][$i][22]; //Код визуального эффекта
                              if($visual_id != 'Нет')
                              {
                                 //Находим текст
                                 foreach($this->data->sheets[1]['cells'] as $key => $val)
                                 {
                                    //if($key>2)
                                    //{
                                       if($val[1] == $visual_id)
                                       {
                                          $visual_text = $val[2];
                                          break;
                                       }
                                    //}
                                 }
                              }*/


                           /*
                           $snoska_text = '';
                           $snoska_id = $this->data->sheets[$this->list]['cells'][$i][36]; //Код сноски
                              if($snoska_id != 'Нет')
                              {
                                 //Находим текст
                                 foreach($this->data->sheets[4]['cells'] as $key => $val)
                                 {
                                    //echo $key ."=>". print_r($val); echo "<hr>";
                                    //if($key>2)
                                    //{
                                       if($val[1] == $snoska_id)
                                       {
                                          $snoska_text = $val[2];
                                          break;
                                       }
                                    //}
                                 }
                              }*/



                           /*
                           $use_text = '';
                           $use_id = $this->data->sheets[$this->list]['cells'][$i][37]; //Код области применения
                              if($use_id != 'Нет')
                              {
                                 //Находим текст
                                 foreach($this->data->sheets[3]['cells'] as $key => $val)
                                 {
                                    //if($key>2)
                                    //{
                                       if($val[1] == $use_id)
                                       {
                                          $use_text = $val[2];
                                          break;
                                       }
                                    //}
                                 }
                              }*/



                           /*
                              $character_id = $this->data->sheets[$this->list]['cells'][$i][39]; //Код общих характеристик
                              if($character_id != 'Нет')
                              {
                                 //print_r($this->data->sheets[2]['cells']); echo "<hr>";
                                 //Находим текст
                                 $character_text = '';
                                 $keys = array();
                                 $values = array();
                                 for($m=0;$m<sizeof($this->data->sheets[2]['cells']);$m++)
                                 {
                                    $vals = $this->data->sheets[2]['cells'][$m];
                                    if($vals[1]!='')
                                    {
                                       $keys[] = $m;
                                       $values[] = $vals[1];
                                    }
                                 }
                                 $key = array_search($character_id,$values);
                                 $curr = $keys[$key];
                                 $next = $keys[$key+1];
                                 if($next == '')
                                 {
                                    $diff = $curr-$keys[$key-1];
                                    $next = $curr+$diff;
                                 }

                                 for($m=$curr;$m<$next;$m++)
                                 {
                                     $vals = $this->data->sheets[2]['cells'][$m];
                                     $character_text .='<p>'.$vals[2].'</p>';
                                 }

                              }*/

                           $item_id = $this->cmf->selectrow_array("select ITEM_ID from ITEM where ARTICLE=?",$this->data->sheets[$this->list]['cells'][$i][$this->v_articul]);
                           //$item_name =  $this->data->sheets[$this->list]['cells'][$i][4]." ".$this->data->sheets[$this->list]['cells'][$i][6]." (".$this->data->sheets[$this->list]['cells'][$i][1].")";
                           //$item_name =  $this->data->sheets[$this->list]['cells'][$i][6];

$tmp = !empty($this->data->sheets[$this->list]['cells'][$i][$this->v_country])?"(".$this->data->sheets[$this->list]['cells'][$i][$this->v_country].")":"";

$item_name = $this->data->sheets[$this->list]['cells'][$i][$this->v_type]." ".$this->data->sheets[$this->list]['cells'][$i][$this->v_brand]." ".
             $tmp." ".$this->data->sheets[$this->list]['cells'][$i][$this->v_attr_start];


                           $item_unit =  $this->data->sheets[$this->list]['cells'][1][$this->v_price];
                           $pts = explode(",",$item_unit);
                           $pts[1] = trim($pts[1]);
                           list($cur,$iuname) = explode("/",$pts[1]);
                           if($iuname)
                           {
                                 $iunit_id = $this->cmf->selectrow_array("select UNIT_ID from UNIT where NAME=?",$iuname);
                                 if($iunit_id == '')
                                 {
                                    //Новая единица измерения
                                    $this->cmf->execute("insert into UNIT set UNIT_ID=?,NAME=?",null,$iuname);
                                    $iunit_id = mysql_insert_id();
                                 }
                           } else $iunit_id = 0;

                           //$item_name = "";


                           if($item_id == '')
                           {
                              //Добавляем новый
                              $query = "insert into ITEM set ITEM_ID='',
                                                             TYPENAME='".$this->data->sheets[$this->list]['cells'][$i][$this->v_type]."',
                                                             CATALOGUE_ID='',
                                                             CURRENCY_ID='1',
                                                             NAME='".$item_name."',
                                                             ARTICLE='".$this->data->sheets[$this->list]['cells'][$i][$this->v_articul]."',
                                                             BRAND_ID='".$brand_id."',

                                                             PRICE='".$this->data->sheets[$this->list]['cells'][$i][$this->v_price]."',
                                                             PRICE1='".$this->data->sheets[$this->list]['cells'][$i][$this->v_price1]."',
                                                             UNIT_ID='".$iunit_id."',
                                                             STATUS='1'";
                               $query .=",IMAGE2='".$this->data->sheets[$this->list]['cells'][$i][$this->v_articul]."'";
//                              if(!empty($this->data->sheets[$this->list]['cells'][$i][23])) $query .=",IMAGE1='".$this->data->sheets[$this->list]['cells'][$i][17]."'";
//                              if(!empty($this->data->sheets[$this->list]['cells'][$i][24])) $query .=",IMAGE2='".$this->data->sheets[$this->list]['cells'][$i][18]."'";
//                              if(!empty($this->data->sheets[$this->list]['cells'][$i][25])) $query .=",IMAGE3='".$this->data->sheets[$this->list]['cells'][$i][19]."'";

//                                $query = "insert into ALIASES set ITEM_ID='',
//                                                             TYPENAME='".$this->data->sheets[$this->list]['cells'][$i][1]."',
//                                                             BRAND_ID='".$brand_id."'";
                                  


                              $this->cmf->execute($query);
                              echo mysql_error();
                              $item_id = mysql_insert_id();
                              $new++;
                           }
                           else
                           {
                              //Изменяем существующий
                              $query = "update ITEM set ARTICLE='".$this->data->sheets[$this->list]['cells'][$i][$this->v_articul]."'";
                              if($item_name) $query .=",NAME='".$item_name."'";
                              if($brand_id) $query .=",BRAND_ID='".$brand_id."'";
                              //if($visual_text) $query .=",VISUAL = '".$visual_text."'";
                              //if($snoska_text) $query .=",SNOSKA  = '".$snoska_text."'";
                              //if($use_text) $query .=",USE_AREA = '".$use_text."'";

                              
                              //print_r($this->data->sheets[$this->list]['cells'][$i]);
                              
                              if($iunit_id) $query .=",UNIT_ID='".$iunit_id."'";
                              if(!empty($this->data->sheets[$this->list]['cells'][$i][$this->v_price]))
                                 $query .=",PRICE='".$this->data->sheets[$this->list]['cells'][$i][$this->v_price]."'";

                              if(!empty($this->data->sheets[$this->list]['cells'][$i][$this->v_price1]))
                                 $query .=",PRICE1='".$this->data->sheets[$this->list]['cells'][$i][$this->v_price1]."'";

//                              if(!empty($this->data->sheets[$this->list]['cells'][$i][17])) $query .=",IMAGE1='".$this->data->sheets[$this->list]['cells'][$i][17]."'";
//                              if(!empty($this->data->sheets[$this->list]['cells'][$i][18])) $query .=",IMAGE2='".$this->data->sheets[$this->list]['cells'][$i][18]."'";
//                              if(!empty($this->data->sheets[$this->list]['cells'][$i][19])) $query .=",IMAGE3='".$this->data->sheets[$this->list]['cells'][$i][19]."'";
                              
                              $query .=" where ITEM_ID='".$item_id."'"; //echo $query."<br>";
                              $this->cmf->execute($query);
                              //$updated++;
                           }

                           //Добавляем XML для товара,текст общих характеристик
                           //echo $item_id."=>". $character_text."<hr>";
                           if(!empty($character_text))
                           {
                              $cntx = $this->cmf->selectrow_array("select COUNT(*) from XMLS where XMLS_ID=? and TYPE=3",$item_id);
                              if($cntx == 0) $this->cmf->execute("insert into XMLS set XMLS_ID=?,TYPE=3,XML=?",$item_id,'<story>'.$character_text.'</story>');
                              else
                              {
                                 $this->cmf->execute("update XMLS set XML=? where XMLS_ID=? and TYPE=3",'<story>'.$character_text.'</story>',$item_id);
                              }
                              //echo mysql_error()."<hr>";
                           }
      $this->SetAttribute($i, $item_id);
	
}

    protected function  SetAttribute($i, $item_id){
    global $attribs;
//  for($j = $this->v_attr_start; $j <= $this->data->sheets[$this->list]['numCols']; $j++)
    for($j = $this->v_attr_start; $j <= $this->data->sheets[$this->list]['numCols']; $j++)
      if(!empty($this->data->sheets[$this->list]['cells'][$i][$j])){

      
                              //Значения атрибутов
                              $attribute_name = $this->data->sheets[$this->list]['cells'][1][$j] ;
                              if(strchr($attribute_name,",")) list($name,$unit) = explode(",",$attribute_name);
                              else
                              {
                                $name = $attribute_name;
                                $unit='';
                              }
                              list($attribut_id, $v_type) =
                                    $this->cmf->selectrow_array("select ATTRIBUT_ID, TYPE from ATTRIBUT where NAME=?
                                    and ATTRIBUT_GROUP_ID=?",$name,  $this->v_articul_group);
                              $value = $this->data->sheets[$this->list]['cells'][$i][$j];
                              if(preg_match("/^\d{1,}(\.)\d{1,}$/",$value))
                              {
                                  echo "<br>Меняем запятую на точку<br>";

                                 //$value = str_replace(".",",",$value);
                              }
                              $value = str_replace("<=","&#8804;",$value);
                              $value = str_replace(">=","&#8805;",$value);

                              $count = $this->cmf->selectrow_array("select COUNT(*) from ATTRIBUT_LIST where ATTRIBUT_ID=? and NAME=?",$attribut_id,$value);
                              echo "insert into ATTRIBUT_LIST set ATTRIBUT_LIST_ID='',ATTRIBUT_ID='".$attribut_id."',NAME='".$value."'<br>";

                              if($count == 0)
                              {
                                 $order = $this->cmf->selectrow_array("select MAX(ORDERING) from ATTRIBUT_LIST where ATTRIBUT_ID=?",$attribut_id);
                                 $this->cmf->execute("insert into ATTRIBUT_LIST set ATTRIBUT_LIST_ID=?,ATTRIBUT_ID=?,NAME=?,ORDERING=?",null,$attribut_id,$value,$order+1);
                              }

                              $attribut_list_id = $this->cmf->selectrow_array("select ATTRIBUT_LIST_ID from ATTRIBUT_LIST where ATTRIBUT_ID=? and NAME=?",$attribut_id,$value);
                              if($item_id && $attribut_list_id)
                              {

                               echo "<br>$v_type<br>";

                               $cnt = $this->cmf->selectrow_array("select COUNT(*) from ITEM0 where ITEM_ID=? and ATTRIBUT_ID=? and VALUE=?",$item_id,$attribut_id,$attribut_list_id);
                               echo $item_id."=>".$value."  cnt = $cnt<br>";
//                                 if($cnt == 0){
                                    // Надо определить куда вставляем заначение атрибута
                                    switch($v_type){
                                    case 0:
                                           $this->cmf->execute("replace ITEM0 set ITEM_ID=?,ATTRIBUT_ID=?,VALUE=?",$item_id,$attribut_id,$value);
                                           break;
                                    case 1:
                                           $this->cmf->execute("replace into ITEM1 set ITEM_ID=?,ATTRIBUT_ID=?,VALUE=?",$item_id,$attribut_id,$value);
                                           break;
                                    default: 
                                    $this->cmf->execute("replace into ITEM0 set ITEM_ID=?,ATTRIBUT_ID=?,VALUE=?",$item_id,$attribut_id,$attribut_list_id);
                                    }

//                                 }
//                                 else
//                                 {
//                                    echo 'update ITEM0 set VALUE='.$attribut_list_id.' where ITEM_ID='.$item_id.' and ATTRIBUT_ID='.$attribut_id.'<br>';
//                                    $this->cmf->execute("update ITEM0 set VALUE=? where ITEM_ID=? and ATTRIBUT_ID=?",$attribut_list_id,$item_id,$attribut_id);
//                                 }
                                 echo mysql_error();
                              }
                          

							/*
                           //Фотки для визуальных эффектов
                           $this->cmf->execute("delete from  VISUAL_EFFECT where ITEM_ID=?",$item_id);
                           if($this->data->sheets[$this->list]['cells'][$i][29] !='Нет') $this->cmf->execute("insert into VISUAL_EFFECT set VISUAL_EFFECT_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][29]);
                           if($this->data->sheets[$this->list]['cells'][$i][30] !='Нет') $this->cmf->execute("insert into VISUAL_EFFECT set VISUAL_EFFECT_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][30]);
                           if($this->data->sheets[$this->list]['cells'][$i][31] !='Нет') $this->cmf->execute("insert into VISUAL_EFFECT set VISUAL_EFFECT_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][31]);
                           if($this->data->sheets[$this->list]['cells'][$i][32] !='Нет') $this->cmf->execute("insert into VISUAL_EFFECT set VISUAL_EFFECT_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][32]);
                           if($this->data->sheets[$this->list]['cells'][$i][33] !='Нет') $this->cmf->execute("insert into VISUAL_EFFECT set VISUAL_EFFECT_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][33]);
                           if($this->data->sheets[$this->list]['cells'][$i][34] !='Нет') $this->cmf->execute("insert into VISUAL_EFFECT set VISUAL_EFFECT_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][34]);
                           if($this->data->sheets[$this->list]['cells'][$i][35] !='Нет') $this->cmf->execute("insert into VISUAL_EFFECT set VISUAL_EFFECT_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][35]);


                           //Фотографии в интерьере
                           $this->cmf->execute("delete from ITEM_PHOTO where ITEM_ID=?",$item_id);
                           if($this->data->sheets[$this->list]['cells'][$i][26] !='Нет') $this->cmf->execute("insert into ITEM_PHOTO set ITEM_ITEM_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][26]);
                           if($this->data->sheets[$this->list]['cells'][$i][27] !='Нет') $this->cmf->execute("insert into ITEM_PHOTO set ITEM_ITEM_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][27]);
                           if($this->data->sheets[$this->list]['cells'][$i][28] !='Нет') $this->cmf->execute("insert into ITEM_PHOTO set ITEM_ITEM_ID='',ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][28]);

                           //Фото области применения
                           //$this->cmf->execute("delete from CHARACTER_PHOTO where ITEM_ID=?",$item_id);
                           //if($this->data->sheets[$this->list]['cells'][$i][38) $this->cmf->execute("insert into CHARACTER_PHOTO set ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][40]);
                           if($this->data->sheets[$this->list]['cells'][$i][38]) $this->cmf->execute("UPDATE ITEM set USE_IMAGE=? where ITEM_ID=?",$this->data->sheets[$this->list]['cells'][$i][38],$item_id);

                           //Фото общих характеристик
                           //$this->cmf->execute("delete from CHARACTER_PHOTO where ITEM_ID=?",$item_id);
                           //if($this->data->sheets[$this->list]['cells'][$i][40]) $this->cmf->execute("insert into CHARACTER_PHOTO set ITEM_ID=?,IMAGE_LARGE=?,STATUS=1",$item_id,$this->data->sheets[$this->list]['cells'][$i][40]);
                           if($this->data->sheets[$this->list]['cells'][$i][40]) $this->cmf->execute("UPDATE ITEM set CHAR_IMAGE=? where ITEM_ID=?",$this->data->sheets[$this->list]['cells'][$i][40],$item_id);
						*/
                   

               }

}

private function SetAttributes($i){


try
{
for($j = $this->v_attr_start; $j <= $this->data->sheets[$this->list]['numCols']; $j++)
        {
//                   echo "i=$i; j=$j <br/>";
                   $cell = trim($this->data->sheets[$this->list]['cells'][$i][$j]);
                   
                   if (empty($cell)) return;
                    // $cell = trim($this->data->sheets[$this->list]['cells'][$i][$j]);  //echo $this->list."==>".$this->listell."|";
                        /*************** Первый лист со списком товаров **********************/
					 $this->SetFirstRow($cell);
                        /****************  Конец обработки первого листа ***************************/
         }
}
 catch (Exception $e) {
   echo 'Caught exception: ',  $e->getMessage(), "\n";
}

}

public function readFile(){
global $attribs, $attribs2;
       $new = 0;
       $updated = 0;
	   $c= $this->list;

             //Цикл по строкам
             for($i = 1; $i <= $this->data->sheets[$this->list]['numRows']; $i++)
             {
                 /***************  Цикл по ячейкам  **********************/

             if ($i == 1) $this->SetAttributes($i);

             if ($i!=1 && !empty($this->data->sheets[$this->list]['cells'][$i][1])) $this->SetData($i);
                 /******************  Конец цикла по ячейкам  *****************************/
                 //echo "<hr>";
             }

}	
	
	
function setHeaderFromFile(){
	
	
	
}

static function setTimer(){
	$upl_file=isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
   if($upl_file)
   {
      $upl_file_name = $_FILES['file']['name'];
      $parts = explode(".",$upl_file_name);
      $ext = $parts[1];
      if($ext!='xls') echo '<font color="red">Файл должен быть в формате XLS</font><br></br>';
      else
      {
         $path = 'csv/'.$upl_file_name;
         if(is_uploaded_file($upl_file))
         {
            move_uploaded_file($upl_file,$path);
         }
         echo '<meta http-equiv="Refresh" content="1;url=IMPORT.php">';
      }
   }
   else echo '<font color="red">Нужно выбрать CSV-file</font><br></br>';
	
}
	
	
	
}

?>
