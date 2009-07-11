<?php
/**
 *
 * Model class.
 *
 */
class Foresmo_Model_PostsTags extends Solar_Sql_Model {

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
        $this->_hasMany('tags', array('foreign_key' => 'id'));
        $this->_hasMany('posts', array('foreign_key' => 'id'));
    }

    /**
     * setPostTags
     *
     * @param $post_id
     * @param $post_data
     */
    public function setPostTags($post_id, $tags)
    {
        $post_id = (int) $post_id;
        $tag_map = array();
        $tags_table = Solar::factory('Foresmo_Model_Tags');
        $existing_tags = $tags_table->fetchArray();
        foreach ($existing_tags as $existing_tag) {
            foreach ($tags as $tag) {
                if (strtolower($tag) == strtolower($existing_tag['tag'])) {
                    $tag_map[$tag] = $existing_tag['id'];
                }
            }
        }
        foreach ($tags as $tag) {
            if (array_key_exists($tag, $tag_map)) {
                $data = array(
                    'post_id' => $post_id,
                    'tag_id' => $tag_map[$tag],
                );
                $this->insert($data);
            } else {
                $data = array(
                    'tag' => $tag,
                    'tag_slug' => $this->makeSlug($tag),
                );
                $last_insert_id = $tags_table->insert($data);
                $data = array(
                    'post_id' => $post_id,
                    'tag_id' => $last_insert_id,
                );
                $this->insert($data);
            }
        }
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
}
