<?php
/**
 * 
 * Example for testing a model of content "tags".
 * 
 * @category Solar
 * 
 * @package Solar_Example
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Tags.php 3617 2009-02-16 19:47:30Z pmjones $
 * 
 */
class Solar_Example_Model_Tags extends Solar_Sql_Model
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
        
        $this->_model_name = 'tags';
        
        $this->_hasMany('taggings');
        
        $this->_hasMany('nodes', array(
            'through' => 'taggings',
        ));
        
        $this->_index = array(
            'name' => 'unique',
        );
    }
}