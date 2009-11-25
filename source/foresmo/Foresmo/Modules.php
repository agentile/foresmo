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
        $this->web_root = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : Solar::$system . '/docroot/';
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
            $results[$key]['output'] = $this->getModuleData($result['class_suffix']);
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

    public function scanForModules()
    {
        $invisible = array('.', '..', '.htaccess', '.htpasswd', '.svn');
        $exts = array('.php', '.php4', '.php5', '.phps', '.inc');

        $dir = $this->web_root . 'modules/';

        $dir_content = scandir($dir);
        foreach ($dir_content as $key => $content) {
            $path = $dir . $content;
            if (!in_array($content, $invisible)) {
                $file_ext = strtolower(substr($path, strrpos($path, '.')));
                if (is_file($path) && is_readable($path) && in_array($file_ext, $exts)) {
                    $name = substr($content, 0, strlen($content) - strlen($file_ext));
                    $this->loadModule($name);
                    $this->registerModule($name);
                }
            }
        }
    }

    public function registerModule($name)
    {
        $module_obj = Solar::factory("Foresmo_Modules_{$name}", array('model' => $this->_model));
        if (!isset($module_obj->name)) {
            return false;
        }
        $description =  (isset($module_obj->description)) ? $module_obj->description : '';

        $this->_model->modules->registerModule($module_obj->name, $name, $description);
        return true;
    }

    public function installModuleByID($module_id)
    {
        $module = $this->_model->modules->fetchModuleInfoByID($module_id);
        $this->loadModule($module['class_suffix']);
        $module_obj = Solar::factory("Foresmo_Modules_{$module['class_suffix']}", array('model' => $this->_model));
        // call the modules install method
        if (method_exists($module, 'install')) {
            $module_obj->install();
        }
        // set the module to enabled
        $data = array(
            'status' => 1,
        );
        $where = array(
            'id = ?' => array((int) $module_id),
        );
        $this->_model->modules->update($data, $where);
    }

    public function uninstallModuleByID($module_id)
    {
        $module = $this->_model->modules->fetchModuleInfoByID($module_id);
        $this->loadModule($module['class_suffix']);
        $module_obj = Solar::factory("Foresmo_Modules_{$module['class_suffix']}", array('model' => $this->_model));
        // call the modules uninstall method
        if (method_exists($module, 'uninstall')) {
            $module_obj->uninstall();
        }
        // set the module to not installed
        $data = array(
            'status' => 2,
        );
        $where = array(
            'id = ?' => array((int) $module_id),
        );
        $this->_model->modules->update($data, $where);
        // delete all module_info
        $where = array(
            'module_id = ?' => array((int) $module_id),
        );
        $this->_model->module_info->delete($where);
    }
}
