<?php
/**
 * 
 * SMTP adapter with "plain" authentication at connection time.
 * 
 * @category Solar
 * 
 * @package Solar_Smtp
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: PlainAuth.php 3850 2009-06-24 20:18:27Z pmjones $
 * 
 */
class Solar_Smtp_Adapter_PlainAuth extends Solar_Smtp_Adapter
{
    /**
     * 
     * Default configuration values.
     * 
     * @config string username The username for authentication.
     * 
     * @config string password The password for authentication.
     * 
     * @var array
     * 
     */
    protected $_Solar_Smtp_Adapter_PlainAuth = array(
        'username' => null,
        'password' => null,
    );
    
    /**
     * 
     * Username for authentication.
     * 
     * @var string
     * 
     */
    protected $_username;
    
    /**
     * 
     * Password for authentication.
     * 
     * @var string
     * 
     */
    protected $_password;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config Configuration value overrides, if any.
     * 
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        if ($this->_config['username']) {
            $this->_username = $this->_config['username'];
        }
        if ($this->_config['password']) {
            $this->_password = $this->_config['password'];
        }
    }
    
    /**
     * 
     * Performs AUTH PLAIN with username and password.
     * 
     * @return bool
     * 
     */
    public function auth()
    {
        if (! $this->_auth) {
            
            // issue AUTH PLAIN, timeout at 2 minutes
            $this->_send('AUTH PLAIN');
            $this->_expect(334, 120);
            
            // send the plain-text username and password
            $data = chr(0) . $this->_username
                  . chr(0) . $this->_password;
            
            $this->_send(base64_encode($data));
            
            // wait for the right response, for 2 minutes
            $this->_expect(235, 120);
            $this->_auth = true;
        }
        
        return $this->_auth;
    }
}
