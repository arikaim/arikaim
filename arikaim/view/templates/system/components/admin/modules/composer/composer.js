/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ComposerPackagesView() {
   
    this.init = function() {
        paginator.init('composer_rows');      
    };

    this.initRows = function() {
        arikaim.ui.button('.package-details',function(element) {                
            var name = $(element).attr('package-name');   
            
            return arikaim.page.loadContent({
                id: 'package_details',
                params: { package_name: name },
                component: 'system:admin.modules.composer.details'             
            });     
        });
    };    
};

var composerPackages = new ComposerPackagesView();

arikaim.component.onLoaded(function() {    
    composerPackages.init();
    composerPackages.initRows();
});