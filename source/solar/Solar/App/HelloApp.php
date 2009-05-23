<?php
/**
 * 
 * Simple "hello world" application with actions, views, and localization.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @subpackage Solar_App_Hello
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: HelloApp.php 3153 2008-05-05 23:14:16Z pmjones $
 * 
 */
class Solar_App_HelloApp extends Solar_App_Base
{
    /**
     * 
     * The default controller action.
     * 
     * @var string
     * 
     */
    protected $_action_default = 'main';
    
    /**
     * 
     * The list of available locale codes.
     * 
     * @var array
     * 
     */
    public $list = array('en_US', 'fr_FR', 'pt_BR');
    
    /**
     * 
     * The requested locale code.
     * 
     * @var string
     * 
     */
    public $code;
    
    /**
     * 
     * The translated text.
     * 
     * @var string
     * 
     */
    public $text;
    
    /**
     * 
     * Overrides the general Solar_App setup so that we don't need a
     * database connection. This is because we want the simplest
     * possible hello-world example.
     * 
     * Thanks, Clay Loveless, for suggesting this.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        // register a Solar_User object if not already.
        // this will trigger the authentication process.
        if (! Solar_Registry::exists('user')) {
            Solar_Registry::set('user', Solar::factory('Solar_User'));
        }
        
        // set the layout title
        $this->layout_head['title'] = get_class($this);
    }
    
    /**
     * 
     * Resets to the requested locale code and shows translated output
     * as an HTML file.
     * 
     * @param string $code The requested locale code.
     * 
     * @return void
     * 
     */
    public function actionMain($code = 'en_US')
    {
        // set the code from input
        $this->code = $code;
        if (! $this->code) {
            $this->code = 'en_US';
        }
        
        // reset the locale strings to the new code
        Solar_Registry::get('locale')->setCode($this->code);
        
        // set the translated text
        $this->text = $this->locale('TEXT_HELLO_WORLD');
        
        // tell the site layout what title to use
        $this->layout_head['title'] = 'Solar: Hello World!';
    }
    
    /**
     * 
     * Resets to the requested locale code and shows translated output
     * as an RSS file.
     * 
     * @param string $code The requested locale code.
     * 
     * @return void
     * 
     */
    public function actionRss($code = 'en_US')
    {
        // set the code from input
        $this->code = $code;
        if (! $this->code) {
            $this->code = 'en_US';
        }
        
        // reset the locale strings to the new code
        Solar_Registry::get('locale')->setCode($this->code);
        
        // set the translated text
        $this->text = $this->locale('TEXT_HELLO_WORLD');
        
        // turn off the site layout so RSS is not mangled
        $this->_layout = false;
    }
}
