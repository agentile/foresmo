<?php
/**
 * Foresmo_App_Index
 * Default Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Index extends Foresmo_App_Base {

    protected $_action_default = 'main';

    public $form;
    public $form_success = false;
    public $msg;
    public $posts = array();
    public $comments_disabled = false;

    protected function _preRender()
    {
        parent::_preRender();
    }

    protected function _postRender()
    {
        parent::_postRender();
    }

    protected function _postAction()
    {
        parent::_postAction();
    }

    protected function _preRun()
    {
        parent::_preRun();
    }

    protected function _postRun()
    {
        parent::_postRun();

        if (isset($this->_registered_hooks['_postRun'][$this->_controller][$this->_action])) {
            $hooks = $this->_registered_hooks['_postRun'][$this->_controller][$this->_action];

            foreach ($hooks as $module => $module_call) {
                $module_obj = Solar::factory("Foresmo_Modules_{$module}", $this->_model);
                if (method_exists($module_obj, $module_call)) {
                    $module_obj->$module_call();
                }
            }
        }

        $this->session->setFlash('redirect', array(
            'controller' => $this->_controller,
            'action' => $this->_action,
            'params' => $this->_info,
        ));
    }

    /**
     * actionIndex
     * Default action/page
     *
     * @return void
     *
     * @access public
     * @since  0.05
     */
    public function actionMain()
    {
        $posts = array();
        $is_post = false;

        // Is this a post?
        if (!empty($this->_info)) {
            $posts = $this->_model->posts->fetchPublishedPostBySlug($this->_info[0]);
        }
        if (!empty($posts)) {
            $this->_view = 'post';
            $posts = $posts[0];
            $this->_setPostCommentForm($posts['id']);
            if ($this->form_success) {
                $posts = $this->_model->posts->fetchPublishedPostBySlug($this->_info[0]);
                $posts = $posts[0];
            }
            $this->page_title .= ' | ' . $posts['title'];
            $is_post = true;
        }

        // Is it a page?
        if (empty($posts) && !$is_post && !empty($this->_info)) {
            $posts = $this->_model->posts->fetchPublishedPageBySlug($this->_info[0]);
        }

        if (!empty($posts) && !$is_post) {
            $this->_view = 'page';
            $posts = $posts[0];
            $this->_setPostCommentForm($posts['id']);
            if ($this->form_success) {
                $posts = $this->_model->posts->fetchPublishedPageBySlug($this->_info[0]);
                $posts = $posts[0];
            }
            $this->page_title .= ' | ' . $posts['title'];
        }

        if (empty($posts) && !empty($this->_info)) {
            $this->_view = 'notfound';
            $this->msg = 'The page/post you are looking for cannot be found.';
        } elseif (empty($posts) && empty($this->_info)) {
            $posts = $this->_model->posts->fetchPublishedPosts();
        }

        $this->posts = $posts;
    }

    /**
     * actionPage
     * Get posts by page number (pagination)
     *
     * @return void
     *
     * @access public
     * @since  0.15
     */
    public function actionPage($page = null)
    {
        if (!$page || $page < 0 || $page > $this->pages_count) {
            $this->_redirect('/');
        }

        $this->posts = $this->_model->posts->fetchPublishedPostsByPage($page);
        if (empty($this->posts)) {
            $this->_view = 'notfound';
            $this->msg = 'This page number does not contain any posts/pages.';
        } else {
            $this->_view = 'main';
        }
    }

    /**
     * actionTag
     * Get posts by tag name
     *
     * @return void
     *
     * @access public
     * @since  0.05
     */
    public function actionTag()
    {
        $tags = func_get_args();
        if (empty($tags)) {
            $this->_redirect('/');
        }

        $this->posts = $this->_model->posts->fetchPostsByTag($tags);
        if (empty($this->posts)) {
            $this->_view = 'notfound';
            $this->msg = 'There are no associated posts/pages for the given tag(s)';
        } else {
            $this->_view = 'main';
        }
    }

    /**
     * actionSort
     * Sort/Get posts by date (month, day, year)
     * URL structure:
     * /sort/04/2009
     * /sort/04/11/2009
     *
     * @return void
     *
     * @access public
     * @since  0.05
     */
    public function actionSort()
    {
        $params = func_get_args();
        if (empty($params)) {
            $this->_redirect('/');
        }
        $month = (isset($params[0])) ? $params[0] : null;
        $year = (isset($params[1])) ? $params[1] : null;
        $day = null;
        // is this m/d/y format?
        if (strlen($year) == 2) {
            $day = $year;
            $year = (isset($params[2])) ? $params[2] : null;
        }

        $this->posts = $this->_model->posts->fetchPublishedPostsByDate($year, $month, $day);
        if (empty($this->posts)) {
            $this->_view = 'notfound';
            $this->msg = 'There are no associated posts/pages for the given date';
        } else {
            $this->_view = 'main';
        }
    }

    /**
     * actionLogin
     * Login Page
     *
     * @return void
     *
     * @access public
     * @since  0.05
     */
    public function actionLogin()
    {
        $form = Solar::factory('Solar_Form');

        $form->setElement('username', array(
            'name'  => 'username',
            'type'  => 'text',
            'label' => 'Username'

        ));
        $form->setElement('password', array(
            'name'  => 'password',
            'type'  => 'password',
            'label' => 'Password'

        ));

        $form->setElement('process', array(
            'type'  => 'submit',
            'label' => '',
            'value' => 'Login',
        ));

        $request = Solar::factory('Solar_Request');
        $process = $request->post('process');
        if ($process == 'Login') {
            $form->populate();
            $values = $form->getValues();
            $result = $this->_model->users->isValidUser($values);
            if ($result === true) {
                $user = $this->_model->users->fetchUserByUsername($values['username']);
                $this->session->set('Foresmo_user_id', $user['id']);
                $this->session->set('Foresmo_groups', $user['groups']);
                $this->session->set('Foresmo_username', $user['username']);
                $this->session->set('Foresmo_permissions', $user['permissions']);
                $this->session->set('Foresmo_user_info', $user['userinfo']);
                $this->_redirect('/admin');
            } else {
                $this->msg = 'Login Failed';
            }
        }

        $view = Solar::factory('Solar_View');
        $this->form = $view->form($form);
        $this->_layout = 'login';
    }

    /**
     * actionLogout
     * Handle Logout
     *
     * @return void
     *
     * @access public
     * @since  0.05
     */
    public function actionLogout()
    {
        $this->session->resetAll();
        $this->_redirect('/');
    }

    /**
     * Get Comment Form for Post and if applicable, the message.
     *
     * @param $post_id
     */
    private function _setPostCommentForm($post_id)
    {
        if ($this->_model->post_info->isCommentsDisabled($post_id)) {
            $this->msg = 'This post has commenting disabled.';
            $this->comments_disabled = true;
            return;
        }
        $form = Solar::factory('Solar_Form');

        $form->setElement('name', array(
            'name'  => 'name',
            'type'  => 'text',
            'label' => 'Name (required)',
            'filters' => array('validateNotBlank'),
        ));
        if ($this->session->get('Foresmo_username', false) === false
            || !$this->session->get('Foresmo_username')) {
            $form->setElement('email', array(
                'name'  => 'email',
                'type'  => 'text',
                'label' => 'E-mail (required, not published)',
                'filters' => array('validateNotBlank','validateEmail'),
            ));
        } else {
            $form->setElement('email', array(
                'name'  => 'email',
                'type'  => 'hidden',
                'value' => $this->_model->users->fetchEmailByID($this->session->get('Foresmo_user_id')),
                'filters' => array('validateNotBlank','validateEmail'),
            ));
        }
        $form->setElement('url', array(
            'name'  => 'url',
            'type'  => 'text',
            'label' => 'Web-site'
        ));
        $form->setElement('comment', array(
            'name'  => 'comment',
            'type'  => 'textarea',
            'label' => 'Comment',
            'filters' => array('validateNotBlank'),
        ));
        $form->setElement('token', array(
            'name'  => 'token',
            'type'  => 'hidden',
            'value' => $this->session->get('Foresmo_token'),
            'filters' => array('validateNotBlank','validateAlnum'),
        ));
        $form->setElement('post_id', array(
            'name'  => 'post_id',
            'type'  => 'hidden',
            'value' => $post_id,
            'filters' => array('validateInt'),
        ));
        $form->setElement('spam_empty', array(
            'name'  => 'spam_empty',
            'type'  => 'text',
            'value' => '',
            'class' => 'hidden',
            'filters' =>  array('validateBlank'),
        ));
        $form->setElement('process', array(
            'type'  => 'submit',
            'label' => '',
            'value' => 'Submit Comment',
        ));

        $request = Solar::factory('Solar_Request');
        $process = $request->post('process');
        if ($process == 'Submit Comment') {
            $form->populate();
            $is_valid = $form->validate();
            if ($is_valid) {
                $values = $form->getValues();
                $token_check = $this->_checkToken($values['token']);
                $registered_check = false;
                $registered = $this->_model->users->checkEmailExists($values['email']);
                if ($token_check == false) {
                    $form->feedback = 'Invalid/Stale token. Comment not submitted.';
                }
                if ($registered !== false) {
                    if ($this->session->get('Foresmo_user_id', false) === false
                        || $this->session->get('Foresmo_user_id') !== $registered['id']) {
                        $form->feedback = 'This e-mail address is registered, if you are this user, please login to comment.';
                    } else {
                        // TODO: check to make sure there current logged in user has entered his/her own e-mail
                        $registered_check = true;
                    }
                } else {
                    $registered_check = true;
                }
                if ($registered_check && $token_check) {
                    if ($this->_model->comments->isSpam($values)) {
                        $form->feedback = 'This comment has been flagged as spam and has been sent to blog admin for review.';
                        $this->_model->comments->insertComment($values, true);
                    } else {
                        $form->feedback = 'Comment posted!';
                        $this->_model->comments->insertComment($values);
                        $this->form_success = true;
                    }
                }
            } else {
                $form->feedback = 'Validation Errors!';
            }
        }

        $view = Solar::factory('Solar_View');
        $this->form = $view->form($form);
    }
}
