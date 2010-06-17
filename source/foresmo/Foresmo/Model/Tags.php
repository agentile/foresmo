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
            'through_native_col' => 'tag_id',
            'through_foreign_col' => 'post_id',
            'conditions' => array('status = ?' => array(1)),
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
        //SELECT t.id as id, t.tag as tag, t.tag_slug as tag_slug, count(*) as count 
        //FROM `foresmo_tags` as t 
        //INNER JOIN `foresmo_posts_tags` as pt ON pt.tag_id = t.id 
        //INNER JOIN `foresmo_posts` as p ON pt.post_id = p.id 
        //WHERE p.status = 1 group by t.tag_slug");

        $results = $this->fetchAllAsArray(
            array(
                'cols' => array('id', 'tag', 'tag_slug', "COUNT(*) AS count"),
                'order'  => array (
                    'tag ASC'
                ),
                'group' => array('tag_slug'),
                'eager'  => array(
                    'posts' => array(
                        'join_type' => 'inner',
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
