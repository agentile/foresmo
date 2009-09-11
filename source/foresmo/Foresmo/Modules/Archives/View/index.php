<div id="module">
<h2>Archives</h2>

<?php
foreach($this->archive as $year => $months) {
    foreach ($months as $month => $posts_count) {
        echo '<a href="/sort/' . $month . '/' . $year . '" alt="' . $posts_count . ' posts">';
        echo $this->months_of_year[$month]['full'] . ', ' . $year . ' (' . $posts_count . ')</a><br/>';
    }
}

?>
</div>