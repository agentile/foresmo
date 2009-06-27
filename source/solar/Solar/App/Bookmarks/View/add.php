<?php
/**
 * 
 * Solar_View template for adding a bookmark.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @subpackage Solar_App_Bookmarks
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: add.php 3015 2008-03-18 04:12:42Z pmjones $
 * 
 */
?>
<h1><?php echo $this->getText('HEADING_BOOKMARKS') ?></h1>
<h2><?php echo $this->getText('HEADING_ADD') ?></h2>

<p>[ <?php echo $this->anchor($this->backlink, 'BACKLINK') ?> ]</p>

<?php echo $this->form()
                ->auto($this->formdata)
                ->addProcess('save')
                ->addProcess('cancel')
                ->fetch();
