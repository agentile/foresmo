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
     * fetchTagsForPublishedPosts
     *
     * Fetch all available tags with pertinant info
     *
     * @return array
     */
    public function fetchTagsForPublishedPosts()
    {
        $results = $this->fetchAllAsArray(
            array(
                'cols' => array('id', 'tag', 'tag_slug', "COUNT({$this->_config['prefix']}tags.id) AS count"),
                'order'  => array (
                    'tag ASC'
                ),
                'group' => array($this->_config['prefix'] . 'tags.id'),
                'eager'  => array(
                    'posts' => array(
                        'join_only' => true,
                        'join_cond' => array(
                            'posts.status = ?' => 1,
                        ),
                    ),
                ),
            )
        );
        return $results;
    }

    /**
     * fetchTags
     *
     * Get all available tags with pertinant info
     *
     * @return array
     */
    public function fetchTags()
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

    /**
     * fetchTagsByID
     * Fetch tags by content id
     *
     * @param int $id
     * @return array
     */
    public function fetchTagsByID($id)
    {
        if ((int) $id != $id) {
            return array();
        }

        $id = (int) $id;

        return $this->fetchAllAsArray(array(
                'cache' => false,
                'eager' => array(
                    'posts' => array(
                        'join_only' => true,
                        'join_cond' => array(
                            'posts.id = ?' => $id,
                        )
                    )
                )
            )
        );
    }

    /**
     * fetchTagIdBySlug
     * Fetch tag id by tag slug
     *
     * @param string $tag_slug
     */
    public function fetchTagIdBySlug($tag_slug)
    {
        return $this->fetchValue(
            array(
                'cols' => array('id'),
                'where' => array('tag_slug = ?' => $tag_slug),
            )
        );
    }
}
