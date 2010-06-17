<div style="margin:0 auto;border:1px solid #cecece;padding:5px;width:700px;">
<h2>Calendar Module Settings</h2>
<form method="post" action="/admin/modules/edit/Calendar">
<label for="sow">Start of week</label><br/>
<select name="sow">
<option value="0" <?php echo ($this->sow == 0) ? 'selected="true"': ''?>>Sunday</option>
<option value="1" <?php echo ($this->sow == 1) ? 'selected="true"': ''?>>Monday</option>
<option value="2" <?php echo ($this->sow == 2) ? 'selected="true"': ''?>>Tuesday</option>
<option value="3" <?php echo ($this->sow == 3) ? 'selected="true"': ''?>>Wednesday</option>
<option value="4" <?php echo ($this->sow == 4) ? 'selected="true"': ''?>>Thursday</option>
<option value="5" <?php echo ($this->sow == 5) ? 'selected="true"': ''?>>Friday</option>
<option value="6" <?php echo ($this->sow == 6) ? 'selected="true"': ''?>>Saturday</option>
</select>
<input type="submit" name="submit" value="Save Settings">
</form>
<br/><br/>

</div>
<br/><br/>