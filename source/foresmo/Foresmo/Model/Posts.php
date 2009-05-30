<?php
/**
 * Posts Model Class
 *
 */
class Foresmo_Model_Posts extends Solar_Sql_Model {

    public $posts_per_page = 10;
    public $page_count;
    public $published_posts_count = 1;

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

        $this->_table_name = Solar_Config::get('Solar_Sql_Adapter_Mysql', 'prefix') . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');

        $this->_hasMany('postinfo', array(
            'foreign_class' => 'Foresmo_Model_PostInfo',
            'foreign_key' => 'post_id',
        ));

        $this->_hasMany('comments', array(
            'foreign_class' => 'Foresmo_Model_Comments',
            'foreign_key' => 'post_id',
        ));

        $this->_hasMany('posts_tags', array(
            'foreign_class' => 'Foresmo_Model_PostsTags',
            'foreign_key' => 'post_id',
        ));

        $this->_hasMany('tags', array(
             'foreign_class' => 'Foresmo_Model_Tags',
             'through'       => 'posts_tags',
             'through_key'   => 'tag_id',
        ));
    }

    /**
     * getPostBySlug
     * get a specific blog post by slug and status of 1 (published),
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param $slug_name
     * @return array
     */
    public function getPostBySlug($slug_name)
    {
        return $this->fetchArray(
            array(
                'where'  => array(
                    'status = ?' => 1,
                    'slug = ?' => $slug_name
                ),
                'order'  => array (
                    'id DESC'
                ),
                'paging' => $this->posts_per_page,
                'page'   => 1,
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo'
                ),
            )
        );
    }

    /**
     * getAllPublishedPosts
     * get all posts with status of 1 (published), with all it's
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function getAllPublishedPosts()
    {
        return $this->fetchArray(
            array(
                'where'  => array(
                    'status = ?' => 1
                ),
                'order'  => array (
                    'id DESC'
                ),
                'paging' => $this->posts_per_page,
                'page'   => 1,
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo'
                ),
            )
        );
    }

    /**
     * getAllPublishedPostsByPage
     * get all posts with status of 1 (published) and page, with all
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function getAllPublishedPostsByPage($page_num)
    {
        $page_num = (int) $page_num;
        return $this->fetchArray(
            array(
                'where'  => array(
                    'status = ?' => 1
                ),
                'order'  => array (
                    'id DESC'
                ),
                'paging' => $this->posts_per_page,
                'page'   => $page_num,
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo'
                ),
            )
        );
    }

    /**
     * getPostsByTag
     * get all posts with status of 1 (published) and a specific tag
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @return array
     */
    public function getPostsByTag($tags)
    {
        if (!$tags || empty($tags)) {
            return array();
        }

        $where_stmt = 'tags.tag_slug = ?';
        for ($i = 1; $i < count($tags); $i++) {
            $where_stmt .= ' OR ?';
        }

        $where = array(
            $where_stmt => $tags
        );

        return $this->fetchArray(
            array(
                'where'  => array(
                    'status = ?' => 1
                ),
                'order'  => array ('id DESC'),
                'paging' => $this->posts_per_page,
                'page'   => 1,
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags' => array(
                        'where' => $where
                    ),
                    'postinfo'
                ),
            )
        );
    }

    /**
     * getPublishedPostsCount
     * Get the number of published posts
     *
     * @return int
     */
    public function getPublishedPostsCount()
    {
        $result = $this->fetchOne(
            array(
                'cols' => array(
                    'COUNT(*) AS count',
                ),
                'where'  => array(
                    'status = ?' => 1
                ),
            )
        );

        $this->published_posts_count = (int) $result->count;
    }
}
