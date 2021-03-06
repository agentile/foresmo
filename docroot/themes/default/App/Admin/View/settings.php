        <div class="grid_16">
            <h2 class="page-heading">Settings</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="clear"></div>
        <div id="admin_settings">
            <form action="/ajax" method="post" id="settings" style="display:inline;">
            <?php
            $blog_title = '';
            $blog_date_format = '';
            $blog_timezone = '';
            $blog_posts_per_page = 10;
            $blog_comment_link_limit = 3;
            foreach ($this->data as $option) {
                if ($option['name'] == 'blog_title') {
                    $blog_title = $option['value'];
                } elseif ($option['name'] == 'blog_date_format') {
                    $blog_date_format = $option['value'];
                } elseif ($option['name'] == 'blog_timezone') {
                    $blog_timezone = $option['value'];
                } elseif ($option['name'] == 'blog_posts_per_page') {
                    $blog_posts_per_page = $option['value'];
                } elseif ($option['name'] == 'blog_comment_link_limit') {
                    $blog_comment_link_limit = $option['value'];
                }
            }
            ?>
            <label for="blog_title">Blog Title</label><br/>
            <input type="text" name="blog_title" class="input" value="<?php echo $blog_title; ?>" style="width: 90%;"/><br/>
            <label for="blog_title">Blog Time Format</label><br/>
            <input type="text" name="blog_date_format" class="input" value="<?php echo $blog_date_format; ?>" style="width: 90%;"/><br/>
            <input type="hidden" name="ajax_action" value="admin_blog_settings" />
            <input type="submit" id="blog_settings_submit" name="submit" value="Submit" class="submit_input"/>

            <br/>
            <div id="message" class="message">
            </div>
            </form>
        </div>
        <div class="clear"></div>
        <script>
        window.addEvent('domready', function() {
            $('blog_settings_submit').addEvent('click', function(e) {
                e.stop();
                $('settings').set('send', {url: '/ajax', onComplete: function(response) {
                    var response = JSON.decode(response);
                    $('message').set('html', response.message);
                    if (response.success) {
                        window.location = '/admin/settings';
                    }
                }});
                $('settings').send();
            });
        });
        </script>
