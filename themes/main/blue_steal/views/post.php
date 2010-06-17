    <div class="grid_12 alpha">

        <div class="postInfo">

            <div id="data" class="grid_3 alpha">

                <div class="date"><?php echo $this->posts['pubdate']; ?></div>
                <div class="postedBy">posted by <?php echo $this->posts['users']['username']; ?></div>
                <div class="postedBy">
                <?php 
                    if (count($this->posts['comments']) == 1) {
                    echo count($this->posts['comments']) . ' ' . '<a href="/'.$this->posts['slug'].'#comments">comment</a>';
                    } else {
                    echo count($this->posts['comments']) . ' ' . '<a href="/'.$this->posts['slug'].'#comments">comments</a>';
                    }
                ?>
                </div>
                <div class="postedBy">
                
                <?php
                $tags = array();
                foreach ($this->posts['tags'] as $tag) {
                    $tags[] = "<span class=\"tag\"><a href=\"/tag/{$tag['tag_slug']}\" alt=\"{$tag['tag']}\">{$tag['tag']}</a></span>";
                }
                if (!empty($this->posts['tags'])) {
                echo '<span>Tags: ' . implode(', ', $tags) . '</span>';
                }
                ?>
                </div>

            </div>

            <div id="post" class="grid_9 omega">
                <h2><?php echo $this->posts['title'];?></h2>

                <div class="content">
                <?php echo $this->posts['content'];?>
                </div>

                <div class="comments">

                <?php

                $comments_count = count($this->posts['comments']);
                if ($this->comments_disabled) {
                echo '<span>Comments have been disabled for this post</span><br/><br/>';
                } elseif ($comments_count == 0) {

                } else {
                echo '<span><a name="comments">Comments</a> - '.$comments_count.'</span><br/><br/>';
                }

                foreach($this->posts['comments'] as $key => $comment){

                if($comment['status'] != '1') {
                continue;
                }

                if ($comment['type'] == '1') {
                    echo "<div class=\"commentContent admin\">";
                } else {
                    if ($key%2 == 1) {
                    echo "<div class=\"commentContent odd\">";
                    } else {
                    echo "<div class=\"commentContent even\">";
                }
            }
            echo '<a name="comment-'.($key+1).'"></a>';
            $default = 'http://' . $_SERVER['SERVER_NAME'] . "/public/Foresmo/{$this->blog_theme}/images/contact_grey.png";
            $size = 50;
            $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($comment['email'])."&default=".urlencode($default)."&size=".$size;

            echo "<div class=\"comment-author\">";
            if ($comment['url'] !== '') {
                echo "<span style=\"font-size:0.8em;\"><a href=\"".$comment['url']."\" target=\"_blank\">".$comment['name']."</a></span><br />";
            } else {
                echo "<span style=\"font-size:0.8em;\">".$comment['name']."</span><br/>";
            }

            echo '<img src="'.$grav_url.'"/>';
            echo "</div>";
            echo "<div class=\"comment-message\">";
            echo "<span style=\"font-size:0.6em;\">{$comment['date']}</span><br/>";
            echo "<span style=\"font-size:0.9em;\">".$comment['content']."</span><br />";
            echo "</div>";
            echo "<div class=\"comment-link\">";
            echo "<a href=\"/".$this->posts['slug']."#comment-".($key+1)."\">link</a>";
            echo "</div>";
            echo "</div>";
            }
            ?>
            <hr/>
            <?php
            if (!$this->comments_disabled) {
            ?>
            <h2>Post a Comment</h2>
            <br/>
            <?php
            echo $this->form;
            echo "\n<br/>";
            if (isset($this->msg)) {
            echo $this->msg;
            }
        }
        ?>
                </div>
            </div>

        </div>
   </div> 
