<?php
/**
 * User: Ruslan
 * Date: 08.07.13
 * Time: 19:02
 */

class ImageParams
{

    private $name;

    private $width;

    private $height;


    public function __construct($name, $width, $height)
    {
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }
}