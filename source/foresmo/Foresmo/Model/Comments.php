<?php
/**
 * Foresmo_Model_Comments
 * Comments model
 *
 * status codes
 * 0 = hidden, disapproved,
 * 1 = visible, approved
 * 2 = spam
 * 3 = under moderation
 *
 * type codes
 * 0 = regular comment
 * 1 = admin comment
 * 2 = trackback
 *
 */
class Foresmo_Model_Comments extends Solar_Sql_Model {

    public $link_count_limit = 3;
    public $default_status = 3;

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
        $this->_hasMany('commentinfo', array(
            'foreign_class' => 'Foresmo_Model_CommentInfo',
            'foreign_key' => 'comment_id',
        ));
        $this->_hasOne('post', array(
            'foreign_class' => 'Foresmo_Model_Posts',
            'foreign_key' => 'id',
        ));
    }

    /**
     * isSpam
     * performs checks to see if comment is Spam
     *
     * @param $form_data
     * @return bool
     */
    public function isSpam($form_data)
    {
        // check honey pot
        if (!isset($form_data['spam_empty']) || $form_data['spam_empty'] !== '') {
            return true;
        }
        if ($this->_hasOnlyLinks($form_data['comment'])) {
            return true;
        }
        if ($this->_overLinkLimit($form_data['comment'])) {
            return true;
        }
        if ($this->_previousSpammer($form_data['email'])) {
            return true;
        }
        return false;
    }
    
    /**
     * _previousSpammer
     *
     */
    private function _previousSpammer($email)
    {
        $ip = sprintf("%u", ip2long(Foresmo::getIP()));
        $results = $this->fetchAllAsArray(array(
            'where' => array('(email = ? OR ip = ?) AND status = 2' => array($email, $ip))
        ));
        return (count($results) > 0) ? true : false;
    }

    /**
     * _overLinkLimit
     *
     */
    private function _overLinkLimit($comment)
    {
        $count = 0;
        $count2 = 0;
        preg_replace("'<a ([^<]*?)</a>'", "", $comment, -1, $count);
        preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", '', $comment, -1, $count2);
        $count = $count + $count2;
        if ($count <= $this->link_count_limit) {
            return false;
        }
        return true;
    }

    /**
     * _hasOnlyLinks
     * isSpam sub-function: check to see if the comment containted only link(s)
     *
     * @param $comment
     * @return bool
     */
    private function _hasOnlyLinks($comment)
    {
        $comment = preg_replace("'<a ([^<]*?)</a>'", '', $comment);
        $comment = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", '', $comment);
        $comment = trim($comment);
        return (empty($comment)) ? true : false;
    }

    /**
     * insertComment
     * Insert a comment from post data
     *
     * @param $comment_data
     * @param $spam default false
     *
     * @return mixed last insert id
     */
    public function insertComment($comment_data, $spam = false)
    {
        $comment_data['post_id'] = (int) $comment_data['post_id'];
        $comment_data['url'] = $this->_cleanURL($comment_data['url']);
        
        if (isset($_SESSION['Foresmo_App']['Foresmo_user_id'])) {
            $type = 1;
        } else {
            $type = 0;
        }
        
        if ($spam) {
            $status = 2;
        } elseif ($type == 1) {
            $status = 1;
        } else {
            $status = $this->default_status;
        }

        $data = array(
            'post_id' => $comment_data['post_id'],
            'name' => $comment_data['name'],
            'email' => $comment_data['email'],
            'url' => $comment_data['url'],
            'ip' => sprintf("%u", ip2long(Foresmo::getIP())),
            'content' => $comment_data['comment'],
            'status' => $status,
            'date' => time(),
            'type' => $type,
        );
        return $this->insert($data);
    }

    /**
     * _cleanURL
     * clean a comment URL field
     *
     * @param $url
     * @return string
     */
    private function _cleanURL($url)
    {
        if (empty($url)) {
            return $url;
        }
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$*\'()\\x80-\\xff]|i', '', $url);
        $strip = array('%0d', '%0a');
        $url = str_replace($strip, '', $url);
        $url = str_replace(';//', '://', $url);
        if ( strpos($url, ':') === false &&
            substr( $url, 0, 1 ) != '/' && !preg_match('/^[a-z0-9-]+?\.php/i', $url) ) {
            $url = 'http://' . $url;
        }
        return $url;
    }

    /**
     * fetchRecentComments
     * Fetch recent comments
     *
     * @param int $limit limit (default 10)
     * @return array result set
     */
    public function fetchRecentComments($limit = 10, $spam = false)
    {
        $limit = (int) $limit;
        
        if ($spam) {
            $where = array('comments.type = ?' => array(0));
        } else {
            $where = array('comments.status <> ? AND comments.type = ?' => array(2, 0));
        }

        $results = $this->fetchAllAsArray(
            array(
                'where' => $where,
                'eager' => array(
                    'commentinfo', 'post'
                ),
                'order'  => array (
                    'id DESC'
                ),
                'limit'  => array($limit),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }
    
    /**
     * fetchComments
     * Fetch all comments
     *
     * @return array result set
     */
    public function fetchComments($spam = false)
    {
        if ($spam) {
            $where = array();
        } else {
            $where = array('comments.status <> ?' => array(2));
        }
        $results = $this->fetchAllAsArray(
            array(
                'where' => $where,
                'eager' => array(
                    'commentinfo', 'post'
                ),
                'order'  => array (
                    'id DESC'
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
    }
    
    /**
     * fetchSpam
     * Fetch all spam comments
     *
     * @return array result set
     */
    public function fetchSpam()
    {
        $results = $this->fetchAllAsArray(
            array(
                'where' => array('comments.status = ?' => array(2)),
                'eager' => array(
                    'commentinfo', 'post'
                ),
                'order'  => array (
                    'id DESC'
                ),
            )
        );
        Foresmo::dateFilter($results);
        Foresmo::escape($results);
        return $results;
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
                    'type = ?' => $type,
                    'status = ?' => $status,
                ),
            )
        );
        return (int) $result[0]['count'];
    }
    
    public function updateCommentsStatus($comments, $status)
    {
        if (!is_array($comments)) {
            return false;
        }
        
        foreach ($comments as $comment_id) {
            $this->updateCommentStatus($comment_id, $status);
        }
    }
    
    public function updateCommentStatus($comment_id, $status)
    {
        $comment_id = (int) $comment_id;
        $status = (int) $status;

        $data = array(
            'status' => $status,
        );
        
        $where = array('id = ?' => $comment_id);
        
        $this->update($data, $where);
    }
    
    public function deleteComments($comments)
    {
        if (!is_array($comments)) {
            return false;
        }
        
        foreach ($comments as $comment_id) {
            $this->deleteComment($comment_id);
        }
    }
    
    public function deleteComment($comment_id)
    {
        $where = array('id = ?' => $comment_id);
        
        $this->delete($where);
    }
}
