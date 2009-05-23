<?php
/**
 * 
 * Layout template with site navigation on the left and two columns of content.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: navleft-2col.php 2869 2007-10-13 13:43:00Z pmjones $
 * 
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
        "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
    // generate the <head>
    include $this->template('_head.php');
    
    // generate the <body>
    include $this->template('_body.php')
?>

</html>