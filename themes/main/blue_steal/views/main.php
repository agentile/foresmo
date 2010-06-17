<?php
foreach($this->posts as $post) {
?>
    
    <div class="postInfo">
        
        <div id="data" class="grid_3 alpha">
            <div class="date"><?php echo $post['pubdate']; ?></div>
            <div class="postedBy">posted by <?php echo $post['users']['username']; ?></div>
            <div class="postedBy">
                <?php if(count($post['comments']) == 1) {
                    echo count($post['comments']) . ' ' . '<a href="/'.$post['slug'].'#comments">comment</a>';
                } else {

                    echo count($post['comments']) . ' ' . '<a href="/'.$post['slug'].'#comments">comments</a>';
    
                }
                ?>
            </div>

            <div class="postedBy">
            
    
                 <?php
                     $tags = array();
                    foreach ($post['tags'] as $tag) {
                    $tags[] = "<span class=\"tag\"><a href=\"/tag/{$tag['tag_slug']}\" alt=\"{$tag['tag']}\">{$tag['tag']}</a></span>";
                     }
                    if (!empty($tags)) {
                    echo '<span>Tags: ' . implode(', ', $tags) . '</span>';
                    }
                ?>
            </div>
        </div>

        <div id="post" class="grid_9 omega">
            <h2><a href="/<?php echo $post['slug'];?>" alt="<?php echo $post['title'];?>"><?php echo $post['title'];?></a></h2>
            
            <div class="content">
                <?php echo $post['content'];?>
            </div>
        </div>

    </div>

<?php } ?>
    
<br/><br/>
<div id="pagination" class="grid_12">
<?php
if (strtolower($this->action) == 'tag' || strtolower($this->action) == 'sort') {
    for ($i = 1; $i <= $this->pages_count; $i++) {
        echo "<a href=\"{$this->query_string}page={$i}\" class=\"pagination\">{$i}</a>";
    }
} else {
    for ($i = 1; $i <= $this->pages_count; $i++) {
        echo "<a href=\"/page/{$i}\" class=\"pagination\">{$i}</a>";
    }
}
?>
</div>

