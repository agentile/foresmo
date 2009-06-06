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

        $adapter = Solar_Config::get('Solar_Sql', 'adapter');
        $this->_table_name = Solar_Config::get($adapter, 'prefix') . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
    }

    /**
     * commentsDisabled
     * Check to see if comments are disabled for a post.
     *
     * @param $post_id
     * @return bool
     */
    public function commentsDisabled($post_id)
    {
        $where = array(
            'post_id = ?' => (int) $post_id,
            'name = ?' => 'comments_disabled'
        );
        $result = $this->fetchArray(array('where' => $where));
        if (empty($result)) {
            return false;
        }
        if (isset($result[0]['value'])) {
            if ($result[0]['value'] == '1') {
                return true;
            }
        }
        return false;
    }

    /**
     * setCommentsDisabled
     * Set a post with comments disabled or not
     *
     * @param $post_id
     * @param $value
     * @return array
     */
    public function setCommentsDisabled($post_id, $value)
    {
        $data = array(
            'id' => '',
            'post_id' => $post_id,
            'name' => 'comments_disabled',
            'type' => 0,
            'value' => $value
        );
        $result = $this->insert($data);
        return $result;
    }
}
