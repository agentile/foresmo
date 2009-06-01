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
}
