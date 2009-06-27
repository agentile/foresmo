<?php
/**
 * 
 * Helper for building CSS-based forms.
 * 
 * This is a fluent class; all method calls except fetch() return
 * $this, which means you can chain method calls for easier readability.
 * 
 * @category Solar
 * 
 * @package Solar_View_Helper_Form
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Form.php 3850 2009-06-24 20:18:27Z pmjones $
 * 
 */
class Solar_View_Helper_Form extends Solar_View_Helper
{
    /**
     * 
     * Default configuration values.
     * 
     * @config array attribs Default attributes to use in the <form> tag.
     * 
     * @config string descr_elem The tag to use for description elements.
     * 
     * @config string descr_tag Put description inside this tag.
     * 
     * @config string descr_class Use this CSS class for descriptions.
     * 
     * @var array
     */
    protected $_Solar_View_Helper_Form = array(
        'attribs' => array(),
        'descr_elem' => 'dd',
        'descr_tag' => 'div',
        'descr_class' => 'descr',
    );
    
    /**
     * 
     * Attributes for the form tag.
     * 
     * @var array
     * 
     */
    protected $_attribs = array();
    
    /**
     * 
     * Collection of form-level feedback messages.
     * 
     * @var array
     * 
     */
    protected $_feedback = array();
    
    /**
     * 
     * Collection of hidden elements.
     * 
     * @var array
     * 
     */
    protected $_hidden = array();
    
    /**
     * 
     * Stack of element and layout pieces for the form.
     * 
     * @var array
     * 
     */
    protected $_stack = array();
    
    /**
     * 
     * Tracks element IDs so we can have unique IDs for each element.
     * 
     * @var array
     * 
     */
    protected $_id_count = array();
    
    /**
     * 
     * CSS classes to use for element and feedback types.
     * 
     * Array format is type => css-class.
     * 
     * @var array
     * 
     */
    protected $_css_class = array(
        'button'    => 'input-button',
        'checkbox'  => 'input-checkbox',
        'date'      => 'input-date',
        'file'      => 'input-file',
        'hidden'    => 'input-hidden',
        'options'   => 'input-option',
        'password'  => 'input-password',
        'radio'     => 'input-radio',
        'reset'     => 'input-reset',
        'select'    => 'input-select',
        'submit'    => 'input-submit',
        'text'      => 'input-text',
        'textarea'  => 'input-textarea',
        'time'      => 'input-time',
        'timestamp' => 'input-timestamp',
        'failure'   => 'failure',
        'success'   => 'success',
        'require'   => 'require',
    );
    
    /**
     * 
     * The current failure/success status.
     * 
     * @var bool
     * 
     */
    protected $_status = null;
    
    /**
     * 
     * Default form tag attributes.
     * 
     * @var array
     * 
     */
    protected $_default_attribs = array(
        'action'  => null,
        'method'  => 'post',
        'enctype' => 'multipart/form-data',
    );
    
    /**
     * 
     * Default info for each element.
     * 
     * @var array
     * 
     */
    protected $_default_info = array(
        'type'     => '',
        'name'     => '',
        'value'    => '',
        'label'    => '',
        'descr'    => '',
        'status'   => null,
        'attribs'  => array(),
        'options'  => array(),
        'disable'  => false,
        'require'  => false,
        'invalid' => array(),
    );
    
    /**
     * 
     * Details about the request environment.
     * 
     * @var Solar_Request
     * 
     */
    protected $_request;
    
    /**
     * 
     * When the raw description XHTML is rendered, do so within this type of
     * tag block (default is 'div' for a `<div>...</div>` tag block.)
     * 
     * @var string
     * 
     */
    protected $_descr_tag = 'div';
    
    /**
     * 
     * Use this class attribute when rendering the raw descripton XHTML.
     * Default is 'descr'; e.g., `<div class="descr">...</div>`.
     * 
     * @var string
     * 
     */
    protected $_descr_class = 'descr';
    
    /**
     * 
     * When rendering the raw description XHTML, do so within this structural
     * element; default is 'dd' for the value portion (as vs 'dl' for the
     * label portion).
     * 
     * @var string
     * 
     */
    protected $_descr_elem = 'dd';
    
    /**
     * 
     * When building the form, are we currently in an element list?
     * 
     * @var bool
     * 
     */
    protected $_in_elemlist = false;
    
