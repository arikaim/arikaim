/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function Queue() {
    var self = this;

    this.init = function() { 
        arikaim.ui.button('#start_worker',function(element) {
            return self.startWorker().done(function(result) {
                self.reload();
            });
        });

        arikaim.ui.button('#stop_worker',function(element) {
            return self.stopWorker().done(function(result) {
                self.reload();
            });
        });
    };

    this.startWorker = function(onSuccess, onError) {
        return arikaim.put('/core/api/queue/worker/start',null,onSuccess,onError)
    };

    this.stopWorker = function(onSuccess, onError) {
        return arikaim.delete('/core/api/queue/worker/stop',onSuccess,onError);           
    };

    this.deleteJobs = function(onSuccess, onError) {
        return arikaim.delete('/core/api/queue/jobs',onSuccess,onError);           
    };

    this.reload = function() {
        arikaim.page.loadContent({
            id: 'tab_content',
            component: 'system:admin.system.queue'
        },function(result) {
            self.init();
        }); 
    };
}

var queue = new Queue();

arikaim.page.onReady(function() {
    queue.init();    
});
