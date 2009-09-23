<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_Tags extends Solar_Sql_Model {

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
        $this->_hasMany('posts_tags', array(
            'foreign_class' => 'Foresmo_Model_PostsTags',
            'foreign_key' => 'tag_id',
        ));
        $this->_hasMany('posts', array(
             'foreign_class' => 'Foresmo_Model_Posts',
             'through'       => 'posts_tags',
             'through_key'   => 'post_id',
        ));
    }

    /**
     * getTags
     *
     * Get all available tags with pertinant info
     *
     * @return array
     */
    public function getTags()
    {
        $results = $this->fetchAllAsArray(
            array(
                'order'  => array (
                    'tag ASC'
                ),
                'eager'  => array(
                    'posts'
                ),
            )
        );
        return $results;
    }
}
