<?php
/**
 * Foresmo_Rss
 * Class for rss feeds
 * TODO: expand for other formats besides ATOM
 *
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class Foresmo_Rss extends Solar_Base {

    protected $_Foresmo_Rss = array(
        'type' => 'atom',
        'title' => null,
        'subtitle' => null,
        'link_self' => null,
        'link_alt' => null,
        'id' => null
    );

    protected $_xml;


    /**
     * getFeed
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
    public function getFeed()
    {
        $out = '<?xml version="1.0"?>';
        $out .= $this->_renderHeaderType();
        $out .= $this->_renderOptions();
        $out .= $this->_renderEntries();
        $out .= $this->_renderFoot();

        return $out;
    }

    /**
     * _renderHeaderType
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
    protected function _renderHeaderType()
    {
        $out = '<feed>';
        switch (strtolower($this->_config['type'])) {
            case 'atom':
            default:
                $out = '<feed xmlns="http://www.w3.org/2005/Atom">';
        }

        return $out;
    }

    /**
     * _renderOptions
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
    protected function _renderOptions()
    {
        $out = '<generator uri="http://www.foresmo.com/" version="0.1 alpha">Foresmo</generator>';
        $out .= '<id>tag:' . $_SERVER['HTTP_HOST'] . ','.date('Y-m-d').':atom/'.$this->_config['id'].'</id>';
        $out .= '<title>' . $this->_config['title'] . '</title>';
        $out .= '<subtitle>' . $this->_config['subtitle'] . '</subtitle>';
        $out .= '<updated></updated>';
        $out .= '<link rel="alternate" href="' . $this->_config['link_alt'] . '"/>';
        $out .= '<link rel="self" href="' . $this->_config['link_self'] . '"/>';

        return $out;
    }

    /**
     * _renderEntries
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
    protected function _renderEntries()
    {
        if ($this->_xml) {
            $out = '';
            foreach ($this->_xml->entry as $entry) {
               $out .= $entry->asXML();
            }
            return $out;
        }

        return '';
    }

    /**
     * _renderFoot
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
    protected function _renderFoot()
    {
        return '</feed>';
    }

    /**
     * addEntry
     * Insert description here
     *
     * @param $arr
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function addEntry($arr = array())
    {
        if (!$this->_xml) {
            $this->_xml= new SimpleXMLElement( $this->_renderHeaderType() . $this->_renderOptions() . $this->_renderFoot() );
        }

        if (!empty($arr)) {
            $entry = $this->_xml->addChild('entry');
            foreach ($arr as $key => $val) {
                if ($key == 'link') {
                    $link = $entry->addChild('link');
                    $link->addAttribute('rel', $val['rel']);
                    $link->addAttribute('href', $val['href']);
                } elseif ($key == 'author') {
                    $author = $entry->addChild('author');
                    $author->addChild('name', $val['name']);
                    $author->addChild('uri', $val['uri']);
                } elseif ($key == 'category') {
                    foreach ($val as $cat) {
                        $cat = $entry->addChild('category');
                        $cat->addAttribute('term', $cat);
                    }
                } elseif ($key == 'content') {
                    $content= $entry->addChild('content', htmlspecialchars($val['content']));
                    $content->addAttribute('type', $val['type']);

                } else {
                    $entry->addChild($key, htmlspecialchars($val));
                }
            }
        }
    }
}
