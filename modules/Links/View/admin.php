<div style="margin:0 auto;border:1px solid #cecece;padding:5px;width:700px;">
<h2>Links Module Settings</h2>
<form method="post" action="/admin/modules/edit/Links">
<label for="title">Link Title</label><br/>
<input type="text" name="title" /><br/>
<label for="url">Link URL</label><br/>
<input type="text" name="url" /><br/>
<label for="target">Link Target</label><br/>
<input type="radio" name="target" value="_top" checked="checked"/> _top (same window)<br/>
<input type="radio" name="target" value="_self" /> _self (same frame) <br/>
<input type="radio" name="target" value="_parent" /> _parent (parent frame) <br/>
<input type="radio" name="target" value="_blank" /> _blank (new window) <br/>
<label for="status">Link Status</label><br/>
<select name="status">
<option value="1" selected="true">Live</option>
<option value="0">Hidden</option>
</select>
<input type="submit" name="submit" value="Add Link">
</form>
<br/><br/>

</div>
<br/><br/>
<table>
<caption>Current Links</caption>
<thead>
    <tr>
        <th scope="col"><span style="font-size:10px;">Title</span></th>
        <th scope="col"><span style="font-size:10px;">URL</span></th>
        <th scope="col"><span style="font-size:10px;">Target</span></th>
        <th scope="col"><span style="font-size:10px;">Status</span></th>
        <th scope="col"><span style="font-size:10px;">Edit</span></th>
        <th scope="col"><span style="font-size:10px;">Delete</span></th>
    </tr>
</thead>
<tfoot>
    <tr>
        <th scope="row">Total:</th>
        <td colspan="6" style="text-align:right;padding-right:30px;"><?php echo (count($this->links) == 1) ? '1 Link' : count($this->links) . ' Links'; ?></td>
    </tr>
</tfoot>
<?php
foreach ($this->links as $link) {
 if ($link['status'] == 1) {
    $status = 'Live';
 } else {
    $status = 'Hidden';
 }
 echo '<tr>';
 echo '<td style="text-align:left">'.$link['name'].'</td>';
 echo '<td style="text-align:left">'.$link['url'].'</td>';
 echo '<td>'.$link['target'].'</td>';
 echo '<td>'.$status.'</td>';
 echo '<td><a href="/admin/modules/edit/Links/edit/'.$link['id'].'">Edit</a></td>';
 echo '<td><a href="/admin/modules/edit/Links/delete/'.$link['id'].'">Delete</a></td>';
 echo '</tr>';
}
?>
</table>