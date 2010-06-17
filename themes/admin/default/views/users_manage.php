
        <div class="grid_16">
            <h2 class="page-heading">Users &#187; Manage</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="grid_16">
            <table id="manage_users" summary="Users">
                <caption>Users</caption>
                <thead>
                    <tr>
                        <th scope="col"><span style="font-size:10px;">Action</span></th>
                        <th scope="col"><span style="font-size:10px;">Picture</span></th>
                        <th scope="col"><span style="font-size:10px;">Username</span></th>
                        <th scope="col"><span style="font-size:10px;">Name</span></th>
                        <th scope="col"><span style="font-size:10px;">E-mail</span></th>
                        <th scope="col"><span style="font-size:10px;">Group</span></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="row">Total:</th>
                        <td colspan="5" style="text-align:right;padding-right:30px;"><?php echo (count($this->data) == 1) ? '1 User' : count($this->data) . ' Users'; ?></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach ($this->data as $key => $user) {
                        echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                        echo '<td><a href="/admin/users/profile/'.$user['username'].'">View Profile</a></td>';
                        $default = 'http://' . $_SERVER['SERVER_NAME'] . "/themes/{$this->blog_admin_theme}/assets/images/contact_grey.png";
                        $size = 30;
                        $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($user['email'])."&default=".urlencode($default)."&size=".$size;
                        echo '<td><img src="'.$grav_url.'" alt="gravatar"/></td>';
                        echo '<td>' . $user['username'] . '</td>';
                        echo '<td>';
                        echo (isset($user['userinfo']['fullname'])) ? $user['userinfo']['fullname'] : 'Not given';
                        echo '</td>';
                        echo '<td>' . $user['email'] . '</td>';
                        echo '<td>' . $user['groups']['name'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="clear"></div>
