<?php
/**
 * Foresmo_Modules
 * This class handles dealing with modules and hooks.
 *
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class Foresmo_Modules extends Foresmo_Modules_Base {

    protected $_Foresmo_Modules = array();

    private static $_hooks = array();
    private $_module_store = array();

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
    }

    /**
     * getEnabledModules
     *
     * @return object
     */
    public function getEnabledModulesData()
    {
        $obj = new StdClass();
        $results = $this->_model->modules->fetchEnabledModules();
        foreach ($results as $key => $result) {
            $name = $result['class_suffix'];
            $obj->$name = $this->getModuleView($name);
            $results[$key]['output'] = (isset($obj->$name->output)) ? $obj->$name->output : '';
        }
        $obj->all = $results;
        return $obj;
    }

    /**
     * getModuleView
     *
     * @param string $name
     * @return object
     */
    public function getModuleView($name)
    {
        $module = $this->loadModule($name);
        if (method_exists($module, 'start')) {
            $module->start();
        }
        if (isset($module->output)) {
            Foresmo::escape($module->output);
            $module->_view->output = $module->output;
        }
        return $module->_view;
    }

    /**
     * getModuleOutput
     *
     * @param string $name
     * @return string
     */
    public function getModuleOutput($name)
    {
        $module = $this->loadModule($name);
        if (method_exists($module, 'start')) {
            $module->start();
        }
        Foresmo::escape($module->output);
        return $module->output;
    }

    /**
     * fetchAdminContent
     *
     * @param string $name
     * @return string
     */
    public function fetchAdminContent($name, $data)
    {
        $module = $this->loadModule($name);
        if (method_exists($module, 'admin')) {
            $module->admin($data);
        }
        Foresmo::escape($module->output);
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
        $module = $this->loadModule($name);
        if (method_exists($module, 'request')) {
            try {
                $module->request($data);
                if (isset($module->output)) {
                    Foresmo::escape($module->output);
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
     * processAdminRequest
     *
     * handle module admin request, and return output
     *
     * @param string $name module name
     * @param array $data request data: POST, GET, PARAMS(from url)
     *
     * @return mixed;
     */
    public function processAdminRequest($name, $data)
    {
        $module = $this->loadModule($name);
        if (method_exists($module, 'admin_request')) {
            try {
                $module->admin_request($data);
                if (isset($module->output)) {
                    Foresmo::escape($module->output);
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
        $module = $this->loadModule($name);
        if (method_exists($module, 'ajaxRequest')) {
            try {
                $module->ajaxRequest($data);
                if (isset($module->output)) {
                    //Foresmo::escape($module->output); will this screw up JSON?
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
     * hook
     * run hook
     *
     * @return void
     */
    public static function hook()
    {
        $fargs = func_get_args();
        $hook = array_shift($fargs);
        $hooks = self::$_hooks;
        $data = $fargs[0];
        if (isset($hooks[$hook])) {
            foreach ($hooks[$hook] as $module) {
                if (is_object($module)) {
                    $method = 'hook_' . $hook;
                    $data = $module->$method($data);
                }
            }
        }
        return $data;
    }

    /**
     * _registerHook
     * register specific module hook
     *
     * @param object $module module object
     * @param string $hook hook method
     * @param mixed $priority int for priority 0 highest, 9 lowest, null don't care.
     *
     * @return void
     */
    private function _registerHook($module, $hook, $priority)
    {
        $hooks =& self::$_hooks;
        if (isset($hooks[$hook])) {
            $set = false;
            for ($i = $priority; $i < 10; $i++) {
                if (!is_object($hooks[$hook][$i])) {
                    $hooks[$hook][$i] = $module;
                    $set = true;
                    break;
                }
            }
            if (!$set) {
                $hooks[$hook][] = $module;
            }
        } else {
            $hooks[$hook] = array_fill(0, 10, null);
            $hooks[$hook][$priority] = $module;
        }
    }

    /**
     * registerModuleHooks
     * register hooks from enabled modules
     *
     * @return void
     */
    public function registerModuleHooks()
    {
        $enabled_modules = $this->_model->modules->fetchEnabledModules();
        foreach ($enabled_modules as $module) {
            $name = ucfirst(strtolower($module['class_suffix']));
            $module_obj = $this->loadModule($name);

            if (method_exists($module_obj, 'hook_priorities')) {
                $priorities = $module_obj->hook_priorities();
            } else {
                $priorities = array();
            }

            foreach (get_class_methods($module_obj) as $method) {
                if (strpos($method, 'hook_') === 0 && $method !== 'hook_priorities') {
                    $priority = (isset($priorities[$method])) ? (int) $priorities[$method] : 9;
                    if ($priority < 0 || $priority > 9) {
                        $priority = 9;
                    }
                    $hook = str_replace('hook_', '', $method);
                    $this->_registerHook($module_obj, $hook, $priority);
                }
            }

            $module_obj = null;
        }
    }

    /**
     * loadModule
     * Insert description here
     *
     * @param $name
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function loadModule($name)
    {
        try {
            if (!isset($this->_module_store[$name]) || !is_object($this->_module_store[$name])) {
                require_once Solar::$system . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $name . '.php';
                $this->_module_store[$name] = Solar::factory("Foresmo_Modules_{$name}", array('model' => $this->_model));
            }
            return $this->_module_store[$name];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * scanForModules
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function scanForModules()
    {
        $invisible = array('.', '..', '.htaccess', '.htpasswd', '.svn');
        $exts = array('.php', '.php4', '.php5', '.phps', '.inc');

        $dir = Solar::$system . DIRECTORY_SEPARATOR . 'modules';

        $dir_content = scandir($dir);
        foreach ($dir_content as $key => $content) {
            $path = $dir . DIRECTORY_SEPARATOR . $content;
            if (!in_array($content, $invisible)) {
                $file_ext = strtolower(substr($path, strrpos($path, '.')));
                if (is_dir($path)) {
                    $file = $path . DIRECTORY_SEPARATOR . $content . '.php';
                    if (is_file($file) && is_readable($file)) {
                        $this->loadModule($content);
                        $this->registerModule($content);
                    }
                }
            }
        }
    }

    /**
     * registerModule
     * Insert description here
     *
     * @param $name
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function registerModule($name)
    {
        $module_obj = Solar::factory("Foresmo_Modules_{$name}", array('model' => $this->_model));
        if (!is_object($module_obj)) {
            return false;
        }
        $m_desc =  (isset($module_obj->info['description'])) ? $module_obj->info['description'] : '';
        $m_name =  (isset($module_obj->info['name'])) ? $module_obj->info['name'] : $name;

        $this->_model->modules->registerModule($m_name, $name, $m_desc);
        return true;
    }

    /**
     * installModuleByID
     * Insert description here
     *
     * @param $module_id
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function installModuleByID($module_id)
    {
        $module = $this->_model->modules->fetchModuleInfoByID($module_id);
        $module_obj = $this->loadModule($module['class_suffix']);
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

    /**
     * uninstallModuleByID
     * Insert description here
     *
     * @param $module_id
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function uninstallModuleByID($module_id)
    {
        $module = $this->_model->modules->fetchModuleInfoByID($module_id);
        $module_obj = $this->loadModule($module['class_suffix']);
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
