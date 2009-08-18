<?php
/**
 * Foresmo_Model_Posts
 * Posts model
 *
 * content type
 * 1 = blog post
 * 2 = page
 *
 * status codes
 * 0 = hidden
 * 1 = published
 * 2 = draft
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

        $adapter = Solar_Config::get('Solar_Sql', 'adapter');
        $this->_table_name = Solar_Config::get($adapter, 'prefix') . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');

        $this->_hasMany('postinfo', array(
            'foreign_class' => 'Foresmo_Model_PostInfo',
            'foreign_key' => 'post_id',
        ));

        $this->_hasMany('comments', array(
            'foreign_class' => 'Foresmo_Model_Comments',
            'foreign_key' => 'post_id',
            'where' => array('status = ?' => 1)
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
     * getPageBySlug
     * get a specific page by slug and status of 1 (published),
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param $slug_name
     * @return array
     */
    public function getPageBySlug($slug_name)
    {
        $results = $this->fetchArray(
            array(
                'where'  => array(
                    'status = ? AND slug = ? AND content_type = ?' => array(1, $slug_name, 2),
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
        Foresmo::dateFilter($results);
        Foresmo::sanitize($results);
        return $results;
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
                    'status = ? AND slug = ? AND content_type = ?' => array(1, $slug_name, 1),
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
        Foresmo::dateFilter($results);
        Foresmo::sanitize($results);
        return $results;
    }

    /**
     * getPublishedPosts
     * get all posts with status of 1 (published), with all it's
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function getPublishedPosts()
    {
        $results = $this->fetchArray(
            array(
                'where'  => array(
                    'status = ? AND content_type = ?' => array(1, 1)
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
        Foresmo::dateFilter($results);
        Foresmo::sanitize($results);
        return $results;
    }

    /**
     * getPublishedPages
     * get all pages with status of 1 (published), with all it's
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function getPublishedPages()
    {
        $results = $this->fetchArray(
            array(
                'where'  => array(
                    'status = ? AND content_type = ?' => array(1, 2)
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
        Foresmo::dateFilter($results);
        Foresmo::sanitize($results);
        return $results;
    }

    /**
     * getPublishedPostsByDate
     * get all posts with status of 1 (published) and corresponding year, month, date
     * with all pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function getPublishedPostsByDate($year = null, $month = null, $day = null)
    {
        if (is_null($year) && is_null($month) && is_null($day)) {
            // If all is null then get posts made today.
            $date = getdate();
            $start = gmmktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
            $end = gmmktime(23, 59, 59, $date['mon'], $date['mday'], $date['year']);
        } elseif (is_null($year) && is_null($month)) {
            // If only day if given than grab all posts
            // for that day from current month of current year
            $date = getdate();
            $start = gmmktime(0, 0, 0, $date['mon'], (int) $day, $date['year']);
            $end = gmmktime(23, 59, 59, $date['mon'], (int) $day, $date['year']);
        } elseif (is_null($year) && is_null($day)) {
            // Only month is given, get all posts for current year.
            $date = getdate();
            $start = gmmktime(0, 0, 0, (int) $month, 1, (int) $date['year']);
            if ($month == 12) {
                $next_month = 1;
                $next_year = $date['year'] + 1;
            } else {
                $next_month = $month + 1;
                $next_year = $date['year'];
            }
            // Get the numeric last day of month.
            $last_of_month = gmstrftime("%d", gmmktime(0, 0, 0, $next_month, 0, $next_year));
            $end = gmmktime(23, 59, 59, (int) $month, (int) $last_of_month, (int) $date['year']);
        } elseif (is_null($month) && is_null($day)) {
            // only year is given, get all posts for year
            $start = gmmktime(0, 0, 0, 1, 1, (int) $year);
            $last_of_month = gmstrftime("%d", gmmktime(0, 0, 0, 1, 0, ((int) $year) + 1));
            $end = gmmktime(23, 59, 59, 12, (int) $last_of_month, (int) $year);
        } elseif (is_null($day)) {
            // Year and month are given, get all posts for month
            $start = gmmktime(0, 0, 0, (int) $month, 1, (int) $year);
            if ($month == 12) {
                $next_month = 1;
                $next_year = $year + 1;
            } else {
                $next_month = $month + 1;
                $next_year = $year;
            }
            // Get the numeric last day of month.
            $last_of_month = gmstrftime("%d", gmmktime(0, 0, 0, $next_month, 0, $next_year));
            $end = gmmktime(23, 59, 59, (int) $month, (int) $last_of_month, (int) $year);

        } elseif (is_null($month)) {
            // Year and day are given, set month to current
            $date = getdate();
            $start = gmmktime(0, 0, 0, $date['mon'], (int) $day, (int) $year);
            $end = gmmktime(23, 59, 59, $date['mon'], (int) $day, (int) $year);
        } elseif (is_null($year)) {
            // Month and day are given, set year to current
            $date = getdate();
            $start = gmmktime(0, 0, 0, (int) $month, (int) $day, $date['year']);
            $end = gmmktime(23, 59, 59, (int) $month, (int) $day, $date['year']);
        } else {
            // Nothing is null, be specific to parameters.
            $start = gmmktime(0, 0, 0, (int) $month, (int) $day, (int) $year);
            $end = gmmktime(23, 59, 59, (int) $month, (int) $day, (int) $year);

        }
        $results = $this->fetchArray(
            array(
                'where'  => array(
                    'status = ? AND content_type = ? AND pubdate BETWEEN \''.$start.'\' AND \''.$end.'\'' => array(1, 1)
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
        Foresmo::dateFilter($results);
        Foresmo::sanitize($results);
        return $results;
    }

    /**
     * getPublishedPostsByPage
     * get all posts with status of 1 (published) and page, with all
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function getPublishedPostsByPage($page_num)
    {
        $page_num = (int) $page_num;
        $results = $this->fetchArray(
            array(
                'where'  => array(
                    'status = ? AND content_type = ?' => array(1, 1)
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
        Foresmo::dateFilter($results);
        Foresmo::sanitize($results);
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
                    'status = ? AND content_type = ?' => array(1, 1)
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
        Foresmo::dateFilter($results);
        Foresmo::sanitize($results);
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
                    'status = ? AND content_type = ?' => array(1, 1)
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
     * @return mixed last insert id
     */
    public function newPost($post_data)
    {
        $post_status = (int) $post_data['post_status'];
        if ($post_data['post_status'] <= 0 || $post_data['post_status'] > 2) {
            // if unexpected int, default to 1 (published)
            $post_data['post_status'] = 1;
        }
        $type = (int) $post_data['post_type'];

        $cur_time = time();
        $data = array(
            'slug' => $post_data['post_slug'],
            'content_type' => $type,
            'title' => $post_data['post_title'],
            'content' => $post_data['post_content'],
            'user_id' => $_SESSION['Foresmo_App']['Foresmo_user_id'],
            'status' => $post_status,
            'pubdate' => $cur_time,
            'modified' => $cur_time,
        );

        return $this->insert($data);
    }

    /**
     * getTotalCount
     * Get count of certain type and status
     *
     * $param int $type
     * $param int $status
     * @return int count
     */
    public function getTotalCount($type, $status)
    {
        $result = $this->fetchArray(
            array(
                'cols' => array(
                    'COUNT(*) as count'
                ),
                'where' => array(
                    'content_type = ?' => $type,
                    'status = ?' => $status,
                ),
            )
        );
        return (int) $result[0]['count'];
    }
}
