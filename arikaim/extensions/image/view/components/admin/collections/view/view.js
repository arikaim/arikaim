/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ImageCollectionsView() {
    var self = this;

    this.init = function() {     
        this.loadMessages('image::admin.collections.messages');
        paginator.init('collections_rows',"image::admin.collections.view.items",'collections'); 
        
        arikaim.ui.loadComponentButton('.create-collection');
    };

    this.loaItems = function() {
        return arikaim.page.loadContent({
            id: 'collections_rows',           
            component: 'image::admin.collections.view.items'                  
        },function(result) {
            self.initRows();
        });         
    };

    this.initRows = function() {
        arikaim.ui.loadComponentButton('.collection-action-button');
       
       
        arikaim.ui.button('.delete-collection',function(element) {
            var uuid = $(element).attr('uuid');
            var title = $(element).attr('data-title');
            var message = arikaim.ui.template.render(self.getMessage('remove.content'),{ title: title });

            modal.confirmDelete({ 
                title: self.getMessage('remove.title'),
                description: message
            },function() {
                imageCollectionsControlPanel.delete(uuid,function(result) {
                    arikaim.ui.table.removeRow('#row_' + uuid);     
                    arikaim.page.toastMessage(result.message);
                });               
            });           
        });
    };    
}

var collectionsView = createObject(ImageCollectionsView,ControlPanelView);

arikaim.component.onLoaded(function() {
    collectionsView.init();
    collectionsView.initRows();
});