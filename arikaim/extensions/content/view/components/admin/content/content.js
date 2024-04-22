/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function ContentApi() {

    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/content/delete/' + uuid,onSuccess,onError);      
    };

    this.add = function(formId, onSuccess, onError) {
        return arikaim.post('/api/content/add',formId,onSuccess,onError); 
    };

    this.update = function(formId, onSuccess, onError) {
        return arikaim.put('/api/content/update',formId,onSuccess,onError); 
    };

    this.setStatus = function(uuid, status, onSuccess, onError) {           
        var data = { 
            uuid: uuid, 
            status: status 
        };

        return arikaim.put('/api/content/status',data,onSuccess,onError);      
    };
}

var contentApi = new ContentApi();
