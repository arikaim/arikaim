/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

function Update() {
    this.checkForNewVersion = function(onSuccess) {
        arikaim.get('/admin/api/update/check',onSuccess,onError);
    };

    this.update = function(onSuccess,onError) {
        arikaim.get('/admin/api/update/',onSuccess,onError);
    };
}

var update = new Update();

arikaim.page.onReady(function() {
    $('#update_button').off();
    $('#update_button').on('click',function() {
        update.update(function(result) {            
        });
    });
});