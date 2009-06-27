<?php
/**
 * 
 * Factory to return an HTTP request adapter instance.
 * 
 * @category Solar
 * 
 * @package Solar_Http
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Request.php 3850 2009-06-24 20:18:27Z pmjones $
 * 
 */
class Solar_Http_Request extends Solar_Factory
{
    /**
     * 
     * Default configuration values.
     * 
     * @config string adapter The adapter class; for example, 'Solar_Http_Request_Adapter_Stream'
     *   (the default).  When the `curl` extension is loaded, the default is
     *   'Solar_Http_Request_Adapter_Curl'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Http_Request = array(
        'adapter' => 'Solar_Http_Request_Adapter_Stream',
    );
    
    /**
     * 
     * Constructor.
     * 
     * @param mixed $config User-defined configuration values.
     * 
     */
    public function __construct($config = null)
    {
        if (extension_loaded('curl')) {
            $this->_Solar_Http_Request['adapter'] = 'Solar_Http_Request_Adapter_Curl';
        }
        parent::__construct($config);
    }
}