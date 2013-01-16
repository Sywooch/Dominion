<?php
  class App_Files{
    private $tr = array(
           "Г"=>"G","Ё"=>"YO","Є"=>"E","Ю"=>"YI","Я"=>"I",
           "и"=>"i","г"=>"g","ё"=>"yo","№"=>"#","є"=>"e",
           "ї"=>"yi","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
           "Д"=>"D","Е"=>"E","Ж"=>"ZH","З"=>"Z","И"=>"I",
           "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
           "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
           "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
           "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"'","Ы"=>"YI","Ь"=>"",
           "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
           "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
           "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
           "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
           "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
           "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"'",
           "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"," "=>'-',
           "…" => "-" ,"і" => "i","®" => "-" ,"І" => "I",
           "«" => "-","»" => "-","(" => "",")" => ""
           ,"<br>" => "-","<br/>" => "-","<br />" => "-", "`"=>""
          );
          
    private $space_patterns = array(0=>'/\s/'
                                   ,1=>'/%20/');
                                   
    private $space_replacements = array(0=>'_'
                                       ,1=>'_');
                                       
                                       
    private $file_type = array(
          'image'=>array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png'),
          'pdf'=>array('application/octet-stream', 'text/comma-separated-values', 'application/pdf'),
          'arc'=>array('application/octet-stream', 'application/x-rar-compressed'
                                    , 'application/zip', 'application/x-zip-compressed', 'application/rar'
                                    , 'application/x-rar', 'application/x-zip'),
          'xls'=>'application/vnd.ms-excel', 'application/octet-stream'
          );
          
    public function isFileType($_files, $type){
      if($_files['name']!="" && $_files['size']!=0){
        if (!in_array($_files['type'],$this->file_type[$type])){
          return false;
        }
      }
      
      return true;
    }
          
    public function copyFileTo($savePath, $_files){
      
      $_files['name'] = preg_replace($this->space_patterns, $this->space_replacements, $_files['name']);
      $_files['name'] = strtr($_files['name'], $this->tr);

      $cnt=0;
      $exp = strstr($_files['name'],'.');

      clearstatcache();
      $current_dir=$savePath."/";
      while(file_exists($current_dir.$_files['name'])){
        $cnt++;
        if ($cnt==1){
          $newFileName = substr($_files['name'], 0, strpos($_files['name'],'.')).'_'.$cnt.$exp;
        }
        else{
          $newFileName = substr($_files['name'], 0, strrpos($_files['name'],'_')).'_'.$cnt.$exp;
        }
        $_files['name'] = $newFileName;
      }

      $path=$savePath."/".$_files['name'];

      copy($_files['tmp_name'],$path);
      
      return $_files['name'];
    }
    
    /*
    * @string $savePath - путь до каталога
    * @array $_files - массив $_FILES['name']
    * @array $params - параметры
    *        bX - длин большой картинки 
    *        bY - высота большой картинки 
    *        sX - длин маленькой картинки 
    *        sY - высота маленькой картинки
    *        small - генерировать ли маленькую картинку
    *        watermark - накладывать ли наклейку
    *        watermark_path - путь до наклейки
    */
    
    public function resizeImage($savePath, $_files, $params){
      $endBX = 0;
      $endBY = 0;
      $endSX = 0;
      $endSY = 0;
      
      if(!isset($params['small'])) $params['small'] = false;
      if(!isset($params['watermark'])) $params['watermark'] = false;
      if(!isset($params['watermark_path'])) $params['watermark_path'] = '';
      
      $_files['name'] = preg_replace($this->space_patterns, $this->space_replacements, $_files['name']);
      $_files['name'] = strtr($_files['name'], $this->tr);
      
      $cnt=0;
      $exp = strstr($_files['name'],'.');

      clearstatcache();
      $current_dir=$savePath."/";
      while(file_exists($current_dir.$_files['name'])){
        $cnt++;
        if ($cnt==1){
          $newFileName = substr($_files['name'], 0, strpos($_files['name'],'.')).'_'.$cnt.$exp;
        }
        else{
          $newFileName = substr($_files['name'], 0, strrpos($_files['name'],'_')).'_'.$cnt.$exp;
        }
        $_files['name'] = $newFileName;
      }


      $size = getimagesize($_files['tmp_name']);

      $X = $size[0];
      $Y = $size[1];

      switch ($size[2]) {
        case 1:
          $imageIn = imagecreatefromgif($_files['tmp_name']);
          break;
        case 2:
          $imageIn = imagecreatefromjpeg($_files['tmp_name']);
          break;
        case 3:
          $imageIn = imagecreatefrompng($_files['tmp_name']);
          break;
      }

      if ($X>$params['bX'] && $Y>$params['bY']){
        if ($X>$Y){
          $prop=$params['bX']/$X;
        }
        else{
          $prop=$params['bY']/$Y;
        }
        $endX = $X*$prop;
        $endY = $Y*$prop;
      }
      else{
        $endX = $X;
        $endY = $Y;
      }

      $imageOut = imagecreatetruecolor($endX,$endY);

      imagecopyresampled($imageOut, $imageIn, 0, 0, 0, 0, $endX, $endY, $X, $Y);

      imagejpeg($imageOut, $_files['tmp_name'], 100);

      imagedestroy($imageIn);
      imagedestroy($imageOut);
      
      if($params['watermark']){
        $size = getimagesize($_files['tmp_name']);
        
        switch ($size[2]) {
           case 1:
             $imageLogoIn = imagecreatefromgif($_files['tmp_name']);
             break;
           case 2:
             $imageLogoIn = imagecreatefromjpeg($_files['tmp_name']);
             break;
           case 3:
             $imageLogoIn = imagecreatefrompng($_files['tmp_name']);
           break;
        }
        
        $imageOut = imagecreatetruecolor($endX, $endY);
        $out = "temp/".time().".jpeg";

        $watermark_info = getimagesize($params['watermark_path']);
        
        $watermark_x = $watermark_info[0];
        $watermark_y = $watermark_info[1];
        
        switch ($watermark_info[2]) {
           case 1:
             $imageLogo = imagecreatefromgif($params['watermark_path']);
             break;
           case 2:
             $imageLogo = imagecreatefromjpeg($params['watermark_path']);
             break;
           case 3:
             $imageLogo = imagecreatefrompng($params['watermark_path']);
           break;
        }
        
        if($watermark_info[2]==3){
          $watermark = new watermark();
          $imageOut = $watermark->create_watermark($imageLogoIn ,$imageLogo, 60);

        }
        else{
          imagecolorclosestalpha($imageLogo, 0, 0, 0, 0);

          imagecopy($imageOut, $imageLogoIn, 0, 0, 0, 0, $endX, $endY);

          $w_x = ceil($endX/2) + (ceil($endX/2) - $watermark_x);
          $w_y = ceil($endY/2) + (ceil($endY/2) - $watermark_y);
          ImageCopyMerge($imageOut,$imageLogo, $w_x, $w_y, 0, 0, $watermark_x, $watermark_y, 100);
        }


        imagejpeg($imageOut, $out, 85);

        imagedestroy($imageLogoIn);
        imagedestroy($imageOut);
        imagedestroy($imageLogo);

        $path=$savePath."/".$_files['name'];
        copy($out,$path);
        unlink($out);
      }
      else{
        $path=$savePath."/".$_files['name'];
        if (copy($_files['tmp_name'],$path)){
          $add1="true";
        }
      }
      
      $endBX = $endX;
      $endBY = $endY;

      if ($params['small']==true){
        $size = getimagesize($_files['tmp_name']);

        $X = $size[0];
        $Y = $size[1];

        switch ($size[2]) {
          case 1:
            $imageIn = imagecreatefromgif($_files['tmp_name']);
            break;
          case 2:
            $imageIn = imagecreatefromjpeg($_files['tmp_name']);
            break;
          case 3:
            $imageIn = imagecreatefrompng($_files['tmp_name']);
            break;
        }

        if ($X>$Y){
          $prop=$params['sX']/$X;
        }
        else{
          $prop=$params['sY']/$Y;
        }

        $endX = $X*$prop;
        $endY = $Y*$prop;

        $imageOut = imagecreatetruecolor($endX,$endY);

        imagecopyresampled($imageOut, $imageIn, 0, 0, 0, 0, $endX, $endY, $X, $Y);

        imagejpeg($imageOut, $_files['tmp_name'], 100);

        imagedestroy($imageIn);
        imagedestroy($imageOut);


        $path=$savePath."/small_".$_files['name'];

        copy($_files['tmp_name'],$path);
      }
      
      $endSX = $endX;
      $endSY = $endY;

      return array('bX' => $endBX
                  ,'bY' => $endBY
                  ,'sX' => $endSX
                  ,'sY' => $endSY
                  ,'name' => $_files['name']
                  );
    }
  }
  
