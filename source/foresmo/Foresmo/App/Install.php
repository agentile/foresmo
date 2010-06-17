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
    // Ajax response properties
    public $success = false;
    public $error = null;
    public $message = null;
    public $data = null;

    // POST and GET data
    protected $_post = array();
    protected $_get  = array();

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
    
    /**
     * _install
     * Install a new blog
     */
    public function _install()
    {
        $this->_post = $this->_request->post();
        
        if ($this->installed) {
            $this->error = 'Blog is already installed';
            $this->message = 'Blog is already installed';
            $this->success = false;
            return;
        }
        if (!empty($this->_post['db_type'])) {
            $db_type = ucfirst($this->_post['db_type']);
            $adapter = 'Solar_Sql_Adapter_' . $db_type;
        } else {
            $this->error = 'DB Type cannot be blank';
            $this->message = 'DB Type cannot be blank';
            $this->success = false;
            return;
        }
        Solar_Config::set('Solar_Sql', 'adapter', $adapter);
        Solar_Config::set($adapter, 'host', $this->_post['db_host']);
        Solar_Config::set($adapter, 'user', $this->_post['db_username']);
        Solar_Config::set($adapter, 'pass', $this->_post['db_password']);
        Solar_Config::set($adapter, 'name', $this->_post['db_name']);
        Solar_Config::set($adapter, 'prefix', $this->_post['db_prefix']);
        $adapter = Solar::factory($adapter);
        try {
            $adapter->connect();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->message = 'Cannot connect to database! Please ensure valid DB info.';
            $this->success = false;
            return;
        }

        $config_file = Solar::$system . '/source/foresmo/config/default.php';
        $config_content = $this->_getConfigContent();
        if(($handle = @fopen($config_file, 'w')) !== false) {
            if (@fwrite($handle, $config_content) === false) {
                fclose($handle);
                $this->error = "Cannot write to: {$config_file}. Please set the permissions to 777 for this file.";
                $this->message = "Cannot write to: {$config_file}. Please set the permissions to 777 for this file.";
                $this->success = false;
                return;
            } else {
                fclose($handle);
            }
        } else {
            $this->error = "Could not open {$config_file}, please ensure that this file exists and is writable by the server.";
            $this->message = "Could not open {$config_file}, please ensure that this file exists and is writable by the server.";
            $this->success = false;
            return;
        }

        $schema = Solar::$system . '/source/foresmo/Foresmo/Schemas/' . $db_type . '.php';
        $schema_sql = Solar_File::load($schema);
        $schema_sql = str_replace('[prefix]', $this->_post['db_prefix'], $schema_sql);
        try {
            $adapter->query($schema_sql);
        } catch (Exception $e) {
            // tables already exist?
            $this->error = $e->getMessage();
            $this->message = 'Error creating database tables, do they already exist?';
            $this->success = false;
            return;
        }

        $errors = array();
        $matches = array();
        $ret_str = '';
        $this->_post['blog_user'] = trim($this->_post['blog_user']);
        if (empty($this->_post['blog_password']) == true
            || empty($this->_post['blog_password2']) == true
            || empty($this->_post['blog_user']) == true
            || empty($this->_post['blog_title']) == true
            || empty($this->_post['blog_email']) == true) {

            $errors[] = 'No fields should be left blank!';
        }

        preg_match('/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $this->_post['blog_email'], $matches);
        if (count($matches) == 0) {
            $errors[] = 'Not a valid email address.';
        }

        if (strlen($this->_post['blog_password']) < 7) {
            $errors[] = 'The user password must be seven characters or more';
        }

        if ($this->_post['blog_password'] !== $this->_post['blog_password2']) {
            $errors[] = 'The user password fields did not match!';
        }

        if (count($errors) > 0) {
            $ret_str .= '<p class="error"><b>Validation Errors:</b></p>';
            foreach ($errors as $error) {
                $ret_str .= '<span class="error">' . $error . '</span><br />';
            }
            $this->error = $ret_str;
            $this->message = $ret_str;
            $this->success = false;
            return;
        }

        $username = $this->_post['blog_user'];
        $password = $this->_post['blog_password'];
        $hasher = new Foresmo_Hashing(8, false);
        $pwhash = $hasher->hashPassword($password);
        $email = trim($this->_post['blog_email']);

        $table = $this->_post['db_prefix'] . 'groups';
        $data = array(
            'name' => 'Admin',
        );

        $adapter->insert($table, $data);
        $last_insert_id = $adapter->lastInsertId($table, 'id');
        $permissions = array();
        $table = $this->_post['db_prefix'] . 'permissions';

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

        $data = array('name' => 'manage_modules');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $data = array('name' => 'blog_settings');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $data = array('name' => 'manage_themes');
        $adapter->insert($table, $data);
        $permissions[] = $adapter->lastInsertId($table, 'id');

        $table = $this->_post['db_prefix'] . 'groups_permissions';
        foreach($permissions as $permission) {
            $data = array(
                'group_id' => $last_insert_id,
                'permission_id' => (int) $permission,
            );
            $adapter->insert($table, $data);
        }

        $table = $this->_post['db_prefix'] . 'users';
        $data = array(
            'group_id' => $last_insert_id,
            'username'=> $username,
            'password' => $pwhash,
            'email' => strtolower($email),
        );
        $adapter->insert($table, $data);

        $table = $this->_post['db_prefix'] . 'options';
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
            'name' => 'blog_admin_theme',
            'type' => 0,
            'value' => 'default',
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_admin_theme_options',
            'type' => 0,
            'value' => serialize(array()),
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_theme_options',
            'type' => 0,
            'value' => serialize(array()),
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_title',
            'type' => 0,
            'value' => $this->_post['blog_title'],
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
            'value' => 'America/New_York',
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_posts_per_page',
            'type' => 0,
            'value' => 10,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_uid',
            'type' => 0,
            'value' => sha1($_SERVER['HTTP_HOST'] . substr(md5(uniqid(mt_rand(), TRUE)),0,12)),
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_comment_link_limit',
            'type' => 0,
            'value' => 3,
        );
        $adapter->insert($table, $data);
        $data = array(
            'name' => 'blog_comment_default_status',
            'type' => 0,
            'value' => 3,
        );
        $adapter->insert($table, $data);
        $table = $this->_post['db_prefix'] . 'posts';
        $data = array(
            'slug' => 'my-first-post',
            'content_type' => 1,
            'title' => 'My first post!',
            'content' => "Welcome to {$this->_post['blog_title']}. Look forward to new blog posts soon!",
            'excerpt' => "Welcome to {$this->_post['blog_title']}. Look forward to new blog posts soon!",
            'user_id' => 1,
            'status' => 1,
            'pubdate' => time(),
            'modified' => time(),
        );
        $adapter->insert($table, $data);
        $table = $this->_post['db_prefix'] . 'comments';
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
        $table = $this->_post['db_prefix'] . 'tags';
        $data = array(
            'tag' => 'Foresmo',
            'tag_slug' => 'foresmo',
        );
        $adapter->insert($table, $data);
        $table = $this->_post['db_prefix'] . 'posts_tags';
        $data = array(
            'post_id' => 1,
            'tag_id' => 1,
        );
        $adapter->insert($table, $data);

        $this->success = true;
        $this->message = 'Foresmo installed! Click <a href="/">here</a> to check it out! Also, don\'t forget to change the permissions of the config back to read only.';
    }
    
    /**
     * _getConfigContent
     * Get standard foresmo content to write.
     *
     * @return string
     */
    private function _getConfigContent()
    {
        return "<?php
\$system = \$config['Solar']['system'];

/**
 * ini_set values
 */
\$config['Solar']['ini_set'] = array(
    'error_reporting'   => (E_ALL | E_STRICT),
    'display_errors'    => false,
    'html_errors'       => true,
    'session.save_path' => \"\$system/tmp/session/\",
    'date.timezone'     => 'America/New_York',
);


/**
 * auto-register some default objects for common use. note that these are
 * lazy-loaded and only get created when called for the first time.
 */
\$config['Solar']['registry_set'] = array(
    'sql'              => 'Solar_Sql',
    'user'             => 'Solar_User',
    'controller_front' => 'Solar_Controller_Front',
    'model_catalog'    => 'Solar_Sql_Model_Catalog',
    'model_cache'      => array(
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
    'prefix' => '".$this->_post['db_prefix']."'
);

\$config['Solar_Sql_Model_Catalog']['classes'] = array('Foresmo_Model');

/**
 * sql connection
 */
\$config['Solar_Sql']['adapter'] = 'Solar_Sql_Adapter_".ucfirst($this->_post['db_type'])."';

\$config['Solar_Sql_Adapter_Mysql'] = array(
    'host' => '".$this->_post['db_host']."',
    'user' => '".$this->_post['db_username']."',
    'pass' => '".$this->_post['db_password']."',
    'name' => '".$this->_post['db_name']."',
);

// Foresmo settings
\$config['Foresmo']['installed'] = true;

// Foresmo Cache
\$config['Foresmo']['cache'] = array(
    // which adapter class to use
    'adapter' => 'Solar_Cache_Adapter_File',
    // where the cache files will be stored
    'path' => \"\$system/tmp/cache\",
    // the cache entry lifetime in seconds
    'life' => 1800,
);

// Authentication source
\$config['Solar_Auth'] = array(
    'adapter' => 'Solar_Auth_Adapter_Sql',
);

/**
 * front controller
 */
\$config['Solar_Controller_Front'] = array(
    'classes' => array('Foresmo_App'),
    'default' => 'index',
);";
    }
}
