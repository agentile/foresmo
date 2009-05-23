<?php
/**
 * 
 * Factory class for reading access privileges.
 * 
 * @category Solar
 * 
 * @package Solar_Access
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Access.php 3333 2008-08-05 22:57:09Z pmjones $
 * 
 */
class Solar_Access extends Solar_Factory
{
    /**
     * 
     * User-supplied configuration values.
     * 
     * Keys are ...
     * 
     * `adapter`
     * : (string) The adapter class, for example 'Solar_Access_Adapter_Open'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Access = array(
        'adapter' => 'Solar_Access_Adapter_Open',
    );
}
