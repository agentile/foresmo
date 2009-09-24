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

        $this->_table_name = $this->_config['prefix'] . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');

        $this->_hasMany('userinfo');

        $this->_hasOne('groups_permissions', array(
            'foreign_class' => 'Foresmo_Model_GroupsPermissions',
            'foreign_key' => 'group_id',
        ));

        $this->_hasMany('permissions', array(
             'foreign_class' => 'Foresmo_Model_Permissions',
             'through'       => 'groups_permissions',
             'through_key'   => 'permission_id',
        ));

        $this->_hasOne('groups', array(
            'foreign_class' => 'Foresmo_Model_Groups',
            'foreign_key' => 'id',
        ));
    }

    /**
     * validUser
     * Given credentionals, is this a valid user. (Login)
     *
     * @param $values
     * @return mixed false on invalid, fetched row if valid.
     */
    public function validUser($values = array())
    {
        if (array_key_exists('username', $values)
            && array_key_exists('password', $values)) {

            $salt = Solar_Config::get('Solar_Auth_Adapter_Sql', 'salt');

            $username = $values['username'];
            $password = md5($salt . $values['password']);
            $where = array('username = ?' => $username, 'password = ?' => $password);

            $result = $this->fetchAllAsArray(array('where' => $where));

            if (is_array($result) && count($result) === 0) {
                return false;
            } elseif (is_array($result) && count($result) > 0) {
                return $result[0];
            }
        }
        return false;
    }

    /**
     * checkUsernameExists
     * Checks to see if e-mail address supplied is an e-mail address of
     * one of the blogs registered users.
     */
    public function checkUsernameExists($username)
    {
        $where = array(
            'username LIKE ?' => $username
        );
        $results = $this->fetchAllAsArray(array('where' => $where));
        if (!empty($results)) {
            return $results[0];
        }
        return false;
    }

    /**
     * checkEmailExists
     * Checks to see if e-mail address supplied is an e-mail address of
     * one of the blogs registered users.
     */
    public function checkEmailExists($email)
    {
        $where = array(
            'email = ?' => strtolower($email)
        );
        $results = $this->fetchAllAsArray(array('where' => $where));
        if (!empty($results)) {
            return $results[0];
        }
        return false;
    }

    /**
     * getEmailFromID
     *
     * @param $id
     * @return string
     */
    public function getEmailFromID($id)
    {
        $where = array(
            'id = ?' => (int) $id
        );

        $results = $this->fetchAllAsArray(array('where' => $where));
        if (!empty($results)) {
            return $results[0]['email'];
        }
        return false;
    }

    /**
     * getUsers
     *
     * @return array
     */
    public function getUsers()
    {
        return $this->fetchAllAsArray(array('eager' => array('permissions','groups')));
    }
}
