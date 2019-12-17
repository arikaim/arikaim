/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function PackageRepository() {
    var self = this;

    this.onInstalled;
    this.onError;

    this.install = function(name, type, onSuccess, onError) {
        var params = { 
            package: name,
            type: type           
        };
        return arikaim.put('/core/api/packages/repository/install',params,onSuccess,onError);
    };

    this.installButton = function(selector, onSuccess, onError) {
        selector = getDefaultValue(selector,'.install-repository-button');

        arikaim.ui.button(selector,function(element) {
            var type = $(element).attr('package-type');
            var name = $(element).attr('package-name');
            return packageRepository.install(name,type,function(result) {
                // show message
                arikaim.ui.form.showMessage({
                    selector: '#message',
                    message: result.message,
                    class: 'success',    
                    removeClass: 'error',                      
                    hide: 4000
                });
                callFunction(self.onInstalled,result);
            },function(error) {
                arikaim.ui.form.showMessage({
                    selector: '#message',
                    message: error,
                    class: 'error', 
                    removeClass: 'success',                       
                    hide: 4000
                });
                callFunction(self.onError,error);
            });
        });   
    };

    this.init = function() {
        var type = $('#package_version_content').attr('package-type');
        var name = $('#package_version_content').attr('package-name');
        var confirm = $('#package_version_content').attr('confirm-overwrite');
       
        arikaim.page.loadContent({
            id: 'package_version_content',
            component: 'system:admin.packages.repository.version',
            params: { 
                name: name,
                type: type,
                confirm_overwrite: confirm
            }
        });
    };
}

var packageRepository = new PackageRepository();

$(document).ready(function() {
    packageRepository.init();
})