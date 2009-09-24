
        <div class="grid_16">
            <h2 class="page-heading">Pages &#187; Manage</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="grid_16">
            <table id="manage_pages" summary="Pages">
                <caption>Pages</caption>
                <thead>
                    <tr>
                        <th scope="col"><span style="font-size:10px;">Select</span></th>
                        <th scope="col"><span style="font-size:10px;">Title</span></th>
                        <th scope="col"><span style="font-size:10px;">Author</span></th>
                        <th scope="col"><span style="font-size:10px;">Status</span></th>
                        <th scope="col"><span style="font-size:10px;">Published</span></th>
                        <th scope="col"><span style="font-size:10px;">Modified</span></th>
                        <th scope="col"><span style="font-size:10px;">Actions</span></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="row">Total:</th>
                        <td colspan="6" style="text-align:right;padding-right:30px;"><?php echo (count($this->data) == 1) ? '1 Page' : count($this->data) . ' Pages'; ?></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach ($this->data as $key => $page_info) {
                        echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                        echo '<td></td>';
                        echo '<td><a href="/'.$page_info['slug'].'" target="_blank">' . $page_info['title'] . '</a></td>';
                        echo '<td>' . $page_info['users']['username'] . '</td>';
                        echo '<td>';
                        if ($page_info['status'] == 0) {
                            echo 'Hidden';
                        } elseif ($page_info['status'] == 1) {
                            echo 'Published';
                        } elseif ($page_info['status'] == 2) {
                            echo 'Draft';
                        }
                        echo'</td>';
                        echo '<td>' . $page_info['pubdate'] . '</td>';
                        echo '<td>' . $page_info['modified'] . '</td>';
                        echo '<td><a href="/'.$page_info['slug'].'" target="_blank">View</a> | <a href="/admin/pages/edit/'.$page_info['slug'].'">Edit</a> | <a href="/admin/pages/delete/'.$page_info['slug'].'">Delete</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="clear"></div>
