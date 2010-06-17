        <div class="grid_16">
            <h2 class="page-heading">Home</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="clear"></div>
        <div class="grid_4">
            <div class="box menu">
                <h2>Left Nav</h2>
                <div class="block" id="section-menu">
                    <ul class="section menu">
                        <li>
                            <a href="#" class="menuitem">Something</a>
                            <ul class="submenu">
                                <li>
                                    <a href="#">hola</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="menuitem">Another thing</a>
                            <ul class="submenu">
                                <li>
                                    <a href="#">blah</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="grid_7">
            <div class="box articles">
                <h2>
                    Recent Comments
                </h2>
                <div class="block" id="comments">
                <?php
                    foreach ($this->recent_comments as $k => $comment) {
                        if ($k == 0) {
                            echo '<div class="first comment">';
                        } else {
                            echo '<div class="comment">';
                        }
                        $default = 'http://' . $_SERVER['SERVER_NAME'] . "/public/Foresmo/{$this->blog_theme}/images/contact_grey.png";
                        $size = 50;
                        $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($comment['email'])."&default=".urlencode($default)."&size=".$size;
                        $name = $comment['name'];
                        if (isset($comment['url']) && $comment['url'] != '') {
                            $name = '<a href="'.$comment['url'].'" target="_blank">'.$name.'</a>';
                        }
                        echo '<p class="meta">'.$name.' &mdash; '.$comment['date'].' &mdash; in <a href="/'.$comment['post']['slug'].'/#comment-'.$comment['id'].'">'.$comment['post']['title'].'</a></p>';
                        echo '<img src="'.$grav_url.'" alt="gravatar"/>';

                        echo '<p>' . $comment['content'] . '</p>';
                        echo '</div>';
                    }
                ?>

                </div>
            </div>
        </div>
        <div class="grid_5">
            <div class="box">
                <h2>Quick Stats</h2>
                <div class="block">
                <p>
                Total Blog Posts: <?php echo $this->quick_stats['total_posts'];?><br/>
                Total Pages: <?php echo $this->quick_stats['total_pages'];?><br/>
                Total Comments: <?php echo $this->quick_stats['total_comments'];?><br/>
                </p>
                </div>
            </div>

        </div>
        <div class="clear"></div>
