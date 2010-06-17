// MOOTOOLS UI ELEMENTS

// SECTION MENU
window.addEvent('domready', function() {

  var accordion = new Accordion('a.menuitem', 'ul.submenu', {
    opacity: false,
    onActive: function(toggler, element){
      toggler.addClass('current');
      element.addClass('current');
    },
    onBackground: function(toggler, element){
      toggler.removeClass('current');
      element.removeClass('current');
    }
  }, $('section-menu'));

});

// TinyMCE content ajax fix
var fixTiny = function(properties) {
    var properties = properties || new Object();
    var instance = properties.instance || 'mce_editor_0';
    tinyMCE.execInstanceCommand(instance,'mceCleanup');
    tinyMCE.triggerSave(true,true);
    return true;
}