    /**
     * 
     * When building the form, are we currently in a grouping?
     * 
     * @var bool
     * 
     */
    protected $_in_group = false;
    
    /**
     * 
     * When building the form, are we currently in a fieldset?
     * 
     * @var bool
     * 
     */
    protected $_in_fieldset = false;
    
    /**
     * 
     * When building a group of elements, collect the "invalid" messages here.
     * 
     * @var string
     * 
     */
    protected $_group_invalid = null;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config Configuration value overrides, if any.
     * 
     */
    public function __construct($config = null)
    {
        // "real" construction
        parent::__construct($config);
        
        // get the current request environment
        $this->_request = Solar_Registry::get('request');
        
        // make sure we have a default action
        $this->_default_attribs['action'] = $this->_request->server('REQUEST_URI');
        
        // reset the form propertes
        $this->reset();
    }
    
    /**
     * 
     * Magic __call() for addElement() using element helpers.
     * 
     * Allows $this->elementName() internally, and
     * $this->form()->elementType() externally.
     * 
     * @param string $type The form element type (text, radio, etc).
     * 
     * @param array $args Arguments passed to the method call; only
     * the first argument is used, the $info array.
     * 
     * @return string The form element helper output.
     * 
     */
    public function __call($type, $args)
    {
        $info = $args[0];
        $info['type'] = $type;
        return $this->addElement($info);
    }
    
    /**
     * 
     * Magic __toString() to print out the form automatically.
     * 
     * Note that this calls fetch() and will reset the form afterwards.
     * 
     * @return string The form output.
     * 
     */
    public function __toString()
    {
        return $this->fetch();
    }
    
    /**
     * 
     * Main method interface to Solar_View.
     * 
     * @param Solar_Form|array $spec If a Solar_Form object, does a
     * full auto build and fetch of a form based on the Solar_Form
     * properties.  If an array, treated as attribute keys and values
     * for the <form> tag.
     * 
     * @return string|Solar_View_Helper_Form
     * 
     */
    public function form($spec = null)
    {
        if ($spec instanceof Solar_Form) {
            // auto-build and fetch from a Solar_Form object
            $this->reset();
            $this->auto($spec);
            return $this->fetch();
        } elseif (is_array($spec)) {
            // set attributes from an array
            foreach ($spec as $key => $val) {
                $this->setAttrib($key, $val);
            }
            return $this;
        } else {
            // just return self
            return $this;
        }
    }
    
