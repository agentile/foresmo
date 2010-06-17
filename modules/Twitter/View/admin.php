<div style="margin:0 auto;border:1px solid #cecece;padding:5px;width:700px;">
<h2>Twitter Module Settings</h2>
<form method="post" action="/admin/modules/edit/Twitter">
<label for="twitter_name">Twitter username (e.g. @foresmo) </label><br/>
<input type="text" name="twitter_name" value="<?php echo $this->twitter_name ?>"/><br/>
<label for="twitter_stream_count">How many tweets to show </label><br/>
<input type="text" name="twitter_stream_count" value="<?php echo $this->twitter_stream_count ?>"/><br/>
<label for="twitter_stream_frequency">How often to check for new tweets (in hours) </label><br/>
<input type="text" name="twitter_stream_frequency" value="<?php echo $this->twitter_stream_frequency ?>"/><br/>

<input type="submit" name="submit" value="Save Settings">
</form>
<br/><br/>

</div>
<br/><br/>