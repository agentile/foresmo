<?php
/**
 * Foresmo_Modules_Base
 * Arch class for modules
 *
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class Foresmo_Modules_Base extends Solar_Base {

    protected $_Foresmo_Modules_Base = array('model' => null);
    protected $_model;
    protected $_module_info = array();
    protected $_module_name = 'Base';
    protected $_view_file;
    protected $_view_path;
    protected $_view;

    public $web_root;
    public $output = '';
    public $info = array(
        'name' => null,
        'description' => null,
    );

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_model = $this->_config['model'];
        $this->web_root = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : Solar::$system . DIRECTORY_SEPARATOR . 'docroot' . DIRECTORY_SEPARATOR;
        $this->web_root = Solar_Dir::fix($this->web_root);
        $class_arr = explode('_', get_class($this));
        $this->_module_name = end($class_arr);
        $this->_module_info = $this->_model->modules->fetchModuleInfoByName($this->_module_name);
        $this->_setViewPath();
        $this->_setViewFile();
        $this->_setView();
        $this->_view->addHelperClass('Foresmo_View_Helper');
    }

    /**
     *_refreshModuleInfo
     *
     */
    protected function _refreshModuleInfo()
    {
        $this->_module_info = $this->_model->modules->fetchModuleInfoByName($this->_module_name, false);
    }

    /**
     * _setViewPath
     *
     */
    protected function _setViewPath($path = null)
    {
        if (!is_null($path)) {
            $this->_view_path = $path;
        } else {
            $this->_view_path = Solar::$system .  DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->_module_name . DIRECTORY_SEPARATOR . 'View';
        }
    }

    /**
     * _setViewFile
     */
    protected function _setViewFile($file = null)
    {
        if (!is_null($file)) {
            $this->_view_file = $file;
        } else {
            $this->_view_file = 'index.php';
        }
    }

    /**
     * _setView
     */
    protected function _setView()
    {
        $this->_view = Solar::factory('Solar_View', array('template_path' => $this->_view_path));
    }
    
    protected function _redirect($spec, $code = 302)
    {
        $this->_response->redirect($spec, $code);
        exit(0);
    }

    /**
     * install
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
    public function install()
    {

    }

    /**
     * uninstall
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
    public function uninstall()
    {

    }

    /**
     * start
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
    public function start()
    {

    }

    /**
     * request
     * Insert description here
     *
     * @param $data
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function request($data)
    {

    }

    /**
     * ajaxRequest
     * Insert description here
     *
     * @param $data
     *
     * @return
     */
    public function ajaxRequest($data)
    {

    }

    /**
     * ajaxRequest
     * Insert description here
     *
     * @param $data
     *
     * @return
     */
    public function adminRequest($data)
    {
        
    }
    
    /**
     * admin
     * Insert description here
     *
     * @param $data
     *
     * @return
     */
    public function admin($data)
    {
        $this->output = '';
    }
}
