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

        $this->_table_name = $this->_config['prefix'] . Solar_File::load($dir . 'table_name.php');
        $this->_table_cols = Solar_File::load($dir . 'table_cols.php');
        $this->_hasMany('tags', array('foreign_key' => 'id'));
        $this->_hasMany('posts', array('foreign_key' => 'id'));
    }

    /**
     * insertContentTags
     * Insert tags for a post/page
     *
     * @param $id
     * @param $tags
     */
    public function insertContentTags($id, $tags = array())
    {
        if ($id != (int) $id) {
            return false;
        }
        $id = (int) $id;
        $tag_map = array();
        $tags_table = Solar::factory('Foresmo_Model_Tags');
        $existing_tags = $tags_table->fetchAllAsArray();
        foreach ($existing_tags as $existing_tag) {
            foreach ($tags as $tag) {
                if (Foresmo::makeSlug($tag) == $existing_tag['tag_slug']) {
                    $tag_map[$tag] = $existing_tag['id'];
                }
            }
        }
        foreach ($tags as $tag) {
            if (array_key_exists($tag, $tag_map) && !$this->hasTag($id, $tag_map[$tag])) {
                $data = array(
                    'post_id' => $id,
                    'tag_id' => $tag_map[$tag],
                );
                $this->insert($data);
            } else {
                $data = array(
                    'tag' => $tag,
                    'tag_slug' => Foresmo::makeSlug($tag),
                );
                $last_insert_id = $tags_table->insert($data);
                $data = array(
                    'post_id' => $id,
                    'tag_id' => $last_insert_id,
                );
                $this->insert($data);
            }
        }
    }

    /**
     * deleteContentTagsById
     * Delete all tag associations to a post
     *
     * @param int $post_id
     */
    public function deleteContentTagsById($id, $tag_ids = null)
    {
        if ($id != (int) $id) {
            return false;
        }
        $id = (int) $id;
        if (is_null($tag_ids)) {
            $where = array('post_id = ?' => $id);
            return $this->delete($where);
        } else {
            $tag_ids = (array) $tag_ids;
            foreach ($tag_ids as $tag_id) {
                if ($id != (int) $id) {
                    continue;
                }
                $where = array('post_id = ? AND tag_id = ?' => array($id, (int) $tag_id));
                $this->delete($where);
            }
        }
    }

    /**
     * updateContentTags
     * Update Tags for a post/page
     * @param $id
     * @param $tags
     */
    public function updateContentTags($id, $tags)
    {
        if ($id != (int) $id) {
            return false;
        }
        $id = (int) $id;
        $tag_map = array();
        $tags_table = Solar::factory('Foresmo_Model_Tags');
        $existing_tags = $tags_table->fetchAllAsArray();
        $content_tags = $tags_table->fetchTagsByID($id);
        foreach ($existing_tags as $existing_tag) {
            foreach ($tags as $tag) {
                if (Foresmo::makeSlug($tag) == $existing_tag['tag_slug']) {
                    $t = $existing_tag['tag_slug'];
                    $tag_map[$t] = $existing_tag['id'];
                }
            }
        }

        // find diff in tags to update and existing tags for content
        $ct_slugs = array();
        $tag_slugs = array();
        foreach ($content_tags as $t) {
            $ct_slugs[] = $t['tag_slug'];
        }
        foreach ($tags as $tag) {
            $tag_slugs[] = Foresmo::makeSlug($tag);
        }

        // delete tags if necessary
        $to_delete = array_diff($ct_slugs, $tag_slugs);
        $delete_tag_ids = array();
        foreach ($to_delete as $del_tag) {
            $delete_tag_ids[] = $tags_table->fetchTagIdBySlug($del_tag);
        }
        if (!empty($to_delete)) {
            $this->deleteContentTagsById($id, $delete_tag_ids);
        }

        // add tags
        foreach ($tag_slugs as $tag) {
            if (array_key_exists($tag, $tag_map) && !$this->hasTag($id, $tag_map[$tag])) {
                $data = array(
                    'post_id' => $id,
                    'tag_id' => $tag_map[$tag],
                );
                $this->insert($data);
            } elseif (!array_key_exists($tag, $tag_map)) {
                $data = array(
                    'tag' => $tag,
                    'tag_slug' => Foresmo::makeSlug($tag),
                );
                $last_insert_id = $tags_table->insert($data);
                $data = array(
                    'post_id' => $id,
                    'tag_id' => $last_insert_id,
                );
                $this->insert($data);
            }
        }
    }

    /**
     * hasTag
     * Check to see if a post has a particular tag
     *
     * @param int $post_id
     * @param int $tag_id
     * @return bool
     */
    public function hasTag($id, $tag_id)
    {
        if ($id != (int) $id || $tag_id != (int) $tag_id) {
            return false;
        }
        $id = (int) $id;
        $tag_id = (int) $tag_id;
        $results = $this->fetchOneAsArray(
            array(
                'where' => array(
                    'post_id = ? AND tag_id = ?' => array($id, $tag_id),
                )
            )
        );

        return (bool) !empty($results);
    }
}
