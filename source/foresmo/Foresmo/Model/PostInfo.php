<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_PostInfo extends Solar_Sql_Model {

    /**
     *
     * Model-specific setup.
     *
     * @return void
     *
     */
    protected function _setup()
    {
        $dir = str_replace('_', DIRECTORY_SEPARATOR, __CLASS__)
             . DIRECTORY_SEPARATOR
             . 'Setup'
             . DIRECTORY_SEPARATOR;

        $this->_table_name = $this->_config['prefix'] . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
    }

    /**
     * isCommentsDisabled
     * Check to see if comments are disabled for a post/page.
     *
     * @param $id
     * @return bool
     */
    public function isCommentsDisabled($id)
    {
        $where = array(
            'post_id = ?' => (int) $id,
            'name = ?' => 'comments_disabled'
        );
        $result = $this->fetchOneAsArray(array('where' => $where));

        if (empty($result)) {
            return false;
        }

        if (isset($result['value']) && $result['value'] == '1') {
            return true;
        }
        return false;
    }

    /**
     * insertCommentsDisabled
     * insert a post/page with comments disabled or not
     *
     * @param $id
     * @param $bool default false
     * @return array
     */
    public function insertCommentsDisabled($id, $bool = false)
    {
        $data = array(
            'post_id' => $id,
            'name' => 'comments_disabled',
            'type' => 0,
            'value' => $bool,
        );
        $this->insert($data);
    }

    /**
     * updateCommentsDisabled
     * update post/page with comments disabled or not
     *
     * @param $id
     * @param $bool default false
     * @return array
     */
    public function updateCommentsDisabled($id, $bool = false)
    {
        $where = array(
            'post_id = ?' => $id,
            'name = ?' => 'comments_disabled',
        );
        $data = array(
            'value' => $bool,
        );
        $this->update($data, $where);
    }
}
