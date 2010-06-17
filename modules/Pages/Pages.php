<?php
/**
 * Foresmo_Modules_Pages
 *
 * Pages Module: Provides links to published pages.
 *
 */
class Foresmo_Modules_Pages extends Foresmo_Modules_Base {

    protected $_Foresmo_Modules_Pages = array();

    public $info = array(
        'name' => 'Pages',
        'description' => 'Display the pages of your blog.'
    );

    public $register = array();
    public $output = '';

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
