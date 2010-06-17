<div class="module">
<h3 class="module-title">Calendar</h3>
<div class="module-calendar-container">
    <div class="caption clear">
        <?php
        $prev = ($this->calendar['month'] == 1) ? 12 : $this->calendar['month'] - 1;
        $next = ($this->calendar['month'] == 12) ? 1 : $this->calendar['month'] + 1;
        if ($this->calendar['month'] == 1) {
            $yearp = $this->calendar['year'] - 1;
            $yearn = $this->calendar['year'];
        } elseif ($this->calendar['month'] == 12) {
            $yearn = $this->calendar['year'] + 1;
            $yearp = $this->calendar['year'];
        } else {
            $yearp = $this->calendar['year'];
            $yearn = $this->calendar['year'];
        }
        echo '<span class="prev"><a href="/module/calendar/'.$prev.'/'.$yearp.'" title="View posts for '.$this->months_of_year[$prev]['full'].' '.$yearp.'">&laquo; '.$this->months_of_year[$prev]['short'].'</a></span>';
        echo '<p class="title">' . $this->calendar['month_text'] . ' ' . $this->calendar['year'] . '</p>';
        echo '<span class="next"><a href="/module/calendar/'.$next.'/'.$yearn.'" title="View posts for '.$this->months_of_year[$next]['full'].' '.$yearn.'">'.$this->months_of_year[$next]['short'].' &raquo;</a></span';
        ?>
    </div>
    <table class="module-calendar">
        <thead>
            <tr>    
            <?php
                $size = count($this->days_of_week);
                for ($i = 0; $i < $size; $i++) {
                    // modulous operation for circular queue
                    $pos = ($this->start_day + $i) % $size;
                    echo '<th abbr="' . $this->days_of_week[$pos]['full'] .
                         '" scope="col" title="' . $this->days_of_week[$pos]['full'] .
                         '">' . $this->days_of_week[$pos]['short'] . '</th>';
                }
            ?>
            </tr>
        </thead>
        <tbody>
        <?php
            $calendar_posts = array();
            foreach ($this->posts as $post) {
                if (isset($post['pubdate_ts'])) {
                    $date = explode('/', date('n/j/Y', $post['pubdate_ts']));
                    $m = $date[0];
                    $d = $date[1];
                    $y = $date[2];
                    if (isset($calendar_posts[$m][$d][$y])) {
                        $calendar_posts[$m][$d][$y] += 1;
                    } else {
                        $calendar_posts[$m][$d][$y] = 1;
                    }
                }
            }
    
            $col_count = 0;
            for ($i = 1; $i <= $this->calendar['last_day']; $i++) {
                if ($col_count == 0) {
                    if($i % 2 == 0){
                        echo '<tr class="even">';
                    }
                    else{
                        echo '<tr class="odd">';
                    }
                }
                if ($i == $this->calendar['today'] && date('Y') == $this->calendar['year'] && date('n') == $this->calendar['month']) {
                    $str = ' class="today"';
                } else {
                    $str = '';
                }
                if (isset($calendar_posts[$this->calendar['month']][$i][$this->calendar['year']])) {
                    $day = '<a href="/sort/'.$this->calendar['month'].'/'.$i.'/'.$this->calendar['year'].'" alt="'.$calendar_posts[$this->calendar['month']][$i][$this->calendar['year']].' Posts" title="'.$calendar_posts[$this->calendar['month']][$i][$this->calendar['year']].' Posts">' . $i . '</a>';
                } else {
                    $day = $i;
                }
                if ($i == 1) {
                    if ($this->start_day != 0) {
                        if ($this->calendar['first_day'] != 0) {
                            $pad = $this->calendar['first_day'] - $this->start_day;
                        } else {
                            $pad = 7 - (abs(0 - $this->start_day));
                        }
                    } else {
                        $pad = $this->calendar['first_day'] - $this->start_day;
                    }
                    $col_count += $pad;
                    if ($pad > 0) {
                        echo '<td colspan="' . $pad . '" class="pad">&nbsp;</td>';
                    }
                    echo '<td' . $str . '>' . $day . '</td>';
                } elseif ($i == $this->calendar['last_day']) {
                    echo '<td' . $str . '>' . $day . '</td>';
                    $pad = 6 - $col_count;
                    if ($pad > 0) {
                        echo '<td colspan="' . $pad . '" class="pad">&nbsp;</td>';
                    }
                    $col_count = 6;
                } else {
                    echo '<td' . $str . '>' . $day . '</td>';
                }
                ++$col_count;
                if ($col_count == 7) {
                    echo '</tr>';
                    $col_count = 0;
                }
            }
        ?>
    
        </tbody>
        </table>
    </div><?php //. .module-calendar-container ?>
</div>