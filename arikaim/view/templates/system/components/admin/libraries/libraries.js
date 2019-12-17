/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Libraries() {
    var self = this;
    
    this.showDetails = function(name, onSuccess) {
        arikaim.page.loadContent({
            id: 'tab_content',
            component: 'system:admin.libraries.library.details',
            params: { library_name: name }
        },function(result) {           
            $('#library_details_tab .item').tab();  
            callFunction(onSuccess,result);           
        });  
    };

    this.showLibraryDetails = function(name, onSuccess) {
        return arikaim.page.loadContent({
            id: 'library_details',
            component: "system:admin.libraries.library.details.tabs",
            params: { library_name : name },
            useHeader: true
        },function(result) {
            $('#library_details_tab .item').tab();  
            callFunction(onSuccess,result);             
        });     
    }

    this.init = function() {
        arikaim.ui.tab();
    };
}

var libraries = new Libraries();

arikaim.page.onReady(function() {
    libraries.init();
});
