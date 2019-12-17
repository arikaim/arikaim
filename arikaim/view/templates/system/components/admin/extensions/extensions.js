/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

/**
 *  @class  Extensions 
 *  Control panel extensions manager component
 */
function Extensions() {
    var self = this;
    
    this.showDetails = function(name) {
        arikaim.ui.setActiveTab('#details_button');
        arikaim.page.loadContent({
            id: 'tab_content',
            component: 'system:admin.extensions.extension.details',
            params: { extension: name }
        });   
    };
    
    this.init = function() {
        arikaim.ui.tab();
    };
}

var extensions = new Extensions();

arikaim.page.onReady(function() {       
    extensions.init();    
});
