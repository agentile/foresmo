<?php
/**
 * 
 * Concrete class test.
 * 
 */
class Test_Foresmo_Model_Groups_Record extends Solar_Test {
    
    /**
     * 
     * Default configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Foresmo_Model_Groups_Record = array(
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
        $obj = Solar::factory('Foresmo_Model_Groups_Record');
        $this->assertInstance($obj, 'Foresmo_Model_Groups_Record');
    }
    
    /**
     * 
     * Test -- Magic getter for record properties; automatically calls __getColName() methods when they exist.
     * 
     */
    public function test__get()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Checks if a data key is set.
     * 
     */
    public function test__isset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Magic setter for record properties; automatically calls __setColName() methods when they exist.
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
     * Test -- Adds a column filter to this record instance.
     * 
     */
    public function testAddFilter()
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
     * Test -- Deletes this record from the database.
     * 
     */
    public function testDelete()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Fetch related objects This differs from a simple traversal in that parameters can further restrict or transform the results.
     * 
     */
    public function testFetchRelated()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Filter the data.
     * 
     */
    public function testFilter()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns a Solar_Form object pre-populated with column properties, values, and filters ready for processing (all based on the model for this record).
     * 
     */
    public function testForm()
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
     * Test -- Gets a list of all changed table columns.
     * 
     */
    public function testGetChanged()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the validation failure message for one or more properties, including the messages on related records and collections.
     * 
     */
    public function testGetInvalid()
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
     * Test -- Gets the name of the primary-key column.
     * 
     */
    public function testGetPrimaryCol()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Gets the value of the primary-key column.
     * 
     */
    public function testGetPrimaryVal()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the exception (if any) generated by the most-recent call to the save() method.
     * 
     */
    public function testGetSaveException()
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
     * Test -- Increments the value of a column **immediately at the database** and retains the incremented value in the record.
     * 
     */
    public function testIncrement()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Initialize the record object.
     * 
     */
    public function testInit()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Tells if the record, or a particular table-column in the record, has changed from its initial value.
     * 
     */
    public function testIsChanged()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Has this record been deleted?
     * 
     */
    public function testIsDeleted()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Is the record invalid?
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
     * Test -- Returns a new filter object with the filters from the record model.
     * 
     */
    public function testNewFilter()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Create a new record related to this one.
     * 
     */
    public function testNewRelated()
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
     * Test -- ArrayAccess: set a key value.
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
     * Test -- Refreshes data for this record from the database.
     * 
     */
    public function testRefresh()
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
     * Test -- Saves this record and all related records to the database, inserting or updating as needed.
     * 
     */
    public function testSave()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Perform a save() within a transaction, with automatic commit and rollback.
     * 
     */
    public function testSaveInTransaction()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Forces one property to be "invalid" and sets a validation failure message for it.
     * 
     */
    public function testSetInvalid()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Forces multiple properties to be "invalid" and sets validation failure message for them.
     * 
     */
    public function testSetInvalids()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets the status of this record only; does not change parent status.
     * 
     */
    public function testSetStatus()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Converts the properties of this model Record or Collection to an array, including related models stored in properties and calculated columns.
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
