$(document).ready(function() {  
    arikaim.ui.button('.driver-config',function(element) {  
        var name = $(element).attr('driver-name');

        arikaim.events.emit('driver.config',element,name);       
    });
});