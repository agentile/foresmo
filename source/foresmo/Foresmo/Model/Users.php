<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_Users extends Solar_Sql_Model {

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

        $this->_table_name = Solar_Config::get('Solar_Sql_Adapter_Mysql', 'prefix') . '_' . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
    }

    public function validUser($values = array())
    {
        if (array_key_exists('username', $values)
            && array_key_exists('password', $values)) {

            $salt = Solar_Config::get('Solar_Auth_Adapter_Sql', 'salt');

            $username = $values['username'];
            $password = md5($salt . $values['password']);
            $where = array('username = ?' => $username, 'password = ?' => $password);

            $result = $this->fetchArray(array('where' => $where));

            if (is_array($result) && count($result) === 0) {
                return false;
            } elseif (is_array($result) && count($result) > 0) {
                return $result;
            }
        }
        return false;
    }
}
