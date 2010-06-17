<?php
/**
 * Foresmo_App_Search
 * Search Controller
 *
 * @category  App
 * @package   Foresmo
 * @author    Anthony Gentile
 * @version   0.15
 * @since     0.15
 */
class Foresmo_App_Search extends Foresmo_App_Base {

    /**
     *
     * The default action when no action is specified.
     *
     * @var string
     *
     */
    protected $_action_default = 'index';

    public $search_adapter = 'Default';
    public $search_adapter_settings = array();

    /**
     * _preRun
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
    protected function _preRun()
    {
        parent::_preRun();
        $module_info = $this->_model->modules->fetchModuleInfoByName('Search');
        if (isset($module_info['moduleinfo'])) {
            foreach ($module_info['moduleinfo'] as $row) {
                if ($row['name'] == 'search_adapter') {
                    $this->search_adapter = ucfirst(strtolower($row['value']));
                }
                if ($row['name'] == 'search_adapter_settings') {
                    $this->search_adapter_settings = unserialize($row['value']);
                }
            }
        }
        if (isset($this->search_adapter_settings[$this->search_adapter])) {
            $this->search_adapter_settings = $this->search_adapter_settings[$this->search_adapter];
        }
    }

    /**
     *
     * Generic index action.
     *
     * @return void
     *
     */
    public function actionIndex()
    {

    }

    /**
     * Search Results page
     *
     * @return void
     *
     */
    public function actionResults()
    {

    }
}
