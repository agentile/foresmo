<?php
/**
 * Foresmo_App_Index
 * Default Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile, Bryden Tweedy
 * @version   0.09
 * @since     0.05
 */
class Foresmo_App_Index extends Foresmo_App_Base {

    protected $_action_default = 'index';

    public $form;
    public $msg;

    /**
     * actionIndex
     * Default action/page
     *
     * @return void
     *
     * @access public
     * @since  0.05
     */
    public function actionIndex()
    {
        if (!$this->installed) {
            $this->_redirect('/install');
        }
        $this->_layout_default = $this->theme;
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
        if ($process=='Login') {
            $form->populate();
            $values = $form->getValues();
            $result = $this->_model->users->validUser($values);
            if ($result !== false) {
                $this->session->set('Foresmo_user_id', $result[0]['id']);
                $this->session->set('Foresmo_group_id', $result[0]['group_id']);
                $this->session->set('Foresmo_username', $result[0]['username']);
                $this->session->set(
                    'Foresmo_permissions',
                    $this->_getGroupPermissions($result[0]['group_id'])
                );
                $this->_redirect('/index');
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
        $this->_redirect('/index');
    }

    /**
     * _getGroupPermissions
     * Get the group permissions as an array to set in the session
     *
     * @access private
     * @param  $group_id
     * @return array
     */
    private function _getGroupPermissions($group_id)
    {
        $where = array('group_id = ?' => $group_id);
        $result = $this->_model->groups_permissions->fetchArray(
            array(
                'where' => $where,
                'eager' => 'permissions'
            )
        );
		return $result;
    }
}
