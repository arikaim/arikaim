'use strict';

arikaim.component.onLoaded(function() {    
    arikaim.ui.form.addRules("#library_config",{
        inline: false,
        fields: {}
    });

    arikaim.ui.form.onSubmit('#library_config',function(data) {  
        var params = arikaim.ui.form.serialize('#library_config');
        var name = $('#library_config').attr('library');

        return packages.saveLibraryParams(name,params);
    },function(result) {
        arikaim.ui.form.showMessage({
            message: result.message
        });
    },function(error) {
        arikaim.ui.form.showErrors(error);
    });
});