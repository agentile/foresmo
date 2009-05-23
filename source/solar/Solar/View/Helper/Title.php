<?php
/**
 * 
 * Helper for title tags.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Title.php 3732 2009-04-29 17:27:56Z pmjones $
 * 
 */
class Solar_View_Helper_Title extends Solar_View_Helper
{
    /**
     * 
     * Returns a <title ... /> tag.
     * 
     * @param string $text The title string.
     * 
     * @return string The <title ... /> tag.
     * 
     */
    public function title($text)
    {
        return '<title>' . $this->_view->escape($text) . '</title>';
    }
}
