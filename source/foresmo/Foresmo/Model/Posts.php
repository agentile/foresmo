<?php
/**
 * Posts Model Class
 *
 */
class Foresmo_Model_Posts extends Solar_Sql_Model {

    public $posts_per_page = 10;
    public $page_count;
    public $published_posts_count = 1;
    public $date_format;
    public $timezone;

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

        $time_info = Foresmo::getTimeInfo();
        $this->date_format = $time_info['blog_date_format'];
        $this->timezone = $time_info['blog_timezone'];
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
        $results = $this->fetchArray(
            array(
                'where'  => array(
                    'status = ? AND slug = ?' => array(1, $slug_name),
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
        $this->dateFilter($results);
        return $results;
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
        $results = $this->fetchArray(
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
        $this->dateFilter($results);
        return $results;
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
        $results = $this->fetchArray(
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
        $this->dateFilter($results);
        return $results;
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
            $where_stmt .= ' OR tags.tag_slug = ?';
        }

        $where = array(
            $where_stmt => $tags
        );

        $results = $this->fetchArray(
            array(
                'where'  => array(
                    'status = ?' => 1
                ),
                'order'  => array ('id DESC'),
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
        $this->dateFilter($results);
        return $results;
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

    /**
     * newPost
     * Insert a new post from post data
     *
     * @param $post_data
     * @return void
     */
    public function newPost($post_data)
    {
        $post_status = (int) $post_data['post_status'];
        if ($post_data['post_status'] <= 0 || $post_data['post_status'] > 2) {
            // if unexpected int, default to 1 (published)
            $post_data['post_status'] = 1;
        }
        $cur_time = time();
        $data = array(
            'id' => '',
            'slug' => $post_data['post_slug'],
            'content_type' => 1,
            'title' => $post_data['post_title'],
            'content' => $post_data['post_content'],
            'user_id' => $_SESSION['Foresmo_App']['Foresmo_user_id'],
            'status' => $post_status,
            'pubdate' => $cur_time,
            'modified' => $cur_time,
        );
        $result = $this->insert($data);
        return $result['id'];
    }

    /**
     * makeSlug
     * Change string to url friendly slug
     *
     * @param $str
     * @param $delim  default '-'
     *
     * @return string
     */
    public function makeSlug($str, $delim = '-')
    {
        $str = preg_replace('/[^a-z0-9-]/', $delim, strtolower(trim($str)));
        $str = preg_replace("/{$delim}+/", $delim, trim($str, $delim));
        return $str;
    }

    /**
     * dateFilter
     * Modify datetime fields for timezone and date format settings
     *
     * @param $posts
     * @param $key
     *
     * @return array
     */
     public function dateFilter(&$posts)
     {
         foreach ($posts as $k => $v) {
             if (is_array($v)) {
                 $this->dateFilter($posts[$k]);
             }
             if ($k ==='date' || $k === 'pubdate' || $k === 'modified') {
                 $fetched_time = (int) $v;
                 $timezone = explode(':', $this->timezone);
                 if ($timezone[0][0] == '-') {
                     $first = substr($timezone[0], 1);
                     $change = $first * 60 * 60;
                     if ($timezone[1] == '30') {
                         $change = $change + 1800;
                     }
                     $time = date($this->date_format, $fetched_time - $change);
                 } else {
                     $change = $timezone[0] * 60 * 60;
                     if ($timezone[1] == '30') {
                         $change = $change + 1800;
                     }
                     $time = date($this->date_format, $fetched_time + $change);
                 }
                 $posts[$k] = $time;
             }
         }
     }
}
