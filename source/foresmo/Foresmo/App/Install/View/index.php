<div id="install">
	<h4>Blog Information</h4>
	<form method="post" id="blog_install" action="/ajax">
		<label for="blog_title">Blog Title</label><br/>
		<input type="text" name="blog_title" value="My Foresmo Blog"/><br/>
		<label for="blog_email">Your e-mail address</label><br/>
		<input type="text" name="blog_email" /><br/>
		<label for="blog_user">Username</label><br/>
		<input type="text" name="blog_user"/><br/>
		<label for="blog_password">Password</label><br/>
		<input type="password" name="blog_password"/><br/>
		<label for="blog_password2">Password Again</label><br/>
		<input type="password" name="blog_password2"/><br/>
		<br/>
		<h4>Database Information</h4>
		<label for="db_type">Database Type</label><br/>
		<select name="db_type">
			<option value="mysql" selected="true">MySQL</option>
		</select><br/>
		<label for="db_host">Database Hostname</label><br/>
		<input type="text" name="db_host" value="localhost"/><br/>
		<label for="db_username">Database Username</label><br/>
		<input type="text" name="db_username"/><br/>
		<label for="db_password">Database Password</label><br/>
		<input type="password" name="db_password"/><br/>
		<label for="db_name">Database Name</label><br/>
		<input type="text" name="db_name"/><br/>
		<label for="db_prefix">Database Prefix</label><br/>
		<input type="text" name="db_prefix" value="foresmo_"/><br/>
		<br/>
		<input type="hidden" name="ajax_action" value="blog_install" />
		<input type="submit" id="submit_button" name="install" alt="submit" title="submit" value="Submit" />
	</form>
	<div id="log">
	</div>
</div>
<script type="text/javascript">
window.addEvent('domready', function(){
	$('submit_button').addEvent('click', function(e) {
		e.stop();
		$('blog_install').set('send', {url: '/ajax', onComplete: function(response) {
			$('log').set('html', response);
		}});
		$('blog_install').send();
	});
});
</script>
