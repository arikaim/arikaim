/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ImagesView() {
    var self = this;
   
    this.init = function() {
        paginator.init('image_rows',"image::admin.images.view.rows",'images'); 

        $('.users-dropdown').on('change',function() {
            var selected = $(this).dropdown('get value');
                    
            search.setSearch({
                search: {
                    user_id: selected,                       
                }          
            },'images',function(result) {                  
                self.loadList();
            });    
        });
        
        search.init({
            id: 'image_rows',
            component: 'image::admin.images.view.rows',
            event: 'image.search.load'
        },'images');
        
        arikaim.events.on('image.search.load',function(result) {      
            paginator.reload();
            self.initRows();    
        },'imageSearch');

        this.loadMessages('image::admin.messages');
    };

    this.loadList = function() {        
        arikaim.page.loadContent({
            id: 'image_rows',         
            component: 'image::admin.images.view.rows'
        },function(result) {
            self.initRows();  
            paginator.reload(); 
        });
    };

    this.initRows = function() {
        $('.status-dropdown').dropdown({
            onChange: function(value) {               
                var uuid = $(this).attr('uuid');
                imageControlPanel.setStatus(uuid,value);
            }
        });    

        arikaim.ui.button('.details-button',function(element) {
            var uuid = $(element).attr('uuid');
            
            $('#details_content').show();

            return arikaim.page.loadContent({
                id: 'details_content',
                component: 'image::admin.images.details',
                params: { uuid: uuid }
            });     
        });

        arikaim.ui.button('.image-relations-button',function(element) {
            var uuid = $(element).attr('uuid');
            arikaim.ui.setActiveTab('#image_relations','.image-tab-item');

            return arikaim.page.loadContent({
                id: 'image_content',
                component: 'image::admin.images.relations',
                params: { uuid: uuid }
            });     
        });

        arikaim.ui.button('.delete-button',function(element) {
            var uuid = $(element).attr('uuid');
            var title = $(element).attr('data-title');
            var message = arikaim.ui.template.render(self.getMessage('remove.content'),{ title: title });

            modal.confirmDelete({ 
                title: self.getMessage('remove.title'),
                description: message
            },function() {
                imageControlPanel.delete(uuid,function(result) {
                    arikaim.ui.table.removeRow('#' + uuid);     
                });
            });
        });

        arikaim.ui.button('.thumbnails-button',function(element) {
            var uuid = $(element).attr('uuid');    
            arikaim.ui.setActiveTab('#thumbnails_image','.image-tab-item');
            
            return arikaim.page.loadContent({
                id: 'image_content',
                component: 'image::admin.thumbnails',
                params: { uuid: uuid }
            });          
        });

        arikaim.ui.button('.edit-button',function(element) {
            var uuid = $(element).attr('uuid');    
            arikaim.ui.setActiveTab('#edit_image','.image-tab-item');
            
            return arikaim.page.loadContent({
                id: 'image_content',
                component: 'image::admin.images.edit',
                params: { uuid: uuid }
            });          
        });
    };
};

var imagesView = createObject(ImagesView,ControlPanelView);

arikaim.component.onLoaded(function() {
    imagesView.init();
    imagesView.initRows();  
}); 