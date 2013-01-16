<?php

class Config {

  /**
   * Get a config object
   * @param string $url
   * @return \Report\iConfig 
   */
  static public function getConfig($url) {
    $path_parts = pathinfo($url);

    switch ($path_parts['extension']) {
      case 'xml':
        $config = new XmlConfig();
        break;

      case 'yml':
        $config = new YamlConfig();
        break;
    }
    $config->parse($url);
    return $config;
  }

}