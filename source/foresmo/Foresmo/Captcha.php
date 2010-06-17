<?php
/**
 * Foresmo_Captcha
 * Abstract Base Captcha Class
 *
 * @author Anthony Gentile <agentile@gmail.com>
 */
abstract class Foresmo_Captcha extends Solar_Base {

    protected $_Foresmo_Captcha = array();

    protected $_error = 'Invalid Captcha!';

    protected $_session;

    /**
     * _postConstruct
     *
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_session = Solar::factory('Solar_Session', array('class' => 'Foresmo_Captcha'));
    }

    /**
     * setErrorMessage
     * Insert description here
     *
     * @param $message
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function setErrorMessage($message)
    {
        $this->_error = $message;
    }

    /**
     * getErrorMessage
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
    public function getErrorMessage()
    {
        return $this->_error;
    }

}