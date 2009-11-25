<?php
/**
 * Foresmo_Modules_Search_Default
 *
 * Very basic search adapter.
 *
 */
class Foresmo_Modules_Search_Default {

    protected $_model;

    public function __construct()
    {
        $this->_model = Solar_Registry::get('model_catalog');
    }

    public function performSearch($search_string, $params = array())
    {

    }
}