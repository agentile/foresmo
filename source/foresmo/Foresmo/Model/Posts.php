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

        $this->_table_name = $this->_config['prefix'] . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');

        $this->_hasMany('postinfo', array(
            'foreign_class' => 'Foresmo_Model_PostInfo',
            'foreign_key' => 'post_id',
        ));

        $this->_hasMany('comments_all', array(
            'foreign_class' => 'Foresmo_Model_Comments',
            'foreign_key' => 'post_id',
        ));
        
        $this->_hasMany('comments', array(
            'foreign_class' => 'Foresmo_Model_Comments',
            'foreign_key' => 'post_id',
            'conditions' => array('status = ?' => array(1)),
        ));

        $this->_hasMany('posts_tags', array(
            'foreign_class' => 'Foresmo_Model_PostsTags',
            'foreign_key' => 'post_id',
        ));

        $this->_hasMany('tags', array(
             'foreign_class' => 'Foresmo_Model_Tags',
             'through'       => 'posts_tags',
             'through_key'   => 'tag_id',
             'through_native_col' => 'post_id',
             'through_foreign_col' => 'tag_id',
        ));

        $this->_hasOne('users', array(
            'foreign_class' => 'Foresmo_Model_Users',
            'cols' => array('id', 'username', 'email'),
            'native_col' => 'user_id',
            'foreign_col' => 'id',
        ));
    }

    /**
     * fetchPageBySlug
     * Fetch a specific page by its slug,
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param $slug_name
     * @return array
     */
    public function fetchPageBySlug($slug_name)
    {
        $results = $this->fetchOneAsArray(
            array(
                'where'  => array(
                    'slug = ? AND content_type = ?' => array($slug_name, 2),
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
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPostBySlug
     * Fetch a specific blog post by slug,
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param $slug_name
     * @return array
     */
    public function fetchPostBySlug($slug_name)
    {
        $results = $this->fetchOneAsArray(
            array(
                'where'  => array(
                    'slug = ? AND content_type = ?' => array($slug_name, 1),
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
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchViewablePageBySlug
     * Fetch a specific page by slug and status of 0 or 1 (hidden, published),
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param $slug_name
     * @return array
     */
    public function fetchViewablePageBySlug($slug_name)
    {
        if (is_array($slug_name)) {
            $parents = $slug_name;
            $slug_name = end($parents);
        }
        $results = $this->fetchOneAsArray(
            array(
                'where'  => array(
                    'status IN (?,?) AND slug = ? AND content_type = ?' => array(0, 1, $slug_name, 2),
                ),
                'order'  => array (
                    'id DESC'
                ),
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );
        
        if (empty($results)) {
            return $results;
        }
        // page exists, if this is a nested page, check the validity of parents
        if (isset($parents) && !empty($parents) && count($parents) > 1) {
            $parents = array_reverse($parents);
            foreach ($parents as $k => $child) {
                if (isset($parents[$k+1])) {
                    // not valid parent, return empty array
                    if (!$this->isChild($child, $parents[$k+1])) {
                        return array();
                    }
                }
            }
        }
        
        if (!isset($parents) || (!empty($parents) && count($parents) == 1)) {
            if ($results['parent_id'] != null) {
                // this page was fetched by its slug alone and not its proper nest path
                return array();
            }
        }
        
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }
    
    /**
     * check to see if parent-child relationship is valid
     * TODO: make this more efficient
     * @var $child child slug 
     * @var $parent parent slug
     */
    public function isChild($child, $parent)
    {
        $child = $this->fetchPageBySlug($child);
        $parent = $this->fetchPageBySlug($parent);
        if (isset($child['parent_id']) && isset($parent['id']) && $child['parent_id'] === $parent['id']) {
            return true;
        }
        return false;
    }

    /**
     * fetchPublishedPageBySlug
     * Fetch a specific page by slug and status of 1 (published),
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param $slug_name
     * @return array
     */
    public function fetchPublishedPageBySlug($slug_name)
    {
        $results = $this->fetchOneAsArray(
            array(
                'where'  => array(
                    'status = ? AND slug = ? AND content_type = ?' => array(1, $slug_name, 2),
                ),
                'order'  => array (
                    'id DESC'
                ),
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPublishedPostBySlug
     * Fetch a specific blog post by slug and status of 1 (published),
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param $slug_name
     * @return array
     */
    public function fetchPublishedPostBySlug($slug_name)
    {
        $results = $this->fetchOneAsArray(
            array(
                'where'  => array(
                    'status = ? AND slug = ? AND content_type = ?' => array(1, $slug_name, 1),
                ),
                'order'  => array (
                    'id DESC'
                ),
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchViewablePostBySlug
     * Fetch a specific blog post by slug and status of 0 or 1 (hidden, published),
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param $slug_name
     * @return array
     */
    public function fetchViewablePostBySlug($slug_name)
    {
        $results = $this->fetchOneAsArray(
            array(
                'where'  => array(
                    'status IN (?,?) AND slug = ? AND content_type = ?' => array(0, 1, $slug_name, 1),
                ),
                'order'  => array (
                    'id DESC'
                ),
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPublishedPosts
     * Fetch all posts with status of 1 (published), with all it's
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function fetchPublishedPosts()
    {
        $results = $this->fetchAllAsArray(
            array(
                'where'  => array(
                    'status = ? AND content_type = ?' => array(1, 1),
                ),
                'order'  => array (
                    'pubdate DESC'
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
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }
    
    /**
     * fetchPublishedPages
     * Fetch all pages with status of 1 (published), with all it's
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function fetchAllPublishedPages()
    {
        $results = $this->fetchAllAsArray(
            array(
                'where'  => array(
                    'status = ? AND content_type = ?' => array(1, 2)
                ),
                'order'  => array (
                    'id DESC'
                ),
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPublishedPages
     * Fetch all pages with status of 1 (published), with all it's
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function fetchPublishedPages()
    {
        $results = $this->fetchAllAsArray(
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
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchAllPages
     * Fetch all pages, with all it's pertitent associated data
     * (tags, comments, postinfo, author) as an array
     *
     * @return array
     */
    public function fetchAllPages()
    {
        $results = $this->fetchAllAsArray(
            array(
                'where'  => array(
                    'content_type = ?' => array(2)
                ),
                'order'  => array (
                    'id DESC'
                ),
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchAllPosts
     * Fetch all posts, with all it's pertitent associated data
     * (tags, comments, postinfo, author) as an array
     *
     * @return array
     */
    public function fetchAllPosts()
    {
        $results = $this->fetchAllAsArray(
            array(
                'where'  => array(
                    'content_type = ?' => array(1)
                ),
                'order'  => array (
                    'id DESC'
                ),
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPublishedPostsCount
     * Get the number of published posts
     *
     * @return int
     */
    public function fetchPublishedPostsByDateCount($year = null, $month = null, $day = null)
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
        $result = $this->fetchOne(
            array(
                'cols' => array(
                    'COUNT(*) AS count',
                ),
                'where'  => array(
                     'status = ? AND content_type = ? AND pubdate BETWEEN \''.$start.'\' AND \''.$end.'\'' => array(1, 1)
                ),
            )
        );

        return (int) $result->count;
    }

    /**
     * fetchPublishedPostsByDate
     * Fetch all posts with status of 1 (published) and corresponding year, month, date
     * with all pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function fetchPublishedPostsByDate($year = null, $month = null, $day = null, $page_num = 1)
    {
        $page_num = (int) $page_num;

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
        $results = $this->fetchAllAsArray(
            array(
                'where'  => array(
                     'status = ? AND content_type = ? AND pubdate BETWEEN \''.$start.'\' AND \''.$end.'\'' => array(1, 1)
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
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPublishedPostsByPage
     * Fetch all posts with status of 1 (published) and page, with all
     * pertitent associated data (tags, comments, postinfo) as an array
     *
     * @return array
     */
    public function fetchPublishedPostsByPage($page_num)
    {
        $page_num = (int) $page_num;
        $results = $this->fetchAllAsArray(
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
                    'postinfo',
                    'users',
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPostsByTag
     * Fetch all posts with status of 1 (published) with specific tag(s)
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param array $tags list of tags
     *
     * @param string $oper AND / OR
     *
     * @return array
     */
    public function fetchPostsByTag($tags, $oper = 'AND', $page_num = 1)
    {
        if (!$tags || empty($tags) || ($oper != 'AND' && $oper != 'OR')) {
            return array();
        }

        $where_stmt = 'status = ? AND content_type = ?';
        $where_values = array(1, 1);
        $join = array();
        $count = count($tags);

        for ($i = 0; $i < $count; $i++) {
            $where_values[] = $tags[$i];
            if ($oper == 'AND') {
                $tc = $i + 1;
                $where_stmt .= " AND tags{$tc}.tag_slug = ?";
                if ($tc == 1) {
                    $join[] = array(
                        'type' => "inner",
                        'name' => "{$this->_config['prefix']}posts_tags AS posts_tags{$tc}",
                        'cond' => "posts_tags{$tc}.post_id = posts.id"
                    );
                } else {
                    $join[] = array(
                        'type' => "inner",
                        'name' => "{$this->_config['prefix']}posts_tags AS posts_tags{$tc}",
                        'cond' => "posts_tags{$tc}.post_id = posts_tags{$i}.post_id"
                    );
                }
                $join[] = array(
                    'type' => "inner",
                    'name' => "{$this->_config['prefix']}tags AS tags{$tc}",
                    'cond' => "posts_tags{$tc}.tag_id = tags{$tc}.id"
                );
            }
        }

        if ($oper == 'OR') {
            $join[] = array(
                'type' => "inner",
                'name' => "{$this->_config['prefix']}posts_tags AS posts_tags1",
                'cond' => "posts_tags1.post_id = posts.id"
            );
            $join[] = array(
                'type' => "inner",
                'name' => "{$this->_config['prefix']}tags AS tags1",
                'cond' => "posts_tags1.tag_id = tags1.id"
            );
            $where_stmt .= ' AND tags1.tag_slug IN (' . rtrim(str_repeat('?,', $count), ',') . ')';
        }

        $where = array(
            $where_stmt => $where_values
        );

        $results = $this->fetchAllAsArray(
            array(
                'distinct' => true,
                'where'  => $where,
                'order'  => array ('id DESC'),
                'join'   => $join,
                'paging' => $this->posts_per_page,
                'page'   => $page_num,
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );

        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPostsByTag
     * Fetch all posts/pages with status of 1 (published) with specific tag(s)
     * with all it's pertitent associated data (tags, comments,
     * postinfo) as an array
     *
     * @param array $tags list of tags
     *
     * @param string $oper AND / OR
     *
     * @return array
     */
    public function fetchContentByTag($tags, $oper = 'AND', $page_num = 1)
    {
        if (!$tags || empty($tags) || ($oper != 'AND' && $oper != 'OR')) {
            return array();
        }

        $where_stmt = 'status = ? ';
        $where_values = array(1);
        $join = array();
        $count = count($tags);

        for ($i = 0; $i < $count; $i++) {
            $where_values[] = $tags[$i];
            if ($oper == 'AND') {
                $tc = $i + 1;
                $where_stmt .= " AND tags{$tc}.tag_slug = ?";
                if ($tc == 1) {
                    $join[] = array(
                        'type' => "inner",
                        'name' => "{$this->_config['prefix']}posts_tags AS posts_tags{$tc}",
                        'cond' => "posts_tags{$tc}.post_id = posts.id"
                    );
                } else {
                    $join[] = array(
                        'type' => "inner",
                        'name' => "{$this->_config['prefix']}posts_tags AS posts_tags{$tc}",
                        'cond' => "posts_tags{$tc}.post_id = posts_tags{$i}.post_id"
                    );
                }
                $join[] = array(
                    'type' => "inner",
                    'name' => "{$this->_config['prefix']}tags AS tags{$tc}",
                    'cond' => "posts_tags{$tc}.tag_id = tags{$tc}.id"
                );
            }
        }

        if ($oper == 'OR') {
            $join[] = array(
                'type' => "inner",
                'name' => "{$this->_config['prefix']}posts_tags AS posts_tags1",
                'cond' => "posts_tags1.post_id = posts.id"
            );
            $join[] = array(
                'type' => "inner",
                'name' => "{$this->_config['prefix']}tags AS tags1",
                'cond' => "posts_tags1.tag_id = tags1.id"
            );
            $where_stmt .= ' AND tags1.tag_slug IN (' . rtrim(str_repeat('?,', $count), ',') . ')';
        }

        $where = array(
            $where_stmt => $where_values
        );

        $results = $this->fetchAllAsArray(
            array(
                'distinct' => true,
                'where'  => $where,
                'order'  => array ('id DESC'),
                'join'   => $join,
                'paging' => $this->posts_per_page,
                'page'   => $page_num,
                'eager'  => array(
                    'comments' => array(
                        'eager' => array(
                            'commentinfo'
                        )
                    ),
                    'tags',
                    'postinfo',
                    'users',
                ),
            )
        );

        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }

    /**
     * fetchPostsByTagCount
     * Get the number of posts by tag
     *
     * @return int
     */
    public function fetchPostsByTagCount($tags, $oper = 'AND')
    {
        if (!$tags || empty($tags) || ($oper != 'AND' && $oper != 'OR')) {
            return array();
        }

        $where_stmt = 'status = ? AND content_type = ?';
        $where_values = array(1, 1);
        $join = array();
        $count = count($tags);

        for ($i = 0; $i < $count; $i++) {
            $where_values[] = $tags[$i];
            if ($oper == 'AND') {
                $tc = $i + 1;
                $where_stmt .= " AND tags{$tc}.tag_slug = ?";
                if ($tc == 1) {
                    $join[] = array(
                        'type' => "inner",
                        'name' => "{$this->_config['prefix']}posts_tags AS posts_tags{$tc}",
                        'cond' => "posts_tags{$tc}.post_id = {$this->_config['prefix']}posts.id"
                    );
                } else {
                    $join[] = array(
                        'type' => "inner",
                        'name' => "{$this->_config['prefix']}posts_tags AS posts_tags{$tc}",
                        'cond' => "posts_tags{$tc}.post_id = posts_tags{$i}.post_id"
                    );
                }
                $join[] = array(
                    'type' => "inner",
                    'name' => "{$this->_config['prefix']}tags AS tags{$tc}",
                    'cond' => "posts_tags{$tc}.tag_id = tags{$tc}.id"
                );
            }
        }

        if ($oper == 'OR') {
            $join[] = array(
                'type' => "inner",
                'name' => "{$this->_config['prefix']}posts_tags AS posts_tags1",
                'cond' => "posts_tags1.post_id = {$this->_config['prefix']}posts.id"
            );
            $join[] = array(
                'type' => "inner",
                'name' => "{$this->_config['prefix']}tags AS tags1",
                'cond' => "posts_tags1.tag_id = tags1.id"
            );
            $where_stmt .= ' AND tags1.tag_slug IN (' . rtrim(str_repeat('?,', $count), ',') . ')';
        }

        $where = array(
            $where_stmt => $where_values
        );

        $result = $this->fetchOne(
            array(
                'cols' => array(
                    'COUNT(*) AS count',
                ),
                'distinct' => true,
                'where'  => $where,
                'join'   => $join,
            )
        );

        return (int) $result->count;
    }

    /**
     * fetchPostsByTagCount
     * Get the number of posts/pages by tag
     *
     * @return int
     */
    public function fetchContentByTagCount($tags, $oper = 'AND')
    {
        if (!$tags || empty($tags) || ($oper != 'AND' && $oper != 'OR')) {
            return array();
        }

        $where_stmt = 'status = ?';
        $where_values = array(1);
        $join = array();
        $count = count($tags);

        for ($i = 0; $i < $count; $i++) {
            $where_values[] = $tags[$i];
            if ($oper == 'AND') {
                $tc = $i + 1;
                $where_stmt .= " AND tags{$tc}.tag_slug = ?";
                if ($tc == 1) {
                    $join[] = array(
                        'type' => "inner",
                        'name' => "{$this->_config['prefix']}posts_tags AS posts_tags{$tc}",
                        'cond' => "posts_tags{$tc}.post_id = posts.id"
                    );
                } else {
                    $join[] = array(
                        'type' => "inner",
                        'name' => "{$this->_config['prefix']}posts_tags AS posts_tags{$tc}",
                        'cond' => "posts_tags{$tc}.post_id = posts_tags{$i}.post_id"
                    );
                }
                $join[] = array(
                    'type' => "inner",
                    'name' => "{$this->_config['prefix']}tags AS tags{$tc}",
                    'cond' => "posts_tags{$tc}.tag_id = tags{$tc}.id"
                );
            }
        }

        if ($oper == 'OR') {
            $join[] = array(
                'type' => "inner",
                'name' => "{$this->_config['prefix']}posts_tags AS posts_tags1",
                'cond' => "posts_tags1.post_id = posts.id"
            );
            $join[] = array(
                'type' => "inner",
                'name' => "{$this->_config['prefix']}tags AS tags1",
                'cond' => "posts_tags1.tag_id = tags1.id"
            );
            $where_stmt .= ' AND tags1.tag_slug IN (' . rtrim(str_repeat('?,', $count), ',') . ')';
        }

        $where = array(
            $where_stmt => $where_values
        );

        $result = $this->fetchOne(
            array(
                'cols' => array(
                    'COUNT(*) AS count',
                ),
                'distinct' => true,
                'where'  => $where,
                'join'   => $join,
            )
        );

        return (int) $result->count;
    }

    /**
     * fetchPublishedPostsCount
     * Get the number of published posts
     *
     * @return int
     */
    public function fetchPublishedPostsCount()
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

        return (int) $result->count;
    }

    /**
     * insertContent
     * Insert new content from post data
     *
     * @param $post_data
     * @return mixed last insert id
     */
    public function insertContent($post_data)
    {
        $post_status = (int) $post_data['post_status'];
        if ($post_data['post_status'] < 0 || $post_data['post_status'] > 2) {
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
            'excerpt' => $post_data['post_excerpt'],
            'user_id' => $_SESSION['Foresmo_App']['Foresmo_user_id'],
            'status' => $post_status,
            'pubdate' => $cur_time,
            'modified' => $cur_time,
        );

        return $this->insert($data);
    }

    /**
     * updateContent
     * Update content
     *
     * @param $post_data
     */
    public function updateContent($post_data)
    {
        $cur_time = time();
        $post_status = (int) $post_data['post_status'];
        if ($post_data['post_status'] < 0 || $post_data['post_status'] > 2) {
            // if unexpected int, default to 1 (published)
            $post_data['post_status'] = 1;
        }
        $where = array('id = ?' => (int) $post_data['id']);
        $data = array(
            'title' => $post_data['post_title'],
            'content' => $post_data['post_content'],
            'excerpt' => $post_data['post_excerpt'],
            'modified' => $cur_time,
            'status' => $post_status,
        );

        return $this->update($data, $where);
    }

    /**
     * fetchTotalCount
     * Fetch count of certain type and status
     *
     * $param int $type
     * $param int $status
     * @return int count
     */
    public function fetchTotalCount($type, $status)
    {
        $result = $this->fetchAllAsArray(
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

    /**
     * fetchContentValue
     * Fetch Column for post/page
     *
     * @param int $content_id
     * @param string $col
     * @return mixed
     */
    public function fetchContentValue($content_id, $col)
    {
        $result = $this->fetchValue(
            array(
                'cols' => array($col),
                'where' => array('id = ?' => $content_id),
            )
        );
        Foresmo::escape($result);
        return $result;
    }

    /**
    * deletePost
    * Deletes the selected post
    *
    * @param int $post_id
    */
    public function deletePost($post_id)
    {
        if (!is_numeric($post_id)) {
            return false;
        }
        $post_id = (int) $post_id;

        $where = array('id = ?' => $post_id, 'content_type = ?' => 1);
        $this->delete($where);
        $posts_info = Solar::factory('Foresmo_Model_PostInfo');
        $posts_info->delete(array('post_id = ?' => $post_id));
        $posts_tags = Solar::factory('Foresmo_Model_PostsTags');
        $posts_tags->delete(array('post_id = ?' => $post_id));
        $comments = Solar::factory('Foresmo_Model_Comments');
        $comment_ids = $comments->fetchAll(array(
                'cols' => array(
                    'id'
                ),
                'where' => array(
                    'post_id = ?' => $post_id
                ),
        ));
        $list = array();
        foreach($comment_ids as $comment) {
            $list[] = (int) $comment['id'];
        }
        $count = count($list);
        if ($count > 0) {
            $comments->delete(array('post_id = ?' => $post_id));
            $comment_info = Solar::factory('Foresmo_Model_CommentInfo');
            $where_stmt = 'id IN (' . rtrim(str_repeat('?,', $count), ',') . ')';
            $comment_info->delete(array($where_stmt => $list));
        }
    }

    /**
    * deletePage
    * Deletes the selected page
    *
    * @param int $page_id
    */
    public function deletePage($page_id)
    {
        if (!is_numeric($page_id)) {
            return false;
        }
        $page_id = (int) $page_id;

        $where = array('id = ?' => $page_id, 'content_type = ?' => 2);
        $this->delete($where);
        $posts_info = Solar::factory('Foresmo_Model_PostInfo');
        $posts_info->delete(array('post_id = ?' => $page_id));
        $posts_tags = Solar::factory('Foresmo_Model_PostsTags');
        $posts_tags->delete(array('post_id = ?' => $page_id));
        $comments = Solar::factory('Foresmo_Model_Comments');
        $comment_ids = $comments->fetchAll(array(
                'cols' => array(
                    'id'
                ),
                'where' => array(
                    'post_id = ?' => $page_id
                ),
        ));
        $list = array();
        foreach($comment_ids as $comment) {
            $list[] = (int) $comment['id'];
        }
        $count = count($list);
        if ($count > 0) {
            $comments->delete(array('post_id = ?' => $page_id));
            $comment_info = Solar::factory('Foresmo_Model_CommentInfo');
            $where_stmt = 'id IN (' . rtrim(str_repeat('?,', $count), ',') . ')';
            $comment_info->delete(array($where_stmt => $list));
        }
    }

}

