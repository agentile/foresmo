<div id="module">
<h2>Calendar</h2>

<table id="module_calendar">
    <caption><?php echo $this->calendar['month_text'] . ' ' . $this->calendar['year'];?></caption>
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

    <tfoot>
    <tr>
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
            echo '<td abbr="'.$this->months_of_year[$prev]['short'].'" colspan="3" id="prev"><a href="/module/calendar/'.$prev.'/'.$yearp.'" title="View posts for '.$this->months_of_year[$prev]['full'].' '.$yearp.'">&laquo; '.$this->months_of_year[$prev]['short'].'</a></td>';
            echo '<td class="pad">&nbsp;</td>';
            echo '<td abbr="'.$this->months_of_year[$next]['short'].'" colspan="3" id="next"><a href="/module/calendar/'.$next.'/'.$yearn.'" title="View posts for '.$this->months_of_year[$next]['full'].' '.$yearn.'">'.$this->months_of_year[$next]['short'].' &raquo;</a></td>';
        ?>
    </tr>
    </tfoot>


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
                echo '<tr>';
            }
            if ($i == $this->calendar['today']) {
                $str = ' class="today"';
            } else {
                $str = '';
            }
            if (isset($calendar_posts[$this->calendar['month']][$i][$this->calendar['year']])) {
                $day = '<a href="/sort/'.$this->calendar['month'].'/'.$i.'/'.$this->calendar['year'].'" alt="'.$calendar_posts[$this->calendar['month']][$i][$this->calendar['year']].' Posts">' . $i . '</a>';
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

</div>