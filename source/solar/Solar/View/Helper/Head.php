<?php
/**
 * 
 * Helper to collect <head> elements and display them in the correct order.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 * @author Clay Loveless <clay@killersoft.com>
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Head.php 3401 2008-09-17 00:26:49Z pmjones $
 * 
 */
class Solar_View_Helper_Head extends Solar_View_Helper
{
    /**
     * 
     * The indent string for each element; default is 4 spaces.
     * 
     * @var string
     * 
     */
    protected $_indent = '    ';
    
    /**
     * 
     * The <title> value.
     * 
     * @var string
     * 
     */
    protected $_title = null;
    
    /**
     * 
     * Array of <meta> values.
     * 
     * @var array
     * 
     */
    protected $_meta = array();
    
    /**
     * 
     * The <base> value.
     * 
     * @var string
     * 
     */
    protected $_base = null;
    
    /**
     * 
     * Array of <link> values.
     * 
     * @var array
     * 
     */
    protected $_link = array();
    
    /**
     * 
     * Array of baseline <style> values that come before all other styles.
     * 
     * @var array
     * 
     */
    protected $_style_base = array();
    
    /**
     * 
     * Array of additional <style> values that come after the baseline styles.
     * 
     * @var array
     * 
     */
    protected $_style = array();
    
    /**
     * 
     * Array of baseline <script> values that come before all other scripts.
     * 
     * @var array
     * 
     */
    protected $_script_base = array();
    
    /**
     * 
     * Array of additional <script> values that come after the baseline
     * scripts.
     * 
     * @var array
     * 
     */
    protected $_script = array();
    
    /**
     * 
     * Array of inline <script> code.
     * 
     * @var array
     * 
     */
    protected $_script_inline = array();
    
    /**
     * 
     * Main helper method; fluent interface.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function head()
    {
        return $this;
    }
    
    /**
     * 
     * Sets the indent string.
     * 
     * @param string $indent The indent string.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function setIndent($indent)
    {
        $this->_indent = $indent;
        return $this;
    }
    
    /**
     * 
     * Sets the <title> string.
     * 
     * @param string $title The title string.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }
    
    /**
     * 
     * Adds a <meta> tag.
     * 
     * @param array $attribs Attributes for the tag.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addMeta($attribs)
    {
        $this->_meta[] = (array) $attribs;
        return $this;
    }
    
    /**
     * 
     * Adds a <meta> HTTP-Equivalent tag.
     * 
     * @param string $http_equiv The equivalent HTTP header label.
     * 
     * @param string $content The equivalent HTTP header value.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addMetaHttp($http_equiv, $content)
    {
        $this->_meta[] = array(
            'http-equiv' => $http_equiv,
            'content'    => $content,
        );
        return $this;
    }
    
    /**
     * 
     * Adds a <meta> name tag.
     * 
     * @param string $name The meta "name" label.
     * 
     * @param string $content The meta "name" value.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addMetaName($name, $content)
    {
        $this->_meta[] = array(
            'name'    => $name,
            'content' => $content,
        );
        return $this;
    }
    
    /**
     * 
     * Sets the <base> URI string.
     * 
     * @param string $base The base URI string.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function setBase($base)
    {
        $this->_base = $base;
        return $this;
    }
    
    /**
     * 
     * Adds a <link> tag.
     * 
     * @param array $attribs Attributes for the tag.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addLink($attribs)
    {
        $this->_link[] = (array) $attribs;
        return $this;
    }
    
    /**
     * 
     * Adds a <style> tag as part of the "baseline" (foundation) styles.
     * Generally used by layouts, not views.
     * 
     * @param string $href The file HREF for the style source.
     * 
     * @param array $attribs Attributes for the tag.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addStyleBase($href, $attribs = null)
    {
        if (empty($this->_style_base[$href])) {
            $this->_style_base[$href] = array($href, (array) $attribs);
        }
        return $this;
    }
    
    /**
     * 
     * Adds a <style> tag as part of the "additional" (override) styles.
     * Generally used by views, not layouts.  If the file has already been
     * added, it does not get added again.
     * 
     * @param string $href The file HREF for the style source.
     * 
     * @param array $attribs Attributes for the tag.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addStyle($href, $attribs = null)
    {
        if (empty($this->_style[$href])) {
            $this->_style[$href] = array($href, (array) $attribs);
        }
        return $this;
    }
    
    /**
     * 
     * Adds a <script> tag as part of the "baseline" (foundation) scripts.
     * Generally used by layouts, not views.  If the file has already been
     * added, it does not get added again.
     * 
     * @param string $src The file HREF for the script source.
     * 
     * @param array $attribs Attributes for the tag.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addScriptBase($src, $attribs = null)
    {
        if (empty($this->_script_base[$src])) {
            $this->_script_base[$src] = array($src, (array) $attribs);
        }
        return $this;
    }
    
    /**
     * 
     * Adds a <script> tag as part of the "additional" (override) scripts.
     * Generally used by views, not layouts.  If the file has already been
     * added, it does not get added again.
     * 
     * @param string $src The file HREF for the script source.
     * 
     * @param array $attribs Attributes for the tag.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addScript($src, $attribs = null)
    {
        if (empty($this->_script[$src])) {
            $this->_script[$src] = array($src, (array) $attribs);
        }
        return $this;
    }
    
    /**
     * 
     * Adds a <script> tag with inline code.
     * 
     * @param string $code The inline code for the tag.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function addScriptInline($code)
    {
        $this->_script_inline[] = $code;
        return $this;
    }
    
    /**
     * 
     * Builds and returns all the tags for the <head> section.
     * 
     * @return Solar_View_Helper_Head
     * 
     */
    public function fetch()
    {
        // array of lines for HTML output
        $html = array();
        
        // title
        if (! empty($this->_title)) {
            $html[] = $this->_view->title($this->_title);
        }
        
        // metas
        foreach ((array) $this->_meta as $val) {
            $html[] = $this->_view->meta($val);
        }
        
        // base
        if (! empty($this->_base)) {
            $html[] = $this->_view->base($this->_base);
        }
        
        // links
        foreach ((array) $this->_link as $val) {
            $html[] = $this->_view->link($val);
        }
        
        // baseline styles
        foreach ((array) $this->_style_base as $val) {
            $html[] = $this->_view->style($val[0], $val[1]);
        }
        
        // additional styles
        foreach ((array) $this->_style as $val) {
            $html[] = $this->_view->style($val[0], $val[1]);
        }
        
        // baseline scripts
        foreach ((array) $this->_script_base as $val) {
            $html[] = $this->_view->script($val[0], $val[1]);
        }
        
        // additional scripts (source)
        foreach ((array) $this->_script as $val) {
            $html[] = $this->_view->script($val[0], $val[1]);
        }
        
        // inline scripts collected into a single block
        $code = $this->_fetchScriptInline();
        if ($code) {
            $html[] = $this->_view->scriptInline($code);
        }
        
        // concat with indents and newlines, and done!
        return $this->_indent
             . implode("\n{$this->_indent}", $html)
             . "\n";
    }
    
    /**
     * 
     * Support method to fetch inline scripts; child classes may wish to
     * override this to wrap in a library-specific "when document is ready"
     * logic.
     * 
     * @return string The code for all inline scripts.
     * 
     */
    protected function _fetchScriptInline()
    {
        $code = null;
        foreach ((array) $this->_script_inline as $val) {
            $code .= $val . "\n\n";
        }
        return rtrim($code);
    }
}