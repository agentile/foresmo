<?php
/**
 * 
 * Factory class for SMTP connections.
 * 
 * @category Solar
 * 
 * @package Solar_Smtp
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Smtp.php 3278 2008-07-30 12:47:02Z pmjones $
 * 
 */
class Solar_Smtp extends Solar_Factory
{
    /**
     * 
     * User-provided configuration.
     * 
     * Keys are ...
     * 
     * `adapter`
     * : (string) The class to factory, for example 'Solar_Smtp_Adapter_NoAuth'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Smtp = array(
        'adapter' => 'Solar_Smtp_Adapter_NoAuth',
    );
}