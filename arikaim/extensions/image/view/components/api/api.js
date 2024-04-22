/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
'use strict';

function ImageApi() {

    this.setStatus = function(uuid, status, onSuccess, onError) {           
        var data = { 
            uuid: uuid, 
            status: status 
        };

        return arikaim.put('/api/image/status',data,onSuccess,onError);      
    };

    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/image/delete/' + uuid,onSuccess,onError);      
    };

    this.import = function(data, onSuccess, onError) {
        return arikaim.post('/api/image/import',data,onSuccess,onError);      
    };

    this.updateMainRelation = function(data, onSuccess, onError) {
        return arikaim.put('/api/image/relations/main',data,onSuccess,onError);      
    };

    
}
 
var imageApi = new ImageApi();
