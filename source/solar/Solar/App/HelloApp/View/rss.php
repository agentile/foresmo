<?php
/**
 * 
 * RSS 2.0 view.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @subpackage Solar_App_HelloApp
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: rss.php 2911 2007-10-20 19:50:39Z pmjones $
 * 
 */
header('Content-Type: text/xml; charset=iso-8859-1');
echo '<?xml version="1.0" encoding="iso-8859-1" ?>' . "\n";
$request = Solar_Registry::get('request');
$server = $request->server();
?>
<rss version="2.0">
    <channel>
        <title>Solar: Hello World</title>
        <link><?php echo $this->escape($server['REQUEST_URI']) ?></link>
        <description>Example hello-app RSS feed</description>
        <pubDate><?php echo $this->date('', DATE_RFC822) ?></pubDate>
        <item>
            <category><?php echo $this->escape($this->code) ?></category>
            <title><?php echo $this->escape($this->text) ?></title>
            <pubDate><?php echo $this->date(time(), DATE_RFC822) ?></pubDate>
            <description><?php echo $this->escape($this->text) ?></description>
            <link><?php echo $this->escape($server['REQUEST_URI']) ?></link>
        </item>
    </channel>
</rss>