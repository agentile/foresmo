<?php
/**
 * 
 * Concrete class test.
 * 
 */
class Test_Foresmo_Model_Posts_Collection extends Solar_Test {
    
    /**
     * 
     * Default configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Foresmo_Model_Posts_Collection = array(
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
        $obj = Solar::factory('Foresmo_Model_Posts_Collection');
        $this->assertInstance($obj, 'Foresmo_Model_Posts_Collection');
    }
    
    /**
     * 
     * Test -- Returns a record from the collection based on its key value.
     * 
     */
    public function test__get()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Does a certain key exist in the data?
     * 
     */
    public function test__isset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets a key value and marks the struct as "dirty"; also marks all parent structs as "dirty" too.
     * 
     */
    public function test__set()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns a string representation of the object.
     * 
     */
    public function test__toString()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets a key in the data to null.
     * 
     */
    public function test__unset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Fetches a new record and appends it to the collection.
     * 
     */
    public function testAppendNew()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Countable: how many keys are there?
     * 
     */
    public function testCount()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: get the current value for the array pointer.
     * 
     */
    public function testCurrent()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Deletes each record in the collection one-by-one.
     * 
     */
    public function testDeleteAll()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Deletes a record from the database and removes it from the collection.
     * 
     */
    public function testDeleteOne()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Frees memory used by this struct.
     * 
     */
    public function testFree()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns an array of all values for a single column in the collection.
     * 
     */
    public function testGetColVals()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns an array of invalidation messages from each invalid record,  keyed on the record offset within the collection.
     * 
     */
    public function testGetInvalid()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns an array of the invalid record objects within the collection, keyed on the record offset within the collection.
     * 
     */
    public function testGetInvalidRecords()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the model from which the data originates.
     * 
     */
    public function testGetModel()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Gets the injected pager information for the collection.
     * 
     */
    public function testGetPagerInfo()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns an array of the unique primary keys contained in this  collection.
     * 
     */
    public function testGetPrimaryVals()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Given a record object, looks up its offset value in the collection.
     * 
     */
    public function testGetRecordOffset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the status (clean/dirty/etc) of the struct.
     * 
     */
    public function testGetStatus()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Are there any invalid records in the collection?
     * 
     */
    public function testIsInvalid()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: get the current key for the array pointer.
     * 
     */
    public function testKey()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Loads the struct with data from an array or another struct.
     * 
     */
    public function testLoad()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: move to the next position.
     * 
     */
    public function testNext()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- ArrayAccess: does the requested key exist?
     * 
     */
    public function testOffsetExists()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- ArrayAccess: get a key value.
     * 
     */
    public function testOffsetGet()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- ArrayAccess: set a key value; appends to the array when using [] notation.
     * 
     */
    public function testOffsetSet()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- ArrayAccess: unset a key.
     * 
     */
    public function testOffsetUnset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Removes all records from the collection but **does not** delete them from the database.
     * 
     */
    public function testRemoveAll()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Removes one record from the collection but **does not** delete it from the database.
     * 
     */
    public function testRemoveOne()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: move to the first position.
     * 
     */
    public function testRewind()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Saves all the records from this collection to the database one-by-one, inserting or updating as needed.
     * 
     */
    public function testSave()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Injects the model from which the data originates.
     * 
     */
    public function testSetModel()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Injects pager information for the collection.
     * 
     */
    public function testSetPagerInfo()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets the status (clean/dirty/etc) on the struct.
     * 
     */
    public function testSetStatus()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the data for each record in this collection as an array.
     * 
     */
    public function testToArray()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns a string representation of the struct.
     * 
     */
    public function testToString()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: is the current position valid?
     * 
     */
    public function testValid()
    {
        $this->todo('stub');
    }
}
