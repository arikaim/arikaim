/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Orm() {
    var self = this;
    
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
