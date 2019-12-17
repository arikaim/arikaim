/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Update() {
    var self = this;

    this.checkForNewVersion = function(onSuccess, onError) {
        return arikaim.get('/core/api/update/check/version',onSuccess,onError);
    };

    this.getLastVersion = function(packageName, onSuccess, onError) {
        packageName = getDefaultVaue(packageName,'');
        return arikaim.get('/core/api/update/last/version/' + packageName,onSuccess,onError);
    };

    this.update = function(packageName,onSuccess, onError) {
        packageName = getDefaultVaue(packageName,'');
        var data = {
            package: packageName
        };

        return arikaim.put('/core/api/update/',data,onSuccess,onError);
    };

    this.loadVersion = function() {
        arikaim.page.loadContent({
            id: 'version_info',
            component: 'system:admin.system.info.version',  
        });
    };

    this.init = function() {
        arikaim.ui.button('.update-button',function(element) {
            $('#new_message').hide();
            var packageName = $(element).attr('package');

            return update.update(packageName,function(result) {
                var currentVersion = result.version;
                var message = result.message;
                self.getLastVersion(packageName,function(result) {
                    if (result.version == currentVersion) {      
                        $(element).hide();                 
                        $('#install_mesage').show();
                        install.repair(function(result) {
                            arikaim.ui.form.showMessage({
                                selector: '#message',
                                message: result.message,
                                hide: 0
                            });
                            self.loadVersion();
                        })
                    }
                })
            });
        });
    }
}

var update = new Update();

arikaim.page.onReady(function() {
    update.init();
});
