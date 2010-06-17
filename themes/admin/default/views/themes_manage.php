
        <div class="grid_16">
            <h2 class="page-heading">Themes &#187; Manage</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="grid_16">
            <form action="/admin/themes/manage" method="post" id="themes_manage">
            <table id="manage_themes" summary="Themes">
                <caption>Blog Themes</caption>
                <thead>
                    <tr>
                        <th scope="col" style="width:60px"><span style="font-size:10px;">Select</span></th>
                        <th scope="col" style="width:200px"><span style="font-size:10px;">Name</span></th>
                        <th scope="col"><span style="font-size:10px;">Description</span></th>
                        <th scope="col"><span style="font-size:10px;">Author</span></th>
                        <th scope="col"><span style="font-size:10px;">Version</span></th>
                        <th scope="col" style="width:140px"><span style="font-size:10px;">Preview</span></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="row">Total:</th>
                        <td colspan="6" style="text-align:right;padding-right:30px;"><?php echo ($this->blog_theme_count == 1) ? '1 Blog Theme' : $this->blog_theme_count . ' Blog Themes'; ?></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach ($this->data as $key => $theme_info) {
                        if (!in_array('main', $theme_info['type'])) {
                            continue;
                        }
                        $preview = '';
                        if ($theme_info['preview'] != '') {
                            $preview = '<a href="'.$theme_info['preview'].'" target="_blank">Preview</a>';
                        }
                        if ($theme_info['folder'] == $this->blog_theme) {
                            echo '<tr class="highlight">';
                        } else {
                            echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                        }
                        echo '<td><input type="radio" name="theme" value="'.$theme_info['folder'].'"></td>';
                        echo '<td style="text-align:left;"><a href="/admin/modules/edit/'.$theme_info['folder'].'">' . $theme_info['name'] . '</a></td>';
                        echo '<td style="text-align:left;">' . $theme_info['description'] . '</td>';
                        echo '<td style="text-align:center;">' . $theme_info['author'] . '</td>';
                        echo '<td style="text-align:center;">' . $theme_info['version'] . '</td>';
                        echo '<td>' . $preview . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <select name="action">
                <option value="enable">Enable</option>
                <option value="disable">Disabled</option>
                <option value="install">Install</option>
                <option value="uninstall">Uninstall</option>
            </select>
            <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>">
            <input type="hidden" name="ajax_action" value="admin_theme_update" />
            <input type="submit" id="theme_submit" name="submit" value="Update" class="submit_input"/>
            </form><br/><br/>
            <div id="theme_message"></div>
            <br/>
            <form action="/ajax" method="post" id="themes_admin_manage">
            <table id="manage_admin_themes" summary="Admin Themes">
                <caption>Admin Themes</caption>
                <thead>
                    <tr>
                        <th scope="col" style="width:60px"><span style="font-size:10px;">Select</span></th>
                        <th scope="col" style="width:200px"><span style="font-size:10px;">Name</span></th>
                        <th scope="col"><span style="font-size:10px;">Description</span></th>
                        <th scope="col"><span style="font-size:10px;">Author</span></th>
                        <th scope="col"><span style="font-size:10px;">Version</span></th>
                        <th scope="col" style="width:140px"><span style="font-size:10px;">Preview</span></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="row">Total:</th>
                        <td colspan="6" style="text-align:right;padding-right:30px;"><?php echo ($this->admin_theme_count == 1) ? '1 Admin Theme' : $this->admin_theme_count . ' Admin Themes'; ?></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach ($this->data as $key => $theme_info) {
                        if (!in_array('admin', $theme_info['type'])) {
                            continue;
                        }
                        $preview = '';
                        if ($theme_info['preview'] != '') {
                            $preview = '<a href="'.$theme_info['preview'].'" target="_blank">Preview</a>';
                        }
                        if ($theme_info['folder'] == $this->blog_admin_theme) {
                            echo '<tr class="highlight">';
                        } else {
                            echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                        }
                        echo '<td><input type="radio" name="theme" value="'.$theme_info['folder'].'"></td>';
                        echo '<td style="text-align:left;"><a href="/admin/modules/edit/'.$theme_info['folder'].'">' . $theme_info['name'] . '</a></td>';
                        echo '<td style="text-align:left;">' . $theme_info['description'] . '</td>';
                        echo '<td style="text-align:center;">' . $theme_info['author'] . '</td>';
                        echo '<td style="text-align:center;">' . $theme_info['version'] . '</td>';
                        echo '<td>' . $preview . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <select name="action">
                <option value="enable">Enable</option>
                <option value="disable">Disabled</option>
                <option value="install">Install</option>
                <option value="uninstall">Uninstall</option>
            </select>
            <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>">
            <input type="hidden" name="ajax_action" value="admin_theme_admin_update" />
            <input type="submit" id="theme_admin_submit" name="submit" value="Update" class="submit_input"/>
            </form><br/><br/>
            <div id="theme_admin_message"></div>
            <br/>
        </div>
        <div class="clear"></div>
        <script>
        window.addEvent('domready', function() {
            $('theme_submit').addEvent('click', function(e) {
                e.stop();
                $('themes_manage').set('send', {url: '/ajax', onComplete: function(response) {
                    var response = JSON.decode(response);
                    $('theme_message').set('html', response.message);
                    if (response.success) {
                        window.location = '/admin/themes/manage';
                    }
                }});
                $('themes_manage').send();
            });
            $('theme_admin_submit').addEvent('click', function(e) {
                e.stop();
                $('themes_admin_manage').set('send', {url: '/ajax', onComplete: function(response) {
                    var response = JSON.decode(response);
                    $('theme_admin_message').set('html', response.message);
                    if (response.success) {
                        window.location = '/admin/themes/manage';
                    }
                }});
                $('themes_admin_manage').send();
            });
        });
        </script>
