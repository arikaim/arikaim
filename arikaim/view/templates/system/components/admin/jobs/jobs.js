/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Jobs() {

    this.enable = function(uuid, onSuccess, onError) {
        var data = { 
            uuid: uuid,
            status: 1 
        };
        return arikaim.put('/core/api/jobs/status',data,onSuccess,onError)
    };

    this.disable = function(uuid, onSuccess, onError) {
        var data = { 
            uuid: uuid,
            status: 0 
        };
        return arikaim.put('/core/api/jobs/status',data,onSuccess,onError);           
    };

    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/core/api/jobs/delete/'+ uuid,onSuccess,onError);           
    };

    this.load = function(uuid, selector, onSuccess, onError) {
        arikaim.page.loadContent({
            id: selector,           
            component: "system:admin.jobs.job",
            params: { uuid: uuid }
        },function(result) {
            callFunction(onSuccess,result);
        },function(error) {
            callFunction(onError,result);
        });
    };
}

var jobs = new Jobs();
