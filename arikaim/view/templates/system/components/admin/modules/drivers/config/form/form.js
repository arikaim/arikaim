$(document).ready(function() {
    $('.checkbox').checkbox({
        onChecked: function() {
            $(this).attr('type','checkbox');
            $(this).val('true');
        },
        onUnchecked: function() {
            $(this).attr('type','hidden');
            $(this).val('false');
        }
    });

    arikaim.ui.form.onSubmit('#driver_config_form',function() {
        return drivers.saveConfig('#driver_config_form');
    },function(result) {         
        arikaim.ui.form.showMessage(result.message);           
    });
});