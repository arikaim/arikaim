/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function UploadLibrary() {
    var self = this;

    this.init = function() {
        $('#library_file').filepond({          
            maxFiles: 1,
            labelIdle: "Drag & Drop UI library zip file or <span class='filepond--label-action'> Browse </span>",
            acceptedFileTypes: ["application/zip","*/zip"],
            onremovefile: function(file) {
               $('.errors').hide();
            },
            server: {
                process: {
                    url: arikaim.getBaseUrl() + '/core/api/ui/library/upload',
                    method: 'POST',
                    onload: function(response) {                      
                        
                    },
                    onerror: function(response) {                      
                        var response = new ApiResponse(response);
                        arikaim.ui.form.showErrors(response.getErrors());
                    }
                },
                fetch: null,
                revert: null
            }
        });       
    };
}

var upload = new UploadLibrary();

arikaim.page.onReady(function() {
    upload.init();
});
