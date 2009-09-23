<?php
/**
 * Foresmo_Modules_Flickr
 *
 *
 */
class Foresmo_Modules_Flickr extends Solar_Base {

    protected $_Foresmo_Modules_Flickr = array('model' => null);
    protected $_model;
    protected $_name = 'Flickr';
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
        $this->_view_path = Solar_Class::dir($this, 'View');
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

}
