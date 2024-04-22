/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ImageControlPanel() {
    
    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/admin/image/delete/' + uuid,onSuccess,onError);          
    };
   
    this.import = function(formId, onSuccess, onError) {
        return arikaim.post('/api/admin/image/import',formId,onSuccess,onError);          
    };

    this.update = function(formId, onSuccess, onError) {
        return arikaim.put('/api/admin/image/update',formId,onSuccess,onError);          
    };

    this.setStatus = function(uuid, status, onSuccess, onError) {           
        var data = { 
            uuid: uuid, 
            status: status 
        };

        return arikaim.put('/api/admin/image/status',data,onSuccess,onError);      
    };
};

var imageControlPanel = new ImageControlPanel();

arikaim.component.onLoaded(function() {
    arikaim.ui.tab();
});