<?php
/**
 * 
 * Helper for <head> elements, with specific additions to help jQuery scripts.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Head.php 3153 2008-05-05 23:14:16Z pmjones $
 * 
 */
class Solar_App_Base_Helper_Head extends Solar_View_Helper_Head
{
    /**
     * 
     * Returns all inline scripts wrapped in a jQuery "$document.ready()"
     * call.
     * 
     * @return string The code for all inline scripts.
     * 
     */
    protected function _fetchScriptInline()
    {
        $code = parent::_fetchScriptInline();
        if ($code) {
            $code = str_replace("\n", "\n{$this->_indent}", $code);
            $code = "\$(document).ready(function() {\n{$this->_indent}$code\n});";
        }
        return $code;
    }
}
