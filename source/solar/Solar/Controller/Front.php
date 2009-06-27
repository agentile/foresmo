<?php
/**
 * 
 * Front-controller class to find and invoke a page-controller.
 * 
 * An example bootstrap "index.php" for your web root using the front
 * controller ...
 * 
 * {{code: php
 *     require 'Solar.php';
 *     Solar::start();
 *     $front = Solar::factory('Solar_Controller_Front');
 *     $front->display();
 *     Solar::stop();
 * }}
 * 
 * @category Solar
 * 
 * @package Solar_Controller
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Front.php 3850 2009-06-24 20:18:27Z pmjones $
 * 
 */
class Solar_Controller_Front extends Solar_Base
{
    /**
     * 
     * Default configuration values.
     * 
     * @config array classes Base class names for page controllers.
     * 
     * @config array disable A list of class names that should be disallowed and treated
     *   as "not found" if a URI maps to them.
     * 
     * @config string default The default page-name.
     * 
     * @config array routing Key-value pairs explicitly mapping a page-name to a
     *   controller class.
     * 
     * @var array
     * 
     */
    protected $_Solar_Controller_Front = array(
        'classes' => array('Solar_App'),
        'disable' => array('base'),
        'default' => 'hello',
        'routing' => array(),
    );
    
    /**
     * 
     * The default page name when none is specified.
     * 
     * @var array
     * 
     */
    protected $_default;
    
    /**
     * 
     * A list of page-controller names that should be disallowed; will be
     * treated as actions on the default controller instead.
     * 
     * @var array
     * 
     */
    protected $_disable = array('base');
    
    /**
     * 
     * Explicit page-name to class-name mappings.
     * 
     * @var array
     * 
     */
    protected $_routing;
    
    /**
     * 
     * The page-name key matched to the routing map, if any.
     * 
     * @var array
     * 
     * @see _getPageClass()
     * 
     */
    protected $_routing_key;
    
    /**
     * 
     * Stack of page-controller class prefixes.
     * 
     * @var Solar_Class_Stack
     * 
     */
    protected $_stack;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config Configuration value overrides, if any.
     * 
     */
    public function __construct($config = null)
    {
        // do the "real" construction
        parent::__construct($config); 
        
        // set convenience vars from config
        $this->_default = (string) $this->_config['default'];
        $this->_disable = (array)  $this->_config['disable'];
        $this->_routing = (array)  $this->_config['routing'];
        
        // set up a class stack for finding apps
        $this->_stack = Solar::factory('Solar_Class_Stack');
        $this->_stack->add($this->_config['classes']);
        
        // extended setup
        $this->_setup();
    }
    
    /**
     * 
     * Sets up the environment for all pages.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
    }
    
    /**
     * 
     * Fetches the output of a page/action/info specification URI.
     * 
     * @param Solar_Uri_Action|string $spec An action URI for the front
     * controller.  E.g., 'bookmarks/user/pmjones/php+blog?page=2'. When
     * empty (the default) uses the current URI.
     * 
     * @return string The output of the page action.
     * 
     */
    public function fetch($spec = null)
    {
        if ($spec instanceof Solar_Uri_Action) {
            // a uri object was passed directly
            $uri = $spec;
        } else {
            // spec is a uri string; if empty, uses the current uri
            $uri = Solar::factory('Solar_Uri_Action', array(
                'uri' => (string) $spec,
            ));
        }
        
        // first path-element is the page-name.
        $page = strtolower(reset($uri->path));
        if (empty($page)) {
            // page-name is blank. get the default class.
            // remove the empty element from the path.
            $class = $this->_getPageClass($this->_default);
            array_shift($uri->path);
        } elseif (in_array($page, $this->_disable)) {
            // page-name is disabled. get the default class.
            // leave existing elements in the path.
            $class = $this->_getPageClass($this->_default);
        } else {
            // look for a controller for the requested page.
            $class = $this->_getPageClass($page);
            if (! $class) {
                // no class for the page-name. get the default class.
                // leave existing elements in the path.
                $class = $this->_getPageClass($this->_default);
            } else {
                // found a class. don't need the page-name any more, so
                // remove it from the path.
                array_shift($uri->path);
            }
        }
        
        // do we have a page-controller class?
        if (! $class) {
            return $this->_notFound($page);
        }
        
        // instantiate the controller class and fetch its content
        $obj = Solar::factory($class);
        $obj->setFrontController($this);
        
        // was this the result of a static routing? if so, force the
        // page controller to use the route name.
        if ($this->_routing_key) {
            $obj->setController($this->_routing_key);
        }
        
        // done, fetch the page-controller results
        return $obj->fetch($uri);
    }
    
    /**
     * 
     * Displays the output of an page/action/info specification URI.
     * 
     * @param string $spec A page/action/info spec for the front
     * controller. E.g., 'bookmarks/user/pmjones/php+blog?page=2'.
     * 
     * @return string The output of the page action.
     * 
     */
    public function display($spec = null)
    {
        echo $this->fetch($spec);
    }
    
    /**
     * 
     * Finds the page-controller class name from a page name.
     * 
     * @param string $page The page name.
     * 
     * @return string The related page-controller class picked from
     * the routing, or from the list of available classes.  If not found,
     * returns false.
     * 
     */
    protected function _getPageClass($page)
    {
        if (! empty($this->_routing[$page])) {
            // found an explicit route
            $this->_routing_key = $page;
            $class = $this->_routing[$page];
        } else {
            // no explicit route
            $this->_routing_key = null;
            
            // try to find a matching class
            $page = str_replace('-',' ', strtolower($page));
            $page = str_replace(' ', '', ucwords(trim($page)));
            $class = $this->_stack->load($page, false);
        }
        return $class;
    }
    
    /**
     * 
     * Executes when fetch() cannot find a related page-controller class.
     * 
     * Generates an "HTTP 1.1/404 Not Found" status header and returns a
     * short HTML page describing the error.
     * 
     * @param string $page The name of the page not found.
     * 
     * @return string
     * 
     */
    protected function _notFound($page)
    {
        $content = "<html><head><title>Not Found</title>"
                 . "<body><h1>404 Not Found</h1><p>"
                 . htmlspecialchars("Page controller for '$page' not found.")
                 . "</p></body></html>";
        
        $response = Solar_Registry::get('response');
        $response->setStatusCode(404);
        $response->content = $content;
        
        return $response;
    }
}