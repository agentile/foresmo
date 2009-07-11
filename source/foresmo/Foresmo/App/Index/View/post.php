
    <h2><?php echo $this->posts['title'];?></h2>
    <span>
    <?php
    echo $this->posts['pubdate'] . '<br/>';
    if (count($this->posts['comments']) == 1) {
        echo count($this->posts['comments']) . ' ' . '<a href="/'.$this->posts['slug'].'#comments">comment</a>';
    } else {
        echo count($this->posts['comments']) . ' ' . '<a href="/'.$this->posts['slug'].'#comments">comments</a>';
    }
    ?>
    </span><br/><br/>
    <p><?php echo $this->posts['content'];?></p><br/>
    <?php
    $tags = array();
    foreach ($this->posts['tags'] as $tag) {
        $tags[] = "<span class=\"tag\"><a href=\"/tag/{$tag['tag_slug']}\" alt=\"{$tag['tag']}\">{$tag['tag']}</a></span>";
    }
    echo '<span>Tags: ' . implode(', ', $tags) . '</span>';
    ?>


    <br/><br/>
    <span><a name="comments">Comments</a> - <?php echo count($this->posts['comments']);?></span><br/><br/>
<?php

foreach($this->posts['comments'] as $key => $comment){

    if ($comment['type'] == '1') {
        echo "<div class=\"comment admin\">";
    } else {
        if ($key%2 == 1) {
            echo "<div class=\"comment odd\">";
        } else {
            echo "<div class=\"comment even\">";
        }
    }
    echo '<a name="comment-'.$comment['id'].'"></a>';
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
    echo "</div>";
}
?>
<hr/>
<h2>Post a Comment</h2>
<br/>
<?php
echo $this->form;
echo "\n<br/>";
if (isset($this->msg)) {
    echo $this->msg;
}

