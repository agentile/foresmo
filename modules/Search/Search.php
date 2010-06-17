<?php
/**
 * Foresmo_Modules_Search
 *
 *
 */
class Foresmo_Modules_Search extends Foresmo_Modules_Base {

    protected $_Foresmo_Modules_Search = array();

    public $info = array(
        'name' => 'Search',
        'description' => 'Search module for your blog. Different search types if available.'
    );

    protected $_search;

    public $search_adapter = 'Default';
    public $search_adapter_settings = array();
    public $output = '';

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
        $get_data = (isset($data['GET'])) ? $data['GET'] : array();

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

    public function install()
    {
        $id = (int) $this->_module_info['id'];
        $data = array(
            'name'  => 'search_adapter',
            'type'  => 0,
            'value' => 'Default',
        );
        $this->_model->module_info->insertModuleEntry($id, $data);

        $data = array(
            'name'  => 'search_adapter_settings',
            'type'  => 0,
            'value' => 'a:5:{s:7:"Default";a:0:{}s:6:"Google";a:0:{}s:5:"Mysql";a:0:{}s:6:"Lucene";a:0:{}s:5:"Sphinx";a:0:{}}',
        );
        $this->_model->module_info->insertModuleEntry($id, $data);
    }

    public function uninstall()
    {

    }
}
