/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function JobsQueue() {

    var self = this;

    this.init = function() {
        $('#worker_delete_jobs').off();
        $('#worker_delete_jobs').on('click',function() {
            self.deleteQueueWorkerJobs();
        });

        $('#worker_update_jobs').off();
        $('#worker_update_jobs').on('click',function() {
            self.updateQueueWorker();
        });
    };

    this.deleteQueueWorkerJobs = function() {
        arikaim.delete('/admin/api/jobs/worker',function(result) {
            self.reload();
        },function(errors) {

        });
    };

    this.updateQueueWorker = function() {
        arikaim.get('/admin/api/jobs/worker/update',function(result) {
            self.reload();
        },function(errors) {

        });        
    };

    this.reload = function() {
        arikaim.page.loadContent({
            id: 'system_tab',
            component: 'system:admin.system.jobs'
        },function(result) {
            self.init();
        }); 
    };
}

var jobs = new JobsQueue();

arikaim.page.onReady(function() {
    jobs.init();    
});