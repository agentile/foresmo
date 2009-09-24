<?php
/**
 * Foresmo_App_Admin
 * Admin Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Admin extends Foresmo_App_Base {

    protected $_layout_default = 'admin';
    protected $_action_default = 'index';

    public $users = array();
    public $recent_comments = array();
    public $quick_stats = array();
    public $message;
    public $data;

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
    public function actionPages($act = null, $slug = null)
    {
        $this->_view = 'pages_manage';

        if ($act !== null) {
            $act = strtolower($act);
            switch ($act) {
                case 'new':
                    $this->_view = 'pages_new';
                break;
                case 'manage':
                    $this->_view = 'pages_manage';
                    $this->data = $this->_model->posts->getAllPages();
                break;
                case 'edit':
                    $this->_view = 'pages_edit';
                    if ($slug === null) {
                        $this->message = 'Please select a page to edit.';
                        $this->_view = 'pages_manage';
                        $this->data = $this->_model->posts->getAllPages();
                        return;
                    }

                    $this->data = $this->_model->posts->getPageBySlug($slug);

                    if (empty($this->data)) {
                        $this->message = "$slug is not a valid page. Please select a page to edit.";
                        $this->_view = 'pages_manage';
                        $this->data = $this->_model->posts->getAllPages();
                        return;
                    }
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
    public function actionPosts($act = null, $slug = null)
    {
        if ($act !== null) {
            $act = strtolower($act);
            switch ($act) {
                case 'new':
                    $this->_view = 'posts_new';
                break;
                case 'manage':
                    $this->_view = 'posts_manage';
                    $this->data = $this->_model->posts->getAllPosts();
                break;
                case 'edit':
                    $this->_view = 'posts_edit';
                    if ($slug === null) {
                        $this->message = 'Please select a post to edit.';
                        $this->_view = 'posts_manage';
                        $this->data = $this->_model->posts->getAllPosts();
                        return;
                    }

                    $this->data = $this->_model->posts->getPostBySlug($slug);

                    if (empty($this->data)) {
                        $this->message = "$slug is not a valid post. Please select a post to edit.";
                        $this->_view = 'posts_manage';
                        $this->data = $this->_model->posts->getAllPosts();
                        return;
                    }
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
        $this->users = $this->_model->users->getUsers();
        $this->quick_stats = array(
            'total_users' => count($this->users),
        );
    }
}
