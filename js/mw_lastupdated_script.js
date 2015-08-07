(function(document) {
  tinymce.create('tinymce.plugins.mw_detect_significant_update', {
    init : function(ed, url) {
      var changes = 0;
      ed.on('keyPress', function(e) {
        changes+=1;
        if (changes >= 20) {
          document.getElementById('mw_significant_update_true').setAttribute('checked', 'checked');
          ed.off('keyPress');
        }
      });
    }
  });
  tinymce.PluginManager.add( 'mw_detect_significant_update', tinymce.plugins.mw_detect_significant_update );
})(document);
