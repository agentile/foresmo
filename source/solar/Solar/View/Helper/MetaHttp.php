<?php
/**
 * 
 * Helper for meta http-equiv tags.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: MetaHttp.php 3732 2009-04-29 17:27:56Z pmjones $
 * 
 */
class Solar_View_Helper_MetaHttp extends Solar_View_Helper
{
    /**
     * 
     * Returns a <meta http-equiv="" content="" /> tag.
     * 
     * @param string $http_equiv The http-equiv type.
     * 
     * @param string $content The content value.
     * 
     * @return string The <meta http-equiv="" content="" /> tag.
     * 
     */
    public function metaHttp($http_equiv, $content)
    {
        $spec = array(
            'http-equiv' => $http_equiv,
            'content' => $content,
        );
        return '<meta' . $this->_view->attribs($spec) . ' />';
    }
}
