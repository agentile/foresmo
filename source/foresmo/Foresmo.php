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
        $results = $options->fetchArray(array('where' => $where));
        foreach ($results as $result) {
            if ($result['name'] == 'blog_timezone') {
                $arr['blog_timezone'] = $result['value'];
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
                $fetched_time = (int) $v;
                if ($k === 'pubdate') {
                    $posts['pubdate_ts'] = $fetched_time;
                }
                $timezone = explode(':', self::$timezone);
                if ($timezone[0][0] == '-') {
                    $first = substr($timezone[0], 1);
                    $change = $first * 60 * 60;
                    if ($timezone[1] == '30') {
                        $change = $change + 1800;
                    }
                    $time = date(self::$date_format, $fetched_time - $change);
                } else {
                    $change = $timezone[0] * 60 * 60;
                    if ($timezone[1] == '30') {
                        $change = $change + 1800;
                    }
                    $time = date(self::$date_format, $fetched_time + $change);
                }
                $posts[$k] = $time;
            }
        }
    }

    /**
    * sanitize
    * Sanitize text output within arrays
    *
    * @param $post
    * @param $track
    * @return void
    */
    public static function sanitize(&$posts, $track = array())
    {
        foreach ($posts as $k => $v) {
            if (is_array($v)) {
                $track[] = $k;
                self::sanitize($posts[$k], $track);
                array_pop($track);
            } elseif ($k === 'title'
                || ($k === 'content' && (count($track) > 1))
                || $k === 'modified'
                || $k === 'name'
                || $k === 'email'
                || $k === 'tag') {

                $posts[$k] = htmlentities($posts[$k], ENT_QUOTES, 'UTF-8');
            }
        }
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
}
