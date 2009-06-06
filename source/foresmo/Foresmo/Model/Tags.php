<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_Tags extends Solar_Sql_Model {

    /**
     *
     * Model-specific setup.
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

        $adapter = Solar_Config::get('Solar_Sql', 'adapter');
        $this->_table_name = Solar_Config::get($adapter, 'prefix') . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
    }
}
