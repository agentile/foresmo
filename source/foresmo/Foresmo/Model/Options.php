<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_Options extends Solar_Sql_Model {

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
     * fetchOptionValue
     * Fetch value from option key
     *
     * @param string $key
     * @return mixed
     */
    public function fetchOptionValue($key)
    {
        $results = $this->fetchAllAsArray(
            array(
                'where' => array(
                    'name = ?' => $key
                )
            )
        );

        if (isset($results[0])) {
            return $results[0]['value'];
        }
        return null;
    }

    /**
     * fetchBlogOptions
     * Fetch blog options
     *
     * @return array
     */
    public function fetchBlogOptions()
    {
        return $this->fetchAllAsArray(array(
            'where' => array(
                'name LIKE ?' => 'blog_%'
                )
            )
        );
    }

    /**
     * fetchAllOptions
     *
     */
    public function fetchAllOptions()
    {
        return $this->fetchAllAsArray();
    }
}
