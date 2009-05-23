<?php
/**
 * 
 * Manipulates generates public URI strings.
 * 
 * This class is functionally identical to Solar_Uri, except that it
 * automatically adds a prefix to the "path" portion of all URIs.  This
 * makes it easy to work with URIs for public Solar resources.
 * 
 * Use the Solar_Uri_Public::$_config key for 'path' to specify
 * the path prefix leading to the public resource directory, if any.
 * 
 * @category Solar
 * 
 * @package Solar_Uri
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Public.php 3153 2008-05-05 23:14:16Z pmjones $
 * 
 */
class Solar_Uri_Public extends Solar_Uri
{
    /**
     * 
     * User-provided configuration values.
     * 
     * Keys are ...
     * 
     * `path`
     * : (string) A path prefix specifically for public resources, for example '/public/'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Uri_Public = array(
        'path' => '/public/',
    );
}
