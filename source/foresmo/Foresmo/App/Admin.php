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

    public $recent_comments = array();
    public $quick_stats = array();

    public function _setup()
    {
        parent::_setup();
        if ($this->session->get('Foresmo_username', false) === false
            || !$this->session->get('Foresmo_username')) {
            $this->_redirect('/login');
        }
        $this->_layout_default = 'admin';
    }

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
        $this->recent_comments = $this->_model->comments->getRecentComments(20);
        $this->quick_stats = array(
            'total_posts' => $this->_model->posts->getTotalCount(1, 1),
            'total_pages' => $this->_model->posts->getTotalCount(2, 1),
            'total_comments' => $this->_model->comments->getTotalCount(0, 1),
        );
    }

    /**
     * actionPages
     * Admin/pages action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionPages($sub = null)
    {
        if ($sub !== null) {
            $sub = strtolower($sub);
            switch ($sub) {
                case 'new':
                    $this->_view = 'pages_new';
                break;
            }
        }
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

    }
}
