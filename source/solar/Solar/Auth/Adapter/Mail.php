<?php
/**
 * 
 * Authenticate against an IMAP or POP3 mail server.
 * 
 * @category Solar
 * 
 * @package Solar_Auth
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Mail.php 3850 2009-06-24 20:18:27Z pmjones $
 * 
 */
class Solar_Auth_Adapter_Mail extends Solar_Auth_Adapter
{
    /**
     * 
     * Default configuration values.
     * 
     * @config string mailbox An imap_open() mailbox string, for example
     *   "mail.example.com:143/imap" or "mail.example.com:110/pop3".
     * 
     * @var array
     * 
     */
    protected $_Solar_Auth_Adapter_Mail = array(
        'mailbox' => null,
    );
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config Configuration value overrides, if any.
     * 
     */
    public function __construct($config = null)
    {
        // make sure the IMAP extension is available
        if (! extension_loaded('imap')) {
            throw $this->_exception(
                'ERR_EXTENSION_NOT_LOADED',
                array('extension' => 'imap')
            );
        }
        
        // continue construction
        parent::__construct($config);
    }
    
    /**
     * 
     * Verifies a username handle and password.
     * 
     * @return mixed An array of verified user information, or boolean false
     * if verification failed.
     * 
     * @todo Check the server status with fsockopen().
     * 
     */
    protected function _processLogin()
    {
        $mailbox = '{' . $this->_config['mailbox'] . '}';
        $conn = @imap_open($mailbox, $this->_handle, $this->_passwd, OP_HALFOPEN);
        if (is_resource($conn)) {
            @imap_close($conn);
            return array('handle' => $this->_handle);
        } else {
            return false;
        }
    }
}