    /**
     * 
     * Sets a form-tag attribute.
     * 
     * @param string $key The attribute name.
     * 
     * @param string $val The attribute value.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function setAttrib($key, $val = null)
    {
        $this->_attribs[$key] = $val;
        return $this;
    }
    
    /**
     * 
     * Sets multiple form-tag attributes.
     * 
     * @param Solar_Form|array $spec If a Solar_Form object, uses the $attribs
     * property.  If an array, uses the keys as the attribute names and the 
     * values as the attribute values.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function setAttribs($spec)
    {
        if ($spec instanceof Solar_Form) {
            $attribs = (array) $spec->attribs;
        } else {
            $attribs = (array) $spec;
        }
        
        foreach ($attribs as $key => $val) {
            $this->setAttrib($key, $val);
        }
        
        return $this;
    }
    
    /**
     * 
     * Adds to the form-level feedback message array.
     * 
     * @param string|array $spec The feedback message(s).
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function addFeedback($spec)
    {
        $this->_feedback = array_merge($this->_feedback, (array) $spec);
        return $this;
    }
    
    /**
     * 
     * Adds a single element to the form.
     * 
     * @param array $info The element information.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function addElement($info)
    {
        // make sure we have all the info keys we need
        $info = array_merge($this->_default_info, $info);
        
        // fix up certain pieces
        $this->_fixElementType($info);
        $this->_fixElementName($info);
        $this->_fixElementId($info);
        $this->_fixElementClass($info);
        
        // place in the normal stack, or as hidden?
        if (strtolower($info['type']) == 'hidden') {
            // hidden elements are a special case
            $this->_hidden[] = $info;
        } else {
            // non-hidden element
            $this->_stack[] = array('element', $info);
        }
        
        return $this;
    }
    
    /**
     * 
     * Adds multiple elements to the form.
     * 
     * @param Solar_Form|array $spec If a Solar_Form object, uses the
     * $elements property as the element source.  If an array, it is treated
     * as a sequential array of element information arrays.
     * 
     * @param array $list A white-list of element names to add from the $spec.
     * If empty, all elements from the $spec are added.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function addElements($spec, $list = null)
    {
        if ($spec instanceof Solar_Form) {
            $elements = (array) $spec->elements;
        } else {
            $elements = (array) $spec;
        }
        
        $list = (array) $list;
        if ($list) {
            // add only listed elements
            foreach ($elements as $info) {
                if (in_array($list, $info['name'])) {
                    // it's on the list, add it
                    $this->addElement($info);
                }
            }
        } else {
            // add all elements
            foreach ($elements as $info) {
                $this->addElement($info);
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * Adds a submit button named 'process' to the form, using a translated
     * locale key stub as the submit value.
     * 
     * @param string $key The locale key stub.  E.g., $key is 'save', the
     * submit-button value is the locale translated 'PROCESS_SAVE' string.
     * 
     * @param array $info Additional element info.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function addProcess($key, $info = null)
    {
        $key = 'PROCESS_' . strtoupper($key);
        
        $base = array(
            'type'  => 'submit',
            'name'  => 'process',
            'value' => $this->_view->getTextRaw($key),
        );
        
        $info = array_merge($base, (array) $info);
        
        if (empty($info['attribs']['id'])) {
            $id = str_replace('_', '-', strtolower($key));
            $info['attribs']['id'] = $id;
        }
        
        return $this->addElement($info);
    }
    
    /**
     * 
     * Adds a group of process buttons with an optional label.
     * 
     * @param array $list An array of process button names. Normally you would
     * pass a sequential array ('save', 'delete', 'cancel').  If you like, you
     * can pass the process name as the key, with an associative array value
     * of element info for that particular submit button.
     * 
     * @param string $label The label for the group.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function addProcessGroup($list, $label = null)
    {
        $this->beginGroup($label);
        
        foreach ((array) $list as $key => $val) {
            if (is_array($val)) {
                // $key stays the same
                $info = $val;
            } else {
                // sequential array; the value is the process key.
                $key = $val;
                // no info
                $info = array();
            }
            
            // add the process within the group
            $this->addProcess($key, $info);
        }
        
        $this->endGroup();
        
        return $this;
    }
    
    /**
     * 
     * Sets the form validation status.
     * 
     * @param bool $flag True if you want to say the form is valid,
     * false if you want to say it is not valid, null if you want to 
     * say that validation has not been attempted.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function setStatus($flag)
    {
        if ($flag === null) {
            $this->_status = null;
        } else {
            $this->_status = (bool) $flag;
        }
        return $this;
    }
    
    /**
     * 
     * Gets the form validation status.
     * 
     * @return bool True if the form is currently valid, false if not,
     * null if validation has not been attempted.
     * 
     */
    public function getStatus()
    {
        return $this->_status;
    }
    
    /**
     * 
     * Automatically adds multiple pieces to the form.
     * 
     * @param Solar_Form|array $spec If a Solar_Form object, adds
     * attributes, elements and feedback from the object properties. 
     * If an array, treats it as a a collection of element info
     * arrays and adds them.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function auto($spec)
    {
        if ($spec instanceof Solar_Form) {
            
            // add from a Solar_Form object.
            // set the form status.
            $this->setStatus($spec->getStatus());
            
            // set the form attributes
            foreach ((array) $spec->attribs as $key => $val) {
                $this->setAttrib($key, $val);
            }
            
            // add form-level feedback
            $this->addFeedback($spec->feedback);
            
            // add elements
            foreach ((array) $spec->elements as $info) {
                $this->addElement($info);
            }
            
        } elseif (is_array($spec)) {
            
            // add from an array of elements.
            foreach ($spec as $info) {
                $this->addElement($info);
            }
        }
        
        // done
        return $this;
    }
    
    /**
     * 
     * Begins a group of form elements under a single label.
     * 
     * @param string $label The label text.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function beginGroup($label = null)
    {
        $this->_stack[] = array('group', array(true, $label));
        return $this;
    }
    
    /**
     * 
     * Ends a group of form elements.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function endGroup()
    {
        $this->_stack[] = array('group', array(false, null));
        return $this;
    }
    
    /**
     * 
     * Begins a <fieldset> block with a legend/caption.
     * 
     * @param string $legend The legend or caption for the fieldset.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function beginFieldset($legend)
    {
        $this->_stack[] = array('fieldset', array(true, $legend));
        return $this;
    }
    
    /**
     * 
     * Ends a <fieldset> block.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function endFieldset()
    {
        $this->_stack[] = array('fieldset', array(false, null));
        return $this;
    }
    
    /**
     * 
     * Builds and returns the form output.
     * 
     * @param bool $with_form_tag If true (the default) outputs the form with
     * <form>...</form> tags.  If false, it does not.
     * 
     * @return string
     * 
     * @see The entire set of _build*() methods.
     * 
     */
    public function fetch($with_form_tag = true)
    {
        // stack of output pieces
        $html = array();
        
        // the opening form tag?
        if ($with_form_tag) {
            $this->_buildBegin($html);
        }
        
        // form-level feedback
        $this->_buildFeedback($html);
        
        // the hidden elements
        if ($this->_hidden) {
            $this->_buildHidden($html);
        }
        
        // the stack of non-hidden elements
        $this->_buildStack($html);
        
        // the closing form tag?
        if ($with_form_tag) {
            $this->_buildEnd($html);
        }
        
        // reset for the next pass
        $this->reset();
        
        // done, return the output pieces!
        return implode("\n", $html);
    }
    
