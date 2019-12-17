/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

arikaim.page.onReady(function() {
    $('#extensions_dropdown').dropdown({
        onChange: function(name) {              
            arikaim.page.loadContent({
                id: 'extension_details',
                component: "system:admin.extensions.extension.details.tabs",
                params: { extension_name : name },
                useHeader: true
            });     
        }
    });   
});