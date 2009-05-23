<?php
/**
 * 
 * Represents the characteristics of a relationship where a native model
 * "belongs to" a foreign model.
 * 
 * @category Solar
 * 
 * @package Solar_Sql_Model
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: BelongsTo.php 3716 2009-04-23 16:33:53Z pmjones $
 * 
 */
class Solar_Sql_Model_Related_BelongsTo extends Solar_Sql_Model_Related_ToOne
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
        $this->type = 'belongs_to';
    }
    
    /**
     * 
     * Corrects the foreign_key value in the options; uses the foreign-model
     * table name as singular.
     * 
     * @param array &$opts The user-defined relationship options.
     * 
     * @return void
     * 
     */
    protected function _fixForeignKey(&$opts)
    {
        $opts['foreign_key'] = $this->_foreign_model->foreign_col;
    }
    
    /**
     * 
     * Fixes the related column names in the user-defined options **in place**.
     * 
     * The foreign key is stored in the **native** model.
     * 
     * @param array $opts The user-defined relationship options.
     * 
     * @return void
     * 
     */
    protected function _fixRelatedCol(&$opts)
    {
        $opts['native_col'] = $opts['foreign_key'];
    }
    
    /**
     * 
     * A support method for _fixRelated() to handle belongs-to relationships.
     * 
     * @param array &$opts The relationship options; these are modified in-
     * place.
     * 
     * @return void
     * 
     */
    protected function _setRelated($opts)
    {
        // the foreign column
        if (empty($opts['foreign_col'])) {
            // named by foreign primary key
            $this->foreign_col = $this->_foreign_model->primary_col;
        } else {
            $this->foreign_col = $opts['foreign_col'];
        }
        
        // the native column
        if (empty($opts['native_col'])) {
            // named by foreign table's suggested foreign_col name
            $this->native_col = $this->_foreign_model->foreign_col;
        } else {
            $this->native_col = $opts['native_col'];
        }
    }
    
    // in a belongs-to, the foreign value is stored in the native column.
    // in "has"es, the native value is stored in the foreign column.
    public function preSave($native)
    {
        // see if we have the foreign record that the native record belongs to
        $foreign = $native->{$this->name};
        if (! $foreign) {
            // we need the record the native belongs to, to connect the two
            throw $this->_exception('ERR_RELATED_DOES_NOT_EXIST', array(
                'name' => $native->{$this->name},
            ));
        } else {
            // the foreign record exists, connect with the native
            $native->{$this->native_col} = $foreign->{$this->foreign_col};
        }
    }
    
    // pre-save has already connected the records
    public function save($native)
    {
        $foreign = $native->{$this->name};
        if ($foreign) {
            $foreign->save();
        }
    }
}