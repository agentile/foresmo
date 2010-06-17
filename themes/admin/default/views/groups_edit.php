
        <div class="grid_16">
            <h2 class="page-heading">Groups &#187; Edit</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="grid_16">
            <div id="group_users" style="float:left;width:25%;">
                <form action="/ajax" method="post" id="groups_user_edit">
                <table id="groups_user_edit" summary="Groups User Edit">
                    <caption>Edit Group Users</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="width:60px"><span style="font-size:10px;">Select</span></th>
                            <th scope="col" style="width:200px"><span style="font-size:10px;">Username</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($this->data['users'] as $key => $user) {
                            echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                            echo '<td><input type="checkbox" name="user[]" value="'.$user['id'].'"></td>';
                            echo '<td style="text-align:left;">' . $user['username'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <select name="action">
                    <option value="allow">Remove</option>
                </select>
                <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>">
                <input type="hidden" name="ajax_action" value="admin_group_user_edit" />
                <input type="submit" id="groups_user_edit_submit" name="submit" value="Update" class="submit_input"/>
                </form>
            </div>
            <div id="group_permissions" style="float:right;width:45%;">
                <form action="/ajax" method="post" id="groups_edit">
                <table id="groups_edit" summary="Groups Edit">
                    <caption>Edit Group Permissions</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="width:60px"><span style="font-size:10px;">Select</span></th>
                            <th scope="col" style="width:200px"><span style="font-size:10px;">Name</span></th>
                            <th scope="col" style="width:140px"><span style="font-size:10px;">Status</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($this->data['permissions'] as $key => $permission) {
                            echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                            echo '<td><input type="checkbox" name="permissions[]" value="'.$permission['id'].'"></td>';
                            echo '<td style="text-align:left;">' . $permission['name'] . '</td>';
                            echo '<td style="text-align:left;"></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <select name="action">
                    <option value="allow">Allow</option>
                    <option value="deny">Deny</option>
                </select>
                <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>">
                <input type="hidden" name="ajax_action" value="admin_group_edit" />
                <input type="submit" id="groups_edit_submit" name="submit" value="Update" class="submit_input"/>
                </form>
            </div>
            <br/><br/>
            <div id="message"></div>
            <br/>
        </div>
        <div class="clear"></div>
        <script>
        window.addEvent('domready', function() {
            $('groups_edit_submit').addEvent('click', function(e) {
                e.stop();
                $('groups_edit').set('send', {url: '/ajax', onComplete: function(response) {
                    var response = JSON.decode(response);
                    $('message').set('html', response.message);
                    if (response.success) {
                        window.location = '/admin/groups/manage';
                    }
                }});
                $('groups_edit').send();
            });
        });
        </script>
