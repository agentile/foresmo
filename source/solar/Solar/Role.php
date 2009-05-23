<?php
/**
 * 
 * Class for reading user roles and groups.
 * 
 * @category Solar
 * 
 * @package Solar_Role
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Role.php 3278 2008-07-30 12:47:02Z pmjones $
 * 
 */
class Solar_Role extends Solar_Factory
{
    /**
     * 
     * User-provided configuration values.
     * 
     * Keys are ...
     * 
     * `adapter`
     * : (string) The adapter class to use.
     * 
     * @var array
     * 
     */
    protected $_Solar_Role = array(
        'adapter' => 'Solar_Role_Adapter_None',
    );
}
