<?php
/**
 * Foresmo_Modules_Twitter
 *
 *
 */
class Foresmo_Modules_Twitter extends Foresmo_Modules_Base {

    protected $_Foresmo_Modules_Twitter = array();

    public $info = array(
        'name' => 'Twitter',
        'description' => 'Personal Twitter stream'
    );

    public $output = '';

    /**
     * _postConstruct
     *
     * @param $model
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
    }

    /**
     * start
     * Begin Module Work
     *
     * @return void
     */
    public function start()
    {
        $this->_refreshModuleInfo();
        $un = null;
        $freq = 12;
        $count = 5;
        $tweets = array();
        foreach ($this->_module_info['moduleinfo'] as $mi) {
            if ($mi['name'] == 'twitter_name') {
                $un = $mi['value'];
            } elseif ($mi['name'] == 'twitter_stream_count') {
                $count = $mi['value'];
            } elseif ($mi['name'] == 'twitter_stream_frequency') {
                $freq = $mi['value'];
            }
        }

        if ($un) {
            $tweets = $this->_fetchTweets($un, $count, $freq);
        }
        $this->_view->assign('tweets', $tweets);
        $this->_view->assign('count', $count);
        $this->_view->assign('timezone', Foresmo::$timezone);
        $this->_view->assign('date_format', Foresmo::$date_format);
        $this->output = $this->_view->fetch($this->_view_file);
    }
    
    protected function _fetchTweets($un, $count = 5, $freq = 12)
    {
        // clean up twitter name (no @)
        if (substr(trim($un), 0, 1) == '@') {
            $un = substr(trim($un), 1);
        }
        
        $stream = null;
        $last_updated = null;
        // get last updated and tweets
        foreach ($this->_module_info['moduleinfo'] as $mi) {
            if ($mi['name'] == 'twitter_stream') {
                $stream = $mi['value'];
            } elseif ($mi['name'] == 'twitter_stream_last_updated') {
                $last_updated = $mi['value'];
            } 
        }
        
        // first use
        if (!$stream || !$last_updated) {
            $module_id = $this->_module_info['id'];
            $stream = serialize(array());
            $last_updated = time();
            $data = array(
                    'name' => 'twitter_stream',
                    'type' => 0,
                    'value' => $stream,
                );
            $this->_model->module_info->insertModuleEntry($module_id, $data);
            $data = array(
                    'name' => 'twitter_stream_last_updated',
                    'type' => 0,
                    'value' => $last_updated,
                );
            $this->_model->module_info->insertModuleEntry($module_id, $data);
        }
        
        $add = ((int) $freq) * 60 * 60;
        if (time() >= ($last_updated + $add)) {
            return $this->_getUsersTweets(array($un), $count);
        }

        return unserialize($stream);
    }
    
    protected function _getUsersTweets($tuns, $limit = 5)
    {
        $arr = array();
        foreach ($tuns as $tun) {
            $this->url = "http://twitter.com/statuses/user_timeline.xml?screen_name=$tun";
            $this->xml = @simplexml_load_file($this->url);
            if ($this->xml == false) {
                continue;
            }

            $i = 0;
            foreach ($this->xml->children() as $tweet) {
                $offset = (int) substr($tweet->user->utc_offset[0], 1);
                $time = (int) strtotime($tweet->created_at);
                $arr[] = array(
                    'tweet' => $this->_formatTweet((string) $tweet->text),
                    'ts' => (int) $time,
                    'name' => (string) $tweet->user->name,
                    'username' => (string) $tweet->user->screen_name,
                    'img' => (string) $tweet->user->profile_image_url,
                    'utc_offset' => (string) $tweet->user->utc_offset,
                );
                $i++;
                if ($limit > 0 && $limit == $i) {
                    break;
                }
            }
        }

        // order by time desc
        $arr2 = array();
        while (count($arr) > 0) {
            $max = 0;
            $key = 0;
            foreach ($arr as $k => $v) {
                if ($v['ts'] > $max) {
                    $max = $v['ts'];
                    $key = $k;
                }
            }
            $arr2[] = $arr[$key];
            unset($arr[$key]);
            $arr = array_values($arr);
        }
        
        $module_id = $this->_module_info['id'];
        $stream_id = null;
        $lu_id = null;
        foreach ($this->_module_info['moduleinfo'] as $mi) {
            if ($mi['name'] == 'twitter_stream') {
                $stream_id = $mi['id'];
            } elseif ($mi['name'] == 'twitter_stream_last_updated') {
                $lu_id = $mi['id'];
            }
        }
        $data = array(
            'name' => 'twitter_stream',
            'type' => 0,
            'value' => serialize($arr2),
        );
        $this->_model->module_info->updateModuleEntryById($stream_id, $module_id, $data);
        $data = array(
            'name' => 'twitter_stream_last_updated',
            'type' => 0,
            'value' => time(),
        );
        $this->_model->module_info->updateModuleEntryById($lu_id, $module_id, $data);
        return $arr2;
    }


