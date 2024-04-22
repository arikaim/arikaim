'use strict';

arikaim.component.onLoaded(function() {    
    var fileUpload = new FileUpload('#package_upload_form',{
        url: '/core/api/packages/upload',
        maxFiles: 1,
        allowMultiple: false,
        acceptedFileTypes: [],
        formFields: {            
        },
        onSuccess: function(result) {
            if (isEmpty(result.package.icon) == false) {
                $('#icon').css('class','icon blue' + result.package.icon);
            }
            $('#version').html(result.package.version);
            $('#title').html(result.package.title);
            $('#name').html(result.package.name);
            $('#description').html(result.package.description);
            var type = (result.package['package-type'] == 'template') ? 'theme' : result.package['package-type'];
            $('#type').html(type);
            $('#destination').html(result.destination);
            $('#confirm_button').attr('package-directory',result.package_directory);

            if (isArray(result.current) == true || isObject(result.current) == true) {
                // show warning message                          
                arikaim.ui.show('#upload_alert');
            }
            $('#package_info').show();
            $('#upload_panel').hide();   
        }
    });
});