<?php
/**
 * 
 * Abstract role adapter.
 * 
 * @category Solar
 * 
 * @package Solar_Role
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Adapter.php 3690 2009-04-17 00:58:32Z pmjones $
 * 
 */
abstract class Solar_Role_Adapter extends Solar_Base {
    
    /**
     * 
     * User-defined configuration values.
     * 
     * Keys are ...
     * 
     * `cache`
     * : (dependency) A Solar_Cache dependency injection. Default is to create
     *   a Solar_Cache_Adapter_Session object internal to this instance to 
     *   retain the role list.
     * 
     * @var array
     * 
     */
    protected $_Solar_Role_Adapter = array(
        'cache' => array(
            'adapter' => 'Solar_Cache_Adapter_Session',
            'prefix'  => 'Solar_Role_Adapter',
        ),
    );
    
    /**
     * 
     * A cache object to retain the current user roles.
     * 
     * @var Solar_Cache_Adapter
     * 
     */
    protected $_cache;
    
    /**
     * 
     * Constructor to set up the storage adapter.
     * 
     * @param array $config User-provided configuration values.
     * 
     */
    public function __construct($config = null)
    {
        // basic config option settings
        parent::__construct($config);
        
        // cache dependency injection
        $this->_cache = Solar::dependency(
            'Solar_Cache',
            $this->_config['cache']
        );
    }
    
    /**
     * 
     * Provides magic "isRoleName()" to map to "is('role_name')".
     * 
     * @param string $method The called method name.
     * 
     * @param array $params Parameters passed to the method.
     * 
     * @return bool
     * 
     */
    public function __call($method, $params)
    {
        if (substr($method, 0, 2) == 'is') {
            // convert from isRoleName to role_name
            $role = substr($method, 2);
            $role = preg_replace('/([a-z])([A-Z])/', '$1_$2', $role);
            $role = strtolower($role);
            // call is() on the role name
            return $this->is($role);
        } else {
            throw $this->_exception('ERR_METHOD_NOT_IMPLEMENTED', array(
                'method' => $method,
                'params' => $params,
            ));
        }
    }
    
    /**
     * 
     * Load the list of roles for the given user from the adapter.
     * 
     * @param string $handle The username to load roles for.
     * 
     * @return void
     * 
     */
    public function load($handle)
    {
        // fetch the role list using the adapter-specific method
        $result = $this->fetch($handle);
        if ($result) {
            $this->setList($result);
        }
    }
    
    /**
     * 
     * Gets the list of all loaded roles for the user.
     * 
     * @return array
     * 
     */
    public function getList()
    {
        return $this->_cache->fetch('list', array());
    }
    
    /**
     * 
     * Sets the list, overriding what is there already.
     * 
     * @param array $list The list of roles to set.
     * 
     * @return void
     * 
     */
    public function setList($list)
    {
        // don't change the list if it's the same. this helps with the
        // default session cache, to keep from starting a session.
        if ($this->getList() !== $list) {
            $this->_cache->save('list', (array) $list);
        }
    }
    
    /**
     * 
     * Appends a list of roles to the existing list of roles.
     * 
     * @param array $list The list of roles to append.
     * 
     * @return void
     * 
     */
    public function addList($list)
    {
        settype($list, 'array');
        $data = $this->_cache->fetch('list', array());
        foreach ($list as $val) {
            $data[] = (string) $val;
        }
        $this->_cache->save('list', $data);
    }
    
    /**
     * 
     * Appends a single role to the existing list of roles.
     * 
     * @param string $list The role to append.
     * 
     * @return void
     * 
     */
    public function add($val)
    {
        $data = $this->_cache->fetch('list', array());
        $data[] = $val;
        $this->_cache->save('list', $data);
    }
    
    /**
     * 
     * Resets the role list to nothing.
     * 
     * @return void
     * 
     */
    public function reset()
    {
        $this->setList(array());
    }
    
    /**
     * 
     * Check to see if a user is in a role.
     * 
     * @param string $role The role to check.
     * 
     * @return bool True if the user is in the role, or false if not.
     * 
     */
    public function is($role = null)
    {
        return in_array($role, $this->getList());
    }
    
    /**
     * 
     * Check to see if a user is in any of the listed roles.
     * 
     * @param string|array $roles The role(s) to check.
     * 
     * @return bool True if the user is in any of the listed roles (a
     * logical 'or'), false if not.
     * 
     */
    public function isAny($roles = array())
    {
        // loop through all of the roles, returning 'true' the first
        // time we find a matching role.
        $list = $this->getList();
        foreach ((array) $roles as $role) {
            if (in_array($role, $list)) {
                return true;
            }
        }
        
        // we got through the whole array without finding a match.
        // therefore, user was not in any of the roles.
        return false;
    }
    
    /**
     * 
     * Check to see if a user is in all of the listed roles.
     * 
     * @param string|array $roles The role(s) to check.
     * 
     * @return bool True if the user is in all of the listed roles (a
     * logical 'and'), false if not.
     * 
     */
    public function isAll($roles = array())
    {
        // loop through all of the roles, returning 'false' the first
        // time we find the user is not in one of the roles.
        $list = $this->getList();
        foreach ((array) $roles as $role) {
            if (! in_array($role, $list)) {
                return false;
            }
        }
        
        // we got through the whole list; therefore, the user is in all
        // of the noted roles.
        return true;
    }
    
    /**
     * 
     * Adapter-specific method to find roles for loading.
     * 
     * @param string $handle User handle to get roles for.
     * 
     * @return array An array of discovered roles.
     * 
     */
    abstract public function fetch($handle);
}
