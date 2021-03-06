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

        $this->_table_name = $this->_config['prefix'] . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
    }

    /**
     * fetchUserInfoByID
     * Get extended user information by user ID.
     *
     * @param $user_id
     * @return array
     */
    public function fetchUserInfoByID($user_id)
    {
        return $this->fetchAllAsArray(
            array(
                'where' => array(
                    'user_id = ?' => $user_id
                ),
            )
        );
    }
}
