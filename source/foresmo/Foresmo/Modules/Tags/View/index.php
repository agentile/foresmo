<div id="module">
<h2>Tags</h2>
<form id="module-tags" method="post" action="/module/tags/">
<?php
foreach ($this->tags as $tag) {
?>
<a href="/tag/<?php echo $tag['tag_slug'];?>" class="module_tags" alt="<?php echo $tag['tag'];?>"><?php echo $tag['tag'];?></a>&nbsp;(<?php echo count($tag['posts']);?>)&nbsp;
<input type="checkbox" name="tags[]" value="<?php echo $tag['tag_slug']?>"/>
<br/>
<?php
}
?>
<div style="width:100%;padding-top:10px;padding-bottom:10px;">
    <input type="radio" name="operator" value="OR"/>
    <label for="operator">OR</label>
    <input type="radio" name="operator" value="AND" checked="checked"/>
    <label for="operator">AND</label>
</div>
<input type="submit" id="module-tags-submit" name="module-tags-submit" value="Filter Posts"/>
</form>
</div>