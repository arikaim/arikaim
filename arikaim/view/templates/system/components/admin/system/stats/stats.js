/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.onComponentLoaded('system:admin.system.stats',function() {
    search.clear(function() {
        search.load();
    });
});   

arikaim.page.onReady(function() { 
});