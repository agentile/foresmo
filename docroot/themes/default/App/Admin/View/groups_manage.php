
        <div class="grid_16">
            <h2 class="page-heading">User Groups &#187; Manage</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="grid_16">
            <table id="manage_groups" summary="User Groups">
                <caption>User Groups</caption>
                <thead>
                    <tr>
                        <th scope="col"><span style="font-size:10px;">Select</span></th>
                        <th scope="col"><span style="font-size:10px;">Name</span></th>
                        <th scope="col"><span style="font-size:10px;"># Users</span></th>
                        <th scope="col"><span style="font-size:10px;">Actions</span></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="row">Total:</th>
                        <td colspan="3" style="text-align:right;padding-right:30px;"><?php echo (count($this->data) == 1) ? '1 Group' : count($this->data) . ' Groups'; ?></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach ($this->data as $key => $group) {
                        echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                        echo '<td></td>';
                        echo '<td>' . $group['name'] . '</td>';
                        echo '<td>' . count($group['users']) . '</td>';
                        echo '<td><a href="/admin/groups/edit/'.$group['name'].'">Edit</a> | <a href="/admin/groups/delete/'.$group['name'].'">Delete</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="clear"></div>
