<?php
/**
 * Foresmo_Modules_Links
 *
 *
 */
class Foresmo_Modules_Links extends Solar_Base {

    protected $_model;
    protected $_name = 'Links';
    protected $_view;
    protected $_view_path;
    protected $_view_file;

    public $output = '';

    /**
     * __construct
     *
     * @param $model
     */
    public function __construct($model = null)
    {
        $this->_model = $model;
        $this->_view_path = Solar::$system . '/source/foresmo/Foresmo/Modules/' . $this->_name . '/View/';
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
        $links = $this->_model->links->getLinks();
        $this->_view->assign('links', $links);

        $this->output = $this->_view->fetch($this->_view_file);
    }

}
