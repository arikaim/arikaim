'use strict';

function ImagesLibrary() {
    var self = this;
   
    this.init = function() {
        paginator.init('image_library_items',"image::admin.library.view.items",'images.library'); 

        this.loadMessages('image::admin.messages');
    };

    this.updateMainRelation = function(imageId, onSuccess) {
        var uuid = $('#image_library').attr('uuid');
        var extension = $('#image_library').attr('extension');
        var modelClass = $('#image_library').attr('model-class');

        imageApi.updateMainRelation({
            image_id: imageId,
            extension: extension,
            model_class: modelClass,
            uuid: uuid
        },function(result) {
            callFunction(onSuccess,result);
        });
    };

    this.initRows = function() {
        arikaim.ui.button('.set-main',function(element) {
            var imageId = $(element).attr('image-id');
           
            self.updateMainRelation(imageId,function(result) {
                $('#model_main_image').attr('src',result.image_src);
            });  
        });

        arikaim.ui.button('.add-image-relation',function(element) {
            var relationType = $('#image_library').attr('relation-type');
            var relationId = $('#image_library').attr('relation-id');
            var imageId = $(element).attr('image-id');
           
            relations.add('ImageRelations','image',imageId,relationType,relationId,function(result) {                                   
                return arikaim.page.loadContent({
                    id: 'image_relations_content',
                    component: 'image::admin.library.relations',
                    params: { 
                        relation_id: relationId,
                        relation_type: relationType
                    }
                });  
            });            
        });
    };
};

var imagesLibrary = createObject(ImagesLibrary,ControlPanelView);

arikaim.component.onLoaded(function() { 
    arikaim.ui.tab('.images-library-tab-item','images_library_content',['relation_id','relation_type']);  
});