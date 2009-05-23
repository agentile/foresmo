<?php
/**
 * 
 * Abstract class test.
 * 
 */
class Test_Solar_Session_Handler_Adapter extends Solar_Test {
    
    /**
     * 
     * Configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Solar_Session_Handler_Adapter = array(
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
     * @param array $config User-defined configuration parameters.
     * 
     */
    public function __construct($config = null)
    {
        $this->skip('abstract class');
        parent::__construct($config);
    }
    
    /**
     * 
     * Destructor; runs after all methods are complete.
     * 
     * @param array $config User-defined configuration parameters.
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
     * Setup; runs after each test method.
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
        $obj = Solar::factory('Solar_Session_Handler_Adapter');
        $this->assertInstance($obj, 'Solar_Session_Handler_Adapter');
    }
    
    /**
     * 
     * Test -- Closes the session handler.
     * 
     */
    public function testClose()
    {
        $this->skip('abstract method');
    }
    
    /**
     * 
     * Test -- Destroys session data.
     * 
     */
    public function testDestroy()
    {
        $this->skip('abstract method');
    }
    
    /**
     * 
     * Test -- Removes old session data (garbage collection).
     * 
     */
    public function testGc()
    {
        $this->skip('abstract method');
    }
    
    /**
     * 
     * Test -- Opens the session handler.
     * 
     */
    public function testOpen()
    {
        $this->skip('abstract method');
    }
    
    /**
     * 
     * Test -- Reads session data.
     * 
     */
    public function testRead()
    {
        $this->skip('abstract method');
    }
    
    /**
     * 
     * Test -- Writes session data.
     * 
     */
    public function testWrite()
    {
        $this->skip('abstract method');
    }
}
