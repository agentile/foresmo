<?php
/**
 * 
 * Log adapter for appending to a file.
 * 
 * @category Solar
 * 
 * @package Solar_Log
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: File.php 3383 2008-09-03 00:23:39Z pmjones $
 * 
 */
class Solar_Log_Adapter_File extends Solar_Log_Adapter
{
    /**
     * 
     * User-defined configuration values.
     * 
     * Keys are ...
     * 
     * `events`
     * : (string|array) The event types this instance
     *   should recognize; a comma-separated string of events, or
     *   a sequential array.  Default is all events ('*').
     * 
     * `file`
     * : (string) The file where events should be logged;
     *   for example '/www/username/logs/solar.log'.
     * 
     * `format`
     * : (string) The line format for each saved event.
     *   Use '%t' for the timestamp, '%c' for the class name, '%e' for
     *   the event type, '%m' for the event description, and '%%' for a
     *   literal percent.  Default is '%t %c %e %m'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Log_Adapter_File = array(
        'events' => '*',
        'file'   => '',
        'format' => '%t %c %e %m',
    );
    
    /**
     * 
     * The path to the log file.
     * 
     * @var string
     * 
     */
    protected $_file = '';
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config User-defined configuration.
     * 
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        $this->_file = $this->_config['file'];
    }
    
    /**
     * 
     * Support method to save (write) an event and message to the log.
     * 
     * Appends to the file, and uses an exclusive lock (LOCK_EX).
     * 
     * @param string $class The class name reporting the event.
     * 
     * @param string $event The event type (for example 'info' or 'debug').
     * 
     * @param string $descr A description of the event. 
     * 
     * @return mixed Boolean false if the event was not saved (usually
     * because it was not recognized), or a non-empty value if it was
     * saved.
     * 
     */
    protected function _save($class, $event, $descr)
    {
        $text = str_replace(
            array('%t', '%c', '%e', '%m', '%%'),
            array($this->_getTime(), $class, $event, $descr, '%'),
            $this->_config['format']
        ) . "\n";
    
        return file_put_contents($this->_file, $text, FILE_APPEND | LOCK_EX);
    }
}
