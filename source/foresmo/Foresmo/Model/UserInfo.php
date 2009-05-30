<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_UserInfo extends Solar_Sql_Model {

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

        $this->_table_name = Solar_Config::get('Solar_Sql_Adapter_Mysql', 'prefix') . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
    }

    /**
     * getUserInfoByID
     * Get extended user information by user ID.
     *
     * @param $user_id
     * @return array
     */
    public function getUserInfoByID($user_id)
    {
        return $this->fetchArray(
            array(
                'where' => array(
                    'user_id = ?' => $user_id
                ),
            )
        );
    }
}
