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
    public $query_string = '?';

    /**
     * _preRender
     * Insert description here
     * @return
     */
    protected function _preRender()
    {
        parent::_preRender();
        // build page tree
        $this->buildPageTree();
    }

    /**
     * _postRender
     * Insert description here
     *
     * @return
     */
    protected function _postRender()
    {
        parent::_postRender();
    }

    /**
     * _postAction
     * Insert description here
     *
     * @return
     */
    protected function _postAction()
    {
        parent::_postAction();
    }

    /**
     * _preRun
     * Insert description here
     *
     * @return
     */
    protected function _preRun()
    {
        parent::_preRun();
    }

    /**
     * _postRun
     * Insert description here
     * @return
     */
    protected function _postRun()
    {
        parent::_postRun();
    }

    /**
     * actionMain
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
            $posts = $this->_model->posts->fetchViewablePostBySlug($this->_info[0]);
        }
        if (!empty($posts)) {
            $posts = Foresmo_Modules::hook('post', $posts);
            $this->_view = 'post';
            $this->_setPostCommentForm($posts['id']);
            if ($this->form_success) {
                $posts = $this->_model->posts->fetchViewablePostBySlug($this->_info[0]);
            }
            $this->page_title .= ' | ' . $posts['title'];
            $is_post = true;
        }

        // Is it a page?
        if (empty($posts) && !$is_post && !empty($this->_info)) {
            $posts = $this->_model->posts->fetchViewablePageBySlug($this->_info);
        }

        if (!empty($posts) && !$is_post) {
            $posts = Foresmo_Modules::hook('page', $posts);
            $this->_view = 'page';
            $this->_setPostCommentForm($posts['id']);
            if ($this->form_success) {
                $posts = $this->_model->posts->fetchViewablePageBySlug($this->_info[0]);
            }
            $this->page_title .= ' | ' . $posts['title'];
        }

        if (empty($posts) && !empty($this->_info)) {
            $this->_response->setStatusCode(404);
            $this->_view = 'notfound';
            $this->msg = 'The page/post you are looking for cannot be found.';
        } elseif (empty($posts) && empty($this->_info)) {
            $posts = $this->_model->posts->fetchPublishedPosts();
            $posts = Foresmo_Modules::hook('main', $posts);
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
            $this->_response->setStatusCode(404);
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
        $page = (int) $this->_request->get('page');
        if ($page <= 0) {
            $page = 1;
        }

        $op = strtoupper($this->_request->get('op'));
        if ($op !== 'OR') {
            $op = 'AND';
        } else {
            $this->query_string .= 'op=OR&';
        }

        $tags = func_get_args();
        if (empty($tags)) {
            $this->_redirect('/');
        }

        $this->posts = $this->_model->posts->fetchContentByTag($tags, $op, $page);

        if (empty($this->posts)) {
            $this->_view = 'notfound';
            $this->_response->setStatusCode(404);
            $this->msg = 'There are no associated posts/pages for the given tag(s)';
        } else {
            $count = $this->_model->posts->fetchContentByTagCount($tags);
            $this->_setPagesCount($this->_model->posts->posts_per_page, $count);
            $this->_view = 'tag';
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
        $page = (int) $this->_request->get('page');
        if ($page <= 0) {
            $page = 1;
        }

        $params = func_get_args();
        if (empty($params)) {
            $this->_redirect('/');
        }
        $month = (isset($params[0])) ? $params[0] : null;
        $year = (isset($params[1])) ? $params[1] : null;
        $day = null;

        // is this y format
        if ($year == null && strlen($month) == 4) {
            $year = $month;
            $month = null;
        }
        // is this m/d/y format?
        if (strlen($year) <= 2) {
            $day = $year;
            $year = (isset($params[2])) ? $params[2] : null;
        }



        $this->posts = $this->_model->posts->fetchPublishedPostsByDate($year, $month, $day, $page);
        if (empty($this->posts)) {
            $this->_view = 'notfound';
            $this->_response->setStatusCode(404);
            $this->msg = 'There are no associated posts/pages for the given date';
        } else {
            $count = $this->_model->posts->fetchPublishedPostsByDateCount($year, $month, $day);
            $this->_setPagesCount($this->_model->posts->posts_per_page, $count);
            $this->_view = 'main';
        }
    }

    /**
     * actionFeed
     * RSS Feed
     *
     * @return void
     *
     * @access public
     * @since  0.05
     */
    public function actionFeed()
    {
        $this->_layout = null;
        $this->_view = null;
        $this->_format = 'atom';
        $uri = Solar::factory('Solar_Uri');
        $url = $uri->get(true);

        if (empty($this->_info)) {
            $posts = $this->_model->posts->fetchPublishedPosts();
            $title = $this->blog_title;
        } else {
            $op = strtoupper($this->_request->get('op'));
            if ($op !== 'OR') {
                $op = 'AND';
            }

            $title = $this->blog_title . ' | ' . implode(', ', $this->_info);
            $posts = $this->_model->posts->fetchPostsByTag($this->_info, $op, 1);
        }

        $rss = Solar::factory('Foresmo_Rss', array(
            'title' => $title,
            'link_self' => $url,
            'id' => $this->blog_uid,
            )
        );

        foreach ($posts as $k => $post) {
            $tags = array();
            foreach ($post['tags'] as $tag) {
                $tags[] = $tag['tag'];
            }
            $rss->addEntry(array(
                'title' => $post['title'],
                'link' => array('rel' => 'alternate', 'href' => '/' . $post['slug']),
                'author' => array('name' => $post['users']['username'], 'uri' => $url),
                'updated' => date('c', strtotime($post['modified'])),
                'published' => date('c', strtotime($post['pubdate'])),
                'category' => $tags,
                'content' => array('type' => 'html', 'content' => $post['content']),
            ));
        }

        $this->_response->content = $rss->getFeed();
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
        if ($this->session->get('Foresmo_username', false) !== false
            && $this->session->get('Foresmo_username')) {
            $this->_redirect('/admin');
        }

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

        if ($this->session->get('captcha_required', false)) {
            $captcha = Solar::factory('Foresmo_Captcha_Word');
            $captcha_info = $captcha->generate();

            $form->setElement('captcha_key', array(
                'name'  => 'captcha_key',
                'type'  => 'hidden',
                'value' => $captcha_info['key']
            ));

            $form->setElement('captcha', array(
                'name'  => 'captcha',
                'type'  => 'text',
                'label' => 'Captcha - Please type in the following word: ' . $captcha_info['word']
            ));
        }

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

            // do rate limiting checks
            $this->_clientFloodCheck();
            $this->_injectDelay($this->response_delay);

            if ($this->session->get('captcha_required', false)) {
                $captcha = Solar::factory('Foresmo_Captcha_Word');
                $info = array('key' => $values['captcha_key'], 'word' => $values['captcha']);
                if ($captcha->isValid($info)) {
                    $valid_captcha = true;
                } else {
                    $valid_captcha = false;
                    // increment flood checks
                    $this->_incrementAccountLoginAttempts($values['username']);
                    $this->_incrementClientLoginAttempts();
                    $this->msg = $captcha->getErrorMessage();
                }
                $form->setValue('captcha_key', $captcha_info['key']);
            }

            $this->_accountFloodCheck($values['username']);

            if (!$this->session->get('captcha_required', false) || ($this->session->get('captcha_required', false) && $valid_captcha)) {
                $result = $this->_model->users->isValidUser($values);
                if ($result === true) {
                    $this->session->set('captcha_required', false);
                    $user = $this->_model->users->fetchUserByUsername($values['username']);
                    $this->session->set('Foresmo_user_id', $user['id']);
                    $this->session->set('Foresmo_groups', $user['groups']);
                    $this->session->set('Foresmo_username', $user['username']);
                    $this->session->set('Foresmo_permissions', $user['groups']['permissions']);
                    $this->session->set('Foresmo_user_info', $user['userinfo']);
                    $this->_redirect('/admin');
                } else {
                    // increment flood checks
                    $this->_incrementAccountLoginAttempts($values['username']);
                    $this->_incrementClientLoginAttempts();

                    $this->msg = 'Login Failed';
                }
            }
        }

        $this->form = $form;
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
                        if ($this->_model->comments->default_status == 3) {
                            $form->feedback = 'Comment posted, pending admin approval!';
                        } else {
                            $form->feedback = 'Comment posted!';
                        }
                        $this->_model->comments->insertComment($values);
                        $this->form_success = true;
                    }
                }
            } else {
                $form->feedback = 'Validation Errors!';
            }
        }

        $this->form = $form;
    }

    // TODO: Use router for this
    /**
     * _notFound
     * Insert description here
     *
     * @param $action
     * @param $params
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _notFound($action, $params = null)
    {
        $this->_info = $params;
        array_unshift($this->_info, $action);
        $this->actionMain();
    }
}
