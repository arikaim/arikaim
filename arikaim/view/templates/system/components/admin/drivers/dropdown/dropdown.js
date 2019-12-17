$(document).ready(function() {
    $('#drivers_dropdown').dropdown({
        onChange: function(value) {    
            drivers.loadConfigForm(value,'driver_config');
        }
    });
});