
<div style="margin:0 auto;border:1px solid #cecece;padding:5px;width:700px;"
<h2>Links Admin</h2>
<form method="post" action="/admin/modules/<?php echo $this->url; ?>">
<label for="title">Link Title</label><br/>
<input type="text" name="title" value="<?php echo $this->link['name'];?>"/><br/>
<label for="url" value="<?php echo $this->link['url'];?>">Link URL</label><br/>
<input type="text" name="url" /><br/>
<label for="target">Link Target</label><br/>
<input type="radio" name="target" value="_top" <?php echo ($this->link['target'] == '_top' || $this->link['target'] == '') ? 'checked="checked"': '';?>/> _top (same window)<br/>
<input type="radio" name="target" value="_self" <?php echo ($this->link['target'] == '_self') ? 'checked="checked"': '';?>/> _self (same frame) <br/>
<input type="radio" name="target" value="_parent" <?php echo ($this->link['target'] == '_parent') ? 'checked="checked"': '';?>/> _parent (parent frame) <br/>
<input type="radio" name="target" value="_blank" <?php echo ($this->link['target'] == '_blank') ? 'checked="checked"': '';?>/> _blank (new window) <br/>
<label for="status">Link Status</label><br/>
<select name="status">
<option value="1" <?php echo ($this->link['status'] == 1) ? 'selected="true"': '';?>>Live</option>
<option value="0" <?php echo ($this->link['status'] == 0) ? 'selected="true"': '';?>>Hidden</option>
</select>
<input type="submit" name="submit" value="Save Link">
</form><br/>
<a href="/admin/modules/edit/Links">Back to Links Admin</a>
<br/>
</div>
<br/><br/>