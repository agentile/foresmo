<?php
/**
 * Foresmo_Modules_Links
 *
 *
 */
class Foresmo_Modules_Links extends Foresmo_Modules_Base {

    protected $_Foresmo_Modules_Links = array();

    public $info = array(
        'name' => 'Links',
        'description' => 'Add/Show specific links.'
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
        $links = $this->_model->links->fetchLinks();
        $this->_view->assign('links', $links);

        $this->output = $this->_view->fetch($this->_view_file);
    }

    /**
     * admin
     * module admin view
     *
     * @param array $data
     * @return void
     */
    public function admin($data)
    {
        if (isset($data['PARAMS'][2]) && $data['PARAMS'][2] == 'edit'
            && isset($data['PARAMS'][3]) && is_numeric($data['PARAMS'][3])) {
            $this->_setViewFile('edit.php');
            $link = $this->_model->links->fetchOneAsArray(array('where' => array('id = ?' => $data['PARAMS'][3])));
            $this->_view->assign('link', $link);
            $this->_view->assign('url', implode('/', $data['PARAMS']));
        } elseif (isset($data['PARAMS'][2]) && $data['PARAMS'][2] == 'delete'
            && isset($data['PARAMS'][3]) && is_numeric($data['PARAMS'][3])) {
            $this->_setViewFile('delete.php');
            $link = $this->_model->links->fetchOneAsArray(array('where' => array('id = ?' => $data['PARAMS'][3])));
            $this->_view->assign('link', $link);
            $this->_view->assign('url', implode('/', $data['PARAMS']));
        } else {
            $this->_setViewFile('admin.php');
            $links = $this->_model->links->fetchLinks();
            $this->_view->assign('links', $links);
        }
        $this->output = $this->_view->fetch($this->_view_file);
    }
    
    /**
     * adminRequest
     * module admin request
     *
     * @param array $data
     * @return void
     */
    public function adminRequest($data)
    {
        $post_data = $data['POST'];
        if (isset($data['PARAMS'][2]) && $data['PARAMS'][2] == 'edit'
            && isset($data['PARAMS'][3]) && is_numeric($data['PARAMS'][3])) {
            if (isset($data['POST']['title']) && trim($data['POST']['title']) != '') {
                $updated_data = array(
                    'name'  => $post_data['title'],
                    'url'  => $post_data['url'],
                    'target'  => $post_data['target'],
                    'status' => $post_data['status'],
                );
                $where = array('id = ?' => array((int) $data['PARAMS'][3]));
                $this->_model->links->update($updated_data, $where);
            }
        } elseif (isset($data['PARAMS'][2]) && $data['PARAMS'][2] == 'delete'
            && isset($data['PARAMS'][3]) && is_numeric($data['PARAMS'][3])) {
            if (isset($data['POST']['yes'])) {
                $where = array('id = ?' => array((int) $data['PARAMS'][3]));
                $this->_model->links->delete($where);
                // TODO provide for proper redirect with $this->_redirect() in Modules_Base
                header('Location: /admin/modules/edit/Links');
                die();
            }
        } else {
            if (isset($post_data['title']) && trim($post_data['title']) != '') {
                $data = array(
                    'name'  => $post_data['title'],
                    'url'  => $post_data['url'],
                    'target'  => $post_data['target'],
                    'status' => $post_data['status'],
                );
                $this->_model->links->insert($data);
            }
        }
    }
}
