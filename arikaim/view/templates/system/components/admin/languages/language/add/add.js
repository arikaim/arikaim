/**
 *  Arikaim
 *  @version    1.0  
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license.html
 *  http://www.arikaim.com
 * 
 */

arikaim.onPageReady(function() {
    arikaim.page.loadContent({
        id: 'form_content',
        component: 'system:admin.languages.language.form',
        loader: false
    });
});