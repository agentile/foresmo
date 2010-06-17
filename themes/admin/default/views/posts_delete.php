<div style="margin: 10 auto;margin-bottom:10px;text-align:center;">
<p>Are you sure you want to delete "<?php echo $this->data['title']; ?>"?</p>
<form method="post" action="/admin/posts/delete/<?php echo $this->data['slug']; ?>">
<input type="submit" name="yes" value="Yes"/><input type="submit" name="no" value="No"/>
</form>
</div>