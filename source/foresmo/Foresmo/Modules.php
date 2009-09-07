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
    public function getEnabledModulesData()
    {
        $results = $this->_model->modules->getEnabledModules();

        foreach ($results as $key => $result) {
            $results[$key]['output'] = $this->getModuleData($result['name']);
        }
        return $results;
    }

    /**
     * getModuleData
     *
     * @param string $name
     * @return string
     */
    public function getModuleData($name)
    {
        $module = Solar::factory("Foresmo_Modules_{$name}", $this->_model);
        $module->start();
        return $module->output;
    }

    /**
     * processRequest
     *
     * handle module request, and return output
     *
     * @param string $name module name
     * @param array $data request data: POST, GET, PARAMS(from url)
     *
     * @return mixed;
     */
    public function processRequest($name, $data)
    {
        $module = Solar::factory("Foresmo_Modules_{$name}", $this->_model);
        if (method_exists($module, 'request')) {
            try {
                $module->request($data);
                if (isset($module->output)) {
                    return $module->output;
                } else {
                    return null;
                }
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * processAjaxRequest
     *
     * handle module ajax request, and return output
     *
     * @param string $name module name
     * @param array $data request data: POST, GET, PARAMS(from url)
     *
     * @return mixed;
     */
    public function processAjaxRequest($name, $data)
    {
        $module = Solar::factory("Foresmo_Modules_{$name}", $this->_model);
        if (method_exists($module, 'ajaxRequest')) {
            try {
                $module->ajaxRequest($data);
                if (isset($module->output)) {
                    return $module->output;
                } else {
                    return null;
                }
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }
}
