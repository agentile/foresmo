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
    protected $_name = 'Tags';
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
        $this->_view_path = Solar_Config::get('Solar', 'web_root') . 'modules/' . $this->_name . '/View';
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

}
