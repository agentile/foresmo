<?php
/**
 * Portable PHP password hashing framework.
 *
 * Version 0.2 / genuine.
 *
 * Written by Solar Designer <solar at openwall.com> in 2004-2006 and placed in
 * the public domain.
 *
 * There's absolutely no warranty.
 *
 * The homepage URL for this framework is:
 *
 *   http://www.openwall.com/phpass/
 *
 * Please be sure to update the Version line if you edit this file in any way.
 * It is suggested that you leave the main version number intact, but indicate
 * your project name (after the slash) and add your own revision information.
 *
 * Please do not change the "private" password hashing method implemented in
 * here, thereby making your hashes incompatible.  However, if you must, please
 * change the hash type identifier (the "$P$") to something different.
 *
 * Obviously, since this code is in the public domain, the above are not
 * requirements (there can be none), but merely suggestions.
 *
 * Authenticate against an SQL database table using secure adaptive hashing + dynamic salts.
 *
 * @category Solar
 *
 * @package Solar_Auth
 *
 * Edited by Anthony Gentile 09/30/09 for SolarPHP
 */
class Solar_Auth_Adapter_Phpass extends Solar_Auth_Adapter
{
    protected $_itoa64;
    protected $_random_state;

    /**
     *
     * Default configuration values.
     *
     * @config dependency sql A Solar_Sql dependency injection.
     *
     * @config string table Name of the table holding authentication data.
     *
     * @config string handle_col Name of the column with the user handle ("username").
     *
     * @config string passwd_col Name of the column with the hash.
     *
     * @config string email_col Name of the column with the email address.
     *
     * @config string moniker_col Name of the column with the display name (moniker).
     *
     * @config string uri_col Name of the column with the website URI.
     *
     * @config string uid_col Name of the column with the numeric user ID ("user_id").
     *
     *
     * @config string|array where Additional _multiWhere() conditions to use
     *   when selecting rows for authentication.
     *
     * @var array
     *
     */
    protected $_Solar_Auth_Adapter_Phpass = array(
        'sql'             => 'sql',
        'table'           => 'members',
        'handle_col'      => 'handle',
        'passwd_col'      => 'passwd',
        'email_col'       => null,
        'moniker_col'     => null,
        'uri_col'         => null,
        'uid_col'         => null,
        'iteration_count' => 8,
        'portable_hashes' => false,
        'where'           => array(),
    );

    public function __construct()
    {
        $this->_itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        if ($this->_config['iteration_count'] < 4 || $this->_config['iteration_count'] > 31) {
            $this->_config['iteration_count'] = 8;
        }

        $this->_random_state = microtime() . getmypid();
    }

    protected function _getRandomBytes($count)
    {
        $output = '';
        if (is_readable('/dev/urandom') &&
            ($fh = @fopen('/dev/urandom', 'rb'))) {
            $output = fread($fh, $count);
            fclose($fh);
        }

        if (strlen($output) < $count) {
            $output = '';
            for ($i = 0; $i < $count; $i += 16) {
                $this->_random_state = md5(microtime() . $this->_random_state);
                $output .= pack('H*', md5($this->_random_state));
            }
            $output = substr($output, 0, $count);
        }

        return $output;
    }

