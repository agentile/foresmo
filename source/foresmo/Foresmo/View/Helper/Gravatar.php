<?php
/**
 * Foresmo_View_Helper_Gravatar
 * Insert description here
 *
 */
class Foresmo_View_Helper_Gravatar extends Solar_View_Helper {


    /**
     * gravatar
     * return gravatar image url from email
     */
    public function gravatar($email, $size = 50, $default = '')
    {
        $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&default=".urlencode($default)."&size=".$size;
        return $grav_url;
    }

}