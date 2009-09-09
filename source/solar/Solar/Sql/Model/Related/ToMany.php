<?php
/**
 * 
 * Represents the characteristics of a "to-many" related model.
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
 * @version $Id: ToMany.php 3995 2009-09-08 18:49:24Z pmjones $
 * 
 */
abstract class Solar_Sql_Model_Related_ToMany extends Solar_Sql_Model_Related
{
    /**
     * 
     * Is this related to one record?
     * 
     * @return bool
     * 
     */
    public function isOne()
    {
        return false;
    }
    
    /**
     * 
     * Is this related to many records?
     * 
     * @return bool
     * 
     */
    public function isMany()
    {
        return true;
    }
    
    /**
     * 
     * Returns foreign data as a collection object.
     * 
     * @param array $data The foreign data.
     * 
     * @return Solar_Sql_Model_Collection A foreign collection object.
     * 
     */
    public function newObject($data)
    {
        return $this->_foreign_model->newCollection($data);
    }
    
    /**
     * 
     * Fetches an empty value for the related.
     * 
     * @return array
     * 
     */
    public function fetchEmpty()
    {
        return array();
    }
    
    /**
     * 
     * Fetches a new related collection.
     * 
     * @param array $data Data for the new collection.
     * 
     * @return Solar_Sql_Model_Collection
     * 
     */
    public function fetchNew($data = array())
    {
        return $this->_foreign_model->newCollection($data);
    }
    
    /**
     * 
     * Sets the base name for the foreign class; assumes the related name is
     * is already plural.
     * 
     * @param array $opts The user-defined relationship options.
     * 
     * @return void
     * 
     */
    protected function _setForeignClass($opts)
    {
        if (empty($opts['foreign_class'])) {
            $catalog = $this->_native_model->catalog;
            $this->foreign_class = $catalog->getClass($opts['name']);
        } else {
            $this->foreign_class = $opts['foreign_class'];
        }
    }
    
    /**
     * 
     * Corrects the foreign_key value in the options; uses the native-model
     * table name as singular when a regular has-many, and the foreign-
     * model primary column as-is when a 'has-many through'.
     * 
     * @param array &$opts The user-defined relationship options.
     * 
     * @return void
     * 
     */
    protected function _fixForeignKey(&$opts)
    {
        $opts['foreign_key'] = $this->_native_model->foreign_col;
    }
    
    /**
     * 
     * Fixes the related column names in the user-defined options **in place**.
     * 
     * The foreign key is stored in the **foreign** model.
     * 
     * @param array $opts The user-defined relationship options.
     * 
     * @return void
     * 
     */
    protected function _fixRelatedCol(&$opts)
    {
        $opts['foreign_col'] = $opts['foreign_key'];
    }
    
    /**
     * 
     * A support method for _fixRelated() to handle has-many relationships.
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
            // named by native table's suggested foreign_col name
            $this->foreign_col = $this->_native_model->foreign_col;
        } else {
            $this->foreign_col = $opts['foreign_col'];
        }
        
        // the native column
        if (empty($opts['native_col'])) {
            // named by native primary key
            $this->native_col = $this->_native_model->primary_col;
        } else {
            $this->native_col = $opts['native_col'];
        }
    }
    
    /**
     * 
     * Merges are always set to 'client' regardless of the settings.
     * 
     * @param array $opts The user-defined options for the relationship.
     * 
     * @return void
     * 
     */
    protected function _setMerge($opts)
    {
        $this->merge = 'client';
    }
    
    /**
     * 
     * Fixes the eager params based on the settings for this related.
     * 
     * Always removes the column prefix.
     * 
     * @param Solar_Sql_Model_Params_Eager $eager The eager params.
     * 
     * @return void
     * 
     */
    protected function _fixEagerParams($eager)
    {
        // never use a cols prefix
        $eager->colsPrefix(null);
        
        // go on
        parent::_fixEagerParams($eager);
    }
    
    /**
     * 
     * Modifies the parent result array to add eager records.
     * 
     * @param Solar_Sql_Model_Params_Eager $eager The eager params.
     * 
     * @param array &$result The parent results.
     * 
     * @param string $type The type of fetch performed (e.g., 'one', 'all', etc).
     * 
     * @param Solar_Sql_Model_Params_Fetch $fetch The native fetch settings.
     * 
     * @return void
     * 
     */
    public function modEagerResult($eager, &$result, $type, $fetch)
    {
        // pre-emptively return if no result, or no cols requested
        if (! $result || ! $eager['cols']) {
            return;
        }
        
        switch ($type) {
        case 'one':
            $this->_fetchIntoArrayOne($eager, $result);
            break;
        case 'all':
        case 'assoc':
        case 'array':
            $this->_fetchIntoArrayAll($eager, $result, $fetch);
            break;
        default:
            throw $this->_exception('ERR_UNKNOWN_TYPE');
            break;
        }
    }
    
    /**
     * 
     * Modifies the native fetch with an eager join so that the foreign table
     * is joined properly.
     * 
     * @param Solar_Sql_Model_Params_Eager $eager The eager params.
     * 
     * @param Solar_Sql_Model_Params_Fetch $fetch The native fetch params.
     * 
     * @return void
     * 
     */
    protected function _modEagerFetchJoin($eager, $fetch)
    {
        $join = array(
            'type' => $eager['join_type'],
            'name' => "{$this->foreign_table} AS {$eager['alias']}",
            'cond' => array(),
            'cols' => null,
        );
        
        // primary-key join condition on foreign table
        $join['cond'][] = "{$fetch['alias']}.{$this->native_col} = "
                        . "{$eager['alias']}.{$this->foreign_col}";
        
        // extra conditions for the parent fetch
        if ($eager['join_cond']) {
            // what type of join?
            if ($join['type'] == 'left') {
                // convert the eager conditions to a WHERE clause
                foreach ((array) $eager['join_cond'] as $cond => $val) {
                    $fetch->where($cond, $val);
                }
            } else {
                // merge join conditions
                $join['cond'] = array_merge(
                    $join['cond'],
                    (array) $eager['join_cond']
                );
            }
        }
        
        // done!
        $fetch->join($join);
        
        // always DISTINCT so we don't get multiple duplicate native rows
        $fetch->distinct(true);
    }
    
    /**
     * 
     * Collates a result array by an array key, grouping the results by that
     * value.
     *
     * @param array $array The result array.
     *
     * @param string $key The key in the array to collate by.
     * 
     * @return array An array of collated elements, keyed by the collation 
     * value.
     * 
     */
    protected function _collate($array, $key)
    {
        $collated = array();
        foreach ($array as $i => $row) {
            $val = $row[$key];
            $collated[$val][] = $row;
        }
        return $collated;
    }
    
}
