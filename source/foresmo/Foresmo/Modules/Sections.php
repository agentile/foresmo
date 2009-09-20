<?php
/**
 * Foresmo_Modules_Sections
 *
 *
 */
class Foresmo_Modules_Sections extends Solar_Base {

    protected $_model;
    protected $_name = 'Sections';
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
        $this->output = $this->_view->fetch($this->_view_file);
    }

}
