/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov   <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Extensions() {
    
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

arikaim.component.onLoaded(function() {       
    extensions.init();    
});