    /**
     * 
     * Resets the form entirely.
     * 
     * @return Solar_View_Helper_Form
     * 
     */
    public function reset()
    {
        // attributes for the <form> tag
        $this->_attribs = array_merge(
            $this->_default_attribs,
            $this->_config['attribs']
        );
        
        // form-element descriptions
        $this->_descr_elem = $this->_config['descr_elem'];
        $this->_descr_tag = $this->_config['descr_tag'];
        $this->_descr_class = $this->_config['descr_class'];
        
        // make sure we force the description to be in either the <dt>
        // element or the <dd> portion
        if ($this->_descr_elem != 'dt') {
            $this->_descr_elem = 'dd';
        }
        
        // build-tracking properties
        $this->_in_elemlist = false;
        $this->_in_group    = false;
        $this->_in_fieldset = false;
        
        // everything else
        $this->_feedback = array();
        $this->_hidden = array();
        $this->_stack = array();
        $this->_status = null;
        $this->_id_count = array();
        
        return $this;
    }
    
    /**
     * 
     * Fixes the element info 'type' value; by default, it just throws an
     * exception when the 'type' is empty.
     * 
     * @param array &$info A reference to the element info array.
     * 
     * @return void
     * 
     */
    protected function _fixElementType(&$info)
    {
        if (empty($info['type'])) {
            throw $this->_exception('ERR_NO_ELEMENT_TYPE', $info);
        }
    }
    
    /**
     * 
     * Fixes the element info 'name' value; by default, it just throws an
     * exception when the 'name' is empty on non-xhtml element types.
     * 
     * @param array &$info A reference to the element info array.
     * 
     * @return void
     * 
     */
    protected function _fixElementName(&$info)
    {
        if (empty($info['name']) && $info['type'] != 'xhtml') {
            throw $this->_exception('ERR_NO_ELEMENT_NAME', $info);
        }
    }
    
    /**
     * 
     * Fixes the element info 'id' value.
     * 
     * When no ID is present, auto-sets an ID from the element name.
     * 
     * Appends sequential integers to the ID as needed to deconflict matching
     * ID values.
     * 
     * @param array &$info A reference to the element info array.
     * 
     * @return void
     * 
     */
    protected function _fixElementId(&$info)
    {
        // auto-set the ID?
        if (empty($info['attribs']['id'])) {
            // convert name[key][subkey] to name-key-subkey
            $info['attribs']['id'] = str_replace(
                    array('[', ']'),
                    array('-', ''),
                    $info['name']
            );
        }
        
        // convenience variable
        $id = $info['attribs']['id'];
        
        // is this id already in use?
        if (empty($this->_id_count[$id])) {
            // not used yet, start tracking it
            $this->_id_count[$id] = 1;
        } else {
            // already in use, increment the count.
            // for example, 'this-id' becomes 'this-id-1',
            // next one is 'this-id-2', etc.
            $id .= "-" . $this->_id_count[$id] ++;
            $info['attribs']['id'] = $id;
        }
    }
    
