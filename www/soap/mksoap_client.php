<?
set_time_limit(3600);

//$ID = 'adlabsTest';
define('PATH_FOR_XML', 'E:/projects/catalog/www/soap');
define('ITEM_COUNT', 300);
define('WSDL', 'http://catalog.loc/soap/soap_catalog2.wsdl'); //("http://allcatalog.biz/soap/soap_catalog.wsdl");


$ID = '21f1aad5eb'//tehnohata//'56F9UT67ME';//meta
$new = 0;
$description = 1;


header("Content-Type: text/html; charset=windows-1251");

ini_set("soap.wsdl_cache_enabled", 0);
//ini_set("default_socket_timeout", 120); //MK

$client = new SoapClient(WSDL);

function CreateXML($ID,$client,$groups,$i,$file_name,$LL,$UL)
{
	//	for($i=0;$i<sizeof($groups); $i++)
	//{

	$header = '<?xml version="1.0" encoding="UTF-8"?><Databases>';
	$footer='</Databases>';


	$fp = fopen(PATH_FOR_XML.'/'.$file_name,'w'); // HEADER
	fwrite($fp,$header);


	//������
	$brands = $client->getBrands($groups[$i],$ID);
	$brands = trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$brands));
	//$string .=$brands;
	echo "Brands received\n";

	fwrite($fp,$brands);


	//�����
	$units = $client->getUnits();
	$units = trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$units));
	//$string .=$units;
	echo "Units received\n";

	fwrite($fp,$units);



	//��������
	$attributs = $client->getAttributGroups($groups[$i]);
	$attributs = trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$attributs));
	//$string .=$attributs;
	echo "Attributs received\n";

	fwrite($fp,$attributs);



	//������
	//	$LL = 0;
	//	$UL = 10;

	$items = $client->getItems($groups[$i],$ID,$LL,$UL);

	$items = trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$items));
	//$string .=$items;


	echo "Items received\n";

	fwrite($fp,$items);



	//FOOTER
	fwrite($fp,$footer);
	fclose($fp);


	//����� ������ ���������
	//} //FOR

}//MyFUNC

try
{
$cats = $client->getTree($ID);  //echo $cats;
$groups = explode(",",$cats);

//print_r (sizeof($groups));
//print_r ($groups);
//die();

	$header = '<?xml version="1.0" encoding="UTF-8"?><Databases>';
	$footer='</Databases>';

	


for($i=0;$i<sizeof($groups); $i++)
{
	
		//������
		$brands = $client->getBrands($groups[$i],$ID);
		$brands = trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$brands));
	
		//�����
		$units = $client->getUnits();
		$units = trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$units));
	
		//��������
		$attributs = $client->getAttributGroups($groups[$i]);
		$attributs = trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$attributs));
	
	$items_ids = $client->getItemsIds($groups[$i],$ID);

	//	print_r(count($items_ids->string));
	//	print_r ($items_ids);
	//	die();
	$c=count($items_ids->string);
    $h=0;
	
	for($j= 0; $j<=count($items_ids->string); $j+=ITEM_COUNT){
		
		$file_name = $groups[$i].'_'.$h.'.xml';
		$fp = fopen(PATH_FOR_XML.'/'.$file_name,'w'); // HEADER
	    fwrite($fp,$header);
	    
	    		echo "Brands received\n";
				fwrite($fp,$brands);
				
				echo "Units received\n";
				fwrite($fp,$units);
				
				echo "Attributs received\n";
				fwrite($fp,$attributs);
				
				
	    
	   	$items = $client->getItems($groups[$i],$ID,$j,ITEM_COUNT);

	   	echo $client->__getLastResponse() ;
		$items = trim(str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$items));
		echo "Items received\n";
	    fwrite($fp,$items);
	    $h++;
	    
	    
	    fwrite($fp,$footer);
    	fclose($fp);
//	    unset($client);
//	    $client = new SoapClient(WSDL);

	}


	//FOOTER

	continue;
	
	
	
	
/*	$LL=0;
	$UL=ITEM_COUNT;
	$n=round($c/ITEM_COUNT);
	if ($c%ITEM_COUNT!=0) {$n=$n+1;}
	
	
	for($ii=0;$ii<$n; $ii++)
	{
	$file_name = $groups[$i].'_'.$ii.'.xml';
	CreateXML($ID,$client,$groups,$i,$file_name,$LL,$UL);
	//$UL=300;
	$LL=$LL+ITEM_COUNT;
	}*/




}//for
	
	
	
	
	
	
	
	/*if($c<300)
	{
		$file_name = $groups[$i].'.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,0,$c);

	}
	if($c>300 and $c<600)
	{
		$file_name = $groups[$i].'.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,0,300);
		$file_name = $groups[$i].'_0.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,300,300);
	}
	if ($c>600 and $c<900)
	{
		$file_name = $groups[$i].'.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,0,300);
		$file_name = $groups[$i].'_0.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,300,300);
		$file_name = $groups[$i].'_1.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,600,300);
	}
	if ($c>900 and $c<1200)
	{
		$file_name = $groups[$i].'.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,0,300);
		$file_name = $groups[$i].'_0.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,300,300);
		$file_name = $groups[$i].'_1.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,600,300);
		$file_name = $groups[$i].'_2.xml';
		CreateXML($ID,$client,$groups,$i,$file_name,900,300);
	}*/
//}//for

} //TRY
catch (SoapFault $exception)
 {
 /*$headers = apache_request_headers();

foreach ($headers as $header => $value) 
{
    echo "<hr>";
	echo "$header: $value <br />\n";
}*/
 echo "<hr>".$exception; echo $exception->getCode();
 echo "<hr>".$exception; echo $exception->getMessage();
 }
