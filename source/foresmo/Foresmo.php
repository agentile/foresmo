<?php
/**
 * Foresmo
 * Foresmo General Arch Class
 *
 * @category  Core
 * @package   Foresmo
 * @author    Anthony Gentile, Bryden Tweedy
 * @version   0.17
 * @since     0.15
 */
class Foresmo extends Solar_Base {

    public static $date_format;
    public static $timezone;

    /**
     * getTimeInfo
     * get date format and timezone from db
     *
     * @return array
     */
    public static function getTimeInfo()
    {
        $arr = array();
        $options = Solar::factory('Foresmo_Model_Options');
        $where = array(
            'name = ? OR name = ?' => array(
                'blog_date_format',
                'blog_timezone'
            )
        );
        $results = $options->fetchAllAsArray(array('where' => $where));
        foreach ($results as $result) {
            if ($result['name'] == 'blog_timezone') {
                $arr['blog_timezone'] = $result['value'];
                ini_set('date.timezone', $result['value']);
            }
            if ($result['name'] == 'blog_date_format') {
                $arr['blog_date_format'] = $result['value'];
            }
        }
        return $arr;
    }

    /**
     * getIP
     * get IP of user
     *
     * @return string
     */
    public static function getIP()
    {
        $ip = '0.0.0.0';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_VIA'])) {
            $ip = $_SERVER['HTTP_VIA'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * dateFilter
     * Modify datetime fields for timezone and date format settings
     *
     * @param $posts
     *
     * @return void
     */
    public static function dateFilter(&$posts)
    {
        foreach ($posts as $k => $v) {
            if (is_array($v)) {
                self::dateFilter($posts[$k]);
            }
            if ($k === 'date' || $k === 'pubdate' || $k === 'modified') {
                $unix_ts = (int) $v;
                if ($k === 'pubdate') {
                    $posts['pubdate_ts'] = $unix_ts;
                }
                $dt = new DateTime("@{$unix_ts}");
                $dt->setTimezone(new DateTimeZone(self::$timezone));
                $posts[$k] = $dt->format(self::$date_format);
            }
        }
    }

    /**
    * escape
    * escape text
    *
    * @param mixed $data array or string.
    * @return void
    */
    public static function escape(&$data, $track = array())
    {
        if (!is_array($data)) {
            htmlentities($data, ENT_COMPAT, 'UTF-8');
            return;
        }
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $track[] = $k;
                self::escape($data[$k], $track);
                array_pop($track);
            } elseif ($k === 'title'
                || ($k === 'content' && (count($track) > 1))
                || ($k === 'excerpt' && (count($track) > 1))
                || $k === 'modified'
                || $k === 'name'
                || $k === 'email'
                || $k === 'tag') {

                $data[$k] = htmlentities($data[$k], ENT_COMPAT, 'UTF-8');
            }
        }
    }

    /**
     * makeExcerpt
     * Create excerpt from post
     * TODO: make this smart to disregard html tags ... and to properly close
     * opened html tags.
     *
     * @param $str
     * @param $word_count
     * @param $trailing
     *
     * @return string
     */
    public static function makeExcerpt($str, $word_count = 60, $trailing = '...')
    {
        $words = explode(' ', $str);
        if (count($words) > $word_count) {
            $str = implode(' ', array_slice($words, 0, $word_count)) . $trailing;
        }
        return $str;
    }

    /**
     * makeSlug
     * Change string to url friendly slug
     *
     * @param $str
     * @param $delim  default '-'
     *
     * @return string
     */
    public static function makeSlug($str, $delim = '-')
    {
        $str = preg_replace('/[^a-z0-9-]/', $delim, strtolower(trim($str)));
        $str = preg_replace("/{$delim}+/", $delim, trim($str, $delim));
        return $str;
    }

    /**
     * Generate and return a random string
     *
     * The default string returned is 8 alphanumeric characters.
     *
     * The type of string returned can be changed with the output parameter.
     * Four types are available: alpha, numeric, alphanum and hexadec.
     *
     * If the output parameter does not match one of the above, then the string
     * supplied is used.
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     2.1.0
     * @link        http://aidanlister.com/repos/v/function.str_rand.php
     * @param       int     $length  Length of string to be generated
     * @param       string  $seeds   Seeds string should be generated from
     */
    public static function randomString($length = 8, $output = 'alphanum')
    {
        // Possible seeds
        $outputs['alpha']    = 'abcdefghijklmnopqrstuvwxyz';
        $outputs['numeric']  = '0123456789';
        $outputs['alphanum'] = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $outputs['alphanumi'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $outputs['hexadec']  = '0123456789abcdef';

        // Choose seed
        if (isset($outputs[$output])) {
            $output = $outputs[$output];
        }

        // Seed generator
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);

        // Generate
        $str = '';
        $output_count = strlen($output);
        for ($i = 0; $length > $i; $i++) {
            $str .= $output{mt_rand(0, $output_count - 1)};
        }

        return $str;
    }

    /**
     * Fetch time zones sans city offsets
     * http://us.php.net/manual/en/function.date-default-timezone-set.php#84459
     *
     * @return array
     */
    public static function fetchTimeZones()
    {
        $timezones = DateTimeZone::listAbbreviations();

        $cities = array();
        foreach( $timezones as $key => $zones )
        {
            foreach( $zones as $id => $zone )
            {
                /**
                 * Only get timezones explicitely not part of "Others".
                 * @see http://www.php.net/manual/en/timezones.others.php
                 */
                if ( preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $zone['timezone_id'] ) )
                    $cities[$zone['timezone_id']][] = $key;
            }
        }

        // For each city, have a comma separated list of all possible timezones for that city.
        foreach( $cities as $key => $value )
            $cities[$key] = join(', ', $value);

        // Only keep one city (the first and also most important) for each set of possibilities.
        $cities = array_unique( $cities );

        // Sort by area/city name.
        ksort( $cities );

        return $cities;
    }

}
