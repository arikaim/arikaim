/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */

arikaim.page.onReady(function() {
    user.logout(function(result) {
        arikaim.clearToken();
        arikaim.page.reload();      
    });
});