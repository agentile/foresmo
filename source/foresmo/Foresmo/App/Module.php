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
    public function actionIndex()
    {
        $this->_layout = null;
        $this->_view = null;
        $f_args = func_get_args();
        // Check if module exists and is enabled
        if (isset($f_args[0]) && $this->_model->modules->isEnabled($f_args[0])) {
            $module_name = ucfirst(strtolower($f_args[0]));
            array_shift($f_args);
            $data = array(
                'POST' => $this->_request->post(),
                'GET' => $this->_request->get(),
                'PARAMS' => $f_args,
            );

            $module_output = $this->_modules->processRequest($module_name, $data);
            if ($module_output && $module_output != '') {
                foreach ($this->enabled_modules_data as $key => $module_data) {
                    if ($module_data['class_suffix'] == $module_name) {
                        $this->enabled_modules_data[$key]['output'] = $module_output;
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

        $redirect_obj->enabled_modules_data = $this->enabled_modules_data;

        if (empty($redirect_info['params'])) {
            $data = $redirect_obj->fetch($action);
        } else {
            $action = $action . '/' . implode('/', $redirect_info['params']);
            $data = $redirect_obj->fetch($action);
        }

        $this->_response->content = $data->content;
    }

}
