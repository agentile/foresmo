<?php
/**
 * 
 * Example for testing a "test_solar_foo" model.
 * 
 * @category Solar
 * 
 * @package Solar_Example
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: TestSolarFoo.php 3153 2008-05-05 23:14:16Z pmjones $
 * 
 */
class Solar_Example_Model_TestSolarFoo extends Solar_Sql_Model
{
    /**
     * 
     * Model setup.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        $dir = str_replace('_', DIRECTORY_SEPARATOR, __CLASS__)
             . DIRECTORY_SEPARATOR
             . 'Setup'
             . DIRECTORY_SEPARATOR;
        
        $this->_table_name = Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
        
        $this->_addFilter('email', 'validateEmail');
        $this->_addFilter('uri', 'validateUri');
        
        $this->_index = array(
            'created',
            'updated',
            'email' => 'unique',
            'uri',
        );
    }
}