class watermark{ 
  # given two images, return a blended watermarked image 
  function create_watermark( $main_img_obj, $watermark_img_obj, $alpha_level = 100 ){ 
    $alpha_level /= 100; # convert 0-100 (%) alpha to decimal 
    
    # calculate our images dimensions 
    $main_img_obj_w = imagesx( $main_img_obj ); 
    $main_img_obj_h = imagesy( $main_img_obj ); 
    $watermark_img_obj_w = imagesx( $watermark_img_obj ); 
    $watermark_img_obj_h = imagesy( $watermark_img_obj ); 
    
    # determine center position coordinates 
    $main_img_obj_min_x = floor( ( $main_img_obj_w / 2 ) - ( $watermark_img_obj_w / 2 ) ); 
    $main_img_obj_max_x = ceil( ( $main_img_obj_w / 2 ) + ( $watermark_img_obj_w / 2 ) ); 
    $main_img_obj_min_y = floor( ( $main_img_obj_h / 2 ) - ( $watermark_img_obj_h / 2 ) ); 
    $main_img_obj_max_y = ceil( ( $main_img_obj_h / 2 ) + ( $watermark_img_obj_h / 2 ) ); 
    
    # create new image to hold merged changes 
    $return_img = imagecreatetruecolor( $main_img_obj_w, $main_img_obj_h ); 
    # walk through main image 
    for( $y = 0; $y < $main_img_obj_h; $y++ ) { 
      for( $x = 0; $x < $main_img_obj_w; $x++ ) { 
        $return_color = NULL; 
        # determine the correct pixel location within our watermark 
        $watermark_x = $x - $main_img_obj_min_x; 
        $watermark_y = $y - $main_img_obj_min_y; 
        
        # fetch color information for both of our images 
        $main_rgb = imagecolorsforindex( $main_img_obj, imagecolorat( $main_img_obj, $x, $y ) ); 
        
        # if our watermark has a non-transparent value at this pixel intersection 
        # and we're still within the bounds of the watermark image 
        
        if ( $watermark_x >= 0 && $watermark_x < $watermark_img_obj_w && 
          $watermark_y >= 0 && $watermark_y < $watermark_img_obj_h ) { 
          $watermark_rbg = imagecolorsforindex( $watermark_img_obj, imagecolorat( $watermark_img_obj, $watermark_x, $watermark_y ) ); 
          
          # using image alpha, and user specified alpha, calculate average 
          $watermark_alpha = round( ( ( 127 - $watermark_rbg['alpha'] ) / 127 ), 2 ); 
          $watermark_alpha = $watermark_alpha * $alpha_level; 
          
          # calculate the color 'average' between the two - taking into account the specified alpha level 
          $avg_red = $this->_get_ave_color( $main_rgb['red'], $watermark_rbg['red'], $watermark_alpha ); 
          $avg_green = $this->_get_ave_color( $main_rgb['green'], $watermark_rbg['green'], $watermark_alpha ); 
          $avg_blue = $this->_get_ave_color( $main_rgb['blue'], $watermark_rbg['blue'], $watermark_alpha ); 
          
          # calculate a color index value using the average RGB values we've determined 
          $return_color = $this->_get_image_color( $return_img, $avg_red, $avg_green, $avg_blue ); 
          # if we're not dealing with an average color here, then let's just copy over the main color 
        }
        else{ 
          $return_color = imagecolorat( $main_img_obj, $x, $y ); 
        } # END if watermark 
        # draw the appropriate color onto the return image 
        imagesetpixel( $return_img, $x, $y, $return_color ); 
      } # END for each X pixel 
    } # END for each Y pixel 
    # return the resulting, watermarked image for display 
    return $return_img; 
  } # END create_watermark() 
  
  # average two colors given an alpha 
  function _get_ave_color( $color_a, $color_b, $alpha_level ) { 
    return round( ( ( $color_a * ( 1 - $alpha_level ) ) + ( $color_b * $alpha_level ) ) ); 
  } # END _get_ave_color() 
  
  # return closest pallette-color match for RGB values 
  function _get_image_color($im, $r, $g, $b) { 
    $c=imagecolorexact($im, $r, $g, $b); 
    if ($c!=-1) return $c; 
    $c=imagecolorallocate($im, $r, $g, $b); 
    if ($c!=-1) return $c; 
    return imagecolorclosest($im, $r, $g, $b); 
  } # EBD _get_image_color() 
} # END watermark API
?>
