<?php
/**
 * 
 * Factory class for cache adapters.
 * 
 * @category Solar
 * 
 * @package Solar_Cache
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Cache.php 3278 2008-07-30 12:47:02Z pmjones $
 * 
 */
class Solar_Cache extends Solar_Factory
{
    /**
     * 
     * User-provided configuration.
     * 
     * Keys are ...
     * 
     * `adapter`
     * : (string) The adapter class for the factory, default 
     * 'Solar_Cache_Adapter_File'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Cache = array(
        'adapter' => 'Solar_Cache_Adapter_File',
    );
}