    protected function _formatTweet($tweet)
    {
        $tweet = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" target=\"_blank\">\\2</a>'", $tweet);
        $tweet = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>'", $tweet);
        $tweet = preg_replace("(@([a-zA-Z0-9_]+))", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">\\0</a>", $tweet);
        return $tweet;
    }

    public function admin($data)
    {
        $this->_refreshModuleInfo();
        $this->_setViewFile('admin.php');
        $name = '';
        $count = 5; // default 5 tweets
        $freq = 12; // default 12 hours
        
        foreach ($this->_module_info['moduleinfo'] as $mi) {
            if ($mi['name'] == 'twitter_name') {
                $name = $mi['value'];
            } elseif ($mi['name'] == 'twitter_stream_count') {
                $count = $mi['value'];
            } elseif ($mi['name'] == 'twitter_stream_frequency') {
                $freq = $mi['value'];
            }
        }
        $this->_view->assign('twitter_name', $name);
        $this->_view->assign('twitter_stream_count', $count);
        $this->_view->assign('twitter_stream_frequency', $freq);
        $this->output = $this->_view->fetch($this->_view_file);
    }

    /**
     * admin_request
     * module admin request
     *
     * @param array $data
     * @return void
     */
    public function admin_request($data)
    {
        $this->_refreshModuleInfo();
        $post_data = $data['POST'];

        if (isset($post_data['twitter_name']) && trim($post_data['twitter_name']) != '') {
            $found = false;
            $id = null;
            $module_id = $this->_module_info['id'];
            foreach ($this->_module_info['moduleinfo'] as $mi) {
                if ($mi['name'] == 'twitter_name') {
                    $found = true;
                    $id = $mi['id'];
                }
            }
            if ($found) { // update
                $data = array(
                    'name' => 'twitter_name',
                    'type' => 0,
                    'value' => $post_data['twitter_name'],
                );
                $this->_model->module_info->updateModuleEntryById($id, $module_id, $data);
            } else { //insert
                $data = array(
                    'name' => 'twitter_name',
                    'type' => 0,
                    'value' => $post_data['twitter_name'],
                );
                $this->_model->module_info->insertModuleEntry($module_id, $data);
            }
        } 
        if (isset($post_data['twitter_stream_count']) && trim($post_data['twitter_stream_count']) != '' && is_numeric($post_data['twitter_stream_count'])) {
            $found = false;
            $id = null;
            $module_id = $this->_module_info['id'];
            foreach ($this->_module_info['moduleinfo'] as $mi) {
                if ($mi['name'] == 'twitter_stream_count') {
                    $found = true;
                    $id = $mi['id'];
                }
            }
            if ($found) { // update
                $data = array(
                    'name' => 'twitter_stream_count',
                    'type' => 0,
                    'value' => $post_data['twitter_stream_count'],
                );
                $this->_model->module_info->updateModuleEntryById($id, $module_id, $data);
            } else { //insert
                $data = array(
                    'name' => 'twitter_stream_count',
                    'type' => 0,
                    'value' => $post_data['twitter_stream_count'],
                );
                $this->_model->module_info->insertModuleEntry($module_id, $data);
            }
        } 
        if (isset($post_data['twitter_stream_frequency']) && trim($post_data['twitter_stream_frequency']) != '' && is_numeric($post_data['twitter_stream_frequency'])) {
            $found = false;
            $id = null;
            $module_id = $this->_module_info['id'];
            foreach ($this->_module_info['moduleinfo'] as $mi) {
                if ($mi['name'] == 'twitter_stream_frequency') {
                    $found = true;
                    $id = $mi['id'];
                }
            }
            if ($found) { // update
                $data = array(
                    'name' => 'twitter_stream_frequency',
                    'type' => 0,
                    'value' => $post_data['twitter_stream_frequency'],
                );
                $this->_model->module_info->updateModuleEntryById($id, $module_id, $data);
            } else { //insert
                $data = array(
                    'name' => 'twitter_stream_frequency',
                    'type' => 0,
                    'value' => $post_data['twitter_stream_frequency'],
                );
                $this->_model->module_info->insertModuleEntry($module_id, $data);
            }
        }
    }
}
