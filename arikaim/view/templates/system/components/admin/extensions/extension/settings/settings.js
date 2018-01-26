/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.onPageReady(function() {
    $('#extensions_dropdown').dropdown({
        onChange: function(name) {             
            arikaim.page.loadContent({
                id: 'extension_details',
                component: "system:admin.extensions.extension.settings.tabs",
                params: { extension_name : name },
                use_header: true
            });     
        }
    });   
});