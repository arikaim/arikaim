/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.page.onReady(function() {
    $('.change-option').off();
    $('.change-option').checkbox({
        onChecked: function() {
            var option_name = $(this).attr('name');
            settings.save(option_name,true);
        },
        onUnchecked: function() {
            var option_name = $(this).attr('name');
            settings.save(option_name,false);
        }
    });        
});