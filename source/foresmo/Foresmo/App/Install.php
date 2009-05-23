<?php
/**
 * Foresmo_App_Install
 * Install Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile, Bryden Tweedy
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Install extends Foresmo_App_Base {

    protected $_layout_default = 'install';
    protected $_action_default = 'index';

    /**
     * actionIndex
     * Default install action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionIndex()
    {
        if ($this->installed) {
            $this->_redirect('/');
        }
    }
}