<?php
/**
 * 
 * Generic application view for displaying errors.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: error.php 2446 2007-04-21 21:07:48Z pmjones $
 * 
 */
?>
<div class='error'>
    <ul>
    <?php foreach ((array) $this->errors as $err): ?>
        <li><?php echo $this->getText($err) ?></li>
    <?php endforeach ?>
    </ul>
</div>