    /**
     * 
     * Fixes the element info 'class' value to add classes for the element
     * type, ID, require, and validation status -- but only if the class is
     * empty to begin with.
     * 
     * @param array &$info A reference to the element info array.
     * 
     * @return void
     * 
     */
    protected function _fixElementClass(&$info)
    {
        // skip is classes are already set
        if (! empty($info['attribs']['class'])) {
            return;
        }
            
        // add a CSS class for the element type
        if (! empty($this->_css_class[$info['type']])) {
            $info['attribs']['class'] = $this->_css_class[$info['type']];
        } else {
            $info['attribs']['class'] = '';
        }
        
        // also use the element ID for further overrides
        $info['attribs']['class'] .= ' ' . $info['attribs']['id'];
        
        // passed validation?
        if ($info['status'] === true) {
            $info['attribs']['class'] .= ' ' . $this->_css_class['success'];
        }
        
        // failed validation?
        if ($info['status'] === false) {
            $info['attribs']['class'] .= ' ' . $this->_css_class['failure'];
        }
        
        // required?
        if ($info['require']) {
            $info['attribs']['class'] .= ' ' . $this->_css_class['require'];
        }
    }
    
    /**
     * 
     * Returns text indented to a number of levels, accounting for whether
     * or not we are in a fieldset.
     * 
     * @param int $num The number of levels to indent.
     * 
     * @param string $text The text to indent.
     * 
     * @return string The indented text.
     * 
     */
    protected function _indent($num, $text = null)
    {
        if ($this->_in_fieldset) {
            $num += 1;
        }
        
        return str_pad('', $num * 4) . $text;
    }
    
    /**
     * 
     * Builds the opening <form> tag for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildBegin(&$html)
    {
        $html[] = '<form' . $this->_view->attribs($this->_attribs) . '>';
    }
    
    /**
     * 
     * Builds the closing </form> tag for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildEnd(&$html)
    {
        $html[] = '</form>';
    }
    
    /**
     * 
     * Builds the form-level feedback tag for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildFeedback(&$html)
    {
        if (empty($this->_feedback)) {
            return;
        }
        
        // what status class should we use?
        if ($this->_status === true) {
            $class = $this->_css_class['success'];
        } elseif ($this->_status === false) {
            $class = $this->_css_class['failure'];
        } else {
            $class = null;
        }
        
        if ($class) {
            $open = '<ul class="' . $this->_view->escape($class) . '">';
        } else {
            $open = '<ul>';
        }
        
        $html[] = $this->_indent(1, $open);
        
        foreach ((array) $this->_feedback as $item) {
            $item = '<li>' . $this->_view->escape($item) . '</li>';
            $html[] = $this->_indent(2, $item);
        }
        
        $html[] = $this->_indent(1, "</ul>");
    }
    /**
     * 
     * Builds the stack of hidden elements for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildHidden(&$html)
    {
        // wrap in a hidden fieldset for XHTML-Strict compliance
        $html[] = '    <fieldset style="display: none;">';
        foreach ($this->_hidden as $info) {
            $html[] = '        ' . $this->_view->formHidden($info);
        }
        $html[] = '    </fieldset>';
    }
    
    /**
     * 
     * Builds the stack of non-hidden elements for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildStack(&$html)
    {
        $this->_in_fieldset = false;
        $this->_in_group    = false;
        $this->_in_elemlist  = false;
        
        foreach ($this->_stack as $key => $val) {
            $type = $val[0];
            $info = $val[1];
            if ($type == 'element') {
                $this->_buildElement($html, $info);
            } elseif ($type == 'group') {
                $this->_buildGroup($html, $info);
            } elseif ($type == 'fieldset') {
                $this->_buildFieldset($html, $info);
            }
        }
        
        // close up any loose ends
        $this->_buildGroupEnd($html);
        $this->_buildElementListEnd($html);
        $this->_buildFieldsetEnd($html);
    }
    
    /**
     * 
     * Builds a single element label and value for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param array $info The array of element information.
     * 
     * @return void
     * 
     */
    protected function _buildElement(&$html, $info)
    {
        $this->_buildElementListBegin($html);
        $this->_buildElementLabel($html, $info);
        $this->_buildElementValue($html, $info);
    }
    
    /**
     * 
     * Modifies the element label information before building for output.
     * 
     * @param array &$info A reference to the element label information.
     * 
     * @return void
     * 
     */
    protected function _buildElementLabelInfo(&$info)
    {
        // checkboxes that are not in groups should not be ID'd to their label
        if (strtolower($info['type']) == 'checkbox' && ! $this->_in_group) {
            // don't unset or we get notices; null is good enough
            $info['attribs']['id'] = null;
        }
    }
    
