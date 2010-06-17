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
class Foresmo_App_Base extends Solar_Controller_Page {

    protected $_layout_default = 'default';
    protected $_model;
    protected $_adapter;
    protected $_modules;
    protected $_themes;
    protected $_cache = null;

    // Cache login attempts in for this many seconds
    const FLOODCONTROL_CACHE_CLIENT = 600;
    const FLOODCONTROL_CACHE_ACCOUNT = 600;

    // Number of attempts allowed before flood control kicks in
    const FLOODCONTROL_CLIENT_MAX = 4;
    const FLOODCONTROL_ACCOUNT_MAX = 3;

    public $response_delay = 0;

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
        'feed',
    );

    public $session;
    public $connect = true;
    public $installed = false;
    public $blog_theme = 'default';
    public $blog_admin_theme = 'default';
    public $blog_theme_options = array();
    public $blog_admin_theme_options = array();
    public $blog_title = 'Foresmo Blog';
    public $blog_uid;
    public $page_title;
    public $pages_count;
    public $web_root;
    public $enabled_modules;
    public $csrf_token = null;
    public $page_tree;

    /**
     * _postRender
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
    protected function _postRender()
    {
        parent::_postRender();
/*
        if (class_exists('tidy')) {
            $tidy = new tidy();
            $config = array(
               'indent' => true,
               'indent-spaces' => 4,
               'wrap' => 200
            );
            $tidy->parseString($this->_response->content, $config, 'utf8');
            $tidy->cleanRepair();

            // Output
            $this->_response->content = $tidy;
        }
*/
    }

    /**
     * _setup
     *
     * Set variables used throughout the app here.
     */
    protected function _setup()
    {
        parent::_setup();
        $this->web_root = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : Solar::$system . '/docroot/';
        $this->web_root = Solar_Dir::fix($this->web_root);

        if (!isset($this->session)) {
            $this->session = Solar::factory('Solar_Session', array('class' => 'Foresmo_App'));
        }

        // Set CSRF Token
        $this->_setToken();
        $this->csrf_token = $this->_getToken();

        $this->installed = (bool) Solar_Config::get('Foresmo', 'installed');
        if (!$this->installed && $this->_controller != 'install' && $this->_controller != 'ajax') {
            $this->_redirect('/install');
        } elseif (!$this->installed) {
            return;
        }

        $adapter_type = Solar_Config::get('Solar_Sql', 'adapter');
        $adapter = Solar::factory($adapter_type);
        try {
            $adapter->connect();
        } catch (Exception $e) {
            // Display No DB Connection view and exit.
            $this->connect = false;
            $view = Solar::factory('Solar_View', array('template_path' => dirname(__FILE__) . '/Base/View/'));
            $view->assign('adapter_config', Solar_Config::get($adapter_type));
            echo $view->fetch('nodb');
            exit;
        }

        $this->_adapter = $adapter;
        $this->_model = Solar_Registry::get('model_catalog');

        // Set Cache
        $cache_settings = Solar_Config::get('Foresmo', 'cache');
        if (isset($cache_settings['adapter'])) {
            $this->_model->_config['cache'] = $cache_settings;
            $this->_cache = Solar::factory('Solar_Cache', $cache_settings);
        }

        $this->_setBlogOptions();

        $this->page_title = $this->blog_title;
        $time_info = Foresmo::getTimeInfo();
        Foresmo::$date_format = $time_info['blog_date_format'];
        Foresmo::$timezone = $time_info['blog_timezone'];
        $this->_model->posts->published_posts_count = $this->_model->posts->fetchPublishedPostsCount();
        $this->_setPagesCount($this->_model->posts->posts_per_page, $this->_model->posts->published_posts_count);
        //$this->_layout_default = $this->blog_theme;

        // Load Themes
        $this->_themes = Solar::factory('Foresmo_Themes', array('model' => $this->_model));

        // Load Modules
        $this->_modules = Solar::factory('Foresmo_Modules', array('model' => $this->_model));
        $this->_modules->registerModuleHooks();
        $this->enabled_modules = $this->_modules->getEnabledModulesData();
    }

    /**
     * _setBlogOptions
     * Fetches blog options from db and sets their respective properties
     *
     * @return void
     */
    protected function _setBlogOptions()
    {
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
                case 'blog_uid':
                    $this->blog_uid = $result['value'];
                break;
                case 'blog_posts_per_page':
                    $this->_model->posts->posts_per_page = (int) $result['value'];
                break;
                case 'blog_comment_link_limit':
                    $this->_model->comments->link_count_limit = (int) $result['value'];
                break;
                case 'blog_comment_default_status':
                    $this->_model->comments->default_status = (int) $result['value'];
                break;
            }
        }
    }

    /**
     * _setPagesCount
     * This function sets how many pages will be available for
     * fetched content given the count of content and what the
     * posts_per_page is set to.
     *
     * @return void
     */
    protected function _setPagesCount($posts_per_page, $content_count)
    {
        if ($content_count <= $posts_per_page) {
            $this->pages_count = 1;
            return;
        }
        $this->pages_count = ceil(($content_count / $posts_per_page));
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
            case 'admin_page_new':
                return $this->isValidPermission('create_page');
            break;
            case 'admin_post_edit':
                return $this->isValidPermission('edit_post');
            break;
            case 'admin_page_edit':
                return $this->isValidPermission('edit_page');
            break;
            case 'admin_modules_change_status':
                return $this->isValidPermission('manage_modules');
            break;
            case 'admin_theme_admin_update':
                return $this->isValidPermission('manage_themes');
            break;
            case 'admin_theme_update':
                return $this->isValidPermission('manage_themes');
            break;
            case 'admin_blog_settings':
                return $this->isValidPermission('blog_settings');
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
            mt_srand(); // make sure we are seeding ourself
            $token = md5(uniqid(mt_rand(), TRUE));
            $this->session->set('Foresmo_token', $token);
        }
        // else already set.
    }

    /**
     * _getToken
     * Fetch session csrf_token if it exists
     *
     * @return string
     */
    protected function _getToken()
    {
        if ($this->session->get('Foresmo_token', false) !== false
            && $this->session->get('Foresmo_token') !== '') {
                return $this->session->get('Foresmo_token');
        }
        return null;
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
        if ($this->_controller == 'admin') {
            $theme_name = $this->blog_admin_theme;
            $theme_view_path = Solar::$system . DIRECTORY_SEPARATOR .'themes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $theme_name . DIRECTORY_SEPARATOR . 'views';
            $default_view_path = Solar::$system . DIRECTORY_SEPARATOR .'themes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views';
        } else {
            $theme_name = $this->blog_theme;
            $theme_view_path = Solar::$system . DIRECTORY_SEPARATOR .'themes' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . $theme_name . DIRECTORY_SEPARATOR . 'views';
            $default_view_path = Solar::$system . DIRECTORY_SEPARATOR .'themes' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views';
        }

        // add default theme to stack as fall back
        if (Solar_Dir::exists($default_view_path)) {
            array_unshift($stack, $default_view_path);
        }
        // add theme to stack
        if (Solar_Dir::exists($theme_view_path) && $default_view_path != $theme_view_path) {
            array_unshift($stack, $theme_view_path);
        }

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
        if ($this->_controller == 'admin') {
            $theme_name = $this->blog_admin_theme;
            $theme_layout_path = Solar::$system . DIRECTORY_SEPARATOR .'themes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $theme_name . DIRECTORY_SEPARATOR . 'layouts';
            $default_layout_path = Solar::$system . DIRECTORY_SEPARATOR .'themes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'layouts';
        } else {
            $theme_name = $this->blog_theme;
            $theme_layout_path = Solar::$system . DIRECTORY_SEPARATOR .'themes' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . $theme_name . DIRECTORY_SEPARATOR . 'layouts';
            $default_layout_path = Solar::$system . DIRECTORY_SEPARATOR .'themes' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'layouts';
        }

        if (Solar_Dir::exists($default_layout_path)) {
            array_unshift($stack, $default_layout_path);
        }

        if (Solar_Dir::exists($theme_layout_path) && $theme_layout_path != $default_layout_path) {
            array_unshift($stack, $theme_layout_path);
        }

        // done, add the stack
        $this->_view_object->setTemplatePath($stack);
    }

    /**
     * _incrementClientLoginAttempts
     * Increment failed login attempts in cache for an ip
     *
     */
    protected function _incrementClientLoginAttempts()
    {
        $ip = Foresmo::getIP();
        if ($ip == '0.0.0.0') {
            return;
        }
        $key = 'foresmo_flood_control_' . $ip;

        if ($this->_cache->fetch($key) === false) {
            $this->_cache->add($key, 1);
        } else {
            $this->_cache->increment($key, 1);
        }
    }

    /**
     * _clientFloodCheck
     * Check cache to see if IP requires rate limiting
     * If exceeds max attempts, inject delay
     *
     */
    protected function _clientFloodCheck()
    {
        $count = $this->_getClientFloodCount();

        if ($count >= self::FLOODCONTROL_CLIENT_MAX) {
            $this->response_delay = ($count/4) > 8 ? 8 : ($count/4);
        }
    }

    /**
     * _getClientFloodCount
     * Check cache to see if IP requires rate limiting
     * If exceeds max attempts, inject delay
     *
     */
    protected function _getClientFloodCount()
    {
        $ip = Foresmo::getIP();
        $count = 0;

        if ($ip != '0.0.0.0') {
            $key = 'foresmo_flood_control_' . $ip;

            $life = $this->_cache->_config['life'];
            $this->_cache->_config['life'] = self::FLOODCONTROL_CACHE_CLIENT;
            $count = $this->_cache->fetch($key);
            $this->_cache->_config['life'] = $life;

            if ($count !== false && is_numeric($count)) {
                $count = (int) $count;
            } else {
                $count = 0;
            }
        }

        return $count;
    }

    /**
     * _incrementAccountLoginAttempts
     * Increment failed login attempts in cache for a username
     *
     */
    protected function _incrementAccountLoginAttempts($username)
    {
        $key = 'foresmo_flood_control_' . $username;

        if ($this->_cache->fetch($key) === false) {
            $this->_cache->add($key, 1);
        } else {
            $this->_cache->increment($key, 1);
        }
    }


    /**
     * _accountFloodCheck
     * Check cache to see if user account requires rate limiting
     * If exceeds max account login attempts, require captcha.
     *
     * @param string $username username
     */
    protected function _accountFloodCheck($username)
    {
        $count = $this->_getAccountFloodCount($username);

        if ($count >= self::FLOODCONTROL_ACCOUNT_MAX) {
            $this->session->set('captcha_required', true);
        }
    }

    /**
     * _getAccountFloodCount
     * Check cache to see if user account requires rate limiting
     * If exceeds max account login attempts, require captcha.
     *
     * @param string $username username
     */
    protected function _getAccountFloodCount($username)
    {
        $key = 'foresmo_flood_control_' . $username;

        $life = $this->_cache->_config['life'];
        $this->_cache->_config['life'] = self::FLOODCONTROL_CACHE_ACCOUNT;
        $count = $this->_cache->fetch($key);
        $this->_cache->_config['life'] = $life;

        if ($count !== false && is_numeric($count)) {
            $count = (int) $count;
        } else {
            $count = 0;
        }

        return $count;
    }

    /**
     * _injectDelay
     * simple util function to inject a delay into the response time.
     *
     * @param int $delay seconds
     */
    protected function _injectDelay($delay = 0) {
        if (is_numeric($delay) && $delay > 0) {
            sleep($delay);
        }
    }

    /**
     * buildPageTree
     * simple util function to build ul li nest for pages by their parent_id
     *
     *
     */
    public function buildPageTree()
    {
        $pages = $this->_model->posts->fetchAllPublishedPages();
        $this->page_tree = $this->_buildPageTree($pages);
    }

    /**
     * _buildPageTree
     * Insert description here
     *
     * @param $pages
     * @param $id
     * @param $path
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _buildPageTree($pages, $id = null, $path = null)
    {
        $pages = array_reverse($pages);
        $html = '';
        foreach ($pages as $key => $page) {
            $class = (in_array($page['slug'], $this->_info)) ? 'current' : 'section';
            if ($page['parent_id'] == $id) {
                $path = ($id) ? $path . $page['slug'] . '/' : $page['slug'] . '/';
                $html .= '<li class="'.$class.'"><a href="/'.$path.'">'.$page['title'].'</a>';
                $ret = $this->_buildPageTree($pages, $page['id'], $path);
                if ($ret != '') {
                    $html .= '<ul>'.$ret.'</ul>';
                }
                $html .= '</li>';
            } else {

            }
        }
        return $html;
    }
    
    /**
     * 
     * Extend Solar_Controller_Page::fetch()
     * to display csrf checks, as we are handling that ourselves.
     * 
     */
    public function fetch($spec = null)
    {
        try {
            
            // load action, info, and query properties
            $this->_load($spec);
            
            // prerun hook
            $this->_preRun();
            
            // action chain, with pre- and post-action hooks
            $this->_forward($this->_action, $this->_info);
            
            // postrun hook
            $this->_postRun();
            
            // render the view and layout, with pre- and post-render hooks
            $this->_render();
            
            // done, return the response headers, cookies, and body
            return $this->_response;
            
        } catch (Exception $e) {
            
            // an exception was thrown somewhere, attempt to rescue it
            return $this->_exceptionDuringFetch($e);
            
        }
    }
}
