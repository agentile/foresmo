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

        $this->_post = $this->_request->post();
        $this->_get  = $this->_request->get();

        // look for ajax_action to properly dispatch
        if (isset($this->_post['ajax_action'])) {
            $action = $this->_post['ajax_action'];
        } elseif (isset($this->_get['ajax_action'])) {
            $action = $this->_get['ajax_action'];
        } else {
            $action = false;
        }

        // look for csrf_token to make sure this is a valid request
        if (isset($this->_post['csrf_token'])) {
            $token = $this->_post['csrf_token'];
        } elseif (isset($this->_get['csrf_token'])) {
            $token = $this->_get['csrf_token'];
        } else {
            $token = false;
        }

        if ($action === false) {
            $this->error = 'No ajax_action data found.';
            $this->success = false;
            $this->message = 'An AJAX action was not found in your request.';
        } elseif ($token === false) {
            $this->error = 'No csrf_token data found.';
            $this->success = false;
            $this->message = 'A CSRF token was not found in your request.';
        } elseif ($this->_getToken() !== $token) {
            $this->error = 'Invalid CSRF Token.';
            $this->success = false;
            $this->message = 'The CSRF token found in your request is invalid.';
        } else {
            $method = 'ajax_' . $action;
            if (method_exists($this, $method)) {
                $allow = true;
                $authorized = true;
                if (stristr($action, 'admin') !== false) {
                    if ($this->session->get('Foresmo_username', false) === false
                        || !$this->session->get('Foresmo_username')) {
                        $allow = false;
                    }
                }
                if ($action !== 'blog_install' && !$this->allowAjaxAction($action)) {
                    $authorized = false;
                }

                if ($allow && $authorized) {
                    try {
                        $this->$method();
                    } catch (Exception $e) {
                        $this->error = $e->getMessage();
                    }
                } elseif ($authorized){
                    $this->error = 'This request requires an appropriate login';
                    $this->success = false;
                    $this->message = 'This request requires an appropriate login';
                } elseif($allow) {
                    $this->error = 'You are not authorized to perform this action.';
                    $this->success = false;
                    $this->message = 'You are not authorized to perform this action.';
                }
            } else {
                $this->error = 'Invalid AJAX Action.';
                $this->success = false;
                $this->message = 'The AJAX Action specified could not be found.';
            }
        }

        $this->_returnResponse();
    }

    /**
     * _returnResponse
     * JSON encodes properties set by AJAX functions
     *
     * @return void
     */
    protected function _returnResponse()
    {
        $ret = array(
            'success' => $this->success,
            'error'   => $this->error,
            'message' => $this->message,
            'data'    => $this->data
        );

        $this->_response->content = json_encode($ret);
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
                $this->success = true;
                $this->data = array('output' => $module_output);
            }
        }

        $this->_returnResponse();
    }

    /**
     * ajax_admin_blog_settings
     * update blog settings
     *
     * @return string
     */
    public function ajax_admin_blog_settings()
    {
        foreach ($this->_post as $key => $value) {
            switch ($key) {
                case 'blog_title':
                    if (trim($value) != '') {
                        $this->_model->options->updateOption('blog_title', $value);
                    }
                break;
                case 'blog_date_format':
                    if (!isset($this->_post['blog_date_format_preset']) && trim($value) != '') {
                        $this->_model->options->updateOption('blog_date_format', $value);
                    }
                break;
                case 'blog_date_format_preset':
                    if (trim($value) != '') {
                        $this->_model->options->updateOption('blog_date_format', $value);
                    }
                break;
            }
        }

        $this->success = true;
        $this->message = 'Successfully updated settings';
    }

    /**
     * ajax_admin_pages_new
     * New page post
     *
     * @return string
     */
    public function ajax_admin_page_new()
    {
        return $this->addContent();
    }

    /**
     * ajax_admin_post_new
     * New blog post
     *
     * @return string
     */
    public function ajax_admin_post_new()
    {
        return $this->addContent();
    }

    /**
     * ajax_admin_page_edit
     * Edit blog page
     *
     * @return string
     */
    public function ajax_admin_page_edit()
    {
        return $this->editContent();
    }

    /**
     * ajax_admin_post_edit
     * Edit blog post
     *
     * @return string
     */
    public function ajax_admin_post_edit()
    {
        return $this->editContent();
    }

    /**
     * ajax_admin_theme_update
     * Update blog theme
     *
     * @return void
     */
    public function ajax_admin_theme_update()
    {
        return $this->_updateTheme(false);
    }

    /**
     * ajax_admin_theme_admin_update
     * Update admin theme
     *
     * @return void
     */
    public function ajax_admin_theme_admin_update()
    {
        return $this->_updateTheme(true);
    }

    /**
     * _updateTheme
     * Update theme
     *
     * @param bool $admin admin theme?
     * @return void
     */
    protected function _updateTheme($admin = false)
    {
        if ($admin) {
            $this->_model->options->updateTheme($this->_post['theme'], true);
            $msg = 'Successfully changed admin theme';
        } else {
            $this->_model->options->updateTheme($this->_post['theme']);
            $msg = 'Successfully changed blog theme';
        }
        $this->success = true;
        $this->message = $msg;
    }

    /**
     * addContent
     * New blog post/page
     *
     * @return void
     */
    public function addContent()
    {
        $errors = array();
        if (!isset($this->_post['post_title']) || $this->validate('validateBlank', $this->_post['post_title'])) {
            $errors[] = 'Title cannot be blank.';
        }
        if (!isset($this->_post['post_content']) || $this->validate('validateBlank', $this->_post['post_title'])) {
            $errors[] = 'Content cannot be blank.';
        }
        $this->_post['post_slug'] = Foresmo::makeSlug($this->_post['post_title']);
        if (in_array(strtolower($this->_post['post_slug']), $this->_restricted_names)) {
            $errors[] = 'The slug for this post/page "'.$this->_post['post_slug'].'" is restricted. Please choose a different slug/title';
        }
        if (count($errors) > 0) {
            $message = implode('<br/>', $errors);
            $this->success = false;
            $this->message = $message;
            return;
        }

        if (!isset($this->_post['post_excerpt']) || $this->validate('validateBlank', $this->_post['post_excerpt'])) {
            $this->_post['post_excerpt'] = Foresmo::makeExcerpt($this->_post['post_content'], 60, '...');
        }

        $last_insert_id = $this->_model->posts->insertContent($this->_post);
        if (!$this->validate('validateBlank', $this->_post['post_tags'])) {
            $tags = explode(',', rtrim(trim($this->_post['post_tags']), ','));
            foreach ($tags as $key => $tag) {
                $tags[$key] = trim($tag);
            }
            $this->_model->posts_tags->insertContentTags($last_insert_id, $tags);
        }
        if (isset($this->_post['post_comments_disabled']) && $this->_post['post_comments_disabled'] == 'true') {
            $this->_model->post_info->insertCommentsDisabled($last_insert_id, true);
        } else {
            $this->_model->post_info->insertCommentsDisabled($last_insert_id, false);
        }

        if ((int) $this->_post['post_type'] == 1) {
            $message = "Successly created new post! <a href=\"/{$this->_post['post_slug']}\">View post</a>.";
        } elseif ((int) $this->_post['post_type'] == 2) {
            $message = "Successly created new page! <a href=\"/{$this->_post['post_slug']}\">View page</a>.";
        }

        $this->success = true;
        $this->data = array('id' => $last_insert_id);
        $this->message = $message;
    }

    /**
     * editContent
     * Edit post/page
     *
     * @return void
     */
    public function editContent()
    {
        $errors = array();
        if (!isset($this->_post['post_title']) || $this->validate('validateBlank', $this->_post['post_title'])) {
            $errors[] = 'Title cannot be blank.';
        }
        if (!isset($this->_post['post_content']) || $this->validate('validateBlank', $this->_post['post_title'])) {
            $errors[] = 'Content cannot be blank.';
        }
        $this->_post['id'] = (int) $this->_post['id'];
        $this->_post['post_slug'] = $this->_model->posts->fetchContentValue($this->_post['id'], 'slug');

        if (in_array(strtolower($this->_post['post_slug']), $this->_restricted_names)) {
            $errors[] = 'The slug for this post/page "'.$this->_post['post_slug'].'" is restricted. Please choose a different slug/title';
        }
        if (count($errors) > 0) {
            $message = implode('<br/>', $errors);
            $this->success = false;
            $this->message = $message;
            return;
        }

        if (!isset($this->_post['post_excerpt'])) {
            $this->_post['post_excerpt'] = '';
        }

        $this->_model->posts->updateContent($this->_post);

        if (!$this->validate('validateBlank', $this->_post['post_tags'])) {
            $tags = explode(',', rtrim(trim($this->_post['post_tags']), ','));
            foreach ($tags as $key => $tag) {
                $tags[$key] = trim($tag);
            }
            $this->_model->posts_tags->updateContentTags($this->_post['id'], $tags);
        }
        if (isset($this->_post['post_comments_disabled']) && $this->_post['post_comments_disabled'] == 'true') {
            $this->_model->post_info->updateCommentsDisabled($this->_post['id'], true);
        } else {
            $this->_model->post_info->updateCommentsDisabled($this->_post['id'], false);
        }

        if ((int) $this->_post['post_type'] == 1) {
            $message = "Successly edited post! <a href=\"/{$this->_post['post_slug']}\">View post</a>.";
        } elseif ((int) $this->_post['post_type'] == 2) {
            $message = "Successly edited page! <a href=\"/{$this->_post['post_slug']}\">View page</a>.";
        }

        $this->success = true;
        $this->data = array('id' => $this->_post['id']);
        $this->message = $message;
    }

    /**
     * ajax_admin_modules_change_status
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
    public function ajax_admin_modules_change_status()
    {
        switch ($this->_post['action']) {
            case 'enable':
                foreach ($this->_post['modules'] as $id) {
                    $this->_model->modules->enableModuleByID($id);
                }
            break;
            case 'disable':
                foreach ($this->_post['modules'] as $id) {
                    $this->_model->modules->disableModuleByID($id);
                }
            break;
            case 'install':
                foreach ($this->_post['modules'] as $id) {
                    $this->_modules->installModuleByID($id);
                }
            break;
            case 'uninstall':
                foreach ($this->_post['modules'] as $id) {
                    $this->_modules->uninstallModuleByID($id);
                }
            break;
        }

        $this->success = true;
    }

    /**
     * ajax_blog_install
     * This ajax action handles blog installation
     *
     * @param $this->_post
     * @return string
     */
    public function ajax_blog_install()
    {
        $installer = new Foresmo_App_Install();
        $installer->_install();
        $this->error = $installer->error;
        $this->data = $installer->data;
        $this->success = $installer->success;
        $this->message = $installer->message;
    }
}
