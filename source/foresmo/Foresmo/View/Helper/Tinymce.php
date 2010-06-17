<?php
/**
 * Foresmo_View_Helper_Tinymce
 * Insert description here
 *
 * @category
 * @package
 * @author
 * @copyright
 * @license
 * @version
 * @link
 * @see
 * @since
 */
class Foresmo_View_Helper_Tinymce extends Solar_View_Helper {


    /**
     * tinymce
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function tinymce()
    {
        return $this;
    }

    /**
     * init
     * Insert description here
     *
     * @param $elem_opt
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function init($elem_opt)
    {
        echo '<script type="text/javascript">';
        if (!empty($elem_opt)) {
            $width = null;
            $height = null;
            foreach ($elem_opt as $elem => $options) {
                echo 'tinyMCE.init({';
                    foreach ($options as $key => $value) {
                        switch ($key) {
                            case 'width':
                                $width = $value;
                            break;
                            case 'height':
                                $height = $value;
                            break;
                        }
                    }
                    $this->_displayOptions($elem, $width, $height);
                echo '});';
            }
        }
        echo '</script>';
        return $this;
    }

    /**
     * _displayOptions
     * Insert description here
     *
     * @param $elem
     * @param $width
     * @param $height
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _displayOptions($elem, $width = null, $height = null)
    {
        if ($width == null) {
            $width = 700;
        }
        if ($height == null) {
            $height = 400;
        }
        echo 'mode : "exact",
            elements : "'.$elem.'",
            width : "'.$width.'",
            height: "'.$height.'",
            verify_html : false,
            apply_source_formatting : false,
            fix_nesting : false,
            fix_list_elements : false,
            fix_content_duplication : false,
            cleanup : false,
            cleanup_on_startup: false,
            trim_span_elements : false,
            skin: \'thebigreason\',
            extended_valid_elements : "?php",
            doctype : \'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\',
            theme : \'advanced\',
                plugins : \'safari,spellchecker,layer,save,advimage,advlink,inlinepopups,contextmenu,paste,noneditable,visualchars,nonbreaking,pagebreak\',
                theme_advanced_buttons1: \'bold,italic,underline,strikethrough,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,|,link,unlink|,spellchecker,formatselect,|,image,charmap,|,outdent,indent,|,undo,redo,|,code\',
                theme_advanced_buttons2: \'\',
                theme_advanced_buttons3: \'\',
                theme_advanced_toolbar_location : \'top\',
                theme_advanced_toolbar_align : \'left\',
                theme_advanced_statusbar_location : \'bottom\',
                theme_advanced_resize_horizontal : true,
                theme_advanced_resizing : true,
                apply_source_formatting : true,
                theme_advanced_source_editor_width : \'700\',
                spellchecker_languages : \'+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv\'
            ';
    }
}