'use strict';

arikaim.component.onLoaded(function() {
    $('.change-driver-status').dropdown({
        onChange: function(value) {
            var driverName = $(this).attr('driver_name');
            var icon = $(this).find('i');
          
            if (isEmpty(driverName) || value == 0) {
                drivers.disable(driverName,function(result) {
                    icon.removeClass('check olive').addClass('close orange');
                });              
            } else {               
                drivers.enable(driverName,function(result) {
                    icon.removeClass('close orange').addClass('check olive');
                });
            }            
        }
    });

    arikaim.ui.button('.driver-config',function(element) {  
        var name = $(element).attr('driver-name');
        arikaim.events.emit('driver.config',element,name);       
    });
});