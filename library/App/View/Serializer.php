<?php

class Serializer
{
    private $xmlDoc;
    
    public function __construct() {
        $this->xmlDoc = new DOMDocument();
    }
    
    public function Serialize($inst, $nodeName=null) {
        if(is_object($inst))
        {
            $nodeName = ($nodeName == null) ? get_class($inst) : $nodeName;
            $root = $this->xmlDoc->createElement($nodeName);
            $this->xmlDoc->appendChild($root);
            $this->SerializeObject($inst, $nodeName, $root);
        }  else if(is_array($inst)) {
            $nodeName = ($nodeName == null) ? get_class($inst) : $nodeName;
            $root = $this->xmlDoc->createElement($nodeName);
            $this->xmlDoc->appendChild($root);
            $this->SerializeArray($inst, $nodeName, $root);
        }

        return $this->xmlDoc;
    }
    
    private function SerializeObject($inst, $nodeName, $parent) {
        $obj = new ReflectionObject($inst);
        $properties = $obj->getProperties();
        
        foreach($properties as $prop) {
            if(!$prop->isPrivate()) {
                $elem = $this->SerializeData($prop->getName(), $prop->getValue($inst), $parent);
            }
        }
    }
    
    private function SerializeArray($array, $nodeName, $parent) {
        foreach($array as $key => $val) {
            $keyStr = (is_numeric($key)) ? 'ArrayValue' : $key;
            $elem = $this->SerializeData($keyStr, $val, $parent);
            
            if(is_numeric($key)) {
                $elem->setAttribute('index', $key);
            }
        }
    }
    
    private function SerializeData($key, $val, $parent) {
        if(is_object($val)) {
            $propNodeName = get_class($val);
            $elem = $this->xmlDoc->createElement($propNodeName);
            $parent->appendChild($elem);                    
            $this->SerializeObject($val, $propNodeName, $parent);
            $elem->setAttribute('type', 'object');
        } else if(is_array($val)) {
            $elem = $this->xmlDoc->createElement($key);
            $parent->appendChild($elem);
            $this->SerializeArray($val, $key, $elem);
            $elem->setAttribute('type', 'array');
        } else {
            $elem = $this->xmlDoc->createElement($key, $val);
            $parent->appendChild($elem);
            $elem->setAttribute('type', 'property');
        }
        
        return $elem;
    }
}
