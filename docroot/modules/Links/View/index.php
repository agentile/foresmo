<div id="module">
<h2>Links</h2>

<?php
foreach ($this->links as $link) {
    echo '<a href="' . $link['url'] . '" alt="' . $link['name'] . '" target="' . $link['target'] . '">' . $link['name'] . '</a><br/>';
}
?>

</div>