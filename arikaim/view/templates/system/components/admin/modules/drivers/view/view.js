/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function DriversView() {
   
    this.init = function() {
        paginator.init('drivers_rows');               
        arikaim.ui.tab('.drivers-tab-item','drivers_tab');            
    };

    this.initRows = function() {
        arikaim.ui.loadComponentButton('.driver-details');

        arikaim.ui.button('.driver-uninstall',function(element) {                           
            var name = $(element).attr('name');
            var uuid = $(element).attr('uuid');
            
            return modal.confirmDelete({ 
                title: 'Confirm',
                description: 'Confirm Uninstall Driver'
            },function() {
                drivers.uninstall(name,function(result) {
                    $('#' + uuid).remove();
                });
            });
        });

        arikaim.ui.button('.driver-config',function(element) {                
            arikaim.events.emit('driver.config',element);

            var name = $(element).attr('name');
            arikaim.ui.setActiveTab('#drivers_config','.drivers-tab-item');
            drivers.loadConfig(name,'driver_details',null,'sixteen wide');
        });
       
        $('.status-dropdown').dropdown({
            onChange: function(status) {   
                var uuid = $(this).attr('uuid');
                var name = $('#' + uuid).attr('name');
                if (status == 1) {
                    drivers.enable(name);
                }
                if (status == 0) {
                    drivers.disable(name);
                }
            }
        });
    };    
};

var driversView = new DriversView();

arikaim.component.onLoaded(function() {    
    driversView.init();
    driversView.initRows();
});
