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
     * fetchEnablesModules
     *
     * fetch modules that are enabled
     *
     * @return array
     */
    public function fetchEnabledModules()
    {
        $where = array('status = ?' => 1);
        $results = $this->fetchAllAsArray(
            array(
                'where' => $where,
                'eager' => 'moduleinfo'
            )
        );
        return $results;
    }

    /**
     * registerModule
     * make an entry for the module if doesn't already exists
     */
    public function registerModule($name, $class, $description)
    {
        $module = $this->fetchModuleInfoByName($name);
        if (empty($module)) {
            $data = array(
                'name' => $name,
                'class_suffix' => $class,
                'description' => $description,
                'status' => 2,
                'position' => 0,
            );
            return $this->insert($data);
        }
        return false;
    }

    /**
     * fetchModules
     *
     * fetch all modules
     *
     * @return array
     */
    public function fetchModules()
    {
        $results = $this->fetchAllAsArray(
            array(
                'cache' => false,
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
        $enabled_modules = $this->fetchEnabledModules();
        foreach ($enabled_modules as $enabled_module) {
            if (strtolower($enabled_module['class_suffix']) == strtolower($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * enabledModuleByID
     *
     * enable a module that is disabled
     *
     */
    public function enableModuleByID($id)
    {
        $data = array(
            'status' => 1,
        );
        $where = array(
            'id = ? AND status = ?' => array((int) $id, 0),
        );
        $this->update($data, $where);
    }

    /**
     * disableModuleByID
     *
     * disable a module that is enabled
     *
     */
    public function disableModuleByID($id)
    {
        $data = array(
            'status' => 0,
        );
        $where = array(
            'id = ? AND status = ?' => array((int) $id, 1),
        );
        $this->update($data, $where);
    }

    /**
     * fetchModuleInfoByName
     *
     * fetch Module Info Rows by Module Name
     *
     * @param string $name module name
     * @return array
     */
    public function fetchModuleInfoByName($name)
    {
        $results = $this->fetchOneAsArray(
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

    /**
     * fetchModuleByID
     *
     * fetch Module by ID
     *
     * @param string $name module name
     * @return array
     */
    public function fetchModuleInfoByID($id)
    {
        $results = $this->fetchOneAsArray(
            array(
                'where' => array(
                    'id = ?' => array((int) $id),
                ),
                'eager' => array(
                    'moduleinfo'
                )
            )
        );

        return $results;
    }
}
