<ul id="index-counters">
    <?php 
    for($i = 0; $i < 3; $i++):
    ?>
    <li class="index-counter clear">
        <div class="span-3">
            <h3 class="counter">5</h3>
        </div>
        <div class="span-9 append-6 last">
            <h2>Comments</h2>
            <ul>
                <li class="status approved">Approved &#8594; <span class="count">3</span></li>
                <li class="status moderation">Awaiting moderation &#8594; <span class="count">1</span></li>
                <li class="status spam">Marked as spam &#8594; <span class="count">1</span></li>
            </ul>
        </div>
    </li>
    <?php 
    endfor;
    ?>
</ul>