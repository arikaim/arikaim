/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

arikaim.page.onReady(function() {
    $('#libraries_dropdown').dropdown({
        onChange: function(name) {              
            arikaim.page.loadContent({
                id: 'library_details',
                component: "system:admin.libraries.library.details.tabs",
                params: { library_name : name },
                useHeader: true
            },function() {
                $('#library_details_tab .item').tab();               
            });     
        }
    });   
});