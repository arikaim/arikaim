/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Relations() {
   
    this.deleteRelation = function(model, extension, id, type, relationId, onSuccess, onError) {
        var data = {
            model: model, 
            extension: extension,
            type: type,
            id: id,
            relation_id: relationId
        };

        return arikaim.put('/core/api/orm/relation/delete',data,onSuccess,onError); 
    };

    this.delete = function(model, extension, uuid, onSuccess, onError) {
        var data = {
            model: model, 
            extension: extension,
            uuid: uuid         
        };
               
        return arikaim.put('/core/api/orm/relation/delete',data,onSuccess,onError);          
    };

    this.add = function(model, extension, id, type, relationId, onSuccess, onError) {    
        var data = {
            model: model, 
            extension: extension,
            type: type,
            id: id,
            relation_id: relationId
        };

        return arikaim.post('/core/api/orm/relation',data,onSuccess,onError);          
    };    
}

var relations = new Relations();
