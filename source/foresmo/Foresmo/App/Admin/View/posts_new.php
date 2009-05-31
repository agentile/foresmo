<script type="text/javascript">
tinyMCE.init({
    mode : "exact",
    elements : "post_content",
    width : "700",
    height: "400",
    verify_html : false,
    apply_source_formatting : false,
    fix_nesting : false,
    fix_list_elements : false,
    fix_content_duplication : false,
    cleanup : false,
    cleanup_on_startup: false,
    trim_span_elements : false,
    skin: 'thebigreason',
    extended_valid_elements : "?php",
    doctype : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
    theme : 'advanced',
        plugins : 'safari,spellchecker,layer,save,advimage,advlink,inlinepopups,contextmenu,paste,noneditable,visualchars,nonbreaking,pagebreak,imagemanager',
        theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,|,link,unlink|,spellchecker,formatselect,|,image,charmap,|,outdent,indent,|,undo,redo,',
        theme_advanced_buttons2: '',
        theme_advanced_buttons3: '',
        theme_advanced_toolbar_location : 'top',
        theme_advanced_toolbar_align : 'left',
        theme_advanced_statusbar_location : 'bottom',
        theme_advanced_resize_horizontal : true,
        theme_advanced_resizing : true,
        apply_source_formatting : true,
        theme_advanced_source_editor_width : '700',
        spellchecker_languages : '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv'
});
</script>
        <div class="grid_16">
            <h2 class="page-heading"><i>Posts &#187; New</i></h2>
            <h2 class="page-heading-right"><i>Foresmo administration</i></h2>
        </div>
        <div class="grid_7">
            <form action="/ajax" method="post" id="post_new">
            <label for="post_title">Post Title</label><br/>
            <input type="text" name="post_title" class="input"/><br/><br/>
            <input type="hidden" name="ajax_action" value="admin_post_new" />
            <input type="textarea" id="post_content" name="post_content" /><br/>
            <input type="submit" id="post_new_submit" name="submit" value="Submit" class="submit_input"/>
            </form>
            <br/>
        </div>
        <div class="clear"></div>
