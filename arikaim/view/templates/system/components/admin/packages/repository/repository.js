/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function PackageRepository() {
    var self = this;

    this.download = function(name, type, repository, repoType, onSuccess, onError) {
        var data = { 
            package: name,
            type: type,
            repository: repository,
            repository_type: repoType                     
        };
        
        return arikaim.put('/core/api/packages/repository/download',data,onSuccess,onError);
    };

    this.init = function() {
        arikaim.ui.button('.download-package',function(element) {
            var type = $(element).attr('package-type');
            var name = $(element).attr('package-name');
            var repository = $(element).attr('repository');
            var repoType = $(element).attr('repository-type');

            return self.download(name,type,repository,repoType,function(result) {
                // show message
                arikaim.page.toastMessage({                   
                    message: result.message,
                    class: 'success'                    
                });
                $(element).hide();
            },function(error) {
                arikaim.page.toastMessage({                  
                    message: error,
                    class: 'error'                                           
                });
            });
        });   
    };
}

var packageRepository = new PackageRepository();

arikaim.component.onLoaded(function() {    
    packageRepository.init();
})