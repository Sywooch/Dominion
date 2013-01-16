<?php
#TODO Class Descritpion
/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';
/**
 * Helper to generate a "text" element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cvs_View_Helper_GridElement extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'text' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are used in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function GridElement($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
         $xhtml = '<table'
                . ' id="' . $this->view->escape($id) . '"'
                . $this->_htmlAttribs($attribs)
                . '>'
                . '</table>'
                . '<div id="'
                . $this->view->escape($id)
                . '_pager" class="scroll" style="text-align:center" mce_style="text-align:center"'
                . '>'
                . '</div>';
        return $xhtml;
    }
}