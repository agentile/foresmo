<?php
/**
 * 
 * Helper for static-text pseudo-element.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: FormStatic.php 3366 2008-08-26 01:36:49Z pmjones $
 * 
 */
class Solar_View_Helper_FormStatic extends Solar_View_Helper_FormElement
{
    /**
     * 
     * A pseudo-element that inserts escaped text into a form, but not as an
     * element.  No hidden element is produced, either, so it doesn't get
     * submitted back to the server.
     * 
     * @param array $info An array of element information.
     * 
     * @return string The element XHTML.
     * 
     */     
    public function formStatic($info)
    {
        $this->_prepare($info);
        return $this->_view->escape($this->_value);
    }
}
