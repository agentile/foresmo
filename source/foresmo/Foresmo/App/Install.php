<?php
/**
 * Foresmo_App_Install
 * Install Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Install extends Foresmo_App_Base {

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
        $this->_layout = 'install';
    }
}
