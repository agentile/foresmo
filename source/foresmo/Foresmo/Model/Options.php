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
        $results = $this->fetchOneAsArray(
            array(
                'where' => array(
                    'name = ?' => $key
                )
            )
        );

        if (isset($results['value'])) {
            return $results['value'];
        }
        return null;
    }

    /**
     * updateOption
     * udate blog option
     *
     */
    public function updateOption($name, $value)
    {
        $data = array(
            'value' => $value
        );
        $where = array(
            'name = ?' => $name
        );
        $this->update($data, $where);
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
    public function fetchAllOptions($cache = true)
    {
        return $this->fetchAllAsArray(array('cache'=>$cache));
    }

    /**
     * updateTheme
     *
     * @param string $theme_name theme name
     * @param bool $admin admin theme or blog theme?
     * @return void
     */
    public function updateTheme($theme_name, $admin = false)
    {
        $data = array(
            'value' => $theme_name,
        );
        if ($admin) {
            $where = array(
                'name = ?' => array('blog_admin_theme'),
            );
        } else {
            $where = array(
                'name = ?' => array('blog_theme'),
            );
        }
        $this->update($data, $where);
    }
}
