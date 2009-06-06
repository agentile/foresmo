<?php
/**
 * Foresmo_Modules
 *
 *
 *
 */
class Foresmo_Modules extends Solar_Base {

    protected $_model;

    /**
     * __construct
     *
     * @param $model
     */
    public function __construct($model)
    {
        $this->_model = $model;
    }

    /**
     * getEnabledModules
     *
     * @return array
     */
    public function getEnabledModules()
    {
        $where = array('enabled = ?' => 1);
        $results = $this->_model->modules->fetchAll(
            array(
                'where' => $where,
                'eager' => 'moduleinfo'
            )
        );

        foreach ($results as $key => $result) {
            $results[$key]->output = $this->getModuleData($result->name);
        }
        return $results;
    }

    /**
     * getModuleData
     *
     * @param $name
     * @return string
     */
    public function getModuleData($name)
    {
        $module = Solar::factory("Foresmo_Modules_{$name}", $this->_model);
        $module->start();
        return $module->output;
    }
}
