<?php
/**
 * 
 * Helper for a 'date' pseudo-element.
 * 
 * For an element named 'foo[bar]', builds a series of selects:
 * 
 * - foo[bar][Y] : +/- 4 years from selected value; if none, current year +/- 4
 * - foo[bar][m] : 01-12
 * - foo[bar][d] : 01-31
 * 
 * @category Solar
 * 
 * @package Solar_View_Helper_Form
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: FormDate.php 3366 2008-08-26 01:36:49Z pmjones $
 * 
 */
class Solar_View_Helper_FormDate extends Solar_View_Helper_FormTimestamp
{
    /**
     * 
     * Helper for a 'date' pseudo-element.
     * 
     * For an element named 'foo[bar]', returns a series of selects:
     * 
     * - foo[bar][Y] : +/- 4 years from selected value; if none, current year +/- 4
     * - foo[bar][m] : 01-12
     * - foo[bar][d] : 01-31
     * 
     * @param array $info An array of element information.
     * 
     * @return string The element XHTML.
     * 
     */
    public function formDate($info)
    {
        $this->_prepare($info);
        return $this->_selectYear()  . '-'
             . $this->_selectMonth() . '-'
             . $this->_selectDay();
    }
}
