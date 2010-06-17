<?php
/**
 * Foresmo_Modules_Flickr
 *
 *
 */
class Foresmo_Modules_Flickr extends Foresmo_Modules_Base {

    protected $_Foresmo_Modules_Flickr = array();

    public $info = array(
        'name' => 'Flickr',
        'description' => 'Flickr module to show Flickr account photos.'
    );

    public $output = '';

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
