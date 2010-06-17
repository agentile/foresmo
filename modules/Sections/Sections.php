<?php
/**
 * Foresmo_Modules_Sections
 *
 *
 */
class Foresmo_Modules_Sections extends Foresmo_Modules_Base {

    protected $_Foresmo_Modules_Sections = array();

    public $info = array(
        'name' => 'Sections',
        'description' => 'Categorize your posts into sections.'
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
