<?php

class ImageConvertor{
  const QUOLITY = 90;	

  const DIF_SIZE = 20;	
	
  private  $typeFormat="";	
  private  $imageWidth;
  private  $imageHeight;
  private  $savePath; // Пусть для сохранения сконвертируемой картинки
  private  $filePath;
  public   $itemResourceID;
  private  $fName;  // Имя файла в который сохраняем картинку
  private  $bX;  // Требуемая ширина конвертируемой картинки
  private  $bY;  // Требуемая высота конвертируемой картинки

  function __construct($filePath, $savePath, $fName, $bX, $bY){
	  $this->filePath = $filePath;
    $this->savePath = $savePath;

	  $this->bX = $bX;
	  $this->bY = $bY;

	  $size = getimagesize($this->filePath);
	  
	  $this->fName = $this->getTranslitName($fName);  
 	  $this->typeFormat = $this->setFormatImages($size);
      
	  //$this->savePath.=$this->fName.".".$this->typeFormat;

 	  $this->savePath.=$this->fName;
	  
    $this->imageWidth = $size[0];
    $this->imageHeight = $size[1];
	  
    $this->itemResourceID = $this->getImgFrom($this->filePath, $this->typeFormat);

    if($this->typeFormat != 'jpeg'){
      $imageOut = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
      imagecopyresampled($imageOut, $this->itemResourceID, 0, 0, 0, 0, $this->imageWidth , $this->imageHeight, $this->imageWidth , $this->imageHeight);
      imagejpeg($imageOut, $this->filePath, self::QUOLITY);      
      $size = getimagesize($this->filePath);      
      $this->typeFormat = $this->setFormatImages($size);      
      $this->itemResourceID = $this->getImgFrom($this->filePath, $this->typeFormat); 
    }
  }  

  private function imageComposeAlpha( &$src, &$ovr, $ovr_x, $ovr_y, $ovr_w = false, $ovr_h = false) //for resize watermark
  {
      if( $ovr_w && $ovr_h )
          $ovr =$this->imageResizeAlpha( $ovr, $ovr_w, $ovr_h );
         
      /* Noew compose the 2 images */
      imagecopy($src, $ovr, $ovr_x, $ovr_y, 0, 0, imagesx($ovr), imagesy($ovr) );   
  }

  private function imageResizeAlpha(&$src, $w, $h){
    $temp = imagecreatetruecolor($w, $h);
   
    $background = imagecolorallocate($temp, 0, 0, 0);
    ImageColorTransparent($temp, $background); // make the new temp image all transparent
    imagealphablending($temp, false); // turn off the alpha blending to keep the alpha channel
   
    imagecopyresized($temp, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));

    return $temp;
  }

  protected function setFormatImages($size){
	  $type = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
	  return $type;
  }

  private function getImgFrom($src, $type){
	  if ($type == 'bmp') $icfunc = 'ImageCreateFromBMP';
	  else $icfunc = "imagecreatefrom" . $type;
	  
	  if(!function_exists($icfunc)) return false;
	  
	  $isrc = $icfunc($src);	
	  
	  return $isrc; 	
  }


  private function getTranslitName($name){
	  $space_patterns[0] = "/\s/";
	  $space_patterns[1] = "/%20/";
	  $space_replacements[0] = "_";
	  $space_replacements[1] = "_";
	  
	  $translitRus=array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',' ','А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Э','Ю','Я','Ь','Ъ','Ы');
  	$translitEng=array('a','b','v','g','d','e','e','g','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','c','s','s','_','i','_','e','u','y','_','A','B','V','G','D','E','G','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','C','S','S','E','U','Y','_','_','_');
    
  	$fName = preg_replace($space_patterns, $space_replacements, $name);

    for ($j=0; $j<65; $j++) {
      $fName=str_replace($translitRus[$j],$translitEng[$j],$fName);
    }
    
    return $fName;	
  }


  private function getImageNewSizeWatermark($width, $height, $newWidth, $newHeight){
	  if ( ($width > $newWidth)   ||   ($height > $newHeight )){	
	    if ($width > $newWidth  &&  $height <= $newHeight)	
	      $prop = $newWidth/$width;
      elseif ($height > $newHeight  &&  $width <= $newWidth)
	      $prop = $newHeight/$height;
      else 
	      $prop = ($width > $height) ? $newWidth/$width :  $newHeight/$height;

      $newWidth = $width * $prop;
      $newHeight = $height * $prop;
	  }   
    
    return array($newWidth, $newHeight); 
  }

  private function getImageNewSize($width, $height, $newWidth, $newHeight){
    if ( ($width > $newWidth)   ||   ($height > $newHeight )){
	    $k = $height/$width;
	    
	    $Hnew = $newWidth*$k;
	      
	    if($Hnew > $newHeight){
		    $newWidth = $newHeight * (1/$k);
		    $newHeight = $newWidth * $k;
	    }else{
		    $newHeight = $newWidth * $k;
	    }
	    
	    
      return array(ceil($newWidth), ceil($newHeight)); 
    }
  }

  private function getPosition($width, $height, $waterWidth, $waterHeight){

	  $x = ($width - $waterWidth) /2;
	  $y = ($height - $waterHeight) /2;
	  
    return array($x, $y);     
  }


  function createWaterMark($watermark_path){
    $watermark_info = getimagesize($watermark_path);
      
    $watermark_x = $watermark_info[0];
    $watermark_y = $watermark_info[1];
      
	  $imageLogoIn = $this->itemResourceID;	 

									    
	  $type = $this->setFormatImages($watermark_info);
	    
	  $imageLogoType = $this->getImgFrom($watermark_path, $type);
        
    if ($type == 'png'){    	  
      $size = $this->getImageNewSize($watermark_x, $watermark_y, $this->imageWidth, $this->imageHeight);
      if ($size){
			  $watermark_x = $size[0];
			  $watermark_y = $size[1];
		  }
      
      $size = $this->getPosition($this->imageWidth, $this->imageHeight, $watermark_x, $watermark_y);	
    	  
		  $this->imageComposeAlpha($imageLogoIn, $imageLogoType, $size[0], $size[1], $watermark_x, $watermark_y);
		  $path = $this->savePath;
		  
		  imagejpeg($imageLogoIn, $path); // output to browser
    }	
  }

  function resizeImage(){
    $size = $this->getImageNewSize($this->imageWidth, $this->imageHeight, $this->bX, $this->bY);
    if ($size){
	    $this->bX = $size[0];
	    $this->bY = $size[1];
    }
	  
    $imageOut = imagecreatetruecolor($this->bX, $this->bY);

    imagecopyresampled($imageOut, $this->itemResourceID, 0, 0, 0, 0, $this->bX, $this->bY, $this->imageWidth , $this->imageHeight);
   
    $this->imageWidth = $this->bX;
    $this->imageHeight = $this->bY;
    
    imagejpeg($imageOut, $this->savePath, self::QUOLITY);      
    imagedestroy($imageOut);
    
    $this->itemResourceID = $this->getImgFrom($this->savePath, $this->typeFormat);
  }
  
  function copyImage(){
    copy($this->filePath, $this->savePath);
    
    $this->itemResourceID = $this->getImgFrom($this->savePath, $this->typeFormat);
  }
}
?>