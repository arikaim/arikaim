/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.page.onReady(function() {
    $('#time_zone').dropdown({
        onChange: function(value) {          
            settings.save('time.zone',value);
        }
    });
    $('#date_format').dropdown({
        onChange: function(value) {          
            settings.save('date.format',value);
        }
    });
    $('#time_format').dropdown({
        onChange: function(value) {          
            settings.save('time.format',value);
        }
    });
});