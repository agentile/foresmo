<?php
/**
 * 
 * Represents the characteristics of a relationship where a native model
 * "has many" of a foreign model.
 * 
 * @category Solar
 * 
 * @package Solar_Sql_Model
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @author Jeff Moore <jeff@procata.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: HasMany.php 3835 2009-06-12 20:05:36Z pmjones $
 * 
 */
class Solar_Sql_Model_Related_HasMany extends Solar_Sql_Model_Related_ToMany
{
    /**
     * 
     * Sets the relationship type.
     * 
     * @return void
     * 
     */
    protected function _setType()
    {
        $this->type = 'has_many';
    }
    
    /**
     * 
     * Saves a related collection from a native record.
     * 
     * @param Solar_Sql_Model_Record $native The native record to save from.
     * 
     * @return void
     * 
     */
    public function save($native)
    {
        $foreign = $native->{$this->name};
        if (! $foreign) {
            return;
        }
        
        // set the foreign_col on each foreign record to the native value
        foreach ($foreign as $record) {
            $record->{$this->foreign_col} = $native->{$this->native_col};
        }
        
        $foreign->save();
    }
}