    /**
     * 
     * Builds the label portion of an element for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param array $info The array of element information.
     * 
     * @return void
     * 
     */
    protected function _buildElementLabel(&$html, $info)
    {
        if ($this->_in_group) {
            // no labels while in an element group
            return;
        }
        
        // modify information **just for the label portion**
        $this->_buildElementLabelInfo($info);
        
        // the label text
        $label = $this->_view->getText($info['label']);
        
        // does the element have an ID?
        $id = $this->_view->escape($info['attribs']['id']);
        if ($id) {
            $for = " for=\"$id\"";
        } else {
            $for = '';
        }
        
        // is the element required?
        if ($info['require']) {
            $require = ' class="' . $this->_css_class['require'] . '"';
        } else {
            $require = '';
        }
            
        // open the label
        $html[] = $this->_indent(2, "<dt$require>");
        $html[] = $this->_indent(3, "<label$require$for>$label</label>");
        
        // do descriptions go in the label?
        if ($this->_descr_elem == 'dt') {
            $this->_buildElementDescr($html, $info);
        }
        
        // close the label
        $html[] = $this->_indent(2, "</dt>");
    }
    
    /**
     * 
     * Modifies the element value information before building for output.
     * 
     * @param array &$info A reference to the element value information.
     * 
     * @return void
     * 
     */
    protected function _buildElementValueInfo(&$info)
    {
        // checkboxes that are not in groups don't get an "extra" label
        if (strtolower($info['type']) == 'checkbox' && ! $this->_in_group) {
            $info['label'] = null;
        }
    }
    
    /**
     * 
     * Builds the value portion of an element for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param array $info The array of element information.
     * 
     * @return void
     * 
     */
    protected function _buildElementValue(&$html, $info)
    {
        // modify information **just for the value portion**
        $this->_buildElementValueInfo($info);
        
        try {
            // look for the requested element helper
            $method = 'form' . ucfirst($info['type']);
            $helper = $this->_view->getHelper($method);
        } catch (Solar_Class_Stack_Exception_ClassNotFound $e) {
            // use 'text' helper as a fallback
            $method = 'formText';
            $helper = $this->_view->getHelper($method);
        }
        
        // get the element output
        $element = $helper->$method($info);
        
        // handle differently if we're in a group
        if ($this->_in_group) {
            $html[] = $this->_indent(3, $element);
            $this->_buildGroupInvalid($info);
            return;
        }
        
        // is the element required?
        if ($info['require']) {
            $require = ' class="' . $this->_css_class['require'] . '"';
        } else {
            $require = '';
        }
        
        // open the element value
        $html[] = $this->_indent(2, "<dd$require>");
        $html[] = $this->_indent(3, $element);
        
        // add invalid messages
        $this->_buildElementInvalid($html, $info);
        
        // add description
        if ($this->_descr_elem == 'dd') {
            $this->_buildElementDescr($html, $info);
        }
        
        // close the value
        $html[] = $this->_indent(2, "</dd>");
    }
    
    /**
     * 
     * Builds the list of "invalid" messages for a single element.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param array $info The array of element information.
     * 
     * @return void
     * 
     */
    protected function _buildElementInvalid(&$html, $info)
    {
        if (empty($info['invalid'])) {
            return;
        }
        
        $html[] = $this->_indent(3, '<ul>');
        
        foreach ((array) $info['invalid'] as $item) {
            $item = '<li>' . $this->_view->escape($item) . '</li>';
            $html[] = $this->_indent(4, $item);
        }
        
        $html[] = $this->_indent(3, "</ul>");
    }
    
    /**
     * 
     * Builds the element description for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param array $info The array of element information.
     * 
     * @return void
     * 
     */
    protected function _buildElementDescr(&$html, $info)
    {
        // only build a description if it's non-empty, and isn't a
        // DESCR_* "empty" locale value.
        if (! $info['descr'] || substr($info['descr'], 0, 6) == 'DESCR_') {
            return;
        }
        
        // open the tag ...
        $descr = "<" . $this->_view->escape($this->_descr_tag);
        
        // ... add a CSS class ...
        if ($this->_descr_class) {
            $descr .= ' class="'
                   . $this->_view->escape($this->_descr_class)
                   . '"';
        }
        
        // ... add the raw descr XHTML, and close the tag.
        $descr .= '>' . $info['descr']
               . '</' . $this->_view->escape($this->_descr_tag) . '>';
        
        $html[] = $this->_indent(3, $descr);
    }
    
