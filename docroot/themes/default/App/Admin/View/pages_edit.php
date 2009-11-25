
        <script type="text/javascript">
        tinyMCE.init({
            mode : "exact",
            elements : "post_content",
            width : "700",
            height: "400",
            verify_html : false,
            apply_source_formatting : false,
            fix_nesting : false,
            fix_list_elements : false,
            fix_content_duplication : false,
            cleanup : false,
            cleanup_on_startup: false,
            trim_span_elements : false,
            skin: 'thebigreason',
            extended_valid_elements : "?php",
            doctype : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            theme : 'advanced',
                plugins : 'safari,spellchecker,layer,save,advimage,advlink,inlinepopups,contextmenu,paste,noneditable,visualchars,nonbreaking,pagebreak',
                theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,|,link,unlink|,spellchecker,formatselect,|,image,charmap,|,outdent,indent,|,undo,redo,',
                theme_advanced_buttons2: '',
                theme_advanced_buttons3: '',
                theme_advanced_toolbar_location : 'top',
                theme_advanced_toolbar_align : 'left',
                theme_advanced_statusbar_location : 'bottom',
                theme_advanced_resize_horizontal : true,
                theme_advanced_resizing : true,
                apply_source_formatting : true,
                theme_advanced_source_editor_width : '700',
                spellchecker_languages : '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv'
        });
        </script>
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
        foreach ($this->data[0]['postinfo'] as $key => $info) {
            if ($info['name'] == 'comments_disabled' && $info['value'] == '1') {
                $comments_disabled = true;
            }
        }
        foreach ($this->data[0]['tags'] as $key => $tag) {
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
                        <option value="1"<?php echo ($this->data[0]['status'] == 1) ? ' selected="selected"': '';?>>Publish</option>
                        <option value="2"<?php echo ($this->data[0]['status'] == 2) ? ' selected="selected"': '';?>>Save as draft</option>
                        <option value="0"<?php echo ($this->data[0]['status'] == 0) ? ' selected="selected"': '';?>>Hidden</option>
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
            <input type="text" name="post_title" class="input" style="width: 694px;" value="<?php echo $this->data[0]['title'];?>"/><br/><br/>
            <input type="hidden" name="ajax_action" value="admin_page_edit" />
            <input type="hidden" name="id" value="<?php echo $this->data[0]['id']; ?>" />
            <input type="hidden" name="post_type" value="2" />
            <input type="textarea" id="post_content" name="post_content" value='<?php echo $this->data[0]['content'];?>'/><br/>
            <input type="submit" id="post_edit_submit" name="submit" value="Submit" class="submit_input"/>

            <br/>
            <div id="message" class="message">
            </div>
        </div>
        </form>
        <div class="clear"></div>
