<?php
/**
 * Foresmo_App_Base
 * Foresmo Arch Class
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile, Bryden Tweedy
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Base extends Solar_App_Base {

    protected $_layout_default = 'default';
    protected $_model;

    public $session;
    public $installed = false;
    public $theme = 'default';

    /**
     * _setup
     *
     * Set variables used throughout the app here.
     */
    protected function _setup()
    {
        $this->_model = Solar_Registry::get('model_catalog');
        $this->session = Solar::factory('Solar_Session', array('class' => 'Foresmo_App'));
        $where = array('name = ?' => 'blog_installed');
        $result = $this->_model->options->fetchArray($where);
        if (count($result) > 0) {
            $this->installed = true;
        }
        $where = array('name = ?' => 'blog_theme');
        $result = $this->_model->options->fetchArray($where);
        if (count($result) > 0) {
            $this->theme = $result[0]['value'];
        }
    }
}
