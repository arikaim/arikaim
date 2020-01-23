/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function OptionsList() {
    var self = this;

    this.add = function(model, extension, typeId, typeName, onSuccess, onError) {       
        var data = {
            model: model,
            extension: extension,
            type_id: typeId,
            type_name: typeName
        }; 

        return arikaim.post('/core/api/orm/options/list/add',data,onSuccess,onError);          
    };

    this.update = function(model, extension, uuid, typeId, onSuccess, onError) {
        var data = {
            model: model,
            extension: extension,
            uuid: uuid,
            type_id: typeId
        };       

        return arikaim.put('/core/api/orm/options/list/update',data,onSuccess,onError);          
    };

    this.delete = function(model, extension, uuid, onSuccess, onError) {
        var data = {
            model: model,
            extension: extension,
            uuid: uuid
        };       

        return arikaim.put('/core/api/orm/options/list/delete',data,onSuccess,onError);          
    };
}

var optionList = new OptionsList();
