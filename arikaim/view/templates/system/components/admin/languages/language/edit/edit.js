/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.onPageReady(function() {
    var component = arikaim.getComponent('system:admin.languages.language.edit');
    var uuid = component.getProperty('uuid')
    if (isEmpty(uuid) == false) {
        languages.load(uuid);
    }
    $('#language_dropdown').dropdown({
        onChange: function(uuid) {                   
            languages.load(uuid);
        }
    });    
});
