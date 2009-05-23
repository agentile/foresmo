<?php
/**
 * 
 * Stores cache entries to the current user session.
 * 
 * @category Solar
 * 
 * @package Solar_Cache
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Var.php 3617 2009-02-16 19:47:30Z pmjones $
 * 
 */
class Solar_Cache_Adapter_Session extends Solar_Cache_Adapter
{
    /**
     * 
     * Cache entries.
     * 
     * @var Solar_Session
     * 
     */
    protected $_entries;
    
    /**
     * 
     * Expiration timestamps for each cache entry.
     * 
     * @var Solar_Session
     * 
     */
    protected $_expires = array();
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config User-provided configuration values.
     * 
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        
        // prefix the session class store, not the individual keys.
        $prefix = $this->_prefix;
        $this->_prefix = null;
        if (! $prefix) {
            $prefix = 'Solar_Cache_Adapter_Session';
        }
        
        // a session store for entries
        $this->_entries = Solar::factory('Solar_Session', array(
            'class' => $prefix . '__entries',
        ));
        
        // a session store for expires
        $this->_expires = Solar::factory('Solar_Session', array(
            'class' => $prefix . '__expires',
        ));
    }
    
    /**
     * 
     * Sets cache entry data.
     * 
     * @param string $key The entry ID.
     * 
     * @param mixed $data The data to write into the entry.
     * 
     * @return bool True on success, false on failure.
     * 
     */
    public function save($key, $data)
    {
        if (! $this->_active) {
            return;
        }
        
        // modify the key to add the prefix
        $key = $this->entry($key);
        
        // save entry and expiry in session
        $this->_entries->set($key, $data);
        $this->_expires->set($key, time() + $this->_life);
        return true;
    }
    
    /**
     * 
     * Inserts cache entry data, but only if the entry does not already exist.
     * 
     * @param string $key The entry ID.
     * 
     * @param mixed $data The data to write into the entry.
     * 
     * @return bool True on success, false on failure.
     * 
     */
    public function add($key, $data)
    {
        if (! $this->_active) {
            return;
        }
        
        // modify the key to add the prefix
        $key = $this->entry($key);
        
        // add entry to session if not already there
        if (! $this->_entries->has($key)) {
            return $this->save($key, $data);
        } else {
            return false;
        }
    }
    
    /**
     * 
     * Gets cache entry data.
     * 
     * @param string $key The entry ID.
     * 
     * @return mixed Boolean false on failure, cache data on success.
     * 
     */
    public function fetch($key)
    {
        if (! $this->_active) {
            return;
        }
        
        // modify the key to add the prefix
        $key = $this->entry($key);
        
        // does it exist?
        if (! $this->_entries->has($key)) {
            return false;
        }
        
        // has it expired?
        if ($this->_isExpired($key)) {
            // clear the entry
            $this->_entries->delete($key);
            $this->_expires->delete($key);
            return false;
        }
        
        // return the value
        return $this->_entries->get($key);
    }
    
    /**
     * 
     * Increments a cache entry value by the specified amount.  If the entry
     * does not exist, creates it at zero, then increments it.
     * 
     * @param string $key The entry ID.
     * 
     * @param string $amt The amount to increment by (default +1).  Using
     * negative values is effectively a decrement.
     * 
     * @return int The new value of the cache entry.
     * 
     */
    public function increment($key, $amt = 1)
    {
        if (! $this->_active) {
            return;
        }
        
        // modify the key to add the prefix
        $key = $this->entry($key);
        
        // make sure we have a key to increment
        $this->add($key, 0, null, $this->_life);
        
        // increment it
        $val = $this->_entries->get($key);
        $this->_entries->set($key, $val + $amt);
        
        // done!
        return $this->_entries->get($key);
    }
    
    /**
     * 
     * Deletes a cache entry.
     * 
     * @param string $key The entry ID.
     * 
     * @return void
     * 
     */
    public function delete($key)
    {
        if (! $this->_active) {
            return;
        }
        
        // modify the key to add the prefix
        $key = $this->entry($key);
        
        // delete entry and expiry
        $this->_entries->delete($key);
        $this->_expires->delete($key);
    }
    
    /**
     * 
     * Removes all cache entries.
     * 
     * Note that APC makes a distinction between "user" entries and
     * "system" entries; this only deletes the "user" entries.
     * 
     * @return void
     * 
     */
    public function deleteAll()
    {
        if (! $this->_active) {
            return;
        }
        
        $this->_entries->resetAll();
        $this->_expires->resetAll();
    }
    
    /**
     * 
     * Checks if an entry has expired (is past its lifetime) or not.
     * 
     * If lifetime is empty (zero), then the entry never expires.
     * 
     * @param string $key The entry key with prefix already added.
     * 
     * @return bool
     * 
     */
    protected function _isExpired($key)
    {
        // is life set as "forever?"
        if (! $this->_life) {
            return false;
        }
        
        // is it past its expiration date?
        if (time() >= $this->_expires->get($key)) {
            return true;
        }
        
        // not expired yet
        return false;
    }
}
