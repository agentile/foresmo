<?php
var_dump($this->posts);
foreach($this->posts as $post) {
?>
    <h4><a href="/<?php echo $post['slug'];?>" alt="<?php echo $post['title'];?>"><?php echo $post['title'];?></a></h4>
    <span><?php echo count($post['comments']);?> comments</span><br/><br/>
    <p><?php echo $post['content'];?></p><br/>
    <?php
    $tags = array();
    foreach ($post['tags'] as $tag) {
        $tags[] = "<span class=\"tag\"><a href=\"/tag/{$tag['tag_slug']}\" alt=\"{$tag['tag']}\">{$tag['tag']}</a></span>";
    }
    echo '<span>Tags: ' . implode(', ', $tags) . '</span>';
    ?>
<?php
}

