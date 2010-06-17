
<div style="margin:0 auto;border:1px solid #cecece;padding:5px;width:700px;"></div>
<h2>Links >> Delete</h2>
<h4>Are you sure you want to delete the following link?</h4>
<p><?php echo $this->link['name']?></p><br/>
<form method="post" action="/admin/modules/<?php echo $this->url; ?>">
<input type="hidden" name="yes" value="true"/>
<input type="submit" name="submit" value="Yes, Delete">
</form><br/>
<a href="/admin/modules/edit/Links">Back to Links Admin</a>
<br/>
</div>
<br/><br/>