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

        $this->_hasMany('userinfo', array(
            'foreign_class' => 'Foresmo_Model_UserInfo',
            'foreign_key' => 'user_id',
        ));

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
     * isValidUser
     * Given credentionals, is this a valid user. (Login)
     *
     * @param $values
     * @return bool
     */
    public function isValidUser($values = array())
    {
        if (!array_key_exists('username', $values) || !array_key_exists('password', $values)) {
            return false;
        }

        $username = $values['username'];
        $password = $values['password'];
        $where = array('username = ?' => $username);

        $results = $this->fetchAllAsArray(array(
            'cache' => false,
            'where' => $where,
            )
        );

        if (is_array($results) && count($results) > 0) {
            $hasher = new Foresmo_Hashing(8, false);
            if ($hasher->checkPassword($password, $results[0]['password'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * checkUsernameExists
     * Checks to see if username supplied is a username of
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
     * fetchEmailByID
     *
     * @param $id
     * @return string
     */
    public function fetchEmailByID($id)
    {
        $where = array(
            'id = ?' => (int) $id
        );

        $result = $this->fetchOneAsArray(array('where' => $where));
        if (!empty($result) && isset($result['email'])) {
            return $result['email'];
        }
        return false;
    }

    /**
     * fetchUsers
     *
     * @return array
     */
    public function fetchUsers()
    {
        return $this->fetchAllAsArray(array(
            'eager' => array(
                'userinfo',
                'permissions',
                'groups',
                )
            )
        );
    }

    /**
     * fetchUserByID
     *
     * @param $user_id
     * @return array
     */
    public function fetchUserByID($user_id)
    {
        if ((int) $user_id != $user_id) {
            return array();
        }

        return $this->fetchOneAsArray(array(
            'cache' => false,
            'where' => array(
                'id = ?' => (int) $user_id
            ),
            'eager' => array(
                'userinfo',
                'permissions',
                'groups',
                )
            )
        );
    }

    /**
     * fetchUserByUsername
     *
     * @param $username
     * @return array
     */
    public function fetchUserByUsername($username)
    {
        return $this->fetchOneAsArray(array(
            'cache' => false,
            'where' => array(
                'username = ?' => (string) $username
            ),
            'eager' => array(
                'userinfo',
                'permissions',
                'groups',
                )
            )
        );
    }
}
