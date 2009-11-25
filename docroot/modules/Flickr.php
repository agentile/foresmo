<?php
/**
 * Foresmo_Modules_Flickr
 *
 *
 */
class Foresmo_Modules_Flickr extends Solar_Base {

    protected $_Foresmo_Modules_Flickr = array('model' => null);
    protected $_model;
    public $name = 'Flickr';
    public $description = 'Flickr module to show Flickr account photos.';
    protected $_view;
    protected $_view_path;
    protected $_view_file;

    public $output = '';

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_model = $this->_config['model'];
        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $web_root = $_SERVER['DOCUMENT_ROOT'];
        } else {
            $web_root = Solar::$system . '/docroot/';
        }
        $this->_view_path = $web_root . 'modules/' . $this->name . '/View';
        $this->_view_file = 'index.php';
        $this->_view = Solar::factory('Solar_View', array('template_path' => $this->_view_path));
    }

    /**
     * start
     * Begin Module Work
     *
     * @return void
     */
    public function start()
    {
        $this->output = $this->_view->fetch($this->_view_file);
    }

    public function install()
    {

    }

    public function uninstall()
    {

    }
}
