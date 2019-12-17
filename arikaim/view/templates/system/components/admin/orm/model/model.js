/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Orm() {
    var self = this;

    this.read = function(name, extension, uuid, onSuccess, onError) {       
        return arikaim.get('/core/api/orm/model/' + name + '/' + extension + '/' + uuid,data,onSuccess,onError);          
    };

    this.loadModel = function(selector, name, extension, uuid) {
        return arikaim.page.loadContent({
            id: selector,
            component: 'system:admin.orm.model',
            params: { 
                name: name,
                extension: extension,
                uuid: uuid 
            }
        });  
    }
}

var orm = new Orm();