    protected function _encode64($input, $count)
    {
        $output = '';
        $i = 0;
        do {
            $value = ord($input[$i++]);
            $output .= $this->_itoa64[$value & 0x3f];
            if ($i < $count)
                $value |= ord($input[$i]) << 8;
            $output .= $this->_itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count)
                break;
            if ($i < $count)
                $value |= ord($input[$i]) << 16;
            $output .= $this->_itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $count)
                break;
            $output .= $this->_itoa64[($value >> 18) & 0x3f];
        } while ($i < $count);

        return $output;
    }

    protected function _genSaltPrivate($input)
    {
        $output = '$P$';
        $output .= $this->_itoa64[min($this->_config['iteration_count'] +
            ((PHP_VERSION >= '5') ? 5 : 3), 30)];
        $output .= $this->_encode64($input, 6);

        return $output;
    }

    protected function _cryptPrivate($password, $setting)
    {
        $output = '*0';
        if (substr($setting, 0, 2) == $output)
            $output = '*1';

        if (substr($setting, 0, 3) != '$P$')
            return $output;

        $count_log2 = strpos($this->_itoa64, $setting[3]);
        if ($count_log2 < 7 || $count_log2 > 30)
            return $output;

        $count = 1 << $count_log2;

        $salt = substr($setting, 4, 8);
        if (strlen($salt) != 8)
            return $output;

        // We're kind of forced to use MD5 here since it's the only
        // cryptographic primitive available in all versions of PHP
        // currently in use.  To implement our own low-level crypto
        // in PHP would result in much worse performance and
        // consequently in lower iteration counts and hashes that are
        // quicker to crack (by non-PHP code).
        if (PHP_VERSION >= '5') {
            $hash = md5($salt . $password, TRUE);
            do {
                $hash = md5($hash . $password, TRUE);
            } while (--$count);
        } else {
            $hash = pack('H*', md5($salt . $password));
            do {
                $hash = pack('H*', md5($hash . $password));
            } while (--$count);
        }

        $output = substr($setting, 0, 12);
        $output .= $this->_encode64($hash, 16);

        return $output;
    }

    protected function _genSaltExtended($input)
    {
        $count_log2 = min($this->_config['iteration_count'] + 8, 24);
        # This should be odd to not reveal weak DES keys, and the
        # maximum valid value is (2**24 - 1) which is odd anyway.
        $count = (1 << $count_log2) - 1;

        $output = '_';
        $output .= $this->_itoa64[$count & 0x3f];
        $output .= $this->_itoa64[($count >> 6) & 0x3f];
        $output .= $this->_itoa64[($count >> 12) & 0x3f];
        $output .= $this->_itoa64[($count >> 18) & 0x3f];

        $output .= $this->_encode64($input, 3);

        return $output;
    }

    protected function _genSaltBlowfish($input)
    {
        # This one needs to use a different order of characters and a
        # different encoding scheme from the one in encode64() above.
        # We care because the last character in our encoded string will
        # only represent 2 bits.  While two known implementations of
        # bcrypt will happily accept and correct a salt string which
        # has the 4 unused bits set to non-zero, we do not want to take
        # chances and we also do not want to waste an additional byte
        # of entropy.
        $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $output = '$2a$';
        $output .= chr(ord('0') + $this->_config['iteration_count'] / 10);
        $output .= chr(ord('0') + $this->_config['iteration_count'] % 10);
        $output .= '$';

        $i = 0;
        do {
            $c1 = ord($input[$i++]);
            $output .= $itoa64[$c1 >> 2];
            $c1 = ($c1 & 0x03) << 4;
            if ($i >= 16) {
                $output .= $itoa64[$c1];
                break;
            }

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 4;
            $output .= $itoa64[$c1];
            $c1 = ($c2 & 0x0f) << 2;

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 6;
            $output .= $itoa64[$c1];
            $output .= $itoa64[$c2 & 0x3f];
        } while (1);

        return $output;
    }

    protected function _hashPassword($password)
    {
        $random = '';

        if (CRYPT_BLOWFISH == 1 && !$this->_config['portable_hashes']) {
            $random = $this->_getRandomBytes(16);
            $hash = crypt($password, $this->_genSaltBlowfish($random));
            if (strlen($hash) == 60)
                return $hash;
        }

        if (CRYPT_EXT_DES == 1 && !$this->_config['portable_hashes']) {
            if (strlen($random) < 3)
                $random = $this->_getRandomBytes(3);
            $hash = crypt($password, $this->_genSaltExtended($random));
            if (strlen($hash) == 20)
                return $hash;
        }

        if (strlen($random) < 6) {
            $random = $this->_getRandomBytes(6);
        }

        $hash = $this->_cryptPrivate($password, $this->_genSaltPrivate($random));
        if (strlen($hash) == 34) {
            return $hash;
        }

        # Returning '*' on error is safe here, but would _not_ be safe
        # in a crypt(3)-like function used _both_ for generating new
        # hashes and for validating passwords against existing hashes.
        return '*';
    }

    protected function _checkHash($password, $stored_hash)
    {
        $hash = $this->_cryptPrivate($password, $stored_hash);
        if ($hash[0] == '*')
            $hash = crypt($password, $stored_hash);

        return $hash == $stored_hash;
    }

    /**
     *
     * Verifies a username handle and password.
     *
     * @return mixed An array of verified user information, or boolean false
     * if verification failed.
     *
     *
     */
    protected function _processLogin()
    {
        // get the dependency object of class Solar_Sql
        $obj = Solar::dependency('Solar_Sql', $this->_config['sql']);

        // get a selection tool using the dependency object
        $select = Solar::factory(
            'Solar_Sql_Select',
            array('sql' => $obj)
        );

        // list of optional columns as (property => field)
        $optional = array(
            'email'   => 'email_col',
            'moniker' => 'moniker_col',
            'uri'     => 'uri_col',
            'uid'     => 'uid_col',
        );

        // always get the user handle
        $cols = array($this->_config['handle_col']);

        // get optional columns
        foreach ($optional as $key => $val) {
            if ($this->_config[$val]) {
                $cols[] = $this->_config[$val];
            }
        }

        // build the select, fetch up to 2 rows (just in case there's actually
        // more than one, we don't want to select *all* of them).
        $select->from($this->_config['table'], $cols)
               ->where("{$this->_config['handle_col']} = ?", $this->_handle)
               ->multiWhere($this->_config['where'])
               ->limit(2);

        // get the results
        $rows = $select->fetchAll();

        // if we get back exactly 1 row, check the hash;
        // otherwise, it's more or less than exactly 1 row.
        if (count($rows) == 1) {

            $row = current($rows);
            $valid = $this->_checkHash($this->_passwd, $row[$this->_config['passwd_col']]);

            if (! $valid) {
                return false;
            }

            // set base info
            $info = array('handle' => $this->_handle);

            // set optional info from optional cols
            foreach ($optional as $key => $val) {
                if ($this->_config[$val]) {
                    $info[$key] = $row[$this->_config[$val]];
                }
            }

            // done
            return $info;

        } else {
            return false;
        }
    }
}
