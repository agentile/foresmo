<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->page_title; ?></title>
<style type="text/css" media="screen">@import url("/public/Foresmo/themes/main/blue_steal/styles/style.css");</style>
<style type="text/css" media="screen">@import url("/public/Foresmo/themes/main/blue_steal/styles/grid.css");</style>
</head>
<body>
    <div id="header" class="container_16">

        <div class="grid_16">

            <div id="branding" class="grid_5 alpha">
                <h1><a href="/"><?php echo $this->blog_title; ?></a></h1>
            </div>
    
            <div id="nav" class="grid_11 omega">
                <div class="underlinemenu">
                    <ul>
                        <li><a href="/">home</a></li>
                        <?php echo $this->page_tree; ?>
                    <ul>
                </div>
            </div>
        </div>
    </div>

    <div id="contentContainer" class="container_16">

        <div id="content" class="grid_16">
            
            <div class="grid_12 alpha">
                <?php echo $this->layout_content; ?>
            </div>

            <div id="content-right" class="grid_4 omega">
                <div id="module">
                    <?php

                        foreach($this->enabled_modules->all as $module) {
                            echo $module['output'];
                        }

                    ?>
                </div>
            </div>

        </div>
    
    </div>
    
   <div ic="footer" class="container_16"></div>

</body>
</html>
