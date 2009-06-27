<?php
/**
 * 
 * Helper for meta tags.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 * @author Jeff Surgeson <solar@3hex.com>
 * 
 * @author Rodrigo Moraes <rodrigo.moraes@gmail.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Meta.php 3732 2009-04-29 17:27:56Z pmjones $
 * 
 */
class Solar_View_Helper_Meta extends Solar_View_Helper
{
    /**
     * 
     * Returns a <meta ... /> tag.
     * 
     * @param string $attribs The specification array, typically
     * with keys 'name' or 'http-equiv', and 'content'.
     * 
     * @return string The <meta ... /> tag.
     * 
     */
    public function meta($attribs)
    {
        return '<meta' . $this->_view->attribs($attribs) . ' />';
    }
}
