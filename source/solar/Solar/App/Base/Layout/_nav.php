<?php
/**
 * 
 * Partial layout template for the "nav" (site navigation) div.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: _nav.php 2556 2007-06-27 20:58:18Z pmjones $
 * 
 */
?>
<h2 class="accessibility">Navigation</h2>
<ul class="clearfix">
    <?php
        foreach ((array) $this->layout_nav as $key => $val) {
            echo "<li";
            if ($this->layout_nav_active == $key) {
                echo ' class="active"';
            }
            echo '>';
            echo $this->action($key, $val);
            echo "</li>\n";
        }
    ?>
</ul>
