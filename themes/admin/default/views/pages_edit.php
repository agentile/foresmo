
        <?php
            $this->tinymce()->init(array(
                'post_content' => array(),
                'post_excerpt' => array('height' => 200)
                )
            );
        ?>
        <script>
        window.addEvent('domready', function() {
            $('post_edit_submit').addEvent('click', function(e) {
                e.stop();
                fixTiny({instance:'post_content'});
                $('post_edit').set('send', {url: '/ajax', onComplete: function(response) {
                    var response = JSON.decode(response);
                    $('message').set('html', response.message);
                    if (response.success) {
                        window.location = '/admin/pages/manage';
                    }
                }});
                $('post_edit').send();
            });
        });
        </script>
        <?php
        $tags = '';
        $comments_disabled = false;
        foreach ($this->data['postinfo'] as $key => $info) {
            if ($info['name'] == 'comments_disabled' && $info['value'] == '1') {
                $comments_disabled = true;
            }
        }
        foreach ($this->data['tags'] as $key => $tag) {
            $tags .= $tag['tag'] . ', ';
        }
        $tags = rtrim($tags, ', ');
        ?>
        <div class="grid_16">
            <h2 class="page-heading">Pages &#187; Edit</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <form action="/ajax" method="post" id="post_edit" style="display:inline;">
        <div class="grid_4">
        <br/>
            <div class="box">
                <h2>Page Options</h2>
                <div class="block">

                    <label for="post_status">Status</label><br/>
                    <select name="post_status">
                        <option value="1"<?php echo ($this->data['status'] == 1) ? ' selected="selected"': '';?>>Publish</option>
                        <option value="2"<?php echo ($this->data['status'] == 2) ? ' selected="selected"': '';?>>Save as draft</option>
                        <option value="0"<?php echo ($this->data['status'] == 0) ? ' selected="selected"': '';?>>Hidden</option>
                    </select><br/><br/>
                    <label for="post_tags">Tags</label><br/>
                    <input type="text" name="post_tags" class="input" style="width: 90%;" value="<?php echo $tags;?>"/><br/>
                    <span class="byline">Seperated by commas.</span>
                    <br/><br/>
                    <label for="post_comments_disabled">Disable Comments</label>
                    <input type="checkbox" name="post_comments_disabled" value="true"<?php echo ($comments_disabled) ? ' checked="checked"' : '';?>/>
                </div>
            </div>
        </div>
        <div class="grid_7">
            <label for="post_title">Page Title</label><br/>
            <input type="text" name="post_title" class="input" style="width: 694px;" value="<?php echo $this->data['title'];?>"/><br/><br/>
            <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>">
            <input type="hidden" name="ajax_action" value="admin_page_edit" />
            <input type="hidden" name="id" value="<?php echo $this->data['id']; ?>" />
            <input type="hidden" name="post_type" value="2" />
            <textarea id="post_content" name="post_content"><?php echo $this->data['content'];?></textarea><br/>
            <label for="post_excerpt">Page Excerpt</label><br/>
            <textarea id="post_excerpt" name="post_excerpt"><?php echo $this->data['excerpt'];?></textarea><br/>
            <input type="submit" id="post_edit_submit" name="submit" value="Submit" class="submit_input"/>

            <br/>
            <div id="message" class="message">
            </div>
        </div>
        </form>
        <div class="clear"></div>
