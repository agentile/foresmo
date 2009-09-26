<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_Links extends Solar_Sql_Model {

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

        $this->_table_name = $this->_config['prefix'] . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
    }

    /**
     * fetchLinks
     * fetch all links
     *
     * @return array
     */
    public function fetchLinks()
    {
        return $this->fetchAllAsArray();
    }
}
