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
            $year = $this->calendar['year'] - 1;
        } elseif ($this->calendar['month'] == 12) {
            $year = $this->calendar['year'] + 1;
        } else {
            $year = $this->calendar['year'];
        }
            echo '<td abbr="'.$this->months_of_year[$prev]['short'].'" colspan="3" id="prev"><a href="/#" title="View posts for '.$this->months_of_year[$prev]['full'].' '.$year.'">&laquo; '.$this->months_of_year[$prev]['short'].'</a></td>';
            echo '<td class="pad">&nbsp;</td>';
            echo '<td abbr="'.$this->months_of_year[$next]['short'].'" colspan="3" id="next"><a href="/#" title="View posts for '.$this->months_of_year[$next]['full'].' '.$year.'">'.$this->months_of_year[$next]['short'].' &raquo;</a></td>';
        ?>
    </tr>
    </tfoot>


    <tbody>
    <?php
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
            if ($i == 1) {
                $pad =  abs($this->start_day - $this->calendar['first_day']);
                $col_count += $pad;
                if ($pad > 0) {
                    echo '<td colspan="' . $pad . '" class="pad">&nbsp;</td>';
                }
                echo '<td' . $str . '>' . $i . '</td>';
            } elseif ($i == $this->calendar['last_day']) {
                echo '<td' . $str . '>' . $i . '</td>';
                $pad = 6 - $col_count;
                if ($pad > 0) {
                    echo '<td colspan="' . $pad . '" class="pad">&nbsp;</td>';
                }
                $col_count = 6;
            } else {
                echo '<td' . $str . '>' . $i . '</td>';
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