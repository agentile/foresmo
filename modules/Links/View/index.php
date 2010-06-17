<div class="module">
    <h3 class="module-title">Links</h3>
    <ul class="links">
    <?php
    foreach ($this->links as $link):
    ?>
        <li class="link"><a href="<?php echo $link['url']; ?>" alt="<?php echo $link['name']; ?>" target="<?php echo $link['target']; ?>"><?php echo $link['name']; ?></a></li>
    <?php 
    endforeach;
    ?>
    </ul>
</div>