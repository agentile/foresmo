<?php
foreach($this->posts as $post) {
?>
    <h2><?php echo $post['title'];?></h2>
    <span>
    <?php
    if (count($post['comments']) == 1) {
        echo count($post['comments']) . ' ' . '<a href="/'.$post['slug'].'#comments">comment</a>';
    } else {
        echo count($post['comments']) . ' ' . '<a href="/'.$post['slug'].'#comments">comments</a>';
    }
    ?>
    </span><br/><br/>
    <p><?php echo $post['content'];?></p><br/>
    <?php
    $tags = array();
    foreach ($post['tags'] as $tag) {
        $tags[] = "<span class=\"tag\"><a href=\"/tag/{$tag['tag_slug']}\" alt=\"{$tag['tag']}\">{$tag['tag']}</a></span>";
    }
    echo '<span>Tags: ' . implode(', ', $tags) . '</span>';
    ?>


    <br/><br/>
    <span><a name="comments">Comments</a> - <?php echo count($post['comments']);?></span><br/><br/>
<?php

foreach($post['comments'] as $key => $comment){

    if ($comment['email'] === 'asgentile@gmail.com') {
        echo "<div class=\"comment admin\">";
    } else {
        if ($key%2 == 1) {
            echo "<div class=\"comment odd\">";
        } else {
            echo "<div class=\"comment even\">";
        }
    }
    echo '<a name="comment-'.$comment['id'].'"></a>';
    $default = "http://agentile.com/img/contact_grey.png";
    $size = 50;
    $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($comment['email'])."&default=".urlencode($default)."&size=".$size;

    echo "<div class=\"comment-author\" style=\"float:left;width:150px;\">";
    if ($comment['url'] !== '') {
        echo "<span style=\"font-size:0.8em;\"><a href=\"".$comment['url']."\" target=\"_blank\">".$comment['name']."</a></span><br />";
    } else {
        echo "<span style=\"font-size:0.8em;\">".$comment['name']."</span><br/>";
    }

    echo '<img src="'.$grav_url.'"/>';
    echo "</div>";
    echo "<div class=\"comment-message\" style=\"float:right;width:470px;\">";
    echo "<span style=\"font-size:0.9em;\">".$comment['content']."</span><br />";
    echo "</div>";
    echo "</div>";
}
echo "<br/>";
?>

<?php
}

