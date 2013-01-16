<?php
class App_View_Helper_Navigation_Breadcrumbs
    extends Zend_View_Helper_Navigation_Breadcrumbs
{
    public function renderStraight(Zend_Navigation_Container $container = null)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }                
        // find deepest active
        if (!$active = $this->findActive($container)) {
            return '';
        }
        $active = $active['page'];
        // put the deepest active page last in breadcrumbs
        if ($this->getLinkLast()) {
            $html = $this->htmlify($active);
        } else {
            $html = $active->getLabel();
            if ($this->getUseTranslator() && $t = $this->getTranslator()) {
                $html = $t->translate($html);
            }
            $html = '<li><span>' . $this->view->escape($html) . '</span></li>';
        }

        // walk back to root
        while ($parent = $active->getParent()) {
            if ($parent instanceof Zend_Navigation_Page) {
                // prepend crumb to html
                $html = '<li>' . $this->htmlify($parent) . '</li>'
                      . ''
                      . $html;
            }
            if ($parent === $container) {
                // at the root of the given container
                break;
            }
            $active = $parent;
        }
        return strlen($html) ? $this->getIndent() . $html : '';
    }
}