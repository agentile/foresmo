<?php
/**
 * Foresmo_App_Admin
 * Admin Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile, Bryden Tweedy
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Admin extends Foresmo_App_Base {

    protected $_layout_default = 'admin';
    protected $_action_default = 'index';

    /**
     * actionIndex
     * Default admin action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionIndex()
    {
        if ($this->session->get('Foresmo_username', false) === false
            || !$this->session->get('Foresmo_username')) {
            $this->_redirect('/login');
        }
        $this->_layout = 'admin';
    }

    /**
     * actionPosts
     * Admin/posts action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionPosts($sub = null)
    {
        if ($this->session->get('Foresmo_username', false) === false
            || !$this->session->get('Foresmo_username')) {
            $this->_redirect('/login');
        }
        $this->_layout = 'admin';
        if ($sub !== null) {
            $sub = strtolower($sub);
            switch ($sub) {
                case 'new':
                    $this->_view = 'posts_new';
                break;
            }
        }
    }

    /**
     * actionComments
     * Admin/comments action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionComments()
    {
        if ($this->session->get('Foresmo_username', false) === false
            || !$this->session->get('Foresmo_username')) {
            $this->_redirect('/login');
        }
        $this->_layout = 'admin';
    }

    /**
     * actionSettings
     * Admin/settings action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionSettings()
    {
        if ($this->session->get('Foresmo_username', false) === false
            || !$this->session->get('Foresmo_username')) {
            $this->_redirect('/login');
        }
        $this->_layout = 'admin';
    }

    /**
     * actionModules
     * Admin/modules action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionModules()
    {
        if ($this->session->get('Foresmo_username', false) === false
            || !$this->session->get('Foresmo_username')) {
            $this->_redirect('/login');
        }
        $this->_layout = 'admin';
    }

    /**
     * actionUsers
     * Admin/users action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionUsers()
    {
        if ($this->session->get('Foresmo_username', false) === false
            || !$this->session->get('Foresmo_username')) {
            $this->_redirect('/login');
        }
        $this->_layout = 'admin';
    }
}
