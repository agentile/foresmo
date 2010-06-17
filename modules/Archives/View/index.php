<div class="module">
    <h3 class="module-title">Archives</h3>
    <ul class="archives">
    <?php
    foreach($this->archive as $year => $months):
        foreach ($months as $month => $posts_count):
            if($posts_count !== 0):
    ?>
                <li class="archive"><a href="/sort/<?php echo $month . '/' . $year; ?>" alt="<?php echo $posts_count . ' posts'; ?>"><?php echo $this->months_of_year[$month]['full'] . ', ' . $year . ' (' . $posts_count . ')'; ?></a></li>
    <?php
            endif;
        endforeach;
    endforeach;
    ?>
    </ul>
</div>
