<?php
/**
 * Foresmo_Modules_Search
 *
 *
 */
class Foresmo_Modules_Search extends Solar_Base {

    protected $_Foresmo_Modules_Search = array('model' => null);
    protected $_model;
    protected $_name = 'Search';
    protected $_view;
    protected $_view_path;
    protected $_view_file;
    protected $_module_info = array();
    protected $_search;

    public $search_adapter = 'Default';
    public $search_adapter_settings = array();
    public $output = '';

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
        $this->_model = $this->_config['model'];
        $this->_view_path = Solar_Class::dir($this, 'View');
        $this->_view_file = 'index.php';
        $this->_view = Solar::factory('Solar_View', array('template_path' => $this->_view_path));
        $this->_module_info = $this->_model->modules->getModuleInfoByName($this->_name);
    }

    /**
     * start
     * Begin Module Work
     *
     * @return void
     */
    public function start()
    {
        if (isset($this->_module_info[0]['moduleinfo'])) {
            foreach ($this->_module_info[0]['moduleinfo'] as $row) {
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
        $this->_view->assign('search_adapter', $this->search_adapter);
        $this->_view->assign('search_adapter_settings', $this->search_adapter_settings);
        $this->output = $this->_view->fetch($this->_view_file);
    }

    /**
     * request
     * module request
     *
     * @param array $data
     * @return void
     */
    public function request($data)
    {
        $post_data = (isset($data['POST'])) ? $data['POST'] : array();

        if (isset($this->_module_info[0]['moduleinfo'])) {
            foreach ($this->_module_info[0]['moduleinfo'] as $row) {
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

        $this->_search = Solar::factory('Foresmo_Modules_Search_' . $this->search_adapter, $this->search_adapter_settings);

        $results = $this->_search->performSearch($post_data['search-input']);
    }
}
