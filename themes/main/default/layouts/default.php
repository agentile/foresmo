<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $this->page_title; ?></title>
        <link rel="stylesheet" type="text/css" media="all" href="/public/Foresmo/themes/main/default/styles/reset.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="/public/Foresmo/themes/main/default/styles/grid.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="/public/Foresmo/themes/main/default/styles/screen.css" />
    </head>
    <body>
        <div id="container" class="container">
            <div id="wrap" class="span-24 last">
                <div id="header">
                    <ul id="nav-main" class="prepend-5 span-18 append-1 last">
                        <li class="section<?php if($this->action == 'main'): echo ' current'; endif; ?>"><a href="/">home</a></li>
                        <?php echo $this->page_tree; ?>
                    </ul>
                    <h1 id="site-title" class="span-4 append-20 last"><a href="/"><?php echo $this->blog_title; ?></a></h1>
                </div><?php //. #header ?>    
                <div id="content" class="prepend-4 span-20 last clear">
                    <div id="main-content" class="span-12 append-1 prepend-1">
                        <?php echo $this->layout_content; ?>
                    </div>
                    <div id="sidebar" class="span-6 last">
                    <?php
                    foreach($this->enabled_modules->all as $module):
                        echo $module['output'];
                    endforeach;
                    ?>
                    </div>
                </div><?php //. #content ?>
            </div><?php //. #wrap ?>
            <div id="footer">
            </div>
        </div><?php //. #container ?>
    </body>
</html>