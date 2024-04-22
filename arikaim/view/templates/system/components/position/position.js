/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Position() {
    var self = this;
    
    this.shift = function(modelName, uuid, targetUuid, onSuccess, onError) {
        var data = { 
            model_name: modelName,
            uuid: uuid,
            target_uuid: targetUuid 
        };
        
        return arikaim.put('/core/api/ui/position/shift',data,onSuccess,onError);       
    };

    this.swap = function(modelName, uuid, targetUuid, onSuccess, onError) {
        var data = { 
            model_name: modelName,
            uuid: uuid,
            target_uuid: targetUuid 
        };

        return arikaim.put('/core/api/ui/position/swap',data,onSuccess,onError);           
    };
}

var position = new Position();