    /**
     * 
     * Builds the beginning of an element list for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildElementListBegin(&$html)
    {
        if ($this->_in_elemlist) {
            // already in a list, don't begin again
            return;
        }
        
        $html[] = $this->_indent(1, '<dl>');
        $this->_in_elemlist = true;
    }
    
    /**
     * 
     * Builds the ending of an element list for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildElementListEnd(&$html)
    {
        if (! $this->_in_elemlist) {
            // can't end a list if not in one
            return;
        }
        
        $html[] = $this->_indent(1, '</dl>');
        $this->_in_elemlist = false;
    }
    
    /**
     * 
     * Builds a group beginning/ending for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param array $info The array of element information.
     * 
     * @return void
     * 
     */
    protected function _buildGroup(&$html, $info)
    {
        $flag  = $info[0];
        $label = $info[1];
        if ($flag) {
            $this->_buildGroupEnd($html);
            $this->_buildGroupBegin($html, $label);
        } else {
            $this->_buildGroupEnd($html);
        }
    }
    
    /**
     * 
     * Builds an element group label for output and begins the grouping.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param string $label The group label.
     * 
     * @return void
     * 
     */
    protected function _buildGroupBegin(&$html, $label)
    {
        if ($this->_in_group) {
            // already in a group, don't start another one
            return;
        }
        
        $this->_buildElementListBegin($html);
        $html[] = $this->_indent(2, "<dt>");
        $html[] = $this->_indent(3, "<label>$label</label>");
        $html[] = $this->_indent(2, "</dt>");
        $html[] = $this->_indent(2, "<dd>");
        $this->_group_invalid = null;
        $this->_in_group = true;
    }
    
    /**
     * 
     * Builds the end of an element group.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildGroupEnd(&$html)
    {
        if (! $this->_in_group) {
            // not in a group so can't end it
            return;
        }
        
        if ($this->_group_invalid) {
            $html[] = $this->_indent(3, $this->_group_invalid);
            $this->_group_invalid = null;
        }
        
        $html[] = $this->_indent(2, "</dd>");
        $this->_in_group = false;
    }
    
    /**
     * 
     * Builds the list of "invalid" messages while in an element group.
     * 
     * @param array $info The array of element information.
     * 
     * @return void
     * 
     */
    protected function _buildGroupInvalid($info)
    {
        $html = array();
        $this->_buildElementInvalid($html, $info);
        $this->_group_invalid .= implode("\n", $html);
    }
    
    /**
     * 
     * Builds a fieldset beginning/ending for output.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param array $info The array of element information.
     * 
     * @return void
     * 
     */
    protected function _buildFieldset(&$html, $info)
    {
        $flag   = $info[0];
        $legend = $this->_view->getText($info[1]);
        if ($flag) {
            
            // end any previous groups, lists, and sets
            $this->_buildGroupEnd($html);
            $this->_buildElementListEnd($html);
            $this->_buildFieldsetEnd($html);
            
            // start a new set
            $this->_buildFieldsetBegin($html, $legend);
            
        } else {
            
            // end previous groups, lists, and sets
            $this->_buildGroupEnd($html);
            $this->_buildElementListEnd($html);
            $this->_buildFieldsetEnd($html);
            
        }
    }
    
    /**
     * 
     * Builds the beginning of a fieldset and its legend.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @param string $legend The legend for the fieldset.
     * 
     * @return void
     * 
     */
    protected function _buildFieldsetBegin(&$html, $legend)
    {
        if ($this->_in_fieldset) {
            // already in a fieldset, don't start another one
            return;
        }
        
        $html[] = $this->_indent(1, "<fieldset>");
        $html[] = $this->_indent(2, "<legend>$legend</legend>");
        $this->_in_fieldset = true;
    }
    
    /**
     * 
     * Builds the end of a fieldset.
     * 
     * @param array &$html A reference to the array of HTML lines for output.
     * 
     * @return void
     * 
     */
    protected function _buildFieldsetEnd(&$html)
    {
        if (! $this->_in_fieldset) {
            // not in a fieldset, so can't end it
            return;
        }
        
        $this->_in_fieldset = false;
        $html[] = $this->_indent(1, "</fieldset>");
    }
}
