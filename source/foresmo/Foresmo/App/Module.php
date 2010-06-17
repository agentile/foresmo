<?php
/**
 * Foresmo_App_Module
 * Module Request Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Module extends Foresmo_App_Base {

    protected $_action_default = 'index';

    /**
     * actionIndex
     * Module dispatcher
     * This will past along url param/request data to the specified module, which will then
     * handle processing, upon return, default behavior is to load referring page.
     *
     * @param $param
     *
     * @return void
     */
    public function actionIndex($module = null, $params = array())
    {
        $this->_layout = null;
        $this->_view = null;
        // Check if module exists and is enabled
        if (isset($module) && $this->_model->modules->isEnabled($module)) {
            $module_name = ucfirst(strtolower($module));
            $data = array(
                'POST' => $this->_request->post(),
                'GET' => $this->_request->get(),
                'PARAMS' => $params,
            );

            $module_output = $this->_modules->processRequest($module_name, $data);
            if ($module_output && $module_output != '') {
                foreach ($this->enabled_modules->all as $key => $module_data) {
                    if ($module_data['class_suffix'] == $module_name) {
                        $this->enabled_modules->all[$key]['output'] = $module_output;
                    }
                }
            }
        }

        $redirect_info = $this->session->getFlash('redirect');
        try {
            $redirect_obj = Solar::factory('Foresmo_App_'.ucfirst($redirect_info['controller']));
        } catch (Exception $e) {
            $redirect_obj = Solar::factory('Foresmo_App_Index');
        }

        if (isset($redirect_info['action'])) {
            $action = $redirect_info['action'];
        } else {
            $action = 'main';
        }

        $redirect_obj->enabled_modules->all = $this->enabled_modules->all;

        if (empty($redirect_info['params'])) {
            $data = $redirect_obj->fetch($action);
        } else {
            $action = $action . '/' . implode('/', $redirect_info['params']);
            $data = $redirect_obj->fetch($action);
        }

        $this->_response->content = $data->content;
    }

    // TODO: Use Router for this
    /**
     * _notFound
     * Insert description here
     *
     * @param $action
     * @param $params
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _notFound($action, $params = null)
    {
        $this->_info[0] = $action;
        $this->actionIndex($action, $params);
    }
}
