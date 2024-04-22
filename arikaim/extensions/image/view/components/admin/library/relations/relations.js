'use strict';

function ImagesLibraryRelatins() {
    var self = this;
   
    this.init = function() {     
        this.loadMessages('image::admin.messages');   
    };

    this.loadItems = function(relationId, relationType) {
        return arikaim.page.loadContent({
            id: 'images_library_relations_items',
            component: 'image::admin.library.relations.items',
            params: { 
                relation_id: relationId,
                relation_type: relationType
            }
        });    
    };

    this.initRows = function() {  
        
        arikaim.ui.button('.image-details',function(element) {
            $('#image_details').fadeIn(500);
            var uuid = $(element).attr('uuid');

            arikaim.page.loadContent({
                id: 'image_details',
                component: 'image::admin.library.details',
                params: { uuid: uuid }
            });   
        });
       
        arikaim.ui.button('.remove-image-relation',function(element) {
            var uuid = $(element).attr('uuid');
            
            relations.delete('ImageRelations','image',uuid,function(result) {
                arikaim.ui.table.removeRow('#row_' + uuid);  
            });           
        });       
    };
};

var imagesLibraryRelations = createObject(ImagesLibraryRelatins,ControlPanelView);

arikaim.component.onLoaded(function() { 
    arikaim.ui.tab('.images-library-tab-item','images_library_content',['relation_id','relation_type']);  
});