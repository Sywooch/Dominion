<?
class antiscripting
{
        var $width  = 210;                              // image height
        var $height = 50;                               // image width

        var $transparent = 1;                           // transparency
        var $interlace = false;
        var $msg  = "";                                 // text to display
        var $font = array("fonts/FEEDBI__.TTF",
                               "fonts/frizzed.ttf"
                               );    // full path to your font

        var $size = 20;                                 // font size
        var $font_width =35;

        var $rotation = 30;                             // font rotation.

        var $pad_x = 10;                                // padding x
        var $pad_y = 9;                                 // padding y

        var $fg_r = 145;                                        // text color RGB - red
        var $fg_g = 23;                                         // text color RGB - green
        var $fg_b = 200;                                        // text color RGB - blue


        var $bg_r = 255;                                // background color RGB - red
        var $bg_g = 255;                                // background color RGB - green
        var $bg_b = 255;                               // background color RGB - blue

        function antiscripting()
        {
                        //global $V_G_FONTS;
                        //$this->font = $V_G_FONTS; // фонты прописываем в глобальных настройках
        }
        
        function drawImage()
        {
                                
                $image = '';

                $image = ImageCreate($this->width+($this->pad_x*2),$this->height+($this->pad_y*2));

                // Allocate background color
                $bg = ImageColorAllocate($image, $this->bg_r, $this->bg_g, $this->bg_b);

                // Allocate text color
                $fg = ImageColorAllocate($image, $this->fg_r, $this->fg_g, $this->fg_b);

                if ($this->transparent)
                        ImageColorTransparent($image, $bg);

                ImageInterlace($image, $this->interlace);

                $count= strlen( $this->msg);

                for ($i=0; $i<$count; $i++){
                     $t = substr($this->msg,$i,1);
                     $rrr =  $this->pad_x+($i*$this->font_width);
                     $font = shuffle($this->font);
                     $font= $this->font[1];
                     $rotation = rand ( -$this->rotation , $this->rotation);
                     $dc = ImageColorAllocate($image, rand(0,255), rand(0,100), rand(0,255));
             ImageTTFText($image, $this->size, $rotation, $this->pad_x+($i*$this->font_width), $this->pad_y+20, $dc, $font, $t);
                }

                //ImageTTFText($image, $this->size, $this->rotation, $this->pad_x, $this->pad_y, $fg, $this->font, $this->msg);

                // Image distortion

                // Alocate distortion color
                $dc = ImageColorAllocate($image, rand(0,255), rand(0,255), rand(0,255));

                // Draw eclipse
                ImageArc($image, rand(0, $this->width ), rand(0, $this->height ), rand($this->width / 2, $this->width) ,rand($this->height / 2, $this->height), 0,360, $dc);

                // Alocate distortion color
                $dc = ImageColorAllocate($image, rand(0,255), rand(0,255), rand(0,255));

                // Draw rectangle
                ImageRectangle($image, rand(0, $this->width/2 ), rand(0, $this->height/2 ), rand($this->width / 2, $this->width) ,rand($this->height / 2, $this->height), $dc);

                // Draw dots at random position
                $dots = $this->width * $this->height / 10;
                for($i=0;$i<$dots;$i++)
                {
                        // Alocate dot color
                        $dc = ImageColorAllocate($image, rand(0,255), rand(0,255), rand(0,255));

                        // Draw dot
                        ImageSetPixel($image, rand(0,$this->width), rand(0,$this->height), $dc);
                }

                // Create image
                ImagePNG($image);
        }
}


