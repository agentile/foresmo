<div class="module">
<h3 class="module-title">Tags</h3>
<ul class="tags">
<!-- <form id="module-tags" method="post" action="/module/tags/"> -->
<?php
foreach ($this->tags as $tag):
?>
    <li class="tag"><a href="/tag/<?php echo $tag['tag_slug'];?>" class="module_tags" alt="<?php echo $tag['tag'];?>"><?php echo $tag['tag'];?></a><!-- &nbsp;<a href="/feed/<?php echo $tag['tag_slug'];?>"> --></li><!--
<img src="/themes/default/assets/images/rss.gif"></a>&nbsp;(<?php echo $tag['count'];?>)&nbsp;
<input type="checkbox" name="tags[]" value="<?php echo $tag['tag_slug']?>"/>
<br/>-->
<?php
endforeach;
?>
<!--
<div style="width:100%;padding-top:10px;padding-bottom:10px;">
    <input type="radio" name="operator" value="OR"/>
    <label for="operator">OR</label>
    <input type="radio" name="operator" value="AND" checked="checked"/>
    <label for="operator">AND</label>
</div>
<input type="submit" id="module-tags-submit" name="module-tags-submit" value="Filter Posts"/>
</form>
-->
</ul>
</div>
