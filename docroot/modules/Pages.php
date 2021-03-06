<?php
/**
 * Foresmo_Modules_Pages
 *
 * Pages Module: Provides links to published pages.
 *
 */
class Foresmo_Modules_Pages extends Solar_Base {

    protected $_Foresmo_Modules_Pages = array('model' => null);
    protected $_model;
    public $name = 'Pages';
    public $description = 'Display the pages of your blog.';
    protected $_view;
    protected $_view_path;
    protected $_view_file;

    public $register = array();
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
        $this->register = array(
            '_preRender' => array(
                'index' => array(
                    'main' => 'preRender',
                ),
            ),
            '_postRender' => array(
                'index' => array(
                    'main' => 'postRender',
                ),
            ),
            '_preRun' => array(
                'index' => array(
                    'main' => 'preRun',
                ),
            ),
            '_postRun' => array(
                'index' => array(
                    'main' => 'postRun',
                ),
            ),
            '_postAction' => array(
                'index' => array(
                    'main' => 'postAction',
                ),
            ),
        );
    }

    /**
     * start
     * Begin Module Work
     *
     * @return void
     */
    public function start()
    {
        $pages = $this->_model->posts->fetchPublishedPages();
        $this->_view->assign('pages', $pages);

        $this->output = $this->_view->fetch($this->_view_file);
    }

    public function preRender()
    {

    }

    public function postRender()
    {

    }

    public function preRun()
    {

    }

    public function postRun()
    {

    }

    public function postAction()
    {

    }

    public function install()
    {

    }

    public function uninstall()
    {

    }
}
