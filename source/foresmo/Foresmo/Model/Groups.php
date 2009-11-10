<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_Groups extends Solar_Sql_Model {

    /**
     *
     * Model-specific setup.
     *
     * @return void
     *
     */
    protected function _setup()
    {
        $dir = str_replace('_', DIRECTORY_SEPARATOR, __CLASS__)
             . DIRECTORY_SEPARATOR
             . 'Setup'
             . DIRECTORY_SEPARATOR;

        $this->_table_name = $this->_config['prefix'] . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');

        $this->_hasMany('groups_permissions', array(
            'foreign_class' => 'Foresmo_Model_GroupsPermissions',
            'foreign_key' => 'group_id',
        ));

        $this->_hasMany('permissions', array(
            'foreign_class' => 'Foresmo_Model_Permissions',
            'through'       => 'groups_permissions',
            'through_key'   => 'group_id',
        ));

        $this->_hasMany('users', array('foreign_key' => 'group_id'));
    }

    /**
     * fetchGroups
     * Fetch Groups and pertaining info
     *
     * @return array
     */
    public function fetchGroups()
    {
        return $this->fetchAllAsArray(array(
                'eager' => array(
                    'users',
                    'permissions'
                )
            )
        );
    }

    /**
     * fetchGroupByName
     * Fetch Group and pertaining info
     *
     * @param string $name group name
     * @return array
     */
    public function fetchGroupByName($name)
    {
        return $this->fetchOneAsArray(array(
                'where' => array('name = ?' => $name),
                'eager' => array(
                    'users',
                    'permissions'
                )
            )
        );
    }

    /**
     * fetchGroupUserCount
     * Fetch the count
     *
     * @param string $name group name
     * @return array
     */
    public function fetchGroupUserCount($name)
    {
        $group = $this->fetchGroupByName($name);
        return (isset($group['users'])) ? count($group['users']) : 0;
    }

    /**
     * isValidGroup
     * Does group exist?
     *
     * @param string $name group name
     * @return bool
     */
    public function isValidGroup($name)
    {
        $group = $this->fetchGroupByName($name);
        return !empty($group);
    }
}
