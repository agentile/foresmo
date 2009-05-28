<?php
foreach($this->posts as $post) {
?>
    <h2><a href="/<?php echo $post['slug'];?>" alt="<?php echo $post['title'];?>"><?php echo $post['title'];?></a></h2>
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
<?php
}

