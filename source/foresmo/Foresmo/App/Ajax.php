<?php
/**
 * Foresmo_App_Ajax
 * Ajax Dispatcher/Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Ajax extends Foresmo_App_Base {

    protected $_action_default = 'index';

    public $random_str;

    /**
     * actionIndex
     * Ajax dispatcher
     *
     * @param $param
     *
     * @return void
     */
    public function actionIndex($param = null)
    {
        $this->_layout = null;
        $this->_view = null;
        $post_data = $this->_request->post();
        if (isset($post_data['ajax_action'])) {
            $method = 'ajax_' . $post_data['ajax_action'];
            if (stristr($post_data['ajax_action'], 'admin_')) {
                if ($this->session->get('Foresmo_username', false) === false
                    || !$this->session->get('Foresmo_username')) {
                    $ret = array(
                        'success' => false,
                        'message' => 'Please login <a href="/login">here</a>.',
                    );
                    $this->_response->content = json_encode($ret);
                    return;
                }
                if (!$this->allowAjaxAction($post_data['ajax_action'])) {
                    $ret = array(
                        'success' => false,
                        'message' => 'You are not authorized to perform this action.',
                    );
                    $this->_response->content = json_encode($ret);
                    return;
                }
            }
            $this->_response->content = self::$method($post_data);
        }
    }

    /**
     * actionModule
     * Ajax for Modules dispatcher
     *
     * @param $param
     *
     * @return void
     */
    public function actionModule()
    {
        $this->_layout = null;
        $this->_view = null;
        $f_args = func_get_args();
        $ret = array(
            'success' => false,
            'message' => '',
        );
        // Check if module exists and is enabled
        if (isset($f_args[0]) && $this->_model->modules->isEnabled($f_args[0])) {
            $module_name = ucfirst(strtolower($f_args[0]));
            array_shift($f_args);
            $data = array(
                'POST' => $this->_request->post(),
                'GET' => $this->_request->get(),
                'PARAMS' => $f_args,
            );

            $module_output = $this->_modules->processAjaxRequest($module_name, $data);
            if ($module_output && $module_output != '') {
                $ret = array(
                    'success' => true,
                    'message' => $module_output,
                );
            }
        }
        $this->_response->content = json_encode($ret);
    }

    /**
     * ajax_admin_pages_new
     * New page post
     *
     * @param $post_data
     * @return string
     */
    public function ajax_admin_pages_new($post_data)
    {
        return $this->ajax_admin_new_content($post_data);
    }

    /**
     * ajax_admin_post_new
     * New blog post
     *
     * @param $post_data
     * @return string
     */
    public function ajax_admin_post_new($post_data)
    {
        return $this->ajax_admin_new_content($post_data);
    }

    /**
     * ajax_admin_new_content
     * New blog post
     *
     * @param $post_data
     * @return string
     */
    public function ajax_admin_new_content($post_data)
    {
        $errors = array();
        if (!isset($post_data['post_title']) || $this->validate('validateBlank', $post_data['post_title'])) {
            $errors[] = 'Title cannot be blank.';
        }
        if (!isset($post_data['post_content']) || $this->validate('validateBlank', $post_data['post_title'])) {
            $errors[] = 'Content cannot be blank.';
        }
        $post_data['post_slug'] = Foresmo::makeSlug($post_data['post_title']);
        if (in_array(strtolower($post_data['post_slug']), $this->_restricted_names)) {
            $errors[] = 'The slug for this post/page "'.$post_data['post_slug'].'" is restricted. Please choose a different slug/title';
        }
        if (count($errors) > 0) {
            $message = implode('<br/>', $errors);
            $ret = array(
                'success' => false,
                'message' => $message,
            );
        } else {
            $last_insert_id = $this->_model->posts->insertNewPost($post_data);
            if (!$this->validate('validateBlank', $post_data['post_tags'])) {
                $tags = explode(',', rtrim(trim($post_data['post_tags']), ','));
                foreach ($tags as $key => $tag) {
                    $tags[$key] = trim($tag);
                }
                $this->_model->posts_tags->insertPostTags($last_insert_id, $tags);
            }
            if (isset($post_data['post_comments_disabled']) && $post_data['post_comments_disabled'] == 'true') {
                $this->_model->post_info->insertCommentsDisabled($last_insert_id, true);
            } else {
                $this->_model->post_info->insertCommentsDisabled($last_insert_id, false);
            }

            if ((int) $post_data['post_type'] == 1) {
                $message = "Successly created new post! <a href=\"/{$post_data['post_slug']}\">View post</a>.";
            } elseif ((int) $post_data['post_type'] == 2) {
                $message = "Successly created new page! <a href=\"/{$post_data['post_slug']}\">View page</a>.";
            }

            $ret = array(
                'success' => true,
                'id' => $last_insert_id,
                'message' => $message,
            );
        }
        return json_encode($ret);
    }

    /**
     * ajax_blog_install
     * This ajax action handles blog installation
     *
     * @param $post_data
     * @return string
     */
    public function ajax_blog_install($post_data)
    {
        if ($this->installed) {
            return 'Blog is already installed!';
        }
        if (!empty($post_data['db_type'])) {
            $db_type = ucfirst($post_data['db_type']);
            $adapter = 'Solar_Sql_Adapter_' . $db_type;
        } else {
            return 'DB Type cannot be blank!';
        }
        Solar_Config::set('Solar_Sql', 'adapter', $adapter);
        Solar_Config::set($adapter, 'host', $post_data['db_host']);
        Solar_Config::set($adapter, 'user', $post_data['db_username']);
        Solar_Config::set($adapter, 'pass', $post_data['db_password']);
        Solar_Config::set($adapter, 'name', $post_data['db_name']);
        Solar_Config::set($adapter, 'prefix', $post_data['db_prefix']);
        $adapter = Solar::factory($adapter);
        try {
            $adapter->connect();
        } catch (Exception $e) {
            return 'Cannot connect to database! Please ensure valid DB info.';
        }

        $this->random_str = Foresmo::randomString(18);
        $config_file = Solar::$system . '/config/Solar.config.php';
        $config_content = $this->_getConfigContent($post_data);
        if(($handle = @fopen($config_file, 'w')) !== false) {
            if (@fwrite($handle, $config_content) === false) {
                fclose($handle);
                return "Cannot write to: {$config_file}. Please set the permissions to 777 for this file.";
            } else {
                fclose($handle);
            }
        } else {
            return "Could not open {$config_file}, please ensure that this file exists and is writable.";
        }

        $schema = Solar::$system . '/source/foresmo/Foresmo/Schemas/' . $db_type . '.php';
        $schema_sql = Solar_File::load($schema);
        $schema_sql = str_replace('[prefix]', $post_data['db_prefix'], $schema_sql);
        try {
            $adapter->query($schema_sql);
        } catch (Exception $e) {
            // tables already exist?
        }

        $errors = array();
        $matches = array();
        $ret_str = '';
        $post_data['blog_user'] = trim($post_data['blog_user']);
        if (empty($post_data['blog_password']) == true
            || empty($post_data['blog_password2']) == true
            || empty($post_data['blog_user']) == true
            || empty($post_data['blog_title']) == true
            || empty($post_data['blog_email']) == true) {

            $errors[] = 'No fields should be left blank!';
        }

        preg_match('/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $post_data['blog_email'], $matches);
        if (count($matches) == 0) {
            $errors[] = 'Not a valid email address.';
        }

        if (strlen($post_data['blog_password']) < 7) {
            $errors[] = 'The user password must be seven characters or more';
        }

        if ($post_data['blog_password'] !== $post_data['blog_password2']) {
            $errors[] = 'The user password fields did not match!';
        }

        if (count($errors) > 0) {
            $ret_str .= '<p class="error"><b>Validation Errors:</b></p>';
            foreach ($errors as $error) {
                $ret_str .= '<span class="error">' . $error . '</span><br />';
            }
            return $ret_str;
        }

        $username = $post_data['blog_user'];
        $password = $post_data['blog_password'];
        $password = md5($this->random_str . $password);
        $email = trim($post_data['blog_email']);

        $table = $post_data['db_prefix'] . 'groups';
        $data = array(
            'name' => 'Admin',
        );

        $adapter->insert($table, $data);
        $last_insert_id = $adapter->lastInsertId($table, 'id');
        $permissions = array();
        $table = $post_data['db_prefix'] . 'permissions';

        $data = array('name' => 'create_post');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $data = array('name' => 'edit_post');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $data = array('name' => 'delete_post');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $data = array('name' => 'create_page');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $data = array('name' => 'edit_page');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $data = array('name' => 'delete_page');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $table = $post_data['db_prefix'] . 'groups_permissions';
        foreach($permissions as $permission) {
            $data = array(
                'group_id' => $last_insert_id,
                'permission_id' => (int) $permission,
            );
            $adapter->insert($table, $data);
        }

        $table = $post_data['db_prefix'] . 'users';
        $data = array(
            'group_id' => $last_insert_id,
            'username'=> $username,
            'password' => $password,
            'email' => strtolower($email),
        );
        $adapter->insert($table, $data);

        $table = $post_data['db_prefix'] . 'options';
        $data = array(
            'name' => 'blog_installed',
            'type' => 1,
            'value' => time(),
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_theme',
            'type' => 0,
            'value' => 'default',
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_title',
            'type' => 0,
            'value' => $post_data['blog_title'],
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_date_format',
            'type' => 0,
            'value' => 'F j, Y, g:ia',
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_timezone',
            'type' => 0,
            'value' => '-4:00',
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_posts_per_page',
            'type' => 0,
            'value' => 10,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_comment_link_limit',
            'type' => 0,
            'value' => 3,
        );
        $adapter->insert($table, $data);
        $table = $post_data['db_prefix'] . 'posts';
        $data = array(
            'slug' => 'my-first-post',
            'content_type' => 1,
            'title' => 'My first post!',
            'content' => "Welcome to {$post_data['blog_title']}. Look forward to new blog posts soon!",
            'user_id' => 1,
            'status' => 1,
            'pubdate' => time(),
            'modified' => time(),
        );
        $adapter->insert($table, $data);
        $table = $post_data['db_prefix'] . 'comments';
        $data = array(
            'post_id' => 1,
            'name' => 'Foresmo',
            'email' => 'foresmo@foresmo.com',
            'url' => 'http://foresmo.com',
            'ip' => sprintf("%u", ip2long('192.168.0.1')),
            'content' => 'Congratulations!',
            'status' => 1,
            'date' => time(),
            'type' => 0,
        );
        $adapter->insert($table, $data);
        $table = $post_data['db_prefix'] . 'tags';
        $data = array(
            'tag' => 'Foresmo',
            'tag_slug' => 'foresmo',
        );
        $adapter->insert($table, $data);
        $table = $post_data['db_prefix'] . 'posts_tags';
        $data = array(
            'post_id' => 1,
            'tag_id' => 1,
        );
        $adapter->insert($table, $data);
        $table = $post_data['db_prefix'] . 'modules';
        $data = array(
            'name' => 'Pages',
            'enabled' => 1,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'Search',
            'enabled' => 1,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'Calendar',
            'enabled' => 1,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'Tags',
            'enabled' => 1,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'Links',
            'enabled' => 1,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'Archives',
            'enabled' => 1,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'Flickr',
            'enabled' => 0,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'Twitter',
            'enabled' => 0,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'Sections',
            'enabled' => 0,
        );
        $adapter->insert($table, $data);
        $table = $post_data['db_prefix'] . 'module_info';
        $data = array(
            'module_id' => 3,
            'name' => 'start_of_week',
            'type' => 0,
            'value' => 0,
        );
        $adapter->insert($table, $data);
        if ($db_type == 'Mysql') {
            $data = array(
                'module_id' => 2,
                'name' => 'search_adapter',
                'type' => 0,
                'value' => 'mysql',
            );
        } else {
            $data = array(
                'module_id' => 2,
                'name' => 'search_adapter',
                'type' => 0,
                'value' => 'default',
            );
        }
        $adapter->insert($table, $data);
        $data = array(
            'module_id' => 2,
            'name' => 'search_adapter_settings',
            'type' => 0,
            'value' => 'a:5:{s:7:"Default";a:0:{}s:6:"Google";a:0:{}s:5:"Mysql";a:0:{}s:6:"Lucene";a:0:{}s:5:"Sphinx";a:0:{}}',
        );
        $adapter->insert($table, $data);
        return 'Foresmo installed! Click <a href="/">here</a> to check it out! Also, don\'t forget to change the permissions of the config back to read only.';
    }

    /**
     * _getConfigContent
     * Get Solar.config.php content to write.
     *
     * @param $post_data
     * @access private
     * @return string
     */
    private function _getConfigContent($post_data)
    {
        return "<?php
/**
 * all config values go in this array, which will be returned at the end of
 * this script
 */
\$config = array();


/**
 * system and autoload-include directories
 */
\$system = dirname(dirname(__FILE__));
\$config['Solar']['system']  = \$system;


/**
 * ini_set values
 */
\$config['Solar']['ini_set'] = array(
    'error_reporting'   => (E_ALL | E_STRICT),
    'display_errors'    => false,
    'html_errors'       => true,
    'session.save_path' => \"\$system/tmp/session/\",
    'date.timezone'     => 'UTC',
);


/**
 * auto-register some default objects for common use. note that these are
 * lazy-loaded and only get created when called for the first time.
 */
\$config['Solar']['registry_set'] = array(
    'sql'           => 'Solar_Sql',
    'user'          => 'Solar_User',
    'model_catalog' => 'Solar_Sql_Model_Catalog',
    'model_cache'   => array(
        'Solar_Cache',
        array(
            'adapter' => 'Solar_Cache_Adapter_File',
            'path' => \"\$system/tmp/cache\",
            'hash' => false,
            'mode' => 0777,
        )
    ),
);

\$config['Solar_Sql_Model'] = array(
    'cache' => 'model_cache',
    'auto_cache' => true,
    'prefix' => '".$post_data['db_prefix']."'
);

\$config['Solar_Sql_Model_Catalog']['classes'] = array('Foresmo_Model');

/**
 * sql connection
 */
\$config['Solar_Sql']['adapter'] = 'Solar_Sql_Adapter_".ucfirst($post_data['db_type'])."';

\$config['Solar_Sql_Adapter_Mysql'] = array(
    'host' => '".$post_data['db_host']."',
    'user' => '".$post_data['db_username']."',
    'pass' => '".$post_data['db_password']."',
    'name' => '".$post_data['db_name']."',
);

// Foresmo settings
\$config['Foresmo']['installed'] = true;

// Foresmo Cache
\$config['Foresmo']['cache'] = array(
    // which adapter class to use
    'adapter' => 'Solar_Cache_Adapter_File',
    // where the cache files will be stored
    'path' => '/tmp/Solar_Cache/',
    // the cache entry lifetime in seconds
    'life' => 1800,
);

// Authentication source
\$config['Solar_Auth'] = array(
    'adapter' => 'Solar_Auth_Adapter_Sql',
);

// Salt for password - change to something unique and strong.
\$config['Solar_Auth_Adapter_Sql']['salt'] = '".$this->random_str."';


/**
 * front controller
 */
\$config['Solar_Controller_Front'] = array(
    'classes' => array('Foresmo_App'),
    'default' => 'index',
);

/**
 * done!
 */
return \$config;
        ";
    }
}
