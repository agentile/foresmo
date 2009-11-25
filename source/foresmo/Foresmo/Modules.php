<?php
/**
 * Foresmo_Modules
 *
 *
 *
 */
class Foresmo_Modules extends Solar_Base {

    protected $_Foresmo_Modules = array('model' => null);
    protected $_model;
    public $web_root;

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_model = $this->_config['model'];
        $this->web_root = Solar::$system . '/docroot/';
    }

    /**
     * getEnabledModules
     *
     * @return array
     */
    public function getEnabledModulesData()
    {
        $results = $this->_model->modules->fetchEnabledModules();

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
        $this->loadModule($name);
        $module = Solar::factory("Foresmo_Modules_{$name}", array('model' => $this->_model));
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
        $this->loadModule($name);
        $module = Solar::factory("Foresmo_Modules_{$name}", array('model' => $this->_model));
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
        $this->loadModule($name);
        $module = Solar::factory("Foresmo_Modules_{$name}", array('model' => $this->_model));
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

    /**
     * getRegisteredHooks
     * Get registered hooks from enabled modules
     *
     * @return array
     */
    public function getRegisteredHooks()
    {
        $hooks = array();
        $enabled_modules = $this->_model->modules->fetchEnabledModules();
        foreach ($enabled_modules as $module) {
            $name = ucfirst(strtolower($module['name']));
            $this->loadModule($name);
            $module_obj = Solar::factory("Foresmo_Modules_{$name}", array('model' => $this->_model));
            if (isset($module_obj->register) && is_array($module_obj->register)) {
                $hooks[$name] = $module_obj->register;
            }
            $module_obj = null;
        }

        return $hooks;
    }

    public function loadModule($name)
    {
        try {
            require_once $this->web_root . 'modules/' . $name . '.php';
        } catch (Exception $e) {
            return false;
        }
    }
}
