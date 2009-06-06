<?php
/**
 * Foresmo_Modules_Search
 *
 *
 *
 */
class Foresmo_Modules_Search extends Solar_Base {

    protected $_model;

    public $output = '';

    /**
     * __construct
     *
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * start
     * Begin Module Work
     *
     * @return void
     */
    public function start()
    {
        $this->output = 'blah';
    }

}
