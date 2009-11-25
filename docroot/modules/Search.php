<?php
/**
 * Foresmo_Modules_Search
 *
 *
 */
class Foresmo_Modules_Search extends Solar_Base {

    protected $_Foresmo_Modules_Search = array('model' => null);
    protected $_model;
    public $name = 'Search';
    public $description = 'Search module for your blog. Different search types if available.';
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
        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $web_root = $_SERVER['DOCUMENT_ROOT'];
        } else {
            $web_root = Solar::$system . '/docroot/';
        }
        $this->_view_path = $web_root . 'modules/' . $this->name . '/View';
        $this->_view_file = 'index.php';
        $this->_view = Solar::factory('Solar_View', array('template_path' => $this->_view_path));
        $this->_module_info = $this->_model->modules->fetchModuleInfoByName($this->name);
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

    public function install()
    {
        $id = (int) $this->_module_info['id'];
        $data = array(
            'name'  => 'search_adapter',
            'type'  => 0,
            'value' => 'default',
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
