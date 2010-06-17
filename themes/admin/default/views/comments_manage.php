        <div class="grid_16">
            <h2 class="page-heading">Comments &#187; Manage</h2>
            <h2 class="page-heading-right"><?php echo $this->blog_title;?> administration</h2>
        </div>
        <div class="grid_16">
            <form action="/admin/comments/manage" method="post" id="comments_manage">
            <table id="manage_comments" summary="Comments">
                <caption>Comments</caption>
                <thead>
                    <tr>
                        <th scope="col"><span style="font-size:10px;">Select</span></th>
                        <th scope="col"><span style="font-size:10px;">Author</span></th>
                        <th scope="col"><span style="font-size:10px;">Email</span></th>
                        <th scope="col"><span style="font-size:10px;">URL</span></th>
                        <th scope="col"><span style="font-size:10px;">Comment</span></th>
                        <th scope="col"><span style="font-size:10px;">Post</span></th>
                        <th scope="col"><span style="font-size:10px;">IP</span></th>
                        <th scope="col"><span style="font-size:10px;">Time</span></th>
                        <th scope="col"><span style="font-size:10px;">Status</span></th>
                        <th scope="col"><span style="font-size:10px;">Type</span></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="row">Total:</th>
                        <td colspan="9" style="text-align:right;padding-right:30px;"><?php echo (count($this->comments) == 1) ? '1 Comment' : count($this->comments) . ' Comments'; ?></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach ($this->comments as $key => $comment) {
                        switch ($comment['status']) {
                            case 0:
                                $status = 'Hidden/Disapproved';
                            break;
                            case 1:
                                $status = 'Visible/Approved';
                            break;
                            case 2:
                                $status = 'Spam';
                            break;
                            case 3:
                                $status = 'Under Moderation';
                            break;
                        }
                        switch ($comment['type']) {
                            case 0:
                                $type = 'Normal';
                            break;
                            case 1:
                                $type = 'Admin';
                            break;
                            case 2:
                                $type = 'Trackback';
                            break;
                        }
                        echo ($key % 2 == 0) ? '<tr>' : '<tr class="alt">';
                        echo '<td><input type="checkbox" name="comments[]" value="'.$comment['id'].'"></td>';
                        echo '<td style="text-align:left">' . $comment['name'] . '</td>';
                        echo '<td style="text-align:left"><a href="mailto:'.$comment['email'].'">' . $comment['email'] . '</a></td>';
                        echo '<td style="text-align:left"><a href="'.$comment['url'].'" target="_blank">' . $comment['url'] . '</a></td>';
                        echo '<td style="text-align:left">' . $comment['content'] . '</td>';
                        echo '<td style="text-align:left"><a href="/'.$comment['post']['slug'].'" target="_blank">'.$comment['post']['title'].'</a></td>';
                        echo '<td style="text-align:left">' . long2ip($comment['ip']) . '</td>';
                        echo '<td style="text-align:left">' . $comment['date'] . '</td>';
                        echo '<td>' . $status . '</td>';
                        echo '<td>' . $type . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <select name="action">
                <option value="approve">Approve</option>
                <option value="disapprove">Disapprove</option>
                <option value="spam">Mark as spam</option>
                <option value="moderation">Mark as under moderation</option>
            </select>
            <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>">
            <input type="hidden" name="ajax_action" value="admin_comments_manage" />
            <input type="submit" id="comments_manage_submit" name="submit" value="Submit" class="submit_input"/>
            </form><br/>
        </div>
        <div class="clear"></div>