<?php
/**
 * 
 * Adapter to fetch roles from an SQL database table.
 * 
 * @category Solar
 * 
 * @package Solar_Role
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Sql.php 3850 2009-06-24 20:18:27Z pmjones $
 * 
 */
class Solar_Role_Adapter_Sql extends Solar_Role_Adapter
{
    /**
     * 
     * Default configuration values.
     * 
     * @config dependency sql A Solar_Sql dependency.
     * 
     * @config string table The table where roles are stored.
     * 
     * @config string handle_col The column for user handles.
     * 
     * @config string role_col The column for roles.
     * 
     * @config string|array where Additional _multiWhere() conditions to use
     *   when selecting role rows.
     * 
     * @var array
     * 
     */
    protected $_Solar_Role_Adapter_Sql = array(
        'sql'        => 'sql',
        'table'      => 'roles',
        'handle_col' => 'handle',
        'role_col'   => 'name',
        'where'      => array(),
    );
    
    /**
     * 
     * Fetches the roles for a user.
     * 
     * @param string $handle User handle to get roles for.
     * 
     * @return array An array of roles discovered in the table.
     * 
     */
    public function fetch($handle)
    {
        // get the dependency object of class Solar_Sql
        $sql = Solar::dependency('Solar_Sql', $this->_config['sql']);
        
        // get a selection tool using the dependency object
        $select = Solar::factory(
            'Solar_Sql_Select',
            array('sql' => $sql)
        );
        
        // build the select
        $select->from($this->_config['table'], $this->_config['role_col'])
               ->where("{$this->_config['handle_col']} = ?", $handle)
               ->multiWhere($this->_config['where']);
        
        // get the results (a column of rows)
        $result = $select->fetch('col');
        
        // done!
        return $result;
    }
}
