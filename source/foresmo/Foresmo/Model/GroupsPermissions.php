<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_GroupsPermissions extends Solar_Sql_Model {

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
        $this->_hasMany('permissions', array('foreign_key' => 'id'));
        $this->_hasMany('groups', array('foreign_key' => 'id'));
    }

    /**
     * getGroupPermissionsByID
     * Get the group permissions by ID as an array
     *
     * @access public
     * @param  $group_id
     * @return array
     */
    public function getGroupPermissionsByID($group_id, $short_list = false)
    {
        $permissions = $this->fetchAllAsArray(
            array(
                'where' => array(
                    'group_id = ?' => $group_id
                ),
                'eager' => 'permissions'
            )
        );

        if ($short_list === false) {
            return $permissions;
        }
        $short = array();
        foreach ($permissions as $permission) {
            if (is_array($permission['permissions'])) {
                foreach ($permission['permissions'] as $permission_data) {
                    if (isset($permission_data['name'])) {
                        $short[] = $permission_data['name'];
                    }
                }
            }
        }
        return $short;
    }
}
