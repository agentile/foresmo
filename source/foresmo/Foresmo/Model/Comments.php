<?php
/**
 * Foresmo_Model_Comments
 * Comments model
 *
 * status codes
 * 0 = hidden, disapproved
 * 1 = visible, approved
 * 2 = spam
 *
 * type codes
 * 0 = regular comment
 * 1 = admin comment
 * 2 = trackback
 *
 */
class Foresmo_Model_Comments extends Solar_Sql_Model {

    public $link_count_limit = 3;

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
        $this->_hasMany('commentinfo', array(
            'foreign_class' => 'Foresmo_Model_CommentInfo',
            'foreign_key' => 'comment_id',
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
        return false;
    }

    /**
     *
     *
     */
    private function _overLinkLimit($comment)
    {
        $count = 0;
        preg_replace("'<a ([^<]*?)</a>'", "", $comment, -1, $count);
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
        $comment = preg_replace("'<a ([^<]*?)</a>'", "", $comment);
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
     * @return array affected rows
     */
    public function insertComment($comment_data, $spam = false)
    {
        $comment_data['post_id'] = (int) $comment_data['post_id'];
        $comment_data['url'] = $this->_cleanURL($comment_data['url']);
        if ($spam) {
            $status = 2;
        } else {
            $status = 1;
        }

        if (isset($_SESSION['Foresmo_App']['Foresmo_user_id'])) {
            $type = 1;
        } else {
            $type = 0;
        }

        $data = array(
            'id' => '',
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
        $result = $this->insert($data);
        return $result;
    }

    /**
    * sanitize
    * Sanitize text output within arrays
    *
    * @param $post
    * @return void
    */
    private function _sanitize(&$data)
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $this->_sanitize($data[$k]);
            }
            if ($k ==='title'
                || $k === 'content'
                || $k === 'modified'
                || $k === 'name'
                || $k === 'email'
                || $k === 'tag') {
                $data[$k] = htmlentities($data[$k], ENT_QUOTES, 'UTF-8');
            }
        }
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
}
