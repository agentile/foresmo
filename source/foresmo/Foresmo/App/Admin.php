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
    public $data = array();
    public $blog_theme_count = 0;
    public $admin_theme_count = 0;
    public $posts;
    public $pages;
    public $timezones;
    public $timezone_current;
    public $module_admin_output = '';
    public $comments = array();

    /**
     * _setup
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
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
        $this->recent_comments = $this->_model->comments->fetchRecentComments(20);
        $this->posts = $this->_model->posts->fetchPublishedPosts();
        $this->pages = $this->_model->posts->fetchPublishedPages();
        $this->quick_stats = array(
            'total_posts' => $this->_model->posts->fetchTotalCount(1, 1),
            'total_pages' => $this->_model->posts->fetchTotalCount(2, 1),
            'total_comments' => $this->_model->comments->fetchTotalCount(0, 1),
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
        if ($act == null) {
            $this->_redirect('/admin/pages/manage');
            return;
        }
        $act = strtolower($act);
        switch ($act) {
            case 'new':
                $this->_view = 'pages_new';
            break;
            case 'manage':
                $this->_view = 'pages_manage';
                $this->data = $this->_model->posts->fetchAllPages();
            break;
            case 'edit':
                $this->_view = 'pages_edit';
                if ($slug === null) {
                    $this->message = 'Please select a page to edit.';
                    $this->_view = 'pages_manage';
                    $this->data = $this->_model->posts->fetchAllPages();
                    return;
                }

                $this->data = $this->_model->posts->fetchPageBySlug($slug);

                if (empty($this->data)) {
                    $this->message = "$slug is not a valid page. Please select a page to edit.";
                    $this->_view = 'pages_manage';
                    $this->data = $this->_model->posts->fetchAllPages();
                    return;
                }
            break;

            case 'delete':
                $this->_view = 'pages_delete';

                if ($slug === null) {
                    $this->_redirect('/admin/pages/manage/');
                    return;
                }

                $this->data = $this->_model->posts->fetchPageBySlug($slug);

                if (empty($this->data)) {
                    $this->message = "$slug is not a valid page.  Please select a page to delete.";
                    $this->_view = 'pages_manage';
                    $this->data = $this->_model->posts->fetchAllPages();
                    return;
                }

                $post_data = $this->_request->post();
                if (isset($post_data['yes'])) {
                    $this->_model->posts->deletePage($this->data['id']);
                    $this->_redirect('/admin/pages/manage/');
                    return;
                } elseif (isset($post_data['no'])) {
                    $this->_redirect('/admin/pages/manage/');
                }


            break;
        }
    }

    /**
     * actionPosts
     * Admin/posts action/post
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionPosts($act = null, $slug = null)
    {
        if ($act == null) {
            $this->_redirect('/admin/posts/manage');
            return;
        }
        $act = strtolower($act);
        switch ($act) {
            case 'new':
                $this->_view = 'posts_new';
            break;
            case 'manage':
                $this->_view = 'posts_manage';
                $this->data = $this->_model->posts->fetchAllPosts();
            break;
            case 'edit':
                $this->_view = 'posts_edit';
                if ($slug === null) {
                    $this->message = 'Please select a post to edit.';
                    $this->_view = 'posts_manage';
                    $this->data = $this->_model->posts->fetchAllPosts();
                    return;
                }

                $this->data = $this->_model->posts->fetchPostBySlug($slug);

                if (empty($this->data)) {
                    $this->message = "$slug is not a valid post. Please select a post to edit.";
                    $this->_view = 'posts_manage';
                    $this->data = $this->_model->posts->fetchAllPosts();
                    return;
                }
            break;
            case 'delete':
                $this->_view = 'posts_delete';
                if ($slug === null) {
                    $this->_redirect('/admin/posts/manage');
                    return;
                }

                $this->data = $this->_model->posts->fetchPostBySlug($slug);

                if (empty($this->data)) {
                    $this->message = "$slug is not a valid post.  Please select a post to delete.";
                    $this->_view = 'posts_manage';
                    $this->data = $this->_model->posts->fetchAllPosts();
                    return;
                }

                $post_data = $this->_request->post();
                if (isset($post_data['yes'])) {
                    $this->_model->posts->deletePost($this->data['id']);
                    $this->_redirect('/admin/posts/manage/');
                    return;
                } elseif (isset($post_data['no'])) {
                    $this->_redirect('/admin/posts/manage/');
                }
            break;
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
    public function actionComments($act = null)
    {
        if ($act == null) {
            $this->actionComments('manage');
            return;
        }
        $act = strtolower($act);
        switch ($act) {
            case 'manage':
                /**
                 * status codes
                 * 0 = hidden, disapproved,
                 * 1 = visible, approved
                 * 2 = spam
                 * 3 = under moderation
                 */
                $post_data = $this->_request->post();
                if (isset($post_data['ajax_action']) && $post_data['ajax_action'] == 'admin_comments_manage') {
                    $comments = $post_data['comments'];
                    switch ($post_data['action']) {
                        case 'approve':
                            $status = 1;
                        break;
                        case 'disapprove':
                            $status = 0;
                        break;
                        case 'spam':
                            $status = 2;
                        break;
                        case 'moderation':
                            $status = 3;
                        break;
                        case 'delete':
                            $this->_model->comments->deleteComments($comments);
                            $this->message = 'Successfully deleted comments';
                        break;
                    }
                    if (isset($status) && isset($post_data['comments'])) {
                        $this->_model->comments->updateCommentsStatus($comments, $status);
                        $this->message = 'Successfully updated comments';
                    }
                } 
                $this->comments = $this->_model->comments->fetchComments();
                $this->_view = 'comments_manage';
            break;
            case 'spam':
                $post_data = $this->_request->post();
                if (isset($post_data['ajax_action']) && $post_data['ajax_action'] == 'admin_comments_spam') {
                    $comments = $post_data['comments'];
                    switch ($post_data['action']) {
                        case 'approve':
                            $status = 1;
                        break;
                        case 'moderation':
                            $status = 3;
                        break;
                    }
                    if (isset($status) && isset($post_data['comments'])) {
                        $this->_model->comments->updateCommentsStatus($comments, $status);
                        $this->message = 'Successfully updated comments';
                    }
                }
                $this->comments = $this->_model->comments->fetchSpam();
                $this->_view = 'comments_spam';
            break;
        }
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
        $post_data = $this->_request->post();
        if (isset($post_data['submit'])) {
            foreach ($post_data as $key => $value) {
                switch ($key) {
                    case 'blog_title':
                        if (trim($value) != '') {
                            $this->_model->options->updateOption('blog_title', $value);
                        }
                    break;
                    case 'blog_date_format':
                        if (!isset($post_data['blog_date_format_preset']) && trim($value) != '') {
                            $this->_model->options->updateOption('blog_date_format', $value);
                        }
                    break;
                    case 'blog_date_format_preset':
                        if (trim($value) != '') {
                            $this->_model->options->updateOption('blog_date_format', $value);
                        }
                    break;
                    case 'blog_timezone':
                        if (trim($value) != '') {
                            $this->_model->options->updateOption('blog_timezone', $value);
                            ini_set('date.timezone', $value);
                        }
                    break;
                }
            }
        }
        $this->data = $this->_model->options->fetchAllOptions(false);
        $this->timezones = Foresmo::fetchTimeZones();
        $this->timezone_current = date_default_timezone_get();
    }

    /**
     * actionThemes
     * Admin/themes action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionThemes($act = null, $slug = null)
    {
        if ($act == null) {
            $this->actionThemes('manage');
            return;
        }
        $act = strtolower($act);
        switch ($act) {
            case 'manage':
                $post_data = $this->_request->post();
                if (isset($post_data['ajax_action']) && $post_data['ajax_action'] == 'admin_theme_admin_update') {
                    $this->_model->options->updateTheme($this->_post['theme'], true);
                    $this->message = 'Successfully changed admin theme';
                    $this->_redirect('/admin/themes/manage');
                } elseif (isset($post_data['ajax_action']) && $post_data['ajax_action'] == 'admin_theme_update') {
                    $this->_model->options->updateTheme($this->_post['theme']);
                    $this->message = 'Successfully changed theme';
                    $this->_redirect('/admin/themes/manage');
                }
                $this->data = $this->_themes->scanForThemes();
                foreach ($this->data as $k => $v) {
                    if (in_array('admin', $v['type'])) {
                        $this->admin_theme_count++;
                    }
                    if (in_array('main', $v['type'])) {
                        $this->blog_theme_count++;
                    }
                }
                $this->_view = 'themes_manage';
            break;
        }
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
    public function actionModules($act = null, $slug = null)
    {
        if ($act == null) {
            $this->actionModules('manage');
            return;
        }
        $act = strtolower($act);
        switch ($act) {
            case 'manage':
                $this->_modules->scanForModules();
                $this->_view = 'modules_manage';
                $this->data = $this->_model->modules->fetchModules();
            break;
            case 'edit':
                $this->_view = 'modules_edit';
                if ($slug === null) {
                    $this->message = 'Please select a module to edit.';
                    $this->_view = 'modules_manage';
                    $this->data = $this->_model->modules->fetchModules();
                    return;
                }

                $this->data = $this->_model->modules->fetchModuleInfoByName($slug);

                if (empty($this->data)) {
                    $this->message = "$slug is not a valid module. Please select a module to edit.";
                    $this->_view = 'modules_manage';
                    $this->data = $this->_model->modules->fetchModules();
                    return;
                } else {
                    $data = array(
                        'POST' => $this->_request->post(),
                        'GET' => $this->_request->get(),
                        'PARAMS' => $this->_info,
                    );

                    $this->_modules->processAdminRequest($this->data['class_suffix'], $data);
                    $this->module_admin_output = $this->_modules->fetchAdminContent($this->data['class_suffix'], $data);
                }
            break;
        }
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
    public function actionUsers($act = null)
    {
        if ($act == null) {
            $this->data = $this->_model->users->fetchUsers();
            $this->quick_stats = array(
                'total_users' => count($this->data),
            );
            return;
        }
        $act = strtolower($act);
        switch ($act) {
            case 'new':
                $this->_view = 'users_new';
            break;
            case 'manage':
                $this->_view = 'users_manage';
                $this->data = $this->_model->users->fetchUsers();
            break;
        }
    }

    /**
     * actionGroups
     * Admin/groups action/page
     *
     * @return void
     *
     * @access public
     * @since .09
     */
    public function actionGroups($act = null, $sub = null)
    {
        if ($act == null) {
            $this->data = $this->_model->groups->fetchGroups();
            $this->_view = 'groups_manage';
            return;
        }
        $act = strtolower($act);
        switch ($act) {
            case 'new':
                $this->_view = 'groups_new';
            break;
            case 'edit':
                $this->_view = 'groups_edit';
                $this->data = $this->_model->groups->fetchGroupByName($sub);
            break;
            case 'delete':
                $group_user_count = $this->_model->groups->fetchGroupUserCount($sub);
                if (!$this->_model->groups->isValidGroup($sub)) {
                    $this->message = "$sub is not a valid group. Please select a valid group to delete.";
                    $this->data = $this->_model->groups->fetchGroups();
                    $this->_view = 'groups_manage';
                } elseif ($group_user_count != 0) {
                    $this->message = "Group '$sub' has associated users. Please change the user group for these users or delete the users.";
                    $this->data = $this->_model->groups->fetchGroups();
                    $this->_view = 'groups_manage';
                } else {
                    $this->_view = 'groups_delete';
                }
            break;
            case 'manage':
                $this->_view = 'groups_manage';
                $this->data = $this->_model->groups->fetchGroups();
            break;
        }
    }
}
