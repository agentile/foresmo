<?php
foreach($this->posts as $post):
?>
    <div class="post">
        <h2 class="post-title"><a href="/<?php echo $post['slug'];?>" alt="<?php echo $post['title'];?>"><?php echo $post['title'];?></a></h2>
        <ul class="post-meta pull-5">
            <li class="post-meta-author">Posted by <?php echo $post['users']['username']; ?></li>
            <li class="post-meta-date"><?php echo $post['pubdate']; ?></li>
            <?php
            if (count($post['comments']) == 1):
            ?>
            <li class="post-meta-comments"><em><?php echo count($post['comments']); ?></em> <a href="/<?php echo $post['slug']; ?>#comments"> comment</a></li>
            <?php
            else:
            ?>
            <li class="post-meta-comments"><em><?php echo count($post['comments']); ?></em> <a href="/<?php echo $post['slug']; ?>#comments"> comments</a></li>
            <?php
            endif;
            ?>
            <?php
            $tags = array();
            foreach ($post['tags'] as $tag):
                $tags[] = "<span class=\"tag\"><a class=\"tag\" href=\"/tag/{$tag['tag_slug']}\" alt=\"{$tag['tag']}\">{$tag['tag']}</a></span>";
            endforeach;
            if (!empty($tags)):
            ?>
            <li class="post-meta-tags"><?php echo implode(', ', $tags); ?></li>
            <?php
            endif;
            ?>
        </ul><?php //. .post-meta ?>
        <div class="post-excerpt">
            <?php echo ($post['excerpt'] != '') ? $post['excerpt'] : $post['content']; ?>
        </div>
    </div><?php //. .post ?>
<?php
endforeach;
?>
<div class="pagination">
    <ul class="pages">
    <?php
    if (strtolower($this->action) == 'tag' || strtolower($this->action) == 'sort'):
        for ($i = 1; $i <= $this->pages_count; $i++):
            echo "<li><a href=\"{$this->query_string}page={$i}\" class=\"pagination\">{$i}</a></li>";
        endfor;
    else:
        for ($i = 1; $i <= $this->pages_count; $i++):
            echo "<li class=\"page\"><a href=\"/page/{$i}\" class=\"pagination\">{$i}</a></li>";
        endfor;
    endif;
    ?>
    </ul>
</div>
