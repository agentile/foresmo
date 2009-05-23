<?php
/**
 * 
 * Factory class for mail transport adapters.
 * 
 * @category Solar
 * 
 * @package Solar_Mail
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Transport.php 3278 2008-07-30 12:47:02Z pmjones $
 * 
 */
class Solar_Mail_Transport extends Solar_Factory
{
    /**
     * 
     * User-provided configuration.
     * 
     * Keys are ...
     * 
     * `adapter`
     * : (string) The class to factory.  Default is
     * 'Solar_Mail_Transport_Adapter_Phpmail'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Mail_Transport = array(
        'adapter' => 'Solar_Mail_Transport_Adapter_Phpmail',
    );
}