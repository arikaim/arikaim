'use strict';

function ImageCollectionsControlPanel() {
    
    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/admin/image/collections/' + uuid,onSuccess,onError);          
    };
   
    this.add = function(formId, onSuccess, onError) {
        return arikaim.post('/api/admin/image/collections/create',formId,onSuccess,onError);          
    };

    this.update = function(formId, onSuccess, onError) {
        return arikaim.put('/api/admin/image/collections/update',formId,onSuccess,onError);          
    };
    
};

var imageCollectionsControlPanel = new ImageCollectionsControlPanel();