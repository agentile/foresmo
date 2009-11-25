
        <div class="grid_16">
            <h2 class="page-heading">Modules &#187; Manage</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="grid_16">
            <form action="/ajax" method="post" id="modules_manage">
            <table id="manage_pages" summary="Pages">
                <caption>Modules</caption>
                <thead>
                    <tr>
                        <th scope="col" style="width:60px"><span style="font-size:10px;">Select</span></th>
                        <th scope="col" style="width:200px"><span style="font-size:10px;">Name</span></th>
                        <th scope="col"><span style="font-size:10px;">Description</span></th>
                        <th scope="col" style="width:140px"><span style="font-size:10px;">Status</span></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="row">Total:</th>
                        <td colspan="4" style="text-align:right;padding-right:30px;"><?php echo (count($this->data) == 1) ? '1 Module' : count($this->data) . ' Modules'; ?></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach ($this->data as $key => $module_info) {
                        if ($module_info['status'] == '2') {
                            $status = 'Not Installed';
                        } elseif ($module_info['status'] == '1') {
                            $status = 'Enabled';
                        } else {
                            $status = 'Disabled';
                        }
                        echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                        echo '<td><input type="checkbox" name="modules[]" value="'.$module_info['id'].'"></td>';
                        echo '<td style="text-align:left;"><a href="/admin/modules/edit/'.$module_info['name'].'" target="_blank">' . $module_info['name'] . '</a></td>';
                        echo '<td style="text-align:left;">' . $module_info['description'] . '</td>';
                        echo '<td>' . $status . '</td>';
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
            <input type="hidden" name="ajax_action" value="admin_modules_change_status" />
            <input type="submit" id="modules_manage_submit" name="submit" value="Update" class="submit_input"/>
            </form><br/><br/>
            <div id="message"></div>
            <br/>
        </div>
        <div class="clear"></div>
        <script>
        window.addEvent('domready', function() {
            $('modules_manage_submit').addEvent('click', function(e) {
                e.stop();
                $('modules_manage').set('send', {url: '/ajax', onComplete: function(response) {
                    var response = JSON.decode(response);
                    $('message').set('html', response.message);
                    if (response.success) {
                        window.location = '/admin/modules/manage';
                    }
                }});
                $('modules_manage').send();
            });
        });
        </script>
