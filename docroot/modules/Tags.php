<?php
/**
 * Foresmo_Modules_Tags
 *
 * Tags Module: Show available tags and count
 *
 */
class Foresmo_Modules_Tags extends Solar_Base {

    protected $_Foresmo_Modules_Tags = array('model' => null);
    protected $_model;
    public $name = 'Tags';
    public $description = 'Listing of available tags and their post count.';
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
        $tags = $this->_model->tags->fetchTagsForPublishedPosts();
        $this->_view->assign('tags', $tags);

        $this->output = $this->_view->fetch($this->_view_file);
    }

    public function install()
    {

    }

    public function uninstall()
    {

    }
}
