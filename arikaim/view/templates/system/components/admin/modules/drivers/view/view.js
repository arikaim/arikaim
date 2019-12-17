/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

function DriversView() {
    var self = this;

    this.init = function() {
        paginator.init('drivers_rows');         
        arikaim.ui.tab('.drivers-tab-item','drivers_tab')

        arikaim.events.on('driver.config',function(element) {
            var name = $(element).attr('name');
          
            arikaim.ui.setActiveTab('#drivers_config','.drivers-tab-item');
            drivers.loadConfig(name,'drivers_tab');
        },'driversView',self);       
    };

    this.initView = function() {
        arikaim.ui.button('.driver-config',function(element) {                
            arikaim.events.emit('driver.config',element);
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

$(document).ready(function() {
    driversView.init();
});
