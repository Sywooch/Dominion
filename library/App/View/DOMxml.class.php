<?php

define('DOMXML_ENCODING_DEFAULT', "UTF-8");
define('DOMXML_CREATE_AND_STAY', 0);
define('DOMXML_CREATE_AND_GO_INSIDE', 1);
define('DOMXML_CREATE_AND_GO_INSIDE_DEPRECATED', 2);
define('DOMXML_CREATE_AND_IMPORT_NODE', 4);

/*
 * Абстрактный класс для описания элементов
 * необходимых для использывания в классе DomXML и в стратегиях создания узлов
 * документа XML
 */

abstract class DomXMLTemplate {

//   protected $xml;
//   protected $tag;
//   protected $root;
//   protected $encoding;
//   protected $Dom;

    public $xml;
    public $tag;
    public $root;
    public $encoding;
    public $Dom;

    public function __construct(&$Dom) {
        $this->Dom = $Dom;
        if (!$this->Dom->root)
            $this->Dom->root = $this->Dom->xml;
    }

    /**
     * Метод добавления атрибутов в узел документа XML
     * Будет одинаковый как для основного класса так и для стратегий
     */
    protected function AddAttrInXMLNode(&$Dom, $attribute) {
        if (empty($attribute) && !is_array($attribute))
            return false;
        try {
            foreach ($attribute as $key => $value) {
                $Dom->root->setAttribute($key, $this->stringConvert($value, $Dom->encoding));
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Метод добавляет текстовый узел в виде cdata к текущему узлу каталога.
     * @param string $tag_name - имя тега куда помещаем данные CDATA
     * @param string $cdata - содержимое CDATA
     * @return void
     */
    protected function AddCdataSection($tag_name, $cdata) {
        if (empty($cdata))
            return false;
        try {
            $element = $this->Dom->root->appendChild(new DOMElement($tag_name));
            $element->appendChild(new DOMCdataSection($cdata));

//            $Dom->root->appendChild($this->Dom->xml->createCDATASection($cdata));
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * возвращает строку в кодировке ENCODING_DEFAULT
     * @param $str
     * @return string
     */
    protected function stringConvert($xml) {
        try {
            if (!empty($encoding) && $encoding != DOMXML_ENCODING_DEFAULT)
                $xml = iconv($encoding, DOMXML_ENCODING_DEFAULT, $xml);
            return $xml;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}

/*
 * Интерфейс, определяющий стратегии.
 */
interface DOMCreateStrategy {

    function MakeElementInXML($tag_name, $xml = "", $attribute = array(), $cdata = 0);
}

/*
 * Стратегия номер 1
 * Создаёт узел XML (один) и если надо атрибуты в нём.
 * При этом курсор на создаваемый узел не устанавливаем.
 */

class CreateElementNotSetCursor extends DomXMLTemplate implements DOMCreateStrategy {

    function MakeElementInXML($tag_name, $xml = "", $attribute = array(), $cdata = 0) {
        try {
            if (empty($cdata))
                $this->Dom->root->appendChild($this->Dom->xml->createElement($tag_name, $xml));
            else
                $this->AddCdataSection($tag_name, $xml);

            if (!empty($attribute))
                $this->AddAttrInXMLNode($this->Dom, $attribute);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}

/*
 * Стратегия номер 2
 * Создаёт узел XML (один) в текущем root'e и если надо атрибуты в нём.
 * При этом курсор устанавливаем на создаваемый узел.
 */

class CreateElementInsideTree extends DomXMLTemplate implements DOMCreateStrategy {

    function MakeElementInXML($tag_name, $xml = "", $attribute = array(), $cdata = 0) {
        try {
            if (empty($cdata))
                $this->Dom->root = &$this->Dom->root->appendChild($this->Dom->xml->createElement($tag_name, $xml));
            else
                $this->AddCdataSection($tag_name, $xml);

            if (!empty($attribute))
                $this->AddAttrInXMLNode($this->Dom, $attribute);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}

class CreateElmentImportNode extends DomXMLTemplate implements DOMCreateStrategy {

    function MakeElementInXML($tag_name, $xml = "", $attribute = array(), $cdata = 0) {
        try {
            if ($cdata) {
                $this->AddCdataSection($tag_name, $xml);
//                $this->Dom->root->appendChild($this->Dom->xml->createElement($tag_name));
//                $this->AddCdataSection($xml);
            } else {
                $imp = new DOMImplementation;
                $dtd = $imp->createDocumentType('xsl:stylesheet', '', 'symbols.ent');
                $xmlPost = $imp->createDocument("", "", $dtd);
                $xmlPost->resolveExternals = true;
                $xmlPost->substituteEntities = true;
                $xmlPost->encoding = DOMXML_ENCODING_DEFAULT;
                $xmlPost->version = '1.0';
                $xmlPost->standalone = false;
                $xml_node = "<?xml version=\"1.0\" encoding=\"" . DOMXML_ENCODING_DEFAULT . "\"?><!DOCTYPE stylesheet SYSTEM \"symbols.ent\"><{$tag_name}>" . $xml . "</{$tag_name}>";
                $xmlPost->loadXML($xml_node);
                $node = $this->Dom->xml->importNode($xmlPost->documentElement, true);
                $this->Dom->root->appendChild($node);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}

class DomXML extends DomXMLTemplate {

    /**
     * Конструктор класса DomXML. Инициализирует DomDocument.
     * @param $version
     * @param $encod
     * @return unknown_type
     */
    public function __construct($version = '1.0', $encod = 'utf-8', $dtd = "xsl") {
        try {
            $imp = new DOMImplementation;


            if ($dtd == 'xsl') {
                $dtd = $imp->createDocumentType('xsl:stylesheet', '', 'symbols.ent');
                $this->xml = $imp->createDocument("", "", $dtd);
            } elseif (is_array($dtd)) {
                $dtd = $imp->createDocumentType($dtd['qualifiedName'], $dtd['publicId'], $dtd['systemId']);
                $this->xml = $imp->createDocument("", "", $dtd);
            } elseif (empty($dtd)) {
                $this->xml = $imp->createDocument();
            }


//	    $dtd = $imp->createDocumentType('xsl:stylesheet', '', 'symbols.ent');
//		$this->xml = $imp->createDocument("", "", $dtd);
//	    $dtd = -1;
//	    switch ($dtd) {
//		case  : $this->xml = $imp->createDocument();
//		default : $dtd = $imp->createDocumentType('xsl:stylesheet', '', 'symbols.ent');
//		    $this->xml = $imp->createDocument("", "", $dtd);
//	    }


            $this->xml->encoding = $encod;
            $this->xml->version = $version;
            $this->xml->resolveExternals = true;
            $this->xml->substituteEntities = true;
            $this->encoding = $encod;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Метод возвращает XML в виде строки.
     * @return string
     */
    public function getXML() {
        try {
            return $this->xml->saveXML();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * вовращаем XML как объект
     * @return DOMDocument
     */
    public function getDOMxml() {
        return $this->xml;
    }

    /**
     * Метод сохраняет XML в файл $file.
     * @param $file
     * @return void
     */
    public function saveXML($file) {
        try {
            $this->xml->save($file);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function load_xml($file) {
        try {
            $this->xml->load($file);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Метод загружает XML из файла $file.
     * @param $file
     * @return void
     */
    public function loadXML($file) {
        $this->load_xml($file);
    }

    /**
     * Метод возвращает кодировку, в которой был создан DOMXml.
     * @return string
     */
    public function get_encoding() {
        try {
            return $this->encoding;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getEncoding() {
        return $this->get_encoding();
    }

    /**
     * Метод создаёт элемент XML-дерева. Перемещене указателя зависит от передаваемой стратегии.
     * @param $strategy
     * @param $tag_name
     * @param $xml
     * @param $attribute
     * @param $cdata
     * @return void
     */
    private function chooseStrategy($strategy, $tag_name, $xml, $attribute, $cdata) {
        try {
            $strategy->MakeElementInXML($tag_name, $xml, $attribute, $cdata);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Метод создает новый элемент XML-дерева. В зависимости от выбранного режима перемещает
     * или не перемещает указатель.
     * @param $tag_name
     * @param $xml
     * @param $mode
     * @param $attribute
     * @param $cdata
     * @return void
     */
    public function create_element($tag_name, $xml = "", $mode = DOMXML_CREATE_AND_STAY, $attribute = array(), $cdata = "") {
        try {
            $xml = $this->stringConvert($xml);
            switch ($mode) {
                case DOMXML_CREATE_AND_STAY: // создает элемент, не перемещая указатель
                    $strategy = new CreateElementNotSetCursor($this);
                    break;
                case DOMXML_CREATE_AND_GO_INSIDE: // создает элемент и перемещает на него указатель
                    $strategy = new CreateElementInsideTree($this);
                    break;
                case DOMXML_CREATE_AND_GO_INSIDE_DEPRECATED: // устаревший вариант создания элемента. Аналогичен DOMXML_CREATE_AND_GO_INSIDE
                    $strategy = new CreateElementInsideTree($this);
                    break;
                case DOMXML_CREATE_AND_IMPORT_NODE: // создает элемент, импортируя в него узел XML
                    $strategy = new CreateElmentImportNode($this);
                    break;
                default:
                    $strategy = new CreateElementNotSetCursor($this);
                    break;
            }
            $this->chooseStrategy($strategy, $tag_name, $xml, $attribute, $cdata);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Create XML Element
     * @param string $tag_name
     * @param text $xml
     * @param int $mode
     * @param array $attribute
     * @param text $cdata
     */
    public function createElement($tag_name, $xml = "", $mode = DOMXML_CREATE_AND_STAY, $attribute = array(), $cdata = "") {
        $this->create_element($tag_name, $xml, $mode, $attribute, $cdata);
    }

    public function createTextNode($text) {
        $node = $this->xml->createTextNode($text);

        $this->root->appendChild($node);
    }

    /**
     * Метод добавляет атрибуты к текущему узлу.
     * @param $attribute
     * @return void
     */
    public function set_attribute($attribute) {
        try {
            $this->AddAttrInXMLNode($this, $attribute);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function setAttribute($attribute) {
        $this->set_attribute($attribute);
    }

    /**
     * Метод возвращает значение атрибута текущего узла.
     * @param $name
     * @return mixed
     */
    public function get_attr_value($name) {
        try {
            if ($this->root->hasAttribute($name))
                return $this->root->getAttribute($name);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getAttrValue($name) {
        return $this->get_attr_value($name);
    }

    /**
     * возвращает текстовое значение узла и его потомков
     * @return mixed
     */
    public function get_tag_value() {
        try {
            return $this->root->textContent;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getTagValue() {
        return $this->get_tag_value();
    }

    public function evaluateXpath($query) {
        $xpath = new DOMXPath($this->xml);
        $eval = $xpath->evaluate($query);
        if ($eval)
            return $eval;
        else
            return false;
    }

    /**
     * устанавливает курсор на конкретный тэг по его имени, если $query = false, или по xpath - если true
     * @param $tag_name
     * @param $query
     * @param $position
     * @return DOMNode
     */
    public function set_tag($tag_name, $query = false) {
        try {
            if (!$query) {
                $items = $this->xml->getElementsByTagName($tag_name);
                $this->root = & $items->item(0);
            } else {
                $xpath = new DOMXPath($this->xml);
                if ($xpath->evaluate($tag_name)) {
                    $items = $xpath->query($tag_name);
                    $this->root = $items->item(0);
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $g = $this->root;
//	$gg = $g->item(0);


        return $g;
    }

    public function setTag($tag_name, $query = false) {
        return $this->set_tag($tag_name, $query);
    }

    /**
     * Метод возвращает значение элемента по указанному имени или пути xpath.
     * @return string
     */
    public function get_element_value($tag_name, $is_xpath = false) {
        try {
            if (!$is_xpath) {
                $items = $this->xml->getElementsByTagName($tag_name);
                $value = $items->item(0)->nodeValue;
            } else {
                $xpath = new DOMXPath($this->xml);
                if ($xpath->evaluate($tag_name)) {
                    $r = $xpath->query($tag_name);
                    $value = $r->item(0)->nodeValue;
                }
            }
            return $value;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getElementValue($tag_name, $is_xpath = false) {
        return $this->get_element_value($tag_name, $is_xpath);
    }

    public function appendXML(DOMDocument $xml) {
        if (empty($xml->documentElement))
            return false;
        $node = $this->xml->importNode($xml->documentElement, true);
        $this->root->appendChild($node);
    }

    /**
     * Метод импортирует в XML-дерево текстовый узел.
     * @param $xml_node
     * @param $cdata
     * @return void
     */
    public function import_node($xml_node, $cdata = false) {
        try {
            if ($cdata) {
                $xml_node = $this->stringConvert($xml_node);
                $cdata = $this->root->ownerDocument->createCDATASection($xml_node);
                $this->root->appendChild($cdata);
            } else {
                $imp = new DOMImplementation;
                $dtd = $imp->createDocumentType('xsl:stylesheet', '', 'symbols.ent');
                $xmlPost = $imp->createDocument("", "", $dtd);
                $xmlPost->resolveExternals = true;
                $xmlPost->substituteEntities = true;
                $xmlPost->encoding = $this->encoding;
                $xmlPost->version = '1.0';
                $xmlPost->standalone = false;

                $xmlPost->loadXML($xml_node);
                $node = $this->xml->importNode($xmlPost->documentElement, true);
                $this->root->appendChild($node);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Метод для перемещения по XML-дереву к узлу $name_tag.
     * @param $name_tag
     * @return void
     */
    public function go_inside_tree($name_tag) {
        try {
            if (!$this->root->hasChildNodes())
                return false;

            $child = &$this->root[0]->firstChild;

            while ($child) {
                if ($name_tag == $child->nodeName) {
                    $this->root[0] = $child;
                    break;
                }
                $child = & $child->nextSibling;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function goInsideTree($name_tag) {
        $this->go_inside_tree($name_tag);
    }

    /**
     * Перемещает указатель на родителя текущего узла.
     * @return void
     */
    public function go_to_parent() {
        try {
            $this->root = &$this->root->parentNode;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function goToParent() {
        $this->go_to_parent();
    }

    /**
     * Метод клонирует узел (с атрибутами и потомками), внутри которого находится указатель.
     * @return void
     */
    public function clone_node() {
        try {
            $elem = $this->root->cloneNode(true);
            $this->go_to_parent();
            $this->root->appendChild($elem);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function cloneNode() {
        $this->clone_node();
    }

    public function removeChildeNode(DOMNode $child) {
//	$g = $child->item(0);

        $this->root->removeChild($child);
    }

    /**
     * Метод удаляет все дочерние узлы текущего узла.
     * @return unknown_type
     */
    public function clear_child_nodes() {
        try {
            $children = $this->root->childNodes;
            while (true) {
                if (isset($children->item(0)->nodeName))
                    $this->root->removeChild($children->item(0)); // каждый раз удаляем именно 0-й элемент, т.к. после удаления всё дерево смещается на 1
                else
                    break;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function clearChildNodes() {
        $this->clear_child_nodes();
    }

    /**
     * Метод преобразует массив $data в узлы XML-дерева. Если указан массив $arrAttributeName - то
     * элементы массива $data, пересекающиеся с элементами массива $arrAttributeName, будут
     * созданы как атрибуты узла $tagName.
     * Если указан $parentTag - то все узлы с именем $tagName будут находиться внутри узла $parentTag.
     * @param array $tagName
     * @param $data
     * @param $arrAttributeName
     * @param $parentTag
     * @return unknown_type
     */
    public function arrayToXML($tagName, array $data, $arrAttributeName = array(), $parentTag = '', $images = array()) {
        try {
            if ($parentTag) {
                $makeParent = new CreateElementInsideTree($this);
                $makeParent->MakeElementInXML($parentTag);
                unset($makeParent);
            }
            foreach ($data as $itemList) {
                $make = new CreateElementInsideTree($this);
                $make->MakeElementInXML($tagName);
                foreach ($itemList as $tag => $value) {
                    if (is_array($value)) {
                        $this->arrayToXML($tag, $value);
//                                    $this->goToParent();
                    } else {
                        $tag = mb_strtolower($tag, 'utf-8');
                        if (in_array($tag, $arrAttributeName)) // если тэг должен быть атрибутом
                            $this->setAttribute(array($tag => $value));
                        else { //	создаем обычный узел
                            if (in_array($tag, $images)) {   // если тэг должен быть картинкой
                                $makeChild = new CreateElementInsideTree($this);
                                if ($value != '' && strchr($value, "#")) {
                                    $image = split("#", $value);
                                    $attributes = array('src' => $image[0],
                                        'w' => $image[1],
                                        'h' => $image[2]);
                                }
                                $makeChild->MakeElementInXML($tag, '', $attributes);
                                $this->goToParent();
                            } else {
                                $makeChild = new CreateElementNotSetCursor($this);
                                $makeChild->MakeElementInXML($tag, $value);
                            }
                        }
                    }
                }
                $this->goToParent();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}