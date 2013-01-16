<?
set_time_limit(0);
require_once ('/home/kkb/public_html/admin/soap/classes/Parser.php'); //classes/Parser.php'
$object = new Parser;
//$addAtr=true;
//define('PATH_FOR_XML', 'E:/projects/tehnohata/www/soap/files');  
define('PATH_FOR_IMG', '/home/kkb/public_html/admin/soap/images'); //for site   
define('PATH_FOR_XML', '/home/kkb/public_html/admin/soap/files'); //for site   

define('ITEM_COUNT', 300);
$d = dir(PATH_FOR_XML);
while (false !== ($entry = $d->read())) 
{
 if($entry!='.' and $entry!='..') 
 { 
 	echo $entry."<br>\n";
 	$file_name=PATH_FOR_XML.'/'.$entry;
 	$xml = simplexml_load_file($file_name);
	$object->addBrands($xml);
	
	$object->addUnits($xml);
	$object->addAtrrGroups($xml);
	$object->addItems($xml);
			 
	 
 }
}

//$l=strlen($entry)
//$rest = substr($entry, $l-3, 3);
//if($rest='zip') 
//$zip = new ZipArchive;
//if ($zip->open('/home/kkb/public_html/admin/soap/images/images85.zip') === TRUE) {
//    $zip->extractTo(PATH_FOR_IMG.'/');
//    $zip->close();
//}




die();

//===============================================================

//$local_file = '113_0.xml';
//$server_file = '/public_html/allcatalog/soap_client/113_0.xml';
//
//$conn_id = ftp_connect('allcatalog.biz');
//
//
//
//// вход с именем пользователя и паролем
//$login_result = ftp_login($conn_id, 'holodki', 'gkhkl%y_fhgwffe');
//
//// попытка скачать $server_file и сохранить в $local_file
//if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
//    echo "WRITE INTO $local_file\n";
//} else {
//    echo "ERROR\n";
//}
//
//// закрытие соединения
//ftp_close($conn_id);
//
//die();
//===============================================================

//$object->getFiles();


//$file_name = PATH_FOR_XML.'/'.'85_0.xml';
$ext = 'xml';
$i = 1;
while (!file_exists(PATH_FOR_XML.'/'.$i.'_0.'.$ext))
{
	$i++;
	$file_name=PATH_FOR_XML.'/'.$i.'_0.'.$ext;
	//print_r($i);

}
//print_r($filename);

$xml = simplexml_load_file($file_name);
$object->addBrands($xml);

$object->addUnits($xml);
$object->addAtrrGroups($xml);
$addAtr=$object->addItems($xml);
if($addAtr==true)
{
	for($c=0; $c<=ITEM_COUNT-1; $c++)
	{
		$object->addAttr($xml,$c);
		
	}
}
print_r("0-FILE PARSE ");

//die();
$notexist=0;
for($j = 1;$notexist!=1;$j++)
{

	if(file_exists(PATH_FOR_XML.'/'.$i.'_'.$j.'.'.$ext))
	{
		$file_name=PATH_FOR_XML.'/'.$i.'_'.$j.'.'.$ext;
		//print_r($file_name);
		$xml = simplexml_load_file($file_name);
		$addAtr=$object->addItems($xml);
		print_r($j."-FILE PARSE ");
		if($addAtr==true)
		{
			for($c=0; $c<=ITEM_COUNT-1; $c++)
			{
				$object->addAttr($xml,$c);
			}
		}
	}
    else{$notexist=1;}
}



//$xml = simplexml_load_file($file_name);
//print_r($xml);
//$object->addBrands($xml);
//$object->addUnits($xml);
//$object->addAtrrGroups($xml);
//$object->addItems($xml);
//$c=ITEM_COUNT-1;
//$object->addAttr($xml,$c);
//for($c=0; $c<=ITEM_COUNT-1; $c++)
//{
//	$object->addAttr($xml,$c);
//  }//for 0-299*/
//	$object->del();