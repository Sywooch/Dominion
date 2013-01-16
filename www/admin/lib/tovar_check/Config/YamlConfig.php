<?php

class YamlConfig extends AbstractConfig{
  
  public function __construct($url){
    $this->_url = $url;
  }
  
  public function parse(){
    if (file_exists($this->_url)) {
      return yaml_parse_file($this->_url);
    } else {
      throw new Exception('File not found');
    }    
  }
}