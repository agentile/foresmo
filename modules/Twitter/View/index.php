<div class="module">
    <h3 class="module-title">Twitter</h3>
    <div id="module_twitter">
        <?php if (isset($this->tweets[0])): ?>
            <!--<p><a href="http://twitter.com/<?php echo $this->tweets[0]['username'] ?>"><img src="<?php echo $this->tweets[0]['img']?>" alt="<?php echo $this->tweets[0]['name']?>" title="<?php echo $this->tweets[0]['name']?>" width="48" height="48"></a></p>-->
        <?php endif ?>
        <ul class="tweets">
        <?php
        for ($i=0; $i<$this->count;$i++):
            $tweet = $this->tweets[$i];
            $dt = new DateTime("@{$tweet['ts']}");
            $dt->setTimezone(new DateTimeZone($this->timezone));
            $time = $dt->format($this->date_format);
        ?>
                <li class="tweet<?php if($i % 2 == 0): echo ' alt'; endif; ?>">
                    <p class="module_twitter_tweet"><?php echo $tweet['tweet']; ?></p>
                    <small class="module_twitter_timestamp date"><?php echo $time ?></small>
                </li>
        <?php
        endfor;
        ?>
        </ul>
    </div>
</div>