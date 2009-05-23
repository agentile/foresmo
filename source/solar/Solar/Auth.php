<?php
/**
 * 
 * Factory class for authentication adapters.
 * 
 * @category Solar
 * 
 * @package Solar_Auth
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Auth.php 3278 2008-07-30 12:47:02Z pmjones $
 * 
 */
class Solar_Auth extends Solar_Factory
{
    /**
     * 
     * The user is anonymous/unauthenticated (no attempt has been made to 
     * authenticate).
     * 
     * @const string
     * 
     */
    const ANON = 'ANON';
    
    /**
     * 
     * The max time for authentication has expired.
     * 
     * @const string
     * 
     */
    const EXPIRED = 'EXPIRED';
    
    /**
     * 
     * The authenticated user has been idle for too long.
     * 
     * @const string
     * 
     */
    const IDLED = 'IDLED';
    
    /**
     * 
     * The user is authenticated and has not timed out.
     * 
     * @const string
     * 
     */
    const VALID = 'VALID';
    
    /**
     * 
     * The user attempted authentication but failed.
     * 
     * @const string
     * 
     */
    const WRONG = 'WRONG';
    
    /**
     * 
     * User-supplied configuration values.
     * 
     * Keys are ...
     * 
     * `adapter`
     * : (string) The adapter class, for example 'Solar_Auth_Adapter_File'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Auth = array(
        'adapter' => 'Solar_Auth_Adapter_None',
    );
}
