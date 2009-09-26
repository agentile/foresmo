<?php
/**
 * Foresmo_App_Base
 * Foresmo App Arch Class
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Base extends Solar_App_Base {

    protected $_layout_default = 'default';
    protected $_model;
    protected $_adapter;
    protected $_modules;
    protected $_cache = null;

    protected $_registered_hooks = array(
        '_preRender' => array(
            'index' => array(
                'main' => array(),
                'tag'   => array(),
                'page'  => array(),
                'sort'  => array(),
            ),
        ),
        '_postRender' => array(
            'index' => array(
                'main' => array(),
                'tag'   => array(),
                'page'  => array(),
                'sort'  => array(),
            ),
        ),
        '_preRun' => array(
            'index' => array(
                'main' => array(),
                'tag'   => array(),
                'page'  => array(),
                'sort'  => array(),
            ),
        ),
        '_postRun' => array(
            'index' => array(
                'main' => array(),
                'tag'   => array(),
                'page'  => array(),
                'sort'  => array(),
            ),
        ),
        '_postAction' => array(
            'index' => array(
                'main' => array(),
                'tag'   => array(),
                'page'  => array(),
                'sort'  => array(),
            ),
        ),
    );

    /**
     * _restricted_names
     *
     * Disallowed slug values for post/pages
     *
     * @var array
     * @access protected
     */
    protected $_restricted_names = array(
        'admin',
        'base',
        'index',
        'install',
        'login',
        'logout',
        'module',
        'page',
        'tag',
        'search',
        'ajax',
        'sort',
    );

    public $session;
    public $connect = true;
    public $installed = false;
    public $blog_theme = 'default';
    public $blog_admin_theme = 'default';
    public $blog_theme_options = array();
    public $blog_admin_theme_options = array();
    public $blog_title = 'Foresmo Blog';
    public $page_title;
    public $pages_count;
    public $web_root;
    public $enabled_modules_data = array();

    /**
     * _setup
     *
     * Set variables used throughout the app here.
     */
    protected function _setup()
    {
        if (Solar_Config::get('Foresmo', 'dev')) {
            xdebug_start_trace('/var/www/foresmo/tmp/trace');
        }
        if (!isset($this->session)) {
            $this->session = Solar::factory('Solar_Session', array('class' => 'Foresmo_App'));
        }
        $adapter = Solar_Config::get('Solar_Sql', 'adapter');
        $adapter = Solar::factory($adapter);
        try {
            $adapter->connect();
        } catch (Exception $e) {
            $this->connect = false;
            // should display an error page and die.
        }
        if ($this->connect) {
            $this->_adapter = $adapter;
            $this->installed = (bool) Solar_Config::get('Foresmo', 'installed');
            if (!$this->installed && $this->_controller != 'install') {
                $this->_redirect('/install');
            }
            $this->web_root = Solar::$system . '/content/';
            $this->_model = Solar_Registry::get('model_catalog');
            $cache_settings = Solar_Config::get('Foresmo', 'cache');
            if (isset($cache_settings['adapter'])) {
                $this->_model->_config['cache'] = $cache_settings;
                $this->_cache = Solar::factory('Solar_Cache', $cache_settings);
            }

            $results = $this->_model->options->fetchBlogOptions();

            foreach ($results as $result) {
                switch ($result['name']) {
                    case 'blog_theme':
                        $this->blog_theme = $result['value'];
                    break;
                    case 'blog_admin_theme':
                        $this->blog_admin_theme = $result['value'];
                    break;
                    case 'blog_theme_options':
                        $this->blog_theme_options = unserialize($result['value']);
                    break;
                    case 'blog_admin_theme_options':
                        $this->blog_admin_theme_options = unserialize($result['value']);
                    break;
                    case 'blog_title':
                        $this->blog_title = $result['value'];
                    break;
                    case 'blog_posts_per_page':
                        $this->_model->posts->posts_per_page = (int) $result['value'];
                    break;
                    case 'blog_comment_link_limit':
                        $this->_model->comments->link_count_limit = (int) $result['value'];
                    break;
                }
            }
            $this->page_title = $this->blog_title;
            $time_info = Foresmo::getTimeInfo();
            Foresmo::$date_format = $time_info['blog_date_format'];
            Foresmo::$timezone = $time_info['blog_timezone'];
            $this->_model->posts->published_posts_count = $this->_model->posts->fetchPublishedPostsCount();
            $this->_setPagesCount();
            $this->_layout_default = $this->blog_theme;
            $this->_setToken();
            $this->_modules = Solar::factory('Foresmo_Modules', array('model' => $this->_model));
            $this->enabled_modules_data = $this->_modules->getEnabledModulesData();
            $this->_registerModuleHooks();
        }
    }

    /**
     * _setPagesCount
     * This function sets how many pages will be available for
     * the blog's posts given the amount of posts and what the
     * posts_per_page is set to.
     *
     * @return void
     */
    private function _setPagesCount()
    {
        $posts_per_page = $this->_model->posts->posts_per_page;
        $posts_count = $this->_model->posts->published_posts_count;
        if ($posts_count <= $posts_per_page) {
            $this->pages_count = 1;
            return;
        }
        $this->pages_count = ceil(($posts_count / $posts_per_page));
    }

    /**
     * allowAjaxAction
     * Check user permissions for an ajax action to be performed
     *
     * @param $action
     * @return bool
     */
    public function allowAjaxAction($action)
    {
        switch ($action) {
            case 'admin_post_new':
                return $this->isValidPermission('create_post');
            break;
            case 'admin_pages_new':
                return $this->isValidPermission('create_page');
            break;
        }
        return false;
    }

    /**
     * isValidPermission
     * Check permission against logged in user's session permission data
     *
     * @param string $permission e.g. 'create_post'
     * @return bool
     */
    public function isValidPermission($permission)
    {
        $user_permissions = $this->session->get('Foresmo_permissions');
        if (!is_array($user_permissions)) {
            return false;
        }

        foreach ($user_permissions as $user_permission) {
            if ($user_permission['name'] == $permission) {
                return true;
            }
        }

        return false;
    }

    /**
     * validate
     * This maps to Solar validation functions, without having to use a
     * form
     *
     * @param $validator validate function to use e.g. validateEmail
     * @param $str string to validate
     *
     * @return bool
     */
    public function validate($validator, $str)
    {
        $obj = Solar::factory('Solar_Filter_' . ucfirst($validator));
        if (is_object($obj)) {
            return $obj->$validator($str);
        }
    }

    /**
     * _setToken
     * This will set a session token that will be used to match against
     * forms posted.
     *
     * @return void
     */
    protected function _setToken()
    {
        if ($this->session->get('Foresmo_token', false) === false
            || !$this->session->get('Foresmo_token')) {
            $token = md5(uniqid(mt_rand(), TRUE));
            $this->session->set('Foresmo_token', $token);
        }
        // else already set.
    }

    /**
     * _checkToken
     * Check a token against the one stored in the session
     *
     * @param $token
     * @return bool
     */
    protected function _checkToken($token)
    {
        if ($this->session->get('Foresmo_token', false) !== false
            && $this->session->get('Foresmo_token') === $token) {
                return true;
        }
        return false;
    }

    /**
     * _getModuleHooks
     * Get registered hooks from enabled modules
     *
     * @return void
     */
    protected function _registerModuleHooks()
    {
        $allowed_processes = array('_preRun', '_postRun', '_postAction', '_preRender', '_postRender');
        $allowed_actions = array(
            'index' => array('main', 'tag', 'page', 'sort'),
        );

        $hooks = $this->_modules->getRegisteredHooks();
        foreach ($hooks as $module => $rh) {
            foreach ($rh as $process => $cont) {
                // allowable hook process?
                if (!in_array($process, $allowed_processes)) {
                    continue;
                }
                foreach ($cont as $controller => $act) {
                    // allowable controller?
                    if (!in_array($controller, array_keys($allowed_actions))) {
                        continue;
                    }
                    foreach ($act as $action => $module_call) {
                        // allowable action?
                        if (!isset($allowed_actions[$controller])
                            || !in_array($action, array_values($allowed_actions[$controller]))) {
                            continue;
                        }
                        $this->_registered_hooks[$process][$controller][$action][$module] = $module_call;
                    }
                }
            }
        }
    }

    /**
     * _addViewTemplates
     * Override Solar_Controller_Page _addViewTemplates
     * to add theme view path to view stack
     *
     */
    protected function _addViewTemplates()
    {
        // get the parents of the current class, including self
        $stack = array_reverse(Solar_Class::parents($this, true));

        // remove Solar_Base
        array_pop($stack);

        // convert underscores to slashes, and add /View
        foreach ($stack as $key => $val) {
            $stack[$key] = str_replace('_', '/', $val) . '/View';
        }

        // add theme view path
        $theme_name = ($this->_controller == 'admin') ? $this->blog_admin_theme : $this->blog_theme;
        $theme_view_path = Solar::$system . '/themes/' . $theme_name;
        $theme_view_path = str_replace('Foresmo', $theme_view_path, $stack[0]);
        array_unshift($stack, $theme_view_path);

        // done, add the stack
        $this->_view_object->addTemplatePath($stack);
    }

    /**
     * _setLayoutTemplates
     * Override Solar_Controller_Page _setLayoutTemplates
     * to add theme layout path to layout stack
     *
     */
    protected function _setLayoutTemplates()
    {
        // get the parents of the current class, including self
        $stack = array_reverse(Solar_Class::parents($this, true));

        // remove Solar_Base
        array_pop($stack);

        // convert underscores to slashes, and add /Layout
        foreach ($stack as $key => $val) {
            $stack[$key] = str_replace('_', '/', $val) . '/Layout';
        }

        // add theme layout path
        $theme_name = ($this->_controller == 'admin') ? $this->blog_admin_theme : $this->blog_theme;
        $theme_layout_path = Solar::$system . '/themes/' . $theme_name;
        $theme_layout_path_base = str_replace('Foresmo', $theme_layout_path, $stack[1]);
        $theme_layout_path = str_replace('Foresmo', $theme_layout_path, $stack[0]);
        array_unshift($stack, $theme_layout_path, $theme_layout_path_base);

        // done, add the stack
        $this->_view_object->setTemplatePath($stack);
    }
}
