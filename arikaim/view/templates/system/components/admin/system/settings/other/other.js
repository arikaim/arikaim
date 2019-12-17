$(document).ready(function() {
    $('#debug_toggle').checkbox({
        onChecked: function() {
            settings.setDebug(true);         
        },
        onUnchecked: function() {
            settings.setDebug(false);         
        }
    }); 
});
