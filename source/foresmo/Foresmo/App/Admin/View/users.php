        <div class="grid_16">
            <h2 class="page-heading">Users</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>

        <div class="grid_4">
            <div class="box menu">
                <h2>User Options</h2>
                <div class="block" id="section-menu">
                    <ul class="section menu">
                        <li>
                            <a href="#" class="menuitem">User Options</a>
                            <ul class="submenu">
                                <li>
                                    <a href="#">Add new user</a>
                                </li>
                                <li>
                                    <a href="#">Manage users</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="menuitem">Roles</a>
                            <ul class="submenu">
                                <li>
                                    <a href="#">Add role</a>
                                </li>
                                <li>
                                    <a href="#">Manage roles</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="grid_7">
            <div class="box articles">
                <h2>
                    Users
                </h2>
                <div class="block" id="comments">
                <table border='0' cellspacing='0' cellpadding='2' align='center'>
                        <thead>
                            <tr>
                                <th scope="col"><span style="font-size:10px;">Pic</span></th>
                                <th scope="col"><span style="font-size:10px;">Username</span></th>
                                <th scope="col"><span style="font-size:10px;">User group</span></th>
                            </tr>
                        </thead>

                    <?php
                        foreach ($this->users as $k => $user) {

                            $default = 'http://' . $_SERVER['SERVER_NAME'] . "/public/Foresmo/{$this->blog_theme}/images/contact_grey.png";
                            $size = 50;
                            $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($user['email'])."&default=".urlencode($default)."&size=".$size;
                            echo '<tr>
                                    <td><img src="'.$grav_url.'" alt="gravatar"/></td>
                                    <td>'.$user['username'].'</td>
                                    <td>'.$user['groups']['name'].'</td>
                                  </tr>';
                        }
                    ?>
                </table>
                </div>
            </div>
        </div>
        <div class="grid_5">
            <div class="box">
                <h2>User Stats</h2>
                <div class="block">
                <p>
                Total Users: <?php echo $this->quick_stats['total_users'];?>
                </p>
                </div>
            </div>
        </div>
        <div class="clear"></div>