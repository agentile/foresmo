<div class="module">
<h3 class="module-title">Pages</h3>

<?php
foreach ($this->pages as $page) {
?>
<a href="/<?php echo $page['slug'];?>" class="module_pages" alt="<?php echo $page['title'];?>"><?php echo $page['title'];?></a><br/>
<?php
}
?>
</div>