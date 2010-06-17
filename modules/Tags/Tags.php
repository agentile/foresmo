<?php
/**
 * Foresmo_Modules_Tags
 *
 * Tags Module: Show available tags and count
 *
 */
class Foresmo_Modules_Tags extends Foresmo_Modules_Base {

    protected $_Foresmo_Modules_Tags = array();

    public $info = array(
        'name' => 'Tags',
        'description' => 'Listing of available tags and their post count.'
    );

    public $output = '';

    /**
     * start
     * Begin Module Work
     *
     * @return void
     */
    public function start()
    {
        $tags = $this->_model->tags->fetchTagsForPublishedPosts();
        $this->_view->assign('tags', $tags);

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
        $tags = implode('/', $data['POST']['tags']);
        $q = '/tag/' . $tags;
        if ($data['POST']['operator'] == 'OR') {
            $q .= '?op=OR';
        }
        $response = Solar::factory('Solar_Http_Response');
        $response->redirect($q);
        die();
    }

    public function install()
    {

    }

    public function uninstall()
    {

    }
}
