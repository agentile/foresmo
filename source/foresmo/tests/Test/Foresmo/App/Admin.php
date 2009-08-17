<?php
/**
 * 
 * Concrete class test.
 * 
 */
class Test_Foresmo_App_Admin extends Solar_Test {
    
    /**
     * 
     * Default configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Foresmo_App_Admin = array(
    );
    
    // -----------------------------------------------------------------
    // 
    // Support methods.
    // 
    // -----------------------------------------------------------------
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config Configuration value overrides, if any.
     * 
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
    }
    
    /**
     * 
     * Destructor; runs after all methods are complete.
     * 
     * @param array $config Configuration value overrides, if any.
     * 
     */
    public function __destruct()
    {
        parent::__destruct();
    }
    
    /**
     * 
     * Setup; runs before each test method.
     * 
     */
    public function setup()
    {
        parent::setup();
    }
    
    /**
     * 
     * Teardown; runs after each test method.
     * 
     */
    public function teardown()
    {
        parent::teardown();
    }
    
    // -----------------------------------------------------------------
    // 
    // Test methods.
    // 
    // -----------------------------------------------------------------
    
    /**
     * 
     * Test -- Constructor.
     * 
     */
    public function test__construct()
    {
        $obj = Solar::factory('Foresmo_App_Admin');
        $this->assertInstance($obj, 'Foresmo_App_Admin');
    }
    
    /**
     * 
     * Test -- Try to force users to define what their view variables are.
     * 
     */
    public function test__get()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Try to force users to define what their view variables are.
     * 
     */
    public function test__set()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function test_setup()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- actionComments
     * 
     */
    public function testActionComments()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Shows a generic error page.
     * 
     */
    public function testActionError()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- actionIndex
     * 
     */
    public function testActionIndex()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- actionModules
     * 
     */
    public function testActionModules()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- actionPages
     * 
     */
    public function testActionPages()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- actionPosts
     * 
     */
    public function testActionPosts()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- actionSettings
     * 
     */
    public function testActionSettings()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- actionUsers
     * 
     */
    public function testActionUsers()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- allowAjaxAction
     * 
     */
    public function testAllowAjaxAction()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Executes the requested action and displays its output.
     * 
     */
    public function testDisplay()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Executes the requested action and returns its output with layout.
     * 
     */
    public function testFetch()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets the name for this page-controller; generally used only by the  front-controller when static routing leads to this page.
     * 
     */
    public function testSetController()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Injects the front-controller object that invoked this page-controller.
     * 
     */
    public function testSetFrontController()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- validate
     * 
     */
    public function testValidate()
    {
        $this->todo('stub');
    }
}
