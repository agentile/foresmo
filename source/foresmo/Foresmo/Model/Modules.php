<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_Modules extends Solar_Sql_Model {

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
        $this->_hasMany('moduleinfo', array(
            'foreign_class' => 'Foresmo_Model_ModuleInfo',
            'foreign_key' => 'module_id',
        ));
    }

    /**
     * getEnablesModules
     *
     * get modules that are enabled
     *
     * @return array
     */
    public function getEnabledModules()
    {
        $where = array('enabled = ?' => 1);
        $results = $this->fetchAllAsArray(
            array(
                'where' => $where,
                'eager' => 'moduleinfo'
            )
        );
        return $results;
    }

    /**
     * isEnabled
     *
     * is module enabled?
     *
     * @param string $name module name
     * @return bool
     */
    public function isEnabled($name)
    {
        $enabled_modules = $this->getEnabledModules();
        foreach ($enabled_modules as $enabled_module) {
            if (strtolower($enabled_module['name']) == strtolower($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * getModuleInfoByName
     *
     * get Module Info Rows by Module Name
     *
     * @param string $name module name
     * @return array
     */
    public function getModuleInfoByName($name)
    {
        $results = $this->fetchAllAsArray(
            array(
                'where' => array(
                    'name = ?' => array($name),
                ),
                'eager' => array(
                    'moduleinfo'
                )
            )
        );

        return $results;
    }
}
