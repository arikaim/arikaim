/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ApiDocsView() {   
    this.init = function() {
        paginator.init('api_rotes_list',"system:admin.help.api.items",'help.api');             
    };

    this.initRows = function() {
        arikaim.ui.button('.view-api-details',function(element) {
            var uuid = $(element).attr('uuid');
          
            return arikaim.page.loadContent({
                id: 'api_details_content',
                params: { uuid: uuid },
                component: 'system:admin.help.api.details'
            });
        });
    };
}

var apiDocsView = createObject(ApiDocsView,ControlPanelView);

arikaim.component.onLoaded(function() {
    apiDocsView.init();
    apiDocsView.initRows();
});