/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ArikaimStoreView() {
    var self = this;

    this.init = function() {
        arikaim.ui.button('.store-settings',function(element) {
            $('#packages_list').html('');
            $('#paginator').html('');
            return self.loadSettings();
        });

        arikaim.ui.button('#packages_type',function(element) {
            var type = $(element).attr('type');
            $('#package_details_button').remove();
            $('#arikaim_store_settings').html('');

            return arikaim.page.loadContent({
                id: 'packages_list',           
                component: 'system:admin.packages.store.view.rows',
                params: { 
                    type: type                   
                }
            },function(result) {
                self.initRows();
            });
        });
    };

    this.loadSettings = function() {
        return arikaim.page.loadContent({
            id: 'arikaim_store_settings',           
            component: 'system:admin.packages.store.settings',
            params: {}
        });
    };

    this.initRows = function() {
        arikaim.ui.button('.package-details',function(element) {
            var uuid = $(element).attr('uuid');
            var installedVersion = $(element).attr('installed');
            var packageName =  $(element).attr('package-name');
            $('#packages_list').html('');
            $('#paginator').html('');

            return arikaim.page.loadContent({
                id: 'arikaim_store_settings',           
                component: 'system:admin.packages.store.details',
                params: { 
                    uuid: uuid,
                    installed_version: installedVersion,
                    package_name: packageName
                }
            },function(result) {
                self.initPackageDetails();
            });
        });
    };

    this.initPackageDetails = function() {
        var packageTitle = $('#package_details').attr('package-title');
        var uuid = $('#package_details').attr('uuid');
        var installedVersion = $('#package_details').attr('installed');
        var packageName = $('#package_details').attr('package-name');

        $('#package_details_link_button').remove();

        arikaim.page.loadContent({
            id: 'links_path',      
            append: true,     
            component: 'system:admin.packages.store.details.link',
            params: { 
                uuid: uuid,
                title: packageTitle,
                package_name: packageName,
                installed_version: installedVersion
            }
        },function(result) {
            arikaim.ui.button('#package_details_link_button',function(element) {
                var uuid = $(element).attr('uuid');
                var installedVersion = $(element).attr('installed-version');
                var packageName = $(element).attr('package-name');
               
                $('#packages_list').html('');
                $('#paginator').html('');
                
                return arikaim.page.loadContent({
                    id: 'arikaim_store_settings',           
                    component: 'system:admin.packages.store.details',
                    params: { 
                        uuid: uuid,
                        package_name: packageName,
                        installed_version: installedVersion
                    }
                },function(result) {                   
                });
            });
        });       
    }
}

var arikaimStoreView = new ArikaimStoreView();

arikaim.component.onLoaded(function() {    
    arikaimStoreView.init();   
    arikaimStoreView.initRows();   
});