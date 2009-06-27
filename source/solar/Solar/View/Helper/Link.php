<?php
/**
 * 
 * Helper for links.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Link.php 3732 2009-04-29 17:27:56Z pmjones $
 * 
 */
class Solar_View_Helper_Link extends Solar_View_Helper
{
    /**
     * 
     * Returns a <link ... /> tag.
     * 
     * @param string $attribs The specification array, typically
     * with keys 'rel' and 'href'.
     * 
     * @return string The <link ... /> tag.
     * 
     */
    public function link($attribs)
    {
        return '<link' . $this->_view->attribs($attribs) . ' />';
    }
}
