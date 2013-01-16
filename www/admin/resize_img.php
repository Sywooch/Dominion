<?php
require_once ('image_resize.php');

class Resize {
  public $image_name;
  public $image_new_name;
  public $new_image_name;
  public $path_for_images;
  
  //const PATH_FOR_IMAGE_WATER='../i/watermark.png';//for site
  private $pathToWatermark;
  
  public $Size_b_X = 480;
  public $Size_b_Y = 700;
  
  public $Size_X;
  public $Size_Y;

  const Size_s_X=120;
  const Size_s_Y=300;
  
  function __construct(){    
  }
  
  function addSettings($image_name, $image_new_name, $path_for_images, $pathToWatermark, $Size_b_X, $Size_b_Y){
    $this->image_name = $image_name;
    $this->image_new_name = $image_new_name;
    $this->path_for_images = $path_for_images;
    $this->pathToWatermark = $pathToWatermark;
    
    $this->Size_b_X = $Size_b_X;
    $this->Size_b_Y = $Size_b_Y;
  }
  
  function addImage(){
    $url = explode('#', $this->image_name);
    $pathToImages = $this->path_for_images;
    //$pathToWatermark = self::PATH_FOR_IMAGE_WATER;
         
//===================================================================================
    list($w, $h) = @getimagesize($pathToImages.$url[0]);
      
//RESIZE!!!    
    $imageResizer = new ImageConvertor($pathToImages.$url[0],$pathToImages,$url[0],$this->Size_b_X,$this->Size_b_Y);
    // Делаем большую картинку IMAGE3
    if ($w > $this->Size_b_X || $h > $this->Size_b_Y){
      $imageResizer->resizeImage();

      //SAVE IMAGE3 in DB
      list($w, $h) = @getimagesize($pathToImages.$url[0]);
    }
    if($this->pathToWatermark) {
      $imageResizer->createWatermark($this->pathToWatermark);
    }
    $this->new_image_name = "$url[0]#$w#$h";    
    
  }//function
  
  function addImagePost(){
    $tmp_name = $_FILES[$this->image_name]['tmp_name'];
    
    list($w, $h) = @getimagesize($tmp_name);
      
    $imageResizer = new ImageConvertor($tmp_name
                                     , $this->path_for_images
                                     , $this->image_new_name.'.jpg'
                                     , $this->Size_b_X
                                     , $this->Size_b_Y);
  
    // Делаем большую картинку IMAGE3
    if(!empty($this->Size_b_X) && !empty($this->Size_b_Y)){      
      if ($w > $this->Size_b_X || $h > $this->Size_b_Y){
        $imageResizer->resizeImage();

        //SAVE IMAGE3 in DB
        list($w, $h) = @getimagesize($this->path_for_images.$this->image_new_name.'.jpg');
      } 
      else{
        $imageResizer->copyImage();
      }   
    }  
      
    
    if($this->pathToWatermark){
      $imageResizer->createWatermark($this->pathToWatermark);
    }
    
    $this->new_image_name = "{$this->image_new_name}.jpg#{$w}#{$h}";        
  }//function
}

