<div id="module">
<h2>Tags</h2>

<?php
foreach ($this->tags as $tag) {
?>
<a href="/tag/<?php echo $tag['tag_slug'];?>" class="module_tags" alt="<?php echo $tag['tag'];?>"><?php echo $tag['tag'];?></a>&nbsp; (<?php echo count($tag['posts']);?>)<br/>
<?php
}
?>
</div>