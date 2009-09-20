<div id="module">
<h2>Search</h2>

<?php
if ($this->search_adapter == 'Google') {
?>
<form action="/search/results" id="cse-search-box">
  <div>
    <input type="hidden" name="cx" value="<?php echo $this->search_adapter_settings['key']; ?>" />
    <input type="hidden" name="cof" value="FORID:10" />
    <input type="hidden" name="ie" value="UTF-8" />
    <input type="text" name="q" size="31" />
    <input type="submit" name="sa" value="Search" />
  </div>
</form>
<script type="text/javascript" src="http://www.google.com/cse/brand?form=cse-search-box&lang=en"></script>

<?php
} else {
?>
<form id="module-search" method="post" action="/module/search/">
    <input type="textbox" name="search-input" value=""/><br/>
    <input type="submit" id="module-search-submit" name="module-search-submit" value="Search"/>
</form>

<?php
}
?>
</div>