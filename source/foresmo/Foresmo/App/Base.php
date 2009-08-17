<?php
/**
 * Foresmo_App_Base
 * Foresmo App Arch Class
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
    protected $_adapter;
    protected $_modules;
    protected $_cache = null;

    public $session;
    public $connect = true;
    public $installed = false;
    public $blog_theme = 'default';
    public $blog_title = 'Foresmo Blog';
    public $pages_count;
    public $web_root;
    public $enabled_modules = array();

    /**
     * _setup
     *
     * Set variables used throughout the app here.
     */
    protected function _setup()
    {
        $this->session = Solar::factory('Solar_Session', array('class' => 'Foresmo_App'));
        $adapter = Solar_Config::get('Solar_Sql', 'adapter');
        $adapter = Solar::factory($adapter);
        try {
            $adapter->connect();
        } catch (Exception $e) {
            $this->connect = false;
        }
        if ($this->connect) {
            $this->_adapter = $adapter;
            $this->installed = (bool) Solar_Config::get('Foresmo', 'installed');
            if (!$this->installed) {
                return;
            }
            $this->web_root = Solar_Config::get('Solar', 'system') . '/content/';
            $this->_model = Solar_Registry::get('model_catalog');
            $cache_settings = Solar_Config::get('Foresmo', 'cache');
            if (isset($cache_settings['adapter'])) {
                $this->_model->_config['cache'] = $cache_settings;
                $this->_cache = Solar::factory('Solar_Cache', $cache_settings);
            }

            $results = $this->_model->options->fetchArray(
                array(
                    'where' => array(
                        'name LIKE ?' => 'blog_%'
                    )
                )
            );

            foreach ($results as $result) {
                switch ($result['name']) {
                    case 'blog_theme':
                        $this->blog_theme = $result['value'];
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
            $time_info = Foresmo::getTimeInfo();
            Foresmo::$date_format = $time_info['blog_date_format'];
            Foresmo::$timezone = $time_info['blog_timezone'];
            $this->_model->posts->published_posts_count = $this->_model->posts->getPublishedPostsCount();
            $this->_setPagesCount();
            $this->_layout_default = $this->blog_theme;
            $this->_setToken();
            $this->_modules = Solar::factory('Foresmo_Modules', $this->_model);
            $this->enabled_modules = $this->_modules->getEnabledModules();
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
     * Check user permissions for an action to be performed
     *
     * @param $action
     * @return bool
     */
    public function allowAjaxAction($action)
    {
        $user_permissions = $this->session->get('Foresmo_permissions');
        if (!is_array($user_permissions)) {
            return false;
        }
        switch ($action) {
            case 'admin_post_new':
                return (in_array('create_post', $user_permissions)) ? true : false;
            break;
            case 'admin_pages_new':
                return (in_array('create_page', $user_permissions)) ? true : false;
            break;
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
     * _loadModules
     * Load enabled modules
     *
     * @return void
     */
    protected function _loadModules()
    {

    }
}
