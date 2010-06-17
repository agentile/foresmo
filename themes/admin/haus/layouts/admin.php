<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title><?php echo $this->blog_title; ?> &#187; admin</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="/public/Foresmo/themes/admin/haus/styles/reset.css" media="all" />
        <link rel="stylesheet" type="text/css" href="/public/Foresmo/themes/admin/haus/styles/brand.css" media="all" />
        <link rel="stylesheet" type="text/css" href="/public/Foresmo/themes/admin/haus/styles/grid.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/public/Foresmo/themes/admin/haus/styles/screen.css" media="screen" />
    </head>
    <body>
        <div id="wrap" class="container">
            <div id="header" class="prepend-3 append-3 span-18 last">
                <ul id="nav-main" class="navigation">
                    <li id="home" class="nav-section span-2">
                        <a href="/admin">Home</a>
                    </li>
                    <li id="pages" class="nav-section span-2">
                        <a href="/admin/pages">Pages</a>
                        <ul class="sub navigation hidden">
                            <li><a href="/admin/pages/new">Create New Page</a></li>
                            <li><a href="/admin/pages/manage">Manage Pages</a></li>
                        </ul>
                    </li>
                    <li id="posts" class="nav-section span-2">
                        <a href="/admin/posts">Posts</a>
                        <ul class="sub navigation hidden">
                            <li><a href="/admin/posts/new">Create New Post</a></li>
                            <li><a href="/admin/posts/manage">Manage Posts</a></li>
                        </ul>
                    </li>
                    <li id="comments" class="nav-section span-2">
                        <a href="/admin/comments">Comments</a>
                        <ul class="sub navigation hidden">
                            <li><a href="/admin/comments/spam">Spam</a></li>
                        </ul>
                    </li>
                    <li id="modules" class="nav-section span-2">
                        <a href="/admin/modules">Modules</a>
                        <ul class="sub navigation hidden">
                            <li><a href="/admin/modules/manage">Manage Modules</a></a></li>
                        </ul>
                    </li>
                    <li id="themes" class="nav-section span-2">
                        <a href="/admin/themes">Themes</a>
                        <ul class="sub navigation hidden">
                            <li><a href="/admin/themes/manage">Manage Themes</a></a></li>
                        </ul>
                    </li>
                    <li id="users" class="nav-section span-2">
                        <a href="#">Users</a>
                        <ul class="sub navigation hidden">
                            <li><a href="/admin/users/add">Add user</a></li>
                            <li><a href="/admin/users/manage">Manage users</a></li>
                            <li><a href="/admin/groups/add">Add user group</a></li>
                            <li><a href="/admin/groups/manage">Manage user groups</a></li>
                        </ul>
                    </li>
                    <li id="settings" class="nav-section span-2 last">
                        <a href="/admin/settings">Settings</a>
                    </li>
                </ul><?php // ENDS .navigation ?>
            </div>
            <!--
<div id="sidebar" class="span-6">
                
            </div><?php // ENDS #nav-main ?>-->
            <div id="content" class="prepend-3 append-3 span-18 last">
                <?php
                if(isset($this->message)):
                    echo '<p class="admin-msg">';
                    echo $this->message;
                    echo '</p>';
                endif;
                
                echo $this->layout_content; 
                ?>
            </div><?php // ENDS #content ?>
            <div id="footer">
            </div>
        </div><?php // ENDS #wrap ?>
    </body>
</html>
