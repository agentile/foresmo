<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_Permissions extends Solar_Sql_Model {

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
            'foreign_key' => 'permission_id',
        ));

        $this->_hasMany('groups', array(
             'foreign_class' => 'Foresmo_Model_Groups',
             'through'       => 'groups_permissions',
             'through_key'   => 'group_id',
             'through_native_col' => 'permission_id',
             'through_foreign_col' => 'group_id',
        ));
    }
}
