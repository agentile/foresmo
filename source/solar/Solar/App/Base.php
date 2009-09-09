<?php
/**
 * 
 * Abstract base class for Solar application classes.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Base.php 3892 2009-07-22 00:19:59Z pmjones $
 * 
 */
abstract class Solar_App_Base extends Solar_Controller_Page {
    
    /**
     * 
     * Values for the <head> block in the layout.
     * 
     * Keys are:
     * 
     * `title`
     * : (string) The <title> tag value.
     * 
     * `base`
     * : (string) The <base> href value.
     * 
     * `meta`
     * : (array) An array of <meta> tag values.
     * 
     * `link`
     * : (array) An array of <link> tag values.
     * 
     * `style`
     * : (array) An array of <style> tag values.
     * 
     * `script`
     * : (array) An array of <script> tag values.
     * 
     * `object`
     * : (array) An array of <object> tag values.
     * 
     * @var array
     * 
     */
    public $layout_head = array(
        'title'  => null,
        'base'   => null,
        'meta'   => array(),
        'link'   => array(),
        'style'  => array(),
        'script' => array(),
        'object' => array(),
    );
    
    /**
     * 
     * Local navigation links.
     * 
     * Format is "link href" => "display text".
     * 
     * @var array
     * 
     */
    public $layout_local = array();
    
    /**
     * 
     * The currently-active local navigation link.
     * 
     * Refers to a key in [[$layout_local]].
     * 
     * @var array
     * 
     */
    public $layout_local_active = null;
    
    /**
     * 
     * Site navigation links.
     * 
     * Format is "link href" => "display text".
     * 
     * @var array
     * 
     */
    public $layout_nav = array();
    
    /**
     * 
     * The currently-active site navigation link.
     * 
     * Refers to a key in [[$layout_nav]].
     * 
     * @var array
     * 
     */
    public $layout_nav_active = null;
    
    /**
     * 
     * Name of the default layout to be rendered.
     * 
     * @var string
     * 
     */
    protected $_layout_default = 'navtop-localright';
    
    /**
     * 
     * The model catalog.
     * 
     * @var Solar_Model_Bookmarks
     * 
     */
    protected $_model;
    
    /**
     * 
     * Sets up the Solar_App environment.
     * 
     * Registers 'sql', 'user', and 'content' objects, and sets the
     * layout title to the class name.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        // register a Solar_Sql object if not already
        if (! Solar_Registry::exists('sql')) {
            Solar_Registry::set('sql', Solar::factory('Solar_Sql'));
        }
        
        // register a model catalog if not already
        if (! Solar_Registry::exists('model_catalog')) {
            Solar_Registry::set(
                'model_catalog',
                Solar::factory('Solar_Sql_Model_Catalog')
            );
        }
        
        // register a Solar_User object if not already.
        // this will trigger the authentication process.
        if (! Solar_Registry::exists('user')) {
            Solar_Registry::set('user', Solar::factory('Solar_User'));
        }
        
        // set the layout title
        $this->layout_head['title'] = get_class($this);
        
        // retain the model catalog
        $this->_model = Solar_Registry::get('model_catalog');
    }
    
    /**
     * 
     * Checks to see if user is allowed access to the requested action
     * for this controller.
     * 
     * On access failure, changes $this->_action to 'error' and adds
     * an error message stating the user is not allowed access.
     * 
     * @return void
     * 
     */
    protected function _preAction()
    {
        $allow = Solar_Registry::get('user')->access->isAllowed(
            get_class($this),
            $this->_action
        );
        
        if (! $allow) {
            $this->_errors[] = $this->locale('ERR_NOT_ALLOWED_ACCESS');
            $this->_action = 'error';
        }
    }
    
    /**
     * 
     * Calls parent _preRender(), then sets additional view properties.
     * 
     * @return void
     * 
     */
    protected function _preRender()
    {
        parent::_preRender();
        
        // add an app-specific CSS file
        $tmp = explode('_', get_class($this));
        $vendor = $tmp[0];
        $this->layout_head['style'][] = "{$vendor}/styles/app/{$this->_controller}.css";
    }
    
    /**
     * 
     * If the action doesn't map to a method, place the action back on top of
     * the info array and use the default action in its place.
     * 
     * @return void
     * 
     */
    protected function _fixAction()
    {
        parent::_fixAction();
        if (! $this->_getActionMethod($this->_action)) {
            array_unshift($this->_info, $this->_action);
            $this->_action = $this->_action_default;
        }
    }
}
