/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Options() {

    this.save = function(name, value, onSuccess, onError) {
        var params = { 
            key: name,
            value: value 
        };
        
        return arikaim.put('/core/api/options/',params,onSuccess,onError);
    };

    this.saveConfigOption = function(name, value, onSuccess, onError) {
        var params = { 
            key: name,
            type: typeof(value),
            value: value 
        };
        
        return arikaim.put('/core/api/settings/update/option',params,onSuccess,onError);
    };

    this.saveAll = function(formId, onSuccess, onError) {
        return arikaim.post('/core/api/options/',formId,onSuccess,onError);
    };

    this.get = function(name, onSuccess, onError) {
        return arikaim.get('/core/api/options/' + name,onSuccess,onError);
    };
}

var options = new Options();
