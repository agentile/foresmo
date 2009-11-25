<?php
/**
 * Foresmo_Modules_Search_Mysql
 *
 * MySQL Search adapter, uses MySQL Fulltext.
 *
 */
class Foresmo_Modules_Search_Mysql {

    protected $_model;

    public function __construct()
    {
        $this->_model = Solar_Registry::get('model_catalog');
    }

    public function performSearch($search_string, $params = array())
    {

    }
}