/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function ArikaimStoreView() {
    var self = this;

    this.initRows = function() {
        $('.package-version').each(function(index) {
            var package = $(this).attr('package');
            var uuid = $(this).attr('uuid');
           
            arikaim.page.loadContent({
                id: 'version_' + uuid,           
                component: "components:repository.version",
                loaderClass: 'ui active inline centered mini blue loader',
                params: { package: package }
            });
        });
        
        arikaim.ui.button('.install-package',function(element) {
            var type = $(element).attr('package-type');
            var name = $(element).attr('package-name');
            var repositoryType = $(element).attr('repository-type');
            var uuid = $(element).attr('uuid');

            return packageRepository.install(name,type,repositoryType,function(result) {
                // show message
                $(element).hide();
                arikaim.page.toastMessage(result.message);
                arikaim.ui.show('#installed_' + uuid);             
            },function(error) {
                arikaim.page.toastMessage({
                    message: error[0],
                    class: 'error'
                });                        
            });
        });

        arikaim.ui.button('.update-package',function(element) {
            var type = $(element).attr('package-type');
            var name = $(element).attr('package-name');
            var uuid = $(element).attr('uuid');

            return packageRepository.update(name,type,function(result) {
                // show message
                $(element).hide();
                arikaim.page.toastMessage(result.message);
                arikaim.ui.show('#installed_' + uuid);    
                if (isEmpty(result.version) == false) {
                    $('#current_' + uuid).html(result.version);    
                }                    
            },function(error) {
                arikaim.page.toastMessage({
                    message: error[0],
                    class: 'error'
                });                        
            });
        });
    };
}

var arikaimStoreView = new ArikaimStoreView();

$(document).ready(function() {
    arikaimStoreView.initRows();   
